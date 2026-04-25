<?php
/**
 * StudentPortal — Register Page
 * Handles new student registration with server-side validation,
 * password hashing, and duplicate email checking.
 */

session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once 'php/config.php';

$error   = '';
$success = '';

// ── Handle POST submission ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize all inputs
    $name             = trim(htmlspecialchars($_POST['name']    ?? ''));
    $email            = trim(strtolower($_POST['email']         ?? ''));
    $password         = $_POST['password']                      ?? '';
    $confirm_password = $_POST['confirm_password']              ?? '';
    $course           = trim(htmlspecialchars($_POST['course']  ?? ''));

    // Server-side validation (JS is first layer; PHP is the reliable layer)
    if (strlen($name) < 2) {
        $error = 'Name must be at least 2 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (empty($course)) {
        $error = 'Please select a course.';
    } else {

        $db = getDB();

        // Check if email already exists (prepared statement)
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'An account with this email already exists.';
        } else {
            // Hash password using bcrypt (PHP default)
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user (prepared statement prevents SQL injection)
            $insert = $db->prepare(
                'INSERT INTO users (name, email, password, course) VALUES (?, ?, ?, ?)'
            );
            $insert->bind_param('ssss', $name, $email, $hashed, $course);

            if ($insert->execute()) {
                $success = 'Account created! You can now log in.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }

            $insert->close();
        }

        $stmt->close();
        $db->close();
    }
}

// Course list used in the select dropdown
$courses = [
    'B.Tech Computer Science',
    'B.Tech Electronics',
    'B.Tech Mechanical',
    'B.Tech Civil',
    'B.Sc Physics',
    'B.Sc Mathematics',
    'B.Sc Chemistry',
    'BCA',
    'MCA',
    'MBA',
    'B.Com',
    'B.A. English',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register — StudentPortal</title>
  <link rel="stylesheet" href="css/style.css"/>
</head>
<body>
  <div class="page-wrapper">
    <div class="card card-wide">

      <!-- Brand -->
      <a href="index.html" class="brand">
        <div class="brand-icon">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 3L22 8.5L12 14L2 8.5L12 3Z" fill="white"/>
            <path d="M6 11V17C6 17 8.5 20 12 20C15.5 20 18 17 18 17V11" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <span class="brand-name">StudentPortal</span>
      </a>

      <!-- Header -->
      <div class="card-header">
        <h1>Create your account</h1>
        <p>Join thousands of students managing their academics smartly.</p>
      </div>

      <!-- Server-side alerts -->
      <?php if ($error): ?>
        <div class="alert alert-error">
          <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success">
          <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <?= htmlspecialchars($success) ?>
          <a href="login.php" style="margin-left:6px;">Log in →</a>
        </div>
      <?php endif; ?>

      <!-- Register Form -->
      <form id="registerForm" method="POST" action="register.php" novalidate>

        <!-- Name -->
        <div class="form-group">
          <label class="form-label" for="name">Full Name</label>
          <input
            class="form-input"
            type="text"
            id="name"
            name="name"
            placeholder="e.g. Arjun Sharma"
            value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>"
            autocomplete="name"
          />
          <div class="field-error" id="nameError"></div>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input
            class="form-input"
            type="email"
            id="email"
            name="email"
            placeholder="you@example.com"
            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
            autocomplete="email"
          />
          <div class="field-error" id="emailError"></div>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-wrapper">
            <input
              class="form-input"
              type="password"
              id="password"
              name="password"
              placeholder="Min. 6 characters"
              autocomplete="new-password"
            />
            <button type="button" class="toggle-password" aria-label="Toggle password visibility">
              <!-- Eye open -->
              <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              <!-- Eye closed (hidden by default) -->
              <svg class="eye-closed" style="display:none" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
            </button>
          </div>
          <!-- Strength bar -->
          <div class="strength-bar" style="margin-top:8px;">
            <span></span><span></span><span></span><span></span><span></span>
          </div>
          <div class="strength-label"></div>
          <div class="field-error" id="passwordError"></div>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
          <label class="form-label" for="confirm_password">Confirm Password</label>
          <div class="input-wrapper">
            <input
              class="form-input"
              type="password"
              id="confirm_password"
              name="confirm_password"
              placeholder="Re-enter your password"
              autocomplete="new-password"
            />
            <button type="button" class="toggle-password" aria-label="Toggle password visibility">
              <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              <svg class="eye-closed" style="display:none" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
            </button>
          </div>
          <div class="field-error" id="confirmError"></div>
        </div>

        <!-- Course -->
        <div class="form-group">
          <label class="form-label" for="course">Course / Program</label>
          <select class="form-input" id="course" name="course">
            <option value="">— Select your course —</option>
            <?php foreach ($courses as $c): ?>
              <option value="<?= htmlspecialchars($c) ?>"
                <?= (isset($_POST['course']) && $_POST['course'] === $c) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="field-error" id="courseError"></div>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-primary" style="margin-top:6px;">
          <span class="btn-text">Create Account</span>
          <span class="btn-loader">
            <span class="spinner"></span>
            Creating…
          </span>
        </button>

      </form>

      <!-- Footer link -->
      <p class="form-footer">
        Already have an account? <a href="login.php">Sign in</a>
      </p>

    </div>
  </div>

  <script src="js/main.js"></script>
</body>
</html>
