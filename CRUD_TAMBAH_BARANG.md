# CRUD Fitur "Tambah Barang" - Admin Mode

## 📋 Ringkasan

Fitur "Tambah Barang" di Admin Mode adalah sistem CRUD (Create, Read, Update, Delete) lengkap untuk mengelola data inventaris asset/barang sekolah. Ketika barang ditambah/diubah/dihapus di Admin Mode, data otomatis terupdate di Public Mode tanpa perlu refresh manual.

---

## 🎯 Fitur yang Tersedia

### 1. CREATE (Tambah Barang Baru)
**Lokasi**: Admin Dashboard → Data Barang → Tombol "+ Tambah Barang"

**Input yang Diperlukan**:
- **Barcode / Scan Kode**: Barcode yang akan di-scan (opsional, bisa kosong)
- **Kategori Produk** *(Required)*: Pilihan dari dropdown
  - Laptop
  - Komputer
  - Printer
  - Scanner
  - Proyektor
  - Monitor
  - Tablet
  - Smartphone
  - Aksesoris
  - Perangkat Jaringan
  - Lainnya
- **Merk (Brand)** *(Required)*: Contoh "Lenovo", "Epson"
- **Model / Tipe** *(Required)*: Contoh "ThinkPad X1", "EB-X"
- **Serial Number** *(Required)*: Nomor unik barang, contoh "LNV-001"
- **Kode Barcode**: Kode untuk barcode (jika kosong, gunakan Serial Number)
- **Preview Barcode**: Preview real-time barcode dalam format CODE128
- **Status Awal**: Ready / Dipinjam / Rusak (default: Ready)

**Proses**:
```
1. Klik "+ Tambah Barang" → Modal terbuka
2. Isi semua field yang required (Brand, Model, SN, Kategori)
3. (Opsional) Masukkan Barcode Code atau klik "Sync" untuk auto-fill dari SN
4. Preview barcode akan muncul otomatis
5. Klik "Simpan" → Data disimpan ke database.json
6. Halaman auto-refresh → Data langsung muncul di tabel Admin
7. Data juga otomatis muncul di Public Mode (Stok Barang Tersedia)
```

**Validasi**:
- ✅ Brand, Model, Category, Serial Number wajib diisi
- ✅ Serial Number harus UNIK (tidak boleh duplikat)
- ✅ Barcode harus UNIK jika diisi (tidak boleh duplikat)
- ✅ Barcode Code auto-generate dari SN jika kosong

**Response Sukses**:
```json
{
  "success": true,
  "message": "✅ Barang berhasil ditambahkan."
}
```

---

### 2. READ (Lihat Data Barang)
**Lokasi**: Admin Dashboard → Data Barang → Tabel

**Kolom yang Ditampilkan**:
| Kolom | Deskripsi |
|-------|-----------|
| NO | Nomor urut |
| KATEGORI | Kategori barang (badge) |
| MERK & TIPE | Brand + Model |
| SERIAL NUMBER | Serial Number (code) |
| BARCODE | Barcode dalam format CODE128 |
| STATUS KONDISI | Status (Ready/Dipinjam/Rusak) |
| AKSI | Edit & Delete button |

**Data Barang juga ditampilkan di Public Mode**:
- Tab "Peminjaman Barang" → Tabel "Stok Barang Tersedia" (hanya yang status "available")
- Filter otomatis hanya menampilkan barang dengan status "available"

---

### 3. UPDATE (Edit Barang)
**Lokasi**: Admin Dashboard → Data Barang → Tabel → Klik Ikon Edit (✏️)

**Field yang Bisa Diubah**:
- Brand (Merk)
- Model (Tipe)
- Serial Number
- Kategori Produk
- Barcode Code
- Status (Ready / Dipinjam / Rusak)

**Proses**:
```
1. Klik ikon edit (✏️) di setiap baris
2. Modal terbuka dengan data barang yang sudah terisi
3. Ubah data yang ingin diubah
4. Klik "Simpan"
5. Data terupdate di database.json
6. Halaman auto-refresh
7. Perubahan langsung terlihat di Public Mode
```

**Batasan**:
- Serial Number yang baru tidak boleh duplikat dengan barang lain
- Barcode yang baru tidak boleh duplikat dengan barang lain

**Response Sukses**:
```json
{
  "success": true,
  "message": "✅ Data barang diperbarui."
}
```

---

