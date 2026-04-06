<?php
// Start session untuk authentication
session_start();

// Set Content-Type untuk HTML
header('Content-Type: text/html; charset=UTF-8');

// Prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// ==========================================
// ENVIRONMENT CONFIGURATION LOADER
// ==========================================
function loadEnv($filePath = '.env') {
    if (!file_exists($filePath)) {
        return; // Gunakan default values jika .env tidak ada
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Set sebagai environment variable (hanya jika belum ada)
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }
}

// Load .env file jika ada
loadEnv(__DIR__ . '/.env');

// ==========================================
// SIMPLE AUTHENTICATION & AUTHORIZATION SYSTEM
// ==========================================
class AuthManager {
    private static $adminPassword = null;
    
    /**
     * Get admin password dari environment atau gunakan default (development only)
     */
    private static function getAdminPassword() {
        if (self::$adminPassword === null) {
            // Coba dari environment variable dulu
            self::$adminPassword = $_ENV['ADMIN_PASSWORD'] ?? 
                                   getenv('ADMIN_PASSWORD') ?? 
                                   'admin123'; // Default untuk development
            
            // Warn jika pakai default (di production)
            if (self::$adminPassword === 'admin123' && 
                ($_ENV['APP_ENV'] ?? getenv('APP_ENV')) === 'production') {
                error_log('WARNING: Using default admin password in production!');
            }
        }
        return self::$adminPassword;
    }
    
    /**
     * Debug method - show password for testing (remove in production)
     */
    public static function getPasswordForDebug() {
        return self::getAdminPassword();
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    public static function login($password) {
        if ($password === self::getAdminPassword()) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_time'] = time();
            return true;
        }
        return false;
    }
    
    public static function logout() {
        session_destroy();
    }
    
    public static function isAdmin() {
        return self::isLoggedIn();
    }
}

// prototype.php - Single File Simulation of the School Inventory System

// 1. Database Connection & Setup (SQLite)
// 1. JSON-Based Database Engine (No-Extension Fallback)
class JsonDB {
    private string $file;
    private array $data;

    public function __construct($file) {
        $this->file = $file;
        if (!file_exists($file)) {
            $this->data = [
                'users' => [], 
                'assets' => [], 
                'loans' => [], 
                'settings' => [
                    'running_text' => 'Selamat datang di Sistem Manajemen Aset Sekolah',
                    'animation_speed' => '20',
                    'bg_color' => '#667eea',
                    'bg_color_end' => '#764ba2',
                    'text_color' => '#ffffff',
                    'font_family' => 'Arial, sans-serif'
                ]
            ];
            $this->save();
            $this->seed();
        } else {
            $this->data = json_decode(file_get_contents($file), true);
            // Ensure settings key exists with all required keys
            if (!isset($this->data['settings'])) {
                $this->data['settings'] = [
                    'running_text' => 'Selamat datang di Sistem Manajemen Aset Sekolah',
                    'animation_speed' => '20',
                    'bg_color' => '#667eea',
                    'bg_color_end' => '#764ba2',
                    'text_color' => '#ffffff',
                    'font_family' => 'Arial, sans-serif'
                ];
                $this->save();
            } else {
                // Backward compatibility: add missing settings keys
                if (!isset($this->data['settings']['animation_speed'])) {
                    $this->data['settings']['animation_speed'] = '20';
                }
                if (!isset($this->data['settings']['bg_color'])) {
                    $this->data['settings']['bg_color'] = '#667eea';
                }
                if (!isset($this->data['settings']['bg_color_end'])) {
                    $this->data['settings']['bg_color_end'] = '#764ba2';
                }
                if (!isset($this->data['settings']['text_color'])) {
                    $this->data['settings']['text_color'] = '#ffffff';
                }
                if (!isset($this->data['settings']['font_family'])) {
                    $this->data['settings']['font_family'] = 'Arial, sans-serif';
                }
                $this->save();
            }
        }
    }

    private function save() {
        file_put_contents($this->file, json_encode($this->data, JSON_PRETTY_PRINT));
    }

    private function seed() {
        // Users
        $this->insert('users', ['id' => 1, 'name' => 'Pak Budi (Guru)', 'identity_number' => '19800101', 'role' => 'teacher', 'kelas' => '-']);
        $this->insert('users', ['id' => 2, 'name' => 'Ani (Siswa)', 'identity_number' => '2024001', 'role' => 'student', 'kelas' => '10 PPLG 1']);
        $this->insert('users', ['id' => 3, 'name' => 'Budi (Siswa Nakal)', 'identity_number' => '2024002', 'role' => 'student', 'kelas' => '10 PPLG 2']);

        // Assets
        $this->insert('assets', ['id' => 1, 'brand' => 'Lenovo', 'model' => 'ThinkPad X1', 'serial_number' => 'LNV-001', 'status' => 'available']);
        $this->insert('assets', ['id' => 2, 'brand' => 'Epson', 'model' => 'Projector EB-X', 'serial_number' => 'EPS-001', 'status' => 'available']);
        $this->insert('assets', ['id' => 3, 'brand' => 'Logitech', 'model' => 'Mouse Wireless', 'serial_number' => 'LOG-001', 'status' => 'maintenance']);
    }

    public function getAll($table) {
        return $this->data[$table];
    }

    public function find($table, $id) {
        foreach ($this->data[$table] as $item) {
            if ($item['id'] == $id) return $item;
        }
        return null;
    }

    public function findByColumn($table, $column, $value) {
        foreach ($this->data[$table] as $item) {
            if (isset($item[$column]) && strtolower($item[$column]) === strtolower($value)) return $item;
        }
        return null;
    }

    public function insert($table, $item) {
        $id = count($this->data[$table]) + 1;
        $item['id'] = $id;
        $this->data[$table][] = $item;
        $this->save();
        return $id;
    }

    public function update($table, $id, $updates) {
        foreach ($this->data[$table] as &$item) {
            if ($item['id'] == $id) {
                foreach ($updates as $k => $v) {
                    $item[$k] = $v;
                }
                $this->save();
                return true;
            }
        }
        return false;
    }

    public function delete($table, $id) {
        foreach ($this->data[$table] as $key => $item) {
            if ($item['id'] == $id) {
                array_splice($this->data[$table], $key, 1);
                $this->save();
                return true;
            }
        }
        return false;
    }

    // Settings Methods
    public function getSetting($key, $default = null) {
        return $this->data['settings'][$key] ?? $default;
    }

    public function setSetting($key, $value) {
        if (!isset($this->data['settings'])) {
            $this->data['settings'] = [];
        }
        $this->data['settings'][$key] = $value;
        $this->save();
        return true;
    }

    public function getAllSettings() {
        return $this->data['settings'] ?? [];
    }

    // Specific Helper: Join Loans
    public function getLoansWithDetails() {
        $loans = [];
        foreach ($this->data['loans'] as $l) {
            $user = $this->find('users', $l['user_id']);
            $asset = $this->find('assets', $l['asset_id']);
            $l['user_name'] = $user['name'] ?? 'Unknown User';
            $l['user_identity'] = $user['identity_number'] ?? 'Unknown';
            $l['user_kelas'] = $user['kelas'] ?? '-';
            $l['asset_name'] = ($asset['brand'] ?? '?') . ' ' . ($asset['model'] ?? '?');
            $l['asset_brand'] = $asset['brand'] ?? '?';
            $l['asset_model'] = $asset['model'] ?? '?';
            $l['asset_serial_number'] = $asset['serial_number'] ?? '?';
            $loans[] = $l;
        }
        return array_reverse($loans);
    }
}

// Activity Logger Class
class ActivityLog {
    private string $file;
    private array $logs = [];

    public function __construct($file) {
        $this->file = $file;
        if (file_exists($file)) {
            $this->logs = json_decode(file_get_contents($file), true) ?? [];
        }
    }

    public function log($action, $table, $data, $details = '') {
        $log = [
            'id' => count($this->logs) + 1,
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => $action, // CREATE, UPDATE, DELETE, BORROW, RETURN
            'table' => $table, // users, assets, loans
            'data' => $data,
            'details' => $details,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ];
        
        array_unshift($this->logs, $log); // Add to beginning (newest first)
        
        // Keep only last 1000 logs
        if (count($this->logs) > 1000) {
            $this->logs = array_slice($this->logs, 0, 1000);
        }
        
        $this->save();
    }

    private function save() {
        file_put_contents($this->file, json_encode($this->logs, JSON_PRETTY_PRINT));
    }

    public function getAll() {
        return $this->logs;
    }

    public function filter($table = null, $action = null) {
        $filtered = $this->logs;
        
        if ($table) {
            $filtered = array_filter($filtered, fn($l) => $l['table'] === $table);
        }
        
        if ($action) {
            $filtered = array_filter($filtered, fn($l) => $l['action'] === $action);
        }
        
        return array_values($filtered);
    }
}

// Initialize DB
$db = new JsonDB(__DIR__ . '/database.json');
$activityLog = new ActivityLog(__DIR__ . '/activity_logs.json');

// 2. Logic Functions
function calculateDueDate($role) {
    $days = ($role === 'teacher') ? 3 : 1;
    return date('Y-m-d H:i:s', strtotime("+$days days"));
}

function checkBlacklist($db, $userId) {
    foreach ($db->getAll('loans') as $loan) {
        if ($loan['user_id'] == $userId && in_array($loan['status'], ['active', 'overdue'])) {
            return true;
        }
    }
    return false;
}

// ==========================================
// TRADITIONAL LOGOUT HANDLER (GET request)
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: ?view=dashboard');
    exit;
}

// ==========================================
// TRADITIONAL LOGIN HANDLER (POST form submit)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'traditional_login') {
    $password = $_POST['password'] ?? '';
    
    if (trim($password) === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['login_time'] = time();
        header('Location: ?view=dashboard');
        exit;
    } else {
        $_SESSION['login_error'] = '❌ Password salah!';
        header('Location: ?view=login');
        exit;
    }
}

