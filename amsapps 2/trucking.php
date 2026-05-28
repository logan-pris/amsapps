<?php 
$pageTitle = "AMS Apps - CP2 Trucking Calculator";
require_once __DIR__ . '/core/auth_check.php';
require_once __DIR__ . '/core/db.php'; 
include 'includes/header.php'; 
?>

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Yantramanav', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: #f0f0f0;
        color: #1e293b;
        min-height: 100vh;
    }

    /* ── Header ── */
    .app-header {
        background: #111111;
        color: white;
        padding: 18px 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .header-logo { display: flex; align-items: center; gap: 14px; }
    /*.header-logo img { filter: brightness(0) invert(1); }*/
    .header-icon {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 12px;
        width: 48px; height: 48px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
    }
    .header-logo h1 { font-size: 20px; font-weight: 800; letter-spacing: -0.3px; }
    .header-logo p  { font-size: 12px; opacity: 0.65; margin-top: 2px; }
    .header-actions { display: flex; gap: 10px; align-items: center; }

    /* ── Layout ── */
    .main {
        max-width: 860px;
        margin: 0 auto;
        padding: 28px 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    /* Both columns now transparent — their cards flow directly into .main */
    .left-col  { display: contents; }
    .right-col { display: contents; }

    /* ── Cards ── */
    .card-header {
        display: flex;
        justify-content: space-between; /* This pushes title left and buttons right */
        align-items: center;
        background: white;
        border-radius: 14px;
        padding: 22px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06), 0 0 0 1px rgba(0,0,0,0.04);
    }
    .card2 {
        display: flex;
        justify-content: space-between;
        background: white;
        border-radius: 14px;
        padding: 22px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06), 0 0 0 1px rgba(0,0,0,0.04);
    }
    .card {
        background: white;
        border-radius: 14px;
        padding: 22px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06), 0 0 0 1px rgba(0,0,0,0.04);
    }
    .card-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #64748b;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .card-title span { font-size: 15px; }

    /* ── Form ── */
    .form-row { display: grid; gap: 12px; margin-bottom: 12px; }
    .form-row.cols-2 { grid-template-columns: 1fr 1fr; }
    .form-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
    .form-row.cols-4 { grid-template-columns: 1fr 1fr 1fr 1fr; }
    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .form-group label {
        font-size: 11px; font-weight: 700;
        color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        border: 1.5px solid #e2e8f0;
        border-radius: 9px;
        padding: 10px 13px;
        font-size: 14px;
        line-height: 1.4;
        color: #1e293b;
        background: #f8fafc;
        outline: none;
        font-family: inherit;
        width: 100%;
        box-sizing: border-box;
        transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #FCDB04;
        background: white;
        box-shadow: 0 0 0 3px rgba(252,219,4,0.18);
    }
    .form-group textarea { resize: vertical; min-height: 80px; }
    .field-hint { font-size: 11px; color: #94a3b8; margin-top: 3px; }

    .pfx-wrap { display: flex; align-items: stretch; width: 100%; min-width: 0; overflow: hidden; border-radius: 9px; }
    .pfx {
        background: #e8edf4;
        border: 1.5px solid #e2e8f0;
        border-right: none;
        border-radius: 9px 0 0 9px;
        padding: 9px 11px;
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
        white-space: nowrap;
    }
    .pfx-wrap input { border-radius: 0 9px 9px 0 !important; flex: 1; min-width: 0; }

    /* ── Buttons ── */
    .btn {
        border: none; border-radius: 9px;
        padding: 9px 16px; font-size: 13px; font-weight: 700;
        cursor: pointer; display: inline-flex; align-items: center;
        gap: 6px; transition: all 0.15s; font-family: inherit;
        white-space: nowrap;
    }
    .btn-primary  { background: #FCDB04; color: #111111; }
    .btn-primary:hover  { background: #FFBA00; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(252,219,4,0.45); }
    .btn-secondary { background: #f1f5f9; color: #475569; border: 1.5px solid #e2e8f0; }
    .btn-secondary:hover { background: #e2e8f0; }
    .btn-success  { background: #16a34a; color: white; }
    .btn-success:hover  { background: #15803d; }
    .btn-ghost-danger {
        background: none; color: #ef4444; border: none;
        padding: 5px 8px; border-radius: 7px; font-size: 13px;
        cursor: pointer; font-family: inherit; font-weight: 600;
        transition: background 0.1s;
    }
    .btn-ghost-danger:hover { background: #fee2e2; }
    .btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; box-shadow: none !important; }

    /* ── Stops / Route ── */
    .stop-item { display: flex; align-items: flex-start; gap: 6px; margin-bottom: 4px;
        transition: opacity 0.15s, transform 0.1s; border-radius: 10px; }
    .stop-item.dragging { opacity: 0.35; }
    .stop-item.drag-over-top    { box-shadow: 0 -3px 0 0 #FCDB04; }
    .stop-item.drag-over-bottom { box-shadow: 0 3px 0 0 #FCDB04; }
    .stop-drag-handle {
        cursor: grab; color: #c5cdd9; font-size: 15px; padding: 8px 1px 0 0;
        user-select: none; flex-shrink: 0; line-height: 1;
        transition: color 0.12s; width: 14px;
    }
    .stop-drag-handle:hover { color: #FCDB04; }
    .stop-drag-handle:active { cursor: grabbing; }
    .stop-left { display: flex; flex-direction: column; align-items: center; padding-top: 7px; width: 34px; flex-shrink: 0; }
    .stop-connector { width: 2px; flex: 1; min-height: 32px; background: #e2e8f0; margin: 3px 0; position: relative; overflow: visible; }
    .leg-miles-label {
        position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);
        background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 20px;
        padding: 2px 7px; font-size: 10px; color: #64748b; font-weight: 700;
        white-space: nowrap; letter-spacing: 0.2px; pointer-events: none;
        display: none;
    }
    .stop-datetime  { display: flex; gap: 8px; margin-top: 8px; flex-wrap: wrap; align-items: flex-end; }
    .stop-dt-field  { display: flex; flex-direction: column; gap: 4px; flex: 1; min-width: 110px; }
    .stop-dt-label  { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; }
    .req-label-sm::after { content: ' *'; color: #ef4444; font-weight: 900; }
    .stop-date-input, .stop-time-input {
        border: 1.5px solid #e2e8f0; border-radius: 8px;
        padding: 10px 12px; font-size: 13px; color: #1e293b;
        background: #f8fafc; outline: none; font-family: inherit;
        width: 100%; box-sizing: border-box;
        transition: border-color 0.15s, background 0.15s;
    }
    .stop-date-input:focus, .stop-time-input:focus {
        border-color: #FCDB04; background: white;
        box-shadow: 0 0 0 3px rgba(252,219,4,0.18);
    }
    .stop-date-input.field-error { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,0.1) !important; }
    .stop-est-arrival {
font-size: 12px; font-weight: 600;
color: #15803d; margin-top: 6px;
padding: 6px 10px; background: #dcfce7;
border-radius: 7px; border-left: 3px solid #22c55e;
display: none;
}
    .stop-est-arrival.late {
color: #b91c1c; background: #fff1f2;
border-left-color: #ef4444;
}
.stop-wh-toggle { display:flex; align-items:center; gap:6px; margin-top:8px; }
    .stop-wh-toggle input[type=checkbox] { width:16px; height:16px; accent-color:#FCDB04; cursor:pointer; flex-shrink:0; }
    .stop-wh-toggle label { font-size:11px; font-weight:600; color:#64748b; cursor:pointer; user-select:none; white-space:nowrap; }
    .stop-detention-field { display: flex; flex-direction: column; gap: 4px; flex: 0 0 auto; width: 100px; }
    .stop-detention-input {
        border: 1.5px solid #e2e8f0; border-radius: 8px;
        padding: 10px 10px; font-size: 13px; color: #1e293b;
        background: #f8fafc; outline: none; font-family: inherit;
        width: 100%; box-sizing: border-box;
        transition: border-color 0.15s, background 0.15s;
    }
    .stop-detention-input:focus { border-color: #FCDB04; background: white; }
    .stop-req-depart {
        font-size: 12px; font-weight: 700; color: #0369a1;
        margin-top: 8px; padding: 8px 12px;
        background: #e0f2fe; border-radius: 8px;
        border-left: 4px solid #0ea5e9;
        display: none; line-height: 1.4;
    }
    .stop-layover-warn {
        display: none; font-size: 11px; font-weight: 600;
        background: #fef3c7; color: #92400e;
        border: 1px solid #fde68a; border-left: 3px solid #f59e0b;
        border-radius: 6px; padding: 5px 9px; margin-top: 5px;
    }
    .stop-layover-warn.show { display: block; }
    .stop-dot {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 900; flex-shrink: 0;
    }
    .dot-origin      { background: #FCDB04; color: #111111; border: 2px solid #e8c800; }
    .dot-stop        { background: #FFBA00; color: #111111; border: 2px solid #e8a800; }
    .dot-destination { background: #FCDB04; color: #111111; border: 2px solid #e8c800; }
    .stop-connector  { width: 2px; height: 18px; background: #e2e8f0; margin: 3px 0; }
    .stop-input-wrap { flex: 1; }
    .stop-input {
        width: 100%; border: 1.5px solid #e2e8f0; border-radius: 9px;
        padding: 10px 13px; font-size: 14px; color: #1e293b;
        background: #f8fafc; outline: none; font-family: inherit;
        box-sizing: border-box;
        transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
    }
    .stop-input:focus { border-color: #FCDB04; background: white; box-shadow: 0 0 0 3px rgba(252,219,4,0.18); }
    .stop-status { font-size: 11px; margin-top: 4px; padding-left: 2px; display: none; }
    .stop-status.show { display: block; }
    .stop-status.ok      { color: #16a34a; }
    .stop-status.error   { color: #ef4444; }
    .stop-status.loading { color: #d97706; }

    /* ── Schedule feasibility alert ── */
    #scheduleAlert {
        display: none;
        margin-top: 12px;
        padding: 12px 16px;
        background: #fff1f2;
        border: 1.5px solid #f87171;
        border-left: 4px solid #ef4444;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        color: #b91c1c;
        line-height: 1.5;
    }
    #scheduleAlert .alert-title {
        font-size: 13px; font-weight: 800; margin-bottom: 4px;
        display: flex; align-items: center; gap: 6px;
    }
    #scheduleAlert .alert-detail {
        font-size: 12px; font-weight: 500; color: #dc2626; margin-top: 2px;
    }

    .miles-bar {
        display: flex; align-items: center; gap: 14px;
        margin-top: 16px; padding-top: 16px;
        border-top: 1.5px dashed #e2e8f0;
    }
    .miles-display {
        background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 10px;
        padding: 8px 16px; font-size: 22px; font-weight: 800;
        color: #1e293b; min-width: 110px; text-align: center; letter-spacing: -0.5px;
    }
    .miles-display.has-value { border-color: #FCDB04; color: #111111; background: #fffde7; }
    .dist-status { font-size: 12px; color: #94a3b8; }
    .dist-status.ok    { color: #16a34a; }
    .dist-status.error { color: #ef4444; }

    /* ── Fuel ── */
    .fuel-badge {
        display: flex; align-items: center; gap: 12px;
        border-radius: 10px; padding: 12px 14px; margin-top: 12px;
        border: 1.5px solid;
    }
    .fuel-badge.ok   { background: #fffde7; border-color: #FCDB04; }
    .fuel-badge.warn { background: #fffbeb; border-color: #fcd34d; }
    .fuel-badge .fb-label { font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 2px; }
    .fuel-badge .fb-value { font-size: 20px; font-weight: 800; }
    .fuel-badge.ok   .fb-value { color: #111111; }
    .fuel-badge.warn .fb-value { color: #d97706; }

    /* ── Accessorials ── */
    .acc-row { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
    .acc-row input {
        border: 1.5px solid #e2e8f0; border-radius: 9px; padding: 10px 12px;
        font-size: 14px; color: #1e293b; background: #f8fafc;
        outline: none; font-family: inherit; box-sizing: border-box;
        transition: border-color 0.12s; width: 100%; min-width: 0;
    }
    .acc-row input:focus { border-color: #FCDB04; background: white; }
    .acc-name { flex: 2; min-width: 0; }
    .acc-amt  { flex: 1; min-width: 0; max-width: 130px; }

    /* ── Summary card ── */
    .summary-card {
        background: #111111;
        border-radius: 14px; padding: 22px; color: white;
        box-shadow: 0 8px 24px rgba(0,0,0,0.35);
    }
    .summary-head {
        font-size: 11px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 1px; opacity: 0.7; margin-bottom: 18px;
    }
    .sum-line {
        display: flex; justify-content: space-between; align-items: baseline;
        padding: 6px 0; font-size: 13.5px;
    }
    .sum-line .lbl { opacity: 0.75; }
    .sum-line .val { font-weight: 700; }
    .sum-line .val.strike { text-decoration: line-through; opacity: 0.45; font-weight: 400; }
    .sum-hr { border: none; border-top: 1px solid rgba(255,255,255,0.18); margin: 8px 0; }
    .sum-total {
        display: flex; justify-content: space-between; align-items: baseline;
        padding-top: 10px;
    }
    .sum-total .lbl { font-size: 15px; font-weight: 800; }
    .sum-total .val { font-size: 32px; font-weight: 900; letter-spacing: -1px; color: #FCDB04; }
    .sum-footer { margin-top: 14px; opacity: 0.55; font-size: 11px; line-height: 1.5; }
    .min-pill {
        display: inline-flex; align-items: center; gap: 4px;
        background: rgba(251,191,36,0.2); border: 1px solid rgba(251,191,36,0.45);
        border-radius: 20px; padding: 3px 9px; font-size: 11px;
        font-weight: 700; color: #fcd34d; margin-top: 5px;
    }
    .sum-empty {
        opacity: 0.55; font-size: 13px; text-align: center;
        padding: 24px 0; line-height: 1.6;
    }

    /* ── Fuel matrix reference ── */
    .matrix-table { width: 100%; font-size: 10px; border-collapse: collapse; }
    .matrix-table th {
        text-align: left; padding: 3px 6px; font-weight: 700;
        color: #64748b; border-bottom: 1.5px solid #e2e8f0; background: #f8fafc;
    }
    .matrix-table th:last-child { text-align: right; }
    .matrix-table td { padding: 2px 6px; border-bottom: 1px solid #f1f5f9; }
    .matrix-table td:last-child { text-align: right; font-weight: 700; color: #1e293b; }
    .matrix-table tr.active td { background: #fffde7; color: #111111; font-weight: 800; border-left: 3px solid #FCDB04; }
    .matrix-table tr.active td:last-child { color: #111111; }
    .matrix-scroll { max-height: 180px; overflow-y: auto; border-radius: 8px; border: 1px solid #e2e8f0; }

    /* ── Spinner ── */
    .spinner {
        display: inline-block; width: 14px; height: 14px;
        border: 2px solid rgba(255,255,255,0.4);
        border-top-color: white; border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }
    .spinner.dark {
        border-color: #e2e8f0; border-top-color: #FCDB04;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Print ── */
    @media print {
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        body { background: white; margin: 0; }
        .app-header, .main { display: none !important; }
        #printReport { display: block !important; }
    }
    #printReport { display: none; }

    /* Readonly date fields */
    input[readonly] {
        background: #f1f5f9 !important;
        color: #64748b !important;
        cursor: default;
        border-color: #e2e8f0 !important;
        box-shadow: none !important;
    }
    /* Required field asterisk */
    .req-label::after { content: ' *'; color: #ef4444; font-weight:900; }
    /* Required field highlight when empty */
    input.field-error { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,0.1) !important; }
    /* Layover row */
    .layover-row {
        display: flex; align-items: center; gap: 8px; margin-bottom: 8px;
        background: #fffde7; border: 1.5px solid #FCDB04; border-radius: 9px; padding: 10px 12px;
    }
    .layover-row label { font-size:13px; font-weight:700; color:#111111; white-space:nowrap; }
    .layover-badge {
        font-size:11px; background:#111111; color:#FCDB04;
        border-radius:4px; padding:2px 7px; font-weight:700; letter-spacing:0.3px;
    }
    .layover-days-wrap { display:flex; align-items:center; gap:6px; margin-left:auto; }
    .layover-days-input {
        width:70px; border:1.5px solid #e2e8f0; border-radius:8px;
        padding:10px 10px; font-size:14px; font-weight:700; color:#111111;
        background:#fff; outline:none; font-family:inherit; text-align:center;
        box-sizing:border-box;
    }
    .layover-days-input:focus { border-color:#FCDB04; box-shadow:0 0 0 3px rgba(252,219,4,0.18); }
    .layover-total {
        min-width:80px; text-align:right; font-size:15px;
        font-weight:800; color:#111111;
    }
    /* ── Route Details Table ── */
    .rd-scroll { overflow-x: auto; width: 100%; }
    .rd-table { width:100%; border-collapse:collapse; font-size:10px; margin-top:4px; min-width:600px; }
    .rd-table th {
        background:#111111; color:#FCDB04; padding:5px 6px; text-align:left;
        font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap;
    }
    .rd-table th:first-child { border-radius:8px 0 0 0; }
    .rd-table th:last-child  { border-radius:0 8px 0 0; }
    .rd-table td { padding:5px 6px; border-bottom:1px solid #f1f5f9; vertical-align:middle; white-space:nowrap; }
    /* Column widths */
    .rd-table th:nth-child(1),
    .rd-table td:nth-child(1) { width:28px; }             /* Stop badge */
    .rd-table th:nth-child(2),
    .rd-table td:nth-child(2) { max-width:130px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; } /* Location */
    .rd-table th:nth-child(8),
    .rd-table td:nth-child(8) { min-width:80px; }         /* Status */
    .rd-table tr:last-child td { border-bottom:none; }
    .rd-table tr:nth-child(even) td { background:#fafbfc; }
    .rd-stop-badge {
        display:inline-flex; align-items:center; justify-content:center;
        width:18px; height:18px; border-radius:50%;
        font-size:9px; font-weight:800; color:white; flex-shrink:0;
    }
    .rd-origin { background:#22c55e; }
    .rd-dest   { background:#6366f1; }
    .rd-stop   { background:#f59e0b; }
    .rd-tag {
        display:inline-block; font-size:8px; font-weight:700; border-radius:3px;
        padding:1px 5px; margin-top:1px; white-space:nowrap;
    }
    .rd-tag-layover  { background:#fee2e2; color:#b91c1c; }
    .rd-tag-wh       { background:#fef3c7; color:#92400e; }
    .rd-tag-ontime   { background:#dcfce7; color:#166534; }
    .rd-tag-early    { background:#eff6ff; color:#1d4ed8; }
    .rd-tag-late     { background:#fee2e2; color:#b91c1c; }
    .rd-deadline-row td { background:#f5f3ff !important; }
    .rd-deadline-row td:first-child { border-left:3px solid #6366f1; }
    /* ══════════════════════════════════════════
       RESPONSIVE / MOBILE
    ══════════════════════════════════════════ */

    /* Tablet: tighten padding */
    @media (max-width: 960px) {
        .main { padding: 16px; gap: 14px; }
    }

    /* Mobile: 768px and below */
    @media (max-width: 768px) {
        .app-header {
            padding: 10px 14px;
            gap: 8px;
        }
        .app-header img { height: 28px; }
        .app-header-title { font-size: 12px; letter-spacing: 0.5px; }
        .header-actions { gap: 7px; }
        .header-actions .btn { padding: 8px 12px; font-size: 12px; }

        .main { padding: 10px; gap: 10px; }

        .card { padding: 14px; border-radius: 10px; }
        .card-title { font-size: 12px; margin-bottom: 14px; }

        /* Collapse 3- and 4-column grids to 2 columns */
        .form-row.cols-3 { grid-template-columns: 1fr 1fr; }
        .form-row.cols-4 { grid-template-columns: 1fr 1fr; }

        /* Stop card fields: stack date/time/detention vertically on small screens */
        .stop-datetime {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .stop-detention-field {
            width: auto;
            grid-column: 1 / -1;
            max-width: 140px;
        }
        .stop-dt-field { min-width: 0; }
        .stop-input { font-size: 13px; padding: 9px 11px; }
        .stop-date-input,
        .stop-time-input,
        .stop-detention-input { font-size: 13px; padding: 9px 10px; }


        /* Miles bar */
        .miles-bar { flex-wrap: wrap; gap: 10px; }
        .miles-display { font-size: 20px; }

        /* Buttons: proper touch targets */
        .btn { font-size: 13px; padding: 10px 14px; min-height: 44px; }
        .btn-ghost-danger { min-height: 36px; min-width: 36px; }

        /* Action bar: stack on very narrow */
        .stop-action-bar { flex-wrap: wrap; }

        /* Accessorials */
        .acc-row { flex-wrap: wrap; gap: 6px; }
        .acc-name { flex: 1 1 160px; }
        .acc-amt  { flex: 0 0 120px; }

        /* Summary card */
        .summary-row { font-size: 13px; }
        .summary-total { font-size: 22px; }

        /* Drag handle: slightly larger touch target */
        .stop-drag-handle { font-size: 20px; padding: 4px 4px 0 0; }

        /* Route schedule table: mobile touch scroll */
        .rd-scroll { -webkit-overflow-scrolling: touch; margin: 0 -14px; padding: 0 14px; }
        .rd-table { min-width: 640px; font-size: 10px; }
        .rd-table th { padding: 5px 6px; font-size: 8px; }
        .rd-table td { padding: 5px 6px; }

        .stop-wh-toggle label { font-size: 12px; }

        /* Arrival / req-depart labels */
        .stop-est-arrival, .stop-req-depart { font-size: 11px; }

        /* Layover row */
        .layover-row { flex-wrap: wrap; gap: 8px; }
        .layover-days-wrap { margin-left: 0; width: 100%; justify-content: flex-end; }
    }

    /* Small mobile: 480px and below */
    @media (max-width: 480px) {
        .app-header { padding: 8px 12px; }
        .app-header img { height: 24px; }
        .app-header-title { display: none; }
        .header-actions { gap: 6px; }
        .header-actions .btn { padding: 8px 10px; font-size: 11px; }
        .main { padding: 8px; gap: 8px; }
        .card { padding: 12px; }
        /* Collapse ALL multi-column grids to single column */
        .form-row.cols-2,
        .form-row.cols-3,
        .form-row.cols-4 { grid-template-columns: 1fr; }
        /* Date/time: full-width stack */
        .stop-datetime { grid-template-columns: 1fr; }
        .stop-detention-field { max-width: 100%; }
        .stop-dt-field[style*="max-width:160px"] { max-width: 100% !important; }
        /* Make add/calculate buttons full width */
        #stopsContainer + div { flex-direction: column; }
        #stopsContainer + div .btn { width: 100%; justify-content: center; }
        /* Manual miles inline */
        .miles-bar { gap: 8px; }
        /* Fuel card title: wrap the EIA button below title */
        .card-title[style*="justify-content:space-between"] { flex-wrap: wrap; gap: 8px; }
        /* Submit card: stack email + button */
        #submitCard .form-row.cols-2 { grid-template-columns: 1fr; }
    }
</style>

<!-- ═══════════════════ MAIN ═══════════════════ -->
<div class="main">

    <!-- ────── LEFT COLUMN ────── -->
    <div class="left-col">
		<div class="card2">
    		<div class="header-logo">
    		    <img src="data:image/png;base64,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" alt="CP2 Logistics" style="height:42px;width:auto;">
                <div style="margin-left:4px;">
                    <h1>Trucking Quote Calculator</h1>
                    <p>CP2 Logistics</p>
                </div>
            </div>
            <div class="header-actions no-print">
                <button class="btn btn-secondary" onclick="resetQuote()">+ New Quote</button>
                <button class="btn btn-primary" onclick="window.print()">🖨 Print Quote</button>
            </div>
		</div>
        <!-- Quote Info -->
        <div class="card">
            <div class="card-title"><span>📋</span> Quote Information</div>
            <div class="form-row cols-3">
                <div class="form-group" style="grid-column:span 2;">
                    <label class="req-label">Requestor Name</label>
                    <input type="text" id="customerName" placeholder="Full name or company name" required oninput="this.classList.remove('field-error')">
                </div>
                <div class="form-group">
                    <label class="req-label">Quote #</label>
                    <input type="text" id="quoteNumber" required oninput="this.classList.remove('field-error')">
                </div>
            </div>
            <div class="form-row cols-3">
                <div class="form-group">
                    <label>Quote Date</label>
                    <input type="date" id="quoteDate" readonly>
                </div>
                <div class="form-group">
                    <label>Valid Until <span style="font-size:10px;font-weight:400;color:#94a3b8;">(7 days)</span></label>
                    <input type="date" id="validUntil" readonly>
                </div>
                <div class="form-group">
                    <label class="req-label">Contact / Email</label>
                    <input type="text" id="contact" placeholder="contact@company.com or (555) 000-0000" required oninput="this.classList.remove('field-error')">
                </div>
            </div>
            <div class="form-group">
                <label>Notes / Special Instructions</label>
                <textarea id="notes" placeholder="Commodity, special requirements, load details..."></textarea>
            </div>
        </div>

        <!-- Live Summary -->
        <div class="summary-card">
            <div class="summary-head">Live Quote Summary</div>
            <div id="summaryContent">
                <div class="sum-empty">Enter a route and miles below<br>to see your quote total.</div>
            </div>
        </div>

        <!-- Equipment & Rate -->
        <div class="card">
            <div class="card-title"><span>🚛</span> Equipment</div>
            <div class="form-group">
                <label>Equipment Type</label>
                <select id="equipmentType">
                    <option>Dry Van</option>
                    <option>Box Truck</option>
                    <option>Sprinter Van</option>
                    <option>Power Only</option>
                    <option>Other</option>
                </select>
            </div>
            <!-- Hidden constants — not shown to users -->
            <input type="hidden" id="baseRate" value="3.50">
            <input type="hidden" id="minRate"  value="1250">
            <!-- Rate info chips -->
            <div style="display:flex;gap:8px;margin-top:14px;flex-wrap:wrap;">
                <div style="background:#fffde7;border:1.5px solid #FCDB04;border-radius:8px;padding:7px 13px;font-size:12px;color:#111111;">
                    <span style="font-weight:700;">$3.50</span> <span style="opacity:0.75;">/ mile</span>
                </div>
                <div style="background:#fffde7;border:1.5px solid #FFBA00;border-radius:8px;padding:7px 13px;font-size:12px;color:#111111;">
                    <span style="font-weight:700;">$1,250</span> <span style="opacity:0.75;">/ day min (under 350 mi)</span>
                </div>
                <div style="background:#fff9e6;border:1.5px solid #FFBA00;border-radius:8px;padding:7px 13px;font-size:12px;color:#111111;">
                    <span style="font-weight:700;">55 mph</span> <span style="opacity:0.75;">avg · HOS rules applied</span>
                </div>
                <div style="background:#111111;border:1.5px solid #FCDB04;border-radius:8px;padding:7px 13px;font-size:12px;color:#FCDB04;">
                    Est. days: <span style="font-weight:700;" id="estDaysChip">—</span>
                </div>
            </div>
        </div>

        <!-- Route -->
        <div class="card">
            <div class="card-title"><span>🗺</span> Route & Mileage</div>
            <div id="stopsContainer"></div>
            <div style="display:flex;gap:8px;margin-top:10px;" class="no-print">
                <button class="btn btn-secondary" onclick="addStop()">＋ Add Stop</button>
                <button class="btn btn-primary" id="calcBtn" onclick="calculateDistance()">📍 Calculate Distance</button>
            </div>
            <div id="scheduleAlert"></div>
            <div class="miles-bar">
                <div>
                    <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Total Miles</div>
                    <div class="miles-display" id="totalMiles">—</div>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Est. Trip Days</div>
                    <div class="miles-display" id="tripDaysDisplay" style="min-width:72px;font-size:18px;">—</div>
                </div>
                <div style="flex:1;min-width:160px;">
                    <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Manual Override</div>
                    <div class="pfx-wrap">
                        <span class="pfx">mi</span>
                        <input type="number" id="manualMiles" placeholder="Enter miles" min="0" step="1" oninput="onManualMiles()" style="min-width:0;">
                    </div>
                </div>
                <div id="distStatus" class="dist-status" style="align-self:flex-end;padding-bottom:10px;"></div>
            </div>
        </div>

        <!-- Route Details -->
        <div class="card" id="routeDetailsCard" style="display:none;">
            <div class="card-title"><span>🗺</span> Route Schedule</div>
            <div id="routeDetailsTable"></div>
        </div>

        <!-- Fuel Surcharge -->
        <div class="card">
            <div class="card-title" style="justify-content:space-between;">
                <span style="display:flex;align-items:center;gap:7px;"><span>⛽</span> Fuel Surcharge</span>
                <button class="btn btn-secondary no-print" id="eiaBtn" onclick="fetchEIA()" style="padding:5px 12px;font-size:12px;">
                    🔄 Fetch Live EIA Rate
                </button>
            </div>
            <div id="eiaStatus" style="display:none;font-size:12px;margin-bottom:10px;padding:8px 12px;border-radius:8px;"></div>
            <div class="form-row cols-2">
                <div class="form-group">
                    <label>National Diesel Average ($/gal)</label>
                    <div class="pfx-wrap">
                        <span class="pfx">$</span>
                        <input type="number" id="fuelAvg" placeholder="e.g. 3.89" step="0.01" min="0" oninput="onFuelChange()">
                    </div>
                    <div class="field-hint" id="fuelAvgHint">Auto-populated from EIA · editable</div>
                </div>
                <div class="form-group">
                    <label>Surcharge Per Mile (auto)</label>
                    <div class="pfx-wrap">
                        <span class="pfx">$</span>
                        <input type="number" id="fuelPerMile" placeholder="—" step="0.01" min="0"
                               readonly style="background:#f1f5f9;color:#64748b;cursor:default;min-width:0;" oninput="recalculate()">
                    </div>
                    <div class="field-hint">Looked up from fuel matrix</div>
                </div>
            </div>
            <div id="fuelBadge"></div>
        </div>

        <!-- Accessorial Fees -->
        <div class="card">
            <div class="card-title"><span>➕</span> Accessorial Fees</div>
            <div id="accContainer"></div>
            <button class="btn btn-secondary no-print" onclick="addAcc()" style="margin-top:8px;">＋ Add Fee</button>
        </div>


        <!-- Submit Request -->
        <div class="card" id="submitCard">
            <div class="card-title" style="justify-content:space-between;">
                <span style="display:flex;align-items:center;gap:7px;"><span>📧</span> Submit Request</span>
            </div>
            <p style="font-size:13px;color:#64748b;margin-bottom:14px;line-height:1.6;">
                Enter the recipient's email address and click <strong>Submit Request</strong> to open your email client with the full quote pre-filled and ready to send.
            </p>
            <div class="form-row cols-2" style="align-items:flex-end;">
                <div class="form-group">
                    <label class="req-label">Send To (Email)</label>
                    <div class="pfx-wrap">
                        <span class="pfx" style="font-size:16px;padding:8px 11px;">✉</span>
                        <input type="email" id="recipientEmail" placeholder="recipient@company.com"
                               value="requests@cp2logistics.com"
                               oninput="this.classList.remove('field-error')">
                    </div>
                </div>
                <div>
                    <button class="btn btn-primary no-print" onclick="submitRequest()"
                            style="width:100%;padding:10px 16px;font-size:14px;justify-content:center;">
                        📧 Submit Request
                    </button>
                </div>
            </div>
            <div id="submitStatus" style="display:none;margin-top:10px;font-size:12px;padding:8px 12px;border-radius:8px;"></div>
            <div id="emailFallback" style="display:none;margin-top:10px;">
                <div style="font-size:11px;font-weight:700;color:#64748b;margin-bottom:6px;">✉ Email client didn't open — copy the quote below and paste it manually:</div>
                <textarea id="emailFallbackBody" readonly
                    style="width:100%;height:160px;font-family:monospace;font-size:11px;padding:8px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;color:#1e293b;resize:vertical;box-sizing:border-box;"></textarea>
                <button onclick="navigator.clipboard.writeText(document.getElementById('emailFallbackBody').value).then(()=>{this.textContent='✓ Copied!';setTimeout(()=>{this.textContent='📋 Copy to Clipboard'},1500)})"
                    style="margin-top:6px;padding:6px 14px;background:#111111;color:#FCDB04;border:none;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;">
                    📋 Copy to Clipboard
                </button>
            </div>
        </div>

    </div><!-- /left-col -->

    <!-- ────── RIGHT COLUMN ────── -->
    <div class="right-col">

        <!-- Fuel Matrix Reference -->
        <div class="card">
            <div class="card-title"><span>⛽</span> Fuel Matrix Reference</div>
            <div class="matrix-scroll">
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th>Diesel Avg Range</th>
                            <th style="text-align:right;">$/Mile</th>
                        </tr>
                    </thead>
                    <tbody id="matrixBody"></tbody>
                </table>
            </div>
        </div>

    </div><!-- /right-col -->

</div><!-- /main -->

<!-- ═══════════════════ SCRIPT ═══════════════════ -->
<script>
// ============================================================
// DATA
// ============================================================
const FUEL_MATRIX = [
    { s: 3.20, e: 3.29, a: 0.00 },
    { s: 3.30, e: 3.39, a: 0.02 },
    { s: 3.40, e: 3.49, a: 0.03 },
    { s: 3.50, e: 3.59, a: 0.06 },
    { s: 3.60, e: 3.69, a: 0.08 },
    { s: 3.70, e: 3.79, a: 0.09 },
    { s: 3.80, e: 3.89, a: 0.11 },
    { s: 3.90, e: 3.99, a: 0.12 },
    { s: 4.00, e: 4.09, a: 0.14 },
    { s: 4.10, e: 4.19, a: 0.15 },
    { s: 4.20, e: 4.29, a: 0.17 },
    { s: 4.30, e: 4.39, a: 0.18 },
    { s: 4.40, e: 4.49, a: 0.20 },
    { s: 4.50, e: 4.59, a: 0.21 },
    { s: 4.60, e: 4.69, a: 0.24 },
    { s: 4.70, e: 4.79, a: 0.26 },
    { s: 4.80, e: 4.89, a: 0.27 },
    { s: 4.90, e: 4.99, a: 0.29 },
    { s: 5.00, e: 5.09, a: 0.30 },
    { s: 5.10, e: 5.19, a: 0.32 },
    { s: 5.20, e: 5.29, a: 0.34 },
    { s: 5.30, e: 5.39, a: 0.35 },
    { s: 5.40, e: 5.49, a: 0.37 },
    { s: 5.50, e: 5.59, a: 0.39 },
    { s: 5.60, e: 5.69, a: 0.40 },
    { s: 5.70, e: 5.79, a: 0.42 },
    { s: 5.80, e: 5.89, a: 0.44 },
    { s: 5.90, e: 5.99, a: 0.45 },
];

function lookupFuel(avg) {
    if (avg < 3.20) return 0;
    for (const t of FUEL_MATRIX) {
        if (avg >= t.s && avg <= t.e) return t.a;
    }
    return 0.45; // above max, hold at last tier
}

// ============================================================
// STATE
// ============================================================
let stops = [
    { id: 1, type: 'origin',      address: '', coords: null, statusText: '', statusClass: '', date: '', time: '', layoverWarning: '', detention: 0,  warehouseHours: true },
    { id: 2, type: 'destination', address: '', coords: null, statusText: '', statusClass: '', date: '', time: '', layoverWarning: '', detention: 60, warehouseHours: true },
];
let nextStopId = 3;
let currentMiles = 0;
let legMiles = [];
let accs = [];
let nextAccId = 1;
let dragSrcIdx = null;
let lastSchedule  = { arrivalTimes: [], departureTimes: [], latestDepartures: [] };
let lastCalcData  = null;  // stored so print can be rebuilt fresh at print time

// ============================================================
// INIT
// ============================================================
// ============================================================
// EIA LIVE FUEL PRICE
// ============================================================
const EIA_API_KEY = 'fEHQ6dpfd8Ti6ipbvdJj969fWAqmOPqiRfjEcF7P';

async function fetchEIA() {
    const btn    = document.getElementById('eiaBtn');
    const status = document.getElementById('eiaStatus');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner dark"></span> Fetching EIA data...';
    status.style.display = 'block';
    status.style.background = '#eff6ff';
    status.style.color = '#1d4ed8';
    status.style.border = '1px solid #bfdbfe';
    status.textContent = '⏳ Contacting EIA API...';

    // EIA v2 – weekly national retail diesel (No. 2 Diesel, U.S. All Grades)
    // Series: EPD2DXL0  |  Area: NUS (national)
    const url = `https://api.eia.gov/v2/petroleum/pri/gnd/data/?api_key=${EIA_API_KEY}` +
        `&frequency=weekly&data[0]=value` +
        `&facets[product][]=EPD2DXL0&facets[duoarea][]=NUS` +
        `&sort[0][column]=period&sort[0][direction]=desc&length=1`;

    try {
        const res  = await fetch(url);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const json = await res.json();
        const row  = json?.response?.data?.[0];
        if (!row || row.value == null) throw new Error('No data in response');

        const price  = parseFloat(row.value);
        const period = row.period; // e.g. "2026-03-17"

        // Populate the input and trigger recalculation
        document.getElementById('fuelAvg').value = price.toFixed(2);
        onFuelChange();

        status.style.background = '#f0fdf4';
        status.style.color      = '#15803d';
        status.style.border     = '1px solid #86efac';
        status.innerHTML = `✓ EIA data loaded — week of <strong>${period}</strong> · National diesel avg: <strong>$${price.toFixed(2)}/gal</strong>`;

        document.getElementById('fuelAvgHint').textContent =
            `Live EIA data · week of ${period} · editable`;

    } catch (err) {
        status.style.background = '#fff7ed';
        status.style.color      = '#c2410c';
        status.style.border     = '1px solid #fed7aa';
        status.textContent = `⚠ Could not reach EIA API (${err.message}) — enter the diesel average manually.`;
    }

    btn.disabled = false;
    btn.innerHTML = '🔄 Fetch Live EIA Rate';
}

// ============================================================
// INIT
// ============================================================
window.addEventListener('DOMContentLoaded', () => {
    const today = new Date();
    const fmt = d => d.toISOString().slice(0, 10);
    const valid = new Date(today); valid.setDate(valid.getDate() + 7);
    document.getElementById('quoteDate').value  = fmt(today);
    document.getElementById('validUntil').value = fmt(valid);
    renderStops();
    renderFuelMatrix();
    addAcc('Scale Ticket', '15', true);  // required, locked at $15 — cannot be removed
    accs.push({ id: nextAccId++, name: 'Layover', amount: 0, layover: true, days: 0, forcedDays: 0 });
    renderAccs();

    // Auto-fetch EIA on load
    fetchEIA();
});

// ============================================================
// STOPS
// ============================================================
function renderStops() {
    const c = document.getElementById('stopsContainer');
    let html = '';
    stops.forEach((stop, idx) => {
        const isFirst  = idx === 0;
        const isLast   = idx === stops.length - 1;
        const isMiddle = !isFirst && !isLast;
        const dotClass  = isFirst ? 'dot-origin' : isLast ? 'dot-destination' : 'dot-stop';
        const letter    = String.fromCharCode(65 + idx);
        const ph        = isFirst ? 'Pickup address, city, or zip' : isLast ? 'Delivery address, city, or zip' : 'Stop address, city, or zip';
        const dateLabel = isFirst ? 'Pickup Date' : isLast ? 'Delivery Date' : 'Stop Date';
        const timeLabel = isFirst ? 'Pickup Time' : isLast ? 'Delivery Time' : 'Stop Time';
        const removeBtn = isMiddle
            ? `<button class="btn-ghost-danger no-print" onclick="removeStop(${stop.id})" title="Remove">✕</button>`
            : '<div style="width:34px;"></div>';
        const connector  = !isLast ? `<div class="stop-connector"><span class="leg-miles-label" id="legmi-${stop.id}"></span></div>` : '';
        const statusShow = stop.statusText     ? 'show' : '';
        const warnShow   = stop.layoverWarning ? 'show' : '';

        html += `
        <div class="stop-item" id="stop-${stop.id}"
             draggable="true"
             ondragstart="onDragStart(event,${idx})"
             ondragover="onDragOver(event,${idx})"
             ondragleave="onDragLeave(event,${idx})"
             ondrop="onDrop(event,${idx})"
             ondragend="onDragEnd(event)">
          <div class="stop-drag-handle no-print" title="Drag to reorder">⠿</div>
          <div class="stop-left">
            <div class="stop-dot ${dotClass}">${letter}</div>
            ${connector}
          </div>
          <div class="stop-input-wrap">
            <input class="stop-input" type="text" placeholder="${ph}"
                   value="${escHtml(stop.address)}"
                   oninput="onStopType(${stop.id}, this.value)"
                   onblur="geocodeStop(${stop.id})">
            <div class="stop-status ${statusClass(stop)} ${statusShow}" id="status-${stop.id}">${stop.statusText}</div>
            <div class="stop-datetime">
              <div class="stop-dt-field">
                <div class="stop-dt-label req-label-sm">${dateLabel}</div>
                <input class="stop-date-input" type="date"
                       value="${escHtml(stop.date || '')}"
                       oninput="onStopDateChange(${stop.id}, this.value)"
                       id="date-${stop.id}">
              </div>
              <div class="stop-dt-field" style="max-width:160px;">
                <div class="stop-dt-label" style="color:#94a3b8;">${timeLabel} <span style="font-size:9px;">(opt)</span></div>
                <input class="stop-time-input" type="time"
                       value="${escHtml(stop.time || '')}"
                       oninput="onStopTimeChange(${stop.id}, this.value)"
                       id="time-${stop.id}">
              </div>
              <div class="stop-detention-field">
                <div class="stop-dt-label" style="color:#94a3b8;">Detention <span style="font-size:9px;">(min)</span></div>
                <input class="stop-detention-input" type="number" min="0" step="15"
                       value="${stop.detention > 0 ? stop.detention : ''}" placeholder="0"
                       oninput="onStopDetention(${stop.id}, this.value)"
                       id="detention-${stop.id}">
              </div>
            </div>
            <div class="stop-wh-toggle">
              <input type="checkbox" id="wh-${stop.id}"
                     ${stop.warehouseHours ? 'checked' : ''}
                     onchange="onStopWarehouse(${stop.id}, this.checked)">
              <label for="wh-${stop.id}">Business hours (8am–5pm)</label>
            </div>
            <div class="stop-layover-warn ${warnShow}" id="warn-${stop.id}">${escHtml(stop.layoverWarning || '')}</div>
            <div class="stop-est-arrival" id="arrival-${stop.id}" style="display:none;"></div>
            <div class="stop-req-depart"  id="reqdepart-${stop.id}"></div>
          </div>
          ${removeBtn}
        </div>`;

        // leg miles label is inline on the connector (see .leg-miles-label CSS)
    });
    c.innerHTML = html;
}
function statusClass(stop) {
    return stop.statusClass || '';
}

function onStopDateChange(id, val) {
    const s = stops.find(s => s.id === id);
    if (s) {
        s.date = val;
        const el = document.getElementById('date-' + id);
        if (el) el.classList.remove('field-error');
    }
    computeSchedule();
}

function onStopTimeChange(id, val) {
    const s = stops.find(s => s.id === id);
    if (s) s.time = val;
    computeSchedule();
}

function onStopDetention(id, val) {
    const s = stops.find(s => s.id === id);
    if (!s) return;
    s.detention = Math.max(0, parseInt(val) || 0);
    computeSchedule();

    // Over-2-hour detention: suggest a detention accessorial ($50/hr over 120 min)
    const isOrigin = stops[0].id === id;
    if (!isOrigin && s.detention > 120) {
        const overHrs = Math.ceil((s.detention - 120) / 60);
        const charge  = overHrs * 50;
        const stopLetter = String.fromCharCode(65 + stops.indexOf(s));
        const already = accs.find(a => a.detentionStopId === id);
        if (!already) {
            const confirmed = confirm(
                `Stop ${stopLetter} has ${s.detention} min of detention — ${s.detention - 120} min over the 2-hour free window.\n\n` +
                `Add a detention accessorial: ${overHrs} hr${overHrs !== 1 ? 's' : ''} × $50 = $${charge}?`
            );
            if (confirmed) {
                const acc = {
                    id: nextAccId++, name: `Detention – Stop ${stopLetter}`,
                    amount: charge, locked: false, detentionStopId: id
                };
                accs.push(acc);
                renderAccs();
                recalculate();
            }
        }
    }
}

function onStopWarehouse(id, checked) {
    const s = stops.find(s => s.id === id);
    if (s) s.warehouseHours = checked;
    computeSchedule();
}

// ============================================================
// STOP DRAG-AND-DROP REORDER
// ============================================================
function onDragStart(e, idx) {
    dragSrcIdx = idx;
    e.dataTransfer.effectAllowed = 'move';
    // Small delay so the ghost image is rendered before opacity change
    setTimeout(() => {
        const el = document.getElementById('stop-' + stops[idx].id);
        if (el) el.classList.add('dragging');
    }, 0);
}

function onDragOver(e, idx) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    if (dragSrcIdx === null || dragSrcIdx === idx) return;
    // Highlight above or below based on cursor position
    const el = document.getElementById('stop-' + stops[idx].id);
    if (!el) return;
    const rect = el.getBoundingClientRect();
    const midY = rect.top + rect.height / 2;
    el.classList.remove('drag-over-top', 'drag-over-bottom');
    el.classList.add(e.clientY < midY ? 'drag-over-top' : 'drag-over-bottom');
}

function onDragLeave(e, idx) {
    const el = document.getElementById('stop-' + stops[idx].id);
    if (el) el.classList.remove('drag-over-top', 'drag-over-bottom');
}

function onDrop(e, toIdx) {
    e.preventDefault();
    if (dragSrcIdx === null || dragSrcIdx === toIdx) { onDragEnd(e); return; }

    // Determine insert position (above or below target)
    const targetEl = document.getElementById('stop-' + stops[toIdx].id);
    let insertIdx = toIdx;
    if (targetEl) {
        const rect = targetEl.getBoundingClientRect();
        if (e.clientY >= rect.top + rect.height / 2) insertIdx = toIdx + 1;
    }

    // Splice and re-insert
    const [moved] = stops.splice(dragSrcIdx, 1);
    const finalIdx = insertIdx > dragSrcIdx ? insertIdx - 1 : insertIdx;
    stops.splice(finalIdx, 0, moved);

    // Reassign types: first = origin, last = destination, rest = stop
    stops.forEach((s, i) => {
        s.type = i === 0 ? 'origin' : i === stops.length - 1 ? 'destination' : 'stop';
    });

    // Route is now stale — reset miles and prompt recalculation
    legMiles = [];
    currentMiles = 0;
    const miEl = document.getElementById('totalMiles');
    if (miEl) { miEl.textContent = '—'; miEl.classList.remove('has-value'); }
    const manEl = document.getElementById('manualMiles');
    if (manEl) manEl.value = '';
    setDistStatus('⚠ Stop order changed — recalculate route', 'error');

    dragSrcIdx = null;
    renderStops();
    computeSchedule();
    recalculate();
}

function onDragEnd(e) {
    dragSrcIdx = null;
    document.querySelectorAll('.stop-item').forEach(el =>
        el.classList.remove('dragging', 'drag-over-top', 'drag-over-bottom')
    );
}

// Warehouse hours — truck must arrive, complete detention, and depart by 5pm.
// If arrival is too late to finish by 5pm, push detention start to next day at 8am.
const WH_OPEN  = 8;   // 08:00
const WH_CLOSE = 17;  // 17:00
function getDetentionStart(arrivalDT, useWarehouseHours, detentionMinutes) {
    if (!useWarehouseHours) return new Date(arrivalDT); // no restriction

    const detMin = detentionMinutes || 0;
    const d = new Date(arrivalDT);

    // Helper: midnight-anchored open/close on same calendar day as `ref`
    const openOf  = ref => { const t = new Date(ref); t.setHours(WH_OPEN,  0, 0, 0); return t; };
    const closeOf = ref => { const t = new Date(ref); t.setHours(WH_CLOSE, 0, 0, 0); return t; };

    // Latest the truck can START detention and still depart by 5pm
    const latestStart = new Date(closeOf(d).getTime() - detMin * 60_000);

    if (d < openOf(d)) {
        // Arrived before 8am — wait for opening
        return openOf(d);
    } else if (d > latestStart) {
        // Arrived too late to finish detention by 5pm — hold overnight, start at 8am next day
        const nextDay = new Date(d);
        nextDay.setDate(nextDay.getDate() + 1);
        return openOf(nextDay);
    }
    // Arrived during business hours with enough runway — start detention immediately
    return new Date(d);
}

// Parse a date string + optional time string into a Date object
function parseDT(dateStr, timeStr) {
    const time = (timeStr && timeStr.trim()) ? timeStr.trim() : '08:00';
    return new Date(dateStr + 'T' + time + ':00');
}

// Format a Date object to a readable string like "Mon, Mar 23 at 2:30 PM"
function formatDT(dt) {
    const days   = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const pad = n => String(n).padStart(2, '0');
    const h    = dt.getHours();
    const ampm = h >= 12 ? 'PM' : 'AM';
    const h12  = h % 12 || 12;
    return days[dt.getDay()] + ', ' + months[dt.getMonth()] + ' ' + dt.getDate() +
           ' at ' + h12 + ':' + pad(dt.getMinutes()) + ' ' + ampm;
}

// Compact date format for tight table cells: "3/23 1:00PM"
function formatDTShort(dt) {
    const pad = n => String(n).padStart(2, '0');
    const h   = dt.getHours();
    const ampm = h >= 12 ? 'PM' : 'AM';
    const h12  = h % 12 || 12;
    return (dt.getMonth()+1) + '/' + dt.getDate() + ' ' + h12 + ':' + pad(dt.getMinutes()) + ampm;
}

function computeSchedule() {
    const n      = stops.length;
    const origin = stops[0];
    const dest   = stops[n - 1];

    // ── Helper: miles for leg i ───────────────────────────────────────────────
    const getLegMiles = i =>
        legMiles.length > i ? legMiles[i] : Math.round(currentMiles / Math.max(n - 1, 1));

    // ── Clear all dynamic labels ──────────────────────────────────────────────
    stops.forEach(s => {
        s.layoverWarning = '';
        ['arrival-', 'reqdepart-'].forEach(pfx => {
            const el = document.getElementById(pfx + s.id);
            if (el) { el.textContent = ''; el.style.display = 'none'; }
        });
    });
    updateLegMilesDisplay();

    const canForward  = !!(origin && origin.date && currentMiles > 0);
    const canBackward = !!(dest   && dest.date   && currentMiles > 0);

    if (!canForward && !canBackward) {
        stops.forEach(s => {
            const el = document.getElementById('warn-' + s.id);
            if (el) { el.textContent = ''; el.classList.remove('show'); }
        });
        const rdCard  = document.getElementById('routeDetailsCard');
        const alertEl = document.getElementById('scheduleAlert');
        if (rdCard)  rdCard.style.display  = 'none';
        if (alertEl) alertEl.style.display = 'none';
        updateLayoverAcc(0);
        return;
    }

    let totalForcedDays  = 0;
    const arrivalTimes   = new Array(n).fill(null);
    const departureTimes = new Array(n).fill(null);

    // ════════════════════════════════════════════════════════════════════════
    // BACKWARD PASS — runs whenever destination date is set + miles known.
    // Computes "latest depart" for every prior stop so the driver arrives on time.
    // HOS note: backward pass uses hosLegMs(miles, 0) — conservative fresh-driver
    // assumption since we don't know exact prior on-duty status in reverse.
    // ════════════════════════════════════════════════════════════════════════
    const latestDepartures = new Array(n).fill(null);

    if (canBackward) {
        // For the destination: if warehouse hours, the truck must arrive by (5pm - detention)
        const destUseWH  = dest.warehouseHours !== false;
        const destDetMin = dest.detention || 0;
        let latestArrival = parseDT(dest.date, dest.time || '00:00');
        if (destUseWH && !dest.time) {
            // No explicit delivery time — truck must arrive by 5pm minus detention
            const closeMs = new Date(latestArrival);
            closeMs.setHours(WH_CLOSE, 0, 0, 0);
            latestArrival = new Date(closeMs.getTime() - destDetMin * 60_000);
        }

        for (let i = n - 2; i >= 0; i--) {
            const legMi      = getLegMiles(i);
            const driveMs    = hosLegMs(legMi, 0);
            let latestDep    = new Date(latestArrival.getTime() - driveMs);

            // If this stop uses warehouse hours, clamp departure to ≤ 5pm
            const stopUseWH = stops[i].warehouseHours !== false;
            if (stopUseWH) {
                const closeToday = new Date(latestDep);
                closeToday.setHours(WH_CLOSE, 0, 0, 0);
                if (latestDep > closeToday) latestDep = closeToday;
            }
            latestDepartures[i] = latestDep;

            // Show prominent "Depart by" label on stop card
            const rdEl = document.getElementById('reqdepart-' + stops[i].id);
            if (rdEl && legMi > 0) {
                rdEl.textContent = '🕐 Depart by ' + formatDT(latestDep) + ' to arrive on time';
                rdEl.style.display = 'block';
            }

            // Walk back through detention at this stop
            const detMs = (stops[i].detention || 0) * 60000;
            latestArrival = new Date(latestDep.getTime() - detMs);
        }
    }

    // ════════════════════════════════════════════════════════════════════════
    // FORWARD PASS — runs only when origin date is set.
    // Computes realistic arrival / departure times using HOS rules, tracking
    // on-duty hours carried across stops.
    // ════════════════════════════════════════════════════════════════════════
    if (canForward) {
        arrivalTimes[0]    = parseDT(origin.date, origin.time || '00:00');
        const origDetStart = getDetentionStart(arrivalTimes[0], origin.warehouseHours !== false, origin.detention || 0);
        departureTimes[0]  = new Date(origDetStart.getTime() + (origin.detention || 0) * 60000);

        // On-duty hours consumed before wheels roll: pre-trip + detention at origin
        const originDetHrs = (origin.detention || 0) / 60;
        // If detention >= reset, driver is fully rested; otherwise carry on-duty time
        let onDutyCarry = (originDetHrs >= HOS_RESET_HRS)
            ? PRE_TRIP_HRS
            : PRE_TRIP_HRS + originDetHrs;

        for (let i = 1; i < n; i++) {
            if (!departureTimes[i - 1]) break;

            const miles   = getLegMiles(i - 1);
            const driveMs = hosLegMs(miles, onDutyCarry);
            arrivalTimes[i] = new Date(departureTimes[i - 1].getTime() + driveMs);

            const isLast = (i === n - 1);
            const detMin = stops[i].detention || 0;
            const detMs  = detMin * 60000;
            const useWH  = stops[i].warehouseHours !== false;
            // Pass detention minutes so the truck must arrive early enough to depart by 5pm
            const detStart    = getDetentionStart(arrivalTimes[i], useWH, detMin);
            const effectiveDep = new Date(detStart.getTime() + detMs);

            // Arrival label — flag late if estimated arrival is after the scheduled stop date/time
            const arrEl = document.getElementById('arrival-' + stops[i].id);
            if (arrEl && miles > 0) {
                const scheduledDT = stops[i].date ? parseDT(stops[i].date, stops[i].time || '00:00') : null;
                const isLateArrival = scheduledDT && arrivalTimes[i] > scheduledDT;
                if (isLateArrival) {
                    const lateHrs = ((arrivalTimes[i] - scheduledDT) / 3_600_000).toFixed(1);
                    arrEl.textContent = (isLast ? '⚠ Late delivery: ' : '⚠ Late arrival: ')
                        + formatDT(arrivalTimes[i])
                        + ' — ' + lateHrs + 'h after scheduled time';
                    arrEl.classList.add('late');
                } else {
                    arrEl.textContent = (isLast ? '📦 Est. delivery: ' : '📍 Est. arrival: ')
                        + formatDT(arrivalTimes[i]);
                    arrEl.classList.remove('late');
                }
                arrEl.style.display = 'block';
            }

            departureTimes[i] = effectiveDep;

            // On-duty carry into next leg:
            // Driver arrives with residual on-duty from driving, then adds detention.
            // If total on-duty >= reset threshold, driver is fully rested.
            const effectiveDetMin = Math.max(detMin, isLast ? 0 : 60); // min 60 min at non-final stops
            const stopDetHrs = effectiveDetMin / 60;
            const residual = hosLegResidualHrs(miles, onDutyCarry);
            const totalOnDutyAtStop = residual + stopDetHrs;
            onDutyCarry = totalOnDutyAtStop >= HOS_RESET_HRS ? PRE_TRIP_HRS : totalOnDutyAtStop;
        }
    }

    // ── Impossible-leg warnings ────────────────────────────────────────────────
    if (canBackward && canForward) {
        for (let i = 0; i < n - 1; i++) {
            if (!latestDepartures[i] || !departureTimes[i]) continue;
            if (departureTimes[i] > latestDepartures[i]) {
                const behindMs  = departureTimes[i] - latestDepartures[i];
                const behindHrs = (behindMs / 3_600_000).toFixed(1);
                const legMi     = getLegMiles(i);
                const fromL     = String.fromCharCode(65 + i);
                const toL       = String.fromCharCode(65 + i + 1);
                stops[i].layoverWarning =
                    `⚠ Behind schedule: departs ${behindHrs}h late — `
                    + `${legMi.toLocaleString()} mi (${fromL}→${toL}) `
                    + `can't be covered in time under HOS rules`;
            }
        }
    }

    // ── Sync warning UI ───────────────────────────────────────────────────────
    stops.forEach(s => {
        const el = document.getElementById('warn-' + s.id);
        if (!el) return;
        el.textContent = s.layoverWarning || '';
        el.classList.toggle('show', !!s.layoverWarning);
    });

    // ── Schedule feasibility alert (HOS-based) ────────────────────────────────
    (function checkFeasibility() {
        const alertEl = document.getElementById('scheduleAlert');
        if (!alertEl) return;

        if (!canBackward || currentMiles <= 0) {
            alertEl.style.display = 'none'; return;
        }

        // Total drive-hours needed for full route starting fresh
        // (conservative: no detention carry between legs for the alert check)
        const requiredMs   = hosLegMs(currentMiles, PRE_TRIP_HRS);
        const requiredHrs  = requiredMs / 3_600_000;
        const requiredDays = requiredMs  / 86_400_000;

        // Available window
        const destDT      = parseDT(dest.date, dest.time || '23:59');
        const originRef   = (canForward && origin.date)
            ? parseDT(origin.date, origin.time || '00:00')
            : new Date(destDT.getTime() - requiredMs - 1);  // just used for comparisons

        const availableMs   = destDT - (canForward ? originRef : new Date(destDT.getTime() - requiredMs * 2));
        const availableDays = availableMs / 86_400_000;

        if (!canForward) {
            // Backward-only: just show "depart by" — no feasibility alert needed
            alertEl.style.display = 'none'; return;
        }

        if (availableMs <= 0) {
            alertEl.innerHTML = '<div class="alert-title">⛔ Delivery deadline is before pickup date</div>';
            alertEl.style.display = 'block'; return;
        }

        if (requiredMs > availableMs + 3_600_000) {  // >1hr tolerance
            const shortfallHrs  = ((requiredMs - availableMs) / 3_600_000).toFixed(1);
            const reqHrsStr     = requiredHrs.toFixed(1);
            const reqDaysStr    = requiredDays.toFixed(1);
            const availDaysStr  = availableDays.toFixed(1);
            alertEl.innerHTML =
                '<div class="alert-title">⚠️ Not enough time for this route under HOS rules</div>'
                + '<div class="alert-detail">'
                + currentMiles.toLocaleString() + ' mi requires <strong>'
                + reqHrsStr + ' hrs</strong> of total transit (<strong>'
                + reqDaysStr + ' days</strong> incl. required breaks &amp; 10-hr resets) '
                + '— but only <strong>' + availDaysStr + ' days</strong> available. '
                + 'Short by ~' + shortfallHrs + ' hours.'
                + '</div>';
            alertEl.style.display = 'block';
        } else {
            alertEl.style.display = 'none';
        }
    })();

    // ── Layover days: idle time between stops ─────────────────────────────────
    // A layover only happens when the driver has days where they are NOT moving —
    // i.e. the time window between two stops is LONGER than the driving time needed.
    // Per leg: layoverDays = max(0, floor(availableDays) - ceil(legMiles / 600))
    // 600 mi/day is the normal driving capacity (≈11 hrs @ 55 mph).
    // If no dates are set, no layovers are calculated.
    totalForcedDays = 0;
    for (let i = 0; i < n - 1; i++) {
        const fromStop = stops[i];
        const toStop   = stops[i + 1];
        if (!fromStop.date || !toStop.date) continue;   // need dates on both ends
        const fromDT = parseDT(fromStop.date, fromStop.time || '00:00');
        const toDT   = parseDT(toStop.date,   toStop.time   || '00:00');
        const availableDays   = (toDT - fromDT) / 86_400_000;
        const legMi           = getLegMiles(i);
        const drivingDaysNeeded = Math.ceil(legMi / 600);
        const idleDays = Math.max(0, Math.floor(availableDays) - drivingDaysNeeded);
        totalForcedDays += idleDays;
    }

    // ── Cache schedule data for print ─────────────────────────────────────────
    lastSchedule = { arrivalTimes, departureTimes, latestDepartures };

    // ── Render route details summary block ────────────────────────────────────
    renderRouteDetails(arrivalTimes, departureTimes, latestDepartures);
    updateLayoverAcc(totalForcedDays);
}

function renderRouteDetails(arrivalTimes, departureTimes, latestDepartures) {
    const card  = document.getElementById('routeDetailsCard');
    const table = document.getElementById('routeDetailsTable');
    if (!card || !table) return;

    const n = stops.length;
    const hasData = arrivalTimes && arrivalTimes.some(t => t !== null);
    if (!hasData || currentMiles === 0) { card.style.display = 'none'; return; }

    card.style.display = 'block';

    // ── Header ────────────────────────────────────────────────────────────
    let html = '<div class="rd-scroll"><table class="rd-table"><thead><tr>'
        + '<th>Stop</th><th>Location</th><th>Scheduled</th>'
        + '<th>Est. Arrival</th><th>Detention</th><th>Est. Departure</th>'
        + '<th>Deadline Depart</th><th>Status</th>'
        + '</tr></thead><tbody>';

    const dest = stops[n - 1];
    const destDT = (dest && dest.date) ? parseDT(dest.date, dest.time) : null;

    for (let i = 0; i < n; i++) {
        const s      = stops[i];
        const letter = String.fromCharCode(65 + i);
        const isFirst = i === 0;
        const isLast  = i === n - 1;
        const badgeClass = isFirst ? 'rd-origin' : isLast ? 'rd-dest' : 'rd-stop';

        const locationText = s.address
            ? (s.statusText && s.statusText.includes('✓') ? s.statusText.replace('✓ ','') : s.address)
            : '<span style="color:#94a3b8;">—</span>';

        const scheduledText = s.date
            ? formatDTShort(parseDT(s.date, s.time))
            : '<span style="color:#94a3b8;">—</span>';

        const arrText = (arrivalTimes[i] && !isFirst)
            ? formatDTShort(arrivalTimes[i])
            : (isFirst ? '<span style="color:#94a3b8;">Origin</span>' : '<span style="color:#94a3b8;">—</span>');

        const detMin = s.detention || 0;
        const detText = detMin > 0 ? detMin + 'm' : '<span style="color:#94a3b8;">—</span>';

        const depText = (departureTimes[i] && !isLast)
            ? formatDTShort(departureTimes[i])
            : (isLast ? '<span style="color:#94a3b8;">Final</span>' : '<span style="color:#94a3b8;">—</span>');

        // Deadline departure (from backward pass)
        const deadlineText = (latestDepartures && latestDepartures[i] && !isLast)
            ? formatDTShort(latestDepartures[i])
            : '<span style="color:#94a3b8;">—</span>';

        // Status tag
        let statusHTML = '';
        const useWH = s.warehouseHours !== false;
        if (isFirst) {
            statusHTML = '<span class="rd-tag rd-tag-ontime">Origin</span>';
        } else if (isLast && arrivalTimes[i] && destDT) {
            const diff = destDT.getTime() - arrivalTimes[i].getTime();
            if (diff > 0) {
                const hrs = Math.round(diff / 3600000);
                statusHTML = '<span class="rd-tag rd-tag-early">✓ ' + hrs + 'h early</span>';
            } else if (diff < 0) {
                const hrs = Math.round(-diff / 3600000);
                statusHTML = '<span class="rd-tag rd-tag-late">⚠ ' + hrs + 'h late</span>';
            } else {
                statusHTML = '<span class="rd-tag rd-tag-ontime">✓ On time</span>';
            }
        } else if (s.layoverWarning) {
            const match = s.layoverWarning.match(/(\d+) mandatory layover day/);
            const days = match ? match[1] : '';
            statusHTML = '<span class="rd-tag rd-tag-layover">⚠ ' + days + 'd layover</span>';
        } else if (arrivalTimes[i] && !isLast) {
            // Check if estimated arrival is after the scheduled stop date/time
            const stopSchedDT = s.date ? parseDT(s.date, s.time || '00:00') : null;
            if (stopSchedDT && arrivalTimes[i] > stopSchedDT) {
                const lateHrs = Math.round((arrivalTimes[i] - stopSchedDT) / 3_600_000);
                statusHTML = '<span class="rd-tag rd-tag-late">⚠ ' + lateHrs + 'h late</span>';
            } else {
                statusHTML = '<span class="rd-tag rd-tag-ontime">✓ On route</span>';
            }
        }
        if (useWH) statusHTML += '<br><span class="rd-tag rd-tag-wh">🏭 WH hrs</span>';

        html += '<tr>'
            + '<td><span class="rd-stop-badge ' + badgeClass + '">' + letter + '</span></td>'
            + '<td>' + locationText + '</td>'
            + '<td style="white-space:nowrap;">' + scheduledText + '</td>'
            + '<td style="white-space:nowrap;">' + arrText + '</td>'
            + '<td style="text-align:center;">' + detText + '</td>'
            + '<td style="white-space:nowrap;">' + depText + '</td>'
            + '<td style="white-space:nowrap;">' + deadlineText + '</td>'
            + '<td>' + statusHTML + '</td>'
            + '</tr>';
    }

    html += '</tbody></table></div>';
    table.innerHTML = html;
}

function updateLayoverAcc(totalForcedDays) {
    const layoverAcc = accs.find(a => a.layover);
    if (!layoverAcc) return;

    const prevForced = layoverAcc.forcedDays || 0;
    layoverAcc.forcedDays = totalForcedDays;

    if (totalForcedDays > layoverAcc.days) {
        layoverAcc.days   = totalForcedDays;
        layoverAcc.amount = layoverAcc.days * 300;
    }
    if (totalForcedDays < prevForced && layoverAcc.days === prevForced) {
        layoverAcc.days   = totalForcedDays;
        layoverAcc.amount = layoverAcc.days * 300;
    }

    renderAccs();
    recalculate();
}

function updateLegMilesDisplay() {
    // Show per-leg miles on each connector line (A→B, B→C, etc.)
    stops.forEach((stop, idx) => {
        if (idx === stops.length - 1) return; // no connector after last stop
        const el = document.getElementById('legmi-' + stop.id);
        if (!el) return;
        const mi = legMiles[idx];
        if (mi && mi > 0) {
            const fromLetter = String.fromCharCode(65 + idx);
            const toLetter   = String.fromCharCode(65 + idx + 1);
            el.textContent   = fromLetter + '→' + toLetter + ': ' + mi.toLocaleString() + ' mi';
            el.style.display = 'block';
        } else {
            el.textContent   = '';
            el.style.display = 'none';
        }
    });
}

function onStopType(id, val) {
    const s = stops.find(s => s.id === id);
    if (!s) return;
    s.address = val;
    s.coords  = null;
    s.statusText  = '';
    s.statusClass = '';
    const el = document.getElementById('status-' + id);
    if (el) { el.textContent = ''; el.className = 'stop-status'; }
}

function addStop() {
    // Append new stop at the bottom of the list — it becomes the next letter (C, D, etc.)
    const newStop = { id: nextStopId++, type: 'stop', address: '', coords: null, statusText: '', statusClass: '', date: '', time: '', layoverWarning: '', detention: 60, warehouseHours: true };
    stops.push(newStop);
    renderStops();
    // Scroll the new stop into view
    const newEl = document.getElementById('stop-' + newStop.id);
    if (newEl) newEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function removeStop(id) {
    stops = stops.filter(s => s.id !== id);
    renderStops();
    recalculate();
}

// ============================================================
// GEOCODING
// ============================================================
async function geocodeStop(id) {
    const s = stops.find(s => s.id === id);
    if (!s || !s.address.trim() || s.coords) return;

    setStopStatus(id, '⏳ Looking up...', 'loading');

    try {
        const q   = encodeURIComponent(s.address);
        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${q}&limit=1&countrycodes=us,ca`);
        const data = await res.json();
        if (data && data.length > 0) {
            s.coords = { lat: +data[0].lat, lon: +data[0].lon };
            const name = data[0].display_name.split(',').slice(0, 3).join(',').trim();
            s.statusText  = '✓ ' + name;
            s.statusClass = 'ok';
            setStopStatus(id, s.statusText, 'ok');
        } else {
            s.statusText  = '⚠ Address not found – try a different format';
            s.statusClass = 'error';
            setStopStatus(id, s.statusText, 'error');
        }
    } catch {
        s.statusText  = '⚠ Lookup failed – check your connection';
        s.statusClass = 'error';
        setStopStatus(id, s.statusText, 'error');
    }
}

function setStopStatus(id, text, cls) {
    const el = document.getElementById('status-' + id);
    if (!el) return;
    el.textContent = text;
    el.className   = `stop-status show ${cls}`;
}

// ============================================================
// DISTANCE CALCULATION
// ============================================================
async function calculateDistance() {
    const btn = document.getElementById('calcBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Calculating...';
    setDistStatus('', '');

    // Geocode any stops that haven't been yet
    for (const s of stops) {
        if (s.address.trim() && !s.coords) {
            await geocodeStop(s.id);
            await sleep(400); // respect Nominatim rate limit
        }
    }

    const located = stops.filter(s => s.address.trim() && s.coords);
    if (located.length < 2) {
        setDistStatus('⚠ Need at least 2 valid addresses', 'error');
        resetCalcBtn();
        return;
    }

    try {
        const coords = located.map(s => `${s.coords.lon},${s.coords.lat}`).join(';');
        const res    = await fetch(`https://router.project-osrm.org/route/v1/driving/${coords}?overview=false`);
        const data   = await res.json();

        if (data.code === 'Ok' && data.routes?.[0]) {
            const miles = Math.round(data.routes[0].distance * 0.000621371);
            currentMiles = miles;
            // Store per-leg distances for schedule computation
            legMiles = data.routes[0].legs.map(leg => Math.round(leg.distance * 0.000621371));
            const el = document.getElementById('totalMiles');
            el.textContent = miles.toLocaleString();
            el.classList.add('has-value');
            document.getElementById('manualMiles').value = '';
            updateDayDisplays(miles);
            setDistStatus('✓ Route calculated via OSRM', 'ok');
            updateLegMilesDisplay();
            computeSchedule();
            recalculate();
        } else {
            setDistStatus('⚠ Could not calculate route', 'error');
        }
    } catch {
        setDistStatus('⚠ Routing service unavailable – enter miles manually', 'error');
    }
    resetCalcBtn();
}

function resetCalcBtn() {
    const btn = document.getElementById('calcBtn');
    btn.disabled = false;
    btn.innerHTML = '📍 Calculate Distance';
}

function setDistStatus(msg, cls) {
    const el = document.getElementById('distStatus');
    el.textContent  = msg;
    el.className    = 'dist-status ' + cls;
}

function onManualMiles() {
    const val = parseFloat(document.getElementById('manualMiles').value);
    const el  = document.getElementById('totalMiles');
    if (!isNaN(val) && val > 0) {
        currentMiles = val;
        // Distribute miles equally across legs
        const numLegs = Math.max(stops.length - 1, 1);
        const milesPerLeg = Math.round(val / numLegs);
        legMiles = Array(numLegs).fill(milesPerLeg);
        updateLegMilesDisplay();
        el.textContent = val.toLocaleString();
        el.classList.add('has-value');
        updateDayDisplays(val);
    } else {
        currentMiles = 0;
        legMiles = [];
        updateLegMilesDisplay();
        el.textContent = '—';
        el.classList.remove('has-value');
        updateDayDisplays(0);
    }
    computeSchedule();
    recalculate();
}

// ============================================================
// FUEL
// ============================================================
function onFuelChange() {
    const avg = parseFloat(document.getElementById('fuelAvg').value);
    const badge = document.getElementById('fuelBadge');
    const perMileEl = document.getElementById('fuelPerMile');

    if (isNaN(avg) || avg <= 0) {
        perMileEl.value = '';
        badge.innerHTML = '';
        highlightMatrix(null);
        recalculate();
        return;
    }

    const surcharge = lookupFuel(avg);
    perMileEl.value = surcharge.toFixed(2);
    highlightMatrix(avg);

    if (surcharge === 0) {
        badge.innerHTML = `<div class="fuel-badge warn">
            <span style="font-size:22px;">⛽</span>
            <div>
                <div class="fb-label">No surcharge applies at $${avg.toFixed(2)}/gal</div>
                <div class="fb-value">$0.00 / mile</div>
            </div></div>`;
    } else {
        badge.innerHTML = `<div class="fuel-badge ok">
            <span style="font-size:22px;">⛽</span>
            <div>
                <div class="fb-label">Surcharge at $${avg.toFixed(2)}/gal national avg</div>
                <div class="fb-value">+ $${surcharge.toFixed(2)} / mile</div>
            </div></div>`;
    }
    recalculate();
}

function highlightMatrix(avg) {
    let activeRow = null;
    document.querySelectorAll('#matrixBody tr').forEach((row, i) => {
        const t = FUEL_MATRIX[i];
        if (avg !== null && avg >= t.s && avg <= t.e) {
            row.classList.add('active');
            activeRow = row;
        } else {
            row.classList.remove('active');
        }
    });
    // Scroll within the matrix container only — does not move the page
    if (activeRow) {
        const container = document.querySelector('.matrix-scroll');
        if (container) {
            const rowTop    = activeRow.offsetTop;
            const rowHeight = activeRow.offsetHeight;
            const contH     = container.clientHeight;
            container.scrollTop = rowTop - (contH / 2) + (rowHeight / 2);
        }
    }
}

// ============================================================
// ACCESSORIALS
// ============================================================
function addAcc(name = '', amount = '', locked = false) {
    accs.push({ id: nextAccId++, name, amount, locked });
    renderAccs();
}

function removeAcc(id) {
    const a = accs.find(a => a.id === id);
    if (a && a.locked) return; // cannot remove locked items
    accs = accs.filter(a => a.id !== id);
    renderAccs();
    recalculate();
}

function onAccChange(id, field, val) {
    const a = accs.find(a => a.id === id);
    if (a && !a.locked) { a[field] = val; if (field === 'amount') recalculate(); }
}

function onLayoverDays(id, val) {
    const a = accs.find(a => a.id === id);
    if (!a || !a.layover) return;
    const forced = a.forcedDays || 0;
    a.days   = Math.max(forced, parseInt(val) || 0);
    a.amount = a.days * 300;
    // Update the total display without re-rendering the whole list
    const totalEl = document.getElementById('layover-total-' + id);
    if (totalEl) totalEl.textContent = a.days > 0 ? fmt(a.amount) : '—';
    recalculate();
}

function renderAccs() {
    const c = document.getElementById('accContainer');
    if (accs.length === 0) {
        c.innerHTML = '<p style="font-size:13px;color:#94a3b8;margin-bottom:8px;">No fees added.</p>';
        return;
    }
    c.innerHTML = accs.map(a => {
        // ── Layover special row ───────────────────────────────────────────────
        if (a.layover) {
            const forced       = a.forcedDays || 0;
            const isMandatory  = forced > 0;
            const displayTotal = a.days > 0 ? fmt(a.amount) : '—';
            const badgeHTML    = isMandatory
                ? `<span class="layover-badge" style="background:#ef4444;color:white;">MANDATORY</span>`
                : `<span class="layover-badge">OPTIONAL</span>`;
            const mandatoryNote = isMandatory
                ? `<div style="font-size:11px;color:#b91c1c;margin-top:5px;font-weight:600;">
                     ⚠ ${forced} day${forced !== 1 ? 's' : ''} required by route dates — minimum locked in
                   </div>`
                : '';
            return `
            <div class="layover-row" style="${isMandatory ? 'border-color:#ef4444;background:#fff5f5;' : ''}">
                <div style="flex:1;">
                    <div style="display:flex;align-items:center;gap:7px;">
                        ${badgeHTML}
                        <span style="font-size:14px;font-weight:700;color:#111111;">Layover</span>
                        <span style="font-size:11px;color:#92400e;font-weight:600;">$300 / day</span>
                    </div>
                    ${mandatoryNote}
                </div>
                <div class="layover-days-wrap">
                    <span style="font-size:12px;color:#64748b;font-weight:600;">Days:</span>
                    <input class="layover-days-input" type="number" min="${forced}" max="30" step="1"
                           value="${a.days}" placeholder="0"
                           oninput="onLayoverDays(${a.id}, this.value)"
                           style="${isMandatory ? 'border-color:#ef4444;' : ''}">
                    <div class="layover-total" id="layover-total-${a.id}">${displayTotal}</div>
                </div>
            </div>`;
        }
        // ── Locked (required) row ─────────────────────────────────────────────
        if (a.locked) {
            return `
            <div class="acc-row" style="opacity:0.85;">
                <div class="acc-name" style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:9px;
                     padding:8px 12px;font-size:14px;color:#475569;display:flex;align-items:center;gap:6px;overflow:hidden;">
                    <span style="font-size:11px;background:#111111;color:#FCDB04;border-radius:4px;
                          padding:2px 6px;font-weight:700;letter-spacing:0.3px;white-space:nowrap;">REQUIRED</span>
                    <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${escHtml(a.name)}</span>
                </div>
                <div class="pfx-wrap acc-amt">
                    <span class="pfx">$</span>
                    <input type="number" value="${a.amount}" readonly
                           style="border-radius:0 9px 9px 0;background:#f1f5f9;color:#64748b;cursor:default;">
                </div>
                <div style="width:34px;"></div>
            </div>`;
        }
        return `
        <div class="acc-row">
            <input class="acc-name" type="text" placeholder="Fee name (e.g. Detention)"
                   value="${escHtml(a.name)}" oninput="onAccChange(${a.id},'name',this.value)">
            <div class="pfx-wrap acc-amt">
                <span class="pfx">$</span>
                <input type="number" placeholder="0.00" value="${a.amount}"
                       min="0" step="0.01" style="border-radius:0 9px 9px 0;"
                       oninput="onAccChange(${a.id},'amount',this.value)">
            </div>
            <button class="btn-ghost-danger no-print" onclick="removeAcc(${a.id})">✕</button>
        </div>`;
    }).join('');
}

// ============================================================
// CALCULATION
// ============================================================
// ── Locked business constants ──────────────────────────────
const BASE_RATE    = 3.50;   // $ per mile
const MIN_PER_DAY  = 1250;   // $ per day minimum
// ── FMCSA Hours-of-Service Constants ───────────────────────────────────────
const AVG_SPEED_MPH   = 55;   // average loaded semi speed (mph)
const MAX_DRIVE_HRS   = 11;   // max driving hours per shift
const MAX_ON_DUTY_HRS = 14;   // 14-hour on-duty window
const HOS_RESET_HRS   = 10;   // required off-duty reset between shifts
const BREAK_AFTER_HRS = 8;    // mandatory break after 8 cumulative drive-hours
const BREAK_DUR_HRS   = 0.5;  // 30-minute break duration
const PRE_TRIP_HRS    = 0.5;  // pre-trip inspection (on-duty, not driving)

// ── HOS-compliant leg drive-time calculator ─────────────────────────────────
// Returns elapsed calendar milliseconds to drive 'miles' starting with
// 'priorOnDutyHrs' already consumed in the current shift.
function hosLegMs(miles, priorOnDutyHrs) {
    if (!miles || miles <= 0) return 0;
    const pOD = Math.max(0, priorOnDutyHrs || 0);
    const driveNeeded = miles / AVG_SPEED_MPH; // pure drive hours needed

    let elapsed    = 0;
    let driveLeft  = driveNeeded;
    let driveShift = 0;             // cumulative drive-hrs this shift
    let onDutyShift = pOD;          // cumulative on-duty hrs this shift
    let brokThisShift = (pOD >= BREAK_AFTER_HRS); // already past break threshold?
    let guard = 0;

    while (driveLeft > 0.005 && guard++ < 500) {
        // Available driving in this shift (min of 11-hr drive cap and 14-hr window)
        const availDrive = Math.min(
            MAX_DRIVE_HRS   - driveShift,
            MAX_ON_DUTY_HRS - onDutyShift
        );

        if (availDrive <= 0.005) {
            // Shift exhausted — take 10-hour reset
            elapsed     += HOS_RESET_HRS;
            driveShift   = 0;
            onDutyShift  = 0;
            brokThisShift = false;
            continue;
        }

        // Mandatory 30-min break after 8 cumulative drive-hours
        if (!brokThisShift && driveShift >= BREAK_AFTER_HRS) {
            elapsed      += BREAK_DUR_HRS;
            onDutyShift  += BREAK_DUR_HRS;
            brokThisShift = true;
            continue;
        }

        // Drive up to the break point (if break not yet taken)
        const toBreak = (!brokThisShift) ? Math.max(0, BREAK_AFTER_HRS - driveShift) : Infinity;
        const chunk   = Math.min(driveLeft, availDrive, isFinite(toBreak) ? toBreak : driveLeft);

        if (chunk <= 0.001) {
            // Safety: force reset to prevent infinite loop
            elapsed += HOS_RESET_HRS; driveShift = 0; onDutyShift = 0; brokThisShift = false;
            continue;
        }

        elapsed     += chunk;
        driveLeft   -= chunk;
        driveShift  += chunk;
        onDutyShift += chunk;
    }

    return Math.round(elapsed * 3_600_000);
}

// Returns the on-duty hours accumulated in the driver's CURRENT shift
// at the end of the leg (after any resets during driving)
function hosLegResidualHrs(miles, priorOnDutyHrs) {
    if (!miles || miles <= 0) return Math.max(0, priorOnDutyHrs || 0);
    const pOD = Math.max(0, priorOnDutyHrs || 0);
    const driveNeeded = miles / AVG_SPEED_MPH;
    let driveLeft = driveNeeded;
    let driveShift = 0, onDutyShift = pOD;
    let brokThisShift = (pOD >= BREAK_AFTER_HRS);
    let guard = 0;
    while (driveLeft > 0.005 && guard++ < 500) {
        const availDrive = Math.min(MAX_DRIVE_HRS - driveShift, MAX_ON_DUTY_HRS - onDutyShift);
        if (availDrive <= 0.005) { driveShift = 0; onDutyShift = 0; brokThisShift = false; continue; }
        if (!brokThisShift && driveShift >= BREAK_AFTER_HRS) { onDutyShift += BREAK_DUR_HRS; brokThisShift = true; continue; }
        const toBreak = (!brokThisShift) ? Math.max(0, BREAK_AFTER_HRS - driveShift) : Infinity;
        const chunk = Math.min(driveLeft, availDrive, isFinite(toBreak) ? toBreak : driveLeft);
        if (chunk <= 0.001) { driveShift = 0; onDutyShift = 0; brokThisShift = false; continue; }
        driveLeft -= chunk; driveShift += chunk; onDutyShift += chunk;
    }
    return onDutyShift;
}

// Counts the number of mandatory 10-hr HOS resets for a leg
// Each reset = one overnight stop = one mandatory layover night
function countHosResets(miles, priorOnDutyHrs) {
    if (!miles || miles <= 0) return 0;
    const pOD = Math.max(0, priorOnDutyHrs || 0);
    const driveNeeded = miles / AVG_SPEED_MPH;
    let driveLeft = driveNeeded;
    let driveShift = 0, onDutyShift = pOD;
    let brokThisShift = (pOD >= BREAK_AFTER_HRS);
    let guard = 0, resets = 0;
    while (driveLeft > 0.005 && guard++ < 500) {
        const availDrive = Math.min(MAX_DRIVE_HRS - driveShift, MAX_ON_DUTY_HRS - onDutyShift);
        if (availDrive <= 0.005) { driveShift = 0; onDutyShift = 0; brokThisShift = false; resets++; continue; }
        if (!brokThisShift && driveShift >= BREAK_AFTER_HRS) { onDutyShift += BREAK_DUR_HRS; brokThisShift = true; continue; }
        const toBreak = (!brokThisShift) ? Math.max(0, BREAK_AFTER_HRS - driveShift) : Infinity;
        const chunk = Math.min(driveLeft, availDrive, isFinite(toBreak) ? toBreak : driveLeft);
        if (chunk <= 0.001) { driveShift = 0; onDutyShift = 0; brokThisShift = false; resets++; continue; }
        driveLeft -= chunk; driveShift += chunk; onDutyShift += chunk;
    }
    return resets;
}

function calcTripDays(miles) {
    // Calendar days required under HOS rules (driver starts fresh)
    const ms = hosLegMs(miles, PRE_TRIP_HRS);
    return Math.max(1, Math.ceil(ms / 86_400_000));
}

function updateDayDisplays(miles) {
    const days = calcTripDays(miles);
    const label = days + (days === 1 ? ' day' : ' days');
    const chip  = document.getElementById('estDaysChip');
    const disp  = document.getElementById('tripDaysDisplay');
    if (chip) chip.textContent = label;
    if (disp) { disp.textContent = days; disp.classList.toggle('has-value', miles > 0); }
}

function recalculate() {
    if (currentMiles <= 0) { showSummaryEmpty(); return; }

    const tripDays      = calcTripDays(currentMiles);
    const fuelPerMile   = parseFloat(document.getElementById('fuelPerMile').value)  || 0;
    const equipment     = document.getElementById('equipmentType').value;

    // Line haul — minimum only applies on trips under 350 miles
    const mileageCharge = currentMiles * BASE_RATE;
    const minimumCharge = tripDays * MIN_PER_DAY;
    const minEligible   = currentMiles < 350;
    const lineHaul      = minEligible ? Math.max(mileageCharge, minimumCharge) : mileageCharge;
    const minApplied    = minEligible && mileageCharge < minimumCharge;

    // Fuel surcharge
    const fuelTotal = currentMiles * fuelPerMile;

    // Accessorials (locked items always included)
    const accLines = accs
        .filter(a => a.name.trim() && parseFloat(a.amount) > 0)
        .map(a => ({ name: a.name, amt: parseFloat(a.amount), locked: !!a.locked }));
    const accTotal = accLines.reduce((sum, a) => sum + a.amt, 0);

    const grandTotal = lineHaul + fuelTotal + accTotal;

    showSummary({ miles: currentMiles, baseRate: BASE_RATE, mileageCharge, minimumCharge, lineHaul,
        minApplied, tripDays, minPerDay: MIN_PER_DAY, fuelPerMile, fuelTotal,
        accLines, accTotal, grandTotal, equipment });

    // Store for use at print time (so form fields like customer name are read fresh)
    lastCalcData = { miles: currentMiles, baseRate: BASE_RATE, mileageCharge, minimumCharge, lineHaul,
        minApplied, tripDays, minPerDay: MIN_PER_DAY, fuelPerMile, fuelTotal,
        accLines, accTotal, grandTotal, equipment };
}

function fmt(n) {
    return '$' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function showSummaryEmpty() {
    document.getElementById('summaryContent').innerHTML =
        '<div class="sum-empty">Enter a route and miles below<br>to see your quote total.</div>';
}

function showSummary(d) {
    const minPill = d.minApplied
        ? `<div class="min-pill">⚡ Daily minimum applied — ${d.miles} mi &lt; 350 mi (${d.tripDays}d × ${fmt(d.minPerDay)}/day)</div>` : '';

    const fuelRow = d.fuelTotal > 0 ? `
        <div class="sum-line">
            <span class="lbl">Fuel Surcharge (${fmt(d.fuelPerMile)}/mi × ${d.miles.toLocaleString()} mi)</span>
            <span class="val">${fmt(d.fuelTotal)}</span>
        </div>` : '';

    const accRows = d.accLines.map(a => `
        <div class="sum-line">
            <span class="lbl">${escHtml(a.name)}</span>
            <span class="val">${fmt(a.amt)}</span>
        </div>`).join('');

    const accSection = d.accLines.length > 0 ? `<hr class="sum-hr">${accRows}` : '';

    document.getElementById('summaryContent').innerHTML = `
        <div class="sum-line">
            <span class="lbl">${d.miles.toLocaleString()} mi × ${fmt(d.baseRate)}/mi</span>
            <span class="val ${d.minApplied ? 'strike' : ''}">${fmt(d.mileageCharge)}</span>
        </div>
        <div class="sum-line">
            <span class="lbl">Line Haul</span>
            <span class="val">${fmt(d.lineHaul)}</span>
        </div>
        ${minPill}
        <hr class="sum-hr">
        ${fuelRow}
        ${accSection}
        <hr class="sum-hr">
        <div class="sum-total">
            <span class="lbl">TOTAL</span>
            <span class="val">${fmt(d.grandTotal)}</span>
        </div>
        <div class="sum-footer">${d.equipment} · ${d.miles.toLocaleString()} miles${d.minApplied ? ' · Min rate applied' : ''}</div>`;
}

function buildPrintSummary(d) {
    // Collect quote meta from form
    const customer   = document.getElementById('customerName').value  || '—';
    const contact    = document.getElementById('contact').value       || '—';
    const quoteNum   = document.getElementById('quoteNumber').value   || '—';
    const quoteDate  = document.getElementById('quoteDate').value     || '—';
    const validUntil = document.getElementById('validUntil').value    || '—';
    const notes      = document.getElementById('notes').value         || '';
    const fuelAvgVal = document.getElementById('fuelAvg').value       || '—';
    const eiaText    = document.getElementById('fuelAvgHint')?.textContent || '';

    // Route stops (with scheduled dates)
    const filledStops = stops.filter(s => s.address.trim());
    const routeLines = filledStops.map((s, i) => {
        const label = i === 0 ? 'Origin' : i === filledStops.length - 1 ? 'Destination' : `Stop ${i}`;
        const dateStr = s.date ? formatDT(parseDT(s.date, s.time || '00:00')) : '';
        const dispAddr = titleCase(s.address);
        return `<tr>
            <td style="padding:6px 10px;color:#64748b;font-weight:700;font-size:11px;white-space:nowrap;text-transform:uppercase;letter-spacing:0.5px;">${label}</td>
            <td style="padding:6px 10px;font-size:13px;font-weight:600;">${escHtml(dispAddr)}</td>
            <td style="padding:6px 10px;font-size:11px;color:#475569;white-space:nowrap;">${escHtml(dateStr)}</td>
            ${s.detention > 0 ? `<td style="padding:6px 10px;font-size:11px;color:#92400e;white-space:nowrap;">Det: ${s.detention} min</td>` : '<td></td>'}
        </tr>`;
    }).join('');

    // Route schedule table (if schedule was computed)
    const { arrivalTimes, departureTimes, latestDepartures } = lastSchedule;
    const hasSchedule = arrivalTimes && arrivalTimes.some(t => t !== null);
    const scheduleTableHTML = hasSchedule ? (() => {
        const destDT = (() => { const d = stops[stops.length-1]; return d.date ? parseDT(d.date, d.time) : null; })();
        const rows = stops.map((s, i) => {
            const isFirst = i === 0, isLast = i === stops.length - 1;
            const letter = String.fromCharCode(65 + i);
            const dot = isFirst ? '#22c55e' : isLast ? '#6366f1' : '#f59e0b';
            const loc = titleCase(s.address) || '—';
            const sched = s.date ? formatDT(parseDT(s.date, s.time)) : '—';
            const arr = arrivalTimes[i] && !isFirst ? formatDT(arrivalTimes[i]) : isFirst ? 'Origin' : '—';
            const dep = departureTimes[i] && !isLast ? formatDT(departureTimes[i]) : isLast ? 'Final stop' : '—';
            const deadline = latestDepartures[i] && !isLast ? formatDT(latestDepartures[i]) : '—';
            let status = '';
            if (isFirst) status = 'Origin';
            else if (isLast && arrivalTimes[i] && destDT) {
                const diff = destDT - arrivalTimes[i];
                const hrs = Math.round(Math.abs(diff) / 3600000);
                status = diff > 0 ? `✓ ${hrs}h early` : diff < 0 ? `⚠ ${hrs}h late` : '✓ On time';
            } else if (arrivalTimes[i]) status = '✓ On route';
            if (s.warehouseHours !== false) status += (status ? ' · ' : '') + 'WH hrs';
            return `<tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:5px 8px;"><span style="display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:50%;background:${dot};color:white;font-size:10px;font-weight:800;">${letter}</span></td>
                <td style="padding:5px 8px;font-size:11px;">${escHtml(loc)}</td>
                <td style="padding:5px 8px;font-size:11px;white-space:nowrap;">${escHtml(sched)}</td>
                <td style="padding:5px 8px;font-size:11px;white-space:nowrap;">${escHtml(arr)}</td>
                <td style="padding:5px 8px;font-size:11px;text-align:center;">${s.detention > 0 ? s.detention+' min' : '—'}</td>
                <td style="padding:5px 8px;font-size:11px;white-space:nowrap;">${escHtml(dep)}</td>
                <td style="padding:5px 8px;font-size:11px;white-space:nowrap;">${escHtml(deadline)}</td>
                <td style="padding:5px 8px;font-size:11px;">${escHtml(status)}</td>
            </tr>`;
        }).join('');
        return `<div style="margin-bottom:28px;">
            <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#64748b;margin-bottom:10px;">Route Schedule</div>
            <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;font-family:inherit;">
                <thead><tr style="background:#111111;color:#FCDB04;">
                    <th style="padding:6px 8px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;text-align:left;">Stop</th>
                    <th style="padding:6px 8px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;text-align:left;">Location</th>
                    <th style="padding:6px 8px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;text-align:left;">Scheduled</th>
                    <th style="padding:6px 8px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;text-align:left;">Est. Arrival</th>
                    <th style="padding:6px 8px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;text-align:center;">Detention</th>
                    <th style="padding:6px 8px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;text-align:left;">Est. Departure</th>
                    <th style="padding:6px 8px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;text-align:left;">Deadline Depart</th>
                    <th style="padding:6px 8px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;text-align:left;">Status</th>
                </tr></thead>
                <tbody>${rows}</tbody>
            </table></div>
        </div>`;
    })() : '';

    // Pricing rows
    const pricingRows = [
        { desc: `Line Haul &nbsp;<span style="color:#64748b;font-weight:400;font-size:11px;">${d.miles.toLocaleString()} mi × ${fmt(d.baseRate)}/mi${d.minApplied ? ' — daily min applied' : ''}</span>`, amt: fmt(d.lineHaul) },
        ...(d.fuelTotal > 0 ? [{ desc: `Fuel Surcharge &nbsp;<span style="color:#64748b;font-weight:400;font-size:11px;">${fmt(d.fuelPerMile)}/mi × ${d.miles.toLocaleString()} mi · EIA avg $${fuelAvgVal}/gal</span>`, amt: fmt(d.fuelTotal) }] : []),
        ...d.accLines.map(a => ({ desc: escHtml(a.name) + (a.locked ? ' &nbsp;<span style="font-size:10px;background:#e0e7ff;color:#4338ca;border-radius:3px;padding:1px 5px;font-weight:700;">REQUIRED</span>' : ''), amt: fmt(a.amt) })),
    ];

    // Logo img — pull src from header
    const logoEl = document.querySelector('.header-logo img');
    const logoSrc = logoEl ? logoEl.src : '';
    const logoHtml = logoSrc
        ? `<img src="${logoSrc}" style="height:52px;width:auto;" alt="CP2 Logistics">`
        : `<div style="font-size:22px;font-weight:900;color:#0f2a4a;">AXIS CREATIVE</div>`;

    document.getElementById('printReport').innerHTML = `
    <div style="font-family:'Yantramanav',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#1e293b;max-width:820px;margin:0 auto;padding:36px 40px;position:relative;z-index:1;">

        <!-- Watermark -->
        <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-35deg);
             font-family:'Yantramanav',sans-serif;font-size:160px;font-weight:900;
             color:rgba(252,219,4,0.07);letter-spacing:20px;z-index:0;pointer-events:none;
             white-space:nowrap;">QUOTE</div>
        <!-- Report Header -->
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:32px;padding-bottom:24px;border-bottom:4px solid #FCDB04;">
            <div>${logoHtml}</div>
            <div style="text-align:right;">
                <div style="font-size:28px;font-weight:900;color:#111111;letter-spacing:-0.5px;">FREIGHT QUOTE</div>
                <div style="font-size:13px;color:#64748b;margin-top:4px;">Quote #&nbsp;<strong style="color:#1e293b;">${escHtml(quoteNum)}</strong></div>
                <div style="font-size:13px;color:#64748b;">Date:&nbsp;<strong style="color:#1e293b;">${escHtml(quoteDate)}</strong></div>
                <div style="font-size:13px;color:#64748b;">Valid Until:&nbsp;<strong style="color:#1e293b;">${escHtml(validUntil)}</strong></div>
            </div>
        </div>

        <!-- Customer + Shipment Info -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:28px;">
            <div style="background:#f8fafc;border-radius:10px;padding:18px 20px;border:1px solid #e2e8f0;">
                <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#64748b;margin-bottom:10px;">Quoted For</div>
                <div style="font-size:16px;font-weight:800;color:#0f2a4a;">${escHtml(customer)}</div>
                <div style="font-size:13px;color:#64748b;margin-top:4px;">${escHtml(contact)}</div>
            </div>
            <div style="background:#f8fafc;border-radius:10px;padding:18px 20px;border:1px solid #e2e8f0;">
                <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#64748b;margin-bottom:10px;">Shipment Details</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                    <div>
                        <div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;">Equipment</div>
                        <div style="font-size:13px;font-weight:700;">${escHtml(d.equipment)}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;">Total Miles</div>
                        <div style="font-size:13px;font-weight:700;">${d.miles.toLocaleString()} mi</div>
                    </div>
                    <div>
                        <div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;">Est. Trip Days</div>
                        <div style="font-size:13px;font-weight:700;">${d.tripDays} day${d.tripDays !== 1 ? 's' : ''}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;">Diesel Avg</div>
                        <div style="font-size:13px;font-weight:700;">$${fuelAvgVal}/gal</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Route -->
        <div style="margin-bottom:28px;">
            <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#64748b;margin-bottom:10px;">Route</div>
            <table style="width:100%;border-collapse:collapse;background:#f8fafc;border-radius:10px;overflow:hidden;border:1px solid #e2e8f0;">
                <thead><tr style="background:#f1f5f9;">
                    <th style="padding:6px 10px;font-size:10px;font-weight:700;color:#64748b;text-align:left;text-transform:uppercase;letter-spacing:0.5px;">Stop</th>
                    <th style="padding:6px 10px;font-size:10px;font-weight:700;color:#64748b;text-align:left;text-transform:uppercase;letter-spacing:0.5px;">Address</th>
                    <th style="padding:6px 10px;font-size:10px;font-weight:700;color:#64748b;text-align:left;text-transform:uppercase;letter-spacing:0.5px;">Scheduled</th>
                    <th style="padding:6px 10px;font-size:10px;font-weight:700;color:#64748b;text-align:left;text-transform:uppercase;letter-spacing:0.5px;">Detention</th>
                </tr></thead>
                <tbody>${routeLines}</tbody>
            </table>
        </div>

        <!-- Route Schedule -->
        ${scheduleTableHTML}

        <!-- Pricing Breakdown -->
        <div style="margin-bottom:28px;">
            <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#64748b;margin-bottom:10px;">Pricing Breakdown</div>
            <table style="width:100%;border-collapse:collapse;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;">
                <thead>
                    <tr style="background:#111111;color:white;">
                        <th style="text-align:left;padding:11px 16px;font-size:12px;font-weight:700;letter-spacing:0.5px;">Description</th>
                        <th style="text-align:right;padding:11px 16px;font-size:12px;font-weight:700;letter-spacing:0.5px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    ${pricingRows.map((r, i) => `
                    <tr style="background:${i % 2 === 0 ? 'white' : '#f8fafc'};">
                        <td style="padding:11px 16px;font-size:13px;font-weight:600;">${r.desc}</td>
                        <td style="padding:11px 16px;font-size:13px;font-weight:700;text-align:right;">${r.amt}</td>
                    </tr>`).join('')}
                </tbody>
                <tfoot>
                    <tr style="background:#111111;color:white;">
                        <td style="padding:14px 16px;font-size:15px;font-weight:800;">TOTAL</td>
                        <td style="padding:14px 16px;font-size:20px;font-weight:900;text-align:right;">${fmt(d.grandTotal)}</td>
                    </tr>
                </tfoot>
            </table>
            ${d.minApplied ? `<p style="font-size:11px;color:#64748b;margin-top:8px;">* Trip under 350 miles — daily minimum of ${fmt(d.minPerDay)}/day × ${d.tripDays} day(s) applied.</p>` : ''}
        </div>

        ${notes ? `
        <!-- Notes -->
        <div style="margin-bottom:28px;">
            <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#64748b;margin-bottom:10px;">Notes / Special Instructions</div>
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;font-size:13px;line-height:1.6;color:#1e293b;">${escHtml(notes)}</div>
        </div>` : ''}

        <!-- Signature Block -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;margin-top:36px;padding-top:24px;border-top:1px solid #e2e8f0;">
            <div>
                <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#64748b;margin-bottom:24px;">Authorized By</div>
                <div style="border-bottom:1.5px solid #1e293b;margin-bottom:6px;height:36px;"></div>
                <div style="font-size:11px;color:#64748b;">Signature &amp; Date</div>
            </div>
            <div>
                <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#64748b;margin-bottom:24px;">Customer Acceptance</div>
                <div style="border-bottom:1.5px solid #1e293b;margin-bottom:6px;height:36px;"></div>
                <div style="font-size:11px;color:#64748b;">Signature &amp; Date</div>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top:32px;text-align:center;font-size:10px;color:#94a3b8;border-top:1px solid #e2e8f0;padding-top:16px;">
            This quote is valid until ${escHtml(validUntil)} · Rates subject to change after expiration · CP2 Logistics
        </div>
    </div>`;
}

// ============================================================
// FUEL MATRIX TABLE
// ============================================================
function renderFuelMatrix() {
    document.getElementById('matrixBody').innerHTML = FUEL_MATRIX.map((t, i) => `
        <tr id="mrow-${i}">
            <td style="color:#475569;">${'$' + t.s.toFixed(2)} – ${'$' + t.e.toFixed(2)}</td>
            <td>${t.a === 0 ? '—' : '$' + t.a.toFixed(2)}</td>
        </tr>`).join('');
}

// ============================================================
// UTILITIES
// ============================================================
function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function titleCase(str) {
    if (!str) return str;
    // Don't re-capitalize if the string is already mixed case (geocoded addresses)
    if (str !== str.toLowerCase()) return str;
    return str.replace(/\w\S*/g, w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase());
}

function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

function validateRequiredFields() {
    const fields = [
        { id: 'customerName', label: 'Requestor Name' },
        { id: 'quoteNumber',  label: 'Quote #' },
        { id: 'contact',      label: 'Contact / Email' },
    ];
    let firstError = null;
    fields.forEach(f => {
        const el = document.getElementById(f.id);
        const empty = !el.value.trim();
        el.classList.toggle('field-error', empty);
        if (empty && !firstError) firstError = el;
    });

    // Validate stop dates (required on all stops)
    stops.forEach(s => {
        const el = document.getElementById('date-' + s.id);
        if (!el) return;
        const empty = !s.date;
        el.classList.toggle('field-error', empty);
        if (empty && !firstError) firstError = el;
    });

    if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstError.focus();
        return false;
    }
    return true;
}

function printQuote() {
    if (!validateRequiredFields()) return;
    // Rebuild print summary fresh so all form fields (customer, contact, etc.) are current
    if (lastCalcData) buildPrintSummary(lastCalcData);
    window.print();
}

function buildEmailBody() {
    const name        = document.getElementById('customerName').value || '—';
    const contact     = document.getElementById('contact').value      || '—';
    const qnum        = document.getElementById('quoteNumber').value  || '—';
    const qdate       = document.getElementById('quoteDate').value    || '';
    const validUntil  = document.getElementById('validUntil').value   || '';
    const notes       = document.getElementById('notes').value        || '';
    const equipment   = document.getElementById('equipmentType').value;
    const fuelAvg     = document.getElementById('fuelAvg').value      || '—';
    const fuelPerMile = document.getElementById('fuelPerMile').value  || '0';
    const tripDays    = calcTripDays(currentMiles);

    // Format yyyy-mm-dd → "Month DD, YYYY"
    const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    function fmtDate(raw) {
        if (!raw) return '—';
        const [y, m, d] = raw.split('-');
        return `${MONTHS[parseInt(m,10)-1]} ${parseInt(d,10)}, ${y}`;
    }
    function fmtTime(t) {
        if (!t) return '';
        const [hh, mm] = t.split(':');
        const h = parseInt(hh, 10);
        const ampm = h >= 12 ? 'PM' : 'AM';
        return `${h % 12 || 12}:${mm} ${ampm}`;
    }

    // Route lines
    const activeStops = stops.filter(s => s.address.trim());
    const routeLines = activeStops.map((s, i, arr) => {
        const label = i === 0 ? 'Origin' : i === arr.length - 1 ? 'Destination' : `Stop ${String.fromCharCode(66 + i - 1)}`;
        const dateStr = s.date ? `${fmtDate(s.date)}${s.time ? ' at ' + fmtTime(s.time) : ''}` : '';
        const line1 = `  ${label.padEnd(14)}${s.address}`;
        const line2 = dateStr ? `  ${''.padEnd(14)}${dateStr}` : '';
        return line1 + (line2 ? '\n' + line2 : '');
    });

    // Pricing
    const baseCharge  = currentMiles * BASE_RATE;
    const minCharge   = tripDays * MIN_PER_DAY;
    const minEligible = currentMiles < 350;
    const lineHaul    = minEligible ? Math.max(baseCharge, minCharge) : baseCharge;
    const minApplied  = minEligible && baseCharge < minCharge;
    const fuelTotal   = currentMiles * (parseFloat(fuelPerMile) || 0);
    const accLines    = accs.filter(a => {
        if (a.layover) return a.days > 0;
        return a.name.trim() && parseFloat(a.amount) > 0;
    });
    const accTotal   = accLines.reduce((sum, a) => sum + parseFloat(a.amount), 0);
    const grandTotal = lineHaul + fuelTotal + accTotal;

    const fmtCur = n => '$' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const W = 42; // total line width for right-aligned prices
    function priceLine(label, amount) {
        const a = fmtCur(amount);
        const spaces = W - label.length - a.length;
        return '  ' + label + ' '.repeat(Math.max(1, spaces)) + a;
    }

    const pricingLines = [
        priceLine('Line Haul' + (minApplied ? ' (min applied)' : ''), lineHaul),
        ...(fuelTotal > 0 ? [priceLine('Fuel Surcharge', fuelTotal)] : []),
        ...accLines.map(a => {
            const lbl = a.layover ? `Layover (${a.days} day${a.days !== 1 ? 's' : ''})` : a.name;
            return priceLine(lbl, parseFloat(a.amount));
        })
    ];

    const SEP  = '  ' + '─'.repeat(W);
    const SEP2 = '  ' + '═'.repeat(W);

    return [
        '╔' + '═'.repeat(W + 2) + '╗',
        '║' + '  CP2 LOGISTICS — FREIGHT QUOTE'.padEnd(W + 2) + '║',
        '╚' + '═'.repeat(W + 2) + '╝',
        '',
        `  Quote #:      ${qnum}`,
        `  Date:         ${fmtDate(qdate)}`,
        `  Valid Until:  ${fmtDate(validUntil)}`,
        '',
        SEP,
        '  PREPARED FOR',
        SEP,
        `  Name:         ${name}`,
        `  Contact:      ${contact}`,
        '',
        SEP,
        '  SHIPMENT DETAILS',
        SEP,
        `  Equipment:    ${equipment}`,
        `  Total Miles:  ${currentMiles > 0 ? currentMiles.toLocaleString() + ' mi' : 'Not calculated'}`,
        `  Est. Trip Days: ${tripDays}`,
        '',
        SEP,
        '  ROUTE',
        SEP,
        ...routeLines.length ? routeLines : ['  (No addresses entered)'],
        '',
        SEP,
        '  PRICING BREAKDOWN',
        SEP,
        ...pricingLines,
        SEP,
        priceLine('TOTAL', grandTotal),
        SEP2,
        ...(minApplied ? [``, `  * Trip under 350 mi — daily minimum of ${fmtCur(MIN_PER_DAY)}/day × ${tripDays} day(s) applied.`] : []),
        ...(notes ? ['', SEP, '  NOTES', SEP, `  ${notes}`] : []),
        '',
        SEP2,
        '  Generated by CP2 Logistics Quote Calculator',
        SEP2,
    ].join('\n');
}

function submitRequest() {
    if (!validateRequiredFields()) return;

    const recipientEl = document.getElementById('recipientEmail');
    const recipient   = recipientEl.value.trim();
    const statusEl    = document.getElementById('submitStatus');

    if (!recipient) {
        recipientEl.classList.add('field-error');
        recipientEl.focus();
        statusEl.style.display = 'block';
        statusEl.style.background = '#fee2e2';
        statusEl.style.color = '#b91c1c';
        statusEl.style.border = '1px solid #fca5a5';
        statusEl.textContent = '⚠ Please enter a recipient email address.';
        return;
    }

    if (currentMiles <= 0) {
        statusEl.style.display = 'block';
        statusEl.style.background = '#fffbeb';
        statusEl.style.color = '#b45309';
        statusEl.style.border = '1px solid #fcd34d';
        statusEl.textContent = '⚠ Please calculate or enter mileage before submitting.';
        return;
    }

    const qnum          = document.getElementById('quoteNumber').value;
    const originDateRaw = stops.find(s => s.type === 'origin')?.date || '';
    const originDate    = originDateRaw
        ? (() => { const [y,m,d] = originDateRaw.split('-'); return `${m}-${d}-${y}`; })()
        : 'TBD';
    const milesLabel  = currentMiles > 0 ? currentMiles.toLocaleString() + ' mi' : 'TBD';
    const emailText   = buildEmailBody();
    const subject     = encodeURIComponent(`Freight Quote #${qnum} — ${originDate} — ${milesLabel}`);
    const body        = encodeURIComponent(emailText);

    // Use anchor click — more reliable than window.location.href across browsers
    const a = document.createElement('a');
    a.href = `mailto:${recipient}?subject=${subject}&body=${body}`;
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);

    statusEl.style.display = 'block';
    statusEl.style.background = '#f0fdf4';
    statusEl.style.color = '#15803d';
    statusEl.style.border = '1px solid #86efac';
    statusEl.textContent = '✓ Opening your email client…';

    // Always populate fallback textarea; show it after a short delay
    const fallbackEl   = document.getElementById('emailFallback');
    const fallbackBody = document.getElementById('emailFallbackBody');
    fallbackBody.value = emailText;
    setTimeout(() => {
        fallbackEl.style.display = 'block';
        statusEl.textContent = '✓ Email client launched — if it didn\'t open, copy the quote below.';
    }, 1800);
}

function resetQuote() {
    if (confirm('Start a new quote? Current data will be cleared.')) location.reload();
}
</script>
<div id="printReport"></div>
</body>
</html>
