<?php
require_once __DIR__ . '/../core/auth_check.php';
require_once __DIR__ . '/../core/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");



try {
  $q = $_GET["q"] ?? "";

  if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
  }

  $stmt = $pdo->prepare("
    SELECT
      name,
      department,
      role,
      phone,
      email,
      job_rate
    FROM contractors
    WHERE name LIKE ?
    ORDER BY name
    LIMIT 10
  ");

  $stmt->execute(["%$q%"]);

  echo json_encode($stmt->fetchAll());

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    "error" => $e->getMessage()
  ]);
}
