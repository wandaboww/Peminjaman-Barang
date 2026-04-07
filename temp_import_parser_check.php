<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['view'] = 'dashboard';
ob_start();
require __DIR__ . '/prototype.php';
ob_end_clean();
$userRows = parseImportSpreadsheetRows($argv[1], basename($argv[1]));
$assetRows = parseImportSpreadsheetRows($argv[2], basename($argv[2]));
echo json_encode([
    'user_rows' => $userRows,
    'asset_rows' => $assetRows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);