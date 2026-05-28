<?php
require_once __DIR__ . '/../core/auth_check.php';
require_once __DIR__ . '/../core/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");


if (!isset($pdo)) {
  http_response_code(500);
  echo json_encode(["error" => "Database connection not initialized"]);
  exit;
}

$q = $_GET["q"] ?? "";

if (strlen($q) < 2) {
  echo json_encode([]);
  exit;
}

$stmt = $pdo->prepare("
  SELECT
    job_number,
    job_name,
    job_location,
    load_in_date,
    load_out_date,
    pm_name,
    pm_contact
  FROM jobs
  WHERE job_number LIKE ?
  ORDER BY job_number
  LIMIT 10
");

$stmt->execute(["%$q%"]);
echo json_encode($stmt->fetchAll());
