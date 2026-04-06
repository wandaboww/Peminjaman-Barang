<?php
/**
 * Dashboard Page
 * Main dashboard with statistics and quick actions
 */

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
$publicPath = '';
?>
<?php include 'app/views/layouts/header.php'; ?>

<!-- Navbar -->
<?php include 'app/views/layouts/navbar.php'; ?>

<!-- Main Content -->
<div class="container-fluid">
  
  <!-- Page Header -->
  <div class="page-header">
    <h1 class="page-title">
      <i class="fas fa-chart-line me-2"></i>Dashboard
    </h1>
    <div class="btn-group">
      <button type="button" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-refresh me-1"></i>Refresh
      </button>
      <button type="button" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-download me-1"></i>Export
      </button>
    </div>
  </div>
  
  <!-- Statistics Row -->
  <div class="row mb-4">
    <div class="col-md-3">
      <?php include 'app/views/components/cards.php'; ?>
      <?php renderStatCard('box', 'Total Aset', '150', 'primary', '15 sedang dipinjam'); ?>
    </div>
    <div class="col-md-3">
      <?php renderStatCard('hand-holding-box', 'Peminjaman Aktif', '15', 'info', '+2 hari ini'); ?>
    </div>
    <div class="col-md-3">
      <?php renderStatCard('clock', 'Overdue', '3', 'warning', 'Perlu perhatian'); ?>
    </div>
    <div class="col-md-3">
      <?php renderStatCard('users', 'Total Pengguna', '245', 'success', '42 guru, 203 siswa'); ?>
    </div>
  </div>
  
  <!-- Main Content Row -->
  <div class="row mb-4">
    <!-- Activity Log -->
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <h6 class="mb-0">
            <i class="fas fa-history me-2"></i>Aktivitas Terbaru
          </h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead>
                <tr>
                  <th>Waktu</th>
                  <th>Tindakan</th>
                  <th>Pengguna</th>
                  <th>Aset</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><small class="text-muted">Hari ini 14:30</small></td>
                  <td><span class="badge badge-success">Peminjaman</span></td>
                  <td>Budi Santoso</td>
                  <td>Laptop Dell XPS</td>
                </tr>
                <tr>
                  <td><small class="text-muted">Hari ini 13:15</small></td>
                  <td><span class="badge badge-info">Pengembalian</span></td>
                  <td>Siti Nurhaliza</td>
                  <td>Proyektor Epson</td>
                </tr>
                <tr>
                  <td><small class="text-muted">Kemarin 16:45</small></td>
                  <td><span class="badge badge-warning">Overdue</span></td>
                  <td>Ahmad Hidayat</td>
                  <td>Kamera DSLR Canon</td>
                </tr>
                <tr>
                  <td><small class="text-muted">Kemarin 15:20</small></td>
                  <td><span class="badge badge-success">Peminjaman</span></td>
                  <td>Eka Putri</td>
                  <td>Speaker Portable</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer bg-light">
          <a href="#" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-arrow-right me-1"></i>Lihat Semua
          </a>
        </div>
      </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-lg-4">
      <div class="row mb-3">
        <div class="col-12">
          <?php renderActionCard('Peminjaman Baru', 'Mulai proses peminjaman aset', 'Mulai', '?page=borrowing', 'hand-holding-box', 'primary'); ?>
        </div>
      </div>
      
      <div class="row mb-3">
        <div class="col-12">
          <?php renderActionCard('Pengembalian Aset', 'Catat pengembalian aset', 'Mulai', '?page=return', 'undo', 'success'); ?>
        </div>
      </div>
      
      <div class="row">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-header bg-light">
              <h6 class="mb-0">
                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Perhatian
              </h6>
            </div>
            <div class="card-body">
              <div class="alert alert-warning mb-3">
                <strong>3 Peminjaman Overdue</strong>
                <p class="mb-0 mt-2 small">Ada aset yang sudah melewati batas waktu pengembalian. Segera hubungi peminjam.</p>
              </div>
              <button type="button" class="btn btn-sm btn-outline-warning w-100">
                <i class="fas fa-list me-1"></i>Lihat Daftar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Assets Status Row -->
  <div class="row">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <h6 class="mb-0">
            <i class="fas fa-chart-pie me-2"></i>Status Aset
          </h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
              <span>Tersedia</span>
              <span><strong>135</strong></span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-success" style="width: 90%"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
              <span>Dipinjam</span>
              <span><strong>15</strong></span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-info" style="width: 10%"></div>
            </div>
          </div>
          <div>
            <div class="d-flex justify-content-between mb-1">
              <span>Maintenance</span>
              <span><strong>0</strong></span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-warning" style="width: 0%"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <h6 class="mb-0">
            <i class="fas fa-clock me-2"></i>Pengembalian Terjadwal
          </h6>
        </div>
        <div class="card-body">
          <div class="list-group list-group-flush">
            <div class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-1">Laptop Dell XPS</h6>
                <small class="text-muted">Budi Santoso</small>
              </div>
              <span class="badge badge-warning">Hari ini</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-1">Proyektor Epson EB-X04</h6>
                <small class="text-muted">Siti Nurhaliza</small>
              </div>
              <span class="badge badge-info">Besok</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-1">Kamera DSLR Canon EOS</h6>
                <small class="text-muted">Ahmad Hidayat</small>
              </div>
              <span class="badge badge-danger">Overdue</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<?php include 'app/views/layouts/footer.php'; ?>
