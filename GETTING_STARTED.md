# 🚀 Getting Started with SIM-IV

Welcome to **SIM-IV (Sistem Inventaris Barang)** - School Asset Inventory Management System.

## What is SIM-IV?

SIM-IV is a comprehensive system for managing school asset borrowing and returns with two user modes:

### 👥 Public Mode (Students/Teachers)
- Browse and borrow school assets (laptops, projectors, etc.)
- Check return/check-in status
- View borrowing history
- Simple, user-friendly interface
- Built-in user guide button with modal instructions
- **Access**: Direct at `http://127.0.0.1:8000`

### 🔒 Admin Mode (Administrators)
- Full inventory management
- User account management
- Asset tracking and maintenance
- Activity logging and audit trail
- **Access**: Login with password `admin123`

---

## ⚙️ System Requirements

- **PHP**: Version 8.0+ (tested on PHP 8.3.22)
- **Browser**: Modern browser (Chrome, Firefox, Edge, Safari)
- **OS**: Windows, macOS, or Linux
- **Database**: JSON files (no external database needed)

---

## 🎯 Quick Start (5 minutes)

### 1. Start the Server
Open Command Prompt/Terminal and run:

```bash
cd d:\Website\Barang
php -S 127.0.0.1:8000
```

**Expected output**:
```
PHP 8.3.22 Development Server (http://127.0.0.1:8000) started
```

### 2. Open in Browser
Navigate to: **`http://127.0.0.1:8000`**

You should see the public dashboard with:
- "Dashboard Inventaris" header
- "Cara Pakai Aplikasi" button
- Asset borrowing section
- Return/checkin section
- "Login Admin" button in navbar

### 3. Test as Public User

**Open User Guide**:
1. Click **"Cara Pakai Aplikasi"** on the public dashboard
2. Read the borrowing and return steps in the modal
3. Click **"Mengerti"** to close the guide
4. Continue using the dashboard normally

**Borrow an Asset**:
1. Enter Student/Teacher ID (use `19800101` for test)
2. Enter Asset Serial Number (check `database.json` for available assets)
3. Click "Pinjam Barang"
4. See confirmation

**Return an Asset**:
1. Scan/Enter asset serial number
2. Select condition (Good/Minor Damage/Major Damage)
3. Click "Kembalikan"
4. See return confirmation

### 4. Login as Admin

1. Click **"Login Admin"** button (top right)
2. Enter password: `admin123`
3. Click **"Login Admin"** button
4. You're now in Admin Mode!

### 5. Explore Admin Features

You now have access to:
- **Dashboard**: Overview of system
- **Pengembalian**: Manage all returns
- **Data Barang**: Add/edit/delete assets
- **Data Pengguna**: Manage users
- **Log & Aktivitas**: View all system activity

### 6. Logout

Click **"Logout"** button (top right) to return to Public Mode.

---

## 📁 Project Structure

```
d:\Website\Barang\
├── prototype.php              # Main application file (3916 lines)
├── index.php                  # Entry point (redirects to prototype)
├── database.json              # Users, Assets, Loans data
├── activity_logs.json         # System activity log
│
├── app/                       # Laravel files (for future use)
│   ├── Http/Controllers/
│   ├── Models/
│   └── Services/
│
├── database/migrations/       # Database schemas
│
├── Documentation/
│   ├── LOGIN_GUIDE.md
│   ├── AUTH_SYSTEM_DOCUMENTATION.md
│   ├── TEST_CHECKLIST.md
│   └── ARCHITECTURE.md
```

---

## 🔐 Authentication

### Default Credentials
- **Role**: Admin
- **Password**: `admin123`
- **Type**: Password-only authentication
- **Recovery**: Bisa diaktifkan dari menu **Pengaturan > Keamanan Admin**

### Changing Password
1. Login sebagai admin
2. Buka menu **Pengaturan**
3. Gunakan panel **Keamanan Admin** untuk:
   - mengganti password admin
   - mengatur kode pemulihan untuk fitur lupa password
4. Untuk setup production, Anda juga bisa menyiapkan `.env`:
   ```bash
   ADMIN_PASSWORD=your_new_secure_password
   ADMIN_RECOVERY_CODE=your_recovery_code
   APP_ENV=production
   ```

---

## 📊 Data Files Explained

### database.json
Contains all system data:
- **users**: Student and teacher accounts
- **assets**: School equipment inventory
- **loans**: Borrowing records with due dates
- **blacklist**: Overdue users

