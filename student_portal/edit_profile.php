<?php
/**
 * StudentPortal — Edit Profile
 * Allows logged-in students to update their name and course.
 * Email is read-only (used for login identity).
 */

session_start();

// Guard: must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'php/config.php';

$error   = '';
$success = '';

// Current user data
$userId = $_SESSION['user_id'];
$db     = getDB();

// Fetch latest data for the form defaults
$stmt = $db->prepare('SELECT name, email, course FROM users WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// ── Handle POST ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name   = trim(htmlspecialchars($_POST['name']   ?? ''));
    $course = trim(htmlspecialchars($_POST['course'] ?? ''));

    if (strlen($name) < 2) {
        $error = 'Name must be at least 2 characters.';
    } elseif (empty($course)) {
        $error = 'Please select a course.';
    } else {

        // Update user record (prepared statement)
        $upd = $db->prepare('UPDATE users SET name = ?, course = ? WHERE id = ?');
        $upd->bind_param('ssi', $name, $course, $userId);

        if ($upd->execute()) {
            // Sync session
            $_SESSION['name']   = $name;
            $_SESSION['course'] = $course;

            // Redirect back to dashboard with success flag
            $upd->close();
            $db->close();

            header('Location: dashboard.php?updated=1');
            exit;
        } else {
            $error = 'Update failed. Please try again.';
        }

        $upd->close();
    }
}

$db->close();

// Use POST data on validation error, otherwise use DB data
$currentName   = ($error && isset($_POST['name']))   ? htmlspecialchars($_POST['name'])   : htmlspecialchars($user['name']);
$currentCourse = ($error && isset($_POST['course'])) ? htmlspecialchars($_POST['course']) : htmlspecialchars($user['course']);

// Courses list
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
  <title>Edit Profile — StudentPortal</title>
  <link rel="stylesheet" href="css/style.css"/>
</head>
<body>
  <div class="page-wrapper">
    <div class="card card-wide">

      <!-- Back link -->
      <a href="dashboard.php" class="edit-profile-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <polyline points="15 18 9 12 15 6"/>
        </svg>
        Back to Dashboard
      </a>

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
        <h1>Edit Profile</h1>
        <p>Update your name and course. Your email cannot be changed.</p>
      </div>

      <!-- Alerts -->
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

      <!-- Edit Form -->
      <form id="editProfileForm" method="POST" action="edit_profile.php" novalidate>

        <!-- Name -->
        <div class="form-group">
          <label class="form-label" for="name">Full Name</label>
          <input
            class="form-input"
            type="text"
            id="name"
            name="name"
            value="<?= $currentName ?>"
            placeholder="Your full name"
            autocomplete="name"
          />
          <div class="field-error"></div>
        </div>

        <!-- Email (read-only) -->
        <div class="form-group">
          <label class="form-label" for="email_ro">Email Address <span style="color:var(--color-text-muted);font-weight:400;">(read-only)</span></label>
          <input
            class="form-input"
            type="email"
            id="email_ro"
            value="<?= htmlspecialchars($user['email']) ?>"
            readonly
            style="background:var(--color-surface-2);cursor:not-allowed;color:var(--color-text-muted);"
          />
        </div>

        <!-- Course -->
        <div class="form-group">
          <label class="form-label" for="course">Course / Program</label>
          <select class="form-input" id="course" name="course">
            <option value="">— Select your course —</option>
            <?php foreach ($courses as $c): ?>
              <option value="<?= htmlspecialchars($c) ?>"
                <?= ($currentCourse === htmlspecialchars($c)) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="field-error"></div>
        </div>

        <!-- Buttons -->
        <div style="display:flex;gap:12px;margin-top:8px;">
          <button type="submit" class="btn btn-primary" style="flex:2;">
            <span class="btn-text">Save Changes</span>
            <span class="btn-loader">
              <span class="spinner"></span>
              Saving…
            </span>
          </button>
          <a href="dashboard.php" class="btn btn-secondary" style="flex:1;">
            Cancel
          </a>
        </div>

      </form>

    </div>
  </div>

  <script src="js/main.js"></script>
</body>
</html>
