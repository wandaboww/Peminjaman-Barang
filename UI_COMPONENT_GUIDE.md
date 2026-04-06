# SIM-Inventaris - UI Component System

## Overview

Option B implementation creates a modern, professional front-end with reusable components and modular architecture. This document describes the new UI system and how to use the components.

## 📁 Directory Structure

```
SIM-Inventaris/
├── public/
│   ├── css/
│   │   └── style.css              # Main stylesheet with design system
│   └── js/
│       └── app.js                 # Utility functions and app logic
├── app/
│   └── views/
│       ├── layouts/               # Reusable layout templates
│       │   ├── header.php          # HTML head and meta tags
│       │   ├── navbar.php          # Main navigation bar
│       │   └── footer.php          # Footer and script loading
│       ├── pages/                 # Complete page templates
│       │   ├── login.php           # Authentication page
│       │   ├── dashboard.php       # Main dashboard
│       │   ├── borrowing.php       # Checkout workflow
│       │   └── return.php          # Return/checkin workflow
│       └── components/            # Reusable components
│           ├── modals.php         # Modal templates
│           └── cards.php          # Card components
```

## 🎨 Design System

### Color Palette

| Variable | Hex | Usage |
|----------|-----|-------|
| --primary-color | #2563eb | Main brand color, buttons, links |
| --secondary-color | #64748b | Secondary actions, meta text |
| --success-color | #10b981 | Confirmations, availability |
| --warning-color | #f59e0b | Alerts, pending actions |
| --danger-color | #ef4444 | Errors, overdue items |
| --info-color | #3b82f6 | Information, notifications |

### Typography

- **Font Family**: Inter (fallback: system fonts)
- **Body Text**: 16px, line-height 1.6
- **Headings**: Font-weight 600, reduced line-height
- **Sizes**: h1(2.25rem) → h6(1rem)

### Spacing & Dimensions

- **Border Radius**: 8-12px (smooth, modern)
- **Box Shadows**: Subtle (0 1px 3px) to prominent (0 10px 25px)
- **Padding**: 1rem-2rem for cards and containers
- **Transitions**: 0.3s ease for all interactive elements

## 📦 CSS Classes

### Buttons

```html
<!-- Primary Button -->
<button class="btn btn-primary">Action</button>

<!-- Sizes -->
<button class="btn btn-primary btn-lg">Large</button>
<button class="btn btn-primary">Normal</button>
<button class="btn btn-primary btn-sm">Small</button>

<!-- Variants -->
<button class="btn btn-success">Success</button>
<button class="btn btn-danger">Danger</button>
<button class="btn btn-warning">Warning</button>
```

### Cards

```html
<div class="card">
  <div class="card-header">Title</div>
  <div class="card-body">Content</div>
  <div class="card-footer">Footer</div>
</div>
```

### Forms

```html
<div class="form-floating mb-3">
  <input type="text" class="form-control" id="name" placeholder="Name">
  <label for="name">Name</label>
</div>

<select class="form-select">
  <option>Choose...</option>
</select>
```

### Alerts

```html
<!-- Success -->
<div class="alert alert-success">Success message</div>

<!-- Error -->
<div class="alert alert-danger">Error message</div>

<!-- Warning -->
<div class="alert alert-warning">Warning message</div>

<!-- Info -->
<div class="alert alert-info">Info message</div>
```

### Badges

```html
<span class="badge badge-primary">Primary</span>
<span class="badge badge-success">Success</span>
<span class="badge badge-warning">Warning</span>
<span class="badge badge-danger">Danger</span>
```

## 🔧 JavaScript Utilities

The `public/js/app.js` file provides utility functions accessible via the `APP` namespace.

### Notifications

```javascript
// Success notification (auto-dismisses in 3s)
APP.notify.success('Item saved successfully!');

// Error notification
APP.notify.error('Something went wrong!', 5000);

// Warning notification
APP.notify.warning('Please review before proceeding');

// Info notification
APP.notify.info('New updates available');
```

