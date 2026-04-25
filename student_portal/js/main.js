/**
 * StudentPortal — JavaScript Utilities
 * Handles form validation, UI interactions, animations
 */

// ─────────────────────────────────────────────
// UTILITY HELPERS
// ─────────────────────────────────────────────

/**
 * Show an inline field error message
 */
function showFieldError(inputEl, message) {
  inputEl.classList.add('error');
  inputEl.classList.remove('success');

  let errorEl = inputEl.closest('.form-group')?.querySelector('.field-error');
  if (errorEl) {
    errorEl.textContent = message;
    errorEl.classList.add('visible');
  }
}

/**
 * Clear an inline field error
 */
function clearFieldError(inputEl) {
  inputEl.classList.remove('error');
  inputEl.classList.add('success');

  let errorEl = inputEl.closest('.form-group')?.querySelector('.field-error');
  if (errorEl) {
    errorEl.classList.remove('visible');
    errorEl.textContent = '';
  }
}

/**
 * Reset all field states in a form
 */
function resetFormState(formEl) {
  formEl.querySelectorAll('.form-input').forEach(input => {
    input.classList.remove('error', 'success');
  });
  formEl.querySelectorAll('.field-error').forEach(el => {
    el.classList.remove('visible');
    el.textContent = '';
  });
}

/**
 * Set button loading state
 */
function setButtonLoading(btn, loading) {
  if (loading) {
    btn.classList.add('loading');
    btn.disabled = true;
  } else {
    btn.classList.remove('loading');
    btn.disabled = false;
  }
}

// ─────────────────────────────────────────────
// VALIDATION RULES
// ─────────────────────────────────────────────

function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email.trim());
}

function validatePassword(password) {
  return password.length >= 6;
}

function validateName(name) {
  return name.trim().length >= 2;
}

