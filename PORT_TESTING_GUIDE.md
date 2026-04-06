# 🚀 PORT TESTING GUIDE - Prototype & Laravel

## 📊 Port Configuration

### Port Allocation

| Implementation | Port | URL | Status | Fungsi |
|---|---|---|---|---|
| **Prototype** | 8000 | http://127.0.0.1:8000 | ✅ Ready | JSON DB, Full UI |
| **Laravel** | 8001 | http://127.0.0.1:8001 | ⏳ Needs Setup | API, MySQL |

---

## 🚀 Quick Start

### Option 1: Prototype Only (Recommended for Testing)
```bash
run-local-server.bat
# Atau: php -S 127.0.0.1:8000
# Akses: http://127.0.0.1:8000
```

### Option 2: Laravel Only (After Setup)
```bash
run-laravel-server.bat
# Atau: php artisan serve --host 127.0.0.1 --port 8001
# Akses: http://127.0.0.1:8001
```

### Option 3: Run BOTH Simultaneously (Testing Both)
```bash
run-dual-servers.bat
# Jalankan Prototype (8000) + Laravel (8001) di window terpisah
```

---

## 📋 Launcher Scripts

### `run-local-server.bat` ✅ Prototype
```batch
Port: 8000
Host: 127.0.0.1
Database: JSON (database.json)
UI: Built-in (Bootstrap 5)
Password: admin123
Status: Ready to use NOW
```

**Usage:**
```bash
# Windows: Double-click
run-local-server.bat

# PowerShell
powershell -ExecutionPolicy Bypass -File run-local-server.ps1

# Any OS
php -S 127.0.0.1:8000
```

---

### `run-laravel-server.bat` ⏳ Laravel
```batch
Port: 8001
Host: 127.0.0.1
Database: MySQL (needs setup)
UI: Not yet (API only)
Status: Needs composer + migration
```

**Prerequisites (Sebelum bisa jalankan):**
```bash
# 1. Install Composer dependencies
composer install

# 2. Copy .env
cp .env.example .env

# 3. Generate app key
php artisan key:generate

# 4. Setup database (MySQL)
# Edit .env dengan database credentials
# Lalu:
php artisan migrate

# 5. Baru bisa jalankan server
php artisan serve --host 127.0.0.1 --port 8001
```

---

### `run-dual-servers.bat` 🔀 Run Both
```batch
Membuka 2 window terminal:
  1. Prototype on port 8000
  2. Laravel on port 8001
Bisa test kedua implementasi sekaligus
```

---

## 🧪 Testing Workflow

### Test Prototype (Paling Mudah - Recommended)

**Step 1: Start Server**
```bash
run-local-server.bat
```
Expected output:
```
[Thu Dec 25 01:34:06] PHP 8.3 Development Server started
[Thu Dec 25 01:34:06] Listening on http://127.0.0.1:8000
```

**Step 2: Open Browser**
```
http://127.0.0.1:8000
```

**Step 3: Login**
- Password: `admin123`

**Step 4: Test Workflows**
1. **Add User**
   - NIP: 19800101
   - Role: teacher

2. **Add Asset**
   - Serial: LNV-TEST-001
   - Status: available

3. **Test Borrowing**
   - Go to "Borrowing" tab
   - Input NIP + Serial
   - Check database.json for new loan record

4. **Test Blacklist**
   - Try borrowing again with same NIP
   - Should be blocked ✓

5. **Test Return**
   - Go to "Return" tab
   - Input Serial + Condition
   - Check status changes to available

---

### Test Laravel (After Setup)

**Step 1: Setup**
```bash
# Install dependencies
composer install

# Setup database
php artisan migrate

# Generate key
php artisan key:generate
```

**Step 2: Start Server**
```bash
run-laravel-server.bat
```

**Step 3: Test API with Postman/cURL**

**Create Loan:**
```bash
POST http://127.0.0.1:8001/api/borrowing
Content-Type: application/json

{
  "identity_number": "19800101",
  "qr_code_hash": "abc123",
  "signature_image": "data:image/png;base64,..."
}
```

**Return Item:**
```bash
POST http://127.0.0.1:8001/api/borrowing/abc123/return
Content-Type: application/json

{
  "condition": "good",
  "checklist": {"charger": true, "bag": true}
}
```

---

## 📊 Running Both Simultaneously

**Ideal untuk:**
- Testing protocol compatibility
- Comparing UI/API
- Migration testing
- Load testing

**Usage:**
```bash
run-dual-servers.bat
```

