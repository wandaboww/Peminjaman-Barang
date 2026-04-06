# 🚀 SIM-IV Quick Reference Card

## 30-Second Setup

```bash
cd d:\Website\Barang
php -S 127.0.0.1:8000
# Then open http://127.0.0.1:8000
```

---

## Login in 3 Steps

1. **Click** "Login Admin" button (top right)
2. **Enter** password: `admin123`
3. **Click** "Login Admin" button

✅ You're now in Admin Mode!

---

## 5 Admin Menus

| Menu | Purpose | URL |
|------|---------|-----|
| 📊 Dashboard | System overview | `?view=dashboard` |
| ↩️ Pengembalian | Return items | `?view=return` |
| 📦 Data Barang | Manage assets | `?view=assets` |
| 👥 Data Pengguna | Manage users | `?view=users` |
| 📋 Log & Aktivitas | View logs | `?view=logs` |

---

## Public Mode vs Admin Mode

### 👥 Public Mode (No Login)
- ✅ Can borrow items
- ✅ Can return items
- ❌ Cannot manage assets
- ❌ Cannot manage users
- ❌ Cannot view logs

### 🔒 Admin Mode (After Login)
- ✅ Can do EVERYTHING
- ✅ Full asset management
- ✅ Full user management
- ✅ View system logs
- ✅ Clear logs

---

## Common URLs

| What | URL |
|------|-----|
| Public Dashboard | `http://127.0.0.1:8000` |
| Login Page | `http://127.0.0.1:8000/?view=login` |
| Admin Dashboard | `http://127.0.0.1:8000/?view=dashboard` (after login) |
| Manage Assets | `http://127.0.0.1:8000/?view=assets` (after login) |
| Manage Users | `http://127.0.0.1:8000/?view=users` (after login) |

---

## Test Data

### Sample User (Teacher)
- **ID**: `19800101`
- **Name**: Pak Budi (Guru)
- **Role**: Teacher
- **Borrow Duration**: 3 days

### Sample User (Student)
- **ID**: `2024001`
- **Name**: Ani (Siswa)
- **Role**: Student
- **Borrow Duration**: 1 day

### Sample Asset
- **Serial**: `LP-001`
- **Item**: Dell Inspiron Laptop
- **Status**: Available

---

## Borrowing Rules

| Rule | Details |
|------|---------|
| **Per User Limit** | 1 item at a time |
| **Teacher Duration** | 3 days |
| **Student Duration** | 1 day |
| **Late Penalty** | Auto-blacklist |
| **Blacklist Status** | Cannot borrow |

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Server won't start | Use: `php -S 127.0.0.1:8001` (different port) |
| Port 8000 in use | Find process: `netstat -ano \| findstr :8000` |
| Login not working | Check password: `admin123` (case-sensitive) |
| Can't see admin menus | Refresh page or clear browser cache |
| Data lost | Check `database.json` and `activity_logs.json` exist |

---

## API Endpoints

### Login
```bash
POST ?action=login
Body: {"password":"admin123"}
Response: {"success":true,"message":"..."}
```

### Logout
```bash
POST ?action=logout
Response: {"success":true,"message":"..."}
```

### Check Session
```bash
POST ?action=check_session
Response: {"is_logged_in":true,"session_id":"..."}
```

---

## Keyboard Shortcuts

| Action | Key |
|--------|-----|
| Open Dev Tools | F12 |
| Clear Cache | Ctrl+Shift+Delete |
| Refresh Page | F5 |
| Hard Refresh | Ctrl+F5 |
| Close Tab | Ctrl+W |
| New Tab | Ctrl+T |

---

## Developer Console Commands

### Check Login Status
```javascript
fetch('?action=check_session')
  .then(r => r.json())
  .then(d => console.log(d))
```

### Test Login
```javascript
fetch('?action=login', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({password: 'admin123'})
})
.then(r => r.json())
.then(d => console.log(d))
```

### View Current URL
```javascript
window.location.href
```

---

## File Locations

