# Testing Setup Summary

## ✅ Port & Server Configuration

**Default Port**: `8000`  
**Host**: `127.0.0.1` (localhost)  
**Access**: `http://127.0.0.1:8000`

---

## 🚀 How to Start Testing

### Option 1: Windows Batch (Easiest)
```powershell
# Double-click in File Explorer
run-local-server.bat
```

### Option 2: PowerShell
```powershell
powershell -ExecutionPolicy Bypass -File run-local-server.ps1
```

### Option 3: Direct Command (Any OS)
```bash
php -S 127.0.0.1:8000
```

---

## 📋 What's New

| File | Purpose |
|------|---------|
| `run-local-server.bat` | Windows batch launcher with setup check |
| `run-local-server.ps1` | PowerShell launcher with instructions |
| `TESTING.md` | Comprehensive testing workflows & debugging |
| `.env.example` | Configuration template for port customization |
| `.github/copilot-instructions.md` | Updated with testing info |

---

## 🧪 Quick Test Workflow

1. **Start Server**: Run `run-local-server.bat`
2. **Open Browser**: `http://127.0.0.1:8000`
3. **Login**: Password is `admin123`
4. **Create Test Data**:
   - Add user: NIP=19800101, Role=teacher
   - Add asset: Serial=LNV-TEST-001, Status=available
5. **Test Borrowing**:
   - Input NIP + Serial → Click "Process Borrowing"
   - Check `database.json` for new loan record
6. **Test Blacklist**:
   - Try borrowing again with same user → Should be blocked
7. **Test Return**:
   - Input Serial + Condition → Click "Process Return"
   - Asset status changes back to available

---

## 📊 Database Files

**Auto-created on first run:**
- `database.json` - All users, assets, loans
- `activity_logs.json` - Transaction history

**Reset data:**
```powershell
Remove-Item database.json, activity_logs.json
# Restart server to recreate with default seed
```

---

## 🔧 Custom Port

Edit `run-local-server.bat` or `.ps1`:
```
set PORT=8080
```

Then access: `http://127.0.0.1:8080`

---

## 📖 More Info

See [TESTING.md](TESTING.md) for:
- Complete test scenarios
- API endpoint examples
- Debugging tips
- Laravel testing setup
