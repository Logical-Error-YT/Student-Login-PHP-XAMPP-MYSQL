<?php
/**
 * StudentPortal — Login Page
 * Authenticates users with email + hashed password,
 * starts a session on success.
 */

session_start();

// Already logged in? Go to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once 'php/config.php';

$error   = '';
$success = '';

// Show success message after logout
if (isset($_GET['logout']) && $_GET['logout'] === '1') {
    $success = 'You have been logged out successfully.';
}

// ── Handle POST submission ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim(strtolower($_POST['email']    ?? ''));
    $password = $_POST['password']                ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $error = 'Please enter a valid email and password.';
    } else {

        $db = getDB();

        // Fetch user by email (prepared statement)
        $stmt = $db->prepare(
            'SELECT id, name, email, password, course FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            // ✅ Login success — store session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['course']  = $user['course'];

            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            $stmt->close();
            $db->close();

            header('Location: dashboard.php');
            exit;
        } else {
            // Vague error on purpose (don't reveal if email exists)
            $error = 'Incorrect email or password. Please try again.';
        }

        $stmt->close();
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign In — StudentPortal</title>
  <link rel="stylesheet" href="css/style.css"/>
</head>
<body>
  <div class="page-wrapper">
    <div class="card">

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
        <h1>Welcome back</h1>
        <p>Sign in to access your student dashboard.</p>
      </div>

      <!-- Error alert -->
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

      <!-- Logout success alert -->
      <?php if ($success): ?>
        <div class="alert alert-success">
          <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <!-- Login Form -->
      <form id="loginForm" method="POST" action="login.php" novalidate>

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
            autofocus
          />
          <div class="field-error"></div>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label class="form-label" for="password">
            Password
          </label>
          <div class="input-wrapper">
            <input
              class="form-input"
              type="password"
              id="password"
              name="password"
              placeholder="Your password"
              autocomplete="current-password"
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
          <div class="field-error"></div>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-primary" style="margin-top:4px;">
          <span class="btn-text">Sign In</span>
          <span class="btn-loader">
            <span class="spinner"></span>
            Signing in…
          </span>
        </button>

      </form>

      <div class="divider">or</div>

      <!-- Footer link -->
      <p class="form-footer">
        Don't have an account? <a href="register.php">Create one</a>
      </p>

    </div>
  </div>

  <script src="js/main.js"></script>
</body>
</html>
