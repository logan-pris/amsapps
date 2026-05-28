<?php
require_once __DIR__ . '/../core/auth_check.php';
require_once __DIR__ . '/../core/db.php';

$userId = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
$action = $_POST['action'] ?? ($_GET['action'] ?? '');

if ($userId <= 0) {
    http_response_code(400);
    exit('Invalid request.');
}

if ($action === 'approve') {
    $stmt = $pdo->prepare("UPDATE admin_users SET role = 'project_manager' WHERE id = ?");
    $stmt->execute([$userId]);
    
    // Update request table
    $stmt = $pdo->prepare("UPDATE pm_requests SET status = 'approved' WHERE user_id = ?");
    $stmt->execute([$userId]);
    
    echo "User has been promoted to Project Manager.";
    exit;
}

http_response_code(400);
echo "Unsupported action.";