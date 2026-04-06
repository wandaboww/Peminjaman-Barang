# Visual Guide - Fitur Pengembalian Barang

## 🎯 Menu Navigation

```
Navbar Items (dari kiri ke kanan):
[SIM-IV] [Dashboard] [Pengembalian Barang] ← NEW! [Data Barang] [Data Pengguna] [Log & Aktivitas]
                                  ↑
                              Green Icon (fa-undo)
```

## 📱 Layout Structure

### Desktop View (1920px and above)

```
┌────────────────────────────────────────────────────────────────────────┐
│ Dashboard Pengembalian                              [Refresh Data]      │
│ Proses pengembalian aset sekolah dan catat kondisi barang...           │
└────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────┬────────────────────────────────────────────────┐
│                         │                                                │
│   SCAN STATION          │           BARANG TERPINJAM TABLE               │
│   (Left 33%)            │           (Right 67% - left side)              │
│                         │                                                │
│ ┌─────────────────────┐ │ ┌──────────────────────────────────────────┐  │
│ │ 🔍 Scan Station     │ │ │ No│Peminjam│Barang│Serial│Tgl │Status   │  │
│ │ 🟢 Live Mode        │ │ ├──────────────────────────────────────────┤  │
│ │                     │ │ │ 1 │Ani... │Leno... LNV-001 15/1 Active  │  │
│ ├─────────────────────┤ │ │ 2 │Pak...│Epson...│EPS-001 14/1│Overdue │  │
│ │ ID Card Input       │ │ └──────────────────────────────────────────┘  │
│ │ [Scan NIP/NIS___]   │ │                                                │
│ │                     │ │                                                │
│ │ QR Code Input       │ │  ┌─────────────────────────────────────────┐  │
│ │ [Scan Barcode___]   │ │  │ SUMMARY CARDS (Right side)              │  │
│ │                     │ │  ├─────────────────────────────────────────┤  │
│ │ Kondisi Barang      │ │  │ [Total Peminjaman: 12]                  │  │
│ │ ☑ Baik              │ │  │ [Sedang Dipinjam: 2]                    │  │
│ │ ○ Lecet Minor       │ │  │ [Overdue: 1]                            │  │
│ │ ○ Rusak Berat       │ │  │ [Dikembalikan: 9]                       │  │
│ │                     │ │  └─────────────────────────────────────────┘  │
│ │ Catatan (Optional)  │ │                                                │
│ │ [Tulis catatan...] │ │                                                │
│ │                     │ │                                                │
│ │ [✓ Konfirmasi...] │ │                                                │
│ │                     │ │                                                │
│ │ [Alert Box]        │ │                                                │
│ │                     │ │                                                │
│ │ 💡 CHEAT SHEET      │ │                                                │
│ │ ┌─────────┬─────┐  │ │                                                │
│ │ │ Users   │Assets│  │ │                                                │
│ │ │19800101 │LNV-001 │ │                                                │
│ │ │2024001  │EPS-001 │ │                                                │
│ │ │2024002  │LOG-001 │ │                                                │
│ │ └─────────┴─────┘  │ │                                                │
│ └─────────────────────┘ │                                                │
└─────────────────────────┴────────────────────────────────────────────────┘
```

## 🎨 Form Details

### Scan Station Form

```
┌─────────────────────────────────────┐
│ 🔍 Scan Station     🔴 Live Mode     │
├─────────────────────────────────────┤
│                                     │
│ Scan Identitas Peminjam             │
│ [🪪] [Scan NIP / NIS disini___]    │
│ Input untuk mencari user             │
│                                     │
│ Scan Barcode Barang                 │
│ [📠] [Scan stiker barcode____]      │
│ Input untuk mencari asset            │
│                                     │
│ Kondisi Barang Saat Dikembalikan    │
│ ☑ ✓ Baik             (good)        │
│ ○  ⚠️ Lecet Minor     (minor_dmg)  │
│ ○  ❌ Rusak Berat     (major_dmg)  │
│                                     │
│ Catatan Tambahan (Opsional)         │
│ ┌──────────────────────────────────┐│
│ │Tulis catatan kondisi barang di... ││
│ │                                  ││
│ └──────────────────────────────────┘│
│                                     │
│ [✅ Konfirmasi Pengembalian]        │
│ (Button turns into loading state    │
│  with spinner on click)             │
│                                     │
│ Alert Box (appears on response):    │
│ ✅ Success: Barang berhasil...      │
│ ❌ Error: Barang tidak ditemukan... │
│                                     │
└─────────────────────────────────────┘
```

