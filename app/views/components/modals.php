<?php
/**
 * Modal Components
 * Reusable modal templates
 */

/**
 * Render a confirmation modal
 * @param string $modalId - Unique modal ID
 * @param string $title - Modal title
 * @param string $message - Modal message
 * @param string $confirmText - Confirm button text
 * @param string $confirmClass - Confirm button class
 * @param string $cancelText - Cancel button text
 */
function renderConfirmModal($modalId, $title, $message, $confirmText = 'Confirm', $confirmClass = 'btn-primary', $cancelText = 'Cancel') {
  ?>
  <div class="modal fade" id="<?php echo htmlspecialchars($modalId); ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo htmlspecialchars($title); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p><?php echo htmlspecialchars($message); ?></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo htmlspecialchars($cancelText); ?></button>
          <button type="button" class="btn <?php echo htmlspecialchars($confirmClass); ?>" id="confirmBtn<?php echo htmlspecialchars($modalId); ?>"><?php echo htmlspecialchars($confirmText); ?></button>
        </div>
      </div>
    </div>
  </div>
  <?php
}

/**
 * Render a form modal
 * @param string $modalId - Unique modal ID
 * @param string $title - Modal title
 * @param array $fields - Form fields
 * @param string $submitText - Submit button text
 * @param string $formAction - Form action URL
 */
function renderFormModal($modalId, $title, $fields = [], $submitText = 'Save', $formAction = '#') {
  ?>
  <div class="modal fade" id="<?php echo htmlspecialchars($modalId); ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo htmlspecialchars($title); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="form<?php echo htmlspecialchars($modalId); ?>" action="<?php echo htmlspecialchars($formAction); ?>" method="POST">
          <div class="modal-body">
            <?php foreach ($fields as $field): ?>
              <div class="mb-3">
                <label for="<?php echo htmlspecialchars($field['name']); ?>" class="form-label">
                  <?php echo htmlspecialchars($field['label']); ?>
                  <?php if (isset($field['required']) && $field['required']): ?>
                    <span class="text-danger">*</span>
                  <?php endif; ?>
                </label>
                
                <?php if ($field['type'] === 'textarea'): ?>
                  <textarea class="form-control" 
                           id="<?php echo htmlspecialchars($field['name']); ?>" 
                           name="<?php echo htmlspecialchars($field['name']); ?>"
                           placeholder="<?php echo htmlspecialchars($field['placeholder'] ?? ''); ?>"
                           <?php echo isset($field['required']) && $field['required'] ? 'required' : ''; ?>></textarea>
                <?php elseif ($field['type'] === 'select'): ?>
                  <select class="form-select" 
                         id="<?php echo htmlspecialchars($field['name']); ?>" 
                         name="<?php echo htmlspecialchars($field['name']); ?>"
                         <?php echo isset($field['required']) && $field['required'] ? 'required' : ''; ?>>
                    <option value="">-- Pilih --</option>
                    <?php foreach ($field['options'] as $value => $label): ?>
                      <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                  </select>
                <?php else: ?>
                  <input type="<?php echo htmlspecialchars($field['type'] ?? 'text'); ?>" 
                        class="form-control" 
                        id="<?php echo htmlspecialchars($field['name']); ?>" 
                        name="<?php echo htmlspecialchars($field['name']); ?>"
                        placeholder="<?php echo htmlspecialchars($field['placeholder'] ?? ''); ?>"
                        <?php echo isset($field['required']) && $field['required'] ? 'required' : ''; ?>>
                <?php endif; ?>
                
                <?php if (isset($field['help'])): ?>
                  <small class="form-text text-muted"><?php echo htmlspecialchars($field['help']); ?></small>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary"><?php echo htmlspecialchars($submitText); ?></button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php
}

/**
 * Render an info modal (display only)
 * @param string $modalId - Unique modal ID
 * @param string $title - Modal title
 * @param string $content - Modal content HTML
 */
function renderInfoModal($modalId, $title, $content) {
  ?>
  <div class="modal fade" id="<?php echo htmlspecialchars($modalId); ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo htmlspecialchars($title); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <?php echo $content; // Content should be pre-escaped ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>
  <?php
}

/**
 * Render an alert component
 * @param string $type - Alert type (success, danger, warning, info)
 * @param string $message - Alert message
 * @param bool $dismissible - Show close button
 */
function renderAlert($type, $message, $dismissible = true) {
  $icons = [
    'success' => 'check-circle',
    'danger' => 'exclamation-circle',
    'warning' => 'exclamation-triangle',
    'info' => 'info-circle'
  ];
  $icon = $icons[$type] ?? 'info-circle';
  ?>
  <div class="alert alert-<?php echo htmlspecialchars($type); ?> <?php echo $dismissible ? 'alert-dismissible fade show' : ''; ?>" role="alert">
    <i class="fas fa-<?php echo htmlspecialchars($icon); ?> me-2"></i>
    <?php echo htmlspecialchars($message); ?>
    <?php if ($dismissible): ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <?php endif; ?>
  </div>
  <?php
}
