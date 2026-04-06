/* ============================================
   SIM-INVENTARIS - APP.JS
   Utility Functions & Event Handlers
   ============================================ */

(function() {
  'use strict';

  // ==================================================
  // GLOBAL APP NAMESPACE
  // ==================================================
  
  window.APP = window.APP || {};

  // ==================================================
  // NOTIFICATION SYSTEM
  // ==================================================

  APP.notify = {
    /**
     * Show success notification
     * @param {string} message - Message to display
     * @param {number} duration - Auto-hide duration (ms)
     */
    success(message, duration = 3000) {
      this._show(message, 'success', duration);
    },

    /**
     * Show error notification
     * @param {string} message - Message to display
     * @param {number} duration - Auto-hide duration (ms)
     */
    error(message, duration = 5000) {
      this._show(message, 'danger', duration);
    },

    /**
     * Show warning notification
     * @param {string} message - Message to display
     * @param {number} duration - Auto-hide duration (ms)
     */
    warning(message, duration = 4000) {
      this._show(message, 'warning', duration);
    },

    /**
     * Show info notification
     * @param {string} message - Message to display
     * @param {number} duration - Auto-hide duration (ms)
     */
    info(message, duration = 3000) {
      this._show(message, 'info', duration);
    },

    /**
     * Internal method to create and show notification
     */
    _show(message, type, duration) {
      const container = document.getElementById('notification-container') || 
                       this._createContainer();
      
      const alert = document.createElement('div');
      alert.className = `alert alert-${type} animate-slide-in`;
      alert.innerHTML = `
        <div class="d-flex align-items-center">
          <i class="fas fa-${this._getIcon(type)} me-2"></i>
          <span>${this._escapeHtml(message)}</span>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
      `;
      
      container.appendChild(alert);
      
      if (duration > 0) {
        setTimeout(() => {
          alert.classList.add('fade');
          setTimeout(() => alert.remove(), 300);
        }, duration);
      }
    },

    /**
     * Create notification container if not exists
     */
    _createContainer() {
      const container = document.createElement('div');
      container.id = 'notification-container';
      container.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
      `;
      document.body.appendChild(container);
      return container;
    },

    /**
     * Get icon for notification type
     */
    _getIcon(type) {
      const icons = {
        success: 'check-circle',
        danger: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
      };
      return icons[type] || 'info-circle';
    },

    /**
     * Escape HTML to prevent XSS
     */
    _escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }
  };

  // ==================================================
  // MODAL HELPERS
  // ==================================================

  APP.modal = {
    /**
     * Show confirmation dialog
     * @param {string} title - Dialog title
     * @param {string} message - Dialog message
     * @param {function} onConfirm - Callback when confirmed
     * @param {string} confirmText - Confirm button text
     * @param {string} cancelText - Cancel button text
     */
    confirm(title, message, onConfirm, confirmText = 'Confirm', cancelText = 'Cancel') {
      const modalId = 'confirmModal_' + Date.now();
      const modal = document.createElement('div');
      modal.className = 'modal fade';
      modal.id = modalId;
      modal.tabIndex = -1;
      modal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">${this._escapeHtml(title)}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p>${this._escapeHtml(message)}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${cancelText}</button>
              <button type="button" class="btn btn-primary" id="confirmBtn">${confirmText}</button>
            </div>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      const bsModal = new bootstrap.Modal(modal);
      
      document.getElementById('confirmBtn').addEventListener('click', function() {
        bsModal.hide();
        if (typeof onConfirm === 'function') {
          onConfirm();
        }
      });
      
      modal.addEventListener('hidden.bs.modal', function() {
        modal.remove();
      });
      
      bsModal.show();
    },

    /**
     * Escape HTML to prevent XSS
     */
    _escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }
  };

  // ==================================================
  // FORM UTILITIES
  // ==================================================

  APP.form = {
    /**
     * Validate form inputs
     * @param {HTMLFormElement} form - Form to validate
     * @returns {boolean}
     */
    validate(form) {
      if (form.checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
      return form.checkValidity();
    },

    /**
     * Clear form validation state
     * @param {HTMLFormElement} form - Form to clear
     */
    clear(form) {
      form.classList.remove('was-validated');
      form.reset();
    },

    /**
     * Disable all form inputs
     * @param {HTMLFormElement} form - Form to disable
     * @param {boolean} state - Disable state
     */
    setDisabled(form, state) {
      Array.from(form.elements).forEach(el => {
        el.disabled = state;
      });
    },

    /**
     * Get form data as object
     * @param {HTMLFormElement} form - Form to get data from
     * @returns {object}
     */
    getData(form) {
      const data = {};
      const formData = new FormData(form);
      for (let [key, value] of formData.entries()) {
        data[key] = value;
      }
      return data;
    }
  };

  // ==================================================
  // API HELPERS
  // ==================================================

  APP.api = {
    /**
     * Make API request
     * @param {string} url - Request URL
     * @param {object} options - Request options
     * @returns {Promise}
     */
    async fetch(url, options = {}) {
      const defaults = {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      };

      const config = { ...defaults, ...options };

      if (config.body && typeof config.body === 'object') {
        config.body = JSON.stringify(config.body);
      }

      try {
        const response = await fetch(url, config);
        const data = await response.json();

        if (!response.ok) {
          throw new Error(data.message || `HTTP ${response.status}`);
        }

        return data;
      } catch (error) {
        console.error('API Error:', error);
        throw error;
      }
    },

    /**
     * POST request
     * @param {string} url - Request URL
     * @param {object} data - Request body
     * @returns {Promise}
     */
    post(url, data) {
      return this.fetch(url, {
        method: 'POST',
        body: data
      });
    },

    /**
     * PUT request
     * @param {string} url - Request URL
     * @param {object} data - Request body
     * @returns {Promise}
     */
    put(url, data) {
      return this.fetch(url, {
        method: 'PUT',
        body: data
      });
    },

    /**
     * DELETE request
     * @param {string} url - Request URL
     * @returns {Promise}
     */
    delete(url) {
      return this.fetch(url, {
        method: 'DELETE'
      });
    }
  };

  // ==================================================
  // LOADING STATES
  // ==================================================

  APP.loading = {
    /**
     * Show loading indicator on button
     * @param {HTMLButtonElement} button - Button element
     * @param {string} text - Loading text
     */
    show(button, text = 'Loading...') {
      button.disabled = true;
      this._originalContent = button.innerHTML;
      button.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>${text}`;
    },

    /**
     * Hide loading indicator from button
     * @param {HTMLButtonElement} button - Button element
     */
    hide(button) {
      button.disabled = false;
      button.innerHTML = this._originalContent || button.innerHTML;
    }
  };

  // ==================================================
  // UTILITY FUNCTIONS
  // ==================================================

  APP.utils = {
    /**
     * Format date
     * @param {Date|string} date - Date to format
     * @param {string} format - Format string
     * @returns {string}
     */
    formatDate(date, format = 'YYYY-MM-DD') {
      const d = new Date(date);
      const pad = (n) => String(n).padStart(2, '0');

      const replacements = {
        'YYYY': d.getFullYear(),
        'MM': pad(d.getMonth() + 1),
        'DD': pad(d.getDate()),
        'HH': pad(d.getHours()),
        'mm': pad(d.getMinutes()),
        'ss': pad(d.getSeconds())
      };

      let result = format;
      Object.entries(replacements).forEach(([key, value]) => {
        result = result.replace(key, value);
      });
      return result;
    },

    /**
     * Format currency
     * @param {number} value - Value to format
     * @param {string} currency - Currency code
     * @returns {string}
     */
    formatCurrency(value, currency = 'IDR') {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: currency
      }).format(value);
    },

    /**
     * Check if element is in viewport
     * @param {HTMLElement} element - Element to check
     * @returns {boolean}
     */
    isInViewport(element) {
      const rect = element.getBoundingClientRect();
      return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
      );
    },

    /**
     * Deep copy object
     * @param {object} obj - Object to copy
     * @returns {object}
     */
    deepCopy(obj) {
      return JSON.parse(JSON.stringify(obj));
    },

    /**
     * Debounce function
     * @param {function} func - Function to debounce
     * @param {number} delay - Delay in ms
     * @returns {function}
     */
    debounce(func, delay = 300) {
      let timeoutId;
      return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
      };
    },

    /**
     * Throttle function
     * @param {function} func - Function to throttle
     * @param {number} limit - Limit in ms
     * @returns {function}
     */
    throttle(func, limit = 300) {
      let inThrottle;
      return function(...args) {
        if (!inThrottle) {
          func.apply(this, args);
          inThrottle = true;
          setTimeout(() => inThrottle = false, limit);
        }
      };
    }
  };

  // ==================================================
  // INITIALIZATION
  // ==================================================

  document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Initialize Bootstrap popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

    // Add Bootstrap validation
    window.validateForm = function(form) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    };
  });

})();
