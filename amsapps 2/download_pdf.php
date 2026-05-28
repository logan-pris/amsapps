<?php
require_once 'core/auth_check.php'; // Updated Path
require_once 'core/db.php';         // Updated Path

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT pdf_file, job_name FROM contractor_rate_sheets WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row || empty($row['pdf_file'])) {
    die("PDF not found.");
}

header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"" . 
       preg_replace('/[^A-Za-z0-9_\-]/', '_', $row['_jobname']) . ".pdf\"");
echo $row['pdf_file'];
exit;