### 4. DELETE (Hapus Barang)
**Lokasi**: Admin Dashboard → Data Barang → Tabel → Klik Ikon Hapus (🗑️)

**Proses**:
```
1. Klik ikon hapus (🗑️) di setiap baris
2. Konfirmasi dialog muncul: "Yakin ingin menghapus barang ini?"
3. Klik OK → Barang dihapus
4. Halaman auto-refresh
5. Barang hilang dari tabel Admin & Public Mode
```

**Batasan** ⚠️:
- **TIDAK BOLEH menghapus barang yang pernah dipinjam** (ada di history loan)
- Error message: "❌ Barang tidak bisa dihapus karena pernah dipinjam (Hapus riwayat terlebih dahulu)."
- Harus menghapus record loan terlebih dahulu via Log Aktivitas → Resume Menu

**Response Sukses**:
```json
{
  "success": true,
  "message": "✅ Barang berhasil dihapus."
}
```

---

## 🔧 Struktur Data Asset di Database

### Struktur JSON (database.json - Key "assets"):
```json
{
  "id": 4,
  "brand": "Lenovo",
  "model": "Ideapad 100",
  "serial_number": "xxx234",
  "category": "Laptop",
  "barcode": "xxx234",
  "status": "available"
}
```

### Field Description:
| Field | Tipe | Required | Deskripsi |
|-------|------|----------|-----------|
| id | integer | Auto | ID unik, auto-generate |
| brand | string | ✓ Yes | Merek/Brand barang |
| model | string | ✓ Yes | Tipe/Model barang |
| serial_number | string | ✓ Yes | Nomor seri UNIK |
| category | string | ✓ Yes | Kategori barang |
| barcode | string | - | Kode barcode (bisa kosong) |
| status | string | - | Status: available/borrowed/broken (default: available) |

### Enum Values:
**status**: `available` | `borrowed` | `broken`

**category**: `Laptop` | `Komputer` | `Printer` | `Scanner` | `Proyektor` | `Monitor` | `Tablet` | `Smartphone` | `Aksesoris` | `Perangkat Jaringan` | `Lainnya`

---

## 🔄 Integrasi Admin Mode ↔ Public Mode

### Bagaimana Data Terupdate Otomatis?

1. **Admin Mode Tambah Barang**
   ```
   User klik "Tambah Barang" → Isi form → Simpan
   ↓
   saveAsset() function kirim POST ke ?action=add_asset
   ↓
   Server side: add_asset handler simpan ke database.json
   ↓
   location.reload() → Halaman refresh
   ↓
   Data barang dimuat ulang dari database.json
   ```

2. **Data Langsung Terlihat di Public Mode**
   ```
   Public Mode tab "Peminjaman Barang"
   ↓
   Tabel "Stok Barang Tersedia" load dari $assets (dari database.json)
   ↓
   Filter: hanya status = "available"
   ↓
   Barang baru langsung terlihat (jika status "available")
   ```

3. **Jika Barang Diedit/Dihapus**
   - Edit: Data terupdate di database.json → Public Mode otomatis tampil yang baru
   - Delete: Data dihapus dari database.json → Barang hilang dari Public Mode

---

## 📝 Contoh Workflow Lengkap

### Scenario: Sekolah Beli Laptop Baru

**Step 1: Admin Tambah Barang Baru**
```
Klik "+ Tambah Barang"
Input:
  - Kategori: Laptop
  - Brand: Lenovo
  - Model: ThinkPad E15
  - Serial Number: LNV-2024-0001
  - Barcode Code: (kosong, akan auto-fill)
  - Status: Ready
Klik "Simpan"
```

**Step 2: System Save ke Database**
```json
{
  "id": 7,
  "brand": "Lenovo",
  "model": "ThinkPad E15",
  "serial_number": "LNV-2024-0001",
  "category": "Laptop",
  "barcode": "LNV-2024-0001",
  "status": "available"
}
```

**Step 3: Halaman Auto-Refresh**
- Tabel Admin langsung menampilkan barang baru
- Barcode preview muncul otomatis

**Step 4: Public Mode Terupdate**
- Pembaca buka http://127.0.0.1:9000/
- Login ke Public Mode
- Tab "Peminjaman Barang"
- Tabel "Stok Barang Tersedia" sudah tampil Laptop Lenovo baru
- Bisa langsung dipinjam (scan barcode "LNV-2024-0001")

