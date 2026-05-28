<?php 
$pageTitle = "AMS Apps - Home";
include 'includes/header.php'; 

// Check for existing request
$stmt = $pdo->prepare("SELECT status FROM pm_requests WHERE user_id = ? LIMIT 1");
$stmt->execute([$_SESSION['admin_user']['id']]);
$hasRequested = $stmt->fetch();
$isDisabled = ($hasRequested || $_SESSION['admin_user']['role'] !== 'user');
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function showNewQuoteForm() {
        document.getElementById('view-search').style.display = 'none';
        document.getElementById('view-new-quote').style.display = 'block';
        document.getElementById('modalTitle').innerText = 'New Quote Details';
    }
    
    function showSearchView() {
        document.getElementById('view-search').style.display = 'block';
        document.getElementById('view-new-quote').style.display = 'none';
        document.getElementById('modalTitle').innerText = 'Quote Manager';
    }
    
    // Live Search logic (Existing)
    document.getElementById('quoteSearch').addEventListener('input', function() {
        const query = this.value;
        const suggestionBox = document.getElementById('searchSuggestions');
        if (query.length < 2) { suggestionBox.style.display = 'none'; return; }
    
        fetch(`core/search_quotes.php?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                suggestionBox.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        const btn = document.createElement('button');
                        btn.className = 'list-group-item list-group-item-action small d-flex justify-content-between';
                        btn.innerHTML = `<span><strong>${item.show_name}</strong> <small class="text-muted">(${item.customer_name})</small></span>`;
                        btn.onclick = () => window.location.href = `quoting.php?showid=${item.id}`;
                        suggestionBox.appendChild(btn);
                    });
                    suggestionBox.style.display = 'block';
                } else { suggestionBox.style.display = 'none'; }
            });
    });
    
</script>
<div class="container py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold">AMS APPS</h2>
        <p class="text-muted">Select a tool to begin</p>
    </div>
    
    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="card card-soft clickable h-100 p-4 text-center" onclick="location.href='https://ams.flexrentalsolutions.com/f5/ui/?provider=microsoft&host_name=ams.flexrentalsolutions.com'">
                <div class="display-4 mb-3"><img src="https://ams.flexrentalsolutions.com/f5/ui/resources/images/favicon.ico"></div>
                    <h5 class="fw-bold">Flex Rental</h5>
                    <p class="small text-muted">Open and log in to AMS Flex</p>
            </div>
        </div>
        <div class="col-md-4">
            <!--div class="card card-soft h-100 p-4 text-center" style="opacity: 0.7; filter: grayscale(1); cursor: not-allowed; background: #1a1d21; color: white;"-->
            <div class="card card-soft clickable h-100 p-4 text-center" onclick="location.href='ledcalculator.php'">
                <!--span class="badge bg-primary position-absolute top-0 start-50 translate-middle">COMING SOON</span-->
                <div class="display-4 mb-3">🖥️</div>
                <h5 class="fw-bold">LED Screen Calculator</h5>
                <p class="small text-muted">Determine installation configurations and power requirements.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-soft clickable h-100 p-4 text-center" data-bs-toggle="modal" data-bs-target="#calcModal">
                <div class="display-4 mb-3">📽️️</div>
                <h5 class="fw-bold">Projection Calculator</h5>
                <p class="small text-muted">Determine installation configurations and power requirements.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-soft clickable h-100 p-4 text-center" data-bs-toggle="modal" data-bs-target="#receivingModal">
                    <div class="display-4 mb-3">📦</div>
                    <h5 class="fw-bold">Receiving</h5>
                    <p class="small text-muted">Scan packages received in the warehouse</p>
            </div>
        </div>
        
        
            <div class="text-center mb-5">
                <br>
                <h2 class="fw-bold">PM Tools</h2>
            </div>
        
        
        <?php if (in_array($_SESSION['admin_user']['role'], ['project_manager', 'admin'])): ?>
        <div class="col-md-4">
            <div class="card card-soft clickable h-100 p-4 text-center" onclick="location.href='generator.php'">
                <div class="display-4 mb-3">📝</div>
                <h5 class="fw-bold">Contract Generator</h5>
                <p class="small text-muted">Create and save PDF rate sheets for contractors.</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card card-soft clickable h-100 p-4 text-center" onclick="location.href='contract_history.php'">
                <div class="display-4 mb-3">📊</div>
                <h5 class="fw-bold">Contract History</h5>
                <p class="small text-muted">Review past generated contracts and user logs.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-soft clickable h-100 p-4 text-center" data-bs-toggle="modal" data-bs-target="#shirtLinkModal">
                <div class="display-4 mb-3">👕</div>
                <h5 class="fw-bold">Shirt Request Link</h5>
                <p class="small text-muted">Generate a custom link for contractors to submit sizes.</p>
            
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-soft clickable h-100 p-4 text-center" onclick="location.href='contractors.php'">
                    <div class="display-4 mb-3">👷</div>
                    <h5 class="fw-bold">Contractor Database</h5>
                    <p class="small text-muted">View, edit, and manage the master list of contractors and their default rates.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-soft clickable h-100 p-4 text-center" onclick="location.href='fuel.php'">
                    <div class="display-4 mb-3">⛽</div>
                    <h5 class="fw-bold">Fuel Rates</h5>
                    <p class="small text-muted">National average rate sheet for fuel.</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card card-soft clickable h-100 p-4 text-center" onclick="location.href='trucking.php'">
                    <div class="display-4 mb-3">⛟</div>
                    <h5 class="fw-bold">Truck Quoting</h5>
                    <p class="small text-muted">Send a trucking request over to CP2.</p>
            </div>
        </div>
         <div class="col-md-4">
            <div class="card card-soft clickable h-100 p-4 text-center" onclick="location.href='purchaseorder.php'">
                <div class="display-4 mb-3">🧾</div>
                <h5 class="fw-bold">PO Request</h5>
                <p class="small text-muted">Submit PO details for purchase approvals.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-soft h-100 p-4 text-center" style="opacity: 0.7; filter: grayscale(1); cursor: not-allowed; background: #1a1d21; color: white;">
            <!--div class="card card-soft clickable h-100 p-4 text-center" data-bs-toggle="modal" data-bs-target="#quoteLaunchModal"-->
                <div class="display-4 mb-3">📋</div>
                <h5 class="fw-bold">Quoting</h5>
                <p class="small text-muted">Manage equipment quotes and show details.</p>
            </div>
        </div>
       
        
        <?php endif; ?>
        
        <?php if (in_array($_SESSION['admin_user']['role'], ['user'])): ?>
        <div class="col-md-4">
            <div class="card card-soft h-100 p-4 text-center <?= $isDisabled ? 'bg-light text-muted' : 'clickable' ?>" 
                 <?= $isDisabled ? 'title="Request has been submitted" style="opacity: 0.6; cursor: not-allowed;"' : 'data-bs-toggle="modal" data-bs-target="#PMReqModal"' ?>>
                <div class="display-4 mb-3" style="<?= $isDisabled ? 'filter: grayscale(1);' : '' ?>">🔓️</div>
                <h5 class="fw-bold">Request PM access</h5>
                <p class="small">
                    <?= $isDisabled ? 'Request has been submitted' : 'Need Project Manager AMS tools? (Quoting, Labor, Etc)' ?>
                </p>
            </div>
        </div>
        <?php endif; ?>
        <?php if (in_array($_SESSION['admin_user']['role'], ['project_manager', 'admin'])): ?>
        <div class="modal fade" id="quoteLaunchModal" tabindex="-1">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="fw-bold" id="modalTitle">Quote Manager</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div id="view-search">
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">OPEN EXISTING QUOTE</label>
                                <div class="position-relative">
                                    <input type="text" id="quoteSearch" class="form-control" placeholder="Search by Show or Customer..." autocomplete="off">
                                    <div id="searchSuggestions" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; display: none;"></div>
                                </div>
                            </div>
                            
                            <div class="text-center py-3 border rounded bg-light">
                                <p class="small text-muted mb-2">Need to start something fresh?</p>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="showNewQuoteForm()">+ Create New Quote</button>
                            </div>
                        </div>
        
                        <div id="view-new-quote" style="display: none;">
                            <form action="api/create_quote.php" method="POST">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="small fw-bold">Customer Name</label>
                                        <input type="text" name="customer_name" class="form-control form-control-sm" placeholder="Client / Company" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="small fw-bold">Show Name</label>
                                        <input type="text" name="show_name" class="form-control form-control-sm" placeholder="e.g. Annual Gala 2026" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="small fw-bold">Venue Name</label>
                                        <input type="text" name="venue_name" class="form-control form-control-sm" placeholder="Location">
                                    </div>
                                    <div class="col-6">
                                        <label class="small fw-bold">Load In Date</label>
                                        <input type="date" name="start_date" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-6">
                                        <label class="small fw-bold">Load Out Date</label>
                                        <input type="date" name="end_date" class="form-control form-control-sm">
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2 mt-4">
                                    <button type="button" class="btn btn-light btn-sm flex-grow-1" onclick="showSearchView()">Back</button>
                                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Create Quote</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
        <?php $_SESSION['admin_user']?>
            <?php if (isset($_SESSION['admin_user']['role']) && $_SESSION['admin_user']['role'] === 'admin'): ?>
            <div class="text-center mb-5">
                <br>
                <h2 class="fw-bold">Admin Tools</h2>
            </div>
            <div class="col-md-4">
                <div class="card card-soft clickable h-100 p-4 text-center" onclick="location.href='admin_users.php'">
                    <div class="display-4 mb-3">👥</div>
                    <h5 class="fw-bold">User Management</h5>
                    <p class="small text-muted">Manage admin accounts, reset passwords, and toggle active status.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-soft clickable h-100 p-4 text-center" onclick="location.href='admin_inventory.php'">
                    <div class="display-4 mb-3">🖥️</div>
                    <h5 class="fw-bold">LED Tile Inventory</h5>
                    <p class="small text-muted">Add, edit and adjust LED Tiles</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div> </div> 

<div class="modal fade" id="calcModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="bg-light p-3 rounded mb-4 text-center">
                    <svg viewBox="0 0 500 150" width="100%" height="150">
                        <rect x="20" y="60" width="60" height="40" fill="#666" rx="5" />
                        <line x1="80" y1="80" x2="400" y2="80" stroke="#999" stroke-dasharray="5,5" />
                        <rect x="400" y="30" width="80" height="100" fill="#e9ecef" stroke="#333" />
                        <text x="50" y="120" text-anchor="middle" font-size="10" font-weight="bold">Throw Distance</text>
                        <text x="240" y="70" text-anchor="middle" font-size="14" font-weight="bold" id="svg-throw-val" fill="#0d6efd">0'</text>
                        <text x="440" y="145" text-anchor="middle" font-size="10" id="svg-dims">0' x 0'</text>
                    </svg>
                </div>

                <div class="row g-3">
                    <div class="col-4"><select id="calc-man" class="form-select" onchange="updateProjectors()"><option value="">Manufacturer</option></select></div>
                    <div class="col-4"><select id="calc-proj" class="form-select" onchange="updateLenses()"><option value="">Projector</option></select></div>
                    <div class="col-4"><select id="calc-lens" class="form-select" onchange="calculate()"><option value="">Lens</option></select></div>
                    
                    <div class="col-12 mt-3">
                        <label class="small fw-bold">Screen Width (Feet)</label>
                        <input type="number" id="calc-width" class="form-control" value="16" oninput="calculate()">
                    </div>

                    <div class="col-12 mt-3">
                        <label class="small fw-bold d-flex justify-content-between">
                            Zoom Range <span id="zoom-val">1x</span>
                        </label>
                        <input type="range" class="form-range" id="zoom-slider" min="0" max="100" value="0" oninput="calculate()" disabled>
                    </div>
                </div>

                <div id="result-box" class="mt-4 p-3 rounded bg-success text-white text-center d-none">
                    <div class="small text-uppercase opacity-75">Calculated Throw Distance</div>
                    <h3 id="final-throw-display" class="mb-0">0.0 ft</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let gearData = {};
let selectedLens = null;

// Fetch JSON and populate Manufacturers
fetch('assets/json/projectors.json').then(r => r.json()).then(data => gearData = data);

function updateProjectors() {
    const man = document.getElementById('calc-man').value;
    const pSelect = document.getElementById('calc-proj');
    pSelect.innerHTML = '<option value="">Projector</option>';
    if(man) Object.keys(gearData[man]).forEach(p => pSelect.innerHTML += `<option value="${p}">${p}</option>`);
}

function updateLenses() {
    const man = document.getElementById('calc-man').value;
    const proj = document.getElementById('calc-proj').value;
    const lSelect = document.getElementById('calc-lens');
    lSelect.innerHTML = '<option value="">Lens</option>';
    if(man && proj) gearData[man][proj].forEach((l, i) => lSelect.innerHTML += `<option value="${i}">${l.name}</option>`);
}

function calculate() {
    const w = parseFloat(document.getElementById('calc-width').value) || 0;
    const lensIdx = document.getElementById('calc-lens').value;
    const slider = document.getElementById('zoom-slider');
    
    if(lensIdx !== "") {
        const man = document.getElementById('calc-man').value;
        const proj = document.getElementById('calc-proj').value;
        selectedLens = gearData[man][proj][lensIdx];
        slider.disabled = false;
        
        // Calculate Ratio based on slider (0 to 100)
        const percent = slider.value / 100;
        const ratio = selectedLens.min + (percent * (selectedLens.max - selectedLens.min));
        const throwDist = (w * ratio).toFixed(1);
        
        // Update Zoom Text
        document.getElementById('zoom-val').innerText = (1 + percent).toFixed(2) + "x";
        
        // Update SVG and Result Box
        document.getElementById('svg-throw-val').innerText = throwDist + "'";
        document.getElementById('svg-dims').innerText = w + "' Width";
        document.getElementById('final-throw-display').innerText = throwDist + " ft";
        document.getElementById('result-box').classList.remove('d-none');
    }
}
</script>




<div class="modal fade" id="PMReqModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Elevate Account Level</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="pmRequestForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Requesting User</label>
                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($_SESSION['admin_user']['full_name']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Reason for Request</label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="Explain why you need PM access..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100 py-2" id="submitPMBtn">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('pmRequestForm').onsubmit = async (e) => {
    e.preventDefault();
    const btn = document.getElementById('submitPMBtn');
    btn.disabled = true;
    btn.innerText = "Sending...";

    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('api/submit_pm_request.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if(result.success) {
            location.reload(); // Reload to grey out the button
        } else {
            alert(result.message);
            btn.disabled = false;
        }
    } catch (err) {
        alert("Error sending request.");
        btn.disabled = false;
    }
};
</script>
<div class="modal fade" id="shirtLinkModal" tabindex="-1" aria-labelledby="shirtLinkModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="shirtLinkModalLabel">Generate Shirt Request Link</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3 text-start">
            <label class="form-label small fw-bold">Show / Client Name</label>
            <input type="text" id="showName" class="form-control mb-3" placeholder="e.g. Experior Orlando" onkeyup="updateLink()">
            
            <label class="form-label small fw-bold">Customized Link</label>
            <div class="input-group">
                <input type="text" id="generatedLink" class="form-control" readonly value="https://amsapps.io/shirts/index.php">
                <button class="btn btn-primary" type="button" onclick="copyLink()">Copy</button>
            </div>
            <div style="height: 20px;">
                <span id="copyFeedback" class="text-success small mt-1 fw-bold" style="display: none;">Copied to clipboard!</span>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="receivingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Shipment Receiving</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="row">
                    <div class="col-6 border-end">
                        <button class="btn btn-link text-decoration-none p-3" onclick="location.href='receiving_scan.php'">
                            <div class="display-6 mb-2">📸</div>
                            <h6 class="fw-bold mb-0">Scan Package</h6>
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-link text-decoration-none p-3" onclick="location.href='receiving_log.php'">
                            <div class="display-6 mb-2">📋</div>
                            <h6 class="fw-bold mb-0">View Log</h6>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function updateLink() {
    const show = document.getElementById('showName').value.trim();
    const baseUrl = "https://amsapps.io/shirts/index.php";
    const linkInput = document.getElementById('generatedLink');
    
    if (show !== "") {
        linkInput.value = `${baseUrl}?show=${encodeURIComponent(show)}`;
    } else {
        linkInput.value = baseUrl;
    }
}

function copyLink() {
    const copyText = document.getElementById("generatedLink");
    copyText.select();
    copyText.setSelectionRange(0, 99999); 
    navigator.clipboard.writeText(copyText.value);
    
    const feedback = document.getElementById('copyFeedback');
    feedback.style.display = 'inline-block';
    
    setTimeout(() => { 
        feedback.style.display = 'none'; 
    }, 2000);
}

// Reset the form when the modal is closed
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('shirtLinkModal');
    if(modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            document.getElementById('showName').value = '';
            updateLink(); 
            document.getElementById('copyFeedback').style.display = 'none';
        });
    }
});
</script>