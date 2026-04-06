<?php
/**
 * Footer Layout Component
 * Global footer and script loading
 */
?>
  </body>
  
  <!-- Footer -->
  <footer class="bg-light border-top mt-5 py-4">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6">
          <p class="text-muted mb-0">
            &copy; 2024 SIM-Inventaris. Sistem Manajemen Inventaris Sekolah.
          </p>
        </div>
        <div class="col-md-6 text-md-end text-muted">
          <small>Version 1.0.0 | <?php echo date('Y'); ?></small>
        </div>
      </div>
    </div>
  </footer>
  
  <!-- Bootstrap Bundle JS (includes Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Custom App JS -->
  <script src="<?php echo isset($publicPath) ? $publicPath : ''; ?>public/js/app.js"></script>
  
  <?php if (isset($customJs)): ?>
    <script src="<?php echo $customJs; ?>"></script>
  <?php endif; ?>
  
</html>
