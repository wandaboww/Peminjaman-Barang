<?php
/**
 * Header Layout Component
 * Global HTML head configuration
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="description" content="SIM-Inventaris - Sistem Manajemen Inventaris Sekolah">
  <meta name="theme-color" content="#2563eb">
  
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | SIM-Inventaris' : 'SIM-Inventaris'; ?></title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- JsBarcode -->
  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?php echo isset($publicPath) ? $publicPath : ''; ?>public/css/style.css">
  
  <?php if (isset($customCss)): ?>
    <link rel="stylesheet" href="<?php echo $customCss; ?>">
  <?php endif; ?>
</head>
<body>
