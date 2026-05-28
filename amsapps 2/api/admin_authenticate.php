<?php
// 1. Only require the database. DO NOT require auth_check.php here.
require_once __DIR__ . '/../core/db.php';

// 2. Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

// 3. Basic Validation
if ($email === "" || $password === "") {
    header("Location: ../login.php?error=" . urlencode("Email and password are required."));
    exit;
}

// 4. Fetch the user
$stmt = $pdo->prepare("SELECT id, full_name, email, password_hash, is_active FROM admin_users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

// 5. Verify Password First
if ($user && password_verify($password, $user["password_hash"])) {
    
    // 6. Check if account is active
    if ((int)$user["is_active"] === 1) {
        // SUCCESS: Regenerate session for security
        session_regenerate_id(true);
        
        $_SESSION["admin_id"] = (int)$user["id"]; // Used by your auth_check.php
        $_SESSION["admin_user"] = [
            "id" => (int)$user["id"],
            "full_name" => $user["full_name"],
            "email" => $user["email"],
        ];

        header("Location: ../index.php");
        exit;
    } else {
        // FAIL: Correct password but account is disabled
        // Using the 'account_disabled' slug so login.php shows the specific red box
        header("Location: ../login.php?error=account_disabled");
        exit;
    }
} else {
    // FAIL: User not found or password incorrect
    header("Location: ../login.php?error=" . urlencode("Invalid credentials."));
    exit;
}