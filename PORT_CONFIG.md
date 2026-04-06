# Port Configuration Guide - SIM-Inventaris

## 📋 Quick Reference

### Default Port Configuration

| Service | Host | Port | URL | Purpose |
|---------|------|------|-----|---------|
| **Prototype** | 127.0.0.1 | 8000 | http://127.0.0.1:8000 | Single-file PHP dengan JSON database |
| **Laravel** | 127.0.0.1 | 8001 | http://127.0.0.1:8001 | Full MVC dengan MySQL |

## 🚀 Quick Start Testing

### Option 1: Prototype Only (Recommended)
```bash
# Windows
run-local-server.bat

# Linux/Mac
./run-local-server.sh

# Manual
php -S 127.0.0.1:8000
```
**Access:** http://127.0.0.1:8000  
**Best for:** Quick testing, demo, development tanpa database setup

### Option 2: Dual Servers
```bash
run-dual-servers.bat
```
**Access:**
- Prototype: http://127.0.0.1:8000
- Laravel: http://127.0.0.1:8001

**Best for:** Testing kompatibilitas, comparing implementations

### Option 3: Custom Port
```bash
php -S 127.0.0.1:9000  # Port 9000
php -S 127.0.0.1:3000  # Port 3000
# ... any available port
```

## 🔧 Configuration Files

### 1. config.testing.env
File konfigurasi utama untuk testing:
```env
PROTOTYPE_HOST=127.0.0.1
PROTOTYPE_PORT=8000
LARAVEL_HOST=127.0.0.1
LARAVEL_PORT=8001
ADMIN_PASSWORD=admin123
```

### 2. Batch Scripts
- `run-local-server.bat` - Start prototype on port 8000
- `run-dual-servers.bat` - Start both servers (8000 & 8001)
- `test-config.bat` - Show configuration info

## 🔐 Testing Credentials

```plaintext
Admin Password: admin123
```
⚠️ **WARNING:** Ganti password ini di production environment!

## 📊 Test Data

### Database Files
- `database.json` - Live database (auto-created)
- `activity_logs.json` - Activity logs (auto-created)

### Reset Test Data
```bash
# Windows PowerShell
Remove-Item database.json, activity_logs.json

# Linux/Mac
rm database.json activity_logs.json
```

## 🧪 Testing Workflows

### 1. Basic Server Test
```bash
# Start server
run-local-server.bat

# Open browser
http://127.0.0.1:8000

# Login with password: admin123
```

### 2. API Testing
```bash
# Test dengan curl
curl http://127.0.0.1:8000/

# Test dengan PowerShell
Invoke-WebRequest -Uri http://127.0.0.1:8000/
```

### 3. Dual Server Comparison
```bash
# Start both servers
run-dual-servers.bat

# Test prototype
curl http://127.0.0.1:8000/api/users

# Test Laravel
curl http://127.0.0.1:8001/api/users
```

## 🛠️ Troubleshooting

### Port Already in Use
```bash
# Check what's using the port (Windows)
netstat -ano | findstr :8000

# Kill process (replace PID)
taskkill /PID <PID> /F

# Or use different port
php -S 127.0.0.1:8002
```

### PHP Not Found
```bash
# Check PHP installation
php -v

# If not found, install PHP or add to PATH
# Windows: Add PHP folder to System Environment Variables
```

### Permission Denied
```bash
# Use elevated privileges (Run as Administrator)
# Or use port > 1024 (e.g., 8000 instead of 80)
```

## 📚 Additional Resources

- **TESTING.md** - Comprehensive test scenarios
- **QUICK_TEST_SETUP.md** - Quick reference guide
- **PORT_TESTING_GUIDE.md** - Detailed port testing procedures
- **prototype.php** - Main application file

## 🎯 Testing Checklist

- [ ] Server starts on configured port
- [ ] Can access login page
- [ ] Admin password works
- [ ] Database files are created
- [ ] Can create users and assets
- [ ] Borrowing workflow functions
- [ ] Return workflow functions
- [ ] Activity logs are recorded

## 💡 Tips

1. **Use port 8000** for most testing (default prototype port)
2. **Keep servers running** in separate terminal windows
3. **Monitor JSON files** for real-time data changes
4. **Use dual servers** to compare implementations
5. **Reset data between tests** if needed

## 📝 Quick Commands Reference

```bash
# Show config
test-config.bat

# Start prototype
run-local-server.bat

# Start both
run-dual-servers.bat

# Custom port
php -S 127.0.0.1:XXXX

# Check port usage
netstat -ano | findstr :8000

# Reset data
Remove-Item database.json, activity_logs.json
```

---
**Last Updated:** January 2026  
**Version:** 1.0.0
