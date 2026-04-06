# Login Admin - Troubleshooting Guide

## ✅ Status: API Tested & Working
The login API has been tested and confirmed **WORKING**. If you can't login, follow this guide.

---

## 🔍 Troubleshooting Steps

### Step 1: Open Browser Developer Console
1. Press **F12** or **Right-Click → Inspect**
2. Go to **Console** tab
3. Keep this open while testing login

### Step 2: Use Debug Test Button
1. Go to **http://127.0.0.1:8000/?view=login**
2. Click **"Test API (Debug)"** button (appears below login button)
3. You should see: ✅ **success: true** message
4. Check the Console (F12) for detailed logs

### Step 3: Manual Login Test
If test button works but login doesn't:
1. Clear password field completely
2. Type: `admin123` (exactly)
3. Click **"Login"** button
4. Check Console (F12) for error messages

---

## 🛠️ Quick Fixes

### Problem: "Login gagal" or "Password salah"
**Solution:**
- Confirm password is **exactly**: `admin123`
- No spaces before/after
- Not `admin 123` or `Admin123`

### Problem: No response after clicking Login
**Solution:**
1. Check if server is running: http://127.0.0.1:8000 (should load)
2. Open F12 Console tab
3. Click Login
4. Look for any red error messages in Console
5. Report the error message

### Problem: Login seems to work but no redirect
**Solution:**
1. Open Console (F12)
2. Look for: `Login response: {success: true}`
3. If you see this, wait 1 second for redirect
4. If no redirect happens, manually go to: `http://127.0.0.1:8000/?view=logs`

### Problem: "Kesalahan: Failed to fetch"
**Solution:**
- Make sure server is running
- Try: http://127.0.0.1:8000 (should load dashboard)
- Restart server if needed

---

## 📊 Expected Behavior

### Correct Login Flow:
```
1. Click "Login Admin" button in navbar
   ↓
2. Opens login page (?view=login)
   ↓
3. Enter password: admin123
   ↓
4. Click "Login" button
   ↓
5. See green success message: ✅ Login berhasil!
   ↓
6. Automatically redirected to: ?view=logs (after 1 second)
   ↓
7. Navbar changes: "Public Mode" → "Admin Mode" 🛡️
```

### After Successful Login:
- Navbar shows: **Dashboard | Pengembalian Barang | Data Barang | Data Pengguna | Log & Aktivitas**
- Badge shows: **🛡️ Admin Mode** (not 🔒 Public Mode)
- Can access all admin-only pages

---

## 🧪 Advanced Debugging

### Check Server Logs
Open another terminal and monitor server logs:
```powershell
cd d:\Website\Barang
php -S 127.0.0.1:8000
```
Look for messages like: `Login attempt with password: adm***`

### Test API Directly with PowerShell
```powershell
$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/?action=login" `
  -Method POST `
  -Headers @{"Content-Type"="application/json"} `
  -Body '{"password":"admin123"}' `
  -UseBasicParsing

$response.Content | ConvertFrom-Json
```

Expected output:
```
success message
------- -------
   True ✅ Login berhasil! Anda sekarang memiliki akses admin.
```

---

## 📝 Browser Console Errors - What They Mean

| Error | Cause | Solution |
|-------|-------|----------|
| `Failed to fetch` | Server not running | Start server: `php -S 127.0.0.1:8000` |
| `HTTP Error: 500` | Server error | Check server logs for details |
| `SyntaxError: Unexpected token` | Invalid JSON response | Report to developer |
| `Kesalahan: Network error` | Network/CORS issue | Restart browser, try again |

---

## 🔐 Important Notes

### Default Password: `admin123`
- Used for development/testing
- **Change this in production!**
- Set via environment variable: `ADMIN_PASSWORD`

### Session Management
- Login creates PHP session
- Session persists across page reloads
- Logout destroys session
- Session expires based on PHP config

### Browser Requirements
- JavaScript must be enabled
- Cookies/Sessions must be allowed
- No browser extensions blocking fetch requests

---

## 🆘 If Still Not Working

### Checklist:
- [ ] Server running on http://127.0.0.1:8000 (test by visiting dashboard)
- [ ] Can see "Login Admin" button in navbar
- [ ] Can open login page (?view=login)
- [ ] Browser console shows no red errors
- [ ] "Test API (Debug)" button works
- [ ] Password is exactly `admin123`
- [ ] No browser extensions blocking requests

### Still Stuck?
Provide these details:
1. **Screenshot** of the login page
2. **Browser console error** (F12 → Console tab)
3. **What happens** when you click login (nothing? error? redirect?)
4. **Server output** from terminal

---

## 💡 Next Steps After Login

Once logged in successfully:
1. You'll see all admin menus in navbar
2. Can access: Data Barang, Data Pengguna, Log & Aktivitas
3. All features available in admin panel
4. Click "Logout" to exit admin mode

---

**Updated:** December 25, 2025
**Status:** Testing & Debugging Enabled ✅
