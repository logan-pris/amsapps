<?php 
$pageTitle = "AMS Apps - Contractor Management";
include 'includes/header.php'; 
require_once 'core/db.php';
require_once 'core/auth_check.php';

// Fetch all contractors
$stmt = $pdo->query("SELECT * FROM contractors 
                     ORDER BY SUBSTRING_INDEX(name, ' ', -1) ASC");
$contractors = $stmt->fetchAll();
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Contractor Database</h2>
            <p class="text-muted">Master list for the Contract Generator</p>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#contractorModal" onclick="prepareAdd()">
            + Add New Contractor
        </button>
    </div>

    <div class="card card-soft p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Role / Dept</th>
                        <th>Contact Info</th>
                        <th>Default Rate</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contractors as $c): ?>
                    <tr>
                        <td class="ps-4 fw-bold"><?= htmlspecialchars($c['name']) ?></td>
                        <td>
                            <div class="small fw-bold"><?= htmlspecialchars($c['role']) ?></div>
                            <div class="text-muted smaller"><?= htmlspecialchars($c['department']) ?></div>
                        </td>
                        <td>
                            <div class="small"><?= htmlspecialchars($c['email']) ?></div>
                            <div class="text-muted smaller"><?= htmlspecialchars($c['phone']) ?></div>
                        </td>
                        <td>$<?= number_format($c['job_rate'], 2) ?></td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary" 
                                    onclick='prepareEdit(<?= json_encode($c, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                Edit
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="contractorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="contractorForm">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Edit Contractor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="contId" name="id">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Full Name</label>
                        <input type="text" class="form-control" id="contName" name="name" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Role</label>
                            <input type="text" class="form-control" id="contRole" name="role">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Department</label>
                            <input type="text" class="form-control" id="contDept" name="department">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" class="form-control" id="contEmail" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Phone</label>
                        <input type="text" class="form-control" id="contPhone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Default Pay Rate ($)</label>
                        <input type="number" step="0.01" class="form-control" id="contRate" name="job_rate">
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger border-0" id="deleteBtn" style="display: none;" onclick="deleteContractor()">
                        Delete Profile
                    </button>
                    <button type="submit" class="btn btn-primary px-4">Save Contractor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card-soft { border: 0; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,.08); }
    .smaller { font-size: 0.75rem; }
</style>

<script>
function prepareAdd() {
    document.getElementById('contractorForm').reset();
    document.getElementById('contId').value = '';
    document.getElementById('modalTitle').innerText = 'Add New Contractor';
    document.getElementById('deleteBtn').style.display = 'none';
}

function prepareEdit(c) {
    document.getElementById('modalTitle').innerText = 'Edit Contractor';
    document.getElementById('contId').value = c.id;
    document.getElementById('contName').value = c.name;
    document.getElementById('contRole').value = c.role;
    document.getElementById('contDept').value = c.department;
    document.getElementById('contEmail').value = c.email;
    document.getElementById('contPhone').value = c.phone;
    document.getElementById('contRate').value = c.job_rate;
    
    document.getElementById('deleteBtn').style.display = 'block';
    
    const modalElement = document.getElementById('contractorModal');
    let modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
    modalInstance.show();
}

document.getElementById('contractorForm').onsubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    const response = await fetch('api/manage_contractor.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    });

    if (response.ok) { location.reload(); }
};

async function deleteContractor() {
    const id = document.getElementById('contId').value;
    const name = document.getElementById('contName').value;
    if (confirm(`Are you sure you want to delete ${name}?`)) {
        await fetch('api/manage_contractor.php?action=delete', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ id: id })
        });
        location.reload();
    }
}
</script>