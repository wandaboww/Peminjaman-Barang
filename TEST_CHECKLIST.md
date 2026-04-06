# Test Checklist untuk SIM-IV Login System

## Pre-Flight Checks

### Server Status
- [ ] PHP server running on 127.0.0.1:8000
  ```bash
  php -S 127.0.0.1:8000
  ```
  Expected: "PHP X.X.X Development Server started"

- [ ] Can access homepage
  ```
  http://127.0.0.1:8000
  ```
  Expected: 200 OK, public dashboard shows

- [ ] Can access login page
  ```
  http://127.0.0.1:8000/?view=login
  ```
  Expected: Login form with password input field

## Public Mode Tests

### Navigation
- [ ] Public dashboard shows
  - [ ] "Public Mode" badge visible in navbar
  - [ ] "Login Admin" button visible in navbar
  - [ ] 1 menu in navbar (Dashboard only, no other menus)

### Clicking Login Button
- [ ] Click "Login Admin" button
  Expected: Navigate to `?view=login`

## Login Form Tests

### Form Elements
- [ ] Password input field present
- [ ] "Login Admin" button present
- [ ] "Kembali ke Public Mode" link present
- [ ] Password placeholder text visible
- [ ] Default password hint shows: `admin123`

### Form Interaction
- [ ] Can type in password field
- [ ] Enter key submits form
- [ ] Button click submits form

## Authentication Tests

### Invalid Password
- [ ] Enter wrong password (e.g., `wrong123`)
- [ ] Click "Login Admin"
- [ ] Expected: Alert shows "❌ Password salah!"
- [ ] Expected: Stay on login page
- [ ] Expected: Password field cleared and focused

### Correct Password
- [ ] Enter `admin123`
- [ ] Click "Login Admin"
- [ ] Open Browser Console (F12)
  - [ ] Check for JavaScript errors
  - [ ] Should see console.logs: "LOGIN ATTEMPT", "SUCCESS", "Redirecting..."
- [ ] Expected: Alert shows "✅ Login berhasil!"
- [ ] Expected: Redirect to `?view=dashboard`

## Admin Mode Tests (After Successful Login)

### Navbar Changes
- [ ] "Public Mode" badge replaced with "Logout" button
- [ ] Navbar shows 5 menus:
  1. [ ] Dashboard
  2. [ ] Pengembalian
  3. [ ] Data Barang
  4. [ ] Data Pengguna
  5. [ ] Log & Aktivitas

### Dashboard Content
- [ ] Admin version of dashboard displays
- [ ] Header shows admin-specific content
- [ ] Peminjaman section visible (without Pengembalian)

### Menu Navigation
- [ ] Can click "Pengembalian" → loads return page
- [ ] Can click "Data Barang" → loads assets page
- [ ] Can click "Data Pengguna" → loads users page
- [ ] Can click "Log & Aktivitas" → loads logs page
- [ ] Can click "Dashboard" → returns to admin dashboard

## Logout Tests

### Logout Button
- [ ] Click "Logout" button in navbar
- [ ] Expected: Confirmation alert
- [ ] Expected: Redirect to public dashboard

### After Logout
- [ ] Public Mode badge re-appears
- [ ] "Login Admin" button re-appears
- [ ] "Logout" button disappears
- [ ] All admin menus hidden
- [ ] Can't access admin pages by direct URL (should redirect to public dashboard)

### Protected Pages Test
- [ ] Try to access `?view=return` without login → redirect to public dashboard
- [ ] Try to access `?view=assets` without login → redirect to public dashboard
- [ ] Try to access `?view=users` without login → redirect to public dashboard
- [ ] Try to access `?view=logs` without login → redirect to public dashboard

## Browser Developer Tools Tests

### Network Tab (F12 → Network)
- [ ] Login page load shows 200 status
- [ ] Clicking "Login Admin" sends POST to `?action=login`
- [ ] POST response status: 200
- [ ] POST response contains: `"success":true`
- [ ] Redirect to dashboard shows GET to `?view=dashboard` (200 OK)

### Console Tab (F12 → Console)
- [ ] No red error messages after login
- [ ] Login console logs appear:
  - "=== LOGIN ATTEMPT ==="
  - "Sending login request to ?action=login"
  - "Response received, status: 200"
  - "Parsed response: {...}"
  - "Login SUCCESS!"

### Application/Storage Tab (F12 → Application/Storage)
- [ ] PHPSESSID cookie exists
- [ ] PHPSESSID cookie has secure flags:
  - [ ] Path: `/`
  - [ ] Domain: `127.0.0.1`
  - [ ] Expires/Max-Age: Session or configured value

## Session Persistence Tests

### Manual Session Check
1. After successful login, open browser console (F12)
2. Run this JavaScript:
   ```javascript
   fetch('?action=check_session')
     .then(r => r.json())
     .then(d => console.log('Session Status:', d))
   ```
3. Expected output: `"is_logged_in": true`

### Page Refresh Test
- [ ] After login, press F5 to refresh page
- [ ] Navbar still shows logout button (session persisted)
- [ ] Admin menus still visible
- [ ] No redirect to public dashboard

### New Tab Test
- [ ] Open new tab
- [ ] Navigate to `http://127.0.0.1:8000`
- [ ] Expected: Should show admin dashboard (session shared across tabs)
- [ ] Expected: Logout button visible

## Cross-Browser Tests (Optional)

- [ ] Test in Chrome/Edge
- [ ] Test in Firefox
- [ ] Test in Safari (if available)
- [ ] Test in Incognito/Private mode

## Performance & Load Tests

- [ ] Login completes in < 2 seconds
- [ ] No console warnings about slow requests
- [ ] No memory leaks (check DevTools > Memory)
- [ ] Works with slow network (test with DevTools throttling)

## Edge Cases & Security

- [ ] Empty password rejected with alert
- [ ] Very long password handled correctly
- [ ] Special characters in password work: `!@#$%`
- [ ] SQL injection attempt blocked: `' OR '1'='1`
- [ ] Session expires after inactivity (if timeout configured)
- [ ] Can't access admin pages with manipulated session cookie
- [ ] CSRF protection (if implemented)

## Database & Logging

- [ ] Login attempts logged to `activity_logs.json`
- [ ] Failed login not logged (security)
- [ ] Successful login shows in activity logs
- [ ] Logout logged in activity logs
- [ ] Log entries have timestamps

## Final Checklist

Before declaring system ready:
- [ ] All Green tests above pass
- [ ] No errors in browser console
- [ ] No errors in PHP error logs
- [ ] Server handles multiple simultaneous logins
- [ ] Performance acceptable (< 1 sec response time)
- [ ] Documentation matches implementation
- [ ] Password can be changed via environment variable
- [ ] Production warning shown if using default password

---

## Testing Summary

**Total Tests**: ~80  
**Pass Criteria**: All tests green ✅  
**Fail Criteria**: Any test red ❌ = needs debugging  

**Quick Test (5 min)**:
1. Start server
2. Open public dashboard
3. Click login
4. Enter admin123
5. Check admin menu appears
6. Click logout
7. Check public mode returns