// ─────────────────────────────────────────────
// PASSWORD TOGGLE (show/hide)
// ─────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {

  // Attach toggle to all password fields
  document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = btn.closest('.input-wrapper').querySelector('input');
      const eyeOpen = btn.querySelector('.eye-open');
      const eyeClosed = btn.querySelector('.eye-closed');

      if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.style.display = 'none';
        eyeClosed.style.display = 'block';
      } else {
        input.type = 'password';
        eyeOpen.style.display = 'block';
        eyeClosed.style.display = 'none';
      }
    });
  });

  // ─────────────────────────────────────────────
  // PASSWORD STRENGTH METER
  // ─────────────────────────────────────────────

  const passwordInput = document.querySelector('#password');
  if (passwordInput) {
    const bars = document.querySelectorAll('.strength-bar span');
    const strengthLabel = document.querySelector('.strength-label');

    passwordInput.addEventListener('input', () => {
      const val = passwordInput.value;
      let score = 0;

      if (val.length >= 6)  score++;
      if (val.length >= 10) score++;
      if (/[A-Z]/.test(val)) score++;
      if (/[0-9]/.test(val)) score++;
      if (/[^A-Za-z0-9]/.test(val)) score++;

      const levels = [
        { color: '#c0392b', label: 'Too weak' },
        { color: '#e67e22', label: 'Weak' },
        { color: '#f1c40f', label: 'Fair' },
        { color: '#27ae60', label: 'Strong' },
        { color: '#2ecc71', label: 'Very strong' },
      ];

      if (bars) {
        bars.forEach((bar, i) => {
          bar.style.background = i < score ? levels[Math.min(score - 1, 4)].color : '#e8e4de';
        });
      }

      if (strengthLabel && val.length > 0) {
        const lvl = levels[Math.min(score - 1, 4)] || levels[0];
        strengthLabel.textContent = lvl.label;
        strengthLabel.style.color = lvl.color;
      } else if (strengthLabel) {
        strengthLabel.textContent = '';
      }
    });
  }

  // ─────────────────────────────────────────────
  // REGISTER FORM VALIDATION
  // ─────────────────────────────────────────────

  const registerForm = document.querySelector('#registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
      e.preventDefault();

      resetFormState(this);

      const name     = document.querySelector('#name');
      const email    = document.querySelector('#email');
      const password = document.querySelector('#password');
      const confirm  = document.querySelector('#confirm_password');
      const course   = document.querySelector('#course');

      let valid = true;

      // Name
      if (!validateName(name.value)) {
        showFieldError(name, 'Name must be at least 2 characters.');
        valid = false;
      } else {
        clearFieldError(name);
      }

      // Email
      if (!validateEmail(email.value)) {
        showFieldError(email, 'Please enter a valid email address.');
        valid = false;
      } else {
        clearFieldError(email);
      }

      // Password
      if (!validatePassword(password.value)) {
        showFieldError(password, 'Password must be at least 6 characters.');
        valid = false;
      } else {
        clearFieldError(password);
      }

      // Confirm password
      if (password.value !== confirm.value) {
        showFieldError(confirm, 'Passwords do not match.');
        valid = false;
      } else if (confirm.value.length > 0) {
        clearFieldError(confirm);
      }

      // Course
      if (!course.value) {
        showFieldError(course, 'Please select a course.');
        valid = false;
      } else {
        clearFieldError(course);
      }

      if (valid) {
        const btn = this.querySelector('button[type="submit"]');
        setButtonLoading(btn, true);
        // Small artificial delay to show loader, then submit
        setTimeout(() => this.submit(), 600);
      }
    });
  }

  // ─────────────────────────────────────────────
  // LOGIN FORM VALIDATION
  // ─────────────────────────────────────────────

  const loginForm = document.querySelector('#loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();

      resetFormState(this);

      const email    = document.querySelector('#email');
      const password = document.querySelector('#password');

      let valid = true;

      if (!validateEmail(email.value)) {
        showFieldError(email, 'Please enter a valid email address.');
        valid = false;
      } else {
        clearFieldError(email);
      }

      if (!validatePassword(password.value)) {
        showFieldError(password, 'Password must be at least 6 characters.');
        valid = false;
      } else {
        clearFieldError(password);
      }

      if (valid) {
        const btn = this.querySelector('button[type="submit"]');
        setButtonLoading(btn, true);
        setTimeout(() => this.submit(), 600);
      }
    });
  }

  // ─────────────────────────────────────────────
  // EDIT PROFILE FORM VALIDATION
  // ─────────────────────────────────────────────

  const editForm = document.querySelector('#editProfileForm');
  if (editForm) {
    editForm.addEventListener('submit', function (e) {
      e.preventDefault();

      resetFormState(this);

      const name   = document.querySelector('#name');
      const course = document.querySelector('#course');

      let valid = true;

      if (!validateName(name.value)) {
        showFieldError(name, 'Name must be at least 2 characters.');
        valid = false;
      } else {
        clearFieldError(name);
      }

      if (!course.value) {
        showFieldError(course, 'Please select a course.');
        valid = false;
      } else {
        clearFieldError(course);
      }

      if (valid) {
        const btn = this.querySelector('button[type="submit"]');
        setButtonLoading(btn, true);
        setTimeout(() => this.submit(), 600);
      }
    });
  }

  // ─────────────────────────────────────────────
  // LIVE INPUT VALIDATION (clear errors on type)
  // ─────────────────────────────────────────────

  document.querySelectorAll('.form-input').forEach(input => {
    input.addEventListener('input', () => {
      if (input.classList.contains('error')) {
        input.classList.remove('error');
        const errorEl = input.closest('.form-group')?.querySelector('.field-error');
        if (errorEl) errorEl.classList.remove('visible');
      }
    });
  });

  // ─────────────────────────────────────────────
  // AUTO-DISMISS ALERTS after 5 seconds
  // ─────────────────────────────────────────────

  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-8px)';
      setTimeout(() => alert.remove(), 400);
    }, 5000);
  });

});
