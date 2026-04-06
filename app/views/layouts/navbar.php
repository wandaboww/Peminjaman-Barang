<?php
/**
 * Navbar Component
 * Main navigation bar with user menu
 */
?>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <!-- Brand -->
    <a class="navbar-brand" href="<?php echo isset($baseUrl) ? $baseUrl : '#'; ?>">
      <i class="fas fa-box me-2"></i>SIM-Inventaris
    </a>
    
    <!-- Toggle Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <!-- Navbar Content -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <!-- Dashboard -->
        <li class="nav-item">
          <a class="nav-link <?php echo isset($activePage) && $activePage === 'dashboard' ? 'active' : ''; ?>" 
             href="<?php echo isset($baseUrl) ? $baseUrl : '#'; ?>">
            <i class="fas fa-chart-line me-1"></i>Dashboard
          </a>
        </li>
        
        <!-- Borrowing -->
        <li class="nav-item">
          <a class="nav-link <?php echo isset($activePage) && $activePage === 'borrowing' ? 'active' : ''; ?>" 
             href="<?php echo isset($baseUrl) ? $baseUrl : '#'; ?>?page=borrowing">
            <i class="fas fa-hand-holding-box me-1"></i>Peminjaman
          </a>
        </li>
        
        <!-- Return -->
        <li class="nav-item">
          <a class="nav-link <?php echo isset($activePage) && $activePage === 'return' ? 'active' : ''; ?>" 
             href="<?php echo isset($baseUrl) ? $baseUrl : '#'; ?>?page=return">
            <i class="fas fa-undo me-1"></i>Pengembalian
          </a>
        </li>
        
        <!-- Admin Menu (if user is admin) -->
        <?php if (isset($userRole) && $userRole === 'admin'): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="adminMenu" role="button" data-bs-toggle="dropdown">
            <i class="fas fa-cog me-1"></i>Admin
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminMenu">
            <li><a class="dropdown-item" href="<?php echo isset($baseUrl) ? $baseUrl : '#'; ?>?page=users">
              <i class="fas fa-users me-2"></i>Kelola Pengguna
            </a></li>
            <li><a class="dropdown-item" href="<?php echo isset($baseUrl) ? $baseUrl : '#'; ?>?page=assets">
              <i class="fas fa-boxes me-2"></i>Kelola Aset
            </a></li>
            <li><a class="dropdown-item" href="<?php echo isset($baseUrl) ? $baseUrl : '#'; ?>?page=loans">
              <i class="fas fa-list me-2"></i>Riwayat Peminjaman
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?php echo isset($baseUrl) ? $baseUrl : '#'; ?>?page=activity">
              <i class="fas fa-history me-2"></i>Log Aktivitas
            </a></li>
          </ul>
        </li>
        <?php endif; ?>
        
        <!-- User Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle me-1"></i><?php echo isset($userName) ? htmlspecialchars($userName) : 'Pengguna'; ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
            <li><a class="dropdown-item" href="<?php echo isset($baseUrl) ? $baseUrl : '#'; ?>?page=profile">
              <i class="fas fa-user me-2"></i>Profil
            </a></li>
            <li><a class="dropdown-item" href="<?php echo isset($baseUrl) ? $baseUrl : '#'; ?>?page=settings">
              <i class="fas fa-sliders-h me-2"></i>Pengaturan
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?php echo isset($logoutUrl) ? $logoutUrl : '#'; ?>">
              <i class="fas fa-sign-out-alt me-2"></i>Keluar
            </a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
