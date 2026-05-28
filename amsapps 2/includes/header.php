<?php
require_once __DIR__ . '/../core/auth_check.php';

// Format the role for display (e.g., 'pm' becomes 'Project Manager', 'admin' becomes 'Admin')
$roleRaw = $_SESSION['admin_user']['role'] ?? 'user';
$displayRole = '';

if ($roleRaw === 'project_manager') {
    $displayRole = 'Project Manager';
} elseif ($roleRaw === 'admin') {
    $displayRole = 'Admin';
} else {
    $displayRole = 'User';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'AMS APPS' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" type="image/x-icon" href="favicon.png">
</head>
<body>
<nav class="navbar navbar-light bg-white border-bottom mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php">AMS PORTAL</a>
        <div class="d-flex align-items-center">
            <!--span class="text-muted small me-3">
                <strong><?= htmlspecialchars($_SESSION['admin_user']['full_name']) ?></strong> 
                <span class="text-secondary opacity-75">— <?= $displayRole ?></span>
            </span-->
            <span class="text-muted small me-3" 
                  data-bs-toggle="tooltip" 
                  data-bs-placement="bottom" 
                  title="<?= htmlspecialchars($_SESSION['admin_user']['email']) ?>" 
                  style="cursor: help;">
                <strong><?= htmlspecialchars($_SESSION['admin_user']['full_name']) ?></strong> 
                <span class="text-secondary opacity-75">— <?= $displayRole ?></span>
            </span>
            <a href="admin_logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
</nav>