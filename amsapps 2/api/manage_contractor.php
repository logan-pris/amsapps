<?php
require_once __DIR__ . '/../core/auth_check.php';
require_once __DIR__ . '/../core/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$action = $_GET['action'] ?? '';

// Handle Delete
if ($action === 'delete' && !empty($data['id'])) {
    $stmt = $pdo->prepare("DELETE FROM contractors WHERE id = ?");
    $stmt->execute([$data['id']]);
    echo json_encode(["status" => "deleted"]);
    exit;
}

// Handle Save/Update
$id = $data['id'] ?? '';
$name = $data['name'] ?? '';
$dept = $data['department'] ?? '';
$role = $data['role'] ?? '';
$phone = $data['phone'] ?? '';
$email = $data['email'] ?? '';
$rate = $data['job_rate'] ?? 0;

if ($id) {
    // Update existing
    $stmt = $pdo->prepare("UPDATE contractors SET name=?, department=?, role=?, phone=?, email=?, job_rate=? WHERE id=?");
    $stmt->execute([$name, $dept, $role, $phone, $email, $rate, $id]);
} else {
    // Create new
    $stmt = $pdo->prepare("INSERT INTO contractors (name, department, role, phone, email, job_rate) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $dept, $role, $phone, $email, $rate]);
}

echo json_encode(["status" => "success"]);