### Modals

```javascript
// Confirmation dialog
APP.modal.confirm(
  'Delete Item?',
  'This action cannot be undone.',
  () => {
    // Callback when confirmed
    console.log('Deleted!');
  },
  'Delete',  // confirm button text
  'Cancel'   // cancel button text
);
```

### Form Utilities

```javascript
// Validate form
const form = document.getElementById('myForm');
if (APP.form.validate(form)) {
  // Form is valid
}

// Get form data as object
const data = APP.form.getData(form);
console.log(data); // { field1: 'value1', field2: 'value2' }

// Disable/enable form
APP.form.setDisabled(form, true);

// Clear validation state
APP.form.clear(form);
```

### API Helpers

```javascript
// GET request
APP.api.fetch('/api/assets')
  .then(data => console.log(data))
  .catch(error => console.error(error));

// POST request
APP.api.post('/api/loans', { user_id: 1, asset_id: 5 })
  .then(data => APP.notify.success('Loan created!'))
  .catch(error => APP.notify.error(error.message));

// PUT request
APP.api.put('/api/loans/1', { status: 'returned' });

// DELETE request
APP.api.delete('/api/loans/1');
```

### Loading States

```javascript
const btn = document.getElementById('submitBtn');

// Show loading
APP.loading.show(btn, 'Processing...');

// Later...
APP.loading.hide(btn);
```

### Utility Functions

```javascript
// Format date
APP.utils.formatDate(new Date(), 'YYYY-MM-DD');
// Output: "2024-01-15"

// Format currency
APP.utils.formatCurrency(150000, 'IDR');
// Output: "Rp. 150.000,00"

// Check if element is in viewport
if (APP.utils.isInViewport(element)) {
  // Element is visible
}

// Deep copy
const copy = APP.utils.deepCopy(originalObj);

// Debounce
const debouncedSearch = APP.utils.debounce((query) => {
  // Search logic
}, 300);

// Throttle
const throttledScroll = APP.utils.throttle(() => {
  // Scroll logic
}, 300);
```

## 🧩 Component Functions

### Modal Components (app/views/components/modals.php)

#### renderConfirmModal()

```php
<?php
renderConfirmModal(
  'deleteModal',           // Modal ID
  'Delete Item?',          // Title
  'Are you sure?',         // Message
  'Delete',                // Confirm button text
  'btn-danger',            // Confirm button class
  'Cancel'                 // Cancel button text
);
?>
```

#### renderFormModal()

```php
<?php
$fields = [
  [
    'name' => 'title',
    'label' => 'Asset Title',
    'type' => 'text',
    'placeholder' => 'Enter title',
    'required' => true
  ],
  [
    'name' => 'category',
    'label' => 'Category',
    'type' => 'select',
    'options' => ['laptop' => 'Laptop', 'printer' => 'Printer'],
    'required' => true
  ],
  [
    'name' => 'notes',
    'label' => 'Notes',
    'type' => 'textarea',
    'placeholder' => 'Additional notes'
  ]
];

renderFormModal(
  'assetModal',            // Modal ID
  'Add Asset',             // Title
  $fields,                 // Form fields
  'Save Asset',            // Submit button text
  'assets/store'           // Form action
);
?>
```

#### renderAlert()

```php
<?php
renderAlert('success', 'Operation completed!', true);
renderAlert('danger', 'Something went wrong!', true);
renderAlert('warning', 'Please review this!', false);
?>
```

### Card Components (app/views/components/cards.php)

#### renderStatCard()

```php
<?php
renderStatCard(
  'box',                   // Font Awesome icon
  'Total Assets',          // Title
  '150',                   // Value
  'primary',               // Color (primary, success, warning, danger)
  '5 borrowed'             // Subtitle (optional)
);
?>
```

#### renderInfoCard()

