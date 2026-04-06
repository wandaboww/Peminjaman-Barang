# Rancangan Sistem Manajemen Inventaris Sekolah (SIM-Inventaris)

## 1. Arsitektur Sistem

### Implementasi Aktual (Dual-Mode Architecture)

#### **Primary: Prototype Single-File Application**
*   **Backend:** Pure PHP (No Framework)
*   **Database:** JSON File-based (`database.json`)
*   **Frontend:** Bootstrap 5 + Vanilla JavaScript
*   **Barcode:** JsBarcode library (CODE128 format)
*   **File:** `prototype.php` - Standalone aplikasi dengan embedded HTML/CSS/JS
*   **Keunggulan:** Zero-dependency, portable, bisa deploy di hosting minimal

#### **Secondary: Laravel Structure (Partial Implementation)**
*   **Backend:** Laravel 11.x (PHP) - Dalam pengembangan
*   **Database:** MySQL (dengan migration siap pakai)
*   **Status:** Migration lengkap, Controller & Service parsial
*   **Catatan:** Belum semua fitur diimplementasi di Laravel

### Technology Stack
*   **Core Language:** PHP 8.x
*   **CSS Framework:** Bootstrap 5.3.2
*   **Icons:** Font Awesome 6.4.0
*   **Barcode Library:** JsBarcode 3.11.5
*   **Font:** Inter (Google Fonts)

## 2. Skema Database (ERD)

### Tabel: `users`
Menyimpan data Guru, Murid, dan Admin.
*   `id` (PK)
*   `name` (String)
*   `identity_number` (String, Unique) -> NIP atau NIS
*   `role` (Enum: 'admin', 'teacher', 'student')
*   `email` (String, Unique) - Untuk notifikasi
*   `phone` (String) - Untuk WhatsApp
*   `is_active` (Boolean)
*   `timestamps`

### Tabel: `assets` (Barang)
*   `id` (PK)
*   `brand` (String) -> Merk (e.g., Lenovo, Epson)
*   `model` (String) -> Tipe (e.g., Thinkpad X240)
*   `serial_number` (String, Unique)
*   `barcode` (String) -> Custom barcode untuk stiker fisik (opsional, default=serial_number)
*   `category` (Enum: 'laptop', 'infocus', 'peripheral', 'other')
*   `condition` (Enum: 'good', 'minor_damage', 'major_damage', 'under_repair')
*   `status` (Enum: 'available', 'borrowed', 'maintenance', 'lost') -> Status ketersediaan
*   `qr_code_hash` (String, Unique) -> Hash untuk generate QR Code
*   `specifications` (JSON) -> Spek detail opsional
*   `notes` (Text, Nullable) -> Catatan tambahan
*   `timestamps`

### Tabel: `loans` (Transaksi Peminjaman)
*   `id` (PK)
*   `user_id` (FK -> users.id)
*   `asset_id` (FK -> assets.id)
*   `admin_id` (FK -> users.id, nullable) -> Admin yang memproses
*   `loan_date` (DateTime)
*   `due_date` (DateTime) -> Otomatis hitung berdasarkan Role User
*   `return_date` (DateTime, Nullable)
*   `status` (Enum: 'active', 'returned', 'overdue', 'lost')
*   `pickup_photo_path` (String, Nullable) -> Foto barang saat diambil
*   `digital_signature_path` (String, Nullable) -> TTD Peminjam
*   `return_notes` (Text, Nullable) -> Catatan saat kembali (denda, kerusakan baru)
*   `return_checklist` (JSON, Nullable) -> Checklist kelengkapan saat kembali (e.g., {"charger": true, "bag": true})
*   `timestamps`

**Catatan Implementasi:**
- **Prototype:** Tidak ada `pickup_photo_path`, signature disimpan inline
- **Laravel:** Field lengkap sesuai migration, tapi belum diimplementasi di controller

## 3. Business Logic Specification

### A. Aturan Peminjaman (Loan Policy)
1.  **Durasi:**
    *   Teacher: 3 Hari.
    *   Student: 1 Hari.
2.  **Blacklist System:**
    *   Sebelum `store` transaksi baru, cek tabel `loans`.
    *   Query: `WHERE user_id = ? AND status = 'active' OR status = 'overdue'`.
    *   Jika count > 0, **REJECT** transaksi.

### B. Alur Peminjaman (Checkout Flow)
1.  Scan/Input NIP/NIS User (Sistem auto-detect Guru/Murid).
2.  Scan/Input Serial Number Barang.
3.  Sistem cek eligibilitas:
    *   Validasi user exists
    *   Blacklist check (tidak boleh ada pinjaman aktif/overdue)
    *   Ketersediaan Barang (status = 'available')
4.  Sistem hitung `due_date` otomatis berdasarkan role.
5.  Submit -> Status Asset update ke `borrowed`, create loan record.
6.  Tampilkan konfirmasi dengan batas waktu pengembalian.

