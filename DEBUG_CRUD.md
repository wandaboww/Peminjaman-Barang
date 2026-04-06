# DEBUG CRUD Tambah Barang

## Langkah Testing

### 1. Jalankan Server
```bash
php -S 127.0.0.1:9000 prototype.php
```

### 2. Test Simple POST (Optional)
Buka: http://127.0.0.1:9000/test-simple-post.php

Buka browser console (F12) dan jalankan:
```javascript
fetch('test-simple-post.php?action=test', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({test: 'data'})
}).then(r => r.json()).then(d => console.log(d))
```

### 3. Test CRUD Lengkap
Buka: http://127.0.0.1:9000/test-crud.html

Pastikan sudah login dulu:
1. Buka http://127.0.0.1:9000/
2. Klik Login
3. Password: admin123
4. Klik Dashboard

Kemudian test:
1. Buka test-crud.html
2. Isi form dengan data
3. Klik "Test Add Asset"
4. Cek response di console (F12)

### 4. Troubleshooting

**Jika Error: "Akses ditolak! Anda harus login sebagai admin"**
- Pastikan sudah login dulu di prototype.php
- Cookies session harus tersimpan

**Jika Error: "Response tidak valid"**
- Cek console browser (F12)
- Lihat tab Network → lihat response body
- Copy response ke http://jsonlint.com untuk validate

**Jika Tidak Ada Error tapi Barang Tidak Muncul**
- Refresh halaman dashboard
- Buka database.json dan cek isinya
- Pastikan barang dengan SN sama belum ada

### 5. Cek Database Langsung

Buka `database.json` dan lihat struktur assets:
```json
{
  "assets": [
    {
      "id": 1,
      "brand": "Lenovo",
      "model": "ThinkPad",
      "serial_number": "LNV-001",
      "category": "Laptop",
      "barcode": "LNV-001",
      "status": "available"
    }
  ]
}
```

### 6. Jika Masih Error

Buka `prototype.php` line 486 dan tambahkan debugging:
```php
if ($action === 'add_asset') {
    // DEBUG
    error_log('ADD_ASSET: ' . json_encode($input));
    error_log('SESSION: ' . json_encode($_SESSION));
    error_log('IS_LOGGED_IN: ' . (AuthManager::isLoggedIn() ? 'YES' : 'NO'));
    
    // ... rest of code
}
```

Kemudian cek PHP error log dengan:
```bash
tail -f /var/log/php-errors.log
```

atau (di Windows):
```bash
type php_errors.log
```
