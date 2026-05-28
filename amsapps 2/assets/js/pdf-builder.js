/** * 1. GLOBAL UI EXPORTS 
 * Defined at the top so HTML onclick events work immediately.
 **/
window.hasUnsavedChanges = false;
window.isGridOn = false;

window.toggleGrid = function() {
    console.log("Grid toggled");
    window.isGridOn = !window.isGridOn;
    const btn = document.getElementById('btnGrid');
    const dz = document.getElementById('drop-zone');
    if (!dz || !btn) return;

    if (window.isGridOn) {
        dz.classList.add('show-grid');
        btn.classList.remove('grid-off');
    } else {
        dz.classList.remove('show-grid');
        btn.classList.add('grid-off');
    }
};

window.handleCloseRequest = function() {
    if (window.hasUnsavedChanges) {
        document.getElementById('unsavedModal').style.display = 'flex';
    } else {
        window.location.href = "admin_template.php";
    }
};

window.closeModal = () => document.getElementById('unsavedModal').style.display = 'none';
window.exitWithoutSaving = () => window.location.href = "admin_template.php";
window.saveAndExit = async () => { 
    await window.saveMapping(); 
    window.location.href = "admin_template.php"; 
};

window.setJustification = (dir) => setJustification(dir);
window.saveMapping = () => saveMapping();
window.startDemoMode = () => startDemoMode();
window.stopDemoMode = () => stopDemoMode();

/** * 2. CONFIGURATION & STATE
 **/
const pdfjsLib = window['pdfjs-dist/build/pdf'];
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

let pdfDoc = null, scale = 1.5;
const GRID_SIZE = 10;
let selectedField = null;
let dropZone = null;

const SAMPLE_DATA = {
    "_jobnum": "26-0001", "_jobname": "AMS Events Sample", "_jobloc": "AMS Events",
    "_jobdates": "1/1/2026 - 12/1/2026", "_jobrate": "400", "_pdrate": "70",
    "_name": "AMS Operator", "_dept": "General", "_role": "Operator",
    "_phone": "(972) 570 - 1118", "_email": "email@ams.events", "_rateqty": "6",
    "_travelqty": "2", "_pdqty": "8", "_rate": "$400.00", "_travel": "$200.00",
    "_pd": "$70.00", "_ratetotal": "$2,400.00", "_traveltotal": "$400.00",
    "_pdtotal": "$560.00", "text_22sqff": "$300.00", "_total": "$3,660.00", "_terms": "Net 30"
};

/** * 3. CORE INITIALIZATION 
 **/
async function init() {
    console.log("Initializing PDF Builder...");
    dropZone = document.getElementById('drop-zone');
    
    if (!dropZone) {
        console.error("Critical Error: #drop-zone element not found in DOM.");
        return;
    }

    // Attach drag listeners BEFORE the PDF loads
    setupDragAndDrop();

    try {
        if (typeof PDF_PATH === 'undefined') throw new Error("PDF_PATH is not defined");
        const loadingTask = pdfjsLib.getDocument(PDF_PATH);
        pdfDoc = await loadingTask.promise;
        
        await renderPage(1);
        await loadExistingMapping();
        console.log("PDF initialization complete.");
    } catch (err) {
        console.error("Initialization Error:", err);
    }
}

/** * 4. INTERACTION LOGIC
 **/
function setupDragAndDrop() {
    console.log("Attaching Drag & Drop listeners...");

    // Sidebar tags
    document.querySelectorAll('.draggable-tag').forEach(tag => {
        tag.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('text/plain', e.target.dataset.key);
            e.dataTransfer.effectAllowed = "copy";
        });
    });

    // Drop Zone 
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault(); // MANDATORY: Allows the drop event to fire
        e.dataTransfer.dropEffect = "copy";
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        const key = e.dataTransfer.getData('text/plain');
        if (!key) return;

        console.log("Tag dropped:", key);
        const rect = dropZone.getBoundingClientRect();
        let x = Math.round((e.clientX - rect.left) / GRID_SIZE) * GRID_SIZE;
        let y = Math.round((e.clientY - rect.top) / GRID_SIZE) * GRID_SIZE;

        const newField = createPlacedField(key, x, y);
        selectField(newField);
        window.hasUnsavedChanges = true;
    });

    // Deselect logic
    dropZone.addEventListener('click', (e) => {
        if (e.target === dropZone) {
            document.querySelectorAll('.placed-field').forEach(f => f.classList.remove('active'));
            selectedField = null;
            const alignGroup = document.getElementById('alignmentGroup');
            if (alignGroup) alignGroup.style.display = 'none';
        }
    });
}

function createPlacedField(key, x, y, align = 'left', width = null) {
    const div = document.createElement('div');
    div.className = `placed-field text-${align}`;
    div.style.left = x + 'px'; 
    div.style.top = y + 'px';
    if (width) div.style.width = width + 'px';
    
    div.dataset.key = key; 
    div.dataset.align = align;
    div.setAttribute('tabindex', '0');
    div.innerHTML = `<span>${key}</span><small>×</small>`;
    
    div.querySelector('small').onclick = (e) => { 
        e.stopPropagation(); 
        div.remove(); 
        window.hasUnsavedChanges = true; 
    };
    
    div.onclick = (e) => { 
        e.stopPropagation(); 
        selectField(div); 
    };
    
    div.onmousedown = (e) => { 
        if (e.target.className !== 'resizer') dragElement(div, e); 
    };
    
    makeResizable(div);
    dropZone.appendChild(div);
    return div;
}