**Output:**
```
Window 1: Prototype on port 8000
Window 2: Laravel on port 8001
```

**Access both:**
- Prototype UI: http://127.0.0.1:8000
- Laravel API: http://127.0.0.1:8001/api/borrowing

---

## 🔧 Custom Port Configuration

### Change Prototype Port

Edit `run-local-server.bat`:
```batch
set PORT=8080  # Change 8000 to 8080
```

Then access: `http://127.0.0.1:8080`

### Change Laravel Port

Edit `run-laravel-server.bat`:
```batch
set PORT=8090  # Change 8001 to 8090
```

Then access: `http://127.0.0.1:8090`

---

## 📊 Database Files

### Prototype
- **Location**: [`database.json`](database.json )
- **Content**: users, assets, loans (JSON format)
- **Auto-created**: Yes, on first run
- **Reset**: Delete file, restart server

### Laravel
- **Type**: MySQL (configured in .env)
- **Setup**: Run `php artisan migrate`
- **Credentials**: Set in [`.env`](.env )

---

## 🐛 Troubleshooting

### Port Already in Use
```powershell
# Check what's using port 8000
netstat -ano | findstr :8000

# Kill process (if needed)
taskkill /PID <PID> /F

# Or use different port
set PORT=8080
```

### PHP Not Found
```bash
# Check PHP is installed
php -v

# Add PHP to PATH if needed
# Windows: Edit Environment Variables
# Add C:\php (or your PHP path)
```

### Laravel Migration Error
```bash
# Check .env database settings
php artisan migrate:status

# Reset database
php artisan migrate:reset
php artisan migrate

# Seed data (if seeders exist)
php artisan db:seed
```

### File Permission Error
```bash
# Make sure files are writable
chmod 755 storage/
chmod 755 bootstrap/cache/

# Windows: Right-click → Properties → Security → Edit
```

---

## ✅ Checklist

### Before Testing Prototype
- [ ] PHP installed
- [ ] Port 8000 available
- [ ] No `.env` file blocking (or have one configured)

### Before Testing Laravel
- [ ] PHP installed
- [ ] Port 8001 available
- [ ] MySQL/Database server running
- [ ] Composer installed (`composer --version`)
- [ ] .env configured
- [ ] Migrations run
- [ ] No conflicts with port 8000 server

### Before Running Dual Servers
- [ ] Ports 8000 + 8001 both available
- [ ] Prototype ready to run
- [ ] Laravel ready to run (or skip if not installed)

---

## 📚 Complete Testing Scenarios

### Scenario 1: Test Prototype Only
```bash
# 1. Start
run-local-server.bat

# 2. Test in browser
http://127.0.0.1:8000

# 3. Create test data
# 4. Test borrowing → blacklist → return
# 5. Check database.json
```
**Duration**: 5-10 minutes
**Difficulty**: Easy ✅

---

### Scenario 2: Test Laravel Only
```bash
# 1. Setup
composer install
php artisan migrate

# 2. Start
run-laravel-server.bat

# 3. Test with Postman
POST http://127.0.0.1:8001/api/borrowing
{...}

# 4. Check response
```
**Duration**: 15-30 minutes
**Difficulty**: Medium ⚠️

---

### Scenario 3: Compare Both
```bash
# 1. Run both
run-dual-servers.bat

# 2. Test Prototype (8000)
# 3. Test Laravel API (8001)

# 4. Compare:
# - Same business logic?
# - Same validation?
# - Compatible data?
```
**Duration**: 20-40 minutes
**Difficulty**: Hard 🔴

---

## 🎯 Recommended Testing Order

1. **Start with Prototype** (Port 8000)
   - Easiest to setup
   - Full UI available
   - Quick feedback

2. **Then Laravel** (Port 8001)
   - After Prototype works
   - More complex setup
   - API testing

3. **Finally Both** (Dual servers)
   - When both are working
   - Advanced testing
   - Migration validation

---

## 📞 Reference

### Server Scripts
- `run-local-server.bat` → Prototype (8000)
- `run-laravel-server.bat` → Laravel (8001)
- `run-dual-servers.bat` → Both simultaneously

### Configuration
- [`.env`](.env ) - Development settings
- [`.env.example`](.env.example ) - Production template
- `.gitignore` - Protected files

### Documentation
- [TESTING.md](TESTING.md ) - Detailed workflows
- [SECURITY_SETUP.md](SECURITY_SETUP.md ) - Security guide
- [ARCHITECTURE.md](ARCHITECTURE.md ) - System design

---

**Ready to test? Start with `run-local-server.bat` on port 8000!** 🚀
