# Testing & Local Development Guide

## Quick Start - Run Local Server

### Windows (Recommended)
Double-click the batch file:
```
run-local-server.bat
```

Or run in PowerShell:
```powershell
powershell -ExecutionPolicy Bypass -File run-local-server.ps1
```

### macOS / Linux
```bash
php -S 127.0.0.1:8000
```

Server akan berjalan di `http://127.0.0.1:8000`

---

## Default Login
- **Username**: admin
- **Password**: `admin123`

⚠️ **Change in production!** Edit `ADMIN_PASSWORD` dalam `prototype.php` (line ~8)

---

## Testing Workflow

### 1. Testing Loan Checkout (Borrowing)

**Setup Data First:**
1. Open `http://127.0.0.1:8000/index.php`
2. Login dengan admin123
3. Go to **Manage Users** → Add test user:
   - Name: "Pak Budi"
   - Identity Number: 19800101
   - Role: teacher

4. Go to **Manage Assets** → Add test asset:
   - Brand: Lenovo
   - Model: ThinkPad X1
   - Serial Number: LNV-TEST-001
   - Status: available

**Test Checkout:**
1. Go to **Borrowing** tab
2. Enter Identity Number: `19800101`
3. Enter Serial Number: `LNV-TEST-001`
4. Click "Process Borrowing"
5. ✅ Verify:
   - `database.json` → loans array now has 1 entry with status `active`
   - `database.json` → assets array shows LNV-TEST-001 with status `borrowed`
   - `activity_logs.json` → new entry with action `BORROW`

### 2. Testing Blacklist (Active Loan Block)

**Setup:** Complete step 1 first (user has 1 active loan)

**Test:**
1. Try to add another asset and borrow it with same user (19800101)
2. ❌ Should see error: "User masih memiliki pinjaman aktif"
3. ✅ Loan creation should be blocked

### 3. Testing Item Return

**Setup:** Complete step 1 first (user has active loan)

**Test Return:**
1. Go to **Return** tab
2. Enter Serial Number: `LNV-TEST-001`
3. Select Condition: "good"
4. Click "Process Return"
5. ✅ Verify:
   - `database.json` → loan status changed to `returned`
   - `database.json` → asset status back to `available`
   - Loan now has `return_date` value
   - `activity_logs.json` → new entry with action `RETURN`

### 4. Testing Role-Based Due Dates

**Test 1: Teacher (3 days)**
- Create user with role: `teacher`
- Borrow item
- Check `database.json` → due_date should be +3 days

**Test 2: Student (1 day)**
- Create user with role: `student`
- Borrow item
- Check `database.json` → due_date should be +1 day

---

## Database Inspection

### Quick Data Check
Both JSON files are in the project root:

**`database.json`** - Main data
```json
{
  "users": [...],
  "assets": [...],
  "loans": [...]
}
```

**`activity_logs.json`** - Activity trail
```json
[
  {
    "id": 1,
    "timestamp": "2025-12-25 10:30:45",
    "action": "BORROW",
    "table": "loans",
    "details": "..."
  }
]
```

### Use any JSON viewer or terminal:
```powershell
Get-Content database.json | ConvertFrom-Json | ConvertTo-Json -Depth 10
```

---

## PHP Testing (Direct API Calls)

### Using cURL or Postman

**Test Checkout Endpoint:**
```bash
POST /prototype.php?action=borrow
Content-Type: application/x-www-form-urlencoded

identity_number=19800101&serial_number=LNV-TEST-001
```

**Test Return Endpoint:**
```bash
POST /prototype.php?action=return
Content-Type: application/x-www-form-urlencoded

serial_number=LNV-TEST-001&condition=good
```

---

## Laravel Testing (If Migrating)

### Setup Database
```bash
php artisan migrate
php artisan db:seed  # If seeders exist
```

### Run Local Server
```bash
php artisan serve
```
Runs on `http://127.0.0.1:8000`

### Test Endpoints
```bash
# Create Loan (Checkout)
POST /api/borrowing
{
  "identity_number": "19800101",
  "qr_code_hash": "abc123",
  "signature_image": "data:image/png;base64,..."
}

# Return Item
POST /api/borrowing/{qr_code_hash}/return
{
  "condition": "good",
  "checklist": {"charger": true, "bag": true}
}
```

---

## Debugging Tips

### Issue: "Admin password tidak valid"
- Default password: `admin123`
- Check `prototype.php` line ~8 for `ADMIN_PASSWORD` constant
- Clear browser cookies/session if stuck

### Issue: "Barang tidak ditemukan"
- Check `database.json` → assets array
- Ensure serial_number matches exactly (case-sensitive)

### Issue: "User sudah memiliki pinjaman aktif"
- This is correct! Check `database.json` → loans with `status: "active"`
- Return the item first before borrowing another

### Issue: Server won't start
- Check if port 8000 is already in use: `netstat -ano | findstr :8000`
- Change PORT in script to 8080 or another free port

---

## File Structure for Testing
```
d:\Website\Barang\
├── prototype.php          # Main application
├── index.php             # Entry point (includes prototype.php)
├── database.json         # Live data (auto-created on first run)
├── activity_logs.json    # Activity trail (auto-created)
├── run-local-server.bat  # Windows batch launcher
└── run-local-server.ps1  # PowerShell launcher
```

---

## Reset Testing Data

### Option 1: Delete JSON files (Full Reset)
```powershell
Remove-Item database.json
Remove-Item activity_logs.json
# Restart server - files will be recreated with default seed data
```

### Option 2: Export & Backup
```powershell
Copy-Item database.json "database.backup.$(Get-Date -Format 'yyyyMMdd_HHmmss').json"
```

---

## Performance Notes
- For testing <1000 loans: JSON DB is fine
- For production >10000 records: Use Laravel + MySQL
- Activity logs auto-trim at 1000 entries (prototype.php line ~145)
