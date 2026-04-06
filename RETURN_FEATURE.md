# Fitur Pengembalian Barang (Return Feature)

## Overview
Fitur **Pengembalian Barang** telah ditambahkan ke Dashboard SIM-Inventaris untuk melengkapi workflow peminjaman. Fitur ini memungkinkan pengguna untuk mengembalikan barang yang dipinjam dan mencatat kondisi barang saat dikembalikan.

## Lokasi
- **Menu Navigasi**: `Pengembalian Barang` (di antara Dashboard Peminjaman dan Data Barang)
- **URL**: `?view=return`
- **Akses**: Publik (tidak perlu login admin)

## Struktur Halaman

### 1. Left Column - Scan Station (50%)
Sebuah form interaktif untuk memproses pengembalian barang dengan fitur:

#### Input Fields:
- **Scan Identitas Peminjam**: Scan NIP/NIS peminjam
- **Scan Barcode Barang**: Scan barcode/serial number barang
- **Kondisi Barang** (Radio Buttons):
  - ✅ **Baik** (good) - Barang dalam kondisi sempurna
  - ⚠️ **Lecet Minor** (minor_damage) - Ada lecet kecil tapi masih berfungsi
  - ❌ **Rusak Berat** (major_damage) - Barang rusak dan perlu perbaikan
- **Catatan Tambahan** (Optional): Text area untuk mencatat kondisi detail

#### Fitur:
- Form validation (identity dan asset code wajib)
- Live feedback messages (success/error alerts)
- Simulation cheat sheet dengan valid user IDs dan asset serial numbers
- Auto-submit dengan loading state

#### Business Logic:
```php
Proses Return:
1. Validasi input (identity + asset code)
2. Lookup user dari identity_number
3. Lookup asset dari serial_number
4. Cari active/overdue loan untuk user-asset combo
5. Update loan status → 'returned'
6. Simpan kondisi barang (return_condition, return_notes)
7. Update asset status berdasarkan kondisi:
   - good → 'available'
   - minor_damage → 'available' (dengan catatan)
   - major_damage → 'maintenance'
8. Log transaksi return ke activity logs
9. Reload halaman setelah 2 detik
```

### 2. Right Column - Active Loans Display (50%)

#### Tabel: "Barang Terpinjam (Menunggu Pengembalian)"
Menampilkan semua barang yang sedang dipinjam dengan kolom:
- **No** - Nomor urut
- **Peminjam** - Nama dan identity number peminjam
- **Barang** - Brand & Model dengan icon
- **Serial Number** - Kode unik barang
- **Tgl Pinjam** - Tanggal peminjaman
- **Status** - Active/Overdue badge

#### Filter & Status:
- Hanya menampilkan loans dengan status `'active'` atau `'overdue'`
- Overdue items ditampilkan dengan badge merah dan exclamation icon
- Active items ditampilkan dengan badge biru dan clock icon

### 3. Summary Cards (Right Side)
4 kartu statistik dengan gradient background:
- **Total Peminjaman** - Total semua transaksi peminjaman (purple gradient)
- **Sedang Dipinjam** - Jumlah barang yang masih aktif dipinjam (pink gradient)
- **Overdue** - Jumlah barang yang terlambat dikembalikan (red gradient)
- **Dikembalikan** - Jumlah barang yang sudah dikembalikan (green gradient)

## Implementasi Teknis

### Database Schema
Loans table sudah memiliki kolom baru:
```json
{
  "id": 1,
  "user_id": 2,
  "asset_id": 3,
  "loan_date": "2024-01-15 10:00:00",
  "due_date": "2024-01-16 10:00:00",
  "return_date": "2024-01-16 09:30:00",
  "return_condition": "good",
  "return_notes": "Barang dalam kondisi prima, tidak ada kerusakan",
  "status": "returned"
}
```

### API Endpoint
```
POST ?action=return
Content-Type: application/json

Request Body:
{
  "identity_number": "2024001",    // User ID
  "asset_code": "LNV-001",         // Asset Serial Number
  "condition": "good",              // good|minor_damage|major_damage
  "notes": "Tidak ada masalah"     // Optional
}

Response:
{
  "success": true,
  "message": "✅ Barang berhasil dikembalikan dengan kondisi: Baik."
}
```

### Frontend JavaScript Handler
```javascript
const returnForm = document.getElementById('returnForm');
// Handles form submission
// Validates inputs
// Sends POST request to ?action=return
// Shows success/error alerts
// Reloads page on success
```

## Workflow Lengkap (Skenario Penggunaan)

### Skenario 1: Return Baik-baik saja
```
1. User navigasi ke "Pengembalian Barang"
2. Scan NIP/NIS: 2024001
3. Scan Barcode: LNV-001
4. Pilih kondisi: "Baik"
5. Klik "Konfirmasi Pengembalian"
6. Sistem menemukan loan: user 2024001 → asset LNV-001 (status: active)
7. Update: loan.status = 'returned', asset.status = 'available'
8. Log: RETURN | Ani mengembalikan Lenovo ThinkPad X1 | Kondisi: Baik
9. Success message ditampilkan
```

