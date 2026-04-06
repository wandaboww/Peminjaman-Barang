# 📑 SIM-Inventaris Documentation Index

Selamat datang! Berikut adalah panduan untuk setup dan testing SIM-Inventaris.

---

## 🚀 QUICK START (Mulai dari sini!)

### Langkah 1: Jalankan Server
```bash
run-local-server.bat
```

### Langkah 2: Buka Browser
```
http://127.0.0.1:8000
```

### Langkah 3: Login
```
Password: admin123
```

---

## 📚 Documentation Guide

### 🟢 GETTING STARTED (Baca dulu!)
1. **[START_HERE.txt](START_HERE.txt)** - Quick overview (5 min read)
2. **[PORT_TESTING_QUICK_REF.txt](PORT_TESTING_QUICK_REF.txt)** - Quick reference (2 min)
3. **[PORT_TESTING_SETUP_COMPLETE.txt](PORT_TESTING_SETUP_COMPLETE.txt)** - Setup summary (5 min)

### 🟡 TESTING GUIDES
1. **[PORT_TESTING_GUIDE.md](PORT_TESTING_GUIDE.md)** - Complete testing guide (30 min)
   - Port allocation (8000 vs 8001)
   - Launcher scripts
   - Testing workflows
   - Troubleshooting

2. **[TESTING.md](TESTING.md)** - Detailed test scenarios (40 min)
   - Setup data first
   - Borrowing workflow
   - Blacklist enforcement
   - Return workflow
   - Role-based due dates

3. **[TESTING_PORT_SETUP.md](TESTING_PORT_SETUP.md)** - Alternative testing reference

### 🔐 SECURITY & CONFIGURATION
1. **[SECURITY_SETUP.md](SECURITY_SETUP.md)** - Security best practices
   - Environment variables
   - .env configuration
   - Production setup
   - Security checklist

2. **[OPTION_A_COMPLETE.txt](OPTION_A_COMPLETE.txt)** - Security setup summary
3. **[.env](.env)** - Development configuration (sensitive - in .gitignore)
4. **[.env.example](.env.example)** - Production template (safe to commit)

### 🏗️ ARCHITECTURE & DESIGN
1. **[ARCHITECTURE.md](ARCHITECTURE.md)** - System design & ERD
   - Database schema
   - Folder structure
   - Component responsibilities

2. **[DESIGN.md](DESIGN.md)** - Detailed specification
   - Business logic rules
   - Loan policy
   - Workflows specification

3. **[.github/copilot-instructions.md](.github/copilot-instructions.md)** - AI agent guidance

---

## 🚀 SERVER LAUNCHER SCRIPTS

### Prototype (JSON Database) - Port 8000 ✅
```bash
# Option 1: Windows Batch
run-local-server.bat

# Option 2: PowerShell
powershell -ExecutionPolicy Bypass -File run-local-server.ps1

# Option 3: Direct command (any OS)
php -S 127.0.0.1:8000
```
**Status:** Ready to use NOW  
**Database:** JSON (database.json - auto-created)  
**UI:** Full featured Bootstrap 5  
**Password:** admin123  

---

### Laravel (MySQL) - Port 8001 ⏳
```bash
# Prerequisites (one-time setup):
composer install
php artisan key:generate
# Edit .env with your database credentials
php artisan migrate

# Then run:
run-laravel-server.bat

# Or PowerShell:
powershell -ExecutionPolicy Bypass -File run-laravel-server.ps1

# Or direct:
php artisan serve --host 127.0.0.1 --port 8001
```
**Status:** Needs setup (composer + migrations)  
**Database:** MySQL  
**UI:** API endpoints only  

---

### Run Both Simultaneously 🔀
```bash
run-dual-servers.bat
```
**Opens:**
- Window 1: Prototype on port 8000
- Window 2: Laravel on port 8001

**Great for:** Comparing both implementations side-by-side

---

## 🧪 Testing Workflows