// 3. Handle API Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $_GET['action'] ?? '';

    try {
        // Authentication handlers (before other checks)
        if ($action === 'login') {
            header('Content-Type: application/json');
            $password = isset($_POST['password']) ? $_POST['password'] : (isset($input['password']) ? $input['password'] : '');
            
            // SIMPLE LOGIN - Password harus 'admin123'
            if (trim($password) === 'admin123') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['login_time'] = time();
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Login berhasil'
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Password salah'
                ]);
            }
            exit;
        }
        
        if ($action === 'logout' || (isset($_POST['action']) && $_POST['action'] === 'traditional_logout')) {
            // Traditional logout - destroy session dan redirect ke public mode
            session_destroy();
            header('Location: ?view=dashboard');
            exit;
        }

        if ($action === 'check_session') {
            // Debug endpoint untuk check status session
            $isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
            echo json_encode([
                'is_logged_in' => $isLoggedIn,
                'session_id' => session_id(),
                'session_data' => $_SESSION,
                'session_status' => session_status(),
                'cookies' => $_COOKIE
            ]);
            exit;
        }
        
        if ($action === 'clear_logs') {
            // SECURITY: Only admin can clear logs
            if (!AuthManager::isAdmin()) {
                throw new Exception("❌ Akses ditolak! Hanya admin yang dapat menghapus log.");
            }
            
            // Clear the activity logs file
            file_put_contents(__DIR__ . '/activity_logs.json', json_encode([], JSON_PRETTY_PRINT));
            echo json_encode(['success' => true, 'message' => "✅ Semua log berhasil dihapus."]);
            exit;
        }

        if ($action === 'get_running_text') {
            // Get running text (accessible to both public and admin)
            $runningText = $db->getSetting('running_text', 'Selamat datang di Sistem Manajemen Aset Sekolah');
            echo json_encode(['success' => true, 'running_text' => $runningText]);
            exit;
        }

        if ($action === 'get_running_text_settings') {
            // Get all running text settings (public access for display purposes)
            $settings = [
                'running_text' => $db->getSetting('running_text', 'Selamat datang di Sistem Manajemen Aset Sekolah'),
                'animation_speed' => $db->getSetting('animation_speed', '20'),
                'bg_color' => $db->getSetting('bg_color', '#667eea'),
                'bg_color_end' => $db->getSetting('bg_color_end', '#764ba2'),
                'text_color' => $db->getSetting('text_color', '#ffffff'),
                'font_family' => $db->getSetting('font_family', 'Arial, sans-serif')
            ];
            
            echo json_encode(['success' => true, 'settings' => $settings]);
            exit;
        }

        if ($action === 'set_running_text') {
            // SECURITY: Only admin can set running text
            if (!AuthManager::isAdmin()) {
                throw new Exception("❌ Akses ditolak! Hanya admin yang dapat mengubah running text.");
            }

            $text = trim($input['text'] ?? '');
            if (empty($text)) {
                throw new Exception("⚠️ Running text tidak boleh kosong!");
            }

            $db->setSetting('running_text', $text);
            $activityLog->log('UPDATE', 'settings', "Running Text diubah", "Text: $text");
            echo json_encode(['success' => true, 'message' => "✅ Running text berhasil diperbarui."]);
            exit;
        }

        if ($action === 'get_all_settings') {
            // Get all running text settings (admin only)
            if (!AuthManager::isAdmin()) {
                throw new Exception("❌ Akses ditolak!");
            }
            
            $settings = [
                'running_text' => $db->getSetting('running_text', 'Selamat datang di Sistem Manajemen Aset Sekolah'),
                'animation_speed' => $db->getSetting('animation_speed', '20'),
                'bg_color' => $db->getSetting('bg_color', '#667eea'),
                'bg_color_end' => $db->getSetting('bg_color_end', '#764ba2'),
                'text_color' => $db->getSetting('text_color', '#ffffff'),
                'font_family' => $db->getSetting('font_family', 'Arial, sans-serif')
            ];
            
            echo json_encode(['success' => true, 'settings' => $settings]);
            exit;
        }

        if ($action === 'set_all_settings') {
            // SECURITY: Only admin can set settings
            if (!AuthManager::isAdmin()) {
                throw new Exception("❌ Akses ditolak! Hanya admin yang dapat mengubah pengaturan.");
            }

            $settings = $input['settings'] ?? [];
            
            if (empty($settings)) {
                throw new Exception("⚠️ Tidak ada pengaturan untuk disimpan!");
            }

            // Validate and save each setting
            if (isset($settings['running_text'])) {
                $text = trim($settings['running_text']);
                if (empty($text)) {
                    throw new Exception("⚠️ Running text tidak boleh kosong!");
                }
                $db->setSetting('running_text', $text);
            }

            if (isset($settings['animation_speed'])) {
                $speed = intval($settings['animation_speed']);
                if ($speed < 5 || $speed > 60) {
                    throw new Exception("⚠️ Kecepatan animasi harus antara 5 hingga 60 detik!");
                }
                $db->setSetting('animation_speed', $speed);
            }

            if (isset($settings['bg_color'])) {
                $db->setSetting('bg_color', $settings['bg_color']);
            }

            if (isset($settings['bg_color_end'])) {
                $db->setSetting('bg_color_end', $settings['bg_color_end']);
            }

            if (isset($settings['text_color'])) {
                $db->setSetting('text_color', $settings['text_color']);
            }

            if (isset($settings['font_family'])) {
                $db->setSetting('font_family', $settings['font_family']);
            }

            $activityLog->log('UPDATE', 'settings', "Pengaturan Running Text diubah", json_encode($settings));
            echo json_encode(['success' => true, 'message' => "✅ Semua pengaturan telah berhasil disimpan."]);
            exit;
        }
        
        if ($action === 'borrow') {
            // New Logic: Scan-based Input
            $idNumber = trim($input['identity_number'] ?? '');
            $assetCode = trim($input['asset_code'] ?? '');

            if (empty($idNumber) || empty($assetCode)) {
                throw new Exception("⚠️ Harap scan Identity User dan Barcode Barang!");
            }

            // Lookup User
            $user = $db->findByColumn('users', 'identity_number', $idNumber);
            if (!$user) throw new Exception("❌ User dengan ID/NIP '$idNumber' tidak ditemukan.");

            // Lookup Asset
            $asset = $db->findByColumn('assets', 'serial_number', $assetCode);
            if (!$asset) throw new Exception("❌ Barang dengan Kode serial '$assetCode' tidak ditemukan.");

            $userId = $user['id'];
            $assetId = $asset['id'];

            // Logic: Blacklist Check
            if (checkBlacklist($db, $userId)) {
                throw new Exception("⛔ BLOCKED: User ini masih memiliki pinjaman aktif!");
            }

            // Logic: Asset Availability
            if ($asset['status'] !== 'available') {
                throw new Exception("⚠️ Barang tidak tersedia (Status: " . ($asset['status']??'Unknown') . ")");
            }

            // Logic: Get Role for Due Date
            $dueDate = calculateDueDate($user['role']);

            // Execute Transaction
            $loanId = $db->insert('loans', [
                'user_id' => $userId,
                'asset_id' => $assetId,
                'loan_date' => date('Y-m-d H:i:s'),
                'due_date' => $dueDate,
                'status' => 'active'
            ]);

            $db->update('assets', $assetId, ['status' => 'borrowed']);
            
            $activityLog->log('BORROW', 'loans', "{$user['name']} meminjam {$asset['brand']} {$asset['model']}", "Durasi: {$dueDate}");

            echo json_encode(['success' => true, 'message' => "✅ Transaksi Sukses! Batas kembali: $dueDate"]);
            exit;
        }

        if ($action === 'return') {
            // New Logic: Scan-based Return Input
            $idNumber = trim($input['identity_number'] ?? '');
            $assetCode = trim($input['asset_code'] ?? '');
            $condition = $input['condition'] ?? 'good';
            $notes = $input['notes'] ?? '';

            if (empty($idNumber) || empty($assetCode)) {
                throw new Exception("⚠️ Harap scan Identity User dan Barcode Barang!");
            }

            // Lookup User
            $user = $db->findByColumn('users', 'identity_number', $idNumber);
            if (!$user) throw new Exception("❌ User dengan ID/NIP '$idNumber' tidak ditemukan.");

            // Lookup Asset
            $asset = $db->findByColumn('assets', 'serial_number', $assetCode);
            if (!$asset) throw new Exception("❌ Barang dengan Kode serial '$assetCode' tidak ditemukan.");

            $userId = $user['id'];
            $assetId = $asset['id'];

            // Find Active Loan for this User-Asset combo
            $activeLoan = null;
            foreach ($db->getAll('loans') as $loan) {
                if ($loan['user_id'] == $userId && $loan['asset_id'] == $assetId && in_array($loan['status'], ['active', 'overdue'])) {
                    $activeLoan = $loan;
                    break;
                }
            }

            if (!$activeLoan) {
                throw new Exception("⚠️ Tidak ada peminjaman aktif untuk user dan barang ini!");
            }

            // Update Loan with condition and notes
            $db->update('loans', $activeLoan['id'], [
                'status' => 'returned',
                'return_date' => date('Y-m-d H:i:s'),
                'return_condition' => $condition,
                'return_notes' => $notes
            ]);

            // Update Asset Status based on condition
            $newAssetStatus = 'available';
            if ($condition === 'major_damage') {
                $newAssetStatus = 'maintenance';
            } else if ($condition === 'minor_damage') {
                $newAssetStatus = 'available'; // Still usable but log the damage
            }

            $db->update('assets', $assetId, ['status' => $newAssetStatus]);
            
            // Log the return with condition
            $conditionLabel = [
                'good' => 'Baik',
                'minor_damage' => 'Lecet Minor',
                'major_damage' => 'Rusak Berat'
            ][$condition] ?? $condition;

            $activityLog->log('RETURN', 'loans', "{$user['name']} mengembalikan {$asset['brand']} {$asset['model']}", "Kondisi: $conditionLabel, Catatan: $notes");

            echo json_encode(['success' => true, 'message' => "✅ Barang berhasil dikembalikan dengan kondisi: $conditionLabel."]);
            exit;
        }

        // --- ASSET CRUD ACTIONS ---

        if ($action === 'add_asset') {
            // Add authentication check
            if (!AuthManager::isLoggedIn()) {
                throw new Exception("❌ Akses ditolak! Anda harus login sebagai admin.");
            }
            
            if(empty($input['brand']) || empty($input['model']) || empty($input['serial_number']) || empty($input['category'])) {
                throw new Exception("Semua field wajib diisi!");
            }
            
            // Check Duplicate SN
            if ($db->findByColumn('assets', 'serial_number', $input['serial_number'])) {
                throw new Exception("Serial Number sudah terdaftar!");
            }
            // Check Duplicate Barcode (if provided)
            if (!empty($input['barcode']) && $db->findByColumn('assets', 'barcode', $input['barcode'])) {
                throw new Exception("Barcode sudah terdaftar!");
            }

            $id = $db->insert('assets', [
                'brand' => $input['brand'],
                'model' => $input['model'],
                'serial_number' => $input['serial_number'],
                'category' => $input['category'],
                'barcode' => $input['barcode'] ?? $input['serial_number'],
                'status' => $input['status'] ?? 'available'
            ]);
            
            $activityLog->log('CREATE', 'assets', "{$input['brand']} {$input['model']}", "Kategori: {$input['category']}, Serial: {$input['serial_number']}");

            echo json_encode(['success' => true, 'message' => "✅ Barang berhasil ditambahkan."]);
            exit;
        }

        if ($action === 'edit_asset') {
            // Add authentication check
            if (!AuthManager::isLoggedIn()) {
                throw new Exception("❌ Akses ditolak! Anda harus login sebagai admin.");
            }
            
            $id = $input['id'];
            $db->update('assets', $id, [
                'brand' => $input['brand'],
                'model' => $input['model'],
                'serial_number' => $input['serial_number'],
                'category' => $input['category'],
                'barcode' => $input['barcode'],
                'status' => $input['status']
            ]);
            
            $activityLog->log('UPDATE', 'assets', "{$input['brand']} {$input['model']}", "Kategori: {$input['category']}, Serial: {$input['serial_number']}, Status: {$input['status']}");
            echo json_encode(['success' => true, 'message' => "✅ Data barang diperbarui."]);
            exit;
        }

        if ($action === 'delete_asset') {
            // Add authentication check
            if (!AuthManager::isLoggedIn()) {
                throw new Exception("❌ Akses ditolak! Anda harus login sebagai admin.");
            }
            
            $id = $input['id'];
            $asset = $db->find('assets', $id);
            // Check if asset has history
            foreach ($db->getAll('loans') as $l) {
                if ($l['asset_id'] == $id) throw new Exception("❌ Barang tidak bisa dihapus karena pernah dipinjam (Hapus riwayat terlebih dahulu).");
            }
            
            $db->delete('assets', $id);
            $activityLog->log('DELETE', 'assets', "{$asset['brand']} {$asset['model']}", "Serial: {$asset['serial_number']}");
            echo json_encode(['success' => true, 'message' => "✅ Barang berhasil dihapus."]);
            exit;
        }

        // --- USER CRUD ACTIONS ---

        if ($action === 'add_user') {
            if(empty($input['identity_number']) || empty($input['name']) || empty($input['kelas']) || empty($input['role'])) {
                throw new Exception("Semua field wajib diisi!");
            }
            
            // Check Duplicate Identity Number
            if ($db->findByColumn('users', 'identity_number', $input['identity_number'])) {
                throw new Exception("ID/NISN/NIP sudah terdaftar!");
            }

            $id = $db->insert('users', [
                'identity_number' => $input['identity_number'],
                'name' => $input['name'],
                'kelas' => $input['kelas'],
                'role' => $input['role'],
                'email' => $input['email'] ?? null,
                'phone' => $input['phone'] ?? null
            ]);
            
            $activityLog->log('CREATE', 'users', "{$input['name']} ({$input['identity_number']})", "Kelas: {$input['kelas']}, Role: {$input['role']}");

            echo json_encode(['success' => true, 'message' => "✅ Pengguna berhasil ditambahkan."]);
            exit;
        }

        if ($action === 'edit_user') {
            $id = $input['id'];
            if(empty($input['identity_number']) || empty($input['name']) || empty($input['kelas']) || empty($input['role'])) {
                throw new Exception("Semua field wajib diisi!");
            }

            // Check if identity_number changed and already exists
            $currentUser = $db->find('users', $id);
            if ($currentUser['identity_number'] !== $input['identity_number']) {
                if ($db->findByColumn('users', 'identity_number', $input['identity_number'])) {
                    throw new Exception("ID/NISN/NIP sudah terdaftar!");
                }
            }

            $db->update('users', $id, [
                'identity_number' => $input['identity_number'],
                'name' => $input['name'],
                'kelas' => $input['kelas'],
                'role' => $input['role'],
                'email' => $input['email'] ?? null,
                'phone' => $input['phone'] ?? null
            ]);
            
            $activityLog->log('UPDATE', 'users', "{$input['name']} ({$input['identity_number']})", "Kelas: {$input['kelas']}, Role: {$input['role']}");
            echo json_encode(['success' => true, 'message' => "✅ Data pengguna diperbarui."]);
            exit;
        }

        if ($action === 'delete_user') {
            $id = $input['id'];
            $user = $db->find('users', $id);
            // Check if user has loan history
            foreach ($db->getAll('loans') as $l) {
                if ($l['user_id'] == $id) throw new Exception("❌ Pengguna tidak bisa dihapus karena memiliki riwayat peminjaman (Hapus riwayat terlebih dahulu).");
            }
            
            $db->delete('users', $id);
            $activityLog->log('DELETE', 'users', "{$user['name']} ({$user['identity_number']})", "Role: {$user['role']}");
            echo json_encode(['success' => true, 'message' => "✅ Pengguna berhasil dihapus."]);
            exit;
        }

        // --- BULK OPERATIONS ---

        if ($action === 'bulk_delete_users') {
            $ids = $input['ids'] ?? [];
            if(empty($ids)) {
                throw new Exception("Pilih minimal satu pengguna untuk dihapus!");
            }

            $deleted = 0;
            $errors = [];
            
            foreach($ids as $id) {
                // Check if user has loan history
                $hasLoan = false;
                foreach ($db->getAll('loans') as $l) {
                    if ($l['user_id'] == $id) {
                        $hasLoan = true;
                        break;
                    }
                }
                
                if($hasLoan) {
                    $user = $db->find('users', $id);
                    $errors[] = "Tidak bisa hapus {$user['name']} (punya riwayat peminjaman)";
                } else {
                    $db->delete('users', $id);
                    $deleted++;
                }
            }
            
            $message = "✅ {$deleted} pengguna berhasil dihapus.";
            if(!empty($errors)) {
                $message .= " ⚠️ " . implode("; ", $errors);
            }
            
            echo json_encode(['success' => true, 'message' => $message, 'deleted' => $deleted]);
            exit;
        }

        if ($action === 'bulk_add_users') {
            $users = $input['users'] ?? [];
            if(empty($users)) {
                throw new Exception("Tidak ada data pengguna untuk ditambahkan!");
            }

            $added = 0;
            $errors = [];
            
            foreach($users as $index => $u) {
                try {
                    if(empty($u['identity_number']) || empty($u['name']) || empty($u['kelas']) || empty($u['role'])) {
                        throw new Exception("Baris " . ($index + 1) . ": Semua field wajib diisi!");
                    }
                    
                    if ($db->findByColumn('users', 'identity_number', $u['identity_number'])) {
                        throw new Exception("Baris " . ($index + 1) . ": ID/NISN/NIP '{$u['identity_number']}' sudah terdaftar!");
                    }
                    
                    $db->insert('users', [
                        'identity_number' => $u['identity_number'],
                        'name' => $u['name'],
                        'kelas' => $u['kelas'],
                        'role' => $u['role'],
                        'email' => $u['email'] ?? null,
                        'phone' => $u['phone'] ?? null
                    ]);
                    $added++;
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
            
            $message = "✅ {$added} pengguna berhasil ditambahkan.";
            if(!empty($errors)) {
                $message .= " ⚠️ Gagal: " . implode("; ", array_slice($errors, 0, 3));
                if(count($errors) > 3) $message .= " (dan " . (count($errors)-3) . " error lainnya)";
            }
            
            echo json_encode(['success' => true, 'message' => $message, 'added' => $added, 'total' => count($users)]);
            exit;
        }

        // --- EXCEL/CSV OPERATIONS ---

        if ($action === 'export_users_csv') {
            $users = $db->getAll('users');
            
            // Prepare CSV content
            $output = "ID (NISN/NIP),Nama Lengkap,Kelas,Status,Email,No. Telepon\n";
            
            foreach($users as $u) {
                $identity = '"' . str_replace('"', '""', $u['identity_number']) . '"';
                $name = '"' . str_replace('"', '""', $u['name']) . '"';
                $kelas = '"' . str_replace('"', '""', $u['kelas'] ?? '') . '"';
                $role = ($u['role'] ?? 'student') === 'teacher' ? 'Guru' : 'Pelajar';
                $email = '"' . str_replace('"', '""', $u['email'] ?? '') . '"';
                $phone = '"' . str_replace('"', '""', $u['phone'] ?? '') . '"';
                
                $output .= "{$identity},{$name},{$kelas},{$role},{$email},{$phone}\n";
            }
            
            // Send CSV file download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="data_pengguna_' . date('Y-m-d_His') . '.csv"');
            echo $output;
            exit;
        }

        if ($action === 'import_users_csv') {
            if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("File tidak terupload dengan benar!");
            }

            $file = $_FILES['csvFile']['tmp_name'];
            
            // Validate file extension
            $filename = $_FILES['csvFile']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if(!in_array($ext, ['csv', 'xlsx', 'xls'])) {
                throw new Exception("Format file harus CSV atau Excel (.csv, .xlsx, .xls)!");
            }

            // Parse CSV
            $imported = 0;
            $errors = [];
            $handle = fopen($file, 'r');
            $header = fgetcsv($handle); // Skip header row
            
            $line = 1;
            while(($data = fgetcsv($handle)) !== FALSE) {
                $line++;
                if(count($data) < 4) continue; // Skip empty lines
                
                try {
                    $u = [
                        'identity_number' => trim($data[0]),
                        'name' => trim($data[1]),
                        'kelas' => trim($data[2]),
                        'role' => strtolower(trim($data[3])) === 'guru' ? 'teacher' : 'student',
                        'email' => isset($data[4]) && !empty(trim($data[4])) ? trim($data[4]) : null,
                        'phone' => isset($data[5]) && !empty(trim($data[5])) ? trim($data[5]) : null
                    ];

                    if(empty($u['identity_number']) || empty($u['name']) || empty($u['kelas'])) {
                        throw new Exception("Baris {$line}: Field ID, Nama, dan Kelas wajib diisi!");
                    }

                    if($db->findByColumn('users', 'identity_number', $u['identity_number'])) {
                        throw new Exception("Baris {$line}: ID/NISN/NIP '{$u['identity_number']}' sudah terdaftar!");
                    }

                    $db->insert('users', $u);
                    $imported++;
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
            
            fclose($handle);
            
            $message = "✅ {$imported} pengguna berhasil diimport dari file.";
            if(!empty($errors)) {
                $message .= " ⚠️ Gagal: " . implode("; ", array_slice($errors, 0, 3));
                if(count($errors) > 3) $message .= " (dan " . (count($errors)-3) . " error lainnya)";
            }
            
            echo json_encode(['success' => true, 'message' => $message, 'imported' => $imported, 'total' => $line - 2]);
            exit;
        }

        // --- ASSET EXCEL/CSV OPERATIONS ---

        if ($action === 'export_assets_csv') {
            $assets = $db->getAll('assets');
            
            // Prepare CSV content
            $output = "Kategori,Merk,Model,Serial Number,Kode Barcode,Status\n";
            
            foreach($assets as $a) {
                $kategori = '"' . str_replace('"', '""', $a['category'] ?? '') . '"';
                $merk = '"' . str_replace('"', '""', $a['brand'] ?? '') . '"';
                $model = '"' . str_replace('"', '""', $a['model'] ?? '') . '"';
                $sn = '"' . str_replace('"', '""', $a['serial_number'] ?? '') . '"';
                $barcode = '"' . str_replace('"', '""', $a['barcode'] ?? '') . '"';
                $status = $a['status'] ?? 'available';
                
                $output .= "{$kategori},{$merk},{$model},{$sn},{$barcode},{$status}\n";
            }
            
            // Send CSV file download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="data_barang_' . date('Y-m-d_His') . '.csv"');
            echo $output;
            exit;
        }

        if ($action === 'import_assets_csv') {
            if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("File tidak terupload dengan benar!");
            }

            $file = $_FILES['csvFile']['tmp_name'];
            
            // Validate file extension
            $filename = $_FILES['csvFile']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if(!in_array($ext, ['csv', 'xlsx', 'xls'])) {
                throw new Exception("Format file harus CSV atau Excel (.csv, .xlsx, .xls)!");
            }

            // Parse CSV
            $imported = 0;
            $errors = [];
            $handle = fopen($file, 'r');
            $header = fgetcsv($handle); // Skip header row
            
            $line = 1;
            while(($data = fgetcsv($handle)) !== FALSE) {
                $line++;
                if(count($data) < 4) continue; // Skip empty lines
                
                try {
                    $a = [
                        'category' => trim($data[0]),
                        'brand' => trim($data[1]),
                        'model' => trim($data[2]),
                        'serial_number' => trim($data[3]),
                        'barcode' => isset($data[4]) && !empty(trim($data[4])) ? trim($data[4]) : trim($data[3]),
                        'status' => isset($data[5]) && !empty(trim($data[5])) ? strtolower(trim($data[5])) : 'available'
                    ];

                    if(empty($a['category']) || empty($a['brand']) || empty($a['model']) || empty($a['serial_number'])) {
                        throw new Exception("Baris {$line}: Field Kategori, Merk, Model, dan Serial Number wajib diisi!");
                    }

                    if($db->findByColumn('assets', 'serial_number', $a['serial_number'])) {
                        throw new Exception("Baris {$line}: Serial Number '{$a['serial_number']}' sudah terdaftar!");
                    }

                    if($db->findByColumn('assets', 'barcode', $a['barcode'])) {
                        throw new Exception("Baris {$line}: Kode Barcode '{$a['barcode']}' sudah terdaftar!");
                    }

                    $db->insert('assets', $a);
                    $imported++;
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
            
            fclose($handle);
            
            $message = "✅ {$imported} barang berhasil diimport dari file.";
            if(!empty($errors)) {
                $message .= " ⚠️ Gagal: " . implode("; ", array_slice($errors, 0, 3));
                if(count($errors) > 3) $message .= " (dan " . (count($errors)-3) . " error lainnya)";
            }
            
            echo json_encode(['success' => true, 'message' => $message, 'imported' => $imported, 'total' => $line - 2]);
            exit;
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// 4. Check Authentication & Authorization
// Define admin-only views
$adminOnlyViews = ['return', 'assets', 'users', 'logs', 'print_barcode', 'settings'];
$view = $_GET['view'] ?? 'dashboard'; // Default view

// Redirect unauthorized access to admin views
if (in_array($view, $adminOnlyViews) && !AuthManager::isLoggedIn()) {
    // If not admin, redirect to dashboard
    $view = 'dashboard';
}

// 5. Fetch Data for View
$assets = $db->getAll('assets');
$users = $db->getAll('users');
$loans = $db->getLoansWithDetails();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Aset Sekolah</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS Framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1e40af;
            --secondary-color: #64748b;
            --bg-color: #f1f5f9;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --success-bg: #dcfce7;
            --success-text: #166534;
            --warning-bg: #fef9c3;
            --warning-text: #854d0e;
            --danger-bg: #fee2e2;
            --danger-text: #991b1b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
        }

        /* Utility */
        .fw-500 { font-weight: 500; }
        .fw-600 { font-weight: 600; }
        .text-xs { font-size: 0.75rem; }
        .text-sm { font-size: 0.875rem; }

        /* Public Mode Professional Styles */
        .bg-success-subtle { background-color: #d1e7dd !important; }
        .bg-warning-subtle { background-color: #fff3cd !important; }
        .bg-danger-subtle { background-color: #f8d7da !important; }
        .bg-info-subtle { background-color: #cfe2ff !important; }
        .bg-primary-subtle { background-color: #cfe2ff !important; }
        .text-success { color: #198754 !important; }
        .text-warning { color: #ffc107 !important; }
        .text-danger { color: #dc3545 !important; }
        .text-info { color: #0dcaf0 !important; }
        .text-primary { color: #0d6efd !important; }

        /* Card Enhancements */
        .card.shadow-sm {
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.075) !important;
        }
        .card-header.bg-white {
            background-color: #fff !important;
            font-weight: 600;
        }

        /* Form Control Focus States */
        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        /* Button Groups */
        .btn-check:checked + .btn-outline-success {
            background-color: #198754;
            border-color: #198754;
            color: #fff;
        }
        .btn-check:checked + .btn-outline-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }
        .btn-check:checked + .btn-outline-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }

        /* Table Hover Effects */
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.035);
        }

        /* Badge Styling */
        .badge {
            font-weight: 500;
            letter-spacing: 0.025em;
        }

        /* Sticky Table Header */
        thead.sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* Divider Styling */
        hr.border-2 {
            border-width: 2px !important;
            opacity: 0.1;
        }

        /* Table Scroll Container - Only for Stock Table */
        .table-scroll-container {
            max-height: 450px;
            overflow-y: auto;
            border-bottom: 2px solid var(--border-color);
        }
        
        /* Smooth scrollbar styling for table */
        .table-scroll-container::-webkit-scrollbar {
            width: 8px;
        }
        
        .table-scroll-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .table-scroll-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        .table-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }


        /* Navbar */
        .navbar {
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 0;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-dark);
            font-size: 1.25rem;
            letter-spacing: -0.025em;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.25rem;
            font-weight: 600;
            color: var(--text-main);
        }
        .card-body {
            padding: 1.25rem;
        }

        /* Forms */
        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }
        .form-select, .form-control {
            border-color: #cbd5e1;
            padding: 0.625rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 6px;
        }
        .form-select:focus, .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.625rem 1rem;
            font-weight: 500;
            font-size: 0.875rem;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        /* Status Indicators */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
        }
        .status-available { background: var(--success-bg); color: var(--success-text); }
        .status-returned { background: var(--success-bg); color: var(--success-text); }
        .status-borrowed { background: var(--danger-bg); color: var(--danger-text); }
        .status-active { background: #eff6ff; color: #1e40af; border: 1px solid #dbeafe; }
        .status-maintenance { background: var(--warning-bg); color: var(--warning-text); }
        .status-overdue { background: var(--danger-bg); color: var(--danger-text); }

        /* Asset Icon */
        .asset-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }
        .table th {
            font-weight: 600;
            color: var(--secondary-color);
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            border-bottom-width: 1px;
            padding: 0.75rem 1rem;
            background-color: #f8fafc;
        }
        .table td {
            padding: 0.875rem 1rem;
            vertical-align: middle;
            color: var(--text-main);
            font-size: 0.875rem;
            border-bottom: 1px solid var(--border-color);
        }
        .table tr:last-child td {
            border-bottom: none;
        }

        /* Layout Tweaks */
        .page-header {
            margin-bottom: 2rem;
            padding-top: 1.5rem;
        }
        .asset-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        .asset-item:last-child {
            border-bottom: none;
        }
        .asset-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary-color);
            margin-right: 0.75rem;
        }

        /* Dashboard Fixed Layout (Kiosk Mode) */
        .dashboard-wrapper {
            height: calc(100vh - 66px); /* Navbar approx 66px */
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding-bottom: 1rem;
        }
        .dashboard-row {
            flex: 1;
            min-height: 0; /* Important for flex overflow */
        }

        /* Assets Fixed Layout (Data Barang) */
        .assets-wrapper {
            height: calc(100vh - 66px); /* Navbar approx 66px */
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding-bottom: 1rem;
        }
        .assets-row {
            flex: 1;
            min-height: 0; /* Important for flex overflow */
        }
        .scrollable-col {
            height: 100%;
            overflow-y: auto;
            padding-right: 5px; /* Prevent scrollbar hiding content */
        }
        /* Hide scrollbar for cleaner UI */
        .scrollable-col::-webkit-scrollbar { width: 4px; }
        .scrollable-col::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        /* Scrollable Table Container */
        .table-scroll-container {
            max-height: 600px;
            overflow-y: auto;
            border-radius: 0 0 0.375rem 0.375rem;
            border: 1px solid var(--border-color);
            border-top: none;
        }
        
        /* Fixed Table Header */
        .table-scroll-container table {
            position: relative;
            width: 100%;
        }
        
        .table-scroll-container thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f8fafc;
            border-top: 1px solid var(--border-color);
        }

        /* Scrollbar Styling */
        .table-scroll-container::-webkit-scrollbar {
            width: 8px;
        }
        .table-scroll-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .table-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
            border: 2px solid #f1f5f9;
        }
        .table-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Firefox Scrollbar */
        .table-scroll-container {
            scrollbar-color: #cbd5e1 #f1f5f9;
            scrollbar-width: thin;
        }

        /* Responsive: Adjust height untuk mobile (Berlaku untuk semua tabel) */
        @media (max-width: 768px) {
            .table-scroll-container {
                max-height: 400px;
            }
        }

        /* A4 Print Page Styling */
        .a4-page {
            width: 210mm;
            height: 297mm;
            background: white;
            padding: 10mm;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            page-break-after: always;
        }

        .barcode-grid {
            display: grid;
            gap: 8px;
            height: 100%;
            align-content: start;
        }

        .barcode-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 6px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            text-align: center;
        }

        .barcode-item svg {
            max-width: 90%;
            height: auto;
            margin: 4px 0;
            display: block;
        }

        .barcode-code {
            max-width: 100% !important;
            height: auto !important;
            width: 100% !important;
            display: block !important;
        }

        .barcode-label {
            font-size: 12px;
            font-weight: 600;
            color: #333;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            width: 100%;
            line-height: 1.2;
            margin: 2px 0;
        }

        .barcode-date {
            font-size: 7px;
            color: #666;
            text-align: right;
            margin-top: 4px;
            font-weight: 500;
        }

        /* Running Text Animation */
        @keyframes scroll-left {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(-100%);
            }
        }

        .running-text-container {
            background: linear-gradient(135deg, var(--running-bg-color, #667eea) 0%, var(--running-bg-color-end, #764ba2) 100%);
            color: white;
            padding: 12px 0;
            overflow: hidden;
            width: 100%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .running-text {
            font-size: 16px;
            font-weight: 600;
            white-space: nowrap;
            animation: scroll-left var(--running-animation-speed, 20s) linear infinite;
            letter-spacing: 1px;
            color: var(--running-text-color, #ffffff);
            font-family: var(--running-font-family, Arial, sans-serif);
        }

        /* Pause animation on hover */
        .running-text-container:hover .running-text {
            animation-play-state: paused;
        }

        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            .a4-page {
                box-shadow: none;
                margin: 0;
                page-break-after: always;
            }
            #barcodePreviewContainer {
                background: white !important;
                padding: 0 !important;
                gap: 0 !important;
            }
        }

        /* NOTIFICATION SYSTEM */
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        @keyframes slideProgress {
            from {
                width: 100%;
            }
            to {
                width: 0%;
            }
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
            }
        }

        #notificationContainer {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        }

        .notification {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 16px 24px;
            border-radius: 8px;
            min-width: 320px;
            max-width: 420px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            animation: slideInRight 0.4s ease-out;
            pointer-events: auto;
            font-weight: 500;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
        }

        .notification.removing {
            animation: slideOutRight 0.3s ease-in forwards;
        }

        .notification-icon {
            font-size: 20px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
        }

        .notification-content {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .notification-title {
            font-size: 15px;
            font-weight: 700;
            line-height: 1.3;
        }

        .notification-message {
            font-size: 13px;
            font-weight: 400;
            opacity: 0.9;
            line-height: 1.4;
        }

        /* SUCCESS NOTIFICATION */
        .notification.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .notification.success .notification-icon {
            animation: pulse 2s infinite;
        }

        /* ERROR NOTIFICATION */
        .notification.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        /* WARNING NOTIFICATION */
        .notification.warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        /* INFO NOTIFICATION */
        .notification.info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        /* CLOSE BUTTON */
        .notification-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .notification-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        /* PROGRESS BAR */
        .notification-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 0 0 8px 0;
        }

        @media (max-width: 480px) {
            #notificationContainer {
                top: 10px;
                right: 10px;
                left: 10px;
            }

            .notification {
                min-width: auto;
                max-width: none;
            }
        }
    </style>
