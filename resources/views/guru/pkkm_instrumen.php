<style>
    a { text-decoration: none; }
    .z-sidebar a, .z-nav-item, .z-nav-dropdown-toggle, .z-nav-dropdown-menu a, .z-sidebar-foot a { text-decoration: none !important; }
    .z-scroll { padding-bottom: 3rem !important; }

    /* CSS Helper Utilities */
    .d-flex { display: flex !important; }
    .d-inline-flex { display: inline-flex !important; }
    .align-items-center { align-items: center !important; }
    .align-items-start { align-items: flex-start !important; }
    .justify-content-between { justify-content: space-between !important; }
    .justify-content-center { justify-content: center !important; }
    .justify-content-end { justify-content: flex-end !important; }
    .flex-wrap { flex-wrap: wrap !important; }
    .flex-column { flex-direction: column !important; }
    .gap-1 { gap: 4px !important; }
    .gap-2 { gap: 8px !important; }
    .gap-3 { gap: 14px !important; }
    .text-center { text-align: center !important; }
    .text-muted { color: #64748b !important; }
    .text-success { color: #059669 !important; }
    .text-danger { color: #ef4444 !important; }
    .me-1 { margin-right: 4px !important; }
    .me-2 { margin-right: 8px !important; }
    .ms-1 { margin-left: 4px !important; }
    .ms-2 { margin-left: 8px !important; }
    .mb-0 { margin-bottom: 0 !important; }
    .mb-1 { margin-bottom: 4px !important; }
    .mb-2 { margin-bottom: 8px !important; }
    .mb-3 { margin-bottom: 12px !important; }
    .mt-1 { margin-top: 4px !important; }
    .mt-2 { margin-top: 8px !important; }
    .w-100 { width: 100% !important; }

    /* Minimalist Banner Header (Zamrud Design System) */
    .elite-page-header {
        position: relative; padding: 1.1rem 1.6rem; margin-bottom: 1.1rem;
        background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
        border-radius: 14px; overflow: hidden; color: #ffffff;
        box-shadow: 0 4px 16px -4px rgba(6, 78, 59, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }
    .elite-page-header .header-bg-shapes {
        position: absolute; top: 0; right: 0; bottom: 0; left: 0;
        background-image: radial-gradient(circle at 85% 20%, rgba(255, 255, 255, 0.12) 0%, transparent 40%),
                          radial-gradient(circle at 15% 80%, rgba(0, 0, 0, 0.1) 0%, transparent 40%);
        z-index: 1; pointer-events: none;
    }
    .elite-page-header .header-content-inner { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; gap: 14px; flex-wrap: wrap; }
    .elite-page-header .header-text-premium h1 { font-size: 1.25rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px; color: #fff; }
    .elite-page-header .header-text-premium p { font-size: 0.8rem; color: rgba(255,255,255,0.85); margin-top: 0.2rem; margin-bottom: 0; max-width: 620px; font-weight: 400; line-height: 1.4; }
    .header-tag-pill { display: inline-flex; align-items: center; gap: 5px; background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); color: #fff; font-size: 0.65rem; font-weight: 700; padding: 2px 8px; border-radius: 999px; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 4px; backdrop-filter: blur(4px); }

    .e-btn {
        display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px;
        border-radius: 8px; font-weight: 700; font-size: 0.78rem; cursor: pointer;
        border: none; transition: 0.2s ease; text-decoration: none;
    }
    .e-btn:hover { transform: translateY(-1px); }
    .e-btn-primary { background: #ffffff; color: #047857; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
    .e-btn-primary:hover { background: #f8fafc; color: #064e3b; }

    /* Compact Assignment Banner */
    .my-assignment-banner {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
        border: 1px solid #a7f3d0; border-radius: 12px; padding: 12px 16px;
        margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap;
        box-shadow: 0 1px 4px rgba(16, 185, 129, 0.08);
    }
    .assignment-icon {
        width: 38px; height: 38px; border-radius: 10px; background: #059669; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .assignment-title { font-size: 0.92rem; font-weight: 800; color: #065f46; margin-bottom: 2px; }
    .assignment-desc { font-size: 0.78rem; color: #047857; margin-bottom: 5px; }
    .assignment-chip {
        background: #ffffff; border: 1px solid #a7f3d0; color: #065f46; padding: 3px 9px; border-radius: 6px; font-weight: 700; font-size: 0.73rem; display: inline-flex; align-items: center; gap: 6px;
    }
    .badge-peran {
        background: #059669; color: #ffffff; font-size: 0.65rem; font-weight: 700; padding: 2px 6px; border-radius: 4px;
    }

    /* Minimalist Stat Grid */
    .stat-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 10px; margin-bottom: 12px;
    }
    .stat-box {
        padding: 9px 13px; border-radius: 10px; display: flex; align-items: center; gap: 10px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); background: #fff; border: 1px solid #e2e8f0; transition: transform 0.15s;
    }
    .stat-box:hover { transform: translateY(-1px); }
    .stat-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.85); flex-shrink: 0; }
    .stat-icon i, .stat-icon svg { width: 16px !important; height: 16px !important; }
    .stat-box.green { background: #f0fdf4; border-color: #dcfce7; color: #14532d; }
    .stat-box.green .stat-icon { color: #10b981; }
    .stat-box.blue { background: #eff6ff; border-color: #dbeafe; color: #1e3a8a; }
    .stat-box.blue .stat-icon { color: #3b82f6; }
    .stat-box.amber { background: #fefce8; border-color: #fef9c3; color: #713f12; }
    .stat-box.amber .stat-icon { color: #f59e0b; }
    .stat-value { font-size: 1.25rem; font-weight: 800; line-height: 1.1; margin-bottom: 1px; }
    .stat-label { font-size: 0.68rem; font-weight: 600; color: #64748b; white-space: nowrap; }

    /* Minimalist Clean Isolation Banner (Zamrud) */
    .isolation-notice-banner {
        display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
        background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #059669; border-radius: 10px;
        padding: 8px 14px; margin-bottom: 14px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .inb-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .inb-badge {
        background: #047857; color: #ffffff; font-size: 0.7rem; font-weight: 800; padding: 3px 8px; border-radius: 6px;
        display: inline-flex; align-items: center; gap: 5px; letter-spacing: 0.3px;
    }
    .inb-text { font-size: 0.8rem; color: #1e293b; display: inline-flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .inb-label { font-weight: 700; color: #475569; }
    .inb-tag {
        background: #f0fdf4; border: 1px solid #bbf7d0; color: #14532d; padding: 2px 8px; border-radius: 6px; font-size: 0.76rem;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .inb-tag strong { color: #047857; font-weight: 800; }
    .inb-right {
        display: inline-flex; align-items: center; gap: 5px; background: #f8fafc; border: 1px solid #e2e8f0; color: #64748b;
        font-size: 0.72rem; font-weight: 600; padding: 3px 9px; border-radius: 999px; white-space: nowrap;
    }

    /* Filter Tabs Nav (If Multiple Komponen) */
    .filter-tabs-bar {
        display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 12px;
    }
    .tab-pill-btn {
        background: #ffffff; border: 1px solid #e2e8f0; color: #475569; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: 0.15s; display: inline-flex; align-items: center; gap: 6px;
    }
    .tab-pill-btn:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }
    .tab-pill-btn.active {
        background: #047857 !important; color: #ffffff !important; border-color: #047857 !important; box-shadow: 0 2px 6px rgba(4, 120, 87, 0.2);
    }

    /* Card Komponen */
    .pkkm-card {
        background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        padding: 16px; margin-bottom: 16px;
    }
    .komponen-header {
        background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #059669; border-radius: 8px;
        padding: 9px 14px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;
    }
    .badge-komp-pill {
        background: #047857; color: #ffffff; font-size: 0.7rem; font-weight: 800; padding: 3px 8px; border-radius: 5px;
        display: inline-flex; align-items: center; gap: 4px; letter-spacing: 0.3px;
    }
    .badge-tugas-pill {
        background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.68rem; font-weight: 700; padding: 2px 7px; border-radius: 5px;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .badge-komp-count {
        background: #ffffff; border: 1px solid #e2e8f0; color: #64748b; font-size: 0.7rem; font-weight: 600; padding: 3px 9px; border-radius: 999px; display: inline-flex; align-items: center; gap: 4px;
    }

    /* Sub Komponen Box */
    .sub-komponen-box {
        background: #ffffff; border: 1px solid #e2e8f0; border-radius: 9px; margin-bottom: 12px; overflow: hidden;
    }
    .sub-komponen-header {
        background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 8px 12px; display: flex; align-items: center; justify-content: space-between;
    }
    .badge-num {
        background: #047857; color: #ffffff; font-size: 0.7rem; font-weight: 800; padding: 1px 6px; border-radius: 4px; margin-right: 6px;
    }

    /* Table Indikator */
    .table-responsive { width: 100%; overflow-x: auto; }
    .table-custom { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-custom th {
        background: #f8fafc; padding: 8px 10px; font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; border-bottom: 1px solid #e2e8f0;
    }
    .table-custom td { padding: 9px 10px; border-bottom: 1px solid #f1f5f9; font-size: 0.78rem; vertical-align: top; }
    .table-custom tr:hover td { background-color: #fbfcfe; }

    /* Action Buttons in Table */
    .btn-upload-direct {
        background: #059669; color: #ffffff; padding: 4px 10px; border-radius: 6px; font-size: 0.73rem; font-weight: 700; border: none; display: inline-flex; align-items: center; gap: 4px; cursor: pointer; transition: 0.15s; text-decoration: none;
    }
    .btn-upload-direct:hover { background: #047857; transform: translateY(-1px); }
    
    .btn-web-direct {
        background: #ffffff; border: 1px solid #86efac; color: #059669; padding: 3px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; margin-top: 3px; transition: 0.15s;
    }
    .btn-web-direct:hover { background: #f0fdf4; border-color: #059669; }

    /* Uploaded Berkas Items */
    .berkas-item {
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 8px; display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 4px; transition: 0.15s;
    }
    .berkas-item:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .badge-master { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.62rem; padding: 1px 5px; border-radius: 4px; font-weight: 700; }
    .btn-del-berkas { color: #ef4444; border: none; background: transparent; cursor: pointer; padding: 2px 4px; border-radius: 4px; display: inline-flex; align-items: center; }
    .btn-del-berkas:hover { background: #fee2e2; }

    .empty-berkas-box {
        display: inline-flex; align-items: center; gap: 5px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 4px 8px; font-size: 0.72rem; color: #94a3b8; font-style: italic;
    }

    .badge-skor-success {
        display: inline-flex; align-items: center; gap: 4px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 3px 8px; border-radius: 6px; font-size: 0.76rem; font-weight: 800;
    }
    .badge-skor-pending {
        display: inline-flex; align-items: center; gap: 4px; background: #f8fafc; border: 1px solid #e2e8f0; color: #64748b; padding: 3px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 600;
    }

    .evidence-card-badge {
        display: inline-flex; align-items: center; gap: 3px;
        background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;
        border-radius: 4px; padding: 1px 6px; font-size: 0.62rem; font-weight: 700;
        cursor: pointer; transition: all 0.15s ease;
    }
    .evidence-card-badge:hover {
        background: #059669; color: #ffffff; border-color: #059669; transform: translateY(-1px);
    }

    /* Modal Custom (Sleek Zamrud Standard) */
    .modal-custom {
        display: none; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(15, 23, 42, 0.55); backdrop-filter: blur(4px);
    }
    .modal-custom.show { display: flex; align-items: center; justify-content: center; }
    .modal-dialog-custom {
        background: #ffffff; border-radius: 16px; width: 100%; max-width: 500px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; overflow: hidden; animation: modalIn 0.2s ease-out; margin: 16px;
    }
    @keyframes modalIn {
        from { transform: translateY(-16px) scale(0.97); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
    }

    /* Modal Header & Content */
    .modal-header-custom {
        padding: 14px 18px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;
        background: linear-gradient(135deg, #064e3b 0%, #047857 100%); color: #ffffff;
    }
    .modal-header-icon {
        width: 32px; height: 32px; border-radius: 8px; background: rgba(255, 255, 255, 0.18); color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .modal-header-title { margin: 0; font-weight: 800; font-size: 0.95rem; color: #ffffff; }
    .modal-header-subtitle { font-size: 0.72rem; color: rgba(255,255,255,0.85); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 380px; }
    .btn-modal-close {
        border: none; background: rgba(255, 255, 255, 0.15); border-radius: 6px; cursor: pointer; color: #ffffff; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; transition: 0.15s;
    }
    .btn-modal-close:hover { background: rgba(255, 255, 255, 0.3); }

    /* Modal Body & Forms */
    .modal-body-custom { padding: 18px 20px; }
    .form-label-custom {
        display: flex; align-items: center; gap: 5px; font-weight: 700; font-size: 0.74rem; color: #334155; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.4px;
    }
    .form-select-custom, .form-control-custom {
        width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #f8fafc; color: #0f172a; font-size: 0.82rem; outline: none; transition: all 0.15s ease; box-sizing: border-box;
    }
    .form-select-custom:focus, .form-control-custom:focus {
        border-color: #059669; background: #ffffff; box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.15);
    }

    /* Drag & Drop Styled File Upload Zone */
    .file-dropzone {
        border: 2px dashed #86efac; border-radius: 10px; background: #f0fdf4; padding: 18px 14px; text-align: center; cursor: pointer; transition: all 0.2s ease;
    }
    .file-dropzone:hover, .file-dropzone.dragover {
        border-color: #059669; background: #dcfce7; transform: translateY(-1px);
    }
    .dropzone-icon {
        width: 36px; height: 36px; border-radius: 10px; background: #ffffff; color: #059669; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto; box-shadow: 0 2px 6px rgba(5, 150, 105, 0.15);
    }
    .dropzone-title { font-size: 0.82rem; font-weight: 800; color: #065f46; margin-bottom: 3px; }
    .dropzone-hint { font-size: 0.72rem; color: #047857; margin-bottom: 6px; }
    .dropzone-note {
        font-size: 0.68rem; color: #15803d; background: #ffffff; border: 1px solid #bbf7d0; border-radius: 6px; padding: 3px 8px; display: inline-flex; align-items: center; gap: 4px;
    }
    .dropzone-selected-box {
        margin-top: 10px; background: #ffffff; border: 1px solid #86efac; border-radius: 8px; padding: 6px 10px; font-size: 0.74rem; color: #065f46; font-weight: 700; display: flex; align-items: center; justify-content: space-between; gap: 6px;
    }

    /* Modal Footer */
    .modal-footer-custom {
        padding: 12px 20px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 8px;
    }
    .btn-cancel-custom {
        background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 7px 16px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; cursor: pointer; transition: 0.15s;
    }
    .btn-cancel-custom:hover { background: #f1f5f9; color: #0f172a; }
    .btn-submit-custom {
        background: #047857; border: 1px solid #047857; color: #ffffff; padding: 7px 18px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(4, 120, 87, 0.25); transition: 0.15s;
    }
    .btn-submit-custom:hover { background: #065f46; transform: translateY(-1px); }
</style>

<!-- Minimalist Banner Header -->
<div class="elite-page-header">
    <div class="header-bg-shapes"></div>
    <div class="header-content-inner">
        <div class="header-text-premium">
            <div class="header-tag-pill"><i data-lucide="award" style="width:11px;height:11px;"></i> PKKM TAHUN 2026/2027 • PORTAL GURU</div>
            <h1><i data-lucide="clipboard-check" style="width:20px;height:20px;"></i> Instrumen Penilaian PKKM</h1>
            <p>Akses butir instrumen, pedoman bukti fisik juknis madrasah, dan unggah berkas fisik pendukung sesuai pembagian komponen tim Anda.</p>
        </div>
        <div class="header-actions-premium d-flex gap-2">
            <a href="/guru/pkkm/master-berkas" class="e-btn e-btn-primary">
                <i data-lucide="folder-kanban" style="width:14px;height:14px;"></i> Buka Daftar Berkas Master
            </a>
        </div>
    </div>
</div>

<!-- Assignment Info Banner -->
<?php if(!empty($myAssignments)): ?>
    <div class="my-assignment-banner">
        <div class="d-flex align-items-center gap-3">
            <div class="assignment-icon">
                <i data-lucide="shield-check" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div class="assignment-title">Halo, Bapak/Ibu <?php echo htmlspecialchars($guru['nama']); ?>!</div>
                <div class="assignment-desc">Anda tercatat sebagai Tim Penanggung Jawab Komponen PKKM:</div>
                <div class="d-flex gap-2 flex-wrap">
                    <?php foreach($myAssignments as $assign): ?>
                        <div class="assignment-chip">
                            <i data-lucide="tag" style="width:12px;height:12px;color:#059669;"></i>
                            <span><strong>Komponen <?php echo $assign['komponen_urutan']; ?></strong>: <?php echo htmlspecialchars($assign['nama_komponen']); ?></span>
                            <span class="badge-peran"><?php echo htmlspecialchars($assign['peran']); ?></span>
                            <?php if(!empty($assign['tugas_keterangan'])): ?>
                                <span style="font-size:0.72rem; color:#64748b; font-weight:normal;">(<?php echo htmlspecialchars($assign['tugas_keterangan']); ?>)</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="isolation-notice-banner" style="border-left-color:#3b82f6;">
        <div class="inb-left">
            <span class="inb-badge" style="background:#2563eb;">
                <i data-lucide="info" style="width:13px;height:13px;"></i> Informasi Tim PKKM
            </span>
            <div class="inb-text">
                Anda dapat meninjau seluruh butir instrumen PKKM dan berkolaborasi mengunggah berkas bukti fisik madrasah.
            </div>
        </div>
        <div class="inb-right">
            <i data-lucide="users" style="width:12px;height:12px;"></i> Semua Komponen Terbuka
        </div>
    </div>
<?php endif; ?>

<!-- Mini Stat Grid (Sleek Zamrud) -->
<div class="stat-grid">
    <div class="stat-box green">
        <div class="stat-icon"><i data-lucide="layers"></i></div>
        <div>
            <div class="stat-value"><?php echo $totalKomponen; ?></div>
            <div class="stat-label"><?php echo (!empty($isSupervisor)) ? 'Komponen PKKM' : 'Komponen Tugas Anda'; ?></div>
        </div>
    </div>
    <div class="stat-box blue">
        <div class="stat-icon"><i data-lucide="check-square"></i></div>
        <div>
            <div class="stat-value"><?php echo $totalIndikator; ?></div>
            <div class="stat-label"><?php echo (!empty($isSupervisor)) ? 'Butir Indikator' : 'Indikator Komponen Anda'; ?></div>
        </div>
    </div>
    <div class="stat-box amber">
        <div class="stat-icon"><i data-lucide="folder-check"></i></div>
        <div>
            <div class="stat-value"><?php echo $totalBerkas; ?></div>
            <div class="stat-label"><?php echo (!empty($isSupervisor)) ? 'Total Berkas Siap' : 'Berkas Siap Komponen Anda'; ?></div>
        </div>
    </div>
</div>

<!-- Header Info & Isolasi Komponen Notice -->
<?php if(empty($data)): ?>
    <div class="pkkm-card text-center my-3" style="padding: 3rem 1.5rem;">
        <i data-lucide="shield-alert" style="width:42px;height:42px;color:#f59e0b;margin:0 auto 10px auto;"></i>
        <h4 style="font-weight:800; color:#0f172a; font-size:1.1rem; margin-bottom:6px;">Belum Ada Komponen Yang Ditugaskan</h4>
        <p style="color:#64748b; max-width:480px; margin:0 auto; font-size:0.82rem;">
            Akun Anda belum tercatat pada pembagian tim komponen PKKM. Hubungi Administrator untuk menetapkan komponen yang menjadi tanggung jawab Anda.
        </p>
    </div>
<?php elseif(count($data) === 1): 
    $singleKomp = reset($data);
?>
    <div class="isolation-notice-banner">
        <div class="inb-left">
            <span class="inb-badge">
                <i data-lucide="shield-check" style="width:13px;height:13px;"></i> Komponen Khusus Tugas Anda
            </span>
            <div class="inb-text">
                <span class="inb-label">Fokus:</span>
                <span class="inb-tag">
                    <i data-lucide="layers" style="width:11px;height:11px;color:#059669;"></i>
                    <strong>Komponen <?php echo $singleKomp['urutan']; ?> — <?php echo htmlspecialchars($singleKomp['nama_komponen']); ?></strong>
                </span>
            </div>
        </div>
        <div class="inb-right">
            <i data-lucide="lock" style="width:12px;height:12px;"></i> Instrumen komponen lain disembunyikan
        </div>
    </div>
<?php else: ?>
    <!-- Filter Komponen Tabs (Jika Guru ditugaskan > 1 komponen atau Supervisor) -->
    <div class="filter-tabs-bar" id="komponenTabs">
        <button type="button" class="tab-pill-btn active" onclick="filterKomponenView('all', this)">
            <i data-lucide="grid" style="width:13px;height:13px;"></i> Semua Tugas Saya (<?php echo count($data); ?> Komponen)
        </button>
        <?php foreach ($data as $komp): ?>
            <button type="button" class="tab-pill-btn" onclick="filterKomponenView('komp-<?php echo $komp['id']; ?>', this)">
                Komponen <?php echo $komp['urutan']; ?>
            </button>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Daftar Komponen -->
<?php foreach ($data as $komp): 
    $isMyKomponen = in_array((int)$komp['id'], $myKomponenIds);
    $totalSubKomp = count($komp['sub_komponens'] ?? []);
    $totalIndikatorKomp = 0;
    if (!empty($komp['sub_komponens'])) {
        foreach($komp['sub_komponens'] as $sub) {
            $totalIndikatorKomp += count($sub['indikators'] ?? []);
        }
    }
    $totalIndikatorKomp += count($komp['indikators'] ?? []);
?>
<div class="pkkm-card komponen-section" id="komp-section-<?php echo $komp['id']; ?>" data-komponen-id="<?php echo $komp['id']; ?>" data-is-my="<?php echo $isMyKomponen ? '1' : '0'; ?>">
    <div class="komponen-header">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <span class="badge-komp-pill">
                <i data-lucide="layers" style="width:11px;height:11px;"></i> Komponen <?php echo $komp['urutan']; ?>
            </span>
            <span style="font-size:0.95rem; font-weight:800; color:#0f172a;">
                <?php echo htmlspecialchars($komp['nama_komponen']); ?>
            </span>
            <?php if($isMyKomponen): ?>
                <span class="badge-tugas-pill"><i data-lucide="star" style="width:11px;height:11px;"></i> Tugas Anda</span>
            <?php endif; ?>
        </div>
        <div class="badge-komp-count">
            <i data-lucide="check-circle-2" style="width:12px;height:12px;color:#059669;"></i>
            <span><?php echo $totalSubKomp > 0 ? $totalSubKomp . ' Sub • ' : ''; ?><?php echo $totalIndikatorKomp; ?> Indikator</span>
        </div>
    </div>

    <?php 
    $renderIndikatorTable = function($indikatorList) {
        if (empty($indikatorList)) return;
    ?>
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">No</th>
                        <th style="width: 42%;">Indikator & Bukti Fisik Juknis</th>
                        <th style="width: 32%;">Arsip Berkas Digital Terunggah</th>
                        <th style="width: 14%; text-align: center;">Skor Penilaian</th>
                        <th style="width: 12%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($indikatorList as $ind): ?>
                    <tr>
                        <td style="text-align: center; font-weight: 700; color: #94a3b8; font-size: 0.76rem;"><?php echo $ind['urutan']; ?></td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a; margin-bottom: 6px; font-size: 0.82rem; line-height: 1.4;">
                                <?php echo htmlspecialchars($ind['indikator']); ?>
                            </div>
                            <div style="font-size: 0.73rem; color: #475569; background: #f8fafc; padding: 7px 10px; border-radius: 7px; border-left: 3px solid #059669; line-height: 1.4;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <strong style="color:#065f46; font-size:0.71rem;">
                                        <i data-lucide="file-text" style="width:12px;height:12px;display:inline-block;vertical-align:text-bottom;"></i> Bukti Fisik Juknis:
                                    </strong>
                                    <span class="evidence-card-badge" onclick="showPanduanBukti('<?php echo htmlspecialchars(addslashes($ind['indikator'])); ?>', <?php echo $ind['id']; ?>)" role="button" title="Petunjuk & Checklist Bukti Fisik">
                                        <i data-lucide="sparkles" style="width:9px;height:9px;"></i>
                                        <span>Panduan</span>
                                    </span>
                                </div>
                                <?php echo nl2br(htmlspecialchars($ind['bukti_fisik'])); ?>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($ind['berkas'])): ?>
                                <?php foreach ($ind['berkas'] as $b): ?>
                                    <div class="berkas-item">
                                        <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 220px;" title="<?php echo htmlspecialchars($b['nama_file']); ?>">
                                            <i data-lucide="<?php echo $b['tipe'] === 'link' ? 'link' : 'file-text'; ?>" style="width: 12px; height: 12px; margin-right: 4px; color:#059669; vertical-align:middle;"></i>
                                            <a href="<?php echo $b['url']; ?>" target="_blank" style="color:#1e293b; font-weight:600; font-size:0.74rem;">
                                                <?php echo htmlspecialchars($b['nama_file']); ?>
                                            </a>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <?php if(!empty($b['is_master'])): ?>
                                                <span class="badge-master">Master</span>
                                            <?php else: ?>
                                                <form method="POST" action="/guru/pkkm/berkas/delete" onsubmit="return confirmDeleteBerkas(event, this);" style="margin:0;">
                                                    <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                                                    <button type="submit" class="btn-del-berkas" title="Hapus Berkas"><i data-lucide="trash-2" style="width: 12px; height: 12px;"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-berkas-box">
                                    <i data-lucide="folder-dashed" style="width:12px;height:12px;"></i>
                                    <span>Belum ada berkas terunggah</span>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <?php if(!empty($ind['skor']) && $ind['skor'] > 0): ?>
                                <span class="badge-skor-success">
                                    <i data-lucide="check" style="width:11px;height:11px;"></i> Skor: <?php echo $ind['skor']; ?>
                                </span>
                            <?php else: ?>
                                <span class="badge-skor-pending">
                                    <i data-lucide="clock" style="width:11px;height:11px;"></i> Belum Dinilai
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <div class="d-flex flex-column gap-1 align-items-center">
                                <button type="button" onclick="openUploadModal(<?php echo $ind['id']; ?>, '<?php echo htmlspecialchars(addslashes($ind['indikator'])); ?>')" class="btn-upload-direct">
                                    <i data-lucide="upload-cloud" style="width: 12px; height: 12px;"></i> Upload
                                </button>
                                <?php if(stripos($ind['bukti_fisik'], 'website') !== false || stripos($ind['bukti_fisik'], 'web') !== false): ?>
                                    <button type="button" onclick="attachWebLink(<?php echo $ind['id']; ?>)" class="btn-web-direct" title="Pasang Web Madrasah Otomatis">
                                        <i data-lucide="globe" style="width:11px;height:11px;"></i> Pasang Web
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php }; ?>

    <!-- Sub-Komponen Loop -->
    <?php if (!empty($komp['sub_komponens'])): ?>
        <?php foreach ($komp['sub_komponens'] as $sub): ?>
            <div class="sub-komponen-box">
                <div class="sub-komponen-header">
                    <div>
                        <span class="badge-num"><?php echo $sub['urutan']; ?></span>
                        <span style="font-weight: 700; color: #1e293b; font-size: 0.82rem;"><?php echo htmlspecialchars($sub['nama_sub']); ?></span>
                    </div>
                    <span style="font-size: 0.68rem; color: #64748b; font-weight: 600;"><?php echo count($sub['indikators'] ?? []); ?> Butir Indikator</span>
                </div>
                <?php $renderIndikatorTable($sub['indikators']); ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($komp['indikators'])): ?>
        <div class="sub-komponen-box mt-2">
            <div class="sub-komponen-header">
                <div><span class="badge-num"><?php echo $komp['urutan']; ?></span> <span style="font-weight: 700; color: #1e293b; font-size: 0.82rem;">Indikator Umum</span></div>
                <span style="font-size: 0.68rem; color: #64748b; font-weight: 600;"><?php echo count($komp['indikators']); ?> Butir Indikator</span>
            </div>
            <?php $renderIndikatorTable($komp['indikators']); ?>
        </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<!-- Modal Upload Berkas (Sleek Zamrud Design System) -->
<div id="uploadModal" class="modal-custom">
    <div class="modal-dialog-custom">
        <form method="POST" action="/guru/pkkm/berkas/save" enctype="multipart/form-data" style="margin:0;">
            <input type="hidden" name="indikator_id" id="modal_indikator_id">
            
            <div class="modal-header-custom">
                <div class="d-flex align-items-center gap-2">
                    <div class="modal-header-icon">
                        <i data-lucide="upload-cloud" style="width:18px;height:18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-header-title">Upload Bukti Fisik PKKM</h5>
                        <div class="modal-header-subtitle" id="modalIndikatorPreview">Mampu mengembangkan struktur organisasi...</div>
                    </div>
                </div>
                <button type="button" onclick="closeUploadModal()" class="btn-modal-close" title="Tutup">
                    <i data-lucide="x" style="width:16px;height:16px;"></i>
                </button>
            </div>

            <div class="modal-body-custom">
                <div class="mb-3">
                    <label class="form-label-custom">
                        <i data-lucide="layers" style="width:12px;height:12px;color:#059669;"></i> Tipe Berkas
                    </label>
                    <select name="tipe" id="tipe_berkas" class="form-select-custom" onchange="toggleTipeInput()">
                        <option value="file">📄 File Dokumen / Foto Kegiatan</option>
                        <option value="link">🔗 Tautan Eksternal (Google Drive / Website)</option>
                    </select>
                </div>

                <div id="input_file_container" class="mb-3">
                    <label class="form-label-custom">
                        <i data-lucide="file-up" style="width:12px;height:12px;color:#059669;"></i> Pilih File / Foto Bukti Fisik
                    </label>
                    <div class="file-dropzone" id="fileDropzone" onclick="document.getElementById('file_upload_input').click()">
                        <div class="dropzone-icon">
                            <i data-lucide="cloud-upload" style="width:20px;height:20px;"></i>
                        </div>
                        <div class="dropzone-title">Klik atau Seret Berkas ke Sini</div>
                        <div class="dropzone-hint">Format didukung: PDF, Word, Excel, JPG, PNG</div>
                        <div class="dropzone-note">
                            <i data-lucide="sparkles" style="width:11px;height:11px;"></i> Foto otomatis digabung menjadi 1 Album PDF resmi
                        </div>
                        <div id="dropzone_selected_box" class="dropzone-selected-box" style="display:none;"></div>
                    </div>
                    <input type="file" id="file_upload_input" name="file_upload[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" style="display:none;" onchange="updateFilePreview(this)">
                </div>

                <div id="input_link_container" class="mb-3" style="display: none;">
                    <label class="form-label-custom">
                        <i data-lucide="link" style="width:12px;height:12px;color:#059669;"></i> URL / Link Bukti
                    </label>
                    <input type="url" name="url" class="form-control-custom" placeholder="https://drive.google.com/..." autocomplete="off">
                </div>

                <div class="mb-2">
                    <label class="form-label-custom">
                        <i data-lucide="edit-3" style="width:12px;height:12px;color:#059669;"></i> Keterangan / Nama File Dokumen (Opsional)
                    </label>
                    <input type="text" name="nama_file" class="form-control-custom" placeholder="Contoh: Berita Acara Sosialisasi Visi Misi">
                </div>
            </div>

            <div class="modal-footer-custom">
                <button type="button" onclick="closeUploadModal()" class="btn-cancel-custom">Batal</button>
                <button type="submit" class="btn-submit-custom">
                    <i data-lucide="check" style="width:14px;height:14px;"></i> Simpan Berkas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form Pasang Web Madrasah -->
<form id="attachWebForm" method="POST" action="/guru/pkkm/bukti-attach-website" style="display:none;">
    <input type="hidden" name="indikator_id" id="web_indikator_id">
    <input type="hidden" name="url" value="https://mtsroudlotussholihin.sch.id">
    <input type="hidden" name="nama_file" value="Portal Resmi Madrasah (mtsroudlotussholihin.sch.id)">
</form>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('msg') === 'saved') {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Bukti fisik berhasil diunggah.', timer: 2000, showConfirmButton: false });
        } else if (urlParams.get('msg') === 'deleted') {
            Swal.fire({ icon: 'success', title: 'Dihapus!', text: 'Berkas bukti fisik telah dihapus.', timer: 2000, showConfirmButton: false });
        } else if (urlParams.get('msg') === 'website_attached') {
            Swal.fire({ icon: 'success', title: 'Terpasang!', text: 'Tautan resmi website madrasah berhasil dipasang.', timer: 2000, showConfirmButton: false });
        }

        // Drag & drop handlers for upload modal
        const dropzone = document.getElementById('fileDropzone');
        if (dropzone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('dragover');
                }, false);
            });

            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                const input = document.getElementById('file_upload_input');
                if (files && files.length > 0) {
                    input.files = files;
                    updateFilePreview(input);
                }
            }, false);
        }
    });

    function updateFilePreview(input) {
        const previewBox = document.getElementById('dropzone_selected_box');
        if (!previewBox) return;

        if (input.files && input.files.length > 0) {
            let text = '';
            if (input.files.length === 1) {
                text = input.files[0].name;
            } else {
                text = input.files.length + ' file dipilih (' + input.files[0].name + ', ...)';
            }
            previewBox.innerHTML = '<span style="display:inline-flex; align-items:center; gap:5px;"><i data-lucide="check-circle" style="width:13px;height:13px;color:#059669;"></i> ' + text + '</span><span style="font-size:0.68rem; color:#64748b; font-weight:normal;">Klik untuk ganti</span>';
            previewBox.style.display = 'flex';
            if (typeof lucide !== 'undefined') lucide.createIcons();
        } else {
            previewBox.style.display = 'none';
        }
    }

    function filterKomponenView(filter, btn = null) {
        const navLinks = document.querySelectorAll('#komponenTabs .tab-pill-btn');
        navLinks.forEach(link => link.classList.remove('active'));
        if (btn) {
            btn.classList.add('active');
        } else if (typeof event !== 'undefined' && event && event.currentTarget) {
            event.currentTarget.classList.add('active');
        }

        const cards = document.querySelectorAll('.komponen-section');
        cards.forEach(card => {
            const kompId = card.getAttribute('data-komponen-id');
            if (filter === 'all' || filter === 'komp-' + kompId) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function confirmDeleteBerkas(event, form) {
        event.preventDefault();
        Swal.fire({
            title: 'Hapus Bukti Fisik?',
            text: 'File bukti fisik yang telah diunggah akan dihapus secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return false;
    }

    function openUploadModal(indikatorId, indikatorText) {
        document.getElementById('modal_indikator_id').value = indikatorId;
        document.getElementById('modalIndikatorPreview').textContent = indikatorText.length > 55 ? indikatorText.substring(0, 55) + '...' : indikatorText;
        
        // Reset file preview
        const fileInput = document.getElementById('file_upload_input');
        if (fileInput) fileInput.value = '';
        const previewBox = document.getElementById('dropzone_selected_box');
        if (previewBox) previewBox.style.display = 'none';

        document.getElementById('uploadModal').classList.add('show');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.remove('show');
    }

    // Close modal on outside click & escape key
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('uploadModal');
        if (e.target === modal) {
            closeUploadModal();
        }
    });
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeUploadModal();
        }
    });

    function toggleTipeInput() {
        const tipe = document.getElementById('tipe_berkas').value;
        if (tipe === 'file') {
            document.getElementById('input_file_container').style.display = 'block';
            document.getElementById('input_link_container').style.display = 'none';
        } else {
            document.getElementById('input_file_container').style.display = 'none';
            document.getElementById('input_link_container').style.display = 'block';
        }
    }

    function attachWebLink(indikatorId) {
        Swal.fire({
            title: 'Pasang Tautan Website?',
            text: 'Tautan portal resmi https://mtsroudlotussholihin.sch.id akan otomatis ditautkan ke butir indikator ini.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            confirmButtonText: 'Ya, Pasang 1-Klik',
            cancelButtonText: 'Batal'
        }).then((res) => {
            if (res.isConfirmed) {
                document.getElementById('web_indikator_id').value = indikatorId;
                document.getElementById('attachWebForm').submit();
            }
        });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include __DIR__ . '/../pkkm/_pkkm_panduan_modal.php'; ?>
