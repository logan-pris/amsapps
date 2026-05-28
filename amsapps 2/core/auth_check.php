<?php
require_once 'db.php';

$public_pages = ['login.php', 'admin_authenticate.php', 'request_code.php', 'verify_code.php', 'login_callback.php'];
$current_page = basename($_SERVER['PHP_SELF']);

if (empty($_SESSION["admin_user"]) && !in_array($current_page, $public_pages, true)) {
    header("Location: login.php");
    exit;
}

// Fetch fresh status AND role from DB
if (!isset($_SESSION['admin_user']['id'])) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=session_invalid");
    exit;
}

$stmt = $pdo->prepare("SELECT is_active, role FROM admin_users WHERE id = ?");
$stmt->execute([$_SESSION['admin_user']['id']]); // Ensure this matches your session key
$userData = $stmt->fetch();

if (!$userData || $userData['is_active'] == 0) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=account_disabled");
    exit;
}

// Update the session role so index.php can see it
$_SESSION['admin_user']['role'] = $userData['role']; 
?>