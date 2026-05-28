<?php
// shirts.php — styled like admin_login.php module
$showMessage = "";

if (isset($_GET["show"])) {
  $showMessage = trim($_GET["show"]);
}
$storageFile = __DIR__ . DIRECTORY_SEPARATOR . "/../shirts/submissions.txt";

function clean($s) {
  $s = trim((string)$s);
  $s = str_replace(["\r", "\n", "\t"], " ", $s);
  $s = preg_replace('/\s+/', ' ', $s);
  return $s;
}

$success = false;
$error = "";
$name = "";
$size = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = clean($_POST["name"] ?? "");
  $size = clean($_POST["size"] ?? "");

  if ($name === "") {
    $error = "Name is required.";
  } elseif ($size === "") {
    $error = "Shirt size is required.";
  } else {
    $timestamp = gmdate("c");
    $line = $timestamp . " | " . $name . " | " . $size . " | " . $showMessage . PHP_EOL;

    $fp = @fopen($storageFile, "a");
    if ($fp === false) {
      $error = "Could not open storage file for writing. Check permissions.";
    } else {
      if (flock($fp, LOCK_EX)) {
        fwrite($fp, $line);
        fflush($fp);
        flock($fp, LOCK_UN);
        $success = true;
        $name = "";
        $size = "";
      } else {
        $error = "Could not lock storage file. Please try again.";
      }
      fclose($fp);
    }
  }
}

$sizes = ["XS","S","M","L","XL","2XL","3XL"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/x-icon" href="/../includes/favicon.png">
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>AMS Apps - Shirt Order </title><!--?= htmlspecialchars($showMessage !== "" ? $showMessage : "AMS Events", ENT_QUOTES, "UTF-8"); ?></title-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Same CSS used by admin_login.php -->
  <link href="includes/css/admin.css" rel="stylesheet">

  <style>
    body { background: #f5f7fa; }
    .card-login {
      max-width: 420px;
      margin: 8vh auto;
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(0,0,0,.08);
    }
    .brand {
      font-weight: 700;
      letter-spacing: .2px;
    }
  </style>
</head>
<body>
  <div class="card card-login">
    <div class="card-body p-4">
      <div class="mb-3">
        <div class="brand h4 mb-1">AMS Shirt Request</div>
        <div class="text-muted"><?= htmlspecialchars($showMessage !== "" ? $showMessage : "AMS Events", ENT_QUOTES, "UTF-8"); ?></div>
      </div>

       <?php if ($success): ?>
        <div class="alert alert-success text-center">
          ✅ Submitted successfully!
        </div>
      <?php elseif ($error !== ""): ?>
        <div class="alert alert-danger text-center">
          ❌ <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?>
        </div>
      <?php endif; ?>

      <form method="POST" autocomplete="off">
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input
            type="text"
            name="name"
            class="form-control"
            required
            placeholder="Jane Doe"
            value="<?= htmlspecialchars($name, ENT_QUOTES, "UTF-8"); ?>"
          >
        </div>

        <div class="mb-3">
          <label class="form-label">Shirt Size</label>
          <select name="size" class="form-select" required>
            <option value="">Select size...</option>
            <?php foreach ($sizes as $opt): ?>
              <option value="<?= $opt ?>" <?= ($size === $opt) ? "selected" : "" ?>>
                <?= $opt ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <button class="btn btn-primary w-100" type="submit">Submit</button>

      </form>
      <div class="text-center mt-3 small text-muted">
        Responses are saved securely.
      </div>
    </div>
  </div>
</body>
</html>