```php
<?php
$items = [
  'Serial Number' => 'LAP-001-2024',
  'Brand' => 'Dell',
  'Model' => 'XPS 13',
  'Status' => 'Available'
];

renderInfoCard('Asset Details', $items);
?>
```

#### renderActionCard()

```php
<?php
renderActionCard(
  'Start Borrowing',       // Title
  'Begin asset checkout process',  // Description
  'Continue',              // Button text
  '?page=borrowing',       // Button URL
  'hand-holding-box',      // Icon
  'primary'                // Color
);
?>
```

## 📱 Responsive Design

The design system is mobile-first with breakpoints:

- **xs**: < 576px (mobile)
- **sm**: ≥ 576px (landscape phone)
- **md**: ≥ 768px (tablet)
- **lg**: ≥ 992px (desktop)
- **xl**: ≥ 1200px (large desktop)

### Bootstrap Grid Usage

```html
<div class="row">
  <!-- Full width on mobile, half on tablet, third on desktop -->
  <div class="col-12 col-md-6 col-lg-4"></div>
</div>
```

## 🔌 Integration Guide

### Using Layouts

Every page should include header, navbar, and footer:

```php
<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
?>
<?php include 'app/views/layouts/header.php'; ?>
<?php include 'app/views/layouts/navbar.php'; ?>

<!-- Your page content here -->

<?php include 'app/views/layouts/footer.php'; ?>
```

### Using Components

Components are included as needed:

```php
<?php include 'app/views/components/modals.php'; ?>
<?php include 'app/views/components/cards.php'; ?>

<!-- Use component functions -->
<?php renderStatCard('box', 'Assets', '150', 'primary'); ?>
```

## 🎯 Best Practices

1. **Always escape user input** to prevent XSS:
   ```php
   <?php echo htmlspecialchars($userInput); ?>
   ```

2. **Use semantic HTML** for accessibility:
   ```html
   <button type="button" class="btn">...</button>
   <label for="name">Name</label>
   <input id="name">
   ```

3. **Leverage Bootstrap utilities** instead of custom CSS:
   ```html
   <!-- Good -->
   <div class="mb-3 mt-4 p-2">...</div>
   
   <!-- Avoid -->
   <div style="margin-bottom: 3px; margin-top: 4px; padding: 2px;">...</div>
   ```

4. **Use the APP namespace** for JavaScript:
   ```javascript
   // Good
   APP.notify.success('Done!');
   
   // Avoid
   alert('Done!');
   ```

5. **Animate with CSS transitions**:
   ```html
   <div class="animate-fade-in">Appears smoothly</div>
   <div class="animate-slide-in">Slides in</div>
   ```

## 🚀 Testing

### Test Locally

```bash
# Windows
run-local-server.bat

# Linux/Mac
php -S 127.0.0.1:8000
```

Visit: http://127.0.0.1:8000

### Test Pages

1. **Login** - Verify demo credentials work
2. **Dashboard** - Check all stat cards and tables
3. **Borrowing** - Form submission and validation
4. **Return** - Asset scan and condition selection

### Responsive Testing

- Use Chrome DevTools (F12) → Device Emulation
- Test on actual mobile devices
- Verify touch interactions

## 📚 Further Development

### Creating New Pages

1. Create file in `app/views/pages/`
2. Include header, navbar, content, footer
3. Use component functions for consistency
4. Test in browser and across devices

### Adding New Components

1. Add function to `app/views/components/*.php`
2. Document parameters and usage
3. Create example in a test page
4. Add to this README

### Extending Styles

Modify `public/css/style.css`:
- Update CSS variables for color/sizing changes
- Add new utility classes as needed
- Maintain mobile-first approach
- Keep specificity low

## 📞 Support

For questions or issues:
1. Check the code comments
2. Review similar existing components
3. Test in browser console (F12)
4. Check activity_logs.json for server errors
