<?php
// 1. Silence display errors so they don't corrupt the JSON output
ini_set('display_errors', 0);
error_reporting(E_ALL);

// 2. Set JSON header immediately
header("Content-Type: application/json");

// 3. Absolute paths to core files
require_once __DIR__ . '/../core/auth_check.php';
require_once __DIR__ . '/../core/db.php';

try {
    // 4. Capture and decode input
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input || empty($input["name"])) {
        throw new Exception("Invalid input: Contractor name is required.");
    }

    $name       = trim($input["name"]);
    $department = trim($input["department"] ?? "");
    $role       = trim($input["role"] ?? "");
    $phone      = trim($input["phone"] ?? "");
    $email      = trim($input["email"] ?? "");
    $job_rate   = !empty($input["job_rate"]) ? (float)$input["job_rate"] : 0;

    // 5. Check if contractor already exists
    $check = $pdo->prepare("SELECT id FROM contractors WHERE name = ? LIMIT 1");
    $check->execute([$name]);

    if ($check->fetch()) {
        echo json_encode(["status" => "exists", "message" => "Contractor already exists."]);
        exit;
    }

    // 6. Insert new contractor
    $stmt = $pdo->prepare("
        INSERT INTO contractors (name, department, role, phone, email, job_rate)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $success = $stmt->execute([
        $name,
        $department,
        $role,
        $phone,
        $email,
        $job_rate
    ]);

    if ($success) {
        echo json_encode(["status" => "saved"]);
    } else {
        throw new Exception("Database failed to save the record.");
    }

} catch (Throwable $e) {
    // 7. If anything fails, return a clean JSON error
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}