</head>
<body>

<!-- NOTIFICATION CONTAINER -->
<div id="notificationContainer"></div>

<!-- LOAD ALL FUNCTION DEFINITIONS FIRST -->
<script>
    // --- NOTIFICATION SYSTEM ---
    function showNotification(title, message, type = 'info', duration = 5000) {
        const container = document.getElementById('notificationContainer');
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        const iconMap = {
            'success': '<i class="fas fa-check-circle"></i>',
            'error': '<i class="fas fa-exclamation-circle"></i>',
            'warning': '<i class="fas fa-exclamation-triangle"></i>',
            'info': '<i class="fas fa-info-circle"></i>'
        };
        
        notification.innerHTML = `
            <div class="notification-icon">${iconMap[type] || iconMap['info']}</div>
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                ${message ? `<div class="notification-message">${message}</div>` : ''}
            </div>
            <button class="notification-close" onclick="this.parentElement.remove();">&times;</button>
            <div class="notification-progress" style="animation: slideProgress ${duration}ms linear forwards;"></div>
        `;
        
        container.appendChild(notification);
        
        if (duration > 0) {
            setTimeout(() => {
                notification.classList.add('removing');
                setTimeout(() => notification.remove(), 300);
            }, duration);
        }
    }

    // Add animation for progress bar
    const style = document.createElement('style');
    style.textContent = `@keyframes slideProgress { from { width: 100%; } to { width: 0%; } }`;
    document.head.appendChild(style);

    // --- ASSET CRUD LOGIC ---
    function openAssetModal(asset = null) {
        const assetModalElement = document.getElementById('assetModal');
        const assetModal = new bootstrap.Modal(assetModalElement);

        if (asset) {
            document.getElementById('assetModalTitle').innerText = 'Edit Barang';
            document.getElementById('assetId').value = asset.id;
            document.getElementById('assetBarcode').value = asset.barcode || asset.serial_number;
            document.getElementById('assetBrand').value = asset.brand;
            document.getElementById('assetModel').value = asset.model;
            document.getElementById('assetCategory').value = asset.category || '';
            document.getElementById('assetSn').value = asset.serial_number;
            document.getElementById('assetBarcodeCode').value = asset.barcode || '';
            document.getElementById('assetStatus').value = asset.status;
            updateBarcodePreview();
        } else {
            document.getElementById('assetModalTitle').innerText = 'Tambah Barang';
            document.getElementById('assetForm').reset();
            document.getElementById('assetId').value = '';
            document.getElementById('assetBarcodeCode').value = '';
            hideBarcodePreview();
        }
        
        assetModalElement.addEventListener('shown.bs.modal', function () {
            document.getElementById('assetBarcode').focus();
        });

        assetModal.show();
    }

    async function saveAsset() {
        const id = document.getElementById('assetId').value;
        const barcode = document.getElementById('assetBarcodeCode').value || document.getElementById('assetSn').value;
        const brand = document.getElementById('assetBrand').value;
        const model = document.getElementById('assetModel').value;
        const category = document.getElementById('assetCategory').value;
        const sn = document.getElementById('assetSn').value;
        const status = document.getElementById('assetStatus').value;

        if(!brand || !model || !category || !sn) {
            alert("⚠️ Harap isi semua field wajib!\n- Brand\n- Model\n- Serial Number\n- Kategori");
            return;
        }

        const action = id ? 'edit_asset' : 'add_asset';
        const payload = { id, barcode, brand, model, category, serial_number: sn, status };

        try {
            const res = await fetch('?action=' + action, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            
            const text = await res.text();
            console.log('Response:', text);
            
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON response:', text);
                alert('❌ Kesalahan server: Response tidak valid');
                return;
            }
            
            if (data.success) {
                alert(data.message);
                const modal = bootstrap.Modal.getInstance(document.getElementById('assetModal'));
                if (modal) modal.hide();
                setTimeout(() => {
                    location.reload();
                }, 500);
            } else {
                alert('❌ Gagal: ' + (data.message || 'Kesalahan tidak diketahui'));
            }
        } catch (e) {
            console.error('Error:', e);
            alert('❌ Terjadi kesalahan sistem: ' + e.message);
        }
    }

    async function deleteAsset(id) {
        if(!confirm("⚠️ Yakin ingin menghapus barang ini?\n\nPerhatian: Barang yang pernah dipinjam tidak bisa dihapus.")) return;

        try {
            const res = await fetch('?action=delete_asset', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id})
            });
            
            const text = await res.text();
            console.log('Delete Response:', text);
            
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON response:', text);
                alert('❌ Kesalahan server: Response tidak valid');
                return;
            }
            
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('❌ ' + (data.message || 'Gagal menghapus barang'));
            }
        } catch (e) {
            console.error('Error:', e);
            alert('❌ Terjadi kesalahan sistem: ' + e.message);
        }
    }

    function updateBarcodePreview() {
        const barcodeCode = document.getElementById('assetBarcodeCode').value || document.getElementById('assetSn').value;
        const preview = document.getElementById('assetBarcodePreview');
        const placeholder = document.getElementById('assetBarcodePreviewPlaceholder');

        if (barcodeCode.trim() === '') {
            preview.style.display = 'none';
            placeholder.style.display = 'block';
            return;
        }

        try {
            JsBarcode("#assetBarcodePreview", barcodeCode, {
                format: "CODE128",
                lineColor: "#000",
                width: 2,
                height: 40,
                displayValue: true
            });
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        } catch (e) {
            placeholder.style.display = 'block';
            placeholder.innerText = 'Kode tidak valid';
            console.warn(e);
        }
    }

    function hideBarcodePreview() {
        const preview = document.getElementById('assetBarcodePreview');
        const placeholder = document.getElementById('assetBarcodePreviewPlaceholder');
        preview.style.display = 'none';
        placeholder.style.display = 'block';
    }

    function syncBarcodeFromSN() {
        const sn = document.getElementById('assetSn').value;
        if(sn) {
            document.getElementById('assetBarcodeCode').value = sn;
            updateBarcodePreview();
        } else {
            alert('Serial Number tidak boleh kosong!');
        }
    }

    function showBarcodeHelp() {
        const modal = new bootstrap.Modal(document.getElementById('barcodeHelpModal'));
        modal.show();
    }
</script>

<?php if (AuthManager::isLoggedIn()): ?>
<!-- ADMIN MODE NAVBAR - Clean & Professional -->
<nav class="navbar navbar-expand-lg sticky-top" style="background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%); box-shadow: 0 2px 15px rgba(0,0,0,0.1);">
    <div class="container-fluid px-4">
        <a class="navbar-brand text-white d-flex align-items-center" href="?view=dashboard">
            <div class="bg-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fas fa-boxes text-primary"></i>
            </div>
            <div>
                <span class="fw-bold fs-5">SIM-IV</span>
                <span class="d-none d-md-inline text-white-50 ms-2 fs-6">| Inventory System</span>
            </div>
        </a>
        
        <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="collapse navbar-collapse" id="adminNav">
            <!-- Center Navigation -->
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill text-white <?= $view=='dashboard'?'active bg-white bg-opacity-25':'' ?>" href="?view=dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill text-white <?= $view=='assets'?'active bg-white bg-opacity-25':'' ?>" href="?view=assets">
                        <i class="fas fa-box me-2"></i>Data Barang
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill text-white <?= $view=='users'?'active bg-white bg-opacity-25':'' ?>" href="?view=users">
                        <i class="fas fa-users me-2"></i>Data Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill text-white <?= $view=='print_barcode'?'active bg-white bg-opacity-25':'' ?>" href="?view=print_barcode">
                        <i class="fas fa-print me-2"></i>Cetak Barcode
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill text-white <?= $view=='settings'?'active bg-white bg-opacity-25':'' ?>" href="?view=settings">
                        <i class="fas fa-cog me-2"></i>Pengaturan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill text-white <?= $view=='logs'?'active bg-white bg-opacity-25':'' ?>" href="?view=logs">
                        <i class="fas fa-history me-2"></i>Log Aktivitas
                    </a>
                </li>
            </ul>
            
            <!-- Right Side -->
            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-lg-flex align-items-center text-white-50">
                    <i class="fas fa-user-shield me-2"></i>
                    <span class="small">Administrator</span>
                </div>
                <a href="?action=logout" class="btn btn-outline-light btn-sm px-3 rounded-pill">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<?php else: ?>
