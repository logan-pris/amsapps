<?php 
$pageTitle = "AMS Apps - Purchase Order Generator";
require_once __DIR__ . '/core/auth_check.php';
require_once __DIR__ . '/core/db.php'; 
include 'includes/header.php'; 
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --navy:  #1B3A6B;
      --steel: #2E5F9E;
      --light: #D9E5F5;
      --gray:  #F5F7FA;
      --border:#C8D4E8;
      --text:  #1A1A2E;
      --muted: #6B7A99;
      --white: #FFFFFF;
      --green: #1A6B3A;
      --lgreen:#D5F0E0;
      --red:   #C0392B;
      --lred:  #FDECEA;
    }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #EEF2F8;
      color: var(--text);
      min-height: 100vh;
    }

    /* ── TOP BAR ── */
    .topbar {
      background: var(--navy);
      color: var(--white);
      padding: 14px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 2px 12px rgba(27,58,107,0.3);
    }
    .topbar-brand { display: flex; flex-direction: column; }
    .topbar-brand .name { font-size: 20px; font-weight: 700; letter-spacing: 0.5px; }
    .topbar-brand .sub  { font-size: 12px; color: var(--light); margin-top: 1px; }
    .topbar-right { font-size: 12px; color: var(--light); text-align: right; line-height: 1.6; }
    .logo-upload-wrap { display:flex; align-items:center; gap:10px; }
    .logo-preview { width:60px; height:40px; object-fit:contain; border-radius:4px; display:none; background:rgba(255,255,255,0.15); }
    .logo-btn { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.35); color:#fff; font-size:11px; padding:5px 10px; border-radius:5px; cursor:pointer; white-space:nowrap; }
    .logo-btn:hover { background:rgba(255,255,255,0.25); }

    /* ── MAIN LAYOUT ── */
    .wrapper { max-width: 960px; margin: 28px auto 60px; padding: 0 16px; }

    /* ── CARD ── */
    .card {
      background: var(--white);
      border-radius: 10px;
      box-shadow: 0 2px 16px rgba(27,58,107,0.09);
      margin-bottom: 20px;
      overflow: hidden;
    }
    .card-header {
      background: var(--navy);
      color: var(--white);
      padding: 10px 20px;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 0.6px;
      text-transform: uppercase;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .card-header .icon { font-size: 15px; }
    .card-body { padding: 20px; }

    /* ── PO META ROW ── */
    .meta-row {
      display: flex;
      gap: 12px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }
    .meta-row .field { flex: 1; min-width: 160px; }

    /* ── FORM GRID ── */
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
    .col-span-2 { grid-column: span 2; }
    .col-span-3 { grid-column: span 3; }

    /* ── FIELD ── */
    .field label {
      display: block;
      font-size: 11px;
      font-weight: 700;
      color: var(--navy);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 5px;
    }
    .field input, .field select, .field textarea {
      width: 100%;
      padding: 9px 12px;
      border: 1.5px solid var(--border);
      border-radius: 6px;
      font-size: 13.5px;
      font-family: inherit;
      color: var(--text);
      background: var(--white);
      transition: border-color 0.15s, box-shadow 0.15s;
      outline: none;
    }
    .field input:focus, .field select:focus, .field textarea:focus {
      border-color: var(--steel);
      box-shadow: 0 0 0 3px rgba(46,95,158,0.12);
    }
    .field input.error, .field select.error, .field textarea.error {
      border-color: var(--red);
      background: var(--lred);
    }
    .field textarea { resize: vertical; min-height: 72px; }
    .field select { cursor: pointer; }

    /* ── CATEGORY CHIPS ── */
    .chip-group { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 2px; }
    .chip {
      display: flex; align-items: center; gap: 6px;
      padding: 7px 14px;
      border: 1.5px solid var(--border);
      border-radius: 20px;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.15s;
      user-select: none;
      background: var(--white);
    }
    .chip input[type="radio"] { display: none; }
    .chip:hover { border-color: var(--steel); background: var(--light); }
    .chip.selected { border-color: var(--navy); background: var(--light); color: var(--navy); font-weight: 600; }
    .chip .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--muted); transition: background 0.15s; }
    .chip.selected .dot { background: var(--navy); }

    /* ── LINE ITEMS TABLE ── */
    .line-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .line-table thead tr { background: var(--navy); }
    .line-table thead th {
      color: var(--white);
      padding: 9px 10px;
      text-align: left;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      white-space: nowrap;
    }
    .line-table thead th:last-child { text-align: center; }
    .line-table tbody tr { border-bottom: 1px solid var(--border); }
    .line-table tbody tr:nth-child(even) { background: var(--gray); }
    .line-table tbody td { padding: 6px 6px; vertical-align: middle; }
    .line-table tbody td input {
      width: 100%;
      padding: 7px 9px;
      border: 1.5px solid transparent;
      border-radius: 5px;
      font-size: 13px;
      font-family: inherit;
      background: transparent;
      color: var(--text);
      transition: border-color 0.15s, background 0.15s;
      outline: none;
    }
    .line-table tbody td input:focus {
      border-color: var(--steel);
      background: var(--white);
      box-shadow: 0 0 0 2px rgba(46,95,158,0.1);
    }
    .line-num { color: var(--muted); font-size: 12px; text-align: center; font-weight: 600; padding: 0 8px; }
    .line-total { font-weight: 600; color: var(--navy); padding: 0 10px; white-space: nowrap; }
    .delete-btn {
      background: none; border: none; cursor: pointer;
      color: var(--muted); font-size: 16px; padding: 4px 8px;
      border-radius: 4px; transition: color 0.15s, background 0.15s;
      display: block; margin: auto;
    }
    .delete-btn:hover { color: var(--red); background: var(--lred); }

    /* ── ADD ROW BUTTON ── */
    .add-row-btn {
      margin-top: 10px;
      background: none;
      border: 1.5px dashed var(--border);
      border-radius: 6px;
      color: var(--steel);
      font-size: 13px;
      font-weight: 600;
      padding: 9px 18px;
      cursor: pointer;
      width: 100%;
      transition: all 0.15s;
    }
    .add-row-btn:hover { border-color: var(--steel); background: var(--light); }

    /* ── TOTALS ── */
    .totals { margin-top: 14px; display: flex; justify-content: flex-end; }
    .totals-table { width: 320px; border-collapse: collapse; }
    .totals-table td { padding: 6px 12px; font-size: 13.5px; }
    .totals-table tr:not(:last-child) td { border-bottom: 1px solid var(--border); }
    .totals-table .label { color: var(--muted); text-align: right; }
    .totals-table .value { font-weight: 600; text-align: right; color: var(--text); }
    .totals-table .total-row { background: var(--light); }
    .totals-table .total-row .label { color: var(--navy); font-weight: 700; font-size: 14px; }
    .totals-table .total-row .value { color: var(--navy); font-weight: 700; font-size: 15px; }
    .totals-table .adj-input {
      width: 100%; padding: 4px 8px; border: 1.5px solid var(--border);
      border-radius: 4px; font-size: 13px; font-family: inherit;
      text-align: right; outline: none;
    }
    .totals-table .adj-input:focus { border-color: var(--steel); }

    /* ── QUOTES SECTION ── */
    .check-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .check-item {
      display: flex; align-items: center; gap: 8px;
      padding: 8px 12px; border: 1.5px solid var(--border);
      border-radius: 6px; cursor: pointer; transition: all 0.15s;
    }
    .check-item:hover { border-color: var(--steel); background: var(--light); }
    .check-item input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; accent-color: var(--navy); }
    .check-item label { font-size: 13px; cursor: pointer; }

    /* ── ACTION BAR ── */
    .action-bar {
      background: var(--white);
      border-top: 2px solid var(--light);
      padding: 18px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-radius: 10px;
      box-shadow: 0 2px 16px rgba(27,58,107,0.09);
      flex-wrap: wrap;
      gap: 12px;
    }
    .action-hint { font-size: 12.5px; color: var(--muted); line-height: 1.5; }
    .action-hint strong { color: var(--navy); }
    .btn-group { display: flex; gap: 10px; }

    .btn {
      padding: 11px 24px;
      border-radius: 7px;
      font-size: 14px;
      font-weight: 700;
      font-family: inherit;
      cursor: pointer;
      border: none;
      transition: all 0.15s;
      display: flex; align-items: center; gap: 8px;
    }
    .btn-outline {
      background: none;
      border: 1.5px solid var(--border);
      color: var(--muted);
    }
    .btn-outline:hover { border-color: var(--steel); color: var(--steel); background: var(--light); }
    .btn-primary {
      background: var(--navy);
      color: var(--white);
      box-shadow: 0 3px 10px rgba(27,58,107,0.25);
    }
    .btn-primary:hover { background: var(--steel); box-shadow: 0 4px 14px rgba(46,95,158,0.3); transform: translateY(-1px); }
    .btn-primary:active { transform: translateY(0); }
    .btn-primary:disabled { background: var(--muted); box-shadow: none; cursor: not-allowed; transform: none; }

    /* ── TOAST ── */
    #toast {
      position: fixed; bottom: 28px; right: 28px;
      padding: 14px 22px; border-radius: 8px;
      font-size: 14px; font-weight: 600; color: var(--white);
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      transform: translateY(80px); opacity: 0;
      transition: all 0.3s ease;
      z-index: 999; pointer-events: none;
    }
    #toast.show { transform: translateY(0); opacity: 1; }
    #toast.success { background: var(--green); }
    #toast.error   { background: var(--red); }

    /* ── STATUS BADGE ── */
    /* ── EMERGENCY TOGGLE ── */
    .emergency-toggle {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 14px;
      border: 1.5px solid var(--border);
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.15s;
      background: var(--white);
      margin-top: 2px;
    }
    .emergency-toggle:hover { border-color: var(--red); background: var(--lred); }
    .emergency-toggle.active { border-color: var(--red); background: var(--lred); }
    .emergency-toggle input[type="checkbox"] { width: 17px; height: 17px; accent-color: var(--red); cursor: pointer; }
    .emergency-toggle .emg-label { font-size: 13px; font-weight: 700; color: var(--red); }
    .emergency-toggle .emg-sub { font-size: 11.5px; color: var(--muted); margin-left: 2px; }

    .emergency-fields {
      display: none;
      margin-top: 14px;
      padding: 16px;
      background: var(--lred);
      border: 1.5px solid #E8AAAA;
      border-radius: 8px;
      grid-column: 1 / -1;
    }
    .emergency-fields.show { display: block; }
    .emergency-fields .emg-title {
      font-size: 12px; font-weight: 700; color: var(--red);
      text-transform: uppercase; letter-spacing: 0.5px;
      margin-bottom: 12px;
      display: flex; align-items: center; gap: 6px;
    }
    .emergency-fields .grid-2 { background: transparent; }

    /* ── TAX REASON ── */
    .tax-reason-wrap {
      display: none;
      margin-top: 12px;
      padding: 12px 14px;
      background: #FFF8E1;
      border: 1.5px solid #E6A817;
      border-radius: 8px;
    }
    .tax-reason-wrap.show { display: block; }
    .tax-reason-wrap label {
      display: block; font-size: 11px; font-weight: 700;
      color: #7A5200; text-transform: uppercase;
      letter-spacing: 0.5px; margin-bottom: 5px;
    }

    @media (max-width: 640px) {
      .grid-2, .grid-3 { grid-template-columns: 1fr; }
      .col-span-2, .col-span-3 { grid-column: span 1; }
      .check-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<div class="wrapper">

  <!-- ── PO META ── -->
  <div class="card">
    <div class="card-header"><span class="icon">📋</span> Purchase Order Details</div>
    <div class="card-body">
      <div class="grid-3">
        <div class="field">
          <label>Date of Request *</label>
          <input type="date" id="req_date" />
        </div>
        <div class="field">
          <label>Required By Date *</label>
          <input type="date" id="req_by" />
        </div>
        <div class="field">
          <label>Emergency PO Request</label>
          <label class="emergency-toggle" id="emg_toggle" onclick="toggleEmergency()">
            <input type="checkbox" id="emg_check" onclick="event.stopPropagation(); toggleEmergency()" />
            <span class="emg-label">🚨 Emergency Purchase</span>
            <span class="emg-sub">Verbal approval required first</span>
          </label>
        </div>

        <!-- Emergency expanded fields -->
        <div class="emergency-fields" id="emg_fields">
          <div class="emg-title">🚨 Emergency Purchase Details — Verbal Approval Must Be Obtained Before Purchase</div>
          <div class="grid-2">
            <div class="field">
              <label>Verbal Approver Name *</label>
              <input type="text" id="emg_approver" placeholder="Who authorized this verbally" />
            </div>
            <div class="field">
              <label>Approver Title / Role *</label>
              <input type="text" id="emg_role" placeholder="e.g. Accounting Manager" />
            </div>
            <div class="field">
              <label>Date & Time of Verbal Approval *</label>
              <input type="datetime-local" id="emg_datetime" />
            </div>
            <div class="field">
              <label>Reason for Emergency *</label>
              <input type="text" id="emg_reason" placeholder="Why standard process couldn't be followed" />
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- ── REQUESTOR ── -->
  <div class="card">
    <div class="card-header"><span class="icon">👤</span> Requestor Information</div>
    <div class="card-body">
      <div class="grid-3">
        <div class="field col-span-2">
          <label>Full Name / Title *</label>
          <input type="text" id="req_name" placeholder="e.g. Jane Smith, Project Manager" />
        </div>
        <div class="field">
          <label>Department</label>
          <input type="text" id="req_dept" placeholder="e.g. Production" />
        </div>
        <div class="field">
          <label>Phone *</label>
          <input type="tel" id="req_phone" placeholder="(xxx) xxx-xxxx" />
        </div>
        <div class="field">
          <label>Email *</label>
          <input type="email" id="req_email" placeholder="you@ams.events" />
        </div>
        <div class="field col-span-3">
          <label>Purchase Type *</label>
          <div class="chip-group" id="purchase_type_chips">
            <label class="chip" id="pt_capex">
              <input type="radio" name="purchase_type" value="Capital Expense" onchange="updatePurchaseTypeChips()" />
              <span class="dot"></span> Capital Expense Purchase
            </label>
            <label class="chip" id="pt_show">
              <input type="radio" name="purchase_type" value="Project/Show" onchange="updatePurchaseTypeChips()" />
              <span class="dot"></span> Project / Show Purchase
            </label>
          </div>
        </div>

        <div class="field col-span-2" id="project_name_wrap" style="display:none;">
          <label>Project / Show Name *</label>
          <input type="text" id="req_project" placeholder="Show Name - ##-#####" />
        </div>
      </div>
    </div>
  </div>

  <!-- ── VENDOR ── -->
  <div class="card">
    <div class="card-header"><span class="icon">🏢</span> Vendor / Supplier Information</div>
    <div class="card-body">
      <div class="grid-2">
        <div class="field">
          <label>Vendor Name *</label>
          <input type="text" id="vend_name" placeholder="Supplier company name" />
        </div>
        <div class="field">
          <label>Vendor Contact Person</label>
          <input type="text" id="vend_contact" placeholder="Name of contact at vendor" />
        </div>
        <div class="field">
          <label>Vendor Phone / Email</label>
          <input type="text" id="vend_phone" placeholder="Phone and/or email" />
        </div>
        <div class="field">
          <label>Payment Terms</label>
          <select id="vend_terms">
            <option value="">Select or type below</option>
            <option>Prepayment / COD</option>
            <option>Net 15</option>
            <option>Net 30</option>
            <option>Net 45</option>
            <option>Net 60</option>
            <option>Custom / Split</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- ── SHIP TO ── -->
  <div class="card">
    <div class="card-header"><span class="icon">📦</span> Ship To / Delivery Information</div>
    <div class="card-body">
      <div class="grid-2">
        <div class="field">
          <label>Deliver To (Name / Location)</label>
          <input type="text" id="ship_to" placeholder="Who or where is receiving" />
        </div>
        <div class="field">
          <label>Shipping Method</label>
          <select id="ship_method">
            <option value="">— Select —</option>
            <option>Ground</option>
            <option>Freight</option>
            <option>Overnight</option>
            <option>2-Day</option>
            <option>Will Call / Pickup</option>
            <option>Hand Delivery</option>
          </select>
        </div>
        <div class="field col-span-2">
          <label>Delivery Address</label>
          <input type="text" id="ship_addr" placeholder="3200 Commander DR STE 110, Carrollton, TX 75006 (or other)" />
        </div>
        <div class="field">
          <label>Estimated Delivery Date</label>
          <input type="date" id="ship_eta" />
        </div>
      </div>
    </div>
  </div>

  <!-- ── LINE ITEMS ── -->
  <div class="card">
    <div class="card-header"><span class="icon">🗒️</span> Line Items</div>
    <div class="card-body">
      <div style="overflow-x:auto;">
        <table class="line-table">
          <thead>
            <tr>
              <th style="width:40px;">#</th>
              <th>Item Description *</th>
              <th style="width:110px;">SKU / Part #</th>
              <th style="width:70px;">Qty *</th>
              <th style="width:120px;">Unit Price ($) *</th>
              <th style="width:120px;">Line Total</th>
              <th style="width:44px;text-align:center;">Del</th>
            </tr>
          </thead>
          <tbody id="line_items_body"></tbody>
        </table>
      </div>
      <button class="add-row-btn" onclick="addRow()">+ Add Line Item</button>

      <div class="totals">
        <table class="totals-table">
          <tr>
            <td class="label">Subtotal</td>
            <td class="value" id="subtotal_val">$0.00</td>
          </tr>
          <tr>
            <td class="label">Shipping & Handling ($)</td>
            <td class="value"><input class="adj-input" id="shipping_val" type="number" min="0" step="0.01" placeholder="0.00" oninput="recalcTotals()" /></td>
          </tr>
          <tr>
            <td class="label">Tax ($)</td>
            <td class="value"><input class="adj-input" id="tax_val" type="number" min="0" step="0.01" placeholder="0.00" oninput="recalcTotals(); checkTaxReason()" /></td>
          </tr>
          <tr class="total-row">
            <td class="label">TOTAL AMOUNT</td>
            <td class="value" id="total_val">$0.00</td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <!-- ── NOTES ── -->
  <div class="card">
    <div class="card-header"><span class="icon">📝</span> Notes / Justification</div>
    <div class="card-body">
      <div class="field">
        <label>Justification for this Purchase</label>
        <textarea id="notes" placeholder="Provide a brief justification for this purchase."></textarea>
      </div>
      <!-- Tax reason — shown only when tax > 0 -->
      <div class="tax-reason-wrap" id="tax_reason_wrap">
        <label>⚠️ Reason for Tax Applied *</label>
        <textarea id="tax_reason" placeholder="Explain why tax is being applied to this purchase (e.g., out-of-state vendor, taxable goods category, etc.)" style="width:100%;padding:9px 12px;border:1.5px solid #E6A817;border-radius:6px;font-size:13.5px;font-family:inherit;background:#FFFDE7;outline:none;resize:vertical;min-height:60px;"></textarea>
      </div>
    </div>
  </div>

  <!-- ── ACTION BAR ── -->
  <div class="action-bar">
    <div class="action-hint">
      <strong>Ready to submit?</strong> Click "Download PO" to generate your formatted PDF document.<br>
      Email the completed PO to <strong>accounting@ams.events</strong> with subject: <em>"PO Request — [Project] — [Date]"</em>
    </div>
    <div class="btn-group">
      <button class="btn btn-outline" onclick="resetForm()">🔄 Reset Form</button>
      <button class="btn btn-primary" id="download_btn" onclick="generatePO()">⬇️ Download PO (.pdf)</button>
    </div>
  </div>

</div>

<!-- TOAST -->
<div id="toast"></div>

<script>
// ═══════════════════════════════════════════════
// UTILITY
// ═══════════════════════════════════════════════
let rowCount = 0;

function fmt(n) {
  if (isNaN(n) || n === '' || n === null) return '$0.00';
  return Number(n).toLocaleString('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
function fmtNum(n) {
  if (isNaN(n) || n === '' || n === null) return '0.00';
  return Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
function val(id) { return (document.getElementById(id) || {}).value || ''; }
function checked(id) { return document.getElementById(id)?.checked ? '✔' : '☐'; }

function toast(msg, type = 'success') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'show ' + type;
  setTimeout(() => { t.className = ''; }, 3000);
}

// ═══════════════════════════════════════════════
// EMERGENCY PO TOGGLE
// ═══════════════════════════════════════════════
function toggleEmergency() {
  const cb     = document.getElementById('emg_check');
  const toggle = document.getElementById('emg_toggle');
  const fields = document.getElementById('emg_fields');
  // Sync checkbox state (may be called from either the label or the checkbox itself)
  const isOn = cb.checked;
  toggle.className = 'emergency-toggle' + (isOn ? ' active' : '');
  fields.className = 'emergency-fields' + (isOn ? ' show' : '');
  if (!isOn) {
    ['emg_approver','emg_role','emg_datetime','emg_reason'].forEach(id => {
      const el = document.getElementById(id);
      if (el) { el.value = ''; el.classList.remove('error'); }
    });
  }
}

// ═══════════════════════════════════════════════
// TAX REASON VISIBILITY
// ═══════════════════════════════════════════════
function checkTaxReason() {
  const tv  = parseFloat(document.getElementById('tax_val').value) || 0;
  const wrap = document.getElementById('tax_reason_wrap');
  wrap.className = 'tax-reason-wrap' + (tv > 0 ? ' show' : '');
  if (tv <= 0) document.getElementById('tax_reason').value = '';
}

// ═══════════════════════════════════════════════
// PURCHASE TYPE CHIPS
// ═══════════════════════════════════════════════
function updatePurchaseTypeChips() {
  const v = document.querySelector('input[name="purchase_type"]:checked')?.value;
  document.getElementById('pt_capex').className = 'chip' + (v === 'Capital Expense' ? ' selected' : '');
  document.getElementById('pt_show').className  = 'chip' + (v === 'Project/Show'    ? ' selected' : '');
  const wrap = document.getElementById('project_name_wrap');
  wrap.style.display = (v === 'Project/Show') ? '' : 'none';
  if (v !== 'Project/Show') {
    const el = document.getElementById('req_project');
    if (el) { el.value = ''; el.classList.remove('error'); }
  }
}

// ═══════════════════════════════════════════════
// CATEGORY DROPDOWN
// ═══════════════════════════════════════════════
function updateCatChips() {
  const v = document.getElementById('category_select').value;
  const otherWrap = document.getElementById('cat_other_wrap');
  otherWrap.style.display = (v === 'Other') ? 'block' : 'none';
  if (v !== 'Other') document.getElementById('cat_other_text').value = '';
}

// ═══════════════════════════════════════════════
// LINE ITEMS
// ═══════════════════════════════════════════════
function addRow() {
  rowCount++;
  const tbody = document.getElementById('line_items_body');
  const tr = document.createElement('tr');
  tr.id = 'row_' + rowCount;
  tr.innerHTML = `
    <td class="line-num">${rowCount}</td>
    <td><input type="text" placeholder="Item description" class="desc" /></td>
    <td><input type="text" placeholder="SKU" style="max-width:100px;" /></td>
    <td><input type="number" min="0" step="1" placeholder="0" class="qty" oninput="calcRow(this)" style="max-width:70px;" /></td>
    <td><input type="number" min="0" step="0.01" placeholder="0.00" class="price" oninput="calcRow(this)" style="max-width:110px;" /></td>
    <td class="line-total" id="lt_${rowCount}">$0.00</td>
    <td><button class="delete-btn" onclick="deleteRow(${rowCount})" title="Remove row">✕</button></td>
  `;
  tbody.appendChild(tr);
}

function calcRow(input) {
  const row = input.closest('tr');
  const qty   = parseFloat(row.querySelector('.qty').value)   || 0;
  const price = parseFloat(row.querySelector('.price').value) || 0;
  const total = qty * price;
  row.querySelector('.line-total').textContent = fmt(total);
  recalcTotals();
}

function deleteRow(id) {
  const row = document.getElementById('row_' + id);
  if (row) row.remove();
  recalcTotals();
  renumberRows();
}

function renumberRows() {
  const rows = document.querySelectorAll('#line_items_body tr');
  rows.forEach((r, i) => {
    const numCell = r.querySelector('.line-num');
    if (numCell) numCell.textContent = i + 1;
  });
}

function recalcTotals() {
  let sub = 0;
  document.querySelectorAll('#line_items_body tr').forEach(row => {
    const qty   = parseFloat(row.querySelector('.qty')?.value)   || 0;
    const price = parseFloat(row.querySelector('.price')?.value) || 0;
    sub += qty * price;
  });
  const ship = parseFloat(document.getElementById('shipping_val').value) || 0;
  const tax  = parseFloat(document.getElementById('tax_val').value) || 0;
  document.getElementById('subtotal_val').textContent = fmt(sub);
  document.getElementById('total_val').textContent = fmt(sub + ship + tax);
}

// ═══════════════════════════════════════════════
// RESET
// ═══════════════════════════════════════════════
function resetForm() {
  if (!confirm('Reset all form fields? This cannot be undone.')) return;
  document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="date"], input[type="datetime-local"], input[type="number"], select, textarea')
    .forEach(el => { el.value = ''; el.classList.remove('error'); });
  document.getElementById('line_items_body').innerHTML = '';
  rowCount = 0;
  document.getElementById('subtotal_val').textContent = '$0.00';
  document.getElementById('total_val').textContent = '$0.00';
  document.querySelectorAll('input[type="checkbox"]').forEach(c => c.checked = false);
  document.querySelectorAll('input[name="purchase_type"]').forEach(r => r.checked = false);
  document.getElementById('emg_check').checked = false;
  document.getElementById('category_select').value = '';
  toggleEmergency();
  updateCatChips();
  updatePurchaseTypeChips();
  checkTaxReason();
  document.getElementById('req_date').value = new Date().toISOString().split('T')[0];
  addRow(); addRow(); addRow();
  toast('Form reset.', 'success');
}

// ═══════════════════════════════════════════════
// VALIDATION
// ═══════════════════════════════════════════════
function validate() {
  let ok = true;
  const required = ['req_date','req_by','req_name','req_phone','req_email','vend_name'];
  required.forEach(id => {
    const el = document.getElementById(id);
    if (!el || !el.value.trim()) { el && el.classList.add('error'); ok = false; }
    else el.classList.remove('error');
  });

  // Purchase type must be selected
  const purchaseType = document.querySelector('input[name="purchase_type"]:checked')?.value;
  if (!purchaseType) {
    document.getElementById('pt_capex').style.borderColor = 'var(--red)';
    document.getElementById('pt_show').style.borderColor  = 'var(--red)';
    ok = false;
  } else {
    document.getElementById('pt_capex').style.borderColor = '';
    document.getElementById('pt_show').style.borderColor  = '';
  }

  // Project/Show Name only required when Project/Show is selected
  if (purchaseType === 'Project/Show') {
    const el = document.getElementById('req_project');
    if (!el || !el.value.trim()) { el && el.classList.add('error'); ok = false; }
    else el.classList.remove('error');
  }

  // If emergency PO, require emergency fields
  if (document.getElementById('emg_check').checked) {
    ['emg_approver','emg_role','emg_datetime','emg_reason'].forEach(id => {
      const el = document.getElementById(id);
      if (!el || !el.value.trim()) { el && el.classList.add('error'); ok = false; }
      else el.classList.remove('error');
    });
    if (!ok) { toast('Please complete all Emergency PO fields.', 'error'); return false; }
  }

  // If tax entered, require reason
  const tv = parseFloat(document.getElementById('tax_val').value) || 0;
  if (tv > 0) {
    const tr = document.getElementById('tax_reason');
    if (!tr.value.trim()) { tr.style.borderColor = 'var(--red)'; toast('Please enter a reason for tax applied.', 'error'); return false; }
    else tr.style.borderColor = '#E6A817';
  }

  // at least one line item with desc + qty + price
  const rows = document.querySelectorAll('#line_items_body tr');
  let hasItem = false;
  rows.forEach(row => {
    const desc  = row.querySelector('.desc')?.value.trim();
    const qty   = parseFloat(row.querySelector('.qty')?.value);
    const price = parseFloat(row.querySelector('.price')?.value);
    if (desc && !isNaN(qty) && qty > 0 && !isNaN(price) && price >= 0) hasItem = true;
  });
  if (!ok) { toast('Please fill in all required fields.', 'error'); return false; }
  if (!hasItem) { toast('Add at least one complete line item.', 'error'); return false; }
  return true;
}

// ═══════════════════════════════════════════════
// PDF GENERATION
// ═══════════════════════════════════════════════
async function generatePO() {
  if (!validate()) return;

  const btn = document.getElementById('download_btn');
  btn.disabled = true;
  btn.textContent = '⏳ Generating...';

  try {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ unit: 'mm', format: 'letter' });

    // ── Colors (RGB arrays) ─────────────────────────────────────────────────
    const NAVY  = [27,  58,  107];
    const STEEL = [46,  95,  158];
    const LIGHT = [217, 229, 245];
    const WHITE = [255, 255, 255];
    const GRAY  = [245, 247, 250];
    const BLACK = [26,  26,  46];
    const MGRAY = [200, 212, 232];
    const AMBER = [255, 248, 225];
    const AMBERT= [122, 82,  0  ];
    const DKRED = [107, 26,  26 ];
    const LTRED = [245, 213, 213];

    // ── Page metrics ────────────────────────────────────────────────────────
    const PW = doc.internal.pageSize.getWidth();
    const ML = 13, MR = 13;
    const CW = PW - ML - MR;

    // ── Collect form values ──────────────────────────────────────────────────
    const purchaseType = document.querySelector('input[name="purchase_type"]:checked')?.value || '';

    // Emergency PO data
    const isEmergency = document.getElementById('emg_check').checked;
    const emgApprover = val('emg_approver');
    const emgRole     = val('emg_role');
    const emgDatetime = val('emg_datetime').replace('T', ' at ');
    const emgReason   = val('emg_reason');
    const taxReason   = val('tax_reason');
    const notes       = val('notes');

    // Line items
    const lineRows = [];
    let subtotal = 0;
    document.querySelectorAll('#line_items_body tr').forEach((row, i) => {
      const desc  = row.querySelector('.desc')?.value.trim()       || '';
      const sku   = row.querySelectorAll('input')[1]?.value.trim() || '';
      const qty   = parseFloat(row.querySelector('.qty')?.value)   || 0;
      const price = parseFloat(row.querySelector('.price')?.value) || 0;
      const lineT = qty * price;
      subtotal += lineT;
      lineRows.push([String(i + 1), desc || '\u2014', sku || '\u2014', qty ? String(qty) : '\u2014', price ? fmt(price) : '\u2014', fmt(lineT)]);
    });
    const shipping = parseFloat(document.getElementById('shipping_val').value) || 0;
    const tax      = parseFloat(document.getElementById('tax_val').value) || 0;
    const total    = subtotal + shipping + tax;

    // ── Shared table styles ─────────────────────────────────────────────────
    const tblS = {
      fontSize: 8.5, overflow: 'linebreak',
      cellPadding: { top: 2.5, bottom: 2.5, left: 3, right: 3 },
      lineColor: MGRAY, lineWidth: 0.2, textColor: BLACK,
    };
    const lblCol = { fillColor: LIGHT, fontStyle: 'bold', textColor: NAVY };
    const mg     = { left: ML, right: MR };
    const lw = Math.round(CW * 0.20);
    const vw = Math.round(CW * 0.30);

    // ── Section bar helper ──────────────────────────────────────────────────
    function sectionBar(label, yPos) {
      doc.setFillColor(...NAVY);
      doc.rect(ML, yPos, CW, 7.5, 'F');
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(9);
      doc.setTextColor(...WHITE);
      doc.text(label, ML + 3.5, yPos + 5.1);
      return yPos + 7.5;
    }

    // ── HEADER ─────────────────────────────────────────────────────────────
    let y = 10;

    // Navy header band
    doc.setFillColor(...NAVY);
    doc.rect(0, 0, PW, 30, 'F');

    // Left: company name + address
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(18);
    doc.setTextColor(...WHITE);
    doc.text('AMS EVENTS LLC', ML, y + 8);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(7.5);
    doc.setTextColor(200, 215, 240);
    doc.text('3200 Commander DR STE 110, Carrollton, TX 75006', ML, y + 14);
    doc.text('972-570-1118  |  accounting@ams.events  |  www.ams.events', ML, y + 19);

    // Right: logo (if uploaded) + PO label
    const logoAreaW = 55;
    const logoX = PW - MR - logoAreaW;

    if (logoDataUrl) {
      // Draw logo in top-right of header
      try {
        doc.addImage(logoDataUrl, logoMimeType, logoX, 3, logoAreaW, 24);
      } catch(e) {
        // fallback to text if image fails
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(11);
        doc.setTextColor(...WHITE);
        doc.text('AMS EVENTS', PW - MR, y + 8, { align: 'right' });
      }
    } else {
      // Styled text badge in lieu of logo
      doc.setFillColor(46, 95, 158);
      doc.roundedRect(logoX, 5, logoAreaW, 20, 2, 2, 'F');
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(9);
      doc.setTextColor(...WHITE);
      doc.text('AMS EVENTS', logoX + logoAreaW / 2, 13, { align: 'center' });
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(7);
      doc.text('ams.events', logoX + logoAreaW / 2, 20, { align: 'center' });
    }

    y = 33;

    // PO title strip (light blue)
    doc.setFillColor(...LIGHT);
    doc.rect(ML, y, CW, 10, 'F');
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(13);
    doc.setTextColor(...NAVY);
    doc.text('PURCHASE ORDER', ML + 4, y + 7);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(8.5);
    doc.setTextColor(...STEEL);
    doc.text('PO #: (Assigned by Accounting)', PW - MR, y + 7, { align: 'right' });
    y += 13;

    // Status + emergency badge
    if (isEmergency) {
      doc.setFillColor(...LTRED);
      doc.rect(ML, y, CW, 7, 'F');
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(8);
      doc.setTextColor(...DKRED);
      doc.text('[EMERGENCY PURCHASE]  —  Status: Pending Approval', ML + 4, y + 4.8);
    } else {
      doc.setFillColor(240, 244, 250);
      doc.rect(ML, y, CW, 7, 'F');
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(8);
      doc.setTextColor(...STEEL);
      doc.text('Status: Pending Approval', ML + 4, y + 4.8);
    }
    y += 9;

    doc.setDrawColor(...NAVY);
    doc.setLineWidth(0.5);
    doc.line(ML, y, PW - MR, y);
    y += 4;

    // ── PO META ROW ─────────────────────────────────────────────────────────
    doc.autoTable({
      startY: y, margin: mg, tableWidth: CW,
      body: [['Date of Request', val('req_date') || '\u2014', 'Required By', val('req_by') || '\u2014', 'PENDING']],
      theme: 'grid', styles: { ...tblS },
      columnStyles: {
        0: { ...lblCol, cellWidth: 36 },
        1: { cellWidth: 46 },
        2: { ...lblCol, cellWidth: 34 },
        3: { cellWidth: 44 },
        4: { cellWidth: CW - 160, fillColor: AMBER, fontStyle: 'bold', textColor: AMBERT, halign: 'center' },
      },
    });
    y = doc.lastAutoTable.finalY + 3;

    // ── REQUESTOR INFORMATION ────────────────────────────────────────────────
    y = sectionBar('REQUESTOR INFORMATION', y);
    doc.autoTable({
      startY: y, margin: mg, tableWidth: CW,
      body: [
        ['Name / Title', val('req_name') || '\u2014', 'Department', val('req_dept') || '\u2014'],
        ['Phone', val('req_phone') || '\u2014', 'Email', val('req_email') || '\u2014'],
        ['Purchase Type', { content: purchaseType || '\u2014', colSpan: 3 }],
        ...(purchaseType === 'Project/Show' ? [['Project / Show', { content: val('req_project') || '\u2014', colSpan: 3 }]] : []),
      ],
      theme: 'grid', styles: { ...tblS },
      columnStyles: {
        0: { ...lblCol, cellWidth: lw },
        1: { cellWidth: vw },
        2: { ...lblCol, cellWidth: lw },
        3: { cellWidth: vw },
      },
    });
    y = doc.lastAutoTable.finalY + 3;

    // ── VENDOR INFORMATION ────────────────────────────────────────────────────
    y = sectionBar('VENDOR / SUPPLIER INFORMATION', y);
    doc.autoTable({
      startY: y, margin: mg, tableWidth: CW,
      body: [
        ['Vendor Name', val('vend_name') || '\u2014', 'Vendor Contact', val('vend_contact') || '\u2014'],
        ['Phone / Email', val('vend_phone') || '\u2014', 'Payment Terms', val('vend_terms') || '\u2014'],
      ],
      theme: 'grid', styles: { ...tblS },
      columnStyles: {
        0: { ...lblCol, cellWidth: lw },
        1: { cellWidth: vw },
        2: { ...lblCol, cellWidth: lw },
        3: { cellWidth: vw },
      },
    });
    y = doc.lastAutoTable.finalY + 3;

    // ── SHIP TO / DELIVERY ───────────────────────────────────────────────────
    y = sectionBar('SHIP TO / DELIVERY INFORMATION', y);
    doc.autoTable({
      startY: y, margin: mg, tableWidth: CW,
      body: [
        ['Deliver To', val('ship_to') || '\u2014', 'Ship Method', val('ship_method') || '\u2014'],
        ['Delivery Address', { content: val('ship_addr') || '\u2014', colSpan: 3 }],
        ['Est. Delivery Date', { content: val('ship_eta') || '\u2014', colSpan: 3 }],
      ],
      theme: 'grid', styles: { ...tblS },
      columnStyles: {
        0: { ...lblCol, cellWidth: lw },
        1: { cellWidth: vw },
        2: { ...lblCol, cellWidth: lw },
        3: { cellWidth: vw },
      },
    });
    y = doc.lastAutoTable.finalY + 3;

    // ── LINE ITEMS ───────────────────────────────────────────────────────────
    y = sectionBar('LINE ITEMS', y);
    doc.autoTable({
      startY: y, margin: mg, tableWidth: CW,
      head: [['#', 'Item Description', 'SKU / Part #', 'Qty', 'Unit Price', 'Line Total']],
      body: lineRows,
      theme: 'grid', styles: { ...tblS },
      headStyles: { fillColor: STEEL, textColor: WHITE, fontStyle: 'bold', fontSize: 8.5 },
      alternateRowStyles: { fillColor: GRAY },
      columnStyles: {
        0: { cellWidth: 9,  halign: 'center' },
        1: { cellWidth: 88 },
        2: { cellWidth: 22 },
        3: { cellWidth: 12, halign: 'center' },
        4: { cellWidth: 29, halign: 'right' },
        5: { cellWidth: 29, halign: 'right', fontStyle: 'bold' },
      },
    });
    y = doc.lastAutoTable.finalY + 3;

    // ── TOTALS ───────────────────────────────────────────────────────────────
    const TW = 90;
    const TX = ML + CW - TW;
    const totBody = [
      ['Subtotal', fmt(subtotal)],
      ['Shipping & Handling', fmt(shipping)],
      ['Tax', fmt(tax)],
    ];
    if (tax > 0 && taxReason) totBody.push(['Reason for Tax', taxReason]);
    totBody.push(['TOTAL AMOUNT', fmt(total)]);
    const totalRowIdx = totBody.length - 1;
    doc.autoTable({
      startY: y, margin: { left: TX, right: MR }, tableWidth: TW,
      body: totBody,
      theme: 'grid', styles: { ...tblS },
      columnStyles: {
        0: { ...lblCol, cellWidth: 52 },
        1: { cellWidth: 38, halign: 'right' },
      },
      didParseCell: (data) => {
        if (data.section === 'body' && data.row.index === totalRowIdx) {
          data.cell.styles.fillColor = NAVY;
          data.cell.styles.textColor = WHITE;
          data.cell.styles.fontStyle = 'bold';
          data.cell.styles.fontSize  = 9.5;
        }
      },
    });
    y = doc.lastAutoTable.finalY + 3;

    // ── NOTES / JUSTIFICATION ────────────────────────────────────────────────
    const notesBody = [];
    if (notes) notesBody.push(['Justification', notes]);
    if (tax > 0 && taxReason) notesBody.push(['Reason for Tax Applied', taxReason]);
    if (notesBody.length > 0) {
      y = sectionBar('NOTES / JUSTIFICATION', y);
      doc.autoTable({
        startY: y, margin: mg, tableWidth: CW,
        body: notesBody,
        theme: 'grid', styles: { ...tblS },
        columnStyles: {
          0: { ...lblCol, cellWidth: 42 },
          1: { cellWidth: CW - 42 },
        },
        didParseCell: (data) => {
          if (data.section === 'body' && data.row.raw[0] === 'Reason for Tax Applied') {
            data.cell.styles.fillColor = (data.column.index === 0) ? AMBER : [255, 253, 231];
            if (data.column.index === 0) data.cell.styles.textColor = AMBERT;
          }
        },
      });
      y = doc.lastAutoTable.finalY + 3;
    }

    // ── EMERGENCY BLOCK ──────────────────────────────────────────────────────
    if (isEmergency) {
      doc.autoTable({
        startY: y, margin: mg, tableWidth: CW,
        body: [
          [{ content: '!! EMERGENCY PURCHASE \u2014 VERBAL APPROVAL ON FILE', colSpan: 4 }],
          ['Verbal Approver', emgApprover || '\u2014', 'Approver Title / Role', emgRole || '\u2014'],
          ['Date & Time of Approval', emgDatetime || '\u2014', 'Reason for Emergency', emgReason || '\u2014'],
        ],
        theme: 'grid', styles: { ...tblS },
        columnStyles: {
          0: { cellWidth: lw, fillColor: LTRED, fontStyle: 'bold', textColor: DKRED },
          1: { cellWidth: vw },
          2: { cellWidth: lw, fillColor: LTRED, fontStyle: 'bold', textColor: DKRED },
          3: { cellWidth: vw },
        },
        didParseCell: (data) => {
          if (data.section === 'body' && data.row.index === 0) {
            data.cell.styles.fillColor  = DKRED;
            data.cell.styles.textColor  = WHITE;
            data.cell.styles.fontStyle  = 'bold';
            data.cell.styles.halign     = 'center';
            data.cell.styles.fontSize   = 9;
          }
        },
      });
      y = doc.lastAutoTable.finalY + 3;
    }

    // ── SIGNATURE BLOCK ──────────────────────────────────────────────────────
    y = sectionBar('AUTHORIZATION & APPROVAL', y);
    y += 2;

    // Instruction line
    doc.setFont('helvetica', 'italic');
    doc.setFontSize(7.5);
    doc.setTextColor(100, 100, 120);
    doc.text('All three approvers must sign before any purchase may proceed. Accounting will notify requestor once fully approved.', ML, y + 3.5);
    y += 8;

    // Three approval columns — header bars + body
    const colW = CW / 3;
    const approvers = [
      { step: 'STEP 1 APPROVAL', name: 'Cole Walters',  fill: STEEL },
      { step: 'STEP 2 APPROVAL', name: 'Drake Huckaba', fill: NAVY  },
      { step: 'STEP 3 — FINAL',  name: 'Jarrett Lopez', fill: [26, 107, 58] },
    ];

    // Draw colored header bars for each approver column
    approvers.forEach((a, i) => {
      const x = ML + i * colW;
      doc.setFillColor(...a.fill);
      doc.rect(x, y, colW - 0.5, 8, 'F');
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(8);
      doc.setTextColor(...WHITE);
      doc.text(a.step + '  —  ' + a.name, x + colW / 2, y + 5.2, { align: 'center' });
    });
    y += 8;

    // Body of approval cells
    doc.autoTable({
      startY: y, margin: mg, tableWidth: CW,
      body: [approvers.map(() =>
        '\n[ ]  APPROVED          [ ]  DENIED\n\nSignature: _________________________\n\nDate: ______________________________\n'
      )],
      theme: 'grid',
      styles: { ...tblS, cellPadding: { top: 5, bottom: 5, left: 5, right: 5 }, fontSize: 8.5, lineColor: MGRAY, lineWidth: 0.3 },
      columnStyles: {
        0: { cellWidth: colW, fillColor: [236, 242, 252] },
        1: { cellWidth: colW, fillColor: GRAY },
        2: { cellWidth: colW, fillColor: [228, 242, 232] },
      },
      didParseCell: (data) => {
        if (data.section === 'body') data.cell.styles.fontStyle = 'normal';
      },
    });
    y = doc.lastAutoTable.finalY + 3;

    // ── FOOTER ───────────────────────────────────────────────────────────────
    doc.setDrawColor(200, 200, 200);
    doc.setLineWidth(0.3);
    doc.line(ML, y, PW - MR, y);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(7);
    doc.setTextColor(150, 150, 150);
    doc.text('Submit to: accounting@ams.events  |  Subject: "PO Request \u2014 [Project Name] \u2014 [Date]"  |  Allow up to 2 business days for review', PW / 2, y + 4, { align: 'center' });

    // ── SAVE ─────────────────────────────────────────────────────────────────
    const projectName = val('req_project').replace(/[^a-zA-Z0-9 \-]/g, '').trim() || 'Purchase Order';
    const dateStr = val('req_date').replace(/-/g, '') || new Date().toISOString().slice(0, 10).replace(/-/g, '');
    const filename = 'AMS PO - ' + projectName + ' - ' + dateStr + '.pdf';
    doc.save(filename);
    toast('PO downloaded: ' + filename, 'success');

  } catch (err) {
    console.error(err);
    toast('Error: ' + (err.message || 'Unknown error. Check console.'), 'error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '⬇️ Download PO (.pdf)';
  }
}

// ═══════════════════════════════════════════════
// LOGO UPLOAD
// ═══════════════════════════════════════════════
let logoDataUrl = null;
let logoMimeType = 'PNG';

function handleLogoUpload(input) {
  const file = input.files[0];
  if (!file) return;
  const ext = file.name.split('.').pop().toUpperCase();
  logoMimeType = (ext === 'JPG' || ext === 'JPEG') ? 'JPEG' : (ext === 'WEBP' ? 'WEBP' : 'PNG');
  const reader = new FileReader();
  reader.onload = (e) => {
    logoDataUrl = e.target.result;
    const preview = document.getElementById('logo_preview');
    preview.src = logoDataUrl;
    preview.style.display = 'block';
  };
  reader.readAsDataURL(file);
}

// ═══════════════════════════════════════════════
// INIT
// ═══════════════════════════════════════════════
document.getElementById('req_date').value = new Date().toISOString().split('T')[0];
addRow(); addRow(); addRow(); addRow(); addRow();
</script>


</body>
</html>
