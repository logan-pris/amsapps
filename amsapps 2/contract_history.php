<?php 
$pageTitle = "AMS Apps - Contractor History";
include 'includes/header.php'; 
require_once 'core/db.php';

$search = $_GET['search'] ?? '';
$records = $pdo->prepare("SELECT * FROM contractor_rate_sheets WHERE contractor_name LIKE ? OR job_name LIKE ? ORDER BY date_generated DESC");
$records->execute(["%$search%", "%$search%"]);
?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Contract History</h3>
        <form class="d-flex w-50">
            <input type="text" name="search" class="form-control me-2" placeholder="Search contractor or job..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary">Filter</button>
        </form>
    </div>

    <div class="card card-soft table-custom">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Created By</th>
                        <th>Contractor</th>
                        <th>Job Name</th>
                        <th>Rate</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $row): ?>
                    <tr>
                        <td class="small"><?= date('M d, Y', strtotime($row['date_generated'])) ?></td>
                        <td><?= htmlspecialchars($row['created_by']) ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($row['contractor_name']) ?></td>
                        <td><?= htmlspecialchars($row['job_name']) ?></td>
                        <td>$<?= number_format($row['pay_rate'], 2) ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="download_pdf.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-light border">Download</a>
                                <a href="generator.php?edit_id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>