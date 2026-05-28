<?php
require_once 'auth_check.php';
session_start();
header("Content-Type: application/json");

if (!empty($_SESSION["admin_user"])) {
  echo json_encode([
    "logged_in" => true,
    "full_name" => $_SESSION["admin_user"]["full_name"],
    "email" => $_SESSION["admin_user"]["email"],
  ]);
  exit;
}

echo json_encode(["logged_in" => false]);
