# 🔐 Authentication System Documentation - SIM-IV

## Architecture Overview

The SIM-IV system uses a **2-flow authentication model**:

### Flow 1: Public Mode (Unauthenticated)
- Users can access dashboard with embedded Peminjaman (Borrowing)
- Users can access Pengembalian (Return/Checkin)
- **Cannot**: Access Data Barang, Data Pengguna, or Logs
- **UI**: Single navbar menu "Dashboard" + "Login Admin" button

### Flow 2: Admin Mode (Authenticated)
- Full access to all 5 admin menus
- Can manage users, assets, view logs
- Can clear activity logs
- **UI**: Navbar shows "Logout" button + 5 menu options

---

## Authentication Implementation

### 1. AuthManager Class

**Location**: [prototype.php](prototype.php#L47-L86)

```php
class AuthManager {
    private static $adminPassword = null;
    
    // Get admin password from environment or default
    private static function getAdminPassword() { ... }
    
    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && 
               $_SESSION['admin_logged_in'] === true;
    }
    
    // Login with password
    public static function login($password) {
        if ($password === self::getAdminPassword()) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_time'] = time();
            return true;
        }
        return false;
    }
    
    // Logout (destroy session)
    public static function logout() {
        session_destroy();
    }
}
```

### 2. Session Management

**Session Start**: Line 3
```php
<?php
session_start();
```

**Session Variables**:
- `$_SESSION['admin_logged_in']`: Boolean flag (true = authenticated)
- `$_SESSION['login_time']`: Timestamp of login

**Session Configuration**:
```php
// Cache prevention headers (Lines 6-8)
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
```

### 3. Login Endpoint

**Location**: [prototype.php](prototype.php#L283-L310)
**Method**: POST
**URL**: `?action=login`
**Content-Type**: `application/json`

**Request**:
```json
{
  "password": "admin123"
}
```

**Success Response** (HTTP 200):
```json
{
  "success": true,
  "message": "✅ Login berhasil! Anda sekarang memiliki akses admin.",
  "session_id": "abc123xyz..."
}
```

**Error Response** (HTTP 401):
```json
{
  "success": false,
  "message": "❌ Password salah! Gunakan password default: admin123"
}
```

**Backend Logic**:
1. Receive POST with password
2. Validate password against configured admin password
3. If valid:
   - Set `$_SESSION['admin_logged_in'] = true`
   - Force session write with `session_write_close()`
   - Return JSON success response
4. If invalid:
   - Return JSON error response
   - No session modification

### 4. Logout Endpoint

**Location**: [prototype.php](prototype.php#L312-L316)
**Method**: POST
**URL**: `?action=logout`

**Response**:
```json
{
  "success": true,
  "message": "✅ Logout berhasil."
}
```

**Backend Logic**:
1. Call `session_destroy()`
2. All session data cleared
3. Return success response

### 5. View Authorization

**Location**: [prototype.php](prototype.php#L752-L760)

```php
// Pages that require admin authentication
$adminOnlyViews = ['return', 'assets', 'users', 'logs'];
$view = $_GET['view'] ?? 'dashboard';

// Redirect unauthorized access to public dashboard
if (in_array($view, $adminOnlyViews) && !AuthManager::isLoggedIn()) {
    $view = 'dashboard';
}
```

**Protected Pages**:
- `?view=return` - Return/Checkin items (admin only)
- `?view=assets` - Manage assets (admin only)
- `?view=users` - Manage users (admin only)
- `?view=logs` - View activity logs (admin only)

**Public Pages**:
- `?view=dashboard` - Dashboard (dual mode based on auth)
- `?view=login` - Login form (always accessible)

### 6. Frontend Login Function

**Location**: [prototype.php](prototype.php#L3487-L3556)

```javascript
function loginAdmin() {
    // 1. Get password from form
    const password = document.getElementById('adminPassword').value;
    
    // 2. Validate not empty
    if (!password) {
        alert('⚠️ Masukkan password terlebih dahulu!');
        return;
    }
    
    // 3. Disable button (show loading)
    
    // 4. Send POST to ?action=login
    fetch('?action=login', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        credentials: 'same-origin',  // Send cookies
        body: JSON.stringify({password: password})
    })
    
    // 5. On success
    .then(res => res.text())
    .then(text => {
        const data = JSON.parse(text);
        if (data.success) {
            // Wait 500ms for session to write
            setTimeout(() => {
                window.location.href = '?view=dashboard';
            }, 500);
        } else {
            // Show error
            alert("❌ " + data.message);
        }
    })
}
```

**Key Features**:
- Comprehensive console logging for debugging
- Uses `credentials: 'same-origin'` to send session cookies
- 500ms delay before redirect to ensure session persists
- Button disabled during request (prevent double-click)
- Error handling with user-friendly messages

---

## Password Configuration

### Default Password
- **Value**: `admin123`
- **Location**: Hardcoded in [AuthManager::getAdminPassword()](prototype.php#L52-L65)
- **Usage**: Development/testing only

### Environment Variable Configuration
**File**: `.env` (if exists)
```bash
ADMIN_PASSWORD=my_secure_password_123
APP_ENV=production
```

**Loading Priority**:
1. Check `$_ENV['ADMIN_PASSWORD']`
2. Check `getenv('ADMIN_PASSWORD')`
3. Fall back to `admin123`

**Production Warning**:
If using default password in production mode:
```
WARNING: Using default admin password in production!
```

---

## Security Considerations

### Current Implementation (Development)
✅ Password-based authentication  
✅ Session-based persistence  
✅ HTTP-only cookie support  
✅ Cache prevention headers  
✅ Activity logging  

### Recommended for Production
⚠️ HTTPS/SSL only (never HTTP)  
⚠️ Rate limiting on login attempts  
⚠️ Account lockout after N failed attempts  
⚠️ Stronger password policy  
⚠️ Two-factor authentication (2FA)  
⚠️ Login audit logging  
⚠️ Session timeout mechanism  
⚠️ IP whitelisting (optional)  
⚠️ Role-based access control (RBAC)  

### Current Vulnerabilities (Development Only)
❌ No rate limiting
❌ No account lockout
❌ Password in code
❌ No HTTPS
❌ No CSRF tokens (rely on same-origin)
❌ Simple password validation
❌ No login history
❌ No session timeout

---

## Session Lifecycle

### 1. Initial Access (No Session)
```
GET http://127.0.0.1:8000
├── PHP: session_start() creates new session
├── Server: Response includes Set-Cookie: PHPSESSID=...
└── Client: Browser stores PHPSESSID cookie
```

### 2. Login Request
```
POST http://127.0.0.1:8000/?action=login
├── Client: Sends password + PHPSESSID cookie
├── PHP:
│   ├── Validate password
│   ├── $_SESSION['admin_logged_in'] = true
│   ├── session_write_close() [force flush]
│   └── Return JSON success
└── Client: Stores response, prepares redirect
```

### 3. Redirect to Dashboard
```
GET http://127.0.0.1:8000/?view=dashboard
├── Client: Sends PHPSESSID cookie
├── PHP:
│   ├── session_start() resumes session
│   ├── Check: $_SESSION['admin_logged_in'] === true
│   ├── AuthManager::isLoggedIn() returns true
│   └── Load admin dashboard variant
└── Client: Displays admin interface
```

### 4. Navigation Within Admin
```
GET http://127.0.0.1:8000/?view=return
├── Client: Sends PHPSESSID cookie (automatic)
├── PHP:
│   ├── session_start() resumes session
│   ├── Check admin authorization
│   ├── Load admin return page
│   └── Persist session
└── Client: Displays page
```

### 5. Logout
```
POST http://127.0.0.1:8000/?action=logout
├── Client: Sends PHPSESSID cookie
├── PHP:
│   ├── session_destroy() clears all data
│   ├── Return JSON success
│   └── Session effectively ended
└── Client: Redirect to public dashboard
```

---

## Debugging & Testing

### Check Session Status
**JavaScript Console**:
```javascript
fetch('?action=check_session')
  .then(r => r.json())
  .then(d => console.log('Session:', d))
```

**Response**:
```json
{
  "is_logged_in": true,
  "session_id": "abc123xyz...",
  "session_data": {
    "admin_logged_in": true,
    "login_time": 1703484000
  }
}
```

### View PHP Error Logs
```bash
# On Windows
type php_error.log

# On Linux/Mac
tail -f php_error.log
```

### Monitor Login Attempts
**File**: `activity_logs.json`  
Look for entries with action type "login" or "logout"

```json
{
  "timestamp": "2025-12-25 10:30:00",
  "user": "system",
  "action": "login",
  "details": "Admin login successful",
  "status": "success"
}
```

---

## API Reference

### Endpoints Summary

| Endpoint | Method | Auth | Purpose |
|----------|--------|------|---------|
| `?action=login` | POST | None | Authenticate user |
| `?action=logout` | POST | Required | End session |
| `?action=check_session` | POST | N/A | Debug session status |
| `?view=login` | GET | N/A | Show login form |
| `?view=dashboard` | GET | N/A | Show dashboard (dual mode) |
| `?view=return` | GET | Required | Return items (admin) |
| `?view=assets` | GET | Required | Manage assets (admin) |
| `?view=users` | GET | Required | Manage users (admin) |
| `?view=logs` | GET | Required | View logs (admin) |

### Example CURL Commands

**Login**:
```bash
curl -X POST "http://127.0.0.1:8000/?action=login" \
  -H "Content-Type: application/json" \
  -d '{"password":"admin123"}' \
  -c cookies.txt
```

**Check Session**:
```bash
curl -X POST "http://127.0.0.1:8000/?action=check_session" \
  -b cookies.txt
```

**Logout**:
```bash
curl -X POST "http://127.0.0.1:8000/?action=logout" \
  -b cookies.txt
```

---

## File References

| File | Lines | Purpose |
|------|-------|---------|
| [prototype.php](prototype.php#L1-L10) | 1-10 | Session init + cache headers |
| [prototype.php](prototype.php#L47-L86) | 47-86 | AuthManager class definition |
| [prototype.php](prototype.php#L283-L320) | 283-320 | Login/logout action handlers |
| [prototype.php](prototype.php#L752-L760) | 752-760 | View authorization logic |
| [prototype.php](prototype.php#L1275-L1310) | 1275-1310 | Login form HTML |
| [prototype.php](prototype.php#L1318-L1350) | 1318-1350 | Admin navbar conditional |
| [prototype.php](prototype.php#L3487-L3556) | 3487-3556 | loginAdmin() JavaScript function |

---

## Quick Reference

### Login Flow Diagram
```
┌─────────────────┐
│  Public Mode    │
│  (No Session)   │
└────────┬────────┘
         │
         │ Click "Login Admin"
         ▼
┌─────────────────┐
│  Login Form     │
│  (GET ?view=login)
└────────┬────────┘
         │
         │ Enter Password
         │ Click Login Button
         ▼
┌─────────────────┐
│ POST ?action=login
│ Body: {password}
└────────┬────────┘
         │
         │ Validate Password
         ▼
    ┌────┴─────┐
    │           │
   YES         NO
    │           │
    ▼           ▼
┌───────┐  ┌──────────┐
│Set    │  │Show Error│
│Session│  │Alert     │
└───┬───┘  └──────────┘
    │
    │ Redirect
    ▼
┌──────────────────┐
│ GET ?view=dashboard
│ (Admin Mode)
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ Admin Dashboard  │
│ + 5 Menu Items   │
│ + Logout Button  │
└──────────────────┘
```

### Key Takeaways
1. **Authentication**: Password-only, session-based
2. **Authorization**: View-based (some pages require login)
3. **Session**: PHP native `$_SESSION` with cookies
4. **Default Password**: `admin123` (configurable via `.env`)
5. **Logout**: Clears entire session
6. **Persistence**: Works across page refreshes and new tabs

---

**Last Updated**: December 25, 2025  
**Version**: 1.0  
**Status**: ✅ Documented & Tested
