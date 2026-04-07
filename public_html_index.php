<?php
/**
 * Entry Point untuk cPanel Deployment
 * 
 * File ini ditempatkan di: public_html/index.php
 * 
 * Asumsi struktur direktori di server cPanel:
 *   /home/USERNAME/Barang/        <- seluruh repo git
 *   /home/USERNAME/public_html/   <- web root (file ini ada di sini)
 *
 * Ubah path di bawah sesuai akun cPanel Anda.
 * Cek path asli dengan: echo __DIR__; di PHP
 */

// ============================================================
// KONFIGURASI PATH - SESUAIKAN DENGAN SERVER ANDA
// ============================================================
// 
// Cara cek path yang benar:
//   1. Upload file test_path.php ke public_html dengan isi:
//      <?php echo __DIR__; ?>
//   2. Buka di browser, catat hasilnya (misal: /home/wandaboww/public_html)
//   3. Ganti nilai PROJECT_ROOT di bawah sesuai path repo git Anda
//
// Contoh umum cPanel:
//   - /home/USERNAME/Barang        (jika repo ada di home/USERNAME/Barang)
//   - /home/USERNAME/repos/Barang  (jika ada di subfolder)

define('PROJECT_ROOT', dirname(__DIR__) . '/Barang');

// ============================================================
// VALIDASI PATH (hapus/comment blok ini setelah berhasil)
// ============================================================
if (!file_exists(PROJECT_ROOT . '/prototype.php')) {
    http_response_code(500);
    echo '<h2>Konfigurasi Error</h2>';
    echo '<p>File prototype.php tidak ditemukan di: <code>' . PROJECT_ROOT . '</code></p>';
    echo '<p>Edit file <code>public_html/index.php</code> dan sesuaikan nilai <code>PROJECT_ROOT</code>.</p>';
    echo '<hr>';
    echo '<p><strong>Path saat ini:</strong> ' . __DIR__ . '</p>';
    echo '<p><strong>PROJECT_ROOT yang dicari:</strong> ' . PROJECT_ROOT . '</p>';
    exit;
}

// ============================================================
// JALANKAN APLIKASI
// ============================================================
require_once PROJECT_ROOT . '/prototype.php';