```
d:\Website\Barang\
├── prototype.php          ← Main application
├── database.json          ← User & asset data
├── activity_logs.json     ← Activity history
├── GETTING_STARTED.md     ← Quick start
├── LOGIN_GUIDE.md         ← Login help
├── AUTH_SYSTEM_DOCUMENTATION.md
├── TEST_CHECKLIST.md      ← All tests
└── PROJECT_SUMMARY.md     ← This summary
```

---

## Quick Test Workflow

### 1. Test Borrow (2 min)
- Go to public dashboard
- ID: `19800101`
- Asset: `LP-001`
- Click "Pinjam Barang"
- ✅ See success message

### 2. Test Return (2 min)
- Asset Serial: `LP-001`
- Condition: Good
- Click "Kembalikan"
- ✅ See success message

### 3. Test Admin (1 min)
- Login: password `admin123`
- Click "Data Barang"
- ✅ See assets list
- Click "Data Pengguna"
- ✅ See users list

**Total**: 5 minutes to verify system works!

---

## Feature Checklist

### Public Features
- [x] Borrow assets
- [x] Return assets
- [x] View borrowing status
- [x] See borrowing history
- [x] Login to admin

### Admin Features
- [x] Add/edit/delete assets
- [x] Add/edit/delete users
- [x] View all loans
- [x] Process returns
- [x] View activity logs
- [x] Clear logs
- [x] Manage blacklist

---

## Important Notes

⚠️ **Development Only**:
- Password in code (`admin123`)
- JSON database (not MySQL)
- No HTTPS
- No rate limiting
- No 2FA

✅ **For Production**:
- Use environment variables for password
- Migrate to MySQL database
- Use HTTPS/SSL
- Add rate limiting
- Consider 2FA
- Use production server (Apache/Nginx)

---

## Password Reset

### Quick Reset
Edit `prototype.php` line 55:
```php
'admin123'  // change to your password
```

### Using Environment Variable
Create `.env` file:
```
ADMIN_PASSWORD=new_password_here
APP_ENV=production
```

---

## Quick Links to Documentation

- 📖 [Getting Started](GETTING_STARTED.md) - Setup & overview
- 🔐 [Login Guide](LOGIN_GUIDE.md) - Login help
- 🔑 [Auth Documentation](AUTH_SYSTEM_DOCUMENTATION.md) - Details
- ✅ [Test Checklist](TEST_CHECKLIST.md) - 80+ tests
- 📋 [Project Summary](PROJECT_SUMMARY.md) - Complete summary
- 🏗️ [Architecture](ARCHITECTURE.md) - System design

---

## Response Times

| Action | Expected Time |
|--------|----------------|
| Page Load | < 500ms |
| Login | < 1s |
| Borrow | < 1s |
| Return | < 1s |
| Load Assets | < 1s |

---

## Browser Compatibility

| Browser | Support |
|---------|---------|
| Chrome | ✅ Full |
| Edge | ✅ Full |
| Firefox | ✅ Full |
| Safari | ✅ Full |
| IE 11 | ❌ Not supported |

---

## Contact & Support

### Documentation
- See [LOGIN_GUIDE.md](LOGIN_GUIDE.md) for login issues
- See [TEST_CHECKLIST.md](TEST_CHECKLIST.md) for testing
- See [ARCHITECTURE.md](ARCHITECTURE.md) for design details

### Common Issues
- Login not working? → Check [LOGIN_GUIDE.md](LOGIN_GUIDE.md#troubleshooting)
- How to borrow? → Check [GETTING_STARTED.md](GETTING_STARTED.md#-common-operations)
- Want to test? → Use [TEST_CHECKLIST.md](TEST_CHECKLIST.md)

---

## Version & Status

- **Version**: 1.0
- **Status**: ✅ Complete & Tested
- **Date**: December 25, 2025
- **PHP Version**: 8.0+
- **Browser**: Modern browsers only

---

## 🎉 You're All Set!

1. ✅ Server running
2. ✅ System ready
3. ✅ Documentation complete
4. ✅ Tests available
5. ✅ Ready to use!

**Start now**: `php -S 127.0.0.1:8000`

---

**Last Updated**: December 25, 2025