**Step 5: Ketika Laptop Dipinjam**
- Record loan dibuat, status asset berubah ke "borrowed"
- Laptop hilang dari tabel "Stok Barang Tersedia" di Public Mode
- Muncul di tabel "Barang Sedang Dipinjam" (di tab Pengembalian)

**Step 6: Ketika Laptop Dikembalikan**
- Status asset berubah kembali ke "available"
- Laptop muncul lagi di tabel "Stok Barang Tersedia"

---

## 🔐 Validasi & Error Handling

### Validasi pada Penambahan:
```
❌ Brand kosong → "Harap isi semua field wajib!"
❌ Model kosong → "Harap isi semua field wajib!"
❌ Category kosong → "Harap isi semua field wajib!"
❌ Serial Number kosong → "Harap isi semua field wajib!"
❌ Serial Number duplikat → "Serial Number sudah terdaftar!"
❌ Barcode duplikat → "Barcode sudah terdaftar!"
✅ Semua valid → "✅ Barang berhasil ditambahkan."
```

### Validasi pada Penghapusan:
```
❌ Barang pernah dipinjam → "❌ Barang tidak bisa dihapus karena pernah dipinjam"
✅ Barang belum pernah dipinjam → "✅ Barang berhasil dihapus."
```

---

## 🎥 Fitur Bonus: Barcode Generator

### Real-Time Barcode Preview:
- Format: CODE128 (standar industri)
- Auto-generate dari Serial Number jika Barcode Code kosong
- Live preview saat mengetik
- Tombol "Sync" untuk copy SN ke Barcode Code

### Cara Penggunaan Barcode:
```
1. Admin buat barang dengan Serial Number: LNV-001
2. Barcode otomatis generate dari LNV-001
3. Print barcode dari preview
4. Tempel pada barang fisik
5. Saat peminjaman, user scan barcode
6. Sistem otomatis detect serial number → load data barang
```

---

## 📊 Activity Logging

Setiap operasi CRUD dicatat di `activity_logs.json`:

```json
{
  "timestamp": "2024-01-07 15:30:45",
  "action": "CREATE",
  "entity_type": "assets",
  "entity_name": "Lenovo ThinkPad E15",
  "details": "Kategori: Laptop, Serial: LNV-2024-0001",
  "user": "Admin"
}
```

**Event yang dicatat**:
- CREATE: Saat tambah barang baru
- UPDATE: Saat edit barang
- DELETE: Saat hapus barang

Bisa dilihat di **Admin Dashboard → Log Aktivitas**

---

## 🧪 Testing Checklist

- [ ] Tambah barang baru dengan kategori Laptop
- [ ] Verifikasi barang muncul di tabel Admin
- [ ] Login Public Mode → cek tabel "Stok Barang Tersedia"
- [ ] Edit barang (ubah brand/model)
- [ ] Verifikasi perubahan di Admin & Public Mode
- [ ] Hapus barang yang belum pernah dipinjam
- [ ] Verifikasi barang hilang dari Admin & Public Mode
- [ ] Coba hapus barang yang pernah dipinjam → error message
- [ ] Barcode preview berfungsi dengan baik
- [ ] Serial Number duplikat → error message

---

## 💡 Tips & Tricks

1. **Barcode Scanning**: 
   - Gunakan barcode scanner device atau trigger keyboard untuk auto-fill
   - Serial Number = Primary identifier
   - Barcode = Secondary identifier untuk scanning

2. **Data Backup**:
   - Backup `database.json` secara rutin
   - Jika ingin reset data, bisa delete file dan restart server

3. **Kategori Barang**:
   - Bisa tambah kategori baru dengan edit dropdown di modal
   - Jangan lupa sync di database kalau perlu

4. **Status Tracking**:
   - `available`: Barang siap dipinjam
   - `borrowed`: Sedang dipinjam
   - `broken`: Rusak/maintenance (tidak bisa dipinjam)

---

## 📞 Troubleshooting

| Problem | Solusi |
|---------|--------|
| Barang tidak muncul di Public Mode | Pastikan status = "available" |
| Barcode preview error | Serial Number tidak valid untuk CODE128, ganti dengan yang valid |
| Tidak bisa tambah barang | Refresh page atau clear browser cache |
| Data tidak terupdate di Public Mode | Reload browser atau clear cache |
| Tidak bisa hapus barang | Barang sudah pernah dipinjam, hapus history terlebih dahulu |

---

**Last Updated**: January 7, 2026  
**Version**: 1.0.0  
**Status**: ✅ Production Ready