### Skenario 2: Return Rusak Berat
```
1. User navigasi ke "Pengembalian Barang"
2. Scan NIP/NIS: 19800101 (Guru)
3. Scan Barcode: EPS-001
4. Pilih kondisi: "Rusak Berat"
5. Catatan: "Proyektor tidak menyala, layar error"
6. Klik "Konfirmasi Pengembalian"
7. Sistem menemukan loan aktif
8. Update: loan.status = 'returned', asset.status = 'maintenance'
9. Log: RETURN | Pak Budi mengembalikan Epson Projector... | Kondisi: Rusak Berat, Catatan: Proyektor tidak menyala...
10. Success message ditampilkan (sistem akan maintenance barang)
```

### Skenario 3: Error - Barang Tidak Ditemukan
```
1. User navigasi ke "Pengembalian Barang"
2. Scan NIP/NIS: 2024001
3. Scan Barcode: INVALID-001
4. Klik "Konfirmasi Pengembalian"
7. Error: "❌ Barang dengan Kode serial 'INVALID-001' tidak ditemukan."
8. Form tetap aktif, user bisa retry
```

## Integrasi dengan Sistem Existing

### Activity Logging
Setiap return tercatat di `activity_logs.json`:
```json
{
  "timestamp": "2024-01-16 10:30:00",
  "action": "RETURN",
  "table": "loans",
  "data": "Ani mengembalikan Lenovo ThinkPad X1",
  "details": "Kondisi: Baik, Catatan: Tidak ada catatan"
}
```

### Dashboard Metrics
Statistics pada halaman return dihitung real-time dari database:
- Total Loans: `count(loans.* where status IN ['active', 'overdue', 'returned'])`
- Sedang Dipinjam: `count(loans.* where status = 'active')`
- Overdue: `count(loans.* where status = 'overdue')`
- Dikembalikan: `count(loans.* where status = 'returned')`

## Testing Checklist

- [x] Fitur navigasi menu Return aktif
- [x] Form validation (input wajib)
- [x] Scan-based input untuk identity dan asset
- [x] Radio button condition selection
- [x] Condition assessment logic
  - [x] Good → asset.status = 'available'
  - [x] Minor damage → asset.status = 'available'
  - [x] Major damage → asset.status = 'maintenance'
- [x] Loan status update to 'returned'
- [x] Activity log recording
- [x] Success message display
- [x] Error handling (user not found, asset not found, no active loan)
- [x] Checked-out items table display
- [x] Statistics cards update
- [x] Cheat sheet with valid IDs and assets
- [x] Form reset after successful submission

## Demo Data

### Test Case 1: Successful Return
```
Identity: 2024001 (Ani)
Asset: LNV-001 (Lenovo ThinkPad X1)
Condition: Baik
Expected: Success, loan marked returned, asset available
```

### Test Case 2: Damaged Return
```
Identity: 19800101 (Pak Budi)
Asset: EPS-001 (Epson Projector)
Condition: Rusak Berat
Notes: Tidak menyala, terjadi error
Expected: Success, loan marked returned, asset in maintenance
```

### Test Case 3: Error - No Active Loan
```
Identity: 2024002 (Budi)
Asset: LNV-001
Expected: Error message (user has no active loan for this asset)
```

## Tampilan UI

### Desktop View (1920px+)
- Left column: 33% width (form)
- Right column: 67% width (table + stats)
- Responsive grid with `col-lg-4` dan `col-lg-8`

### Tablet View (768px - 1920px)
- Left column full width, then right column
- Summary cards stack vertically

### Mobile View (<768px)
- Single column layout
- Form first, then table
- Cards stack as full-width badges

## Future Enhancements

1. **Photo Evidence**: Tambahkan upload foto kondisi barang saat return
2. **Digital Signature**: Tanda tangan digital dari penerima
3. **SMS Notification**: Notifikasi ke user untuk overdue items
4. **Fine Calculation**: Hitung denda untuk return terlambat
5. **Condition History**: Tracking kondisi barang dari waktu ke waktu
6. **Export Report**: Export return history ke Excel/PDF

## File Modifications

### prototype.php
- Added return view section (lines ~1340-1520)
- Updated navbar with return menu link
- Added returnForm JavaScript handler
- Updated return API handler with new logic
- Modified getLoansWithDetails() to include asset details
- Added condition assessment in database
- Enhanced activity logging

### No Database Migration Needed
Fitur ini fully backward compatible dengan database JSON existing. Kolom baru akan dibuat otomatis saat data diupdate.

---

**Created**: January 2024  
**Version**: 1.0  
**Status**: Production Ready