<!-- PUBLIC MODE NAVBAR - Simple & Clean -->
<nav class="navbar navbar-expand-lg sticky-top bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="?view=dashboard">
            <div class="bg-primary rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i class="fas fa-boxes text-white small"></i>
            </div>
            <span class="fw-bold text-dark">SIM-IV</span>
            <span class="text-muted ms-2 d-none d-md-inline">| School Inventory System</span>
        </a>
        
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-secondary border px-3 py-2">
                <i class="fas fa-globe me-1"></i> Public Mode
            </span>
            <a href="?view=login" class="btn btn-primary btn-sm px-3 rounded-pill">
                <i class="fas fa-sign-in-alt me-1"></i> Login Admin
            </a>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="<?= $view=='dashboard' ? 'container-fluid dashboard-wrapper px-lg-5' : 'container pb-5' ?>" style="<?= $view=='dashboard' ? 'height: calc(100vh - 70px); display: flex; flex-direction: column; overflow: hidden;' : '' ?>">
    <?php if ($view == 'login'): ?>
    
    <div style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
        <div class="card border-0 shadow-lg" style="max-width: 380px; width: 100%;">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-lock fa-3x text-primary mb-3" style="display: block;"></i>
                    <h4 class="fw-bold mb-2">Admin Login</h4>
                    <p class="text-muted text-sm">Masukkan password untuk masuk ke Admin Mode</p>
                </div>

                <form method="POST" action="">
                    <input type="hidden" name="action" value="traditional_login">
                    <div class="mb-3">
                        <label class="form-label fw-500">Password Admin</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-key text-muted"></i></span>
                            <input type="password" class="form-control border-start-0 ps-0" name="password" placeholder="Masukkan password admin" required autofocus>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle me-1"></i> Password default: <code>admin123</code>
                        </small>
                        <?php if (isset($_SESSION['login_error'])): ?>
                        <div class="alert alert-danger mt-2 mb-0 py-2 px-3">
                            <?= $_SESSION['login_error'] ?>
                            <?php unset($_SESSION['login_error']); ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-500 py-2">
                        <i class="fas fa-sign-in-alt me-2"></i> Login Admin
                    </button>
                </form>

                <div class="mt-4 pt-3 border-top">
                    <a href="?view=dashboard" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Public Mode
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php elseif ($view == 'dashboard'): ?>
    
    <?php if (!AuthManager::isLoggedIn()): ?>
    <!-- PUBLIC MODE HEADER -->
    <div class="page-header pt-3 pb-2 mb-4 border-bottom">
        <h4 class="fw-bold mb-1 text-dark"><i class="fas fa-th-large me-2 text-primary"></i>Dashboard Inventaris</h4>
        <p class="text-muted text-sm mb-0">Sistem Peminjaman & Pengembalian Aset Sekolah</p>
    </div>
    <?php else: ?>
    <!-- ADMIN MODE HEADER -->
    <div class="page-header d-flex justify-content-between align-items-center mb-2 pt-2 flex-shrink-0">
        <div>
            <h5 class="fw-bold mb-0">Dashboard Peminjaman</h5>
            <p class="text-muted text-xs mb-0">Kelola peminjaman aset sekolah secara real-time.</p>
        </div>
        <div>
            <button onclick="location.reload()" class="btn btn-outline-secondary btn-sm"><i class="fas fa-sync-alt me-1"></i> Refresh</button>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!AuthManager::isLoggedIn()): ?>
    <!-- PUBLIC MODE: TAB NAVIGATION LAYOUT -->
    <div class="container-fluid px-0" style="height: calc(100vh - 70px); display: flex; flex-direction: column; overflow: hidden; padding-bottom: 70px;">
        
        <!-- TAB NAVIGATION BUTTONS -->
        <div class="mb-4 flex-shrink-0">
            <div class="d-flex gap-3 justify-content-center">
                <button type="button" class="btn btn-lg px-5 py-3 btn-primary shadow" id="tabBtnPeminjaman" onclick="showPublicTab('peminjaman')" style="min-width: 250px;">
                    <i class="fas fa-hand-holding me-2 fs-5"></i>
                    <span class="fw-bold fs-5">Peminjaman Barang</span>
                </button>
                <button type="button" class="btn btn-lg px-5 py-3 btn-outline-success" id="tabBtnPengembalian" onclick="showPublicTab('pengembalian')" style="min-width: 250px;">
                    <i class="fas fa-undo-alt me-2 fs-5"></i>
                    <span class="fw-bold fs-5">Pengembalian Barang</span>
                </button>
            </div>
        </div>

        <!-- TAB CONTENT: PEMINJAMAN BARANG -->
        <div id="tabPeminjaman" class="tab-content-public flex-grow-1" style="min-height: 0; overflow: hidden;">
            <div class="row g-4 h-100" style="min-height: 0; overflow: hidden;">
                <!-- Scan Form -->
                <div class="col-lg-4 d-flex flex-column" style="min-height: 0; max-height: 100%;">
                    <div class="card h-100 border-0 shadow-sm d-flex flex-column overflow-hidden">
                        <div class="card-header bg-primary text-white py-3 flex-shrink-0">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-qrcode me-2"></i>Scan Station Peminjaman
                            </h6>
                        </div>
                        <div class="card-body flex-grow-1 d-flex flex-column justify-content-start">
                            <form id="borrowForm">
                                <div class="mb-3">
                                    <label class="form-label fw-500 small text-muted">Identitas Peminjam</label>
                                    <input type="text" class="form-control form-control-lg" id="identityNumber" 
                                           placeholder="Input NISN / NIP..." autofocus required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-500 small text-muted">Barcode Barang</label>
                                    <input type="text" class="form-control form-control-lg" id="assetCode" 
                                           placeholder="Scan barcode..." required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-500">
                                    <i class="fas fa-check-circle me-2"></i>Konfirmasi Peminjaman
                                </button>
                            </form>
                            <div id="alertBox" class="mt-3" style="display:none;"></div>
                        </div>
                    </div>
                </div>

                <!-- Stock Table -->
                <div class="col-lg-8 d-flex flex-column" style="min-height: 0; max-height: 100%;">
                    <div class="card h-100 border-0 shadow-sm d-flex flex-column overflow-hidden">
                        <div class="card-header d-flex justify-content-between align-items-center bg-white flex-shrink-0">
                            <span><i class="fas fa-check-circle me-2 text-success"></i>Stok Barang Tersedia</span>
                            <span class="badge bg-success bg-opacity-10 text-success fw-normal">
                                <?= count(array_filter($assets, fn($a) => $a['status'] === 'available')) ?> Items
                            </span>
                        </div>
                        <div class="table-responsive flex-grow-1" style="overflow-y: auto; position: relative;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="sticky-top" style="top: 0; background-color: white; z-index: 10; box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);">
                                    <tr>
                                        <th style="width: 12%" class="ps-4">No</th>
                                        <th style="width: 58%">Barang</th>
                                        <th style="width: 30%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $availableAssets = array_filter($assets, fn($a) => $a['status'] === 'available');
                                    if(empty($availableAssets)): 
                                    ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted">
                                                <i class="fas fa-box-open fa-2x mb-3 text-light-emphasis"></i>
                                                <p class="mb-0">Semua barang sedang dipinjam.</p>
                                            </td>
                                        </tr>
                                    <?php else: $no=1; foreach($availableAssets as $a): ?>
                                        <tr>
                                            <td class="ps-4 text-muted"><?= $no++ ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="asset-icon me-3">
                                                        <i class="fas <?= strpos(strtolower($a['model']), 'mouse') !== false ? 'fa-mouse' : (strpos(strtolower($a['model']), 'projector') !== false ? 'fa-video' : 'fa-laptop') ?>"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-600 text-dark"><?= htmlspecialchars($a['brand']) ?></div>
                                                        <div class="text-xs text-muted"><?= htmlspecialchars($a['model']) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-available">Ready</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: PENGEMBALIAN BARANG -->
        <div id="tabPengembalian" class="tab-content-public flex-grow-1" style="display: none; min-height: 0; overflow: hidden;">
            <div class="row g-4 h-100" style="min-height: 0; overflow: hidden;">
                <!-- Return Form -->
                <div class="col-lg-4 d-flex flex-column" style="min-height: 0; max-height: 100%;">
                    <div class="card h-100 border-0 shadow-sm d-flex flex-column overflow-hidden">
                        <div class="card-header bg-success text-white py-3 flex-shrink-0">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-qrcode me-2"></i>Scan Station Pengembalian
                            </h6>
                        </div>
                        <div class="card-body flex-grow-1 d-flex flex-column justify-content-start overflow-y-auto">
                            <form id="returnForm">
                                <div class="mb-3">
                                    <label class="form-label fw-500 small text-muted">Identitas Peminjam</label>
                                    <input type="text" class="form-control form-control-lg" id="returnIdentityNumber" 
                                           placeholder="Input NISN / NIP..." required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-500 small text-muted">Barcode Barang</label>
                                    <input type="text" class="form-control form-control-lg" id="returnAssetCode" 
                                           placeholder="Scan barcode..." required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-500 small text-muted">Kondisi Barang</label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="returnCondition" id="condGood" value="good" checked>
                                        <label class="btn btn-outline-success" for="condGood">
                                            <i class="fas fa-check-circle me-1"></i>Baik
                                        </label>
                                        <input type="radio" class="btn-check" name="returnCondition" id="condMinor" value="minor_damage">
                                        <label class="btn btn-outline-warning" for="condMinor">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Lecet
                                        </label>
                                        <input type="radio" class="btn-check" name="returnCondition" id="condMajor" value="major_damage">
                                        <label class="btn btn-outline-danger" for="condMajor">
                                            <i class="fas fa-times-circle me-1"></i>Rusak
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-500 small text-muted">Catatan (Opsional)</label>
                                    <textarea class="form-control" id="returnNotes" 
                                              placeholder="Tulis catatan jika ada..." rows="2"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success w-100 py-2 fw-500">
                                    <i class="fas fa-check-circle me-2"></i>Konfirmasi Pengembalian
                                </button>
                            </form>
                            <div id="returnAlertBox" class="mt-3" style="display:none;"></div>
                        </div>
                    </div>
                </div>

                <!-- Active Loans Table -->
                <div class="col-lg-8 d-flex flex-column" style="min-height: 0; max-height: 100%;">
                    <div class="card h-100 border-0 shadow-sm d-flex flex-column overflow-hidden">
                        <div class="card-header d-flex justify-content-between align-items-center bg-white flex-shrink-0">
                            <span><i class="fas fa-clock me-2 text-warning"></i>Barang Sedang Dipinjam</span>
                            <span class="badge bg-warning bg-opacity-10 text-warning fw-normal">
                                <?= count(array_filter($loans, fn($l) => $l['status'] === 'active' || $l['status'] === 'overdue')) ?> Active
                            </span>
                        </div>
                        <div class="table-responsive flex-grow-1" style="overflow-y: auto; position: relative;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="sticky-top" style="top: 0; background-color: white; z-index: 10; box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);">
                                    <tr>
                                        <th style="width: 8%" class="ps-4">No</th>
                                        <th style="width: 22%">Nama Peminjam</th>
                                        <th style="width: 28%">Barang</th>
                                        <th style="width: 18%">Kelas</th>
                                        <th style="width: 24%">Waktu Peminjaman</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $activeLoans = array_filter($loans, fn($l) => $l['status'] === 'active' || $l['status'] === 'overdue');
                                    if(empty($activeLoans)): 
                                    ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="fas fa-check-double fa-2x mb-3 text-light-emphasis"></i>
                                                <p class="mb-0">Semua barang sudah dikembalikan</p>
                                            </td>
                                        </tr>
                                    <?php else: $no=1; foreach($activeLoans as $loan): 
                                        $isOverdue = strtotime($loan['due_date']) < strtotime(date('Y-m-d'));
                                    ?>
                                        <tr class="<?= $isOverdue ? 'table-danger' : '' ?>">
                                            <td class="ps-4 text-muted fw-600"><?= $no++ ?></td>
                                            <td>
                                                <div class="fw-600"><?= htmlspecialchars($loan['user_name'] ?? '-') ?></div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="asset-icon me-2" style="font-size: 16px; color: #6366f1;">
                                                        <i class="fas <?= strpos(strtolower($loan['asset_model'] ?? ''), 'mouse') !== false ? 'fa-mouse' : (strpos(strtolower($loan['asset_model'] ?? ''), 'projector') !== false ? 'fa-video' : 'fa-laptop') ?>"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-600 text-dark" style="font-size: 14px;"><?= htmlspecialchars($loan['asset_brand'] ?? '-') ?></div>
                                                        <div class="text-xs text-muted"><?= htmlspecialchars($loan['asset_model'] ?? '-') ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info bg-opacity-10 text-info fw-600"><?= htmlspecialchars($loan['user_kelas'] ?? '-') ?></span>
                                            </td>
                                            <td>
                                                <div class="text-sm"><?= date('d/m/Y', strtotime($loan['loan_date'])) ?></div>
                                                <small class="text-muted"><?= date('H:i:s', strtotime($loan['loan_date'])) ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Running Text Section (Bottom) -->
        <div class="running-text-container" style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 100;">
            <div class="running-text" id="runningTextElement">
                Selamat datang di Sistem Manajemen Aset Sekolah
            </div>
        </div>
    </div>
    
    <script>
    // Load Running Text on Page Load
    function loadRunningText() {
        const runningTextElement = document.getElementById('runningTextElement');
        if (!runningTextElement) return; // Element doesn't exist on this page
        
        fetch('?action=get_running_text')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    runningTextElement.textContent = data.running_text;
                }
            })
            .catch(e => console.warn('Running text load error:', e));
        
        // Load all running text settings and apply them
        loadRunningTextSettings();
    }

    // Load and apply all running text settings
    function loadRunningTextSettings() {
        const runningTextContainer = document.querySelector('.running-text-container');
        const runningText = document.querySelector('.running-text');
        
        if (!runningTextContainer || !runningText) return; // Elements don't exist on this page
        
        // Fetch settings from public endpoint
        fetch('?action=get_running_text_settings')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.settings) {
                    const settings = data.settings;
                    
                    // Set CSS variables
                    document.documentElement.style.setProperty('--running-animation-speed', settings.animation_speed + 's');
                    document.documentElement.style.setProperty('--running-bg-color', settings.bg_color);
                    document.documentElement.style.setProperty('--running-bg-color-end', settings.bg_color_end);
                    document.documentElement.style.setProperty('--running-text-color', settings.text_color);
                    document.documentElement.style.setProperty('--running-font-family', settings.font_family);
                }
            })
            .catch(e => {
                console.warn('Running text settings load error:', e);
                // Use defaults if request fails
                document.documentElement.style.setProperty('--running-animation-speed', '20s');
                document.documentElement.style.setProperty('--running-bg-color', '#667eea');
                document.documentElement.style.setProperty('--running-bg-color-end', '#764ba2');
                document.documentElement.style.setProperty('--running-text-color', '#ffffff');
                document.documentElement.style.setProperty('--running-font-family', 'Arial, sans-serif');
            });
    }

    // Call on page load
    document.addEventListener('DOMContentLoaded', loadRunningText);

    // Function to switch between tabs in Public Mode
    function showPublicTab(tab) {
        // Hide all tab contents
        document.getElementById('tabPeminjaman').style.display = 'none';
        document.getElementById('tabPengembalian').style.display = 'none';
        
        // Reset all tab buttons (keep min-width style)
        var btnPeminjaman = document.getElementById('tabBtnPeminjaman');
        var btnPengembalian = document.getElementById('tabBtnPengembalian');
        
        btnPeminjaman.className = 'btn btn-lg px-5 py-3 btn-outline-primary';
        btnPeminjaman.style.minWidth = '250px';
        btnPengembalian.className = 'btn btn-lg px-5 py-3 btn-outline-success';
        btnPengembalian.style.minWidth = '250px';
        
        // Show selected tab and activate button
        if (tab === 'peminjaman') {
            document.getElementById('tabPeminjaman').style.display = 'block';
            btnPeminjaman.className = 'btn btn-lg px-5 py-3 btn-primary shadow';
            document.getElementById('identityNumber').focus();
        } else {
            document.getElementById('tabPengembalian').style.display = 'block';
            btnPengembalian.className = 'btn btn-lg px-5 py-3 btn-success shadow';
            document.getElementById('returnIdentityNumber').focus();
        }
    }
    </script>
    
    <?php else: ?>
    <!-- ADMIN MODE: REDESIGNED LAYOUT -->
    <?php
    // Calculate Laptop statistics
    $totalLaptops = count(array_filter($assets, fn($a) => strtolower($a['category'] ?? '') === 'laptop'));
    $readyLaptops = count(array_filter($assets, fn($a) => strtolower($a['category'] ?? '') === 'laptop' && $a['status'] === 'available'));
    $borrowedLaptops = count(array_filter($assets, fn($a) => strtolower($a['category'] ?? '') === 'laptop' && $a['status'] === 'borrowed'));
    $brokenLaptops = count(array_filter($assets, fn($a) => strtolower($a['category'] ?? '') === 'laptop' && $a['status'] === 'broken'));
    $repairLaptops = count(array_filter($assets, fn($a) => strtolower($a['category'] ?? '') === 'laptop' && $a['status'] === 'maintenance'));
    ?>
    
    <!-- Horizontal Summary Cards Row -->
    <div class="row g-3 mb-3 flex-shrink-0">
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body d-flex align-items-center text-white py-3 px-4">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                        <i class="fas fa-laptop fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-xs opacity-75 mb-1">Total Laptop</div>
                        <div class="h2 fw-bold mb-0"><?= $totalLaptops ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="card-body d-flex align-items-center text-white py-3 px-4">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-xs opacity-75 mb-1">Tersedia</div>
                        <div class="h2 fw-bold mb-0"><?= $readyLaptops ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body d-flex align-items-center text-white py-3 px-4">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                        <i class="fas fa-share-alt fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-xs opacity-75 mb-1">Dipinjam</div>
                        <div class="h2 fw-bold mb-0"><?= $borrowedLaptops ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body d-flex align-items-center text-white py-3 px-4">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                        <i class="fas fa-wrench fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-xs opacity-75 mb-1">Perbaikan</div>
                        <div class="h2 fw-bold mb-0"><?= $repairLaptops ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);">
                <div class="card-body d-flex align-items-center text-white py-3 px-4">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                        <i class="fas fa-exclamation-circle fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-xs opacity-75 mb-1">Rusak</div>
                        <div class="h2 fw-bold mb-0"><?= $brokenLaptops ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Tables Side by Side -->
    <div class="row g-4" style="flex: 1; min-height: 0; overflow: hidden;">
        <!-- Left Table: Stok Barang Tersedia -->
        <div class="col-lg-6 d-flex flex-column" style="min-height: 0; max-height: 100%;">
            <div class="card h-100 border-0 shadow-sm d-flex flex-column overflow-hidden">
                <div class="card-header d-flex justify-content-between align-items-center bg-white flex-shrink-0">
                    <span><i class="fas fa-check-circle me-2 text-success"></i> Stok Barang Tersedia</span>
                    <span class="badge bg-success bg-opacity-10 text-success fw-normal">Live Stock</span>
                </div>
                <div class="table-responsive flex-grow-1" style="overflow-y: auto; position: relative;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="sticky-top" style="top: 0; background-color: white; z-index: 10; box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);">
                            <tr>
                                <th style="width: 12%" class="ps-4">No</th>
                                <th style="width: 58%">Barang</th>
                                <th style="width: 30%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $availableAssets = array_filter($assets, fn($a) => $a['status'] === 'available');
                            if(empty($availableAssets)): 
                            ?>
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-2x mb-3 text-light-emphasis"></i>
                                        <p class="mb-0">Semua barang sedang dipinjam.</p>
                                    </td>
                                </tr>
                            <?php else: $no=1; foreach($availableAssets as $a): ?>
                                <tr>
                                    <td class="ps-4 text-muted"><?= $no++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="asset-icon me-3">
                                                <i class="fas <?= strpos(strtolower($a['model']), 'mouse') !== false ? 'fa-mouse' : (strpos(strtolower($a['model']), 'projector') !== false ? 'fa-video' : 'fa-laptop') ?>"></i>
                                            </div>
                                            <div>
                                                <div class="fw-600 text-dark"><?= htmlspecialchars($a['brand']) ?></div>
                                                <div class="text-xs text-muted"><?= htmlspecialchars($a['model']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-available">Ready</span>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Table: Barang Sedang Dipinjam -->
        <div class="col-lg-6 d-flex flex-column" style="min-height: 0; max-height: 100%;">
            <div class="card h-100 border-0 shadow-sm d-flex flex-column overflow-hidden">
                <div class="card-header d-flex justify-content-between align-items-center bg-white flex-shrink-0">
                    <span><i class="fas fa-hand-holding me-2 text-warning"></i> Barang Sedang Dipinjam</span>
                    <span class="badge bg-warning bg-opacity-10 text-warning fw-normal">Active Loans</span>
                </div>
                <div class="table-responsive flex-grow-1" style="overflow-y: auto; position: relative;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="sticky-top" style="top: 0; background-color: white; z-index: 10; box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);">
                            <tr>
                                <th style="width: 10%" class="ps-4">No</th>
                                <th style="width: 30%">Barang</th>
                                <th style="width: 30%">Peminjam</th>
                                <th style="width: 30%">Jatuh Tempo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $activeLoans = array_filter($loans, fn($l) => $l['status'] === 'active');
                            if(empty($activeLoans)): 
                            ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-3 text-light-emphasis"></i>
                                        <p class="mb-0">Tidak ada barang yang sedang dipinjam.</p>
                                    </td>
                                </tr>
                            <?php else: $no=1; foreach($activeLoans as $loan): 
                                // Find asset and user details
                                $loanAsset = null;
                                $loanUser = null;
                                foreach($assets as $a) {
                                    if($a['id'] == $loan['asset_id']) { $loanAsset = $a; break; }
                                }
                                foreach($users as $u) {
                                    if($u['id'] == $loan['user_id']) { $loanUser = $u; break; }
                                }
                                
                                // Check if overdue
                                $dueDate = strtotime($loan['due_date']);
                                $today = strtotime(date('Y-m-d'));
                                $isOverdue = $today > $dueDate;
                            ?>
                                <tr class="<?= $isOverdue ? 'table-danger' : '' ?>">
                                    <td class="ps-4 text-muted"><?= $no++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="asset-icon me-3">
                                                <i class="fas <?= $loanAsset && strpos(strtolower($loanAsset['model']), 'mouse') !== false ? 'fa-mouse' : ($loanAsset && strpos(strtolower($loanAsset['model']), 'projector') !== false ? 'fa-video' : 'fa-laptop') ?>"></i>
                                            </div>
                                            <div>
                                                <div class="fw-600 text-dark"><?= $loanAsset ? htmlspecialchars($loanAsset['brand']) : '-' ?></div>
                                                <div class="text-xs text-muted"><?= $loanAsset ? $loanAsset['serial_number'] : '-' ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-600"><?= $loanUser ? htmlspecialchars($loanUser['name']) : '-' ?></div>
                                        <div class="text-xs text-muted"><?= $loanUser ? $loanUser['identity_number'] : '-' ?></div>
                                    </td>
                                    <td>
                                        <?php if($isOverdue): ?>
                                            <span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>Terlambat</span>
                                        <?php else: ?>
                                            <span class="text-muted"><?= date('d M Y', strtotime($loan['due_date'])) ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php elseif ($view == 'assets'): ?>
    
    <div style="height: calc(100vh - 70px); display: flex; flex-direction: column; overflow: hidden; padding: 1rem;">
    
    <div class="page-header d-flex justify-content-between align-items-center mb-3 flex-shrink-0">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-box me-2"></i>Data Barang</h4>
            <p class="text-muted text-sm mb-0">Manajemen inventaris aset fisik sekolah.</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="showAssetsResume()" class="btn btn-outline-primary btn-sm" title="Lihat ringkasan fitur dan cara pakai">
                <i class="fas fa-book me-1"></i> Resume
            </button>
            <button onclick="exportAssetsCsv()" class="btn btn-info btn-sm" title="Export data barang ke file Excel">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
            <button onclick="openAssetImportModal()" class="btn btn-warning btn-sm" title="Import data barang dari file Excel">
                <i class="fas fa-file-csv me-1"></i> Import Excel
            </button>
            <button onclick="downloadAssetTemplate()" class="btn btn-secondary btn-sm" title="Download template Excel">
                <i class="fas fa-download me-1"></i> Template
            </button>
            <button onclick="openAssetModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> Tambah Barang</button>
        </div>
    </div>

    <div class="row g-3 flex-grow-1" style="min-height: 0; overflow: hidden;">
        <!-- Tabel Data Barang -->
        <div class="col-lg-8 h-100 d-flex flex-column">
            <div class="card border-0 shadow-sm h-100 d-flex flex-column overflow-hidden">
                <div class="card-body p-0 flex-grow-1" style="min-height: 0;">
                    <div style="height: 100%; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="sticky-top" style="top: 0; background-color: #f8f9fa; z-index: 10;">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Kategori</th>
                                    <th>Merk & Tipe</th>
                                    <th>Serial Number</th>
                                    <th>Barcode</th>
                                    <th>Status Kondisi</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php foreach($assets as $idx => $a): ?>
                        <tr>
                            <td class="ps-4 text-muted"><?= $idx+1 ?></td>
                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($a['category'] ?? '-') ?></span></td>
                            <td>
                                <div class="fw-600 text-dark"><?= htmlspecialchars($a['brand']) ?></div>
                                <div class="text-xs text-muted"><?= htmlspecialchars($a['model']) ?></div>
                            </td>
                            <td><code class="text-primary fw-bold"><?= $a['serial_number'] ?></code></td>
                            <td>
                                <svg class="barcode-mini" 
                                     jsbarcode-value="<?= $a['serial_number'] ?>" 
                                     jsbarcode-format="CODE128" 
                                     jsbarcode-width="1.5" 
                                     jsbarcode-height="30" 
                                     jsbarcode-displayValue="false"
                                     jsbarcode-margin="0"
                                     style="max-width: 100px; height: 30px;"></svg>
                            </td>
                            <td>
                                <?php 
                                $statusBadges = [
                                    'available' => '<span class="badge bg-success">Ready</span>',
                                    'borrowed' => '<span class="badge bg-warning text-dark">Dipinjam</span>',
                                    'maintenance' => '<span class="badge bg-danger">Rusak</span>',
                                    'broken' => '<span class="badge bg-danger">Rusak</span>'
                                ];
                                echo $statusBadges[$a['status']] ?? '<span class="badge bg-secondary">Unknown</span>';
                                ?>
                            </td>
                            <td class="text-end pe-4" style="white-space: nowrap;">
                                <div class="d-inline-flex gap-1">
                                    <button onclick='openAssetModal(<?= htmlspecialchars(json_encode($a), ENT_QUOTES) ?>)' class="btn btn-sm btn-outline-secondary" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button onclick="deleteAsset(<?= $a['id'] ?>)" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barcode Generator -->
        <div class="col-lg-4 h-100 d-flex flex-column">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-print me-2 text-primary"></i> Buat Barcode Baru</span>
                    <button class="btn btn-sm btn-link text-primary p-0" onclick="showBarcodeHelp()" title="Petunjuk Pembuatan Barcode">
                        <i class="fas fa-question-circle fa-lg"></i>
                    </button>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <div class="mb-3">
                        <label class="form-label text-sm">Masukan Kode / SN Barang</label>
                        <input type="text" id="barcodeInput" class="form-control text-sm" placeholder="Ketik kode disini..." oninput="generateBarcode()">
                    </div>
                    <div class="text-center mb-3 p-3 bg-white rounded border" style="min-height: 80px; display: flex; align-items: center; justify-content: center;">
                        <svg id="barcode" style="max-width: 100%; height: 40px;"></svg>
                        <div id="barcodePlaceholder" class="text-muted text-xs fst-italic">Preview akan muncul disini</div>
                    </div>
                    <button id="btnDownloadBarcode" class="btn btn-outline-primary btn-sm w-100" onclick="downloadBarcode()" disabled>
                        <i class="fas fa-download me-2"></i> Download JPEG
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    </div>

    <!-- Assets Resume Modal -->
    <div class="modal fade" id="assetsResumeModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary bg-opacity-10">
                    <h5 class="modal-title">
                        <i class="fas fa-book text-primary me-2"></i>
                        Resume Menu Data Barang
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Fitur Utama -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-star me-2"></i>Fitur Utama
                        </h6>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item border-0 pb-3">
                                <div class="fw-bold text-dark">1. Tambah Barang Baru</div>
                                <p class="text-muted text-sm mb-0">Klik tombol <strong>"Tambah Barang"</strong> untuk menambahkan barang baru ke inventaris. Isi field: Kategori, Merk, Model, Serial Number, Kode Barcode, dan Status.</p>
                            </div>
                            <div class="list-group-item border-0 pb-3">
                                <div class="fw-bold text-dark">2. Edit Barang</div>
                                <p class="text-muted text-sm mb-0">Klik ikon <i class="fas fa-edit text-secondary"></i> di setiap baris untuk mengubah data barang. Fitur termasuk preview barcode real-time dan tombol sinkronisasi dari Serial Number.</p>
                            </div>
                            <div class="list-group-item border-0 pb-3">
                                <div class="fw-bold text-dark">3. Hapus Barang</div>
                                <p class="text-muted text-sm mb-0">Klik ikon <i class="fas fa-trash text-danger"></i> untuk menghapus barang. Sistem mencegah penghapusan jika barang pernah dipinjam.</p>
                            </div>
                            <div class="list-group-item border-0 pb-3">
                                <div class="fw-bold text-dark">4. Tabel Data Barang</div>
                                <p class="text-muted text-sm mb-0">Menampilkan semua barang dengan kolom: No, Kategori, Merk & Tipe, Serial Number, Barcode mini, Status, dan Aksi. Tabel scrollable untuk data banyak.</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Fitur Import/Export -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-success mb-3">
                            <i class="fas fa-exchange-alt me-2"></i>Import & Export Excel
                        </h6>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item border-0 pb-3">
                                <div class="fw-bold text-dark">📥 Export Excel</div>
                                <p class="text-muted text-sm mb-2">Download semua data barang ke file CSV yang bisa dibuka di Excel. Format sesuai dengan kolom tabel:</p>
                                <div class="alert alert-info alert-sm mb-0">
                                    <strong>Kolom Export (urutan):</strong><br>
                                    1. Kategori | 2. Merk | 3. Model | 4. Serial Number | 5. Kode Barcode | 6. Status
                                </div>
                            </div>
                            <div class="list-group-item border-0 pb-3">
                                <div class="fw-bold text-dark">📤 Import Excel</div>
                                <p class="text-muted text-sm mb-2">Upload file CSV/Excel untuk menambahkan banyak barang sekaligus. Harus sesuai format export.</p>
                                <div class="alert alert-warning alert-sm mb-0">
                                    <strong>⚠️ Ketentuan Impor:</strong><br>
                                    ✓ Kategori, Merk, Model, Serial Number = WAJIB<br>
                                    ✓ Kode Barcode, Status = OPSIONAL<br>
                                    ✓ Serial Number HARUS unik (tidak boleh duplikat)<br>
                                    ✓ Status default: "available" jika kosong
                                </div>
                            </div>
                            <div class="list-group-item border-0 pb-3">
                                <div class="fw-bold text-dark">📋 Template Excel</div>
                                <p class="text-muted text-sm mb-0">Klik tombol "Template" untuk download file template Excel contoh dengan data sample. Edit kolom sesuai data barang Anda, lalu upload untuk import massal.</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Fitur Barcode -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-warning mb-3">
                            <i class="fas fa-barcode me-2"></i>Fitur Barcode
                        </h6>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item border-0 pb-3">
                                <div class="fw-bold text-dark">🎯 Buat Barcode Baru</div>
                                <p class="text-muted text-sm mb-2">Di panel sebelah kanan, ketik kode barang untuk generate barcode CODE128.</p>
                                <ul class="text-sm text-muted mb-0 ps-3">
                                    <li>Preview otomatis saat mengetik</li>
                                    <li>Download sebagai file JPEG siap cetak</li>
                                    <li>Gunakan Serial Number yang sama untuk konsistensi</li>
                                </ul>
                            </div>
                            <div class="list-group-item border-0">
                                <div class="fw-bold text-dark">🔄 Edit Kode Barcode</div>
                                <p class="text-muted text-sm mb-2">Saat edit barang, bisa mengubah kode barcode dan melihat preview.</p>
                                <ul class="text-sm text-muted mb-0 ps-3">
                                    <li>Tombol <i class="fas fa-sync"></i> untuk sinkronkan dengan Serial Number</li>
                                    <li>Preview barcode muncul otomatis saat input berubah</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Tips & Trik -->
                    <div>
                        <h6 class="fw-bold text-info mb-3">
                            <i class="fas fa-lightbulb me-2"></i>Tips & Trik
                        </h6>
                        <ul class="text-sm text-muted">
                            <li class="mb-2">🎯 <strong>Kategori Wajib:</strong> Laptop, Komputer, Printer, Scanner, Proyektor, Monitor, Tablet, Smartphone, Aksesoris, Perangkat Jaringan, atau Lainnya</li>
                            <li class="mb-2">🔖 <strong>Serial Number Unik:</strong> Gunakan format yang mudah dikenali, misal: LNV-001, EPS-001, CAN-002</li>
                            <li class="mb-2">📊 <strong>Bulk Import:</strong> Untuk 50+ barang, gunakan fitur Import Excel daripada manual satu per satu</li>
                            <li class="mb-2">🖨️ <strong>Barcode Printing:</strong> Download dan cetak barcode untuk ditempel pada barang fisik</li>
                            <li class="mb-2">🔒 <strong>Proteksi Data:</strong> Barang dengan riwayat peminjaman tidak bisa dihapus</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset Import Modal -->
    <div class="modal fade" id="assetImportModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning bg-opacity-10">
                    <h5 class="modal-title">
                        <i class="fas fa-file-csv text-warning me-2"></i>
                        Import Data Barang dari Excel
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-list me-2 text-warning"></i>Struktur Tabel Excel</h6>
                        <p class="text-sm text-muted mb-3">File Excel harus memiliki kolom dengan urutan berikut (sesuai dengan tabel):</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered bg-light">
                                <thead class="table-warning">
                                    <tr>
                                        <th style="width: 12%">Urutan</th>
                                        <th style="width: 20%">Nama Kolom</th>
                                        <th style="width: 18%">Wajib/Opsional</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">1</td>
                                        <td><code>Kategori</code></td>
                                        <td><span class="badge bg-danger">Wajib</span></td>
                                        <td>Jenis aset: Laptop, Printer, Monitor, Proyektor, dll</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">2</td>
                                        <td><code>Merk</code></td>
                                        <td><span class="badge bg-danger">Wajib</span></td>
                                        <td>Brand barang: Lenovo, Canon, Dell, Epson, dll</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">3</td>
                                        <td><code>Model</code></td>
                                        <td><span class="badge bg-danger">Wajib</span></td>
                                        <td>Tipe/varian model barang</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">4</td>
                                        <td><code>Serial Number</code></td>
                                        <td><span class="badge bg-danger">Wajib</span></td>
                                        <td>Kode unik barang (tidak boleh ada duplikat)</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">5</td>
                                        <td><code>Kode Barcode</code></td>
                                        <td><span class="badge bg-info">Opsional</span></td>
                                        <td>Kode untuk barcode (jika kosong, gunakan Serial Number)</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">6</td>
                                        <td><code>Status</code></td>
                                        <td><span class="badge bg-info">Opsional</span></td>
                                        <td>available, borrowed, maintenance (default: available)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-file-excel me-2 text-success"></i>Contoh Data Excel</h6>
                        <div class="alert alert-light border">
                            <code class="text-xs d-block" style="line-height: 1.8;">
                                Kategori,Merk,Model,Serial Number,Kode Barcode,Status<br>
                                <span class="text-success">Laptop,Lenovo,ThinkPad X1,LNV-001,LNV-001,available</span><br>
                                <span class="text-success">Printer,Canon,G2010,CAN-001,CAN-001,available</span><br>
                                <span class="text-success">Proyektor,Epson,EB-X05,EPS-001,EPS-001,available</span><br>
                                <span class="text-success">Monitor,Samsung,24 inch,SAM-001,,available</span><br>
                                <span class="text-success">Tablet,Apple,iPad Pro,APP-001,APP-001,</span>
                            </code>
                        </div>
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Catatan: Kolom Kode Barcode dan Status boleh kosong akan diisi dengan nilai default</small>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fas fa-upload me-2 text-warning"></i>Pilih File untuk Diupload</label>
                        <input type="file" id="assetImportFile" class="form-control" accept=".csv,.xlsx,.xls" required>
                        <small class="text-muted d-block mt-2">
                            Format: CSV, XLSX, atau XLS (Max: 5MB)
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" onclick="submitAssetImport()" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Upload & Import
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset Modal -->
    <div class="modal fade" id="assetModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assetModalTitle">Tambah Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="assetForm" onsubmit="event.preventDefault(); saveAsset();">
                        <input type="hidden" id="assetId">
                        <div class="mb-3">
                            <label class="form-label">Barcode / Scan Kode</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-barcode"></i></span>
                                <input type="text" class="form-control" id="assetBarcode" placeholder="Scan barcode disini..." autofocus>
                            </div>
                            <div class="form-text text-xs">Arahkan cursor kemari untuk scan barcode barang.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori Produk</label>
                            <select class="form-select" id="assetCategory" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Laptop">Laptop</option>
                                <option value="Komputer">Komputer</option>
                                <option value="Printer">Printer</option>
                                <option value="Scanner">Scanner</option>
                                <option value="Proyektor">Proyektor</option>
                                <option value="Monitor">Monitor</option>
                                <option value="Tablet">Tablet</option>
                                <option value="Smartphone">Smartphone</option>
                                <option value="Aksesoris">Aksesoris</option>
                                <option value="Perangkat Jaringan">Perangkat Jaringan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Merk (Brand)</label>
                            <input type="text" class="form-control" id="assetBrand" required placeholder="Contoh: Lenovo, Epson">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Model / Tipe</label>
                            <input type="text" class="form-control" id="assetModel" required placeholder="Contoh: ThinkPad X1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Serial Number</label>
                            <input type="text" class="form-control" id="assetSn" required placeholder="Unik, misal: LNV-009">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode Barcode</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-barcode"></i></span>
                                <input type="text" class="form-control" id="assetBarcodeCode" placeholder="Kode untuk barcode" oninput="updateBarcodePreview()">
                                <button class="btn btn-outline-secondary" type="button" onclick="syncBarcodeFromSN()" title="Sinkronkan dengan Serial Number">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            <small class="text-muted">Jika kosong, akan menggunakan Serial Number.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preview Barcode</label>
                            <div class="text-center p-3 bg-light rounded border" style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                <svg id="assetBarcodePreview" style="max-width: 100%; height: 50px;"></svg>
                                <div id="assetBarcodePreviewPlaceholder" class="text-muted text-xs fst-italic">Preview akan muncul disini</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status Awal</label>
                            <select class="form-select" id="assetStatus">
                                <option value="available">Ready</option>
                                <option value="borrowed">Dipinjam</option>
                                <option value="broken">Rusak</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" onclick="saveAsset()" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Barcode Help Modal -->
    <div class="modal fade" id="barcodeHelpModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary bg-opacity-10">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Petunjuk Pembuatan Barcode
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <h6 class="fw-bold text-primary"><i class="fas fa-1 me-2"></i>Masukan Kode</h6>
                        <p class="text-muted mb-0">Ketik atau paste kode/serial number barang ke dalam kolom input. Bisa berupa kombinasi huruf dan angka.</p>
                    </div>
                    <hr class="my-3">
                    <div class="mb-3">
                        <h6 class="fw-bold text-primary"><i class="fas fa-2 me-2"></i>Preview Otomatis</h6>
                        <p class="text-muted mb-0">Barcode akan ter-generate secara otomatis dan ditampilkan di area preview dalam format CODE128.</p>
                    </div>
                    <hr class="my-3">
                    <div class="mb-3">
                        <h6 class="fw-bold text-primary"><i class="fas fa-3 me-2"></i>Download Barcode</h6>
                        <p class="text-muted mb-0">Klik tombol "Download JPEG" untuk menyimpan barcode sebagai file gambar. File siap untuk dicetak atau ditempelkan pada barang.</p>
                    </div>
                    <hr class="my-3">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Tips:</strong> Gunakan kode yang sama dengan Serial Number barang agar mudah untuk scan saat peminjaman!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($view == 'users'): ?>

    <div style="height: calc(100vh - 70px); display: flex; flex-direction: column; overflow: hidden; padding: 1rem;">

    <div class="page-header d-flex justify-content-between align-items-center mb-4 pt-3 flex-shrink-0">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-users me-2"></i>Data Pengguna</h4>
            <p class="text-muted text-sm mb-0">Daftar Guru dan Siswa yang terdaftar dalam sistem.</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="exportToExcel()" class="btn btn-info btn-sm" title="Export data ke file Excel">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
            <button onclick="openImportExcelModal()" class="btn btn-warning btn-sm" title="Import data dari file Excel">
                <i class="fas fa-file-csv me-1"></i> Import Excel
            </button>
            <button onclick="openBulkImportModal()" class="btn btn-success btn-sm" title="Import multiple users">
                <i class="fas fa-file-import me-1"></i> Import Bulk
            </button>
            <button onclick="bulkDeleteUsers()" class="btn btn-danger btn-sm" id="bulkDeleteBtn" disabled>
                <i class="fas fa-trash me-1"></i> Hapus Terpilih
            </button>
            <button onclick="openUserModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> Tambah Pengguna</button>
        </div>
    </div>

    <!-- Search Box -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input 
                    type="text" 
                    class="form-control border-start-0" 
                    id="userSearchInput" 
                    placeholder="Cari berdasarkan ID, Nama, Kelas, Email, atau No. Telepon..."
                    onkeyup="filterUserTable()"
                >
                <button class="btn btn-outline-secondary" type="button" onclick="clearUserSearch()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <small class="text-muted mt-2 d-block">
                <i class="fas fa-info-circle me-1"></i>
                Ketik untuk mencari data pengguna secara real-time
                <span id="userSearchResultInfo" class="ms-2 fw-600 text-primary"></span>
            </small>
        </div>
    </div>

    <div class="card border-0 shadow-sm flex-grow-1" style="min-height: 0; display: flex; flex-direction: column; overflow: hidden;">
        <div class="card-body p-0 flex-grow-1" style="min-height: 0;">
            <div style="height: 100%; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0" id="userTable">
                <thead class="sticky-top" style="top: 0; background-color: #f8f9fa; z-index: 10;">
                        <tr>
                            <th class="ps-4" style="width: 50px;">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this.checked)">
                            </th>
                            <th>No</th>
                            <th>ID (NISN/NIP)</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(empty($users)): 
                        ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-users fa-2x mb-3 text-light-emphasis"></i>
                                    <p class="mb-0">Belum ada data pengguna. Silakan tambahkan pengguna baru.</p>
                                </td>
                            </tr>
                        <?php else: $no=1; foreach($users as $u): ?>
                        <tr>
                            <td class="ps-4">
                                <input type="checkbox" class="userCheckbox" value="<?= $u['id'] ?>" onchange="updateBulkDeleteBtn()">
                            </td>
                            <td class="ps-4 text-muted"><?= $no++ ?></td>
                            <td>
                                <code class="text-dark fw-bold text-sm"><?= $u['identity_number'] ?? '-' ?></code>
                            </td>
                            <td>
                                <div class="fw-600 text-dark"><?= htmlspecialchars($u['name'] ?? '') ?></div>
                            </td>
                            <td>
                                <span class="text-sm text-muted"><?= htmlspecialchars($u['kelas'] ?? '-') ?></span>
                            </td>
                            <td>
                                <span class="text-sm text-muted"><?= htmlspecialchars($u['email'] ?? '-') ?></span>
                            </td>
                            <td>
                                <span class="text-sm text-muted"><?= htmlspecialchars($u['phone'] ?? '-') ?></span>
                            </td>
                            <td>
                                <?php if(($u['role'] ?? 'student') == 'teacher'): ?>
                                    <span class="badge bg-primary text-white">Guru</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary text-white">Pelajar</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-warning" onclick="editUser(<?= htmlspecialchars(json_encode($u), ENT_QUOTES) ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?= $u['id'] ?>)">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    </div>

    <!-- User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalTitle">Tambah Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm">
                        <input type="hidden" id="userId">
                        
                        <div class="mb-3">
                            <label class="form-label">ID (NISN/NIP) *</label>
                            <input type="text" class="form-control" id="userIdentity" placeholder="e.g. 2024001" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap *</label>
                            <input type="text" class="form-control" id="userName" placeholder="e.g. Ahmad Dani" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas *</label>
                            <select class="form-select" id="userClass" required>
                                <option value="">Pilih Kelas</option>
                                <option value="10 PPLG 1">10 PPLG 1</option>
                                <option value="10 PPLG 2">10 PPLG 2</option>
                                <option value="10 PPLG 3">10 PPLG 3</option>
                                <option value="11 PPLG 1">11 PPLG 1</option>
                                <option value="11 PPLG 2">11 PPLG 2</option>
                                <option value="11 PPLG 3">11 PPLG 3</option>
                                <option value="12 PPLG 1">12 PPLG 1</option>
                                <option value="12 PPLG 2">12 PPLG 2</option>
                                <option value="12 PPLG 3">12 PPLG 3</option>
                                <option value="-">-</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status *</label>
                            <select class="form-select" id="userRole" required>
                                <option value="student">Pelajar</option>
                                <option value="teacher">Guru</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="userEmail" placeholder="e.g. ahmad@school.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="tel" class="form-control" id="userPhone" placeholder="e.g. 08123456789">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="saveUserBtn" onclick="saveUser()" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bulk Import Pengguna -->
    <div class="modal fade" id="bulkImportModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-import me-2"></i>Import Data Pengguna Bulk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <strong>Format:</strong> ID | Nama | Kelas | Role | Email | No. Telepon<br>
                        <strong>Contoh:</strong><br>
                        <code>2024001 | Ahmad Dani | 10 PPLG 1 | student | ahmad@school.com | 08123456789</code><br>
                        <code>19800101 | Pak Budi | - | teacher | budi@school.com | 08112345678</code><br>
                        <small class="text-muted">Field Email dan No. Telepon bersifat opsional (bisa dikosongkan)</small>
                    </div>

                    <form id="bulkImportForm">
                        <div class="mb-3">
                            <label class="form-label">Paste Data Pengguna (satu baris per pengguna)</label>
                            <textarea 
                                class="form-control font-monospace" 
                                id="bulkImportData" 
                                rows="8"
                                placeholder="2024001 | Ani | 10 PPLG 1 | student | ani@school.com | 08123456789&#10;2024002 | Budi | 10 PPLG 1 | student&#10;2024003 | Citra | 10 PPLG 2 | student"
                                onkeyup="parseImportData()"
                                oninput="parseImportData()"></textarea>
                        </div>

                        <div id="importDataPreview"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import Excel Pengguna -->
    <div class="modal fade" id="importExcelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-excel me-2"></i>Import Data Pengguna dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <strong>Format File:</strong> CSV atau Excel (.csv, .xlsx, .xls)<br>
                        <strong>Struktur Kolom:</strong><br>
                        <code>ID (NISN/NIP) | Nama Lengkap | Kelas | Status | Email | No. Telepon</code><br>
                        <small class="text-muted">Kolom Email dan No. Telepon bersifat opsional. Status: Guru atau Pelajar</small>
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="downloadExcelTemplate()">
                            <i class="fas fa-download me-2"></i> Download Template Excel
                        </button>
                    </div>

                    <form id="importExcelForm">
                        <div class="mb-3">
                            <label class="form-label">Pilih File Excel/CSV</label>
                            <input type="file" class="form-control" id="excelFile" accept=".csv,.xlsx,.xls" required>
                            <small class="text-muted d-block mt-2">Max 5MB • Format: CSV, XLSX, atau XLS</small>
                        </div>
                    </form>

                    <div id="excelPreview"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="submitExcelImport()" id="submitExcelBtn">
                        <i class="fas fa-upload me-2"></i> Import
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($view == 'logs'): ?>

    <div style="height: calc(100vh - 70px); display: flex; flex-direction: column; overflow: hidden; padding: 1rem;">

    <div class="page-header d-flex justify-content-between align-items-center mb-3 flex-shrink-0">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-history me-2"></i>Log & Aktivitas Sistem</h4>
            <p class="text-muted text-sm mb-0">Riwayat lengkap semua perubahan data barang dan pengguna dalam sistem.</p>
        </div>
        <div class="d-flex gap-2">
            <?php if (AuthManager::isLoggedIn()): ?>
                <button onclick="clearAllLogs()" class="btn btn-outline-danger btn-sm" title="Hapus semua log">
                    <i class="fas fa-trash me-1"></i> Hapus Semua Log
                </button>
            <?php else: ?>
                <button onclick="alert('⚠️ Anda perlu login sebagai admin untuk menghapus log. Klik logout untuk login ulang.')" class="btn btn-outline-danger btn-sm" title="Hanya admin yang dapat menghapus" disabled>
                    <i class="fas fa-lock me-1"></i> Hapus Semua Log (Admin Only)
                </button>
            <?php endif; ?>
            <button onclick="location.reload()" class="btn btn-outline-secondary btn-sm"><i class="fas fa-sync-alt me-1"></i> Refresh</button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-3 flex-shrink-0">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label text-sm fw-500">Filter Tipe Aksi</label>
                    <select class="form-select form-select-sm" id="filterAction" onchange="filterLogs()">
                        <option value="">Semua Aksi</option>
                        <option value="CREATE">CREATE (Tambah)</option>
                        <option value="UPDATE">UPDATE (Ubah)</option>
                        <option value="DELETE">DELETE (Hapus)</option>
                        <option value="BORROW">BORROW (Pinjam)</option>
                        <option value="RETURN">RETURN (Kembali)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-sm fw-500">Filter Tabel/Kategori</label>
                    <select class="form-select form-select-sm" id="filterTable" onchange="filterLogs()">
                        <option value="">Semua Data</option>
                        <option value="users">Data Pengguna</option>
                        <option value="assets">Data Barang</option>
                        <option value="loans">Peminjaman</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-500">Cari</label>
                    <input type="text" class="form-control form-control-sm" id="searchLogs" placeholder="Cari nama, ID, barang..." onkeyup="filterLogs()">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-500">Filter Periode Waktu</label>
                    <select class="form-select form-select-sm" id="filterPeriod" onchange="filterLogs()">
                        <option value="">Semua Waktu</option>
                        <option value="today">Hari Ini</option>
                        <option value="yesterday">Kemarin</option>
                        <option value="week">7 Hari Terakhir</option>
                        <option value="month">30 Hari Terakhir</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-sm fw-500">&nbsp;</label>
                    <button onclick="resetFilters()" class="btn btn-outline-secondary btn-sm w-100">Reset Filter</button>
                </div>
            </div>
            
            <!-- Custom Date Range (Hidden by default) -->
            <div id="customDateRange" class="row g-3 mt-2" style="display: none;">
                <div class="col-md-3">
                    <label class="form-label text-sm fw-500">Dari Tanggal</label>
                    <input type="date" class="form-control form-control-sm" id="dateFrom" onchange="filterLogs()">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-500">Sampai Tanggal</label>
                    <input type="date" class="form-control form-control-sm" id="dateTo" onchange="filterLogs()">
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card border-0 shadow-sm flex-grow-1 d-flex flex-column overflow-hidden" style="min-height: 0;">
        <div class="card-body p-0 flex-grow-1" style="min-height: 0;">
            <div style="height: 100%; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0" id="logsTable">
                    <thead class="sticky-top" style="top: 0; background-color: #f8f9fa; z-index: 10;">
                        <tr>
                            <th class="ps-4">Timestamp</th>
                            <th>Aksi</th>
                            <th>Kategori</th>
                            <th>Data</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $logs = $activityLog->getAll();
                        if(empty($logs)): 
                        ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-3 text-light-emphasis"></i>
                                    <p class="mb-0">Belum ada log aktivitas. Mulai dengan melakukan perubahan data.</p>
                                </td>
                            </tr>
                        <?php else: foreach($logs as $log): ?>
                        <tr class="log-row" data-action="<?= $log['action'] ?>" data-table="<?= $log['table'] ?>" data-search="<?= strtolower($log['data'] . ' ' . $log['details']) ?>" data-timestamp="<?= $log['timestamp'] ?>">
                            <td class="ps-4">
                                <span class="text-sm fw-500 text-dark"><?= date('d/m/Y', strtotime($log['timestamp'])) ?></span><br>
                                <small class="text-muted"><?= date('H:i:s', strtotime($log['timestamp'])) ?></small>
                            </td>
                            <td>
                                <?php 
                                $actionBadges = [
                                    'CREATE' => ['badge bg-success', 'fas fa-plus', 'Tambah'],
                                    'UPDATE' => ['badge bg-warning', 'fas fa-edit', 'Ubah'],
                                    'DELETE' => ['badge bg-danger', 'fas fa-trash', 'Hapus'],
                                    'BORROW' => ['badge bg-info', 'fas fa-hand-holding', 'Pinjam'],
                                    'RETURN' => ['badge bg-primary', 'fas fa-undo', 'Kembali']
                                ];
                                $badge = $actionBadges[$log['action']] ?? ['badge bg-secondary', 'fas fa-circle', $log['action']];
                                ?>
                                <span class="<?= $badge[0] ?> text-white">
                                    <i class="<?= $badge[1] ?> me-1"></i><?= $badge[2] ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $tableBadges = [
                                    'users' => ['bg-light text-dark', 'fas fa-users', 'Pengguna'],
                                    'assets' => ['bg-light text-dark', 'fas fa-box', 'Barang'],
                                    'loans' => ['bg-light text-dark', 'fas fa-handshake', 'Peminjaman']
                                ];
                                $badge = $tableBadges[$log['table']] ?? ['bg-light text-dark', 'fas fa-database', 'Data'];
                                ?>
                                <span class="badge <?= $badge[0] ?>">
                                    <i class="<?= $badge[1] ?> me-1"></i><?= $badge[2] ?>
                                </span>
                            </td>
                            <td>
                                <strong class="text-dark"><?= htmlspecialchars(substr($log['data'], 0, 40)) ?></strong>
                                <?php if(strlen($log['data']) > 40): ?>
                                    <span class="text-muted">...</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted"><?= htmlspecialchars(substr($log['details'], 0, 50)) ?></small>
                                <?php if(strlen($log['details']) > 50): ?>
                                    <span class="text-muted">...</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    </div>

    <?php elseif ($view == 'print_barcode'): ?>

    <div style="height: calc(100vh - 70px); display: flex; flex-direction: column; overflow: hidden; padding: 1rem;">

    <div class="page-header d-flex justify-content-between align-items-center mb-3 flex-shrink-0">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-print me-2"></i>Cetak Barcode</h4>
            <p class="text-muted text-sm mb-0">Preview dan cetak kertas label barcode ukuran A4 untuk semua barang yang tersedia.</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="printBarcode()" class="btn btn-primary btn-sm" title="Cetak ke printer">
                <i class="fas fa-print me-1"></i> Cetak
            </button>
            <button onclick="exportBarcodePDF()" class="btn btn-danger btn-sm" title="Export ke PDF">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </button>
        </div>
    </div>

    <!-- Filter Options Card -->
    <div class="card border-0 shadow-sm mb-3 flex-shrink-0">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-sm fw-500">Kategori Barang</label>
                    <select class="form-select form-select-sm" id="filterCategory" onchange="updatePrintBarcodePreview()">
                        <option value="">Semua Kategori</option>
                        <?php 
                        $categories = [];
                        foreach($assets as $a) {
                            if(!empty($a['category']) && !in_array($a['category'], $categories)) {
                                $categories[] = $a['category'];
                            }
                        }
                        sort($categories);
                        foreach($categories as $cat): 
                        ?>
                            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-sm fw-500">Status Barang</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="statusFilter" id="statusAvailable" value="available" checked onchange="updatePrintBarcodePreview()">
                        <label class="btn btn-outline-success btn-sm" for="statusAvailable">
                            <i class="fas fa-check-circle me-1"></i>Tersedia
                        </label>
                        <input type="radio" class="btn-check" name="statusFilter" id="statusAll" value="all" onchange="updatePrintBarcodePreview()">
                        <label class="btn btn-outline-secondary btn-sm" for="statusAll">
                            <i class="fas fa-th me-1"></i>Semua
                        </label>
                    </div>
                </div>
                <div class="col-md-3">
                    <button onclick="updatePrintBarcodePreview()" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-sync me-1"></i> Update Preview
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Card -->
    <div class="card border-0 shadow-sm mb-3 flex-shrink-0">
        <div class="card-body py-2 px-4">
            <div class="row g-3">
                <div class="col-auto">
                    <span class="text-sm text-muted">Total Barang Ditampilkan:</span>
                    <span class="fw-bold text-primary" id="totalItems">0</span>
                </div>
                <div class="col-auto">
                    <span class="text-sm text-muted">Jumlah Halaman A4:</span>
                    <span class="fw-bold text-primary" id="totalPages">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Container (A4 Format) -->
    <div class="flex-grow-1" style="min-height: 0; overflow-y: auto; background: #e0e0e0;">
        <div id="barcodePreviewContainer" style="padding: 2rem; display: flex; flex-direction: column; gap: 2rem; align-items: center;">
            <!-- A4 pages will be rendered here -->
        </div>
    </div>

    </div>

    <?php elseif ($view == 'settings'): ?>

    <div style="height: calc(100vh - 70px); display: flex; flex-direction: column; overflow: hidden; padding: 1rem;">

    <div class="page-header d-flex justify-content-between align-items-center mb-3 flex-shrink-0">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-cog me-2"></i>Pengaturan Sistem</h4>
            <p class="text-muted text-sm mb-0">Atur konfigurasi running text dan pengaturan lainnya.</p>
        </div>
    </div>

    <!-- Running Text Settings -->
    <div class="card border-0 shadow-sm flex-grow-1" style="min-height: 0; max-height: 100%; overflow-y: auto;">
        <div class="card-body">
            <h6 class="fw-bold mb-4"><i class="fas fa-scroll me-2 text-primary"></i>Pengaturan Running Text</h6>
            
            <form id="runningTextForm">
                <!-- Text Content -->
                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form-label fw-500">Teks yang Ditampilkan</label>
                        <textarea class="form-control" id="runningTextInput" placeholder="Masukkan teks running text..." rows="3" required></textarea>
                        <small class="text-muted d-block mt-2">Teks ini akan menampilkan animasi bergerak (scrolling) di bagian bawah dashboard peminjam.</small>
                    </div>
                </div>

                <!-- Animation Speed -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-500">Kecepatan Animasi (detik)</label>
                        <div class="input-group">
                            <input type="range" class="form-range" id="animationSpeed" min="5" max="60" value="20">
                            <input type="number" class="form-control" id="animationSpeedValue" value="20" min="5" max="60" style="width: 80px; margin-left: 10px;">
                        </div>
                        <small class="text-muted">Lebih kecil = lebih cepat, Lebih besar = lebih lambat</small>
                    </div>
                </div>

                <!-- Colors Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-500">Warna Background (Start)</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="bgColor" value="#667eea" style="width: 60px; padding: 8px;">
                            <input type="text" class="form-control" id="bgColorText" value="#667eea" placeholder="#667eea" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">Warna Background (End)</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="bgColorEnd" value="#764ba2" style="width: 60px; padding: 8px;">
                            <input type="text" class="form-control" id="bgColorEndText" value="#764ba2" placeholder="#764ba2" readonly>
                        </div>
                    </div>
                </div>

                <!-- Text Color and Font -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-500">Warna Teks</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="textColor" value="#ffffff" style="width: 60px; padding: 8px;">
                            <input type="text" class="form-control" id="textColorText" value="#ffffff" placeholder="#ffffff" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">Jenis Font</label>
                        <select class="form-select" id="fontFamily">
                            <option value="Arial, sans-serif">Arial</option>
                            <option value="'Segoe UI', Tahoma, sans-serif">Segoe UI</option>
                            <option value="'Times New Roman', serif">Times New Roman</option>
                            <option value="'Courier New', monospace">Courier New</option>
                            <option value="Georgia, serif">Georgia</option>
                            <option value="Trebuchet, sans-serif">Trebuchet</option>
                            <option value="Verdana, sans-serif">Verdana</option>
                        </select>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex gap-2 mb-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="loadCurrentRunningText()">
                        <i class="fas fa-redo me-2"></i> Muat Ulang
                    </button>
                </div>

                <div id="runningTextAlert" class="mt-3" style="display: none;"></div>
            </form>

            <hr class="my-4">

            <!-- Preview Section -->
            <h6 class="fw-bold mb-3"><i class="fas fa-eye me-2 text-info"></i>Preview</h6>
            <div class="alert alert-info mb-0">
                <p class="mb-3"><strong>Running Text Preview:</strong></p>
                <div id="settingsRunningTextPreview" class="running-text-container p-3" style="position: static; margin-bottom: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="running-text" style="animation: scroll-left 20s linear infinite; color: white; font-family: Arial, sans-serif;">
                        Teks preview - ini adalah contoh animasi
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>

    <?php endif; ?>