## 📊 Summary Cards Grid

```
Kondisi 3 Column Layout (Right Side):

┌──────────────────┐
│  Total Peminj.   │
│       12         │  ← Purple Gradient
│ 🤝 Transaksi     │     (#667eea → #764ba2)
└──────────────────┘

┌──────────────────┐
│ Sedang Dipinjam  │
│       2          │  ← Pink Gradient
│ 🕐 Aktif         │     (#f093fb → #f5576c)
└──────────────────┘

┌──────────────────┐
│    Overdue       │
│       1          │  ← Red Gradient
│ 🚨 Terlambat     │     (#eb3349 → #f45c43)
└──────────────────┘

┌──────────────────┐
│  Dikembalikan    │
│       9          │  ← Green Gradient
│ ✅ Selesai       │     (#11998e → #38ef7d)
└──────────────────┘
```

## 🔄 User Interaction Flow

### Happy Path (Successful Return)

```
┌─────────────────────┐
│  User opens         │
│  Pengembalian       │
│  Barang menu        │
└──────────┬──────────┘
           │
           ↓
┌─────────────────────┐
│ Scan/Input:         │
│ - NIP/NIS: 2024001  │
│ - Asset: LNV-001    │
└──────────┬──────────┘
           │
           ↓
┌─────────────────────┐
│ Select condition:   │
│ ☑ Baik              │
└──────────┬──────────┘
           │
           ↓
┌─────────────────────┐
│ Click:              │
│ Konfirmasi Kembali  │
└──────────┬──────────┘
           │
           ↓
┌─────────────────────┐
│ [⏳ Processing...]  │
│ Button disabled     │
└──────────┬──────────┘
           │
           ↓
┌─────────────────────┐
│ ✅ Success Alert:   │
│ Barang berhasil     │
│ dikembalikan        │
│ dengan kondisi: Baik│
└──────────┬──────────┘
           │
           ↓
┌─────────────────────┐
│ [Reload in 2 secs]  │
│ Form reset          │
└─────────────────────┘
```

### Error Path (User Not Found)

```
┌─────────────────────┐
│ User enters:        │
│ NIP/NIS: 9999999    │
└──────────┬──────────┘
           │
           ↓
┌─────────────────────┐
│ Click:              │
│ Konfirmasi Kembali  │
└──────────┬──────────┘
           │
           ↓
┌─────────────────────┐
│ ❌ Error Alert:     │
│ User dgn ID        │
│ 9999999 tidak       │
│ ditemukan           │
└──────────┬──────────┘
           │
           ↓
┌─────────────────────┐
│ Form remains active │
│ Button enabled      │
│ User can retry      │
└─────────────────────┘
```

## 📋 Active Loans Table

```
┌─────────────────────────────────────────────────────────┐
│ 🔄 Barang Terpinjam (Menunggu Pengembalian)  ⏱️ Aktif    │
├─────────────────────────────────────────────────────────┤
│ No│Peminjam         │Barang              │Serial  │Tgl    │Status
├─────────────────────────────────────────────────────────┤
│ 1 │Ani (Siswa)     │💻 Lenovo ThinkPad│LNV-001│15/1   │🕐 Aktif
│   │2024001         │    ThinkPad X1     │       │       │
├─────────────────────────────────────────────────────────┤
│ 2 │Pak Budi (Guru) │📽️ Epson Projector│EPS-001│14/1   │🚨 Overdue
│   │19800101        │    EB-X            │       │       │
├─────────────────────────────────────────────────────────┤
│ 3 │Budi (Siswa)    │🖱️ Logitech Mouse │LOG-001│16/1   │🕐 Aktif
│   │2024002         │    Wireless        │       │       │
└─────────────────────────────────────────────────────────┘

Status Indicators:
🕐 Aktif      = Loan still active, due_date not passed
🚨 Overdue    = Loan still active but due_date has passed
✅ Dikembalikan = Loan completed and returned
```

## 🎯 Condition Assessment Visual

```
Kondisi Options dengan Icons:

✅ Baik
   └─ Asset tetap status "available"
   └─ Bisa langsung dipinjam lagi
   └─ Tidak ada catatan kerusakan
   
⚠️ Lecet Minor  
   └─ Asset tetap status "available"
   └─ Ada catatan kecil di activity logs
   └─ Maintenance dapat monitor
   
❌ Rusak Berat
   └─ Asset status berubah "maintenance"
   └─ Tidak bisa dipinjam sampai diperbaiki
   └─ Detail kerusakan tercatat lengkap
```

