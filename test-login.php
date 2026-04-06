<?php
// TEST LOGIN - File untuk debugging
session_start();

header('Content-Type: text/html; charset=utf-8');

// Test 1: Session Start
echo "<h2>Test 1: Session Status</h2>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'ACTIVE' : 'INACTIVE') . "<br>";
echo "Session Data: " . json_encode($_SESSION) . "<br><br>";

// Test 2: Check if already logged in
echo "<h2>Test 2: Check Login Status</h2>";
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
echo "Is Logged In: " . ($isLoggedIn ? 'YES' : 'NO') . "<br><br>";

// Test 3: Try Login
if (isset($_GET['action']) && $_GET['action'] === 'login') {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['login_time'] = time();
    $_SESSION['test'] = 'Login berhasil!';
    
    echo "<h2>Test 3: Login Executed</h2>";
    echo "Session set: admin_logged_in = true<br>";
    echo "Session Data: " . json_encode($_SESSION) . "<br>";
    echo "<a href='test-login.php'>Refresh untuk check session persist</a><br><br>";
}

// Test 4: Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    echo "<h2>Test 4: Logout Executed</h2>";
    echo "Session destroyed<br>";
    echo "<a href='test-login.php'>Refresh</a><br><br>";
}

// Test 5: Links
echo "<h2>Test Actions</h2>";
echo "<a href='test-login.php?action=login'>Test Login</a><br>";
echo "<a href='test-login.php?action=logout'>Test Logout</a><br>";
echo "<a href='test-login.php'>Refresh</a><br><br>";

// Test 6: Check prototype.php integration
echo "<h2>Test 6: Integration Links</h2>";
echo "<a href='prototype.php'>Go to Prototype (Main App)</a><br>";
echo "<a href='prototype.php?view=login'>Go to Login Page</a><br>";
echo "<a href='prototype.php?view=dashboard'>Go to Dashboard</a><br>";
?>
