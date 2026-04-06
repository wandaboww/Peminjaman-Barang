<?php
/**
 * Return Page
 * Asset return/checkin workflow
 */

$pageTitle = 'Pengembalian';
$activePage = 'return';
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
      <i class="fas fa-undo me-2"></i>Pengembalian Aset
    </h1>
  </div>
  
  <div class="row">
    <!-- Return Form -->
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <h5 class="mb-0">
            <i class="fas fa-clipboard-check me-2"></i>Form Pengembalian
          </h5>
        </div>
        <div class="card-body">
          <form id="returnForm" method="POST" action="" onsubmit="handleReturn(event)">
            
            <!-- Step 1: Asset Scan -->
            <div class="card bg-light mb-4">
              <div class="card-header">
                <h6 class="mb-0">
                  <span class="badge badge-primary me-2">1</span>Pindai Aset
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
                    required
                    autofocus
                    autocomplete="off">
                  <label for="assetSerial">
                    <i class="fas fa-barcode me-2"></i>Serial Nomor / Barcode
                  </label>
                  <small class="form-text text-muted">Scan barcode aset yang dikembalikan</small>
                </div>
                
                <!-- Asset Loan Info -->
                <div id="loanInfo" style="display: none;">
                  <div class="alert alert-info">
                    <h6 class="mb-3">
                      <i class="fas fa-check-circle me-2"></i>Informasi Peminjaman
                    </h6>
                    <div class="row mb-2">
                      <div class="col-md-6">
                        <strong>Aset:</strong> <span id="assetName">-</span>
                      </div>
                      <div class="col-md-6">
                        <strong>Peminjam:</strong> <span id="borrowerName">-</span>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <strong>Tanggal Peminjaman:</strong> <span id="loanDate">-</span>
                      </div>
                      <div class="col-md-6">
                        <strong>Tanggal Jatuh Tempo:</strong> <span id="dueDate">-</span>
                      </div>
                    </div>
                    <div id="overdueAlert" style="display: none;" class="mt-3">
                      <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>OVERDUE!</strong> Pengembalian terlambat <span id="daysOverdue">-</span> hari.
                      </div>
                    </div>
                  </div>
                </div>
                
                <div id="assetNotFound" style="display: none;">
                  <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Aset tidak ditemukan atau tidak sedang dipinjam.</strong>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Step 2: Condition Assessment -->
            <div class="card bg-light mb-4">
              <div class="card-header">
                <h6 class="mb-0">
                  <span class="badge badge-primary me-2">2</span>Kondisi Aset
                </h6>
              </div>
              <div class="card-body">
                <label class="form-label mb-3">Pilih kondisi aset saat dikembalikan</label>
                
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <div class="form-check">
                      <input 
                        class="form-check-input" 
                        type="radio" 
                        name="condition" 
                        id="conditionGood" 
                        value="good"
                        checked
                        required>
                      <label class="form-check-label" for="conditionGood">
                        <h6 class="mb-1">
                          <i class="fas fa-check-circle text-success me-2"></i>Baik
                        </h6>
                        <small class="text-muted">Tidak ada kerusakan</small>
                      </label>
                    </div>
                  </div>
                  
                  <div class="col-md-4 mb-3">
                    <div class="form-check">
                      <input 
                        class="form-check-input" 
                        type="radio" 
                        name="condition" 
                        id="conditionMinor" 
                        value="minor_damage">
                      <label class="form-check-label" for="conditionMinor">
                        <h6 class="mb-1">
                          <i class="fas fa-exclamation-circle text-warning me-2"></i>Kerusakan Ringan
                        </h6>
                        <small class="text-muted">Goresan/lecet kecil</small>
                      </label>
                    </div>
                  </div>
                  
                  <div class="col-md-4 mb-3">
                    <div class="form-check">
                      <input 
                        class="form-check-input" 
                        type="radio" 
                        name="condition" 
                        id="conditionMajor" 
                        value="major_damage">
                      <label class="form-check-label" for="conditionMajor">
                        <h6 class="mb-1">
                          <i class="fas fa-times-circle text-danger me-2"></i>Kerusakan Berat
                        </h6>
                        <small class="text-muted">Tidak berfungsi/rusak</small>
                      </label>
                    </div>
                  </div>
                </div>
                
                <!-- Damage Details -->
                <div id="damageDetails" style="display: none;" class="mt-3">
                  <label for="damageDescription" class="form-label">Deskripsi Kerusakan</label>
                  <textarea 
                    class="form-control" 
                    id="damageDescription" 
                    name="damage_description"
                    rows="3"
                    placeholder="Jelaskan detail kerusakan..."></textarea>
                </div>
              </div>
            </div>
            
            <!-- Step 3: Notes -->
            <div class="card bg-light mb-4">
              <div class="card-header">
                <h6 class="mb-0">
                  <span class="badge badge-primary me-2">3</span>Catatan Tambahan
                </h6>
              </div>
              <div class="card-body">
                <textarea 
                  class="form-control" 
                  id="notes" 
                  name="notes"
                  rows="3"
                  placeholder="Catatan tambahan tentang pengembalian (opsional)"></textarea>
              </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-between mb-4">
              <button type="reset" class="btn btn-secondary">
                <i class="fas fa-redo me-2"></i>Reset Form
              </button>
              <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-check-circle me-2"></i>Terima Pengembalian
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
            <li class="mb-2">Scan aset yang dikembalikan</li>
            <li class="mb-2">Verifikasi informasi peminjaman</li>
            <li class="mb-2">Pilih kondisi aset saat dikembalikan</li>
            <li class="mb-2">Tambahkan catatan jika ada kerusakan</li>
            <li>Klik "Terima Pengembalian" untuk menyelesaikan</li>
          </ol>
        </div>
      </div>
      
      <!-- Overdue Items -->
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <h6 class="mb-0">
            <i class="fas fa-clock text-warning me-2"></i>Item Overdue
          </h6>
        </div>
        <div class="card-body p-0">
          <div class="list-group list-group-flush">
            <div class="list-group-item">
              <div class="d-flex justify-content-between">
                <div>
                  <h6 class="mb-1">Kamera DSLR Canon</h6>
                  <small class="text-muted">Ahmad Hidayat</small>
                </div>
                <span class="badge badge-danger">2 hari</span>
              </div>
            </div>
            <div class="list-group-item">
              <div class="d-flex justify-content-between">
                <div>
                  <h6 class="mb-1">Speaker Portable</h6>
                  <small class="text-muted">Eka Putri</small>
                </div>
                <span class="badge badge-warning">1 hari</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<?php include 'app/views/layouts/footer.php'; ?>

