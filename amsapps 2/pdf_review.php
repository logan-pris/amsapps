<?php 
require_once __DIR__ . '/core/auth_check.php';
require_once __DIR__ . '/core/db.php';

$pageTitle = "Review Contract";
include 'includes/header.php'; 
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    body { background: #525659; margin: 0; padding: 0; }
    
    .review-wrapper {
        height: calc(100vh - 56px); 
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px 40px 100px 40px;
        position: relative;
    }

    /* Floating Bar - Matches Builder Styling */
    .review-controls { 
        position: sticky; 
        top: 20px; 
        margin-bottom: 40px; 
        display: flex; 
        gap: 12px; 
        background: rgba(255, 255, 255, 0.95); 
        backdrop-filter: blur(10px); 
        padding: 10px 20px; 
        border-radius: 50px; 
        z-index: 5000; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.5); 
        align-items: center; 
        border: 1px solid rgba(255,255,255,0.3);
    }

    .review-btn {
        height: 40px;
        padding: 0 20px;
        border-radius: 20px;
        border: 1px solid #ddd;
        background: #fff;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        color: #333;
        transition: all 0.2s ease;
        text-decoration: none;
        cursor: pointer;
    }

    .review-btn:hover { background: #f8f9fa; color: #0d6efd; border-color: #0d6efd; }
    .btn-submit { background: #198754; color: white; border-color: #198754; }
    .btn-submit:hover { background: #157347; color: white; }

    .pdf-viewer-container {
        background: white;
        box-shadow: 0 0 30px rgba(0,0,0,0.5);
        line-height: 0;
        margin-bottom: 50px;
    }
    
    canvas { max-width: 100%; height: auto; display: block; }

    /* Hide the hidden print iframe */
    #print-iframe { display: none; }
</style>

<div class="review-wrapper">
    <div class="review-controls">
        
    
        <a href="generator.php" class="review-btn" title="Back to Editor">
            <i class="bi bi-arrow-left"></i> <span>Edit</span>
        </a>
        
        <div style="border-left: 1px solid #ddd; height: 24px; margin: 0 5px;"></div>
        
        <button class="review-btn" onclick="downloadPDF()">
            <i class="bi bi-download"></i> <span>Download</span>
        </button>
        
        <button class="review-btn" onclick="printPDF()">
            <i class="bi bi-printer"></i> <span>Print</span>
        </button>
    
        <div style="border-left: 1px solid #ddd; height: 24px; margin: 0 5px;"></div>
        
        <button class="review-btn btn-submit" onclick="initiateSignature()">
            <i class="bi bi-pen-fill"></i> <span>Submit for Signatures</span>
        </button>
        
        <div style="border-left: 1px solid #ddd; height: 24px; margin: 0 5px;"></div>
        <a href="contract_history.php" class="review-btn" title="Close and Return to History">
            <i class="bi bi-x-lg"></i> <span>Close</span>
        </a>
    
    </div>

    <div class="pdf-viewer-container" id="canvas-holder">
        </div>
</div>

<iframe id="print-iframe"></iframe>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
<script>
    // 1. Declare variables ONCE at the top
    const pdfjsLib = window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

    let currentPdfBase64 = sessionStorage.getItem('pending_pdf_data');
    let pdfMeta = JSON.parse(sessionStorage.getItem('pending_pdf_meta') || '{}');
    const editId = pdfMeta.db_id; 

    // 2. Security Check: If no data, send back to generator
    if (!currentPdfBase64) {
        window.location.href = 'generator.php';
    }

    // 3. Update the Edit button link dynamically
    if (editId) {
        const editBtn = document.querySelector('a[href="generator.php"]');
        if(editBtn) editBtn.href = `generator.php?edit_id=${editId}`;
    }

    // 4. Convert Base64 to Blob for consistent use
    const pdfBlob = new Blob([Uint8Array.from(atob(currentPdfBase64), c => c.charCodeAt(0))], { type: 'application/pdf' });
    const pdfUrl = URL.createObjectURL(pdfBlob);

    async function renderPreview() {
        try {
            const loadingTask = pdfjsLib.getDocument({data: atob(currentPdfBase64)});
            const pdf = await loadingTask.promise;
            const holder = document.getElementById('canvas-holder');
            holder.innerHTML = ''; 

            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
                const viewport = page.getViewport({scale: 1.5});
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                canvas.className = "mb-3 shadow";
                
                holder.appendChild(canvas);
                await page.render({canvasContext: context, viewport: viewport}).promise;
            }
        } catch (err) {
            console.error("PDF Render Error:", err);
        }
    }

    function downloadPDF() {
        const link = document.createElement('a');
        const jobNum = pdfMeta._jobnum || 'Draft';
        const contractorName = pdfMeta._name || 'Contractor';
        const fileName = `${jobNum} - ${contractorName}.pdf`;
    
        link.href = pdfUrl;
        link.download = fileName;
        link.click();
    }

    function printPDF() {
        const iframe = document.getElementById('print-iframe');
        iframe.src = pdfUrl;
        
        iframe.onload = function() {
            setTimeout(() => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }, 100);
        };
    }

    function initiateSignature() {
        alert("Coming Soon.....");
    }

    // 5. Start the render
    renderPreview();
</script>