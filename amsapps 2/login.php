<?php
// 1. Start the session and require your database connection
session_start();
require_once __DIR__ . '/core/db.php';

// 2. Security Check: If the user is already logged in, send them to the Tool Hub
if (isset($_SESSION["admin_user"])) {
    header("Location: index.php");
    exit;
}

// 3. Handle any error messages passed via URL
$error = isset($_GET["error"]) ? $_GET["error"] : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>AMS Apps - Sign In</title>
  <link rel="icon" type="image/x-icon" href="includes/favicon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  
  
  
    body { background: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    
    /* Layout Container to hold both the Alert and the Card */
    .login-wrapper {
      max-width: 420px;
      margin: 8vh auto; /* Adjust vertical position of the whole unit */
      position: relative;
    }

    /* Notification Area (Exactly in your red circle) */
    #notification-container {
        width: 100%;
        min-height: 50px; /* Reserves space so the card doesn't jump when error appears */
        margin-bottom: 10px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }

    .alert-floating {
        width: 100%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid rgba(220, 53, 69, 0.2);
        border-radius: 10px;
        transition: opacity 0.5s ease-in-out;
        background-color: #fff5f5; /* Subtle red tint */
        color: #dc3545;
        font-weight: 500;
    }
    
    /* 3D Flip Mechanics */
    .flip-container {
      perspective: 1000px;
      width: 100%;
    }
    .flipper {
      transition: 0.6s;
      transform-style: preserve-3d;
      position: relative;
      height: 520px;
    }
    .flipped {
      transform: rotateY(180deg);
    }
    .card-front, .card-back {
      backface-visibility: hidden;
      position: absolute;
      top: 0; left: 0; width: 100%; height: 100%;
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(0,0,0,.08);
      padding: 2.5rem;
    }
    .card-back { transform: rotateY(180deg); }
    .brand { font-weight: 700; color: #212529; }
    
    /* Form Styling */
    .form-control { border-radius: 8px; padding: 0.6rem 0.75rem; border: 1px solid #dee2e6; background-color: #f8faff; }
    .form-control:focus { border-color: #0d6efd; box-shadow: none; background-color: #fff; }
    .btn-login { padding: 0.5rem 1.8rem; font-weight: 600; border-radius: 8px; }
    
    .divider { display: flex; align-items: center; text-align: center; margin: 1.5rem 0; color: #ced4da; }
    .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #dee2e6; }
    .divider:not(:empty)::before { margin-right: .5em; }
    .divider:not(:empty)::after { margin-left: .5em; }
  </style>
</head>
<body>

  <div class="login-wrapper">
    
    <div id="notification-container">
      <?php if ($error): ?>
          <div id="floating-alert" class="alert alert-danger alert-floating py-2 px-3 small text-center mb-0">
              <?php 
                  if ($error === 'account_disabled') echo "Account disabled. Contact an administrator.";
                  else echo htmlspecialchars($error);
              ?>
          </div>
      <?php endif; ?>
    </div>

    <div class="flip-container">
      <div class="flipper" id="main-flipper">
        
        <div class="card-front">
          <div class="mb-4">
            
            <div class="brand h3 mb-1">Sign In</div>
            <div class="text-muted small">Log in below to access AMS Tools</div>
          </div>


          <div class="text-center">
              <?php
                  $tenantId = env_value('MS_TENANT_ID', "24573bbe-42ab-4a8d-9938-c6d5b5835223");
                  $clientId = env_value('MS_CLIENT_ID', "a430fcca-24b5-48d8-a22f-2cd3a6ddbbad");
                  $redirectUri = urlencode(env_value('MS_REDIRECT_URI', "https://amsapps.io/login_callback.php"));
                  $oauthState = bin2hex(random_bytes(16));
                  $_SESSION['ms_oauth_state'] = $oauthState;
                  $loginUrl = "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/authorize?" .
            "client_id=$clientId&response_type=code&redirect_uri=$redirectUri" .
            "&response_mode=query&scope=openid%20profile%20email%20User.Read" .
            "&state=$oauthState";
              ?>
              <a href="<?= $loginUrl ?>" class="btn btn-outline-dark w-100 d-flex align-items-center justify-content-center py-2" style="border-radius: 8px;">
                  <img src="https://upload.wikimedia.org/wikipedia/commons/4/44/Microsoft_logo.svg" alt="MS" style="width: 18px; margin-right: 10px;">
                  <span class="small fw-bold">Sign in with Microsoft</span>
              </a>
          </div>
          
        </div>


      </div>
    </div>
  </div>

  <script>
    // Fade out error message after 5 seconds
    document.addEventListener('DOMContentLoaded', () => {
        const alert = document.getElementById('floating-alert');
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                // Wait for fade to finish before removing to prevent layout "snap"
                setTimeout(() => alert.style.visibility = 'hidden', 500);
            }, 5000);
        }
    });
    </script>
</body>
</html>