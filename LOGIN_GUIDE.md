# 🔐 Admin Login Guide - SIM-IV

## Quick Start

### 1. **Server Status Check**
✅ Server sudah running di `http://127.0.0.1:8000`
✅ Login page tersedia di `http://127.0.0.1:8000/?view=login`

### 2. **Login Credentials**
- **Username**: Not required (system menggunakan password-only authentication)
- **Password**: `admin123`

### 3. **Step-by-Step Login**

#### Step 1: Open Login Page
```
Go to: http://127.0.0.1:8000/?view=login
```

#### Step 2: Enter Password
1. Click on password input field
2. Type: `admin123`
3. Press Enter or click "Login Admin" button

#### Step 3: Verify Login Success
After successful login, you should:
- See a success alert: "✅ Login berhasil! Mengarahkan ke Admin Mode..."
- Get redirected to admin dashboard
- See navbar change from "Public Mode" to "Logout" button
- See all 5 admin menus: Dashboard, Pengembalian, Data Barang, Data Pengguna, Log & Aktivitas

### 4. **Troubleshooting**

#### Problem: Login button not working / No redirect
**Solution steps:**
1. Open Browser Developer Tools: Press `F12`
2. Go to **Console** tab
3. Click "Login Admin" button
4. Check console for error messages
5. Look at **Network** tab to see if POST request was sent to `?action=login`

#### Problem: "Password salah!" error
**Solution:**
- Make sure you typed exactly: `admin123`
- Check Caps Lock is OFF
- Password is case-sensitive

#### Problem: Session not persisting (no logout button after redirect)
**Solution:**
1. Clear browser cache: Ctrl+Shift+Delete
2. Close all browser tabs for this site
3. Try logging in again
4. If still not working, manually check session:
   - Open console (F12)
   - Run: `fetch('?action=check_session').then(r => r.json()).then(d => console.log(d))`
   - Should show: `"is_logged_in": true`

### 5. **API Endpoints for Testing**

#### Login Endpoint
```bash
POST http://127.0.0.1:8000/?action=login
Content-Type: application/json

Request body:
{
  "password": "admin123"
}

Success response (200):
{
  "success": true,
  "message": "✅ Login berhasil! Anda sekarang memiliki akses admin.",
  "session_id": "abc123..."
}

Error response (401):
{
  "success": false,
  "message": "❌ Password salah! Gunakan password default: admin123"
}
```

#### Check Session Status
```bash
POST http://127.0.0.1:8000/?action=check_session

Response:
{
  "is_logged_in": true/false,
  "session_id": "abc123...",
  "session_data": {...}
}
```

#### Logout Endpoint
```bash
POST http://127.0.0.1:8000/?action=logout

Response:
{
  "success": true,
  "message": "✅ Logout berhasil."
}
```

### 6. **Browser Console Testing**

After opening `http://127.0.0.1:8000/?view=login`:

1. **Test Login via Console**:
```javascript
// In browser console (F12):
fetch('?action=login', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  credentials: 'same-origin',
  body: JSON.stringify({password: 'admin123'})
})
.then(r => r.json())
.then(d => console.log('Response:', d))
.catch(e => console.error('Error:', e))
```

2. **Check Current Session**:
```javascript
fetch('?action=check_session')
  .then(r => r.json())
  .then(d => console.log('Session:', d))
```

3. **Check if isLoggedIn function works**:
```javascript
// This checks what the server knows about your session
fetch('?action=check_session')
  .then(r => r.json())
  .then(d => {
    console.log('Is logged in:', d.is_logged_in);
    console.log('Session ID:', d.session_id);
  })
```

### 7. **JavaScript Function Reference**

The login button uses the `loginAdmin()` function which:
1. Gets password from input field
2. Sends POST to `?action=login` with password
3. On success:
   - Shows alert
   - Waits 500ms for session to be written
   - Redirects to `?view=dashboard`
4. On error:
   - Shows error alert
   - Clears password field
   - Re-focuses on input

### 8. **Admin Mode Features**

Once logged in, you have access to:

**Dashboard** (`?view=dashboard`)
- Admin view with Peminjaman only
- See borrowing data
- Manage system

**Pengembalian** (`?view=return`)
- Admin-only: Return/checkin items
- Full control over return process
- Can mark items as damaged

**Data Barang** (`?view=assets`)
- Manage all school assets
- Add/edit/delete assets
- View asset status
- Track asset availability

**Data Pengguna** (`?view=users`)
- Manage users (admin, teacher, student)
- Add/edit/delete users
- Manage blacklist
- View user roles

**Log & Aktivitas** (`?view=logs`)
- View all system activity logs
- Track all borrowing/return transactions
- System audit trail
- Clear logs (admin only)

### 9. **Session Details**

**Session File Location:**
- Stored in PHP's default session directory
- Session ID stored in PHPSESSID cookie
- Session value: `$_SESSION['admin_logged_in'] = true`
- Session created on: Login timestamp stored in `$_SESSION['login_time']`

**Cache Control Headers Added:**
To prevent browser caching from interfering with session state:
```
Cache-Control: no-store, no-cache, must-revalidate, max-age=0
Pragma: no-cache
Expires: 0
```

### 10. **Common Issues & Fixes**

| Issue | Cause | Solution |
|-------|-------|----------|
| Port 8000 in use | Another process using port | `netstat -ano \| findstr :8000` or change port |
| 404 error | Server not running | Run `php -S 127.0.0.1:8000` in project folder |
| CORS error | Cross-origin issue | Use `credentials: 'same-origin'` in fetch (already done) |
| Session not saving | File permissions | Check `/tmp` or system temp folder permissions |
| Redirect loop | Cache issue | Clear browser cache or use incognito mode |

---

**Last Updated**: Dec 25, 2025  
**Status**: ✅ Active & Tested  
**Authentication Type**: Password-only (development)
