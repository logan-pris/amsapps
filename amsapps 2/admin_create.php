<?php
require_once 'auth_check.php';
session_start();

// Require login (same approach as admin_history.php)
if (empty($_SESSION["admin_user"])) {
  header("Location: login.php?error=" . urlencode("Please log in to continue."));
  exit;
}

require_once __DIR__ . "/db.php";

$error = "";
$success = "";

// Form defaults
$full_name = "";
$email = "";
$is_active = 1;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $full_name = trim($_POST["full_name"] ?? "");
  $email = trim($_POST["email"] ?? "");
  $password = $_POST["password"] ?? "";
  $confirm  = $_POST["confirm_password"] ?? "";
  $is_active = isset($_POST["is_active"]) ? 1 : 0;

  // Basic validation
  if ($full_name === "" || $email === "" || $password === "" || $confirm === "") {
    $error = "All fields are required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Please enter a valid email address.";
  } elseif ($password !== $confirm) {
    $error = "Passwords do not match.";
  } elseif (strlen($password) < 8) {
    $error = "Password must be at least 8 characters.";
  } else {
    try {
      // Enforce unique email
      $check = $pdo->prepare("SELECT id FROM admin_users WHERE email = ? LIMIT 1");
      $check->execute([$email]);
      if ($check->fetch()) {
        $error = "That email is already in use.";
      } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
          INSERT INTO admin_users (full_name, email, password_hash, is_active, created_at)
          VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$full_name, $email, $password_hash, $is_active]);

        $success = "Admin user created successfully.";

        // Reset form
        $full_name = "";
        $email = "";
        $is_active = 1;
      }
    } catch (Throwable $e) {
      $error = "Database error: " . $e->getMessage();
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Create Admin User</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Match your existing Bootstrap usage -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* “Login-style” page: centered card on a light background */
    body { background: #f5f7fa; }
    .auth-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; }
    .auth-card {
      width: 100%;
      max-width: 520px;
      border: 0;
      border-radius: 14px;
      box-shadow: 0 10px 28px rgba(0,0,0,.08);
      overflow: hidden;
    }
    .auth-card .card-header {
      background: #0d6efd; /* Bootstrap primary */
      color: #fff;
      padding: 1.25rem 1.5rem;
    }
    .auth-card .card-body { padding: 1.5rem; }
    .form-text { color: #6c757d; }
  </style>
</head>

<body>
  <div class="auth-wrap">
    <div class="card auth-card">
      <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="h5 mb-0">Create Admin User</div>
            <small class="opacity-75">Add a new admin account</small>
          </div>
          <a class="btn btn-sm btn-light" href="admin_history.php">Back</a>
        </div>
      </div>

      <div class="card-body">
        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text"
                   class="form-control"
                   name="full_name"
                   value="<?= htmlspecialchars($full_name) ?>"
                   required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   class="form-control"
                   name="email"
                   value="<?= htmlspecialchars($email) ?>"
                   required>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Password</label>
              <input type="password"
                     class="form-control"
                     name="password"
                     minlength="8"
                     required>
              <div class="form-text">Min 8 characters</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirm Password</label>
              <input type="password"
                     class="form-control"
                     name="confirm_password"
                     minlength="8"
                     required>
            </div>
          </div>

          <div class="form-check mt-3">
            <input class="form-check-input"
                   type="checkbox"
                   name="is_active"
                   id="is_active"
                   <?= $is_active ? "checked" : "" ?>>
            <label class="form-check-label" for="is_active">
              Active account
            </label>
          </div>

          <button class="btn btn-primary w-100 mt-4" type="submit">
            Create Admin
          </button>

          <div class="d-flex justify-content-between mt-3">
            <a href="admin_history.php" class="text-decoration-none">View History</a>
            <a href="admin_logout.php" class="text-decoration-none">Log out</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