<script>
/**
 * Handle return form submission
 */
function handleReturn(e) {
  e.preventDefault();
  
  const form = document.getElementById('returnForm');
  const assetSerial = document.getElementById('assetSerial').value;
  const condition = document.querySelector('input[name="condition"]:checked').value;
  
  if (!assetSerial.trim()) {
    APP.notify.error('Nomor seri aset harus diisi');
    return;
  }
  
  if (condition === 'major_damage') {
    const damageDesc = document.getElementById('damageDescription').value;
    if (!damageDesc.trim()) {
      APP.notify.warning('Harap jelaskan detail kerusakan berat');
      return;
    }
  }
  
  // Show loading
  const submitBtn = form.querySelector('button[type="submit"]');
  APP.loading.show(submitBtn, 'Memproses...');
  
  // Simulate API call
  setTimeout(() => {
    APP.loading.hide(submitBtn);
    
    let message = 'Pengembalian berhasil! Aset telah diterima.';
    if (condition !== 'good') {
      message = 'Pengembalian berhasil! Aset akan diperiksa oleh admin.';
    }
    
    APP.notify.success(message);
    form.reset();
    document.getElementById('loanInfo').style.display = 'none';
    document.getElementById('damageDetails').style.display = 'none';
  }, 1500);
}

/**
 * Show/hide damage details based on condition
 */
document.querySelectorAll('input[name="condition"]').forEach(radio => {
  radio.addEventListener('change', function() {
    const damageDetails = document.getElementById('damageDetails');
    if (this.value !== 'good') {
      damageDetails.style.display = 'block';
    } else {
      damageDetails.style.display = 'none';
    }
  });
});
</script>
