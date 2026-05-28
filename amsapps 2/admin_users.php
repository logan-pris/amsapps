<?php 
$pageTitle = "AMS Apps - User Management";
include 'includes/header.php'; 
require_once 'core/db.php';
require_once 'core/auth_check.php';


// Fetch all users
$stmt = $pdo->query("SELECT id, full_name, email, is_active, role FROM admin_users ORDER BY full_name ASC");
$users = $stmt->fetchAll();
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Users</h2>
            <p class="text-muted">Manage system access</p>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#userModal" onclick="prepareAdd()">
            + Create New User
        </button>
    </div>

    <div class="card card-soft p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Email</th>
                        <th>Role</th> 
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="ps-4 fw-bold"><?= htmlspecialchars($user['full_name'] ?: 'N/A') ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <select class="form-select form-select-sm border-0 fw-bold" 
                                    style="width: 130px; cursor: pointer;"
                                    onchange="updateUserRole(<?= $user['id'] ?>, this.value)">
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                <option value="project_manager"<?= $user['role'] === 'project_manager' ? 'selected' : '' ?>>Project Manager</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </td>
                        <td>
                            <button type="button" 
                                    class="btn btn-sm border-0 py-1 rounded-pill fw-bold <?= $user['is_active'] ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' ?>"
                                    style="width: 90px; text-align: center;"
                                    onclick="toggleUserStatus(<?= $user['id'] ?>, <?= $user['is_active'] ?>)"
                                    title="Click to toggle status">
                                <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                            </button>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary" 
                                    onclick='prepareEdit(<?= json_encode($user) ?>)'>
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

<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="userForm">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="userError" class="alert alert-danger d-none small py-2" role="alert">
    </div>
                    <input type="hidden" id="userId" name="id">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Full Name</label>
                        <input type="text" class="form-control" id="userFullName" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="email" class="form-control" id="userEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password (Leave blank to keep current)</label>
                        <input type="password" class="form-control" id="userPassword" name="password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">User Role</label>
                        <select class="form-select" id="userRole" name="role">
                            <option value="user">Standard User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="userActive" name="is_active" checked>
                        <label class="form-check-label small fw-bold" for="userActive">Account Active</label>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger border-0" id="deleteBtn" style="display: none;" onclick="deleteUser()">
                        <i class="bi bi-trash"></i> Delete Profile
                    </button>
                
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Ensure the status button looks consistent */
.bg-success-soft, .bg-danger-soft {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    font-size: 0.75rem; /* Slightly smaller text helps the 90px width look balanced */
    letter-spacing: 0.3px;
}

/* Maintain hover states */
.bg-success-soft:hover { background-color: #d4edda !important; }
.bg-danger-soft:hover { background-color: #f8d7da !important; }
    .card-soft { border: 0; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,.08); }
    
</style>

<script>
async function updateUserRole(userId, newRole) {
    try {
        const response = await fetch('api/save_user.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ 
                id: userId, 
                role: newRole,
                role_only: true // Flag to help your PHP identify the action
            })
        });

        const result = await response.json();

        if (response.ok && result.status === 'success') {
            // Optional: Provide a small visual confirmation
            console.log(`User ${userId} updated to ${newRole}`);
        } else {
            alert("Error updating role: " + (result.message || "Unknown error"));
            location.reload(); // Revert UI if DB update fails
        }
    } catch (err) {
        console.error("Fetch error:", err);
        alert("Connection error. Please try again.");
        location.reload();
    }
}
async function toggleUserStatus(userId, currentState) {
    // If currentState was 1 (true), we send 0 (false), and vice versa
    const newStatus = currentState ? 0 : 1;
    
    try {
        const response = await fetch('api/save_user.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ 
                id: userId, 
                is_active: newStatus,
                toggle_only: true 
            })
        });

        const result = await response.json();

        if (response.ok && result.status === 'success') {
            // Reload to show the new badge color and text
            location.reload(); 
        } else {
            alert("Error updating status: " + (result.message || "Unknown error"));
        }
    } catch (err) {
        console.error("Fetch error:", err);
        alert("Connection error. Please try again.");
    }
}
function prepareAdd() {
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('userError').classList.add('d-none'); // Hide error on open
    
    // 1. Make password REQUIRED for new users
    const pwdInput = document.getElementById('userPassword');
    pwdInput.value = '';
    pwdInput.required = true;
    
    // 2. Update Label for clarity
    pwdInput.previousElementSibling.innerText = "Password";
    
    document.getElementById('modalTitle').innerText = 'Create New User';
    document.getElementById('userActive').checked = true;
    document.getElementById('deleteBtn').style.display = 'none';
}

function prepareEdit(user) {
    document.getElementById('userError').classList.add('d-none'); // Hide error on open
    document.getElementById('modalTitle').innerText = 'Edit User';
    document.getElementById('userId').value = user.id;
    document.getElementById('userFullName').value = user.full_name;
    document.getElementById('userEmail').value = user.email;
    document.getElementById('userActive').checked = user.is_active == 1;
    document.getElementById('userRole').value = user.role || 'user';
    
    // 3. Make password OPTIONAL for editing
    const pwdInput = document.getElementById('userPassword');
    pwdInput.value = '';
    pwdInput.required = false;
    
    // 4. Reset Label to show it's optional
    pwdInput.previousElementSibling.innerText = "Password (Leave blank to keep current)";
    
    document.getElementById('deleteBtn').style.display = 'block'; 
    
    const modalElement = document.getElementById('userModal');
    let modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
    modalInstance.show();
}

async function deleteUser() {
    const userId = document.getElementById('userId').value;
    const userName = document.getElementById('userFullName').value;

    // Soft confirmation matching the UI style
    if (confirm(`Are you sure you want to permanently delete ${userName}? This action cannot be undone.`)) {
        const response = await fetch('api/save_user.php?action=delete', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ id: userId })
        });

        if (response.ok) {
            location.reload();
        } else {
            alert("Error deleting user.");
        }
    }
}

document.getElementById('userForm').onsubmit = async (e) => {
    e.preventDefault();
    const errorDiv = document.getElementById('userError');
    errorDiv.classList.add('d-none'); 

    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    data.is_active = document.getElementById('userActive').checked ? 1 : 0;

    try {
        const response = await fetch('api/save_user.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });

        // Check if the response is actually JSON before parsing
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            const result = await response.json();
            
            if (response.ok && result.status === 'success') {
                location.reload();
            } else {
                errorDiv.innerText = result.message || "Error saving user.";
                errorDiv.classList.remove('d-none');
            }
        } else {
            // This handles cases where PHP sends a raw error or HTML crash page
            const textError = await response.text();
            console.error("Server returned non-JSON:", textError);
            errorDiv.innerText = "Server Error: Unable to process request.";
            errorDiv.classList.remove('d-none');
        }
    } catch (err) {
        console.error("Fetch error:", err);
        errorDiv.innerText = "Connection error. Please try again.";
        errorDiv.classList.remove('d-none');
    }
};
</script>