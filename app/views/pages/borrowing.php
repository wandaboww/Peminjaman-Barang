<?php
/**
 * Borrowing Page
 * Asset checkout workflow
 */

$pageTitle = 'Peminjaman';
$activePage = 'borrowing';
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
      <i class="fas fa-hand-holding-box me-2"></i>Peminjaman Aset
    </h1>
  </div>
  
  <div class="row">
    <!-- Borrowing Form -->
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <h5 class="mb-0">
            <i class="fas fa-clipboard-list me-2"></i>Form Peminjaman
          </h5>
        </div>
        <div class="card-body">
          <form id="borrowingForm" method="POST" action="" onsubmit="handleBorrowing(event)">
            
            <!-- Step 1: User Information -->
            <div class="card bg-light mb-4">
              <div class="card-header">
                <h6 class="mb-0">
                  <span class="badge badge-primary me-2">1</span>Data Peminjam
                </h6>
              </div>
              <div class="card-body">
                <div class="form-floating mb-3">
                  <input 
                    type="text" 
                    class="form-control" 
                    id="userIdentity" 
                    name="user_identity"
                    placeholder="NIP / NIS"
                    required
                    autofocus
                    autocomplete="off">
                  <label for="userIdentity">
                    <i class="fas fa-id-card me-2"></i>NIP / NIS Peminjam
                  </label>
                  <small class="form-text text-muted">Scan ID card atau masukkan NIP/NIS</small>
                </div>
                
                <!-- User Info Display -->
                <div id="userInfo" style="display: none;">
                  <div class="alert alert-info">
                    <h6 class="mb-2">
                      <i class="fas fa-check-circle me-2"></i>Data Ditemukan
                    </h6>
                    <div class="row">
                      <div class="col-md-6">
                        <strong>Nama:</strong> <span id="userName">-</span>
                      </div>
                      <div class="col-md-6">
                        <strong>Role:</strong> <span id="userRole" class="badge">-</span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div id="userNotFound" style="display: none;">
                  <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Pengguna tidak ditemukan.</strong> Periksa NIP/NIS dan coba lagi.
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Step 2: Asset Selection -->
            <div class="card bg-light mb-4">
              <div class="card-header">
                <h6 class="mb-0">
                  <span class="badge badge-primary me-2">2</span>Pilih Aset
                </h6>
              </div>
              <div class="card-body">
                <div class="form-floating mb-3">
                  <input 
                    type="text" 
                    class="form-control" 
                    id="assetSerial" 
                    name="asset_serial"
                    placeholder="Serial / Barcode"
                    autocomplete="off">
                  <label for="assetSerial">
                    <i class="fas fa-barcode me-2"></i>Serial Nomor / Barcode
                  </label>
                  <small class="form-text text-muted">Scan barcode atau masukkan nomor seri</small>
                </div>
                
                <!-- Asset Info Display -->
                <div id="assetInfo" style="display: none;">
                  <div class="alert alert-info">
                    <h6 class="mb-2">
                      <i class="fas fa-check-circle me-2"></i>Aset Ditemukan
                    </h6>
                    <div class="row mb-3">
                      <div class="col-md-6">
                        <strong>Brand:</strong> <span id="assetBrand">-</span>
                      </div>
                      <div class="col-md-6">
                        <strong>Model:</strong> <span id="assetModel">-</span>
                      </div>
                    </div>
                    <div>
                      <strong>Status:</strong> <span id="assetStatus" class="badge">-</span>
                    </div>
                  </div>
                </div>
                
                <div id="assetNotFound" style="display: none;">
                  <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Aset tidak ditemukan.</strong> Periksa nomor seri dan coba lagi.
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Step 3: Borrowing Details -->
            <div class="card bg-light mb-4">
              <div class="card-header">
                <h6 class="mb-0">
                  <span class="badge badge-primary me-2">3</span>Detail Peminjaman
                </h6>
              </div>
              <div class="card-body">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">Tanggal Peminjaman</label>
                    <input 
                      type="date" 
                      class="form-control" 
                      id="loanDate"
                      name="loan_date"
                      readonly>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Tanggal Kembali</label>
                    <input 
                      type="date" 
                      class="form-control" 
                      id="dueDate"
                      name="due_date"
                      readonly>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="notes" class="form-label">Catatan (Opsional)</label>
                  <textarea 
                    class="form-control" 
                    id="notes" 
                    name="notes"
                    rows="3"
                    placeholder="Catatan tambahan tentang peminjaman"></textarea>
                </div>
              </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-between mb-4">
              <button type="reset" class="btn btn-secondary">
                <i class="fas fa-redo me-2"></i>Reset Form
              </button>
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-check-circle me-2"></i>Proses Peminjaman
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <!-- Info Sidebar -->
    <div class="col-lg-4">
      <!-- Instructions -->
      <div class="card shadow-sm mb-3">
        <div class="card-header bg-light">
          <h6 class="mb-0">
            <i class="fas fa-info-circle me-2"></i>Petunjuk
          </h6>
        </div>
        <div class="card-body">
          <ol class="small mb-0">
            <li class="mb-2">Scan atau masukkan NIP/NIS peminjam</li>
            <li class="mb-2">Sistem akan memvalidasi kelayakan peminjam</li>
            <li class="mb-2">Scan atau masukkan nomor seri aset</li>
            <li class="mb-2">Periksa detail peminjaman</li>
            <li>Klik "Proses Peminjaman" untuk menyelesaikan</li>
          </ol>
        </div>
      </div>
      
      <!-- Recent Borrowings -->
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <h6 class="mb-0">
            <i class="fas fa-history me-2"></i>Peminjaman Terakhir
          </h6>
        </div>
        <div class="card-body p-0">
          <div class="list-group list-group-flush">
            <a href="#" class="list-group-item list-group-item-action">
              <h6 class="mb-1">Budi Santoso</h6>
              <small class="text-muted">Laptop Dell XPS</small>
              <small class="d-block text-muted">14:30 hari ini</small>
            </a>
            <a href="#" class="list-group-item list-group-item-action">
              <h6 class="mb-1">Siti Nurhaliza</h6>
              <small class="text-muted">Proyektor Epson</small>
              <small class="d-block text-muted">13:15 hari ini</small>
            </a>
            <a href="#" class="list-group-item list-group-item-action">
              <h6 class="mb-1">Ahmad Hidayat</h6>
              <small class="text-muted">Kamera DSLR Canon</small>
              <small class="d-block text-muted">Kemarin</small>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<?php include 'app/views/layouts/footer.php'; ?>

