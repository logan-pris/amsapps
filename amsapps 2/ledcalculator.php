<?php 
$pageTitle = "AMS Apps - LED Calculator";

require_once __DIR__ . '/core/auth_check.php';
require_once __DIR__ . '/core/db.php'; 

// Fetch products - Updated to include typical wattage
$query = "SELECT * FROM led_products ORDER BY model_name ASC";
$stmt = $pdo->query($query);
$db_products = ['indoor' => [], 'outdoor' => []];

if ($stmt) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cat = $row['category'];
        if (isset($db_products[$cat])) {
            $db_products[$cat][] = [
                'id' => $row['id'],
                'name' => $row['model_name'],
                'w_m' => $row['panel_width_m'],
                'h_m' => $row['panel_height_m'],
                'px_w' => $row['pixels_w'],
                'px_h' => $row['pixels_h'],
                'weight' => $row['weight_lbs'],
                'img' => $row['image_url'],
                'typical_watts' => $row['typical_watts_per_panel'], // New field
                'max_watts' => $row['max_watts_per_panel']         // Renamed/mapped
            ];
        }
    }
}

include 'includes/header.php'; 
?>

<style>
    body { background-color: #f8f9fa; color: #212529; font-family: 'Inter', sans-serif; }
    .calculator-wrapper { background: #ffffff; border-radius: 12px; padding: 40px; margin-top: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #dee2e6; }
    .section-title { font-weight: 800; text-transform: uppercase; color: #adb5bd; font-size: 1.5rem; margin-bottom: 20px; letter-spacing: 1px; }
    .step-number { background: #e7f1ff; color: #0d6efd; border-radius: 50%; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; margin-right: 12px; font-weight: bold; font-size: 0.8rem; vertical-align: middle; }
    .input-label { text-transform: uppercase; font-weight: 700; color: #6c757d; font-size: 0.85rem; letter-spacing: 1px; }
    .section-locked { opacity: 0.3; pointer-events: none; filter: grayscale(1); }
    .unit-selector-row { display: flex; width: 100%; background: #f1f3f5; border: 1px solid #dee2e6; border-radius: 6px; overflow: hidden; margin-bottom: 20px; }
    .unit-btn { flex: 1; border: none; padding: 12px; background: transparent; color: #495057; font-weight: 700; font-size: 0.9rem; transition: 0.2s; border-right: 1px solid #dee2e6; }
    .unit-btn:last-child { border-right: none; }
    .unit-btn.active { background: #0d6efd; color: #ffffff; }
    .form-select, .form-control { background-color: #ffffff; border: 1px solid #ced4da; color: #212529; height: 3.5rem; font-weight: 600; }
    .override-input { display: none; margin-top: 10px; border: 2px solid #0d6efd; }
    #panelInfoSection { display: none; margin-top: 4rem; padding-top: 2rem; border-top: 2px solid #f1f3f5; }
    .spec-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f3f5; }
    .spec-label { font-weight: 700; text-transform: uppercase; color: #6c757d; font-size: 0.9rem; }
    .spec-value { font-weight: 700; color: #212529; font-size: 1.1rem; }
    .text-blue { color: #0d6efd !important; }
    .amp-box { border-left: 1px solid #dee2e6; padding-left: 20px; }
</style>

<div class="container py-5">
    <div class="calculator-wrapper">
        <h2 class="section-title">Input</h2>
        
        <div class="row mb-4">
            <div class="col-12 mb-2"><span class="step-number">1</span><span class="input-label">Product</span></div>
            <div class="col-md-6 mb-2">
                <select class="form-select" id="category" onchange="filterProducts()">
                    <option value="">SELECT CATEGORY</option>
                    <option value="indoor">LED PANEL INDOOR</option>
                    <option value="outdoor">LED PANEL OUTDOOR</option>
                </select>
            </div>
            <div class="col-md-6">
                <select class="form-select section-locked" id="product" onchange="showProductDetails()">
                    <option value="">SELECT PRODUCT</option>
                </select>
            </div>
        </div>

        <div id="step2" class="row mb-4 section-locked">
            <div class="col-12 mb-2"><span class="step-number">2</span><span class="input-label">Input Units</span></div>
            <div class="col-12">
                <div class="unit-selector-row">
                    <button class="unit-btn in-unit active" onclick="setInputUnits('feet')">FEET</button>
                    <button class="unit-btn in-unit" onclick="setInputUnits('meters')">METERS</button>
                    <button class="unit-btn in-unit" onclick="setInputUnits('tiles')">TILES</button>
                </div>
            </div>
        </div>

        <div id="step3" class="row mb-5 section-locked">
            <div class="col-12 mb-2"><span class="step-number">3</span><span class="input-label">Dimensions</span></div>
            <div class="col-md-6 mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <label class="small fw-bold text-uppercase">Width: <span id="widthLabel" class="text-blue">0</span> <span id="inUnitTextW">feet</span></label>
                    <div class="form-check"><input class="form-check-input" type="checkbox" id="wOverride" onchange="toggleOverride('width')"><label class="form-check-label small text-muted">OVERRIDE</label></div>
                </div>
                <input type="range" class="form-range" id="widthSlider" oninput="runCalc()">
                <input type="number" class="form-control override-input" id="widthManual" oninput="runCalc()">
            </div>
            <div class="col-md-6 mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <label class="small fw-bold text-uppercase">Height: <span id="heightLabel" class="text-blue">0</span> <span id="inUnitTextH">feet</span></label>
                    <div class="form-check"><input class="form-check-input" type="checkbox" id="hOverride" onchange="toggleOverride('height')"><label class="form-check-label small text-muted">OVERRIDE</label></div>
                </div>
                <input type="range" class="form-range" id="heightSlider" oninput="runCalc()">
                <input type="number" class="form-control override-input" id="heightManual" oninput="runCalc()">
            </div>
        </div>

        <div id="panelInfoSection">
            <h2 class="section-title">Output</h2>
            <div class="row">
                <div class="col-md-5 text-center mb-4">
                    <img id="productDisplayImg" src="" class="img-fluid rounded border shadow-sm" style="background: #f8f9fa; min-height: 300px;">
                </div>
                <div class="col-md-7">
                    <div class="unit-selector-row">
                        <button class="unit-btn out-unit active" onclick="setOutputUnits('feet')">FEET</button>
                        <button class="unit-btn out-unit" onclick="setOutputUnits('meters')">METERS</button>
                        <button class="unit-btn out-unit" onclick="setOutputUnits('tiles')">TILES</button>
                    </div>

                    <div class="spec-list">
                        <div class="spec-row">
                            <span class="spec-label">Dimensions Total</span>
                            <div class="text-end">
                                <div id="outDimRow1" class="spec-value text-blue">--</div>
                                <div id="outDimRow2" class="spec-label" style="font-size:0.75rem">--</div>
                            </div>
                        </div>
                        <div class="spec-row"><span class="spec-label">Pixels</span><span class="spec-value" id="outPixels">--</span></div>
                        <div class="spec-row"><span class="spec-label">Total Weight</span><span class="spec-value" id="outWeight">--</span></div>
                        <div class="spec-row"><span class="spec-label">Aspect Ratio</span><span class="spec-value" id="outAspect">--</span></div>
                        
                        <div class="row mt-4">
                            <div class="col-6">
                                <span class="spec-label d-block mb-1">Amperage 110V (Typical/Max)</span>
                                <span class="spec-value text-blue" id="outTypical110" style="font-size: 1.1rem;">--</span><span>  /  </span><span class="spec-value text-danger" id="outMax110" style="font-size: 1.1rem;">--</span>
                            </div>
                           
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <span class="spec-label d-block mb-1">Amperage 240V (Typical/Max)</span>
                                <span class="spec-value text-blue" id="outTypical240" style="font-size: 1.1rem;">--</span><span>  /  </span><span class="spec-value text-danger" id="outMax240" style="font-size: 1.1rem;">--</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const productData = <?php echo json_encode($db_products); ?>;
let activeInputUnit = 'feet';
let activeOutputUnit = 'feet';
let pW = 0.5, pH = 0.5, pxW = 128, pxH = 128;
let pTypicalWatts = 0, pMaxWatts = 0; // Updated wattage variables
let currentTilesW = 10, currentTilesH = 6;
const MAX_TILES = 50;

function filterProducts() {
    const cat = document.getElementById('category').value;
    const prodDropdown = document.getElementById('product');
    prodDropdown.innerHTML = '<option value="">SELECT PRODUCT</option>';
    if (cat !== "" && productData[cat]) {
        prodDropdown.classList.remove('section-locked');
        productData[cat].forEach(p => {
            let opt = document.createElement('option');
            opt.value = p.id; opt.innerText = p.name;
            opt.dataset.w_m = p.w_m; opt.dataset.h_m = p.h_m;
            opt.dataset.px_w = p.px_w; opt.dataset.px_h = p.px_h;
            opt.dataset.weight = p.weight; opt.dataset.img = p.img;
            opt.dataset.typicalWatts = p.typical_watts; // New
            opt.dataset.maxWatts = p.max_watts;         // New
            prodDropdown.appendChild(opt);
        });
    } else { lockFromStep(2); }
}

function showProductDetails() {
    const prod = document.getElementById('product').value;
    if (prod !== "") {
        const sel = document.getElementById('product').options[document.getElementById('product').selectedIndex];
        pW = parseFloat(sel.dataset.w_m); pH = parseFloat(sel.dataset.h_m);
        pxW = parseInt(sel.dataset.px_w); pxH = parseInt(sel.dataset.px_h);
        pTypicalWatts = parseFloat(sel.dataset.typicalWatts); // New
        pMaxWatts = parseFloat(sel.dataset.maxWatts);         // New
        document.getElementById('productDisplayImg').src = sel.dataset.img;
        document.getElementById('step2').classList.remove('section-locked');
        document.getElementById('step3').classList.remove('section-locked');
        document.getElementById('panelInfoSection').style.display = 'block';
        setInputUnits(activeInputUnit);
    } else { lockFromStep(2); }
}

function lockFromStep(step) {
    if(step <= 2) {
        document.getElementById('product').classList.add('section-locked');
        document.getElementById('step2').classList.add('section-locked');
    }
    document.getElementById('step3').classList.add('section-locked');
    document.getElementById('panelInfoSection').style.display = 'none';
}

function setInputUnits(unit) {
    activeInputUnit = unit;
    const wS = document.getElementById('widthSlider'), hS = document.getElementById('heightSlider');
    
    document.querySelectorAll('.in-unit').forEach(btn => btn.classList.toggle('active', btn.innerText.toLowerCase() === unit));

    if (unit === 'tiles') {
        wS.min = 1; wS.max = MAX_TILES; wS.step = 1; wS.value = currentTilesW;
        hS.min = 1; hS.max = MAX_TILES; hS.step = 1; hS.value = currentTilesH;
    } else if (unit === 'meters') {
        wS.min = pW; wS.max = (MAX_TILES * pW).toFixed(2); wS.step = pW; wS.value = (currentTilesW * pW).toFixed(2);
        hS.min = pH; wS.max = (MAX_TILES * pH).toFixed(2); hS.step = pH; hS.value = (currentTilesH * pH).toFixed(2);
    } else {
        const maxFtW = (MAX_TILES * pW * 3.28084).toFixed(2);
        const maxFtH = (MAX_TILES * pH * 3.28084).toFixed(2);
        wS.min = (pW * 3.28084).toFixed(2); wS.max = maxFtW; wS.step = 0.01; wS.value = (currentTilesW * pW * 3.28084).toFixed(2);
        hS.min = (pH * 3.28084).toFixed(2); hS.max = maxFtH; hS.step = 0.01; hS.value = (currentTilesH * pH * 3.28084).toFixed(2);
    }
    document.getElementById('inUnitTextW').innerText = unit;
    document.getElementById('inUnitTextH').innerText = unit;
    runCalc();
}

function setOutputUnits(unit) {
    activeOutputUnit = unit;
    document.querySelectorAll('.out-unit').forEach(btn => btn.classList.toggle('active', btn.innerText.toLowerCase() === unit));
    runCalc();
}

function toggleOverride(axis) {
    const isChecked = document.getElementById(axis === 'width' ? 'wOverride' : 'hOverride').checked;
    const slider = document.getElementById(axis + 'Slider'), manual = document.getElementById(axis + 'Manual');
    if (isChecked) { slider.style.display = 'none'; manual.style.display = 'block'; manual.value = slider.value; } 
    else { slider.style.display = 'block'; manual.style.display = 'none'; slider.value = manual.value; }
    runCalc();
}

function runCalc() {
    const prodVal = document.getElementById('product').value;
    if (prodVal === "") return;
    
    const wInput = parseFloat(document.getElementById('wOverride').checked ? document.getElementById('widthManual').value : document.getElementById('widthSlider').value) || 0;
    const hInput = parseFloat(document.getElementById('hOverride').checked ? document.getElementById('heightManual').value : document.getElementById('heightSlider').value) || 0;
    const sel = document.getElementById('product').options[document.getElementById('product').selectedIndex];

    if (activeInputUnit === 'feet') {
        currentTilesW = Math.ceil((wInput * 0.3048) / pW);
        currentTilesH = Math.ceil((hInput * 0.3048) / pH);
    } else if (activeInputUnit === 'meters') {
        currentTilesW = Math.ceil(wInput / pW);
        currentTilesH = Math.ceil(hInput / pH);
    } else {
        currentTilesW = Math.round(wInput);
        currentTilesH = Math.round(hInput);
    }

    if (currentTilesW > MAX_TILES) currentTilesW = MAX_TILES;
    if (currentTilesH > MAX_TILES) currentTilesH = MAX_TILES;

    document.getElementById('widthLabel').innerText = wInput;
    document.getElementById('heightLabel').innerText = hInput;

    const totalTiles = currentTilesW * currentTilesH;
    const mW = currentTilesW * pW, mH = currentTilesH * pH;
    const ftW = mW * 3.28084, ftH = mH * 3.28084;
    
    const r1 = document.getElementById('outDimRow1'), r2 = document.getElementById('outDimRow2');
    if (activeOutputUnit === 'feet') {
        r1.innerText = `${Math.floor(ftW)}ft, ${((ftW % 1) * 12).toFixed(2)}in x ${Math.floor(ftH)}ft, ${((ftH % 1) * 12).toFixed(2)}in`;
        r2.innerText = `${(ftW * ftH).toFixed(2)} sq ft Total`;
    } else if (activeOutputUnit === 'meters') {
        r2.innerText = `${mW.toFixed(1)*1000}mm x ${mH.toFixed(2)*1000}mm \n ${(mW * mH).toFixed(2)}m² Total Area`;
        r1.innerText = `${mW.toFixed(2)}m x ${mH.toFixed(2)}m`;
    
    } else {
        r1.innerText = `${currentTilesW} tiles x ${currentTilesH} tiles`;
        r2.innerText = `${totalTiles} Total Tiles`;
    }

    document.getElementById('outPixels').innerText = `${currentTilesW * pxW} x ${currentTilesH * pxH}`;
    document.getElementById('outWeight').innerText = (totalTiles * parseFloat(sel.dataset.weight)).toLocaleString() + " lbs";
    document.getElementById('outAspect').innerText = ((currentTilesW * pxW) / (currentTilesH * pxH)).toFixed(2);
    
    // Power Calc Logic
    const totalTypicalWatts = totalTiles * pTypicalWatts;
    const totalMaxWatts = totalTiles * pMaxWatts;

    // Display Typical
    document.getElementById('outTypical110').innerHTML = `<span>${ (totalTypicalWatts / 110).toFixed(1) }A</span>`;
    document.getElementById('outTypical240').innerHTML = `<span>${ (totalTypicalWatts / 240).toFixed(1) }A</span>`;
    
    // Display Max
    document.getElementById('outMax110').innerHTML = `<span>${ (totalMaxWatts / 110).toFixed(1) }A</span>`;
    document.getElementById('outMax240').innerHTML = `<span>${ (totalMaxWatts / 240).toFixed(1) }A</span>`;
}
</script>