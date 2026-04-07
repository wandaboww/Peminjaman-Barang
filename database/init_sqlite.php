<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$baseSqlitePath = __DIR__ . DIRECTORY_SEPARATOR . 'sim_inventaris.sqlite';
$jsonDatabasePath = $projectRoot . DIRECTORY_SEPARATOR . 'database.json';
$jsonLogsPath = $projectRoot . DIRECTORY_SEPARATOR . 'activity_logs.json';

$sqlitePath = $baseSqlitePath;
if (file_exists($baseSqlitePath)) {
    $canReplaceBaseFile = @unlink($baseSqlitePath);
    if (!$canReplaceBaseFile) {
        $sqlitePath = __DIR__ . DIRECTORY_SEPARATOR . 'sim_inventaris_' . date('Ymd_His') . '.sqlite';
    }
}
$sqliteJournalPath = $sqlitePath . '-journal';
if (file_exists($sqliteJournalPath)) {
    @unlink($sqliteJournalPath);
}

$pdo = new PDO('sqlite:' . $sqlitePath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = ON');
$pdo->exec('PRAGMA journal_mode = DELETE');

$schemaStatements = [
<<<SQL
CREATE TABLE users (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    identity_number TEXT NOT NULL UNIQUE,
    role TEXT NOT NULL,
    kelas TEXT DEFAULT '-',
    email TEXT,
    phone TEXT
);
SQL,
<<<SQL
CREATE TABLE assets (
    id INTEGER PRIMARY KEY,
    brand TEXT NOT NULL,
    model TEXT NOT NULL,
    serial_number TEXT NOT NULL UNIQUE,
    category TEXT,
    barcode TEXT UNIQUE,
    status TEXT NOT NULL DEFAULT 'available'
);
SQL,
<<<SQL
CREATE TABLE loans (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    asset_id INTEGER NOT NULL,
    loan_date TEXT NOT NULL,
    due_date TEXT NOT NULL,
    status TEXT NOT NULL,
    return_date TEXT,
    return_condition TEXT,
    return_notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
);
SQL,
<<<SQL
CREATE TABLE settings (
    setting_key TEXT PRIMARY KEY,
    setting_value TEXT NOT NULL
);
SQL,
<<<SQL
CREATE TABLE activity_logs (
    id INTEGER PRIMARY KEY,
    timestamp TEXT NOT NULL,
    action TEXT NOT NULL,
    table_name TEXT NOT NULL,
    data TEXT NOT NULL,
    details TEXT,
    user_agent TEXT
);
SQL,
'CREATE INDEX idx_users_identity_number ON users(identity_number);',
'CREATE INDEX idx_assets_serial_number ON assets(serial_number);',
'CREATE INDEX idx_assets_barcode ON assets(barcode);',
'CREATE INDEX idx_loans_user_id ON loans(user_id);',
'CREATE INDEX idx_loans_asset_id ON loans(asset_id);',
'CREATE INDEX idx_loans_status ON loans(status);',
'CREATE INDEX idx_activity_logs_timestamp ON activity_logs(timestamp);',
<<<SQL
CREATE VIEW loan_details AS
SELECT
    l.id,
    l.loan_date,
    l.due_date,
    l.return_date,
    l.status,
    l.return_condition,
    l.return_notes,
    u.id AS user_id,
    u.name AS user_name,
    u.identity_number,
    u.role,
    u.kelas,
    a.id AS asset_id,
    a.brand,
    a.model,
    a.serial_number,
    a.category,
    a.barcode
FROM loans l
JOIN users u ON u.id = l.user_id
JOIN assets a ON a.id = l.asset_id;
SQL,
];

foreach ($schemaStatements as $statement) {
    $pdo->exec($statement);
}

function loadJsonFile(string $path, mixed $default): mixed
{
    if (!file_exists($path)) {
        return $default;
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new RuntimeException("Invalid JSON in {$path}: " . json_last_error_msg());
    }

    return $decoded ?? $default;
}

$defaultDatabase = [
    'users' => [
        ['id' => 1, 'name' => 'Pak Budi (Guru)', 'identity_number' => '19800101', 'role' => 'teacher', 'kelas' => '-'],
        ['id' => 2, 'name' => 'Ani (Siswa)', 'identity_number' => '2024001', 'role' => 'student', 'kelas' => '10 PPLG 1'],
        ['id' => 3, 'name' => 'Budi (Siswa Nakal)', 'identity_number' => '2024002', 'role' => 'student', 'kelas' => '10 PPLG 2'],
    ],
    'assets' => [
        ['id' => 1, 'brand' => 'Lenovo', 'model' => 'ThinkPad X1', 'serial_number' => 'LNV-001', 'category' => 'Laptop', 'barcode' => 'LNV-001', 'status' => 'available'],
        ['id' => 2, 'brand' => 'Epson', 'model' => 'Projector EB-X', 'serial_number' => 'EPS-001', 'category' => 'Proyektor', 'barcode' => 'EPS-001', 'status' => 'available'],
        ['id' => 3, 'brand' => 'Logitech', 'model' => 'Mouse Wireless', 'serial_number' => 'LOG-001', 'category' => 'Aksesoris', 'barcode' => 'LOG-001', 'status' => 'maintenance'],
    ],
    'loans' => [],
    'settings' => [
        'running_text' => 'Selamat datang di Sistem Manajemen Aset Sekolah',
        'animation_speed' => '20',
        'bg_color' => '#667eea',
        'bg_color_end' => '#764ba2',
        'text_color' => '#ffffff',
        'font_family' => 'Arial, sans-serif',
    ],
];

$database = loadJsonFile($jsonDatabasePath, $defaultDatabase);
$activityLogs = loadJsonFile($jsonLogsPath, []);

$insertUser = $pdo->prepare(
    'INSERT INTO users (id, name, identity_number, role, kelas, email, phone) VALUES (:id, :name, :identity_number, :role, :kelas, :email, :phone)'
);
$insertAsset = $pdo->prepare(
    'INSERT INTO assets (id, brand, model, serial_number, category, barcode, status) VALUES (:id, :brand, :model, :serial_number, :category, :barcode, :status)'
);
$insertLoan = $pdo->prepare(
    'INSERT INTO loans (id, user_id, asset_id, loan_date, due_date, status, return_date, return_condition, return_notes)
     VALUES (:id, :user_id, :asset_id, :loan_date, :due_date, :status, :return_date, :return_condition, :return_notes)'
);
$insertSetting = $pdo->prepare(
    'INSERT INTO settings (setting_key, setting_value) VALUES (:setting_key, :setting_value)'
);
$insertLog = $pdo->prepare(
    'INSERT INTO activity_logs (id, timestamp, action, table_name, data, details, user_agent)
     VALUES (:id, :timestamp, :action, :table_name, :data, :details, :user_agent)'
);

$pdo->beginTransaction();

foreach (($database['users'] ?? []) as $user) {
    $insertUser->execute([
        ':id' => (int) ($user['id'] ?? 0),
        ':name' => (string) ($user['name'] ?? ''),
        ':identity_number' => (string) ($user['identity_number'] ?? ''),
        ':role' => (string) ($user['role'] ?? 'student'),
        ':kelas' => (string) ($user['kelas'] ?? '-'),
        ':email' => isset($user['email']) ? (string) $user['email'] : null,
        ':phone' => isset($user['phone']) ? (string) $user['phone'] : null,
    ]);
}

foreach (($database['assets'] ?? []) as $asset) {
    $insertAsset->execute([
        ':id' => (int) ($asset['id'] ?? 0),
        ':brand' => (string) ($asset['brand'] ?? ''),
        ':model' => (string) ($asset['model'] ?? ''),
        ':serial_number' => (string) ($asset['serial_number'] ?? ''),
        ':category' => isset($asset['category']) ? (string) $asset['category'] : null,
        ':barcode' => isset($asset['barcode']) ? (string) $asset['barcode'] : null,
        ':status' => (string) ($asset['status'] ?? 'available'),
    ]);
}

foreach (($database['loans'] ?? []) as $loan) {
    $insertLoan->execute([
        ':id' => (int) ($loan['id'] ?? 0),
        ':user_id' => (int) ($loan['user_id'] ?? 0),
        ':asset_id' => (int) ($loan['asset_id'] ?? 0),
        ':loan_date' => (string) ($loan['loan_date'] ?? ''),
        ':due_date' => (string) ($loan['due_date'] ?? ''),
        ':status' => (string) ($loan['status'] ?? 'active'),
        ':return_date' => isset($loan['return_date']) ? (string) $loan['return_date'] : null,
        ':return_condition' => isset($loan['return_condition']) ? (string) $loan['return_condition'] : null,
        ':return_notes' => isset($loan['return_notes']) ? (string) $loan['return_notes'] : null,
    ]);
}

foreach (($database['settings'] ?? []) as $key => $value) {
    $insertSetting->execute([
        ':setting_key' => (string) $key,
        ':setting_value' => is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE),
    ]);
}

foreach ($activityLogs as $log) {
    $insertLog->execute([
        ':id' => (int) ($log['id'] ?? 0),
        ':timestamp' => (string) ($log['timestamp'] ?? ''),
        ':action' => (string) ($log['action'] ?? ''),
        ':table_name' => (string) ($log['table'] ?? ''),
        ':data' => (string) ($log['data'] ?? ''),
        ':details' => isset($log['details']) ? (string) $log['details'] : null,
        ':user_agent' => isset($log['user_agent']) ? (string) $log['user_agent'] : null,
    ]);
}

$pdo->commit();

$counts = [
    'users' => (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'assets' => (int) $pdo->query('SELECT COUNT(*) FROM assets')->fetchColumn(),
    'loans' => (int) $pdo->query('SELECT COUNT(*) FROM loans')->fetchColumn(),
    'settings' => (int) $pdo->query('SELECT COUNT(*) FROM settings')->fetchColumn(),
    'activity_logs' => (int) $pdo->query('SELECT COUNT(*) FROM activity_logs')->fetchColumn(),
];

echo "SQLite database created: {$sqlitePath}" . PHP_EOL;
foreach ($counts as $table => $count) {
    echo strtoupper($table) . ': ' . $count . PHP_EOL;
}
