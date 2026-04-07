<?php
/**
 * File diagnostik - upload ke public_html/ untuk cek path server
 * HAPUS FILE INI setelah konfigurasi berhasil!
 */

echo '<style>body{font-family:monospace;padding:20px;background:#1a1a2e;color:#eee;}
code{background:#333;padding:2px 6px;border-radius:4px;color:#0ff;}
.ok{color:#0f0;} .err{color:#f55;}</style>';

echo '<h2>🔍 Diagnostik Path Server</h2>';
echo '<p><strong>Lokasi file ini:</strong> <code>' . __FILE__ . '</code></p>';
echo '<p><strong>Direktori public_html:</strong> <code>' . __DIR__ . '</code></p>';
echo '<p><strong>Parent direktori:</strong> <code>' . dirname(__DIR__) . '</code></p>';

echo '<hr><h3>Mencari prototype.php di lokasi umum:</h3><ul>';

$candidates = [
    dirname(__DIR__) . '/Barang/prototype.php',
    dirname(__DIR__) . '/barang/prototype.php',
    dirname(__DIR__) . '/app/prototype.php',
    __DIR__ . '/../Barang/prototype.php',
    '/home/' . get_current_user() . '/Barang/prototype.php',
];

foreach ($candidates as $path) {
    $exists = file_exists($path);
    $icon = $exists ? '✅' : '❌';
    $class = $exists ? 'ok' : 'err';
    echo "<li class='$class'>$icon <code>$path</code></li>";
    if ($exists) {
        echo "<li class='ok'><strong>👆 GUNAKAN PATH INI sebagai PROJECT_ROOT:</strong> <code>" . dirname($path) . "</code></li>";
    }
}

echo '</ul>';

echo '<hr><h3>Info Server:</h3>';
echo '<p>PHP Version: <code>' . PHP_VERSION . '</code></p>';
echo '<p>Current user: <code>' . get_current_user() . '</code></p>';
echo '<p>Document root: <code>' . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . '</code></p>';

// Scan parent dir
echo '<hr><h3>Isi direktori parent (' . dirname(__DIR__) . '):</h3><ul>';
$items = @scandir(dirname(__DIR__));
if ($items) {
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = dirname(__DIR__) . '/' . $item;
        $type = is_dir($fullPath) ? '📁' : '📄';
        echo "<li>$type <code>$item</code></li>";
    }
} else {
    echo '<li class="err">Tidak dapat membaca direktori (permission denied)</li>';
}
echo '</ul>';
echo '<p style="color:#f55;margin-top:20px;"><strong>⚠️ HAPUS FILE INI setelah selesai konfigurasi!</strong></p>';