</div>

<script>
const borrowForm = document.getElementById('borrowForm');
if (borrowForm) {
    borrowForm.onsubmit = async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    
    // UI Loading State
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memindai...';
    
    const idNumber = document.getElementById('identityNumber').value;
    const assetCode = document.getElementById('assetCode').value;
    
    try {
        const res = await fetch('?action=borrow', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({identity_number: idNumber, asset_code: assetCode})
        });
        const data = await res.json();
        
        if(data.success) {
            showNotification(
                '✓ Peminjaman Berhasil',
                data.message,
                'success',
                4000
            );
            setTimeout(() => location.reload(), 2000);
        } else {
            showNotification(
                '⚠ Peminjaman Gagal',
                data.message,
                'error',
                5000
            );
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    } catch(err) {
        console.error(err);
        showNotification(
            '❌ Kesalahan Sistem',
            'Terjadi kesalahan saat memproses peminjaman',
            'error',
            5000
        );
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
    };
}

const returnForm = document.getElementById('returnForm');
if (returnForm) {
    returnForm.onsubmit = async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    
    // UI Loading State
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
    
    const idNumber = document.getElementById('returnIdentityNumber').value;
    const assetCode = document.getElementById('returnAssetCode').value;
    const condition = document.querySelector('input[name="returnCondition"]:checked').value;
    const notes = document.getElementById('returnNotes').value;
    
    try {
        const res = await fetch('?action=return', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                identity_number: idNumber, 
                asset_code: assetCode,
                condition: condition,
                notes: notes
            })
        });
        const data = await res.json();
        
        if(data.success) {
            showNotification(
                '✓ Pengembalian Berhasil',
                data.message,
                'success',
                4000
            );
            setTimeout(() => location.reload(), 2000);
        } else {
            showNotification(
                '⚠ Pengembalian Gagal',
                data.message,
                'error',
                5000
            );
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    } catch(err) {
        console.error(err);
        showNotification(
            '❌ Kesalahan Sistem',
            'Terjadi kesalahan saat memproses pengembalian',
            'error',
            5000
        );
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
};
}

    // --- USER SEARCH LOGIC ---

    function filterUserTable() {
        const searchInput = document.getElementById('userSearchInput');
        const filterValue = searchInput.value.toLowerCase().trim();
        const tableBody = document.querySelector('#userTable tbody');
        const rows = tableBody.querySelectorAll('tr');
        let visibleCount = 0;

        rows.forEach((row) => {
            // Skip empty state row
            if (row.querySelector('td[colspan]')) return;

            // Get all cell text content
            const cells = row.querySelectorAll('td');
            let rowText = '';
            
            cells.forEach((cell, index) => {
                // Skip No dan Aksi columns
                if (index !== 0 && index !== cells.length - 1) {
                    rowText += cell.textContent.toLowerCase() + ' ';
                }
            });

            // Check if row matches filter
            if (filterValue === '' || rowText.includes(filterValue)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide "no results" message
        const emptyStateRow = tableBody.querySelector('tr[data-empty-state]');
        if (visibleCount === 0 && rows.length > 0) {
            if (!emptyStateRow) {
                const noResultsRow = document.createElement('tr');
                noResultsRow.setAttribute('data-empty-state', 'true');
                noResultsRow.innerHTML = `
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="fas fa-search fa-2x mb-3 text-light-emphasis"></i>
                        <p class="mb-0">Tidak ada data pengguna yang sesuai dengan pencarian.</p>
                    </td>
                `;
                tableBody.appendChild(noResultsRow);
            }
        } else if (emptyStateRow) {
            emptyStateRow.remove();
        }

        // Update result count
        const searchResultInfo = document.getElementById('userSearchResultInfo');
        if (filterValue) {
            if (searchResultInfo) {
                searchResultInfo.textContent = `Ditemukan ${visibleCount} data`;
            }
        }
    }

    function clearUserSearch() {
        const searchInput = document.getElementById('userSearchInput');
        searchInput.value = '';
        filterUserTable();
        searchInput.focus();
    }

    // --- USER CRUD LOGIC ---

    function openUserModal(user = null) {
        const userModalElement = document.getElementById('userModal');
        const userModal = new bootstrap.Modal(userModalElement);

        if (user) {
            document.getElementById('userModalTitle').innerText = 'Edit Pengguna';
            document.getElementById('userId').value = user.id;
            document.getElementById('userIdentity').value = user.identity_number;
            document.getElementById('userName').value = user.name;
            document.getElementById('userClass').value = user.kelas || '-';
            document.getElementById('userRole').value = user.role || 'student';
            document.getElementById('userEmail').value = user.email || '';
            document.getElementById('userPhone').value = user.phone || '';
        } else {
            document.getElementById('userModalTitle').innerText = 'Tambah Pengguna';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
        }

        userModal.show();
        
        // Auto-focus first input field
        setTimeout(() => {
            document.getElementById('userIdentity').focus();
        }, 300);
    }

    function editUser(user) {
        openUserModal(user);
    }

    async function saveUser() {
        const id = document.getElementById('userId').value;
        const identity = document.getElementById('userIdentity').value;
        const name = document.getElementById('userName').value;
        const kelas = document.getElementById('userClass').value;
        const role = document.getElementById('userRole').value;
        const email = document.getElementById('userEmail').value;
        const phone = document.getElementById('userPhone').value;

        if(!identity || !name || !kelas || !role) {
            alert("⚠️ Harap isi semua field yang wajib (*)!");
            return;
        }

        // Disable button dan tampilkan loading state
        const btn = document.getElementById('saveUserBtn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';

        const action = id ? 'edit_user' : 'add_user';
        const payload = { 
            id, 
            identity_number: identity, 
            name, 
            kelas, 
            role, 
            email, 
            phone 
        };

        try {
            const res = await fetch('?action=' + action, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if (data.success) {
                // Close modal
                const modalElement = document.getElementById('userModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
                
                // Tampilkan success alert
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                successAlert.style.zIndex = '9999';
                successAlert.style.minWidth = '400px';
                successAlert.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i> <strong>${action === 'add_user' ? '✅ Pengguna berhasil ditambahkan!' : '✅ Data pengguna berhasil diperbarui!'}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(successAlert);
                
                // Remove modal backdrop
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(b => b.remove());
                
                // Reset form
                document.getElementById('userForm').reset();
                
                // Reload halaman setelah 2 detik
                setTimeout(() => location.reload(), 2000);
            } else {
                btn.disabled = false;
                btn.innerHTML = originalText;
                alert("❌ " + data.message);
            }
        } catch (e) {
            btn.disabled = false;
            btn.innerHTML = originalText;
            alert("❌ Terjadi kesalahan sistem: " + e.message);
            console.error(e);
        }
    }

    async function deleteUser(id) {
        if(!confirm("⚠️ Yakin ingin menghapus pengguna ini? Aksi ini tidak dapat dibatalkan.")) return;

        try {
            const res = await fetch('?action=delete_user', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id})
            });
            const data = await res.json();
            
            if (data.success) {
                // Tampilkan success alert
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                successAlert.style.zIndex = '9999';
                successAlert.style.minWidth = '400px';
                successAlert.innerHTML = `
                    <i class="fas fa-trash-alt me-2"></i> <strong>✅ ${data.message}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(successAlert);
                
                // Reload halaman setelah 1.5 detik
                setTimeout(() => location.reload(), 1500);
            } else {
                alert("❌ " + data.message);
            }
        } catch (e) {
            alert("❌ Terjadi kesalahan sistem: " + e.message);
            console.error(e);
        }
    }

    // --- BULK OPERATIONS ---

    function toggleSelectAll(checked) {
        document.querySelectorAll('.userCheckbox').forEach(cb => {
            cb.checked = checked;
        });
        updateBulkDeleteBtn();
    }

    function updateBulkDeleteBtn() {
        const checkedCount = document.querySelectorAll('.userCheckbox:checked').length;
        const btn = document.getElementById('bulkDeleteBtn');
        if(checkedCount > 0) {
            btn.disabled = false;
            btn.innerHTML = `<i class="fas fa-trash me-1"></i> Hapus Terpilih (${checkedCount})`;
        } else {
            btn.disabled = true;
            btn.innerHTML = `<i class="fas fa-trash me-1"></i> Hapus Terpilih`;
            document.getElementById('selectAllCheckbox').checked = false;
        }
    }

    async function bulkDeleteUsers() {
        const ids = Array.from(document.querySelectorAll('.userCheckbox:checked')).map(cb => parseInt(cb.value));
        if(ids.length === 0) {
            alert("⚠️ Pilih minimal satu pengguna!");
            return;
        }

        if(!confirm(`⚠️ Yakin ingin menghapus ${ids.length} pengguna? Aksi ini tidak dapat dibatalkan.`)) return;

        try {
            const res = await fetch('?action=bulk_delete_users', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ids})
            });
            const data = await res.json();
            
            if (data.success) {
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                successAlert.style.zIndex = '9999';
                successAlert.style.minWidth = '400px';
                successAlert.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i> <strong>${data.message}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(successAlert);
                
                setTimeout(() => location.reload(), 1500);
            } else {
                alert("❌ " + data.message);
            }
        } catch (e) {
            alert("❌ Terjadi kesalahan sistem: " + e.message);
            console.error(e);
        }
    }

    function openBulkImportModal() {
        const modal = new bootstrap.Modal(document.getElementById('bulkImportModal'));
        modal.show();
        // Reset form
        document.getElementById('bulkImportForm').reset();
        document.getElementById('importDataPreview').innerHTML = '';
    }

    function parseImportData() {
        const textarea = document.getElementById('bulkImportData').value;
        const preview = document.getElementById('importDataPreview');
        
        if(!textarea.trim()) {
            preview.innerHTML = '';
            return;
        }

        try {
            const lines = textarea.trim().split('\n');
            const users = [];
            
            for(let i = 0; i < lines.length; i++) {
                const parts = lines[i].split('|').map(p => p.trim());
                if(parts.length < 4) continue; // identity_number, name, kelas, role required
                
                users.push({
                    identity_number: parts[0],
                    name: parts[1],
                    kelas: parts[2],
                    role: parts[3],
                    email: parts[4] || null,
                    phone: parts[5] || null
                });
            }

            if(users.length === 0) {
                preview.innerHTML = '<div class="alert alert-warning">Format tidak valid. Gunakan format: ID|Nama|Kelas|Role|Email|Phone</div>';
                return;
            }

            let html = `<div class="alert alert-info mb-3"><strong>${users.length} data siap diimport</strong></div>`;
            html += '<table class="table table-sm table-bordered mb-0">';
            html += '<thead class="table-light"><tr><th>ID</th><th>Nama</th><th>Kelas</th><th>Role</th><th>Email</th></tr></thead>';
            html += '<tbody>';
            
            users.slice(0, 5).forEach(u => {
                html += `<tr>
                    <td><code>${u.identity_number}</code></td>
                    <td>${u.name}</td>
                    <td>${u.kelas}</td>
                    <td><span class="badge ${u.role === 'teacher' ? 'bg-primary' : 'bg-secondary'}">${u.role}</span></td>
                    <td><small>${u.email || '-'}</small></td>
                </tr>`;
            });
            
            if(users.length > 5) {
                html += `<tr class="table-light"><td colspan="5" class="text-center text-muted">... dan ${users.length - 5} data lainnya</td></tr>`;
            }
            
            html += '</tbody></table>';
            html += `<button type="button" class="btn btn-success btn-sm mt-3" onclick="submitBulkImport('${textarea.replace(/'/g, "\\'")}')">
                <i class="fas fa-check me-1"></i> Import ${users.length} Data
            </button>`;
            
            preview.innerHTML = html;
            
        } catch (e) {
            preview.innerHTML = `<div class="alert alert-danger">Error parsing: ${e.message}</div>`;
        }
    }

    async function submitBulkImport(rawData) {
        const lines = rawData.trim().split('\n');
        const users = [];
        
        for(let i = 0; i < lines.length; i++) {
            const parts = lines[i].split('|').map(p => p.trim());
            if(parts.length < 4) continue;
            
            users.push({
                identity_number: parts[0],
                name: parts[1],
                kelas: parts[2],
                role: parts[3],
                email: parts[4] || null,
                phone: parts[5] || null
            });
        }

        if(users.length === 0) {
            alert('⚠️ Tidak ada data valid untuk diimport');
            return;
        }

        try {
            const res = await fetch('?action=bulk_add_users', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({users})
            });
            const data = await res.json();
            
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('bulkImportModal'));
                if(modal) modal.hide();
                
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                successAlert.style.zIndex = '9999';
                successAlert.style.minWidth = '500px';
                successAlert.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i> <strong>${data.message}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(successAlert);
                
                setTimeout(() => location.reload(), 1500);
            } else {
                alert("❌ " + data.message);
            }
        } catch (e) {
            alert("❌ Terjadi kesalahan sistem: " + e.message);
            console.error(e);
        }
    }

    // --- EXCEL/CSV IMPORT-EXPORT FUNCTIONS ---

    function exportToExcel() {
        try {
            const loadingBtn = event.target.closest('button');
            const originalHTML = loadingBtn.innerHTML;
            loadingBtn.disabled = true;
            loadingBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Export...';
            
            // Trigger CSV download
            window.location.href = '?action=export_users_csv';
            
            setTimeout(() => {
                loadingBtn.disabled = false;
                loadingBtn.innerHTML = originalHTML;
            }, 1000);
        } catch (e) {
            console.error(e);
            alert("❌ Gagal export data: " + e.message);
        }
    }

    function openImportExcelModal() {
        const modal = new bootstrap.Modal(document.getElementById('importExcelModal'));
        modal.show();
        document.getElementById('importExcelForm').reset();
        document.getElementById('excelPreview').innerHTML = '';
    }

    function downloadExcelTemplate() {
        // Create sample CSV
        const template = `ID (NISN/NIP),Nama Lengkap,Kelas,Status,Email,No. Telepon
2024001,Ahmad Dani,10 PPLG 1,Pelajar,ahmad@school.com,08123456789
2024002,Budi Santoso,10 PPLG 1,Pelajar,budi@school.com,08112345678
19800101,Pak Budi,-,Guru,pakbudi@school.com,08111111111`;

        const blob = new Blob([template], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `template_pengguna_${new Date().getTime()}.csv`;
        link.click();
    }

    document.getElementById('excelFile')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if(!file) return;

        if(file.size > 5 * 1024 * 1024) {
            alert('⚠️ File terlalu besar (max 5MB)');
            this.value = '';
            return;
        }

        const preview = document.getElementById('excelPreview');
        preview.innerHTML = `<div class="alert alert-info"><i class="fas fa-check-circle me-2"></i>File "${file.name}" siap diupload (${(file.size / 1024).toFixed(2)} KB)</div>`;
    });

    async function submitExcelImport() {
        const fileInput = document.getElementById('excelFile');
        const file = fileInput.files[0];
        
        if(!file) {
            alert('⚠️ Pilih file terlebih dahulu!');
            return;
        }

        const formData = new FormData();
        formData.append('csvFile', file);

        try {
            const submitBtn = document.getElementById('submitExcelBtn');
            const originalHTML = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Importing...';

            const res = await fetch('?action=import_users_csv', {
                method: 'POST',
                body: formData
            });
            
            const data = await res.json();
            
            if(data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('importExcelModal'));
                if(modal) modal.hide();
                
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                successAlert.style.zIndex = '9999';
                successAlert.style.minWidth = '500px';
                successAlert.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i> <strong>${data.message}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(successAlert);
                
                setTimeout(() => location.reload(), 1500);
            } else {
                alert("❌ " + data.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
            }
        } catch (e) {
            alert("❌ Terjadi kesalahan: " + e.message);
            console.error(e);
            const submitBtn = document.getElementById('submitExcelBtn');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-upload me-2"></i> Import';
        }
    }

    // --- ASSET IMPORT-EXPORT FUNCTIONS ---

    function showAssetsResume() {
        const modal = new bootstrap.Modal(document.getElementById('assetsResumeModal'));
        modal.show();
    }

    function exportAssetsCsv() {
        try {
            window.location.href = '?action=export_assets_csv';
        } catch(e) {
            alert("Gagal export: " + e.message);
        }
    }

    function openAssetImportModal() {
        const modal = new bootstrap.Modal(document.getElementById('assetImportModal'));
        document.getElementById('assetImportFile').value = '';
        modal.show();
    }

    function downloadAssetTemplate() {
        const csvContent = "Kategori,Merk,Model,Serial Number,Kode Barcode,Status\n" +
            "Laptop,Lenovo,ThinkPad X1,LNV-001,LNV-001,available\n" +
            "Laptop,Dell,Inspiron 15,DEL-001,DEL-001,available\n" +
            "Printer,Canon,G2010,CAN-001,CAN-001,available\n" +
            "Proyektor,Epson,EB-X05,EPS-001,EPS-001,available\n" +
            "Monitor,Samsung,24 inch,SAM-001,SAM-001,available\n" +
            "Monitor,LG,27 inch,LG-001,LG-001,available\n" +
            "Tablet,Apple,iPad Pro,APP-001,APP-001,available\n" +
            "Aksesoris,Logitech,Wireless Mouse,LOG-001,LOG-001,available\n" +
            "Aksesoris,HP,USB Drive 32GB,HP-001,HP-001,available";
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'template_data_barang.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('assetImportFile');
        if(fileInput) {
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                
                if(!file) return;

                if(file.size > 5 * 1024 * 1024) {
                    alert('⚠️ File terlalu besar (max 5MB)');
                    this.value = '';
                    return;
                }
            });
        }
    });

    async function submitAssetImport() {
        const fileInput = document.getElementById('assetImportFile');
        const file = fileInput.files[0];
        
        if(!file) {
            alert('⚠️ Pilih file terlebih dahulu!');
            return;
        }

        const formData = new FormData();
        formData.append('csvFile', file);

        try {
            const submitBtn = document.querySelector('#assetImportModal .btn-primary');
            const originalHTML = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Uploading...';

            const res = await fetch('?action=import_assets_csv', {
                method: 'POST',
                body: formData
            });
            
            const data = await res.json();
            
            if(data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('assetImportModal'));
                if(modal) modal.hide();
                
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                successAlert.style.zIndex = '9999';
                successAlert.style.minWidth = '500px';
                successAlert.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i> <strong>${data.message}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(successAlert);
                
                setTimeout(() => location.reload(), 1500);
            } else {
                alert("❌ " + data.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
            }
        } catch(e) {
            alert("❌ Terjadi kesalahan sistem: " + e.message);
            console.error(e);
            const submitBtn = document.querySelector('#assetImportModal .btn-primary');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-upload me-2"></i> Upload & Import';
        }
    }

    // --- AUTHENTICATION FUNCTIONS ---

    function loginAdmin() {
        var form = document.getElementById('loginForm');
        var pass = document.getElementById('adminPassword').value.trim();
        
        if (!pass) {
            alert('Masukkan password!');
            return false;
        }
        
        // Disable button
        var btn = document.getElementById('loginButton');
        var originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Proses...';
        
        // Simple XMLHttpRequest - most reliable
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '?action=login', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        
        xhr.onload = function() {
            btn.disabled = false;
            btn.innerHTML = originalText;
            
            if (xhr.status === 200) {
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.success) {
                        // Success - redirect to dashboard
                        setTimeout(function() {
                            window.location.href = '?view=dashboard';
                        }, 100);
                    } else {
                        alert('Password salah: ' + (res.message || 'Unknown error'));
                        document.getElementById('adminPassword').value = '';
                        document.getElementById('adminPassword').focus();
                    }
                } catch(e) {
                    alert('Error parsing response: ' + e.message);
                }
            } else {
                alert('Login gagal. Status: ' + xhr.status);
            }
        };
        
        xhr.onerror = function() {
            btn.disabled = false;
            btn.innerHTML = originalText;
            alert('Network error. Cek koneksi internet.');
        };
        
        xhr.send(JSON.stringify({password: pass}));
        return false;
    }

    function testLoginAPI() {
        console.log('=== STARTING API TEST ===');
        const button = event.target;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Testing...';
        
        const password = 'admin123';
        console.log('Testing with default password...');
        
        fetch('?action=login', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            credentials: 'same-origin',
            body: JSON.stringify({password: password})
        })
        .then(res => {
            console.log('API Response status:', res.status);
            console.log('Response headers:', {
                'content-type': res.headers.get('content-type'),
                'set-cookie': res.headers.get('set-cookie')
            });
            return res.text().then(text => ({status: res.status, text}));
        })
        .then(({status, text}) => {
            console.log('Response body:', text);
            const data = JSON.parse(text);
            console.log('Parsed data:', data);
            console.log('=== API TEST COMPLETE ===');
            
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-flask me-2"></i> Test API (Debug)';
            
            let message = '✅ API TEST SUCCESSFUL\n\n';
            message += 'Status: ' + status + '\n';
            message += 'Success: ' + (data.success ? 'YES' : 'NO') + '\n';
            message += 'Message: ' + data.message + '\n\n';
            message += 'Console (F12) menampilkan detail lengkap.';
            
            alert(message);
        })
        .catch(e => {
            console.error('=== API TEST FAILED ===');
            console.error('Error:', e);
            console.error('================================');
            
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-flask me-2"></i> Test API (Debug)';
            
            alert('❌ API TEST FAILED\n\n' + 
                  'Error: ' + e.message + '\n\n' +
                  'Lihat Console (F12) untuk detail.\n\n' +
                  'Kemungkinan:\n' +
                  '1. Server tidak berjalan\n' +
                  '2. Port 8000 tidak terbuka\n' +
                  '3. Firewall memblokir koneksi');
        });
    }

    function logoutAdmin() {
        if (!confirm('Yakin ingin logout dari admin mode?')) return;
        
        console.log('=== LOGOUT ATTEMPT ===');
        
        // Disable button to prevent double-click
        const button = event.target.closest('button');
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Logout...';
        }
        
        try {
            fetch('?action=logout', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                credentials: 'same-origin',
                body: JSON.stringify({})
            })
            .then(res => {
                console.log('Logout response status:', res.status);
                if (!res.ok) {
                    throw new Error(`HTTP Error: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                console.log('Logout response:', data);
                
                if (data.success) {
                    const logoutAlert = document.createElement('div');
                    logoutAlert.className = 'alert alert-info alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                    logoutAlert.style.zIndex = '9999';
                    logoutAlert.style.minWidth = '400px';
                    logoutAlert.innerHTML = `
                        <i class="fas fa-check-circle me-2"></i> <strong>${data.message}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(logoutAlert);
                    
                    console.log('Logout SUCCESS! Redirecting to public mode (dashboard)...');
                    setTimeout(() => {
                        window.location.href = '?view=dashboard';
                    }, 800);
                } else {
                    console.error('Logout failed:', data.message);
                    alert('❌ Logout gagal: ' + (data.message || 'Unknown error'));
                    if (button) {
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-sign-out-alt me-1"></i> Logout';
                    }
                }
            })
            .catch(e => {
                console.error('Logout error:', e);
                alert('❌ Kesalahan: ' + e.message);
                if (button) {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-sign-out-alt me-1"></i> Logout';
                }
            });
        } catch (e) {
            console.error('Unexpected error:', e);
            alert('❌ Error tidak terduga: ' + e.message);
            if (button) {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-sign-out-alt me-1"></i> Logout';
            }
        }
    }

    // --- LOGS FILTERING FUNCTIONS ---

    function filterLogs() {
        const actionFilter = document.getElementById('filterAction')?.value || '';
        const tableFilter = document.getElementById('filterTable')?.value || '';
        const searchInput = document.getElementById('searchLogs')?.value?.toLowerCase() || '';
        const periodFilter = document.getElementById('filterPeriod')?.value || '';
        
        // Show/hide custom date range
        const customDateRange = document.getElementById('customDateRange');
        if (customDateRange) {
            customDateRange.style.display = periodFilter === 'custom' ? 'flex' : 'none';
        }
        
        // Get date range based on period filter
        let dateFrom = null;
        let dateTo = new Date();
        dateTo.setHours(23, 59, 59, 999); // End of today
        
        if (periodFilter === 'today') {
            dateFrom = new Date();
            dateFrom.setHours(0, 0, 0, 0);
        } else if (periodFilter === 'yesterday') {
            dateFrom = new Date();
            dateFrom.setDate(dateFrom.getDate() - 1);
            dateFrom.setHours(0, 0, 0, 0);
            dateTo = new Date();
            dateTo.setDate(dateTo.getDate() - 1);
            dateTo.setHours(23, 59, 59, 999);
        } else if (periodFilter === 'week') {
            dateFrom = new Date();
            dateFrom.setDate(dateFrom.getDate() - 7);
            dateFrom.setHours(0, 0, 0, 0);
        } else if (periodFilter === 'month') {
            dateFrom = new Date();
            dateFrom.setDate(dateFrom.getDate() - 30);
            dateFrom.setHours(0, 0, 0, 0);
        } else if (periodFilter === 'custom') {
            const dateFromInput = document.getElementById('dateFrom')?.value;
            const dateToInput = document.getElementById('dateTo')?.value;
            if (dateFromInput) {
                dateFrom = new Date(dateFromInput);
                dateFrom.setHours(0, 0, 0, 0);
            }
            if (dateToInput) {
                dateTo = new Date(dateToInput);
                dateTo.setHours(23, 59, 59, 999);
            }
        }
        
        const rows = document.querySelectorAll('.log-row');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const action = row.getAttribute('data-action');
            const table = row.getAttribute('data-table');
            const search = row.getAttribute('data-search');
            const timestamp = row.getAttribute('data-timestamp');
            
            let match = true;
            
            if (actionFilter && action !== actionFilter) match = false;
            if (tableFilter && table !== tableFilter) match = false;
            if (searchInput && !search.includes(searchInput)) match = false;
            
            // Filter by date
            if (periodFilter && timestamp) {
                const logDate = new Date(timestamp);
                if (dateFrom && logDate < dateFrom) match = false;
                if (dateTo && logDate > dateTo) match = false;
            }
            
            row.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });
        
        // Show "no results" message if needed
        if (visibleCount === 0) {
            const tbody = document.querySelector('#logsTable tbody');
            let noResultsRow = document.getElementById('noResultsRow');
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'noResultsRow';
                noResultsRow.innerHTML = '<td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-search fa-2x mb-3"></i><p class="mb-0">Tidak ada log yang sesuai dengan filter.</p></td>';
                tbody.appendChild(noResultsRow);
            }
            noResultsRow.style.display = '';
        } else {
            const noResultsRow = document.getElementById('noResultsRow');
            if (noResultsRow) noResultsRow.style.display = 'none';
        }
    }

    function resetFilters() {
        document.getElementById('filterAction').value = '';
        document.getElementById('filterTable').value = '';
        document.getElementById('searchLogs').value = '';
        document.getElementById('filterPeriod').value = '';
        document.getElementById('dateFrom').value = '';
        document.getElementById('dateTo').value = '';
        document.getElementById('customDateRange').style.display = 'none';
        filterLogs();
    }

    function clearAllLogs() {
        if (!confirm('⚠️ Yakin ingin menghapus SEMUA log? Aksi ini tidak dapat dibatalkan!')) return;
        
        try {
            fetch('?action=clear_logs', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const successAlert = document.createElement('div');
                    successAlert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                    successAlert.style.zIndex = '9999';
                    successAlert.style.minWidth = '400px';
                    successAlert.innerHTML = `
                        <i class="fas fa-check-circle me-2"></i> <strong>${data.message}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(successAlert);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert("❌ " + data.message);
                }
            })
            .catch(e => alert("❌ Error: " + e.message));
        } catch (e) {
            console.error(e);
        }
    }

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    // --- BARCODE GENERATOR LOGIC ---
    function generateBarcode() {
        const input = document.getElementById('barcodeInput').value;
        const placeholder = document.getElementById('barcodePlaceholder');
        const svg = document.getElementById('barcode');
        const btn = document.getElementById('btnDownloadBarcode');

        if (input.trim() === '') {
            svg.style.display = 'none';
            placeholder.style.display = 'block';
            btn.disabled = true;
            return;
        }

        try {
            JsBarcode("#barcode", input, {
                format: "CODE128",
                lineColor: "#000",
                width: 2,
                height: 50,
                displayValue: true
            });
            svg.style.display = 'block';
            placeholder.style.display = 'none';
            btn.disabled = false;
        } catch (e) {
            // Invalid characters for barcode
            console.warn(e);
        }
    }

    function downloadBarcode() {
        const svg = document.getElementById('barcode');
        const input = document.getElementById('barcodeInput').value;
        
        // Create a canvas to convert SVG to JPEG
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const xml = new XMLSerializer().serializeToString(svg);
        const svg64 = btoa(xml);
        const b64Start = 'data:image/svg+xml;base64,';
        const image64 = b64Start + svg64;
        
        const img = new Image();
        img.onload = function() {
            // Set canvas size with white background (JPEG needs background)
            canvas.width = img.width + 20; 
            canvas.height = img.height + 20;
            ctx.fillStyle = "#FFFFFF";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 10, 10);
            
            const jpgUrl = canvas.toDataURL("image/jpeg");
            const link = document.createElement('a');
            link.href = jpgUrl;
            link.download = 'Barcode_' + input + '.jpg';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };
        img.src = image64;
    }

    // Initialize Barcodes in Table
    try {
        JsBarcode(".barcode-mini").init();
    } catch(e) { console.warn("Barcode init error", e); }

    // Scanner Friendly: Move to next field on Enter in Barcode Input
    const assetBarcodeInput = document.getElementById('assetBarcode');
    if(assetBarcodeInput) {
        assetBarcodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('assetBrand').focus();
            }
        });
    }
    
    // --- PRINT BARCODE FUNCTIONS ---
    function updatePrintBarcodePreview() {
        const container = document.getElementById('barcodePreviewContainer');
        if(!container) return;

        const category = document.getElementById('filterCategory')?.value || '';
        const statusFilter = document.querySelector('input[name="statusFilter"]:checked')?.value || 'available';
        const columnsPerRow = 3; // Fixed 3 columns per row

        // Filter assets
        const assets = <?= json_encode($assets) ?>;
        let filteredAssets = assets.filter(a => {
            let match = true;
            if(category) match = match && (a.category === category);
            if(statusFilter === 'available') match = match && (a.status === 'available');
            return match;
        });

        // Update stats
        document.getElementById('totalItems').textContent = filteredAssets.length;
        const itemsPerPage = columnsPerRow * 10; // Assume 10 rows per A4 page
        document.getElementById('totalPages').textContent = Math.ceil(filteredAssets.length / itemsPerPage);

        // Create A4 pages
        container.innerHTML = '';
        let currentPage = [];
        
        filteredAssets.forEach((asset, index) => {
            if(currentPage.length > 0 && currentPage.length % itemsPerPage === 0) {
                createBarcodeA4Page(container, currentPage, columnsPerRow);
                currentPage = [];
            }
            currentPage.push(asset);
        });

        // Create last page
        if(currentPage.length > 0) {
            createBarcodeA4Page(container, currentPage, columnsPerRow);
        }

        // Initialize barcodes
        setTimeout(() => {
            try {
                JsBarcode(".barcode-code").init();
            } catch(e) {
                console.error("Barcode init error:", e);
            }
        }, 200);
    }

    function createBarcodeA4Page(container, assets, columnsPerRow) {
        const page = document.createElement('div');
        page.className = 'a4-page';
        
        const grid = document.createElement('div');
        grid.className = 'barcode-grid';
        grid.style.gridTemplateColumns = 'repeat(3, 1fr)';

        // Get current date in DD/MM/YY format
        const today = new Date();
        const dateStr = String(today.getDate()).padStart(2, '0') + '/' + 
                       String(today.getMonth() + 1).padStart(2, '0') + '/' + 
                       String(today.getFullYear()).slice(-2);

        assets.forEach(asset => {
            const item = document.createElement('div');
            item.className = 'barcode-item';
            
            // Label: Brand
            const brandLabel = document.createElement('div');
            brandLabel.className = 'barcode-label';
            brandLabel.textContent = asset.brand;
            
            // Label: Type/Model
            const typeLabel = document.createElement('div');
            typeLabel.className = 'barcode-label';
            typeLabel.textContent = asset.model;
            
            // Barcode SVG
            const barcodeSvg = document.createElement('svg');
            barcodeSvg.className = 'barcode-code';
            barcodeSvg.setAttribute('jsbarcode-value', asset.serial_number);
            barcodeSvg.setAttribute('jsbarcode-format', 'CODE128');
            barcodeSvg.setAttribute('jsbarcode-width', '2');
            barcodeSvg.setAttribute('jsbarcode-height', '50');
            barcodeSvg.setAttribute('jsbarcode-displayValue', 'false');
            barcodeSvg.setAttribute('jsbarcode-margin', '2');
            barcodeSvg.setAttribute('width', '100%');
            barcodeSvg.setAttribute('height', 'auto');
            barcodeSvg.style.maxWidth = '100%';
            barcodeSvg.style.height = 'auto';
            
            // Print date: bottom right
            const dateDiv = document.createElement('div');
            dateDiv.className = 'barcode-date';
            dateDiv.textContent = dateStr;
            
            item.appendChild(brandLabel);
            item.appendChild(typeLabel);
            item.appendChild(barcodeSvg);
            item.appendChild(dateDiv);
            grid.appendChild(item);
        });

        page.appendChild(grid);
        container.appendChild(page);
    }

    function printBarcode() {
        const container = document.getElementById('barcodePreviewContainer');
        if(!container || container.children.length === 0) {
            alert('⚠️ Tidak ada barang untuk dicetak. Silakan update preview terlebih dahulu.');
            return;
        }

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Cetak Barcode</title>
                <style>
                    body { margin: 0; padding: 0; background: white; }
                    .a4-page {
                        width: 210mm;
                        height: 297mm;
                        padding: 10mm;
                        page-break-after: always;
                        position: relative;
                    }
                    .barcode-grid {
                        display: grid;
                        gap: 8px;
                        grid-template-columns: repeat(3, 1fr);
                    }
                    .barcode-item {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        padding: 6px;
                        border: 1px solid #e0e0e0;
                        border-radius: 4px;
                        text-align: center;
                    }
                    .barcode-item svg { max-width: 90%; height: auto; margin: 4px 0; display: block; }
                    .barcode-code { max-width: 100% !important; height: auto !important; width: 100% !important; display: block !important; }
                    .barcode-label { font-size: 12px; font-weight: 600; color: #333; line-height: 1.2; margin: 2px 0; }
                    .barcode-date { font-size: 7px; color: #666; text-align: right; margin-top: 4px; }
                    @media print { body { margin: 0; padding: 0; } }
                </style>
            </head>
            <body>
                <div id="printContent">${container.innerHTML}</div>
                <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>
                <script>
                    setTimeout(() => { JsBarcode(".barcode-code").init(); }, 200);
                    setTimeout(() => { window.print(); }, 500);
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }

    function exportBarcodePDF() {
        const container = document.getElementById('barcodePreviewContainer');
        if(!container || container.children.length === 0) {
            alert('⚠️ Tidak ada barang untuk di-export. Silakan update preview terlebih dahulu.');
            return;
        }

        alert('📥 Fitur Export PDF akan dijalankan. Sistem akan membuka dialog cetak dengan format PDF.\n\nTips: Pilih "Simpan sebagai PDF" di dialog cetak.');
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Barcode - Export PDF</title>
                <style>
                    body { margin: 0; padding: 0; background: white; }
                    .a4-page {
                        width: 210mm;
                        height: 297mm;
                        padding: 10mm;
                        page-break-after: always;
                    }
                    .barcode-grid {
                        display: grid;
                        gap: 8px;
                        grid-template-columns: repeat(3, 1fr);
                    }
                    .barcode-item {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        padding: 6px;
                        border: 1px solid #e0e0e0;
                        border-radius: 4px;
                        text-align: center;
                    }
                    .barcode-item svg { max-width: 90%; height: auto; margin: 4px 0; display: block; }
                    .barcode-code { max-width: 100% !important; height: auto !important; width: 100% !important; display: block !important; }
                    .barcode-label { font-size: 12px; font-weight: 600; color: #333; line-height: 1.2; margin: 2px 0; }
                    .barcode-date { font-size: 7px; color: #666; text-align: right; margin-top: 4px; }
                    @media print { 
                        body { margin: 0; padding: 0; } 
                        @page { margin: 0; }
                    }
                </style>
            </head>
            <body>
                <div id="printContent">${container.innerHTML}</div>
                <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>
                <script>
                    setTimeout(() => { JsBarcode(".barcode-code").init(); }, 200);
                    setTimeout(() => { 
                        window.print(); 
                    }, 500);
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }

    // Initialize barcode preview on page load if in print_barcode view
    window.addEventListener('load', function() {
        const container = document.getElementById('barcodePreviewContainer');
        if(container && container.children.length === 0) {
            updatePrintBarcodePreview();
        }

        // Load running text settings if on settings page
        const runningTextForm = document.getElementById('runningTextForm');
        if (runningTextForm) {
            loadCurrentRunningText();
        }
    });

    // --- RUNNING TEXT FUNCTIONS ---
    function loadCurrentRunningText() {
        const textInput = document.getElementById('runningTextInput');
        const speedInput = document.getElementById('animationSpeed');
        const speedValue = document.getElementById('animationSpeedValue');
        const bgColorInput = document.getElementById('bgColor');
        const bgColorText = document.getElementById('bgColorText');
        const bgColorEndInput = document.getElementById('bgColorEnd');
        const bgColorEndText = document.getElementById('bgColorEndText');
        const textColorInput = document.getElementById('textColor');
        const textColorText = document.getElementById('textColorText');
        const fontFamilySelect = document.getElementById('fontFamily');
        const preview = document.getElementById('settingsRunningTextPreview');
        
        if (!textInput) return; // Not on settings page

        fetch('?action=get_all_settings')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Load text
                    textInput.value = data.settings.running_text;
                    
                    // Load animation speed
                    const speed = parseInt(data.settings.animation_speed) || 20;
                    speedInput.value = speed;
                    speedValue.value = speed;
                    
                    // Load colors
                    bgColorInput.value = data.settings.bg_color || '#667eea';
                    bgColorText.value = data.settings.bg_color || '#667eea';
                    bgColorEndInput.value = data.settings.bg_color_end || '#764ba2';
                    bgColorEndText.value = data.settings.bg_color_end || '#764ba2';
                    textColorInput.value = data.settings.text_color || '#ffffff';
                    textColorText.value = data.settings.text_color || '#ffffff';
                    
                    // Load font
                    fontFamilySelect.value = data.settings.font_family || 'Arial, sans-serif';
                    
                    // Update preview
                    updateRunningTextPreview();
                }
            })
            .catch(e => console.error('Load error:', e));
    }

    function updateRunningTextPreview() {
        const textInput = document.getElementById('runningTextInput');
        const speedInput = document.getElementById('animationSpeed');
        const bgColorInput = document.getElementById('bgColor');
        const bgColorEndInput = document.getElementById('bgColorEnd');
        const textColorInput = document.getElementById('textColor');
        const fontFamilySelect = document.getElementById('fontFamily');
        const preview = document.getElementById('settingsRunningTextPreview');
        
        if (!preview) return;
        
        const text = textInput.value || 'Preview text';
        const speed = speedInput.value;
        const bgColor = bgColorInput.value;
        const bgColorEnd = bgColorEndInput.value;
        const textColor = textColorInput.value;
        const fontFamily = fontFamilySelect.value;
        
        // Update preview container
        preview.style.background = `linear-gradient(135deg, ${bgColor} 0%, ${bgColorEnd} 100%)`;
        
        // Update text element
        const textElement = preview.querySelector('.running-text');
        if (textElement) {
            textElement.textContent = text;
            textElement.style.color = textColor;
            textElement.style.fontFamily = fontFamily;
            textElement.style.animation = `scroll-left ${speed}s linear infinite`;
        }
    }

    // Sync color inputs with text displays
    document.addEventListener('input', function(e) {
        if (e.target.id === 'bgColor') {
            document.getElementById('bgColorText').value = e.target.value;
            updateRunningTextPreview();
        }
        if (e.target.id === 'bgColorEnd') {
            document.getElementById('bgColorEndText').value = e.target.value;
            updateRunningTextPreview();
        }
        if (e.target.id === 'textColor') {
            document.getElementById('textColorText').value = e.target.value;
            updateRunningTextPreview();
        }
        if (e.target.id === 'animationSpeed') {
            document.getElementById('animationSpeedValue').value = e.target.value;
            updateRunningTextPreview();
        }
        if (e.target.id === 'animationSpeedValue') {
            document.getElementById('animationSpeed').value = e.target.value;
            updateRunningTextPreview();
        }
        if (e.target.id === 'fontFamily') {
            updateRunningTextPreview();
        }
        if (e.target.id === 'runningTextInput') {
            updateRunningTextPreview();
        }
    });

    // Handle running text form submission
    const runningTextForm = document.getElementById('runningTextForm');
    if (runningTextForm) {
        runningTextForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const textInput = document.getElementById('runningTextInput');
            const speedInput = document.getElementById('animationSpeed');
            const bgColorInput = document.getElementById('bgColor');
            const bgColorEndInput = document.getElementById('bgColorEnd');
            const textColorInput = document.getElementById('textColor');
            const fontFamilySelect = document.getElementById('fontFamily');
            const alertBox = document.getElementById('runningTextAlert');
            const btn = runningTextForm.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';

            try {
                const res = await fetch('?action=set_all_settings', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        settings: {
                            running_text: textInput.value,
                            animation_speed: speedInput.value,
                            bg_color: bgColorInput.value,
                            bg_color_end: bgColorEndInput.value,
                            text_color: textColorInput.value,
                            font_family: fontFamilySelect.value
                        }
                    })
                });

                const data = await res.json();

                if (data.success) {
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert alert-success';
                    alertBox.innerHTML = `<i class="fas fa-check-circle me-2"></i> ${data.message}`;
                    
                    showNotification('✓ Pengaturan Tersimpan', 'Perubahan pengaturan running text telah disimpan', 'success', 3000);
                } else {
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert alert-danger';
                    alertBox.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i> ${data.message}`;
                }
            } catch (err) {
                alertBox.style.display = 'block';
                alertBox.className = 'alert alert-danger';
                alertBox.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i> Error: ${err.message}`;
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    }
    
    // Login Form Handler removed - using traditional form submit
</script>
</body>
</html>
