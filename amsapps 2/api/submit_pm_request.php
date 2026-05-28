<?php
/**
 * AMS Portal - Submit PM Access Request (Brevo API Version)
 * Location: api/submit_pm_request.php
 */

ob_start();
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../core/db.php'; 

    if (!isset($_SESSION['admin_user'])) {
        throw new Exception("Unauthorized access.");
    }

    $userId = $_SESSION['admin_user']['id'];
    $userName = $_SESSION['admin_user']['full_name'];
    $userEmail = $_SESSION['admin_user']['email'];
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

    if (empty($reason)) {
        throw new Exception("A reason for the request is required.");
    }

    // 1. Save request to database
    $stmt = $pdo->prepare("INSERT INTO pm_requests (user_id, reason, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$userId, $reason]);

    // 2. Brevo API Configuration
    // Use the same API Key from your request_code.php
    $apiKey = env_value('BREVO_API_KEY', '');
    if ($apiKey === '') {
        throw new Exception('Email provider is not configured.');
    }
    $apiUrl = 'https://api.brevo.com/v3/smtp/email';

    // 3. Prepare the Approval Link
    $approveLink = "https://amsapps.io/api/handle_pm.php?action=approve&id=" . $userId;

    // 4. Email Payload
    $payload = [
        'sender' => ['name' => 'AMS Apps Portal', 'email' => 'no-reply@amsapps.io'],
        'to' => [['email' => 'logan@ams.events']],
        'subject' => "⚠️ PM Access Request: $userName",
        'htmlContent' => "
            <div style='font-family: sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                <div style='text-align: center; border-bottom: 2px solid #0d6efd; padding-bottom: 15px; margin-bottom: 20px;'>
                    <h2 style='color: #333; margin: 0;'>Access Upgrade Request</h2>
                    <p style='color: #0d6efd; font-weight: bold; margin: 5px 0;'>Project Manager Level</p>
                </div>
                
                <p><strong>User:</strong> $userName ($userEmail)</p>
                <p><strong>Reason Provided:</strong></p>
                <div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #0d6efd; color: #444; font-style: italic; margin: 15px 0;'>
                    \"$reason\"
                </div>

                <div style='text-align: center; margin-top: 30px;'>
                    <a href='$approveLink' style='background-color: #198754; color: white; padding: 12px 25px; text-decoration: none; font-weight: bold; border-radius: 6px; display: inline-block;'>
                        Approve Request
                    </a>
                </div>

                <p style='color: #666; font-size: 12px; text-align: center; margin-top: 30px;'>
                    If you wish to deny this request, simply ignore this email or manage the user directly in the User Management panel.
                </p>
                <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='font-size: 11px; color: #999; text-align: center;'>AMS Apps Automated System</p>
            </div>",
        'textContent' => "User $userName ($userEmail) has requested PM access. Reason: $reason. Approve here: $approveLink"
    ];

    // 5. Execute CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'api-key: ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 400) {
        throw new Exception("Mail API Error (HTTP $httpCode)");
    }

    ob_end_clean();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if (ob_get_length()) ob_end_clean();
    http_response_code(500);
    error_log('submit_pm_request error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Unable to submit request.']);
}