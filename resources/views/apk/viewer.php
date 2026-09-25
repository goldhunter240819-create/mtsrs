<style>
    .viewer-container { padding: 20px 15px; display: flex; flex-direction: column; align-items: center; min-height: calc(100vh - 120px); }
    
    #pdf-render { width: 100%; max-width: 600px; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); background: white; display: none; }
    
    .pdf-nav { width: 100%; max-width: 600px; background: rgba(255, 255, 255, 0.95); padding: 12px 15px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between; margin-top: 15px; box-sizing: border-box; }
    .pdf-nav button { border: none; padding: 10px 15px; border-radius: 10px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.9rem; }
    .btn-prev { background: #f1f5f9; color: #475569; }
    .btn-next { background: var(--apk-primary); color: white; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3); }
    .page-info { font-size: 0.9rem; font-weight: 800; background: #ecfdf5; padding: 8px 16px; border-radius: 20px; color: var(--apk-primary); }
    
    #loading { text-align: center; padding: 40px; color: #475569; }
    #loading svg { animation: spin 1s linear infinite; color: var(--apk-primary); margin-bottom: 10px; }
    @keyframes spin { 100% { transform: rotate(360deg); } }
</style>

<div class="apk-vector-header" style="padding-bottom: 25px; align-items: center; justify-content: flex-start; gap: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
    <a href="javascript:window.history.back();" style="color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; background: rgba(255,255,255,0.25); flex-shrink: 0;">
        <i data-lucide="arrow-left" style="width: 20px;"></i>
    </a>
    <div style="flex: 1; overflow: hidden; color: white;">
        <p style="margin: 0; font-size: 0.75rem; opacity: 0.9; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Sedang Membaca</p>
        <h1 style="margin: 0; font-size: 1.15rem; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($page_title) ?></h1>
    </div>
</div>

<div class="viewer-container">
    <?php if ($doc_ext === 'pdf'): ?>
        
        <div id="loading">
            <i data-lucide="loader-2" style="width: 40px; height: 40px;"></i>
            <p style="margin: 0; font-weight: 700; margin-top: 5px;">Memuat Dokumen...</p>
        </div>

        <div style="width: 100%; max-width: 600px; display: flex; flex-direction: column; align-items: center;">
            <canvas id="pdf-render"></canvas>
            
            <div id="pdf-nav" class="pdf-nav" style="display: none;">
                <button id="prev-page" class="btn-prev">
                    <i data-lucide="chevron-left" style="width: 16px;"></i> Sblm
                </button>
                <div class="page-info">
                    Hal <span id="page-num">0</span> / <span id="page-count">0</span>
                </div>
                <button id="next-page" class="btn-next">
                    Lanjut <i data-lucide="chevron-right" style="width: 16px;"></i>
                </button>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
        <script>
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
            const url = '<?= $doc_url ?>';
            const loading = document.getElementById('loading');
            const canvas = document.getElementById('pdf-render');
            const navBar = document.getElementById('pdf-nav');
            const ctx = canvas.getContext('2d');
            
            let pdfDoc = null, pageNum = 1, pageRendering = false, pageNumPending = null, totalPages = 0;

            const pageNumSpan = document.getElementById('page-num');
            const pageCountSpan = document.getElementById('page-count');
            const btnPrev = document.getElementById('prev-page');
            const btnNext = document.getElementById('next-page');

            pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
                pdfDoc = pdfDoc_;
                totalPages = pdfDoc.numPages;
                pageCountSpan.textContent = totalPages;
                loading.style.display = 'none';
                canvas.style.display = 'block';
                navBar.style.display = 'flex';
                renderPage(pageNum);
            }).catch(function(error) {
                loading.innerHTML = '<i data-lucide="alert-circle" style="width: 40px; height: 40px; color: #ef4444; margin-bottom: 10px;"></i><p>Gagal memuat PDF.</p>';
                if(window.lucide) lucide.createIcons();
            });

            function renderPage(num) {
                pageRendering = true;
                pdfDoc.getPage(num).then(function(page) {
                    const containerWidth = canvas.parentElement.clientWidth || window.innerWidth - 30;
                    const unscaledViewport = page.getViewport({scale: 1});
                    const baseScale = containerWidth / unscaledViewport.width;
                    const outputScale = Math.max(window.devicePixelRatio || 1, 2); 
                    const viewport = page.getViewport({scale: baseScale * outputScale});

                    canvas.width = Math.floor(viewport.width);
                    canvas.height = Math.floor(viewport.height);

                    const renderContext = { canvasContext: ctx, viewport: viewport };
                    page.render(renderContext).promise.then(function() {
                        pageRendering = false;
                        if (pageNumPending !== null) {
                            renderPage(pageNumPending);
                            pageNumPending = null;
                        }
                    });
                });
                pageNumSpan.textContent = num;
                updateButtons();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function queueRenderPage(num) {
                if (pageRendering) pageNumPending = num;
                else renderPage(num);
            }

            btnPrev.addEventListener('click', () => { if (pageNum > 1) { pageNum--; queueRenderPage(pageNum); } });
            btnNext.addEventListener('click', () => { if (pageNum < totalPages) { pageNum++; queueRenderPage(pageNum); } });

            function updateButtons() {
                btnPrev.style.opacity = pageNum <= 1 ? '0.4' : '1';
                btnPrev.style.pointerEvents = pageNum <= 1 ? 'none' : 'auto';
                btnNext.style.opacity = pageNum >= totalPages ? '0.4' : '1';
                btnNext.style.pointerEvents = pageNum >= totalPages ? 'none' : 'auto';
            }
        </script>
        
    <?php elseif (in_array($doc_ext, ['jpg', 'jpeg', 'png'])): ?>
        <img src="<?= $doc_url ?>" style="max-width: 100%; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
    <?php else: ?>
        <div style="padding: 40px; text-align: center; background: white; border-radius: 16px; width: 100%; max-width: 400px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <i data-lucide="file-down" style="width: 50px; height: 50px; color: #94a3b8; margin-bottom: 15px;"></i>
            <p style="font-weight: 700; color: #334155;">Format tidak dapat dipratinjau.</p>
            <a href="<?= $doc_url ?>" target="_blank" style="display: inline-block; background: var(--apk-primary); color: white; padding: 10px 20px; border-radius: 8px; font-weight: bold; text-decoration: none; margin-top: 10px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);">Unduh File</a>
        </div>
    <?php endif; ?>
</div>
