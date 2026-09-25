<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="layers"></i> Laporan Rekapitulasi BOS</h1>
        <p class="mph-subtitle">Unduh laporan bulanan/tahunan dana BOS</p>
    </div>
</div>
<div class="z-content-pad">



<div class="z-panel" style="max-width: 600px;">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="printer"></i>Cetak Laporan BOS</div></div>
    <div class="z-panel-body">
        <form onsubmit="event.preventDefault();alert('Fitur cetak laporan sedang dalam pengembangan.');">
            <div class="z-form-group">
                <label class="z-label">Periode Bulan</label>
                <input class="z-field" type="month" value="<?php echo date('Y-m'); ?>" required>
            </div>
            <div class="z-form-group">
                <label class="z-label">Format Laporan</label>
                <select class="z-field">
                    <option value="pdf">PDF Dokumen</option>
                    <option value="excel">Microsoft Excel (.xlsx)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top: 10px; width: 100%; justify-content: center;"><i data-lucide="download"></i> Unduh Laporan</button>
        </form>
    </div>
</div>
</div>
</div>