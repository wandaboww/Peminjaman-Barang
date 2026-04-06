<?php
/**
 * Card Components
 * Reusable card templates for statistics and info
 */

/**
 * Render a stat card
 * @param string $icon - Font Awesome icon class
 * @param string $title - Card title
 * @param string $value - Main value/number
 * @param string $color - Color class (primary, success, warning, danger)
 * @param string $subtitle - Optional subtitle
 */
function renderStatCard($icon, $title, $value, $color = 'primary', $subtitle = '') {
  ?>
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="d-flex align-items-center">
        <div class="flex-shrink-0 me-3">
          <div class="fs-2 text-<?php echo htmlspecialchars($color); ?>">
            <i class="fas fa-<?php echo htmlspecialchars($icon); ?>"></i>
          </div>
        </div>
        <div class="flex-grow-1">
          <h6 class="card-title mb-1 text-muted"><?php echo htmlspecialchars($title); ?></h6>
          <h3 class="mb-0"><?php echo htmlspecialchars($value); ?></h3>
          <?php if ($subtitle): ?>
            <small class="text-muted"><?php echo htmlspecialchars($subtitle); ?></small>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?php
}

/**
 * Render an info card
 * @param string $title - Card title
 * @param array $items - Info items (label => value)
 */
function renderInfoCard($title, $items = []) {
  ?>
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-light">
      <h6 class="mb-0"><?php echo htmlspecialchars($title); ?></h6>
    </div>
    <div class="card-body">
      <?php foreach ($items as $label => $value): ?>
        <div class="row mb-3">
          <div class="col-sm-4">
            <small class="text-muted"><?php echo htmlspecialchars($label); ?></small>
          </div>
          <div class="col-sm-8">
            <strong><?php echo htmlspecialchars($value); ?></strong>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php
}

/**
 * Render an action card
 * @param string $title - Card title
 * @param string $description - Card description
 * @param string $buttonText - Button text
 * @param string $buttonUrl - Button URL
 * @param string $icon - Font Awesome icon
 * @param string $color - Color class
 */
function renderActionCard($title, $description, $buttonText, $buttonUrl, $icon = 'arrow-right', $color = 'primary') {
  ?>
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <h5 class="card-title">
        <i class="fas fa-<?php echo htmlspecialchars($icon); ?> text-<?php echo htmlspecialchars($color); ?> me-2"></i>
        <?php echo htmlspecialchars($title); ?>
      </h5>
      <p class="card-text text-muted"><?php echo htmlspecialchars($description); ?></p>
      <a href="<?php echo htmlspecialchars($buttonUrl); ?>" class="btn btn-<?php echo htmlspecialchars($color); ?> btn-sm">
        <?php echo htmlspecialchars($buttonText); ?>
        <i class="fas fa-chevron-right ms-1"></i>
      </a>
    </div>
  </div>
  <?php
}
