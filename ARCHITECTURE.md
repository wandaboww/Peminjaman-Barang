# Skema Database (ERD) - Sistem Inventaris Sekolah

Berikut adalah rancangan database relasional menggunakan notasi Mermaid. Diagram ini mencakup entitas utama: **Users** (Peminjam), **Assets** (Barang), dan **Loans** (Transaksi).

```mermaid
erDiagram
    USERS ||--o{ LOANS : "makes"
    ASSETS ||--o{ LOANS : "is_borrowed_in"
    USERS ||--o{ LOANS : "approves_by_admin"

    USERS {
        bigint id PK
        string name "Nama Lengkap"
        string identity_number UK "NIP (Guru) / NIS (Murid)"
        enum role "teacher, student, admin"
        string email UK
        string password
        boolean is_blacklisted "Status Blacklist Otomatis"
        timestamp created_at
    }

    ASSETS {
        bigint id PK
        string brand "Merk (Lenovo, Epson)"
        string model "Tipe"
        string serial_number UK
        date purchase_date
        enum status "available, borrowed, maintenance, broken"
        string qr_code_hash UK "Untuk QR Code Fisik"
        json specifications "Spek detail"
        timestamp created_at
    }

    LOANS {
        bigint id PK
        bigint user_id FK "Peminjam"
        bigint asset_id FK "Barang"
        bigint admin_id FK "Petugas scanner"
        datetime loan_date "Waktu Pinjam"
        datetime due_date "Jatuh Tempo (1 vs 3 hari)"
        datetime return_date "Waktu Kembali"
        enum status "active, returned, overdue, lost"
        text digital_signature "Path file TTD"
        json return_checklist "Checklist Kelengkapan (Tas, Charger)"
        text return_condition "Kondisi akhir"
        decimal fine_amount "Denda jika ada"
    }
```

## Penjelasan Relasi & Logika
1.  **Users ↔ Loans (One-to-Many)**: Satu user bisa memiliki banyak riwayat peminjaman, tapi sistem logic (Blacklist) akan membatasi peminjaman *aktif* hanya satu (atau sesuai limit).
2.  **Assets ↔ Loans (One-to-Many)**: Satu barang akan memiliki banyak history peminjaman. Status barang saat ini ditentukan oleh transaksi terakhir (jika status=active, maka Asset=borrowed).
3.  **Role Based Logic**: Kolom `role` di tabel `USERS` menjadi penentu logika perhitungan `due_date` di Controller.

---

## Struktur Folder Project (Laravel)

```
/app
├── Http
│   ├── Controllers
│   │   ├── AuthController.php
│   │   ├── AssetController.php      (CRUD Barang & Log History)
│   │   ├── BorrowingController.php  (Core Transaction)
│   │   └── MonitorController.php    (Dashboard Realtime)
│   ├── Middleware
│   │   └── CheckBlacklist.php       (Middleware pencegah transaksi untuk user bermasalah)
│   └── Requests
│       └── StoreLoanRequest.php     (Validasi Input)
├── Models
│   ├── User.php
│   ├── Asset.php
│   └── Loan.php
├── Services
│   ├── LoanService.php              (Business Logic: Hitung tanggal, Cek stok)
│   └── QrCodeService.php            (Generate QR)
└── Notifications
    └── LoanDueReminder.php          (Email/WA Notification)
```
