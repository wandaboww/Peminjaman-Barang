## 📋 PORT & TESTING SETUP - COMPLETE SUMMARY

### ✅ What Was Created

#### 1. **Server Launcher Scripts**
```
├── run-local-server.bat     (1.2 KB) → Windows - just double-click!
└── run-local-server.ps1     (1.7 KB) → PowerShell alternative
```

**Features:**
- Auto-detects PHP installation
- Shows access URL and data location
- Clear error messages if PHP not found
- Instructions for CTRL+C to stop

#### 2. **Documentation Files**
```
├── TESTING.md               → Complete testing guide with 4+ scenarios
├── QUICK_TEST_SETUP.md      → Quick reference guide
├── PORT_REFERENCE.txt       → Quick port config
├── SETUP_COMPLETE.txt       → This summary
└── .env.example             → Config template for customization
```

#### 3. **Updated Files**
```
└── .github/copilot-instructions.md  → Added testing setup references
```

---

### 🎯 PORT CONFIGURATION

| Setting | Value |
|---------|-------|
| **Host** | 127.0.0.1 |
| **Port** | 8000 |
| **URL** | http://127.0.0.1:8000 |
| **Admin Password** | admin123 |

**To Change Port:**
Edit `run-local-server.bat` line ~10:
```batch
set PORT=8080
```

---

### 🚀 START TESTING IN 3 STEPS

#### Step 1: Launch Server
**Windows (Easiest)**:
1. Open File Manager
2. Navigate to `d:\Website\Barang`
3. Double-click `run-local-server.bat`

**Or command line**:
```bash
php -S 127.0.0.1:8000
```

#### Step 2: Open Browser
```
http://127.0.0.1:8000
```

#### Step 3: Login
- Password: `admin123`
- Click "Admin Login"

---

### 🧪 ESSENTIAL TEST WORKFLOWS

#### Test 1: Borrowing (Checkout)
1. Login with admin123
2. Manage Users → Add test user
   - NIP: 19800101
   - Role: teacher
3. Manage Assets → Add test asset
   - Serial: LNV-TEST-001
   - Status: available
4. Borrowing tab → Enter NIP + Serial
5. ✅ Check `database.json`:
   - New loan record with status `active`
   - Asset status changed to `borrowed`

#### Test 2: Blacklist (Block Active Loan)
1. From Test 1, user has 1 active loan
2. Try borrowing another asset with same NIP
3. ❌ Should get error: "User masih memiliki pinjaman aktif"

#### Test 3: Return (Checkin)
1. From Test 1, go to Return tab
2. Enter serial number + select condition
3. ✅ Check `database.json`:
   - Loan status changed to `returned`
   - Asset status back to `available`
   - `return_date` is set

---

### 📊 DATABASE FILES

Auto-created on first run in project root:

```json
// database.json
{
  "users": [
    {"id": 1, "identity_number": "19800101", "role": "teacher", ...},
    ...
  ],
  "assets": [
    {"id": 1, "serial_number": "LNV-001", "status": "available", ...},
    ...
  ],
  "loans": [
    {"id": 1, "user_id": 1, "asset_id": 1, "status": "active", ...},
    ...
  ]
}

// activity_logs.json
[
  {
    "id": 1,
    "timestamp": "2025-12-25 01:34:06",
    "action": "BORROW",
    "table": "loans",
    ...
  },
  ...
]
```

---

### 🔧 CUSTOMIZATION

**Change Port to 8080**:
```batch
# run-local-server.bat line 10
set PORT=8080
```

**Change Admin Password** (Production):
```php
// prototype.php line ~8
const ADMIN_PASSWORD = 'your_secure_password';
```

**Reset Test Data**:
```powershell
Remove-Item database.json, activity_logs.json
# Restart server - will recreate with seed data
```

---

### 🐛 TROUBLESHOOTING

| Issue | Solution |
|-------|----------|
| "Port 8000 already in use" | Change PORT in script or `netstat -ano \| findstr :8000` |
| "PHP not found" | Add PHP to PATH or install PHP |
| "Admin password wrong" | Default is `admin123` (case-sensitive) |
| "Barang tidak ditemukan" | Check serial_number spelling in database.json |
| "User masih ada pinjaman" | This is correct! Return item first (feature, not bug) |

---

### 📚 COMPLETE TESTING SCENARIOS

See **`TESTING.md`** for:
- ✅ Loan checkout workflow (4 steps)
- ✅ Blacklist enforcement test
- ✅ Item return workflow
- ✅ Role-based due date calculation
- ✅ Database inspection tips
- ✅ API call examples (cURL/Postman)
- ✅ Laravel testing setup
- ✅ Performance notes

---

### 🎓 NEXT STEPS

1. **Run Server**: `run-local-server.bat`
2. **Test Workflows**: Follow `TESTING.md`
3. **Inspect Data**: Open `database.json` with any text editor
4. **Check Logs**: View `activity_logs.json` for transaction history
5. **Review Code**: See `prototype.php` for implementation patterns

---

### 📞 REFERENCE DOCS

- **`ARCHITECTURE.md`** → System design & ERD
- **`DESIGN.md`** → Database schema & business logic
- **`.github/copilot-instructions.md`** → AI agent guidance
- **`TESTING.md`** → Detailed test scenarios
- **`prototype.php`** → Full implementation (3240 lines)

---

✅ **Everything is ready for testing!**

Start with `run-local-server.bat` and explore the application at `http://127.0.0.1:8000`