function dragElement(elmnt, e) {
    selectField(elmnt);
    let pos3 = e.clientX, pos4 = e.clientY;
    
    document.onmouseup = () => { 
        document.onmouseup = null; 
        document.onmousemove = null; 
    };
    
    document.onmousemove = (e) => {
        let pos1 = pos3 - e.clientX, pos2 = pos4 - e.clientY;
        pos3 = e.clientX; pos4 = e.clientY;
        
        elmnt.style.top = (Math.round((elmnt.offsetTop - pos2) / GRID_SIZE) * GRID_SIZE) + "px";
        elmnt.style.left = (Math.round((elmnt.offsetLeft - pos1) / GRID_SIZE) * GRID_SIZE) + "px";
        window.hasUnsavedChanges = true;
    };
}

function makeResizable(elmnt) {
    const resizer = document.createElement('div');
    resizer.className = 'resizer';
    elmnt.appendChild(resizer);

    resizer.addEventListener('mousedown', function(e) {
        e.stopPropagation(); 
        e.preventDefault();
        window.addEventListener('mousemove', resize);
        window.addEventListener('mouseup', stopResize);
    });

    function resize(e) {
        const width = e.pageX - elmnt.getBoundingClientRect().left;
        if (width > 20) {
            elmnt.style.width = Math.round(width / GRID_SIZE) * GRID_SIZE + 'px';
            window.hasUnsavedChanges = true;
        }
    }
    
    function stopResize() {
        window.removeEventListener('mousemove', resize);
        window.removeEventListener('mouseup', stopResize);
    }
}

/** * 5. RENDERING & DATA
 **/
async function renderPage(num) {
    const page = await pdfDoc.getPage(num);
    const viewport = page.getViewport({ scale: scale });
    const canvas = document.getElementById('pdf-render');
    const context = canvas.getContext('2d');
    
    canvas.height = viewport.height; 
    canvas.width = viewport.width;
    
    dropZone.style.width = viewport.width + 'px'; 
    dropZone.style.height = viewport.height + 'px';
    
    await page.render({ canvasContext: context, viewport: viewport }).promise;
}

function selectField(el) {
    document.querySelectorAll('.placed-field').forEach(f => f.classList.remove('active'));
    selectedField = el;
    el.classList.add('active');
    
    const alignGroup = document.getElementById('alignmentGroup');
    if (alignGroup) alignGroup.style.display = 'flex';
    updateAlignButtons(el.dataset.align || 'left');
}

function setJustification(dir) {
    if (!selectedField) return;
    selectedField.classList.remove('text-left', 'text-center', 'text-right');
    selectedField.classList.add('text-' + dir);
    selectedField.dataset.align = dir;
    window.hasUnsavedChanges = true;
    updateAlignButtons(dir);
}

function updateAlignButtons(activeDir) {
    ['Left', 'Center', 'Right'].forEach(dir => {
        const btn = document.getElementById('btnAlign' + dir);
        if (btn) {
            dir.toLowerCase() === activeDir ? btn.classList.add('active-align') : btn.classList.remove('active-align');
        }
    });
}

async function loadExistingMapping() {
    try {
        const res = await fetch(`api/get_template_mapping.php?id=${TEMPLATE_ID}`);
        const mapping = await res.json();
        if (mapping?.fields) {
            const h = document.getElementById('pdf-render').height;
            mapping.fields.forEach(f => {
                createPlacedField(
                    f.field_key, 
                    parseFloat(f.pos_x) * scale, 
                    h - (parseFloat(f.pos_y) * scale) - (10 * scale), 
                    f.alignment || 'left',
                    f.width ? parseFloat(f.width) * scale : null
                );
            });
        }
    } catch (e) { 
        console.error("Load failed", e); 
    }
}

async function saveMapping() {
    const fields = []; 
    const h = document.getElementById('pdf-render').height;
    
    document.querySelectorAll('.placed-field').forEach(el => {
        fields.push({
            field_key: el.dataset.key, 
            page_number: 1, 
            pos_x: (parseFloat(el.style.left) / scale),
            pos_y: ((h - parseFloat(el.style.top)) / scale) - 10, 
            font_size: 11, 
            alignment: el.dataset.align || 'left',
            width: el.style.width ? parseFloat(el.style.width) / scale : null
        });
    });

    try {
        const res = await fetch('api/save_template_config.php', {
            method: 'POST', 
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ template_id: TEMPLATE_ID, fields: fields })
        });
        if (res.ok) { 
            alert('Saved!'); 
            window.hasUnsavedChanges = false; 
        }
    } catch (err) {
        console.error("Save failed:", err);
    }
}

function startDemoMode() {
    document.getElementById('btnDemo').classList.add('text-warning');
    document.querySelectorAll('.placed-field').forEach(field => {
        field.querySelector('span').innerText = SAMPLE_DATA[field.dataset.key] || "Sample";
        field.classList.add('demo-active');
    });
}

function stopDemoMode() {
    document.getElementById('btnDemo').classList.remove('text-warning');
    document.querySelectorAll('.placed-field').forEach(field => {
        field.querySelector('span').innerText = field.dataset.key;
        field.classList.remove('demo-active');
    });
}

// Kick off initialization
init();