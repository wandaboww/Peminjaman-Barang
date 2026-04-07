# SQLite Database

File database lokal yang dihasilkan:

- `database/sim_inventaris.sqlite`
- Jika file utama sedang terkunci, generator akan membuat file fallback seperti `database/sim_inventaris_YYYYMMDD_HHMMSS.sqlite`

Generator:

- `php database/init_sqlite.php`

Sumber data yang diimpor:

- `database.json`
- `activity_logs.json`

Tabel yang dibuat:

- `users`
- `assets`
- `loans`
- `settings`
- `activity_logs`

View yang disediakan:

- `loan_details`

Catatan:

- `prototype.php` sekarang bisa memakai SQLite langsung lewat `DB_DRIVER=sqlite`.
- File JSON lama tetap bisa disimpan sebagai backup atau sumber migrasi awal.