## 🔌 API Integration Visualization

```
Frontend (JavaScript):
┌─────────────────────┐
│  Return Form        │
│  (returnForm)       │
└─────────┬───────────┘
          │
          │ Form Submit
          ↓
┌─────────────────────────────────────────┐
│ returnForm.onsubmit Handler             │
│ - Validate inputs                       │
│ - Collect: identity, asset, condition  │
│ - Show loading state                    │
└─────────┬───────────────────────────────┘
          │
          │ POST /
          │ ?action=return
          │ (JSON payload)
          ↓
Backend (PHP):
┌─────────────────────────────────────────┐
│ $action === 'return' handler            │
│ - Lookup user & asset                   │
│ - Find active loan                      │
│ - Update loan.status = 'returned'       │
│ - Update loan.condition & notes          │
│ - Update asset.status (conditional)    │
│ - Log activity to activity_logs.json   │
│ - Return JSON response                  │
└─────────┬───────────────────────────────┘
          │
          │ JSON Response
          │ {success: true, message: "..."}
          ↓
Frontend:
┌─────────────────────────────────────────┐
│ JavaScript Handler                      │
│ - Show success/error alert              │
│ - Hide loading state                    │
│ - Auto reload after 2 seconds           │
└─────────────────────────────────────────┘
```

## 💾 Database State Changes

### Before Return
```json
{
  "loans": [
    {
      "id": 1,
      "user_id": 2,
      "asset_id": 1,
      "loan_date": "2024-01-15 10:00:00",
      "due_date": "2024-01-16 10:00:00",
      "status": "active"
      // return_date, return_condition, return_notes = null
    }
  ],
  "assets": [
    {
      "id": 1,
      "status": "borrowed"
      // ... other fields
    }
  ]
}
```

### After Return (Condition: Good)
```json
{
  "loans": [
    {
      "id": 1,
      "user_id": 2,
      "asset_id": 1,
      "loan_date": "2024-01-15 10:00:00",
      "due_date": "2024-01-16 10:00:00",
      "return_date": "2024-01-16 09:30:00",  ← NEW
      "return_condition": "good",             ← NEW
      "return_notes": "Tidak ada catatan",    ← NEW
      "status": "returned"                    ← CHANGED
    }
  ],
  "assets": [
    {
      "id": 1,
      "status": "available"                   ← CHANGED
      // ... other fields
    }
  ],
  "activity_logs": [
    {
      "timestamp": "2024-01-16 09:30:00",
      "action": "RETURN",
      "table": "loans",
      "data": "Ani mengembalikan Lenovo ThinkPad X1",
      "details": "Kondisi: Baik, Catatan: Tidak ada catatan"
    }
  ]
}
```

## 🎬 Complete User Journey

```
1. USER OPENS RETURN PAGE
   Click: Pengembalian Barang menu
   ↓
2. PAGE LOADS
   - Table shows active/overdue loans
   - Form ready for input
   - Summary cards display stats
   ↓
3. USER SCANS/INPUTS DATA
   - Click identity field
   - Scan NIP/NIS (or use cheat sheet)
   - Click barcode field
   - Scan asset serial (or use cheat sheet)
   ↓
4. USER ASSESSES CONDITION
   - Select condition radio button
   - Type optional notes
   ↓
5. USER SUBMITS
   - Click "Konfirmasi Pengembalian"
   - Button shows loading state
   ↓
6. SYSTEM PROCESSES
   - Validates input
   - Looks up user and asset
   - Finds active loan
   - Updates database
   - Logs transaction
   ↓
7. USER SEES RESULT
   - Success alert shown
   - Page reloads after 2 sec
   - Form resets
   - Statistics update
   ↓
8. READY FOR NEXT RETURN
   - Form empty again
   - Ready to scan next item
```

---

## 🚀 Quick Links

- **Access Return Page**: http://127.0.0.1:8000/?view=return
- **Demo Users**: 19800101, 2024001, 2024002
- **Demo Assets**: LNV-001, EPS-001, LOG-001
- **Full Documentation**: See [RETURN_FEATURE.md](RETURN_FEATURE.md)
- **Summary**: See [RETURN_FEATURE_SUMMARY.md](RETURN_FEATURE_SUMMARY.md)

