<?php
// 1. Correct the paths using __DIR__ (This looks relative to the current folder)
require_once __DIR__ . '/../core/auth_check.php';
require_once __DIR__ . '/../core/db.php';

// 2. Set headers for JSON response
header("Content-Type: application/json");

// 3. Prevent PHP from leaking errors into the JSON output
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || empty($data["job_number"])) {
        throw new Exception("Invalid input: Job Number is required.");
    }

    $stmt = $pdo->prepare("
      INSERT INTO jobs 
      (job_number, job_name, job_location, load_in_date, load_out_date, pm_name, pm_contact)
      VALUES (?, ?, ?, ?, ?, ?, ?)
      ON DUPLICATE KEY UPDATE 
        job_name = VALUES(job_name),
        job_location = VALUES(job_location),
        load_in_date = VALUES(load_in_date),
        load_out_date = VALUES(load_out_date),
        pm_name = VALUES(pm_name),
        pm_contact = VALUES(pm_contact)
    ");

    $success = $stmt->execute([
      $data["job_number"],
      $data["job_name"],
      $data["job_location"],
      $data["load_in_date"],
      $data["load_out_date"],
      $data["pm_name"],
      $data["pm_contact"]
    ]);

    if ($success) {
        echo json_encode(["status" => "saved"]);
    } else {
        throw new Exception("Database execution failed.");
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}