### 1. Test Prototype (FASTEST - 5 minutes)
```
1. run-local-server.bat
2. http://127.0.0.1:8000
3. Login (admin123)
4. Create test user + asset
5. Test borrowing → blacklist → return
```
**Recommended:** Start here!

### 2. Test Laravel (15-30 minutes)
```
1. composer install
2. php artisan migrate
3. run-laravel-server.bat
4. Test API with Postman
```

### 3. Test Both (10 minutes)
```
1. run-dual-servers.bat
2. Test Prototype UI (8000)
3. Test Laravel API (8001)
4. Compare responses
```

---

## 📊 Port Configuration

| Port | Implementation | Status | Database | Access |
|------|---|---|---|---|
| **8000** | Prototype | ✅ Ready | JSON | http://127.0.0.1:8000 |
| **8001** | Laravel | ⏳ Setup | MySQL | http://127.0.0.1:8001 |

---

## 📁 Key Files & Directories

### Configuration
- `.env` - Development settings (local, not in git)
- `.env.example` - Production template (safe to commit)
- `.gitignore` - Protects sensitive files from git

### Database
- `database.json` - Prototype data (auto-created)
- `activity_logs.json` - Activity trail (auto-created)
- `database/migrations/` - Laravel migrations

### Application Code
- `prototype.php` - Full Prototype implementation (3240 lines)
- `app/Http/Controllers/BorrowingController.php` - Laravel checkout/return
- `app/Services/LoanService.php` - Business logic

### Documentation
- `ARCHITECTURE.md` - System design
- `DESIGN.md` - Database & business logic spec
- `PORT_TESTING_GUIDE.md` - Testing guide
- `SECURITY_SETUP.md` - Security best practices

---

## 🔑 Default Credentials

**Admin Password:** `admin123`

⚠️ Change in production! Use `.env.example` template.

---

## 🐛 Troubleshooting

### "Port 8000 already in use"
```powershell
netstat -ano | findstr :8000
taskkill /PID <PID> /F
```

### "PHP not found"
```bash
php -v
# If not found, add PHP to PATH
```

### "Laravel won't start"
```bash
composer install
php artisan migrate
php artisan key:generate
```

### "Can't login"
- Default password: `admin123` (case-sensitive)
- Check `.env` for ADMIN_PASSWORD setting

---

## 📞 Reading Order Recommended

**New to project?**
1. [START_HERE.txt](START_HERE.txt) (5 min)
2. [PORT_TESTING_QUICK_REF.txt](PORT_TESTING_QUICK_REF.txt) (2 min)
3. Run `run-local-server.bat` and test

**Want to understand architecture?**
1. [ARCHITECTURE.md](ARCHITECTURE.md)
2. [DESIGN.md](DESIGN.md)
3. [.github/copilot-instructions.md](.github/copilot-instructions.md)

**Need security info?**
1. [SECURITY_SETUP.md](SECURITY_SETUP.md)
2. [.env.example](.env.example)
3. [.gitignore](.gitignore)

**Setting up for production?**
1. [SECURITY_SETUP.md](SECURITY_SETUP.md)
2. [.env.example](.env.example)
3. [ARCHITECTURE.md](ARCHITECTURE.md)
4. Setup Laravel properly

---

## ✅ Checklist

- [ ] Read [START_HERE.txt](START_HERE.txt)
- [ ] Run `run-local-server.bat`
- [ ] Test Prototype at http://127.0.0.1:8000
- [ ] Login with admin123
- [ ] Follow workflow in [TESTING.md](TESTING.md)
- [ ] Understand [ARCHITECTURE.md](ARCHITECTURE.md)
- [ ] Review [SECURITY_SETUP.md](SECURITY_SETUP.md)

---

## 🎯 Next Steps

1. **Test Prototype** (5 min) - Quick feedback
2. **Understand Design** (15 min) - Read ARCHITECTURE.md
3. **Setup Laravel** (30 min) - If needed
4. **Review Security** (10 min) - Best practices

---

**Ready to start? Run: `run-local-server.bat` 🚀**
