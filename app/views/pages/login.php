<?php
/**
 * Login Page
 * User authentication interface
 */
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="description" content="SIM-Inventaris - Login">
  <title>Login | SIM-Inventaris</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="public/css/style.css">
  
  <style>
    body {
      background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .login-container {
      width: 100%;
      max-width: 420px;
    }
    
    .login-card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .login-header {
      background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
      color: white;
      padding: 2rem;
      text-align: center;
      border-radius: 12px 12px 0 0;
    }
    
    .login-header .logo {
      font-size: 3rem;
      margin-bottom: 1rem;
    }
    
    .login-header h1 {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 0.25rem;
    }
    
    .login-header p {
      color: rgba(255, 255, 255, 0.9);
      margin-bottom: 0;
    }
    
    .form-floating > .form-control {
      border-radius: 8px;
      border: 1px solid #e2e8f0;
    }
    
    .form-floating > .form-control:focus {
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    
    .remember-me {
      font-size: 0.9rem;
    }
    
    .btn-login {
      padding: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.5px;
    }
    
    .forgot-password {
      text-align: center;
      margin-top: 1.5rem;
      padding-top: 1.5rem;
      border-top: 1px solid #e2e8f0;
    }
    
    .forgot-password a {
      color: #2563eb;
      text-decoration: none;
      font-size: 0.9rem;
    }
    
    .forgot-password a:hover {
      text-decoration: underline;
    }
    
    .alert {
      border-radius: 8px;
      border: none;
      margin-bottom: 1.5rem;
    }
    
    .demo-alert {
      background-color: #fef3c7;
      color: #78350f;
      border-left: 4px solid #f59e0b;
      margin-bottom: 1.5rem;
      padding: 1rem;
      border-radius: 8px;
    }
  </style>
</head>
<body>

<div class="login-container">
  <!-- Error Messages -->
  <?php if (isset($error) && !empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      <i class="fas fa-exclamation-circle me-2"></i>
      <?php echo htmlspecialchars($error); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  
  <!-- Success Messages -->
  <?php if (isset($success) && !empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show">
      <i class="fas fa-check-circle me-2"></i>
      <?php echo htmlspecialchars($success); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  
  <!-- Demo Credentials Alert -->
  <div class="demo-alert">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Demo Password:</strong> admin123
  </div>
  
  <!-- Login Card -->
  <div class="card login-card">
    <!-- Header -->
    <div class="login-header">
      <div class="logo">
        <i class="fas fa-box"></i>
      </div>
      <h1>SIM-Inventaris</h1>
      <p>Sistem Manajemen Inventaris Sekolah</p>
    </div>
    
    <!-- Login Form -->
    <div class="card-body p-4">
      <form method="POST" action="../../../prototype.php" id="loginForm">
        <input type="hidden" name="action" value="login">
        
        <!-- Password Input (Admin Login) -->
        <div class="form-floating mb-3">
          <input 
            type="password" 
            class="form-control" 
            id="password" 
            name="password" 
            placeholder="Password"
            required
            autocomplete="current-password"
            autofocus>
          <label for="password">
            <i class="fas fa-lock me-2"></i>Password
          </label>
          <small class="form-text text-muted mt-2">Masukkan password admin untuk akses</small>
        </div>
        
        <!-- Remember Me -->
        <div class="form-check remember-me mb-4">
          <input 
            class="form-check-input" 
            type="checkbox" 
            id="rememberMe" 
            name="remember">
          <label class="form-check-label" for="rememberMe">
            Ingat password saya di perangkat ini
          </label>
        </div>
        
        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100 btn-login">
          <i class="fas fa-sign-in-alt me-2"></i>Masuk
        </button>
      </form>
      
      <!-- Forgot Password Link -->
      <div class="forgot-password">
        <p class="mb-0">
          <a href="../../../prototype.php">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
          </a>
        </p>
      </div>
    </div>
  </div>
  
  <!-- Footer -->
  <div class="text-center mt-4">
    <p class="text-white mb-0">
      <small>&copy; 2024 SIM-Inventaris | Version 1.0.0</small>
    </p>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- App JS -->
<script src="public/js/app.js"></script>

</body>
</html>