<script>
/**
 * Handle borrowing form submission
 */
function handleBorrowing(e) {
  e.preventDefault();
  
  const form = document.getElementById('borrowingForm');
  const userIdentity = document.getElementById('userIdentity').value;
  const assetSerial = document.getElementById('assetSerial').value;
  
  if (!userIdentity.trim()) {
    APP.notify.error('NIP/NIS peminjam harus diisi');
    return;
  }
  
  if (!assetSerial.trim()) {
    APP.notify.error('Nomor seri aset harus diisi');
    return;
  }
  
  // Show loading
  const submitBtn = form.querySelector('button[type="submit"]');
  APP.loading.show(submitBtn, 'Memproses...');
  
  // Simulate API call
  setTimeout(() => {
    APP.loading.hide(submitBtn);
    APP.notify.success('Peminjaman berhasil! Aset dapat diambil di ruang inventaris.');
    form.reset();
    document.getElementById('userInfo').style.display = 'none';
    document.getElementById('assetInfo').style.display = 'none';
  }, 1500);
}

// Set today's date
document.getElementById('loanDate').valueAsDate = new Date();

// Calculate due date (1 day for students, 3 days for teachers)
function calculateDueDate() {
  const today = new Date();
  const daysToAdd = 1; // Default to 1 day
  const dueDate = new Date(today.setDate(today.getDate() + daysToAdd));
  document.getElementById('dueDate').valueAsDate = dueDate;
}

calculateDueDate();
</script>