### C. Alur Pengembalian (Checkin Flow)
1.  Scan/Input Serial Number Barang atau Loan ID.
2.  Sistem cari active loan untuk barang tersebut.
3.  Admin konfirmasi pengembalian.
4.  Update status transaksi `returned`, set `return_date`.
5.  Update status Asset kembali `available`.

**Optional Features (Untuk implementasi lengkap):**
- Form "Return Checklist" (Tas, Charger, dll) -> Save as JSON
- Inspeksi kondisi fisik
- Jika Rusak -> Update `condition` asset, catat di `return_notes`
- Perhitungan denda otomatis jika overdue

## 4. Struktur Folder & File

### Implementasi Aktual

```
Barang/
├── prototype.php                        # ⭐ Main Application (Single-File)
├── database.json                        # JSON Database (Auto-generated)
├── ARCHITECTURE.md                      # ERD & System Architecture
├── DESIGN.md                            # This file
│
├── app/                                 # Laravel Structure (Partial)
│   ├── Http/
│   │   └── Controllers/
│   │       └── BorrowingController.php  # Transaction Controller
│   ├── Models/
│   │   └── User.php                     # User Model (minimal)
│   └── Services/
│       └── LoanService.php              # Business Logic Layer
│
└── database/
    └── migrations/
        └── 2024_01_01_000000_create_school_inventory_tables.php
```

### File Descriptions

#### **prototype.php** (Main Application)
**Class & Functions:**
- `JsonDB` class: Custom database engine
  - `getAll($table)`: Fetch all records
  - `find($table, $id)`: Find by ID
  - `findByColumn($table, $column, $value)`: Search by column
  - `insert($table, $item)`: Create new record
  - `update($table, $id, $updates)`: Update record
  - `delete($table, $id)`: Delete record
  - `getLoansWithDetails()`: Join loans with user & asset info
  
- `calculateDueDate($role)`: Helper untuk hitung tanggal jatuh tempo
- `checkBlacklist($db, $userId)`: Validasi blacklist user

**API Endpoints (POST):**
- `?action=borrow`: Proses peminjaman baru
- `?action=return`: Proses pengembalian
- `?action=add_asset`: Tambah barang baru
- `?action=edit_asset`: Edit data barang
- `?action=delete_asset`: Hapus barang

**Views:**
- `?view=dashboard` (default): Scan station + live stock
- `?view=assets`: CRUD management barang
- `?view=history`: Riwayat transaksi

#### **app/Services/LoanService.php**
**Methods:**
- `createLoan(User $user, Asset $asset, ...)`: Core transaction logic
- `canUserBorrow(User $user)`: Blacklist validation
- `calculateDueDate(User $user)`: Private helper untuk durasi

#### **app/Http/Controllers/BorrowingController.php**
**Methods:**
- `store(Request $request)`: Handle new loan
- `update(Request $request, $qrCodeHash)`: Handle return

#### **app/Models/User.php**
**Methods:**
- `loans()`: hasMany relationship
- `isTeacher()`: Helper method

**⚠️ Files NOT Implemented (Yet):**
- `app/Models/Asset.php`
- `app/Models/Loan.php`
- `app/Http/Middleware/CheckBlacklist.php`
- `app/Http/Controllers/AssetController.php`

---

## 5. Fitur yang Sudah Diimplementasi

### ✅ Core Features (prototype.php)

#### **A. Transaction Management**
- [x] Peminjaman barang dengan scan identity & asset code
- [x] Validasi blacklist otomatis (1 pinjaman aktif per user)
- [x] Perhitungan due_date berdasarkan role (Teacher: 3 hari, Student: 1 hari)
- [x] Pengembalian barang dengan update status otomatis
- [x] History transaksi lengkap dengan detail user & asset

#### **B. Asset Management (CRUD)**
- [x] Tambah barang baru dengan validasi duplicate serial number
- [x] Edit data barang
- [x] Hapus barang (dengan validasi history)
- [x] Custom barcode field (selain serial number)
- [x] Status tracking: available, borrowed, maintenance, lost

#### **C. Barcode System**
- [x] Generate barcode CODE128 real-time
- [x] Preview barcode sebelum download
- [x] Download barcode sebagai JPEG
- [x] Mini barcode display di tabel inventaris
- [x] Scanner-friendly input (auto-focus, Enter key navigation)

#### **D. Dashboard & Monitoring**
- [x] Live view stok barang tersedia
- [x] Active loans monitoring dengan user info
- [x] Color-coded status badges
- [x] Cheat sheet untuk testing (simulation mode)
- [x] Responsive 2-column layout dengan scrollable sections

#### **E. User Experience**
- [x] Modern UI dengan Inter font & custom styling
- [x] Real-time feedback (loading states, alerts)
- [x] Keyboard-friendly (auto-focus fields)
- [x] AJAX-based operations (no page reload)
- [x] Error handling dengan pesan jelas

### ⚠️ Partial Implementation (Laravel)

#### **Implemented:**
- [x] Database migration lengkap
- [x] BorrowingController dengan store() dan update() methods
- [x] LoanService dengan business logic
- [x] User model dengan relationship & helper method

