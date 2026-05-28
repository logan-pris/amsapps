<?php 
require_once __DIR__ . '/core/auth_check.php';
require_once __DIR__ . '/core/db.php';

$pageTitle = "AMS Apps - Contract Generator";
$edit_id = $_GET['edit_id'] ?? null;
$edit_data = null;

if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM contractor_rate_sheets WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}
include 'includes/header.php'; 

// Fetch available templates for the dropdown
try {
    $templatesQuery = $pdo->query("SELECT id, template_name, base_pdf_path FROM pdf_templates ORDER BY template_name ASC");
    $templates = $templatesQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $templates = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Contract Generator</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <script src="https://unpkg.com/pdf-lib/dist/pdf-lib.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <style>
    body { background: #f5f7fa; }
    .form-card { max-width: 900px; margin: 2rem auto; padding: 2rem; background: #ffffff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08); }
    .section-title { font-weight: 600; margin: 2rem 0 1rem; border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem; }
    .hidden-section { display: none; }
    input:disabled { background-color: #f1f3f5; cursor: not-allowed; }
    .list-group-item { cursor: pointer; }
    .list-group-item:hover { background-color: #f8f9fa; }
  </style>
</head>

<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Contract Generator</h3>
        <div class="col-md-4">
            <label class="form-label small fw-bold">Select Template</label>
            <select class="form-select" id="templateSelector">
                <!--option value="default" data-path="templates/ams.pdf">Standard AMS Template (Legacy)</option-->
                <?php foreach ($templates as $tmpl): ?>
                    <option value="<?= $tmpl['id'] ?>" data-path="<?= htmlspecialchars($tmpl['base_pdf_path']) ?>">
                        <?= htmlspecialchars($tmpl['template_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <main class="form-card">
        <h5 class="section-title">Job Information</h5>
        <input type="hidden" id="edit_record_id" value="<?= $edit_id ?>">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Job Number</label>
                <input type="text" class="form-control" id="_jobnum" value="<?= htmlspecialchars($edit_data['job_number'] ?? '') ?>" autocomplete="off">
                <ul id="jobSuggestions" class="list-group position-absolute w-50" style="z-index: 1000;"></ul> 
            </div>
            <div class="col-md-4">
                <label class="form-label">Job Name</label>
                <input type="text" class="form-control" id="_jobname" value="<?= htmlspecialchars($edit_data['job_name'] ?? '') ?>" disabled >
            </div>
            <div class="col-md-4">
                <label class="form-label">Venue</label>
                <input type="text" class="form-control" id="_jobloc" placeholder="Venue or address..." value="<?= htmlspecialchars($edit_data['job_location'] ?? '') ?>" autocomplete="off" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">PM Name</label>
                <input type="text" class="form-control" id="_pm_name" value="<?= htmlspecialchars($edit_data['pm_name'] ?? '') ?>" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">PM Contact</label>
                <input type="text" class="form-control" id="_pm_contact" value="<?= htmlspecialchars($edit_data['pm_contact'] ?? '') ?>" disabled>
            </div>
            <div class="col-md-12">
                <label class="form-label">Dates</label>
                <div class="d-flex gap-2 align-items-center">
                    <input type="date" class="form-control" id="_jobdates" value="<?= htmlspecialchars($edit_data['load_in_date'] ?? '') ?>" disabled>
                    <span>to</span>
                    <input type="date" class="form-control" id="_jobdatee" value="<?= htmlspecialchars($edit_data['load_out_date'] ?? '') ?>" disabled>
                </div>
            </div>
        </div>

        <div id="contractorSection" class="hidden-section">
            <h5 class="section-title">Contractor Information</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" id="_name" value="<?= htmlspecialchars($edit_data['contractor_name'] ?? '') ?>">
                    <ul id="contractorSuggestions" class="list-group position-absolute w-50" style="z-index: 1000;"></ul>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Department</label>
                    <input type="text" class="form-control" id="_dept" value="<?= htmlspecialchars($edit_data['contractor_dept'] ?? '') ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" id="_role" value="<?= htmlspecialchars($edit_data['job_role'] ?? '') ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" id="_phone" value="<?= htmlspecialchars($edit_data['contractor_phone'] ?? '') ?>" disabled>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="_email" value="<?= htmlspecialchars($edit_data['contractor_email'] ?? '') ?>" disabled>
                </div>
            </div>
        </div>


            

        <div id="ratesSection" class="hidden-section">
            <h5 class="section-title">Rates & Terms</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Daily Rate</label>
                    <input type="number" class="form-control" id="_jobrate" value="<?= htmlspecialchars($edit_data['pay_rate'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Per Diem</label>
                    <input type="number" class="form-control" id="_pdrate" value="<?= htmlspecialchars($edit_data['per_diem_rate'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Additional $</label>
                    <input type="number" class="form-control" id="text_22sqff" value="<?= htmlspecialchars($edit_data['additional_fee'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Days Worked</label>
                    <input type="number" class="form-control" id="_rateqty" value="<?= htmlspecialchars($edit_data['days_worked'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">OT Hours</label>
                    <input type="number" class="form-control" id="_otqty" value="<?= htmlspecialchars($edit_data['ot_days'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Travel Days</label>
                    <input type="number" class="form-control" id="_travelqty" value="<?= htmlspecialchars($edit_data['travel_days'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">PD Days</label>
                    <input type="number" class="form-control" id="_pdqty" value="<?= htmlspecialchars($edit_data['pd_days'] ?? '') ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Payment Terms</label>
                    <select class="form-select" id="_terms">
                        <?php 
                            $currentTerm = $edit_data['payment_terms'] ?? ''; 
                        ?>
                        <option <?= ($currentTerm == 'Show Completion') ? 'selected' : '' ?>>Show Completion</option>
                        <option <?= ($currentTerm == 'Net 15') ? 'selected' : '' ?>>Net 15</option>
                        <option <?= ($currentTerm == 'Net 30') ? 'selected' : '' ?>>Net 30</option>
                    </select>
                </div>
            </div>
        </div>

        <button id="dl" class="btn btn-primary w-100 mt-4 py-2 fw-bold" onclick="processContractGeneration();">
            Generate and Download PDF
        </button>
    </main>
</div>

<?php include 'includes/generator_modals.php'; ?>

<script>
/**
 * UI LOGIC: RESTORING AUTO-COMPLETE AND FIELD LOCKING
 */
document.addEventListener('DOMContentLoaded', () => {
    const jobInput = document.getElementById('_jobnum');
    const jobSug = document.getElementById('jobSuggestions');
    const nameInput = document.getElementById('_name');
    const nameSug = document.getElementById('contractorSuggestions');
    const editId = "<?= $edit_id ?>";

    if (editId) {
        // EDIT MODE: Clear drafts to ensure we only see DB data
        sessionStorage.removeItem('generator_form_draft');
        sessionStorage.removeItem('pending_pdf_meta');
        
        $('#contractorSection').show();
        $('#ratesSection').show();
        $('input, select, textarea').prop('disabled', false);
        
        console.log("Edit Mode: Loaded Record " + editId);
    } else {
        // CREATE MODE: Force a clean slate by clearing all session storage
        sessionStorage.removeItem('generator_form_draft');
        sessionStorage.removeItem('pending_pdf_meta');
        sessionStorage.removeItem('pending_pdf_data');

        // Reset the actual form fields in case the browser cached them
        $('input').val('');
        $('select').prop('selectedIndex', 0);
        
        // Ensure sections are hidden for a new entry
        $('#contractorSection').hide();
        $('#ratesSection').hide();

        console.log("Create Mode: Session storage cleared for fresh form.");
    }
    // 1. Job Search
    jobInput.addEventListener('input', async () => {
        const q = jobInput.value.trim();
        if (q.length < 2) { jobSug.innerHTML = ""; return; }
        
        const res = await fetch(`api/search_jobs.php?q=${encodeURIComponent(q)}`);
        const jobs = await res.json();
        
        jobSug.innerHTML = "";
        if (jobs.length === 0) {
            const li = $('<li class="list-group-item text-primary"><strong>+ Create New Job</strong></li>');
            li.on('click', () => { openNewJobModal(q); jobSug.innerHTML = ""; });
            $('#jobSuggestions').append(li);
        } else {
            jobs.forEach(j => {
                const li = $(`<li class="list-group-item"><strong>${j.job_number}</strong> - ${j.job_name}</li>`);
                li.on('click', () => selectJob(j));
                $('#jobSuggestions').append(li);
            });
        }
    });

    // 2. Contractor Search
    nameInput.addEventListener('input', async () => {
        const q = nameInput.value.trim();
        if (q.length < 2) { nameSug.innerHTML = ""; return; }

        const res = await fetch(`api/search_contractors.php?q=${encodeURIComponent(q)}`);
        const contractors = await res.json();

        nameSug.innerHTML = "";
        if (contractors.length === 0) {
            const li = $('<li class="list-group-item text-primary"><strong>+ Create New Contractor</strong></li>');
            li.on('click', () => { openNewContractorModal(q); nameSug.innerHTML = ""; });
            $('#contractorSuggestions').append(li);
        } else {
            contractors.forEach(c => {
                const li = $(`<li class="list-group-item">${c.name}</li>`);
                li.on('click', () => selectContractor(c));
                $('#contractorSuggestions').append(li);
            });
        }
    });

    // 3. Restore Form State if returning from Review
    restoreFormState();
});
function openNewJobModal(prefillJobNumber = "") {
    document.getElementById("nj_job_number").value = prefillJobNumber;
    const modalEl = document.getElementById("newJobModal");
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function openNewContractorModal(prefillName = "") {
    document.getElementById("nc_name").value = prefillName;
    const modalEl = document.getElementById("newContractorModal");
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}
function saveFormState() {
    const state = {};
    $('input, select').each(function() {
        if (this.id) state[this.id] = $(this).val();
    });
    sessionStorage.setItem('generator_form_draft', JSON.stringify(state));
}

function restoreFormState() {
    const saved = sessionStorage.getItem('generator_form_draft');
    if (!saved) return;

    const state = JSON.parse(saved);
    Object.keys(state).forEach(id => {
        const $el = $('#' + id);
        if ($el.length) {
            $el.val(state[id]);
            // Re-enable fields that were filled
            if (state[id] && id !== '_jobnum') {
                $el.prop('disabled', false);
            }
        }
    });

    // Trigger UI transitions if data exists
    if (state._jobnum) $('#contractorSection').show();
    if (state._name) $('#ratesSection').show();
    
    // Clear storage after restoration to prevent stale data on fresh visits
    sessionStorage.removeItem('generator_form_draft');
}
window.saveNewContractor = async function () {
    // 1. Grab data from the modal IDs (nc_ prefix)
    const payload = {
        name: document.getElementById("nc_name").value.trim(),
        department: document.getElementById("nc_department").value.trim(),
        role: document.getElementById("nc_role").value.trim(),
        phone: document.getElementById("nc_phone").value.trim(),
        email: document.getElementById("nc_email").value.trim(),
        job_rate: document.getElementById("nc_jobrate").value
    };

    // 2. Simple validation
    if (!payload.name) {
        alert("Contractor name is required.");
        return;
    }

    try {
        // 3. POST to your API
        const res = await fetch("api/save_contractor.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        if (!res.ok) throw new Error('Network response was not ok');

        const result = await res.json();

        if (result.status === "saved" || result.status === "exists") {
            // 4. Update the main form fields automatically
            document.getElementById("_name").value = payload.name;
            document.getElementById("_dept").value = payload.department;
            document.getElementById("_role").value = payload.role;
            document.getElementById("_phone").value = payload.phone;
            document.getElementById("_email").value = payload.email;
            document.getElementById("_jobrate").value = payload.job_rate;

            // 5. Unlock fields & show rates (as per your workflow)
            if (typeof lockContractorFields === "function") lockContractorFields(false);
            document.getElementById("ratesSection").style.display = "block";
            
            // 6. Close the modal
            const modalEl = document.getElementById("newContractorModal");
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            console.log("Contractor saved/linked successfully.");
        } else {
            alert("Error: " + result.message);
        }
    } catch (err) {
        console.error("Fetch Error:", err);
        alert("Failed to connect to the server. Check the console.");
    }
};
window.saveNewJob = async function () {
    // 1. Collect data from the Modal fields
    const payload = {
        job_number: document.getElementById("nj_job_number").value.trim(),
        job_name: document.getElementById("nj_job_name").value.trim(),
        job_location: document.getElementById("nj_job_location").value.trim(),
        load_in_date: document.getElementById("nj_load_in").value,
        load_out_date: document.getElementById("nj_load_out").value,
        pm_name: document.getElementById("nj_pm_name").value.trim(),
        pm_contact: document.getElementById("nj_pm_contact").value.trim()
    };

    // 2. Validation
    if (!payload.job_number || !payload.job_name) {
        alert("Job Number and Job Name are required");
        return;
    }

    try {
        // 3. Send to your API
        const res = await fetch("api/save_job.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        const result = await res.json();

        if (result.status === "saved" || result.status === "exists") {
            // 4. Update the main form with the new job details
            selectJob(payload);

            // 5. Hide the modal
            const modalEl = document.getElementById("newJobModal");
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            
            console.log("Job successfully linked:", result);
        } else {
            alert("Failed to save job: " + (result.message || "Unknown error"));
        }
    } catch (err) {
        console.error("Save Job Error:", err);
        alert("Network error. Check console.");
    }
};
function selectJob(job) {
    $('#_jobnum').val(job.job_number);
    $('#_jobname').val(job.job_name).prop('disabled', false);
    $('#_jobloc').val(job.job_location).prop('disabled', false);
    $('#_jobdates').val(job.load_in_date).prop('disabled', false);
    $('#_jobdatee').val(job.load_out_date).prop('disabled', false);
    $('#_pm_name').val(job.pm_name).prop('disabled', false);
    $('#_pm_contact').val(job.pm_contact).prop('disabled', false);
    
    $('#contractorSection').fadeIn();
    $('#jobSuggestions').empty();
}

function selectContractor(c) {
    $('#_name').val(c.name);
    $('#_dept').val(c.department).prop('disabled', false);
    $('#_role').val(c.role).prop('disabled', false);
    $('#_phone').val(c.phone).prop('disabled', false);
    $('#_email').val(c.email).prop('disabled', false);
    $('#_jobrate').val(c.job_rate);
    
    $('#ratesSection').fadeIn();
    $('#contractorSuggestions').empty();
}

/**
 * CORE LOGIC: COORDINATE-BASED PDF GENERATION
 */
async function processContractGeneration() {
    saveFormState(); // Capture everything currently in the form
    const templateId = document.getElementById('templateSelector').value;
    const templatePath = $('#templateSelector option:selected').data('path');
    const vals = getFormValues();

    if (templateId === 'default') {
        fillLegacyForm(templatePath, vals);
    } else {
        await fillDynamicTemplate(templateId, templatePath, vals);
    }
}

function getFormValues() {
    const _jobrate = parseFloat($('#_jobrate').val()) || 0;
    const _otqty = parseFloat($('#_otqty').val()) || 0;
    const _pdrate = parseFloat($('#_pdrate').val()) || 0;
    const _rateqty = parseFloat($('#_rateqty').val()) || 0;
    const _travelqty = parseFloat($('#_travelqty').val()) || 0;
    const _pdqty = parseFloat($('#_pdqty').val()) || 0;
    const _addl = parseFloat($('#text_22sqff').val()) || 0;
    
    
    const t_travel_val = _jobrate / 2; // 0.5x Daily Rate
    const t_ot_val = (_jobrate / 10) * 1.5;
    const t_ottotal = t_ot_val * _otqty;
    const t_ratetotal = _rateqty * _jobrate;
    const t_traveltotal = _travelqty * t_travel_val;
    const t_pdtotal = _pdqty * _pdrate;
    const t_total = t_ratetotal + t_ottotal + t_traveltotal + t_pdtotal + _addl;

    return {
        '_jobnum': $('#_jobnum').val(),
        '_jobname': $('#_jobname').val(),
        '_jobloc': $('#_jobloc').val(),
        '_jobdates': $('#_jobdates').val() + " - " + $('#_jobdatee').val(),
        '_pm_name': $('#_pm_name').val(),
        '_pm_contact': $('#_pm_contact').val(),
        '_name': $('#_name').val(),
        '_dept': $('#_dept').val(),
        '_role': $('#_role').val(),
        '_phone': $('#_phone').val(),
        '_email': $('#_email').val(),
        
        '_rate': "$ " + _jobrate,     
        '_otrate': "$ " + t_ot_val,  
        '_travel': "$ " + t_travel_val,    
        '_pd': "$ " + _pdrate,             
        
        '_jobrate': "$ " + _jobrate,       
        '_pdrate': "$ " + _pdrate,         
        
        '_rateqty': String(_rateqty),
        '_otqty': String(_otqty),
        '_travelqty': String(_travelqty),
        '_pdqty': String(_pdqty),
        '_ratetotal': "$ " + t_ratetotal,
        '_ottotal': "$ " + t_ottotal,
        '_traveltotal': "$ " + t_traveltotal,
        '_pdtotal': "$ " + t_pdtotal,
        'text_22sqff': "$ " + _addl,
        '_total': "$ " + t_total + " Total",
        '_terms': $('#_terms').val()
    };
}

async function fillDynamicTemplate(templateId, path, values) {
    try {
        const res = await fetch(`api/get_template_mapping.php?id=${templateId}`);
        const mapping = await res.json();

        const bytes = await fetch(path).then(res => res.arrayBuffer());
        const pdfDoc = await PDFLib.PDFDocument.load(bytes);
        
        const standardFont = await pdfDoc.embedFont(PDFLib.StandardFonts.Helvetica);
        const pages = pdfDoc.getPages();

        mapping.fields.forEach(f => {
            const page = pages[f.page_number - 1] || pages[0];
            const text = values[f.field_key] || "";
            const fontSize = parseInt(f.font_size) || 11;
            
            let xPos = parseFloat(f.pos_x);
            const yPos = parseFloat(f.pos_y);
        
            if (f.alignment === 'center' || f.alignment === 'right') {
                const textWidth = standardFont.widthOfTextAtSize(text, fontSize);
                const containerWidth = f.width ? parseFloat(f.width) : 0;
        
                if (f.alignment === 'center') {
                    xPos = xPos + (containerWidth / 2) - (textWidth / 2);
                } else if (f.alignment === 'right') {
                    xPos = xPos + containerWidth - textWidth;
                }
            }
        
            page.drawText(text, {
                x: xPos,
                y: yPos,
                size: fontSize,
                font: standardFont,
                color: PDFLib.rgb(0, 0, 0)
            });
        });

        const pdfBytes = await pdfDoc.save();
        saveAndOpenPdf(pdfBytes, values);
    } catch (err) {
        console.error("Dynamic generation failed", err);
    }
}

async function fillLegacyForm(path, values) {
    const bytes = await fetch(path).then(res => res.arrayBuffer());
    const pdfDoc = await PDFLib.PDFDocument.load(bytes, { ignoreEncryption: true });
    const form = pdfDoc.getForm();

    Object.entries(values).forEach(([key, val]) => {
        try {
            const field = form.getField(key);
            field.setText(val);
        } catch (e) {}
    });

    const pdfBytes = await pdfDoc.save();
    saveAndOpenPdf(pdfBytes, values);
}

/*async function saveAndOpenPdf(pdfBytes, values) {
    const pdfBase64 = btoa(new Uint8Array(pdfBytes).reduce((data, byte) => data + String.fromCharCode(byte), ''));
    
    sessionStorage.setItem('pending_pdf_data', pdfBase64);
    sessionStorage.setItem('pending_pdf_meta', JSON.stringify(values));

    const rawRate = parseFloat($('#_jobrate').val()) || 0;
    fetch("api/save_rate_sheet.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            job_name: values._jobname,
            job_role: values._role,
            pay_rate: rawRate,
            contractor_name: values._name,
            contractor_email: values._email,
            contractor_phone: values._phone,
            pdf_file: pdfBase64
        })
    });

    window.location.href = 'pdf_review.php';
}*/
/*async function saveAndOpenPdf(pdfBytes, values) {
    const editId = document.getElementById('edit_record_id').value;
    
    // Convert PDF to Base64
    const pdfBase64 = btoa(
        new Uint8Array(pdfBytes).reduce((data, byte) => data + String.fromCharCode(byte), '')
    );

    // Prepare payload with all fields for database consistency
    const payload = {
        id: editId,
        job_number: $('#_jobnum').val(),
        job_name: $('#_jobname').val(),
        job_location: $('#_jobloc').val(),
        pm_name: $('#_pm_name').val(),
        pm_contact: $('#_pm_contact').val(),
        load_in_date: $('#_jobdates').val(),
        load_out_date: $('#_jobdatee').val(),
        contractor_name: $('#_name').val(),
        contractor_dept: $('#_dept').val(),
        job_role: $('#_role').val(),
        contractor_phone: $('#_phone').val(),
        contractor_email: $('#_email').val(),
        pay_rate: parseFloat($('#_jobrate').val()) || 0,
        per_diem_rate: parseFloat($('#_pdrate').val()) || 0,
        additional_fee: parseFloat($('#text_22sqff').val()) || 0,
        days_worked: parseInt($('#_rateqty').val()) || 0,
        ot_days: parseInt($('#_otqty').val()) || 0,
        travel_days: parseInt($('#_travelqty').val()) || 0,
        pd_days: parseInt($('#_pdqty').val()) || 0,
        payment_terms: $('#_terms').val(),
        pdf_file: pdfBase64
    };

    try {
        const response = await fetch("api/save_rate_sheet.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        // Check if the network request was successful
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        if (result.status === 'success') {
            console.log("Data saved successfully.");
            
            // Store the database ID in values so pdf_review knows if we are editing
            
            const finalId = editId || result.id;
            values.db_id = finalId; 
            // Store for preview page
            sessionStorage.setItem('pending_pdf_data', pdfBase64);
            sessionStorage.setItem('pending_pdf_meta', JSON.stringify(values));
            
            // Redirect to review page
            window.location.href = 'pdf_review.php';
        } else {
            console.error("Database error:", result.message);
            alert("Warning: PDF generated but failed to save to database: " + result.message);
        }
    } catch (err) {
        console.error("Critical error during save:", err);
        alert("An error occurred while saving. Please check the console.");
    }
}*/
async function saveAndOpenPdf(pdfBytes, values) {
    const editId = document.getElementById('edit_record_id').value;
    
    // 1. Create a real Blob from the PDF bytes (standard file handling)
    const pdfBlob = new Blob([pdfBytes], { type: 'application/pdf' });
    
    // 2. Use FormData instead of JSON to avoid "obfuscation" flags
    const formData = new FormData();
    formData.append('id', editId);
    formData.append('job_number', $('#_jobnum').val());
    formData.append('job_name', $('#_jobname').val());
    formData.append('job_location', $('#_jobloc').val());
    formData.append('pm_name', $('#_pm_name').val());
    formData.append('pm_contact', $('#_pm_contact').val());
    formData.append('load_in_date', $('#_jobdates').val());
    formData.append('load_out_date', $('#_jobdatee').val());
    formData.append('contractor_name', $('#_name').val());
    formData.append('contractor_dept', $('#_dept').val());
    formData.append('job_role', $('#_role').val());
    formData.append('contractor_phone', $('#_phone').val());
    formData.append('contractor_email', $('#_email').val());
    formData.append('pay_rate', parseFloat($('#_jobrate').val()) || 0);
    formData.append('per_diem_rate', parseFloat($('#_pdrate').val()) || 0);
    formData.append('additional_fee', parseFloat($('#text_22sqff').val()) || 0);
    formData.append('days_worked', parseInt($('#_rateqty').val()) || 0);
    formData.append('ot_days', parseInt($('#_otqty').val()) || 0);
    formData.append('travel_days', parseInt($('#_travelqty').val()) || 0);
    formData.append('pd_days', parseInt($('#_pdqty').val()) || 0);
    formData.append('payment_terms', $('#_terms').val());

    // 3. Attach the PDF as a physical file
    formData.append('pdf_file', pdfBlob, 'contract_preview.pdf');

    try {
        const response = await fetch("api/save_rate_sheet.php", {
            method: "POST",
            // Note: Do NOT set Content-Type header. The browser sets it to 
            // multipart/form-data with the correct boundary automatically.
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        if (result.status === 'success') {
            console.log("Data saved successfully.");
            
            // Map the final ID for navigation
            const finalId = editId || result.id;
            values.db_id = finalId; 

            // 4. Convert to Base64 ONLY for local sessionStorage (for the preview viewer)
            const reader = new FileReader();
            reader.onloadend = function() {
                const base64data = reader.result.split(',')[1];
                sessionStorage.setItem('pending_pdf_data', base64data);
                sessionStorage.setItem('pending_pdf_meta', JSON.stringify(values));
                
                // Redirect to review page after storage is ready
                window.location.href = 'pdf_review.php';
            };
            reader.readAsDataURL(pdfBlob);

        } else {
            console.error("Database error:", result.message);
            alert("Warning: PDF generated but failed to save to database: " + result.message);
        }
    } catch (err) {
        console.error("Critical error during save:", err);
        alert("An error occurred while saving. Please check the console.");
    }
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY&libraries=places&callback=initAutocomplete" async defer></script>
</body>
</html>