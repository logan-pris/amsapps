<?php
require_once __DIR__ . '/../core/auth_check.php';
require_once __DIR__ . '/../core/db.php';

// Set header so the browser knows to expect JSON
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$action = $_GET['action'] ?? '';

try {
    // 1. DELETE LOGIC
    if ($action === 'delete' && !empty($data['id'])) {
        $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
        $stmt->execute([$data['id']]);
        echo json_encode(["status" => "success", "message" => "User deleted"]);
        exit;
    }

    $id = $data['id'] ?? '';
    $full_name = $data['full_name'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $is_active = $data['is_active'] ?? 1;
    $role = $data['role'] ?? 'user'; // Default to standard user

    // 2. QUICK UPDATE LOGIC (Toggles/Dropdowns)
    if ($id && (isset($data['toggle_only']) || isset($data['role_only']))) {
        if (isset($data['toggle_only'])) {
            // Case: Status Toggle
            $stmt = $pdo->prepare("UPDATE admin_users SET is_active=? WHERE id=?");
            $stmt->execute([$is_active, $id]);
        } else {
            // Case: Role Dropdown Update
            $stmt = $pdo->prepare("UPDATE admin_users SET role=? WHERE id=?");
            $stmt->execute([$role, $id]);
        }
        echo json_encode(["status" => "success"]);
        exit;
    }

    // 3. DUPLICATE EMAIL CHECK (Only for full form saves)
    $checkStmt = $pdo->prepare("SELECT id FROM admin_users WHERE email = ? AND id != ?");
    $checkStmt->execute([$email, $id]);
    if ($checkStmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Email is already in use"]);
        exit;
    }

    // 4. UPDATE OR INSERT LOGIC (Full Modal Form)
    if ($id) {
        // Update existing user
        if (!empty($password)) {
            // Case A: Full edit WITH a password change
            $passHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin_users SET full_name=?, email=?, password_hash=?, is_active=?, role=? WHERE id=?");
            $stmt->execute([$full_name, $email, $passHash, $is_active, $role, $id]);
        } else {
            // Case B: Full edit WITHOUT a password change
            $stmt = $pdo->prepare("UPDATE admin_users SET full_name=?, email=?, is_active=?, role=? WHERE id=?");
            $stmt->execute([$full_name, $email, $is_active, $role, $id]);
        }
    } else {
        // Create new user
        if (empty($password)) {
            echo json_encode(["status" => "error", "message" => "Password is required for new users"]);
            exit;
        }
        $passHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admin_users (full_name, email, password_hash, is_active, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $passHash, $is_active, $role]);
    }

    echo json_encode(["status" => "success"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}