**Structure**:
```json
{
  "users": [
    {
      "id": 1,
      "identity_number": "19800101",
      "name": "Pak Budi (Guru)",
      "role": "teacher",
      "kelas": "-"
    }
  ],
  "assets": [
    {
      "id": 1,
      "serial_number": "LP-001",
      "brand": "Dell",
      "model": "Inspiron",
      "status": "available",
      "qr_code_hash": "..."
    }
  ],
  "loans": [
    {
      "id": 1,
      "user_id": 1,
      "asset_id": 1,
      "loan_date": "2025-12-25 10:00:00",
      "due_date": "2025-12-28 10:00:00",
      "return_date": null,
      "status": "active"
    }
  ]
}
```

### activity_logs.json
Tracks all system actions:
```json
{
  "timestamp": "2025-12-25 10:30:00",
  "user": "Pak Budi",
  "action": "borrow",
  "details": "Borrowed laptop LP-001",
  "status": "success"
}
```

---

## 🎮 Common Operations

### Public Mode Operations

#### Open User Guide
1. Stay on the public dashboard
2. Click **"Cara Pakai Aplikasi"**
3. Read the quick instructions for borrowing and returns
4. Close the modal by clicking **"Mengerti"** or the close button

#### Borrow an Asset
1. Go to "Peminjaman" section
2. Enter your ID (NIP for teacher, NIS for student)
3. Scan or enter asset serial number
4. System automatically calculates due date:
   - **Teachers**: 3 days
   - **Students**: 1 day
5. Receive confirmation

#### Return an Asset
1. Go to "Pengembalian" section
2. Scan or enter asset serial number
3. Select condition:
   - ✅ Good
   - ⚠️ Minor Damage
   - ❌ Major Damage
4. Receive return confirmation

### Admin Mode Operations

#### View Dashboard
- System overview
- Recent borrowing activity
- Asset status summary

#### Manage Returns
- Complete return processing
- Manage damaged assets
- Track return status

#### Manage Assets
- Add new assets
- Edit asset details
- Delete/archive assets
- Mark assets for maintenance
- Track asset status

#### Manage Users
- Add/edit/delete users
- View user details
- Manage user roles
- View blacklist (overdue users)

#### View Activity Logs
- System audit trail
- All borrowing/return records
- User activity history
- Timestamps for all actions
- Clear logs (admin only)

---

## 🔍 Testing the System

### Quick Test Script

1. **Test Borrow Flow**:
   - Use ID: `19800101` (Pak Budi, teacher)
   - Use Asset: `LP-001` (Dell Inspiron laptop)
   - Expected: Loan created with 3-day due date

2. **Test Student Limit**:
   - Use ID: `2024001` (Ani, student)
   - Try to borrow twice
   - Expected: Second borrow fails (1-item limit)

3. **Test Blacklist**:
   - Check `database.json` for blacklist users
   - Try to borrow as blacklisted user
   - Expected: Borrow blocked

4. **Test Admin Features**:
   - Login as admin
   - Go to "Data Barang"
   - Try adding/editing asset
   - Expected: Changes reflected in system

See [TEST_CHECKLIST.md](TEST_CHECKLIST.md) for comprehensive test scenarios.

---

## 🐛 Troubleshooting

### Server won't start
```bash
# Error: "Address already in use"
# Solution: Change port
php -S 127.0.0.1:8001

# Error: "PHP command not found"
# Solution: Add PHP to PATH or use full path
C:\php\php.exe -S 127.0.0.1:8000
```

### Can't access http://127.0.0.1:8000
```bash
# Check if server is running
netstat -ano | findstr :8000

# Check firewall isn't blocking port 8000
# Restart server
```

### Login not working
1. Open DevTools: Press `F12`
2. Go to **Console** tab
3. Look for error messages
4. Try password again: `admin123`
5. Check [AUTH_SYSTEM_DOCUMENTATION.md](AUTH_SYSTEM_DOCUMENTATION.md#troubleshooting) for more help

### Data lost after restart
- JSON files are auto-saved
- Check `database.json` and `activity_logs.json` exist
- Don't delete these files manually
- Backup files before major changes

---

## 📚 Documentation Files

| Document | Purpose |
|----------|---------|
| [LOGIN_GUIDE.md](LOGIN_GUIDE.md) | Step-by-step login instructions |
| [AUTH_SYSTEM_DOCUMENTATION.md](AUTH_SYSTEM_DOCUMENTATION.md) | Complete authentication details |
| [TEST_CHECKLIST.md](TEST_CHECKLIST.md) | All test scenarios (80+ tests) |
| [ARCHITECTURE.md](ARCHITECTURE.md) | System architecture and design |
| [DESIGN.md](DESIGN.md) | Business logic and workflows |
| [TESTING.md](TESTING.md) | Testing guide and workflows |

---

## 🔄 Workflow Overview

### Complete User Journey

```
NEW STUDENT
    ↓
PUBLIC MODE DASHBOARD
    ├── Check Available Assets
    ├── Search for Item to Borrow
    └── Enter ID & Asset Serial
         ↓
    BORROW VALIDATION
    ├── Check: Not already borrowing (1-item limit)
    ├── Check: Not on blacklist
    ├── Check: Asset available
    └── If OK: Create loan record
         ↓
    CONFIRMATION
    ├── Receive confirmation
    ├── Due date shown (1 day for student, 3 days for teacher)
    └── Asset status changed to "borrowed"
         ↓
    USE ASSET (During borrow period)
         ↓
    RETURN PROCESS
    ├── Go to "Pengembalian" section
    ├── Scan/Enter asset serial
    ├── Confirm condition
    └── Submit return
         ↓
    RETURN VALIDATION
    ├── Check: Loan matches asset
    ├── Check: Asset belongs to borrower
    └── If OK: Mark loan as returned
         ↓
    CONFIRMATION
    ├── Return confirmed
    ├── Asset status changed to "available"
    └── User can borrow again

LATE RETURN
    ↓
    OVERDUE DETECTION
    ├── Loan due_date < now
    └── User auto-added to blacklist
         ↓
    BLACKLIST ACTIVE
    ├── User cannot borrow new items
    ├── Admin notified
    └── Admin can clear blacklist
         ↓
    ADMIN CLEARS BLACKLIST
    └── User can borrow again
```

---

## 🔐 Security Notes

### Current Implementation
- ✅ Password-protected admin access
- ✅ Session-based authentication
- ✅ Role-based access control (Public/Admin)
- ✅ Activity logging for audit trail

### For Production Deployment
- ⚠️ Use HTTPS/SSL only
- ⚠️ Implement rate limiting on login
- ⚠️ Add login account lockout
- ⚠️ Use stronger password policy
- ⚠️ Consider two-factor authentication (2FA)
- ⚠️ Implement session timeout
- ⚠️ Use database instead of JSON files
- ⚠️ Run on production PHP server (Nginx, Apache)

---

## 💾 Backup & Recovery

### Backup Your Data
```bash
# Windows Command Prompt
copy database.json database.json.backup
copy activity_logs.json activity_logs.json.backup

# Or use Windows Explorer to copy files
```

### Restore from Backup
```bash
copy database.json.backup database.json
copy activity_logs.json.backup activity_logs.json
```

### Regular Backup Schedule
- **Daily**: Copy JSON files to external drive
- **Weekly**: Upload to cloud storage
- **Monthly**: Archive old activity logs

---

## 🤝 Support & Help

### Quick Help
- Check [LOGIN_GUIDE.md](LOGIN_GUIDE.md) for login issues
- See [TEST_CHECKLIST.md](TEST_CHECKLIST.md) for testing
- Review [ARCHITECTURE.md](ARCHITECTURE.md) for system design

### Common Questions

**Q: Forgot admin password?**  
A: Check [AUTH_SYSTEM_DOCUMENTATION.md](AUTH_SYSTEM_DOCUMENTATION.md#password-configuration) for password reset steps.

**Q: How to add new users?**  
A: Use Admin Mode → Data Pengguna → Add User

**Q: How to add new assets?**  
A: Use Admin Mode → Data Barang → Add Asset

**Q: Can students access admin features?**  
A: No, only password login grants admin access (password-based authentication).

**Q: What happens if user doesn't return item on time?**  
A: User is auto-added to blacklist, cannot borrow until admin clears blacklist.

---

## ✅ Checklist Before Going Live

- [ ] Server tested and working
- [ ] Login/logout tested
- [ ] All 5 admin menus accessible
- [ ] Public mode dashboard shows correctly
- [ ] Borrow and return workflows work
- [ ] Blacklist functionality tested
- [ ] Activity logs recording events
- [ ] Backup files created
- [ ] Documentation reviewed
- [ ] Password changed from default (if production)

---

## 📞 Next Steps

1. **Start Server**: `php -S 127.0.0.1:8000`
2. **Open Browser**: `http://127.0.0.1:8000`
3. **Test Public Mode**: Borrow and return an asset
4. **Login Admin**: Use password `admin123`
5. **Explore Admin Features**: Try all 5 menus
6. **Review Documentation**: Read [ARCHITECTURE.md](ARCHITECTURE.md)
7. **Run Test Checklist**: Follow [TEST_CHECKLIST.md](TEST_CHECKLIST.md)
8. **Backup Data**: Copy JSON files regularly

---

**Welcome to SIM-IV! 🎉**

For detailed information, see the documentation files listed above.

Last updated: December 25, 2025
