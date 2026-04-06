<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\User;
use App\Models\Asset;
use Carbon\Carbon;
use Exception;

class LoanService
{
    /**
     * Core Transaction Logic: Create a new Loan
     */
    public function createLoan(User $user, Asset $asset, ?string $signaturePath = null, ?string $photoPath = null)
    {
        // 1. Blacklist Validation
        if (!$this->canUserBorrow($user)) {
            throw new Exception("User is Blacklisted! Please return previous items.");
        }

        // 2. Asset Availability Validation
        if ($asset->status !== 'available') {
            throw new Exception("Asset is currently unavailable.");
        }

        // 3. Calculate Due Date based on Role
        $dueDate = $this->calculateDueDate($user);

        // 4. Create Transaction
        $loan = Loan::create([
            'user_id' => $user->id,
            'asset_id' => $asset->id,
            'loan_date' => Carbon::now(),
            'due_date' => $dueDate,
            'status' => 'active',
            'digital_signature_path' => $signaturePath,
            'pickup_photo_path' => $photoPath,
        ]);

        // 5. Update Asset Status
        $asset->update(['status' => 'borrowed']);

        return $loan;
    }

    /**
     * Logic: Check if user is eligible (Not Blacklisted)
     */
    public function canUserBorrow(User $user): bool
    {
        // User cannot borrow if they have any active loan that is OVERDUE
        // OR if requirements say "must return previous item first" (strict 1 item policy):
        
        $activeLoansCount = Loan::where('user_id', $user->id)
            ->whereIn('status', ['active', 'overdue'])
            ->count();

        // If Strict 1 item per user rule applies:
        if ($activeLoansCount > 0) {
            return false;
        }

        return true;
    }

    /**
     * Logic: Determine Loan Duration based on Role
     */
    private function calculateDueDate(User $user): Carbon
    {
        if ($user->role === 'teacher') {
            return Carbon::now()->addDays(3);
        }

        // Default or Student
        return Carbon::now()->addDays(1);
    }
}
