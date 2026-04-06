<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Asset;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowingController extends Controller
{
    protected $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    /**
     * Store a new borrowing transaction.
     * Alur: Input ID -> Validasi -> Cek Blacklist -> Buat Transaksi
     */
    public function store(Request $request)
    {
        // 1. Validasi Input (Bisa dipindah ke FormRequest)
        $validated = $request->validate([
            'identity_number' => 'required|exists:users,identity_number', // NIP atau NIS
            'qr_code_hash'    => 'required|exists:assets,qr_code_hash', // Hasil Scan QR
            'signature_image' => 'required|string', // Base64 encoded signature
        ]);

        DB::beginTransaction();
        try {
            // 2. Fetch Entities
            $user = User::where('identity_number', $validated['identity_number'])->firstOrFail();
            $asset = Asset::where('qr_code_hash', $validated['qr_code_hash'])->firstOrFail();

            // 3. Business Logic Check via Service
            //    - Cek User Blacklist/Tanggungan
            //    - Cek Ketersediaan Barang
            if (!$this->loanService->canUserBorrow($user)) {
                throw ValidationException::withMessages([
                    'user' => 'User ini masih memiliki pinjaman aktif atau denda (Blacklisted).'
                ]);
            }

            if ($asset->status !== 'available') {
                throw ValidationException::withMessages([
                    'asset' => 'Barang sedang tidak tersedia (Status: ' . $asset->status . ').'
                ]);
            }

            // 4. Proses Peminjaman
            //    Service akan menghitung DueDate berdasarkan Role (Guru=3 hari, Murid=1 hari)
            $loan = $this->loanService->createLoan(
                $user, 
                $asset, 
                $validated['signature_image']
            );

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Peminjaman berhasil dicatat.',
                'data' => [
                    'loan_id' => $loan->id,
                    'due_date' => $loan->due_date->format('Y-m-d H:i'),
                    'asset' => $asset->model
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Handle Item Return (Pengembalian)
     * Alur: Scan Barang -> Cek Kondisi -> Update Status
     */
    public function update(Request $request, $qrCodeHash)
    {
        $request->validate([
            'condition' => 'required|in:good,minor_damage,major_damage',
            'checklist' => 'required|array', // e.g. ['charger' => true, 'bag' => true]
        ]);

        DB::beginTransaction();
        try {
            // Cari Active Loan berdasarkan Barang
            $asset = Asset::where('qr_code_hash', $qrCodeHash)->firstOrFail();
            
            $activeLoan = Loan::where('asset_id', $asset->id)
                ->where('status', 'active')
                ->latest()
                ->firstOrFail();

            // Logic: Cek jika barang rusak
            if ($request->condition !== 'good') {
                // Catat kerusakan di asset & loan log
                $asset->update(['status' => 'maintenance', 'condition' => $request->condition]);
                $activeLoan->update([
                    'return_notes' => 'Barang kembali dengan kondisi: ' . $request->condition,
                    // Di implementasi nyata, bisa hitung denda otomatis di sini
                ]);
            } else {
                $asset->update(['status' => 'available']);
            }

            // Finalize Transaction
            $activeLoan->update([
                'status' => 'returned',
                'return_date' => now(),
                'return_checklist' => json_encode($request->checklist)
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pengembalian berhasil diproses.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal memproses pengembalian: ' . $e->getMessage()], 500);
        }
    }
}
