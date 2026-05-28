<?php
require_once __DIR__ . '/../core/auth_check.php';
require_once __DIR__ . '/../core/db.php';

// IMPORTANT: With FormData, we use $_POST instead of json_decode(php://input)
$id = $_POST['id'] ?? null;
$created_by = $_SESSION['admin_user']['full_name'] ?? 'Unknown Admin';

// Handle the PDF file upload
$pdf_binary = null;
if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
    $pdf_binary = file_get_contents($_FILES['pdf_file']['tmp_name']);
}

try {
    if (!empty($id)) {
        // UPDATE EXISTING RECORD
        $sql = "UPDATE contractor_rate_sheets SET 
                job_number = ?, job_name = ?, job_location = ?, pm_name = ?, pm_contact = ?, 
                load_in_date = ?, load_out_date = ?, contractor_name = ?, contractor_dept = ?, 
                job_role = ?, contractor_phone = ?, contractor_email = ?, pay_rate = ?, 
                per_diem_rate = ?, additional_fee = ?, days_worked = ?, ot_days = ?, 
                travel_days = ?, pd_days = ?, payment_terms = ?, pdf_file = ? 
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['job_number'], $_POST['job_name'], $_POST['job_location'], 
            $_POST['pm_name'], $_POST['pm_contact'], $_POST['load_in_date'], 
            $_POST['load_out_date'], $_POST['contractor_name'], $_POST['contractor_dept'], 
            $_POST['job_role'], $_POST['contractor_phone'], $_POST['contractor_email'], 
            $_POST['pay_rate'], $_POST['per_diem_rate'], $_POST['additional_fee'], 
            $_POST['days_worked'], $_POST['ot_days'], $_POST['travel_days'], 
            $_POST['pd_days'], $_POST['payment_terms'], $pdf_binary, $id
        ]);
        echo json_encode(["status" => "success", "id" => $id]);
    } else {
        // INSERT NEW RECORD
        $sql = "INSERT INTO contractor_rate_sheets 
                (created_by, job_number, job_name, job_location, pm_name, pm_contact, load_in_date, load_out_date, 
                 contractor_name, contractor_dept, job_role, contractor_phone, contractor_email, pay_rate, 
                 per_diem_rate, additional_fee, days_worked, ot_days, travel_days, pd_days, payment_terms, pdf_file) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $created_by, $_POST['job_number'], $_POST['job_name'], $_POST['job_location'], 
            $_POST['pm_name'], $_POST['pm_contact'], $_POST['load_in_date'], 
            $_POST['load_out_date'], $_POST['contractor_name'], $_POST['contractor_dept'], 
            $_POST['job_role'], $_POST['contractor_phone'], $_POST['contractor_email'], 
            $_POST['pay_rate'], $_POST['per_diem_rate'], $_POST['additional_fee'], 
            $_POST['days_worked'], $_POST['ot_days'], $_POST['travel_days'], 
            $_POST['pd_days'], $_POST['payment_terms'], $pdf_binary
        ]);
        echo json_encode(["status" => "success", "id" => $pdo->lastInsertId()]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}