#### **Not Yet Implemented:**
- [ ] Asset & Loan models
- [ ] CheckBlacklist middleware (logic ada inline di service)
- [ ] File upload untuk foto & signature
- [ ] QR Code generation (pakai barcode di prototype)
- [ ] Email/WhatsApp notification system
- [ ] Dashboard views (masih pakai prototype)
- [ ] API untuk mobile app

---

## 6. Design Decisions & Trade-offs

### Kenapa Prototype-First?
1. **Fast deployment**: Satu file PHP, bisa langsung jalan di hosting murah
2. **No dependencies**: Tidak perlu Composer, database server, atau konfigurasi complex
3. **Portability**: File database.json bisa di-backup dengan mudah
4. **Development speed**: Testing lebih cepat tanpa migration/seeding
5. **Learning curve**: Staff TU bisa maintain tanpa Laravel knowledge

### Kenapa Tetap Ada Laravel Structure?
1. **Scalability**: Siap upgrade ke full Laravel jika sekolah berkembang
2. **Best practices**: Showcase proper MVC architecture
3. **Team collaboration**: Struktur jelas untuk development team
4. **Testing**: Unit test lebih mudah dengan Service layer
5. **Future features**: Notification, API, reporting butuh Laravel

### Database Design Choices
- **JSON vs MySQL**: Prototype pakai JSON, Laravel siap MySQL
- **No soft deletes**: Hard delete dengan validasi history
- **Enum vs constants**: Enum di migration, string di JSON
- **Timestamps**: Otomatis di Laravel, manual di prototype

---

## 7. Roadmap & Future Development

### Phase 1: Prototype Completion ✅ (Current)
- [x] Core CRUD operations
- [x] Transaction flow
- [x] Barcode system
- [x] Basic dashboard

### Phase 2: Enhancement (In Progress)
- [ ] Return checklist implementation
- [ ] Fine calculation for overdue
- [ ] Condition tracking on return
- [ ] Print receipt/proof

### Phase 3: Laravel Migration
- [ ] Complete all models (Asset, Loan)
- [ ] Implement middleware
- [ ] Photo upload feature
- [ ] Digital signature with canvas
- [ ] Migrate dari JSON ke MySQL

### Phase 4: Advanced Features
- [ ] Email/SMS notification system
- [ ] WhatsApp integration untuk reminder
- [ ] Mobile app (REST API)
- [ ] Reporting & analytics dashboard
- [ ] QR Code printing bulk
- [ ] Multi-tenancy (multi-sekolah)

---

## 8. Testing & Validation

### Test Data (database.json)

**Users:**
- `19800101` - Pak Budi (Teacher)
- `2024001` - Ani (Student)
- `2024002` - Budi (Student Nakal) - Has active loan

**Assets:**
- `LNV-001` - Lenovo (Available)
- `EPS-001` - Epson Projector (Borrowed)
- `LOG-001` - Logitech Mouse (Maintenance)

### Manual Test Cases

✅ **Happy Path:**
1. Scan NIP valid + Scan asset available → Success
2. Return asset → Status updated, asset available again

❌ **Validation Tests:**
1. User dengan pinjaman aktif → Reject (Blacklisted)
2. Asset tidak available → Reject
3. User/Asset tidak ditemukan → Error message
4. Duplicate serial number saat add asset → Reject

### Browser Compatibility
- ✅ Chrome/Edge (Recommended)
- ✅ Firefox
- ⚠️ Safari (Barcode render mungkin berbeda)
- ❌ IE11 (Not supported)

---

## 9. Deployment Guide

### Quick Start (Prototype)
```bash
# 1. Upload prototype.php ke hosting
# 2. Set permissions
chmod 644 prototype.php
chmod 666 database.json  # File akan auto-create

# 3. Access via browser
http://yourschool.com/prototype.php
```

### Laravel Deployment (Future)
```bash
# 1. Clone repository
git clone <repo-url>

# 2. Install dependencies
composer install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Database setup
php artisan migrate
php artisan db:seed

# 5. Serve
php artisan serve
```

### Server Requirements
- PHP >= 8.0
- Extension: JSON (enabled by default)
- Write permission untuk database.json
- Apache/Nginx dengan mod_rewrite

---

## 10. Security Considerations

### Implemented
- ✅ Input validation (trim, empty check)
- ✅ JSON encoding untuk prevent injection
- ✅ Business logic validation (blacklist, availability)
- ✅ Unique constraints (serial_number, identity_number)

### TODO (Laravel Phase)
- [ ] CSRF protection
- [ ] Authentication & authorization
- [ ] SQL injection prevention (prepared statements)
- [ ] File upload validation (photo/signature)
- [ ] Rate limiting untuk API
- [ ] Audit log untuk sensitive actions

### Production Checklist
- [ ] Change default admin password
- [ ] Disable debug mode
- [ ] HTTPS enforcement
- [ ] Regular backup database.json
- [ ] Restrict file permissions
- [ ] Hide .env file
