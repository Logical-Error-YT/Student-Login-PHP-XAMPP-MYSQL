<?php
/**
 * StudentPortal — Dashboard
 * Protected page: accessible only when logged in.
 * Displays user info, stats, and quick actions.
 */

session_start();

// Guard: redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'php/config.php';

// Re-fetch latest data from DB (in case profile was updated)
$db   = getDB();
$stmt = $db->prepare(
    'SELECT id, name, email, course, created_at FROM users WHERE id = ? LIMIT 1'
);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
$db->close();

if (!$user) {
    // User no longer exists (e.g. deleted from DB) — clear session
    session_destroy();
    header('Location: login.php');
    exit;
}

// Update session with fresh data
$_SESSION['name']   = $user['name'];
$_SESSION['email']  = $user['email'];
$_SESSION['course'] = $user['course'];

// Derive avatar initials from name (up to 2 chars)
$nameParts = explode(' ', trim($user['name']));
$initials  = strtoupper(substr($nameParts[0], 0, 1));
if (count($nameParts) > 1) {
    $initials .= strtoupper(substr(end($nameParts), 0, 1));
}

// Format join date
$joinDate = date('F Y', strtotime($user['created_at']));

// Success message after edit
$updated = isset($_GET['updated']) && $_GET['updated'] === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard — StudentPortal</title>
  <link rel="stylesheet" href="css/style.css"/>
</head>
<body>

  <div class="dashboard-wrapper">

    <!-- ── Top Navigation Bar ── -->
    <header class="topbar">
      <!-- Brand -->
      <a href="index.html" class="brand" style="margin-bottom:0;">
        <div class="brand-icon">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 3L22 8.5L12 14L2 8.5L12 3Z" fill="white"/>
            <path d="M6 11V17C6 17 8.5 20 12 20C15.5 20 18 17 18 17V11" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <span class="brand-name">StudentPortal</span>
      </a>

      <!-- User info + logout -->
      <div class="topbar-right">
        <div class="user-info-mini">
          <strong><?= htmlspecialchars($user['name']) ?></strong>
          <span><?= htmlspecialchars($user['course']) ?></span>
        </div>
        <div class="avatar" title="<?= htmlspecialchars($user['name']) ?>">
          <?= htmlspecialchars($initials) ?>
        </div>
        <a href="logout.php" class="btn btn-secondary btn-nav" style="font-size:13px;padding:8px 16px;">
          Log out
        </a>
      </div>
    </header>

    <!-- ── Main Content ── -->
    <main class="dashboard-content">

      <!-- Update success alert -->
      <?php if ($updated): ?>
        <div class="alert alert-success" style="margin-bottom:28px;">
          <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          Profile updated successfully!
        </div>
      <?php endif; ?>

      <!-- Greeting -->
      <div class="dash-greeting">
        <h2>Hello, <?= htmlspecialchars(explode(' ', $user['name'])[0]) ?> 👋</h2>
        <p>Here's a summary of your academic profile. Everything looks great.</p>
      </div>

      <!-- Stats Row -->
      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-label">Student ID</div>
          <div class="stat-value">#<?= str_pad($user['id'], 5, '0', STR_PAD_LEFT) ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Enrolled Since</div>
          <div class="stat-value"><?= $joinDate ?></div>
          <div class="stat-badge">Active</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Account Status</div>
          <div class="stat-value">Verified</div>
          <div class="stat-badge">✓ Secure</div>
        </div>
      </div>

      <!-- Profile Card -->
      <div class="profile-card">

        <div class="profile-card-header">
          <div class="avatar-lg"><?= htmlspecialchars($initials) ?></div>
          <div class="profile-card-name">
            <h3><?= htmlspecialchars($user['name']) ?></h3>
            <p>Student · <?= htmlspecialchars($user['course']) ?></p>
          </div>
        </div>

        <div class="profile-fields">
          <div class="profile-field">
            <div class="profile-field-label">Full Name</div>
            <div class="profile-field-value"><?= htmlspecialchars($user['name']) ?></div>
          </div>
          <div class="profile-field">
            <div class="profile-field-label">Email Address</div>
            <div class="profile-field-value"><?= htmlspecialchars($user['email']) ?></div>
          </div>
          <div class="profile-field">
            <div class="profile-field-label">Course / Program</div>
            <div class="profile-field-value"><?= htmlspecialchars($user['course']) ?></div>
          </div>
          <div class="profile-field">
            <div class="profile-field-label">Member Since</div>
            <div class="profile-field-value"><?= $joinDate ?></div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="dash-actions">
          <a href="edit_profile.php" class="btn btn-accent">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Edit Profile
          </a>
          <a href="logout.php" class="btn btn-danger">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Log Out
          </a>
        </div>

      </div>

    </main>

  </div>

  <script src="js/main.js"></script>
</body>
</html>
