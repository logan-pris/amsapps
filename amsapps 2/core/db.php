<?php
declare(strict_types=1);

require_once __DIR__ . '/security.php';
security_bootstrap();

$host = env_value('DB_HOST', 'localhost');
$user = env_value('DB_USER', 'amsphp');
$pass = env_value('DB_PASS', 'baRdub-3xygky-docbiv');
$db = env_value('DB_NAME', 'contractors_db');
$charset = env_value('DB_CHARSET', 'utf8mb4');

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  http_response_code(500);
  error_log('Database connection error: ' . $e->getMessage());
  echo json_encode(["error" => "Database connection failed."]);
  exit;
}
?>