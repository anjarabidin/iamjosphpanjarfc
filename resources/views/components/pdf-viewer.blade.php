@props([
    'fileUrl' => '',
    'fileName' => 'Document',
    'height' => '750px',
    'downloadUrl' => null,
])

<div class="pdf-viewer-container" style="height: {{ $height }}; display: flex; flex-direction: column; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
    {{-- Toolbar --}}
    <div id="pdf-toolbar" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; flex-shrink: 0;">
        <button id="pdf-prev-page" onclick="pdfViewerPrevPage()" style="padding: 0.25rem 0.75rem; background: white; border: 1px solid #d1d5db; border-radius: 0.375rem; cursor: pointer; font-size: 0.875rem;">
            ← Prev
        </button>
        <span id="pdf-page-info" style="font-size: 0.875rem; color: #374151;">
            Page <span id="pdf-current-page">1</span> of <span id="pdf-total-pages">-</span>
        </span>
        <button id="pdf-next-page" onclick="pdfViewerNextPage()" style="padding: 0.25rem 0.75rem; background: white; border: 1px solid #d1d5db; border-radius: 0.375rem; cursor: pointer; font-size: 0.875rem;">
            Next →
        </button>
        <span style="margin: 0 0.25rem; color: #d1d5db;">|</span>
        <button id="pdf-zoom-out" onclick="pdfViewerZoom(-0.25)" style="padding: 0.25rem 0.75rem; background: white; border: 1px solid #d1d5db; border-radius: 0.375rem; cursor: pointer; font-size: 0.875rem;">
            −
        </button>
        <span id="pdf-zoom-level" style="font-size: 0.875rem; color: #374151; min-width: 3rem; text-align: center;">100%</span>
        <button id="pdf-zoom-in" onclick="pdfViewerZoom(0.25)" style="padding: 0.25rem 0.75rem; background: white; border: 1px solid #d1d5db; border-radius: 0.375rem; cursor: pointer; font-size: 0.875rem;">
            +
        </button>
        <div style="flex: 1;"></div>
        <span style="font-size: 0.75rem; color: #6b7280; margin-right: 0.5rem;">{{ $fileName }}</span>
        @if($downloadUrl)
            <a href="{{ $downloadUrl }}" download style="padding: 0.25rem 0.75rem; background: #2563eb; color: white; border-radius: 0.375rem; text-decoration: none; font-size: 0.875rem;">
                ⬇ Download
            </a>
        @endif
    </div>

    {{-- Canvas container --}}
    <div id="pdf-canvas-container" style="flex: 1; overflow: auto; background: #6b7280; display: flex; justify-content: center; padding: 1rem;">
        <div id="pdf-loading" style="display: flex; align-items: center; justify-content: center; width: 100%; color: white; font-size: 1rem;">
            <svg style="animation: spin 1s linear infinite; width: 1.5rem; height: 1.5rem; margin-right: 0.5rem;" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="white" stroke-width="3" stroke-dasharray="31.4 31.4" stroke-linecap="round"/>
            </svg>
            Loading PDF...
        </div>
        <canvas id="pdf-canvas" style="display: none; box-shadow: 0 4px 6px rgba(0,0,0,0.3);"></canvas>
    </div>

    {{-- Non-PDF fallback --}}
    <div id="pdf-fallback" style="display: none; flex: 1; padding: 2rem; text-align: center; background: #f9fafb;">
        <p style="color: #6b7280; margin-bottom: 1rem;">This file type cannot be previewed in the browser.</p>
        @if($downloadUrl)
            <a href="{{ $downloadUrl }}" download style="padding: 0.5rem 1.5rem; background: #2563eb; color: white; border-radius: 0.375rem; text-decoration: none;">
                Download File
            </a>
        @endif
    </div>
</div>

<style>
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function() {
    const fileUrl = @json($fileUrl);
    const fileName = @json($fileName);
    const ext = fileName.split('.').pop().toLowerCase();

    // Only render PDF files with PDF.js
    if (ext !== 'pdf') {
        document.getElementById('pdf-toolbar').style.display = 'none';
        document.getElementById('pdf-canvas-container').style.display = 'none';
        document.getElementById('pdf-loading').style.display = 'none';
        document.getElementById('pdf-fallback').style.display = 'flex';
        return;
    }

    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    let pdfDoc = null;
    let currentPage = 1;
    let scale = 1.0;
    const canvas = document.getElementById('pdf-canvas');
    const ctx = canvas.getContext('2d');

    function renderPage(num) {
        pdfDoc.getPage(num).then(function(page) {
            const viewport = page.getViewport({ scale: scale });
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };

            page.render(renderContext);
            document.getElementById('pdf-current-page').textContent = num;
            document.getElementById('pdf-zoom-level').textContent = Math.round(scale * 100) + '%';
        });
    }

    // Load the PDF
    pdfjsLib.getDocument(fileUrl).promise.then(function(pdf) {
        pdfDoc = pdf;
        document.getElementById('pdf-total-pages').textContent = pdf.numPages;
        document.getElementById('pdf-loading').style.display = 'none';
        canvas.style.display = 'block';
        renderPage(1);
    }).catch(function(error) {
        console.error('Error loading PDF:', error);
        document.getElementById('pdf-loading').innerHTML = '<span style="color: #fca5a5;">Failed to load PDF. Please try downloading instead.</span>';
    });

    // Expose navigation functions globally
    window.pdfViewerPrevPage = function() {
        if (currentPage <= 1) return;
        currentPage--;
        renderPage(currentPage);
    };

    window.pdfViewerNextPage = function() {
        if (pdfDoc && currentPage >= pdfDoc.numPages) return;
        currentPage++;
        renderPage(currentPage);
    };

    window.pdfViewerZoom = function(delta) {
        const newScale = scale + delta;
        if (newScale < 0.5 || newScale > 3.0) return;
        scale = newScale;
        renderPage(currentPage);
    };
})();
</script>
