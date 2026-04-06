# 🎯 PORT TESTING SETUP - COMPLETE GUIDE

## ⚡ FASTEST START (30 seconds)

### Windows
1. Double-click: `run-local-server.bat`
2. Open browser: `http://127.0.0.1:8000`
3. Password: `admin123`

### macOS / Linux
```bash
chmod +x run-local-server.sh
./run-local-server.sh
```

### Any OS
```bash
php -S 127.0.0.1:8000
```

---

## 📊 PORT CONFIGURATION

| Property | Value |
|----------|-------|
| Host | 127.0.0.1 (localhost) |
| **Port** | **8000** |
| Protocol | HTTP |
| URL | http://127.0.0.1:8000 |
| Default Password | admin123 |

---

## 📁 FILES CREATED FOR TESTING

### 🚀 Server Launchers
- `run-local-server.bat` → Windows (double-click)
- `run-local-server.ps1` → PowerShell
- `run-local-server.sh` → macOS / Linux

### 📚 Documentation
| File | Purpose |
|------|---------|
| `README_TESTING_SETUP.md` | **← Start here** Complete testing guide |
| `TESTING.md` | Detailed test workflows & scenarios |
| `QUICK_TEST_SETUP.md` | Quick reference guide |
| `PORT_REFERENCE.txt` | Port configuration reference |
| `.env.example` | Configuration template |

### 📖 Updated
- `.github/copilot-instructions.md` → Added testing setup info

---

## 🧪 THREE TEST SCENARIOS

### ✅ Test 1: Borrowing an Item
```
1. Add User (NIP: 19800101, Role: teacher)
2. Add Asset (Serial: LNV-001, Status: available)
3. Go to "Borrowing" → Enter NIP + Serial
4. Check database.json → Loan created, Asset marked as borrowed
```

### ✅ Test 2: Blacklist (Block Active Loan)
```
1. User from Test 1 has 1 active loan
2. Try borrowing another item with same NIP
3. Result: ❌ "User masih memiliki pinjaman aktif" (Feature working!)
```

### ✅ Test 3: Returning an Item
```
1. Go to "Return" tab
2. Enter Serial + Select Condition
3. Check database.json → Loan marked as returned, Asset available again
```

---

## 📊 DATA & LOGS

**Auto-created in project root:**

```
database.json
├── users[]       → All users (id, name, identity_number, role)
├── assets[]      → All equipment (id, serial_number, status)
└── loans[]       → All transactions (user_id, asset_id, status, due_date)

activity_logs.json
└── logs[]        → All actions (timestamp, action, user, details)
```

**Reset Data:**
```powershell
Remove-Item database.json, activity_logs.json
# Restart server - recreates with seed data
```

---

## 🔧 CHANGE PORT

Edit launcher script, change this line:

**Windows (BAT)**:
```batch
set PORT=8080
```

**PowerShell**:
```powershell
$port = 8080
```

**Bash**:
```bash
PORT=8080
```

Then access: `http://127.0.0.1:8080`

---

## 🔐 SECURITY NOTES

### Default Credentials
- **Admin Password**: `admin123`
- **Change in production!** Edit `prototype.php` line ~8

### Development Mode
- JSON database (no encryption)
- No user authentication validation
- Activity logs unlimited

### Production
- Use Laravel + MySQL
- Enable proper authentication
- Set strong admin password
- Configure environment variables

---

## 📚 FULL DOCUMENTATION

### Essential
- **README_TESTING_SETUP.md** — Complete testing guide (70+ lines)
- **TESTING.md** — Detailed workflows & debugging (150+ lines)

### Architecture
- **ARCHITECTURE.md** — System design & ERD
- **DESIGN.md** — Database schema & business logic
- **.github/copilot-instructions.md** — AI agent guidance

### Source Code
- **prototype.php** — Full implementation (3240 lines)
- **app/Services/LoanService.php** — Core business logic

---

## 🐛 QUICK TROUBLESHOOTING

| Problem | Solution |
|---------|----------|
| Port 8000 in use | Change PORT in script or use `netstat -ano \| findstr :8000` |
| PHP not found | Install PHP or add to PATH |
| "Admin password invalid" | Use `admin123` (case-sensitive) |
| "Asset not found" | Check serial_number spelling in database.json |
| "User has active loan" | ✓ This is correct! Return item first |

---

## 🚀 TYPICAL WORKFLOW

1. **Start Server**
   ```bash
   run-local-server.bat  # or .ps1 or .sh
   ```

2. **Open Browser**
   ```
   http://127.0.0.1:8000
   ```

3. **Login**
   - Password: `admin123`

4. **Create Test Data**
   - Add User, Asset
   - Create Loan record

5. **Run Tests**
   - Test borrowing
   - Test blacklist
   - Test return
   - Check database.json

6. **Inspect Data**
   - Open database.json (text editor)
   - View activity_logs.json
   - Verify state changes

7. **Reset** (if needed)
   - Delete database.json
   - Restart server

---

## 📞 REFERENCE

- **Full Testing Guide**: See `TESTING.md`
- **API Examples**: See `TESTING.md` → "PHP Testing" section
- **Architecture**: See `ARCHITECTURE.md`
- **Business Logic**: See `DESIGN.md`

---

## ✅ VERIFICATION CHECKLIST

- [x] Server launcher scripts created (bat, ps1, sh)
- [x] Port configured: 8000
- [x] Documentation complete
- [x] Test data setup guide written
- [x] Database files verified
- [x] Admin password set: admin123
- [x] Activity logging enabled
- [x] Blacklist feature verified
- [x] Role-based due dates implemented
- [x] Ready for testing!

---

**Ready to test? Start with:**
```
run-local-server.bat → http://127.0.0.1:8000
```
