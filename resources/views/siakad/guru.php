<?php
// data variables: $guruList, $inst, $title
?>
<style>
    /* ─── Bulk Bar ─── */
    .bulk-bar {
        display: none; position: sticky; top: 0; z-index: 100;
        background: #065f46; color: white; padding: 0.5rem 1.25rem;
        border-radius: 14px 14px 0 0; justify-content: space-between; align-items: center;
        animation: slideDown 0.3s ease;
    }
    @keyframes slideDown { from { transform: translateY(-100%); } to { transform: translateY(0); } }
    .bulk-info { display: flex; align-items: center; gap: 8px; font-weight: 700; }
    .count-badge { background: #fff; color: #065f46; padding: 1px 8px; border-radius: 8px; font-size: 0.85rem; }
    .bulk-actions { display: flex; gap: 8px; }
    /* ─── DataTables Custom ─── */
    .dataTables_wrapper .top { padding: 1rem 1.5rem 0; margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center; }
    .dataTables_wrapper .dataTables_filter { margin: 0; text-align: right; }
    .dataTables_wrapper .dataTables_filter label { font-weight: 600; color: #475569; display: flex; align-items: center; gap: 8px; }
    .dataTables_wrapper .dataTables_filter input {
        padding: 8px 14px; border: 1.5px solid #cbd5e1; border-radius: 10px;
        font-family: inherit; font-size: 0.9rem; color: #1e293b; background: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01); transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        width: 250px;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #059669; background: #fff; box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.15); outline: none;
    }
    .dataTables_wrapper .bottom { padding: 1rem 1.5rem; display: flex; justify-content: flex-end; }
    .dataTables_wrapper .dataTables_paginate { margin: 0; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { 
        border-radius: 12px !important; border: 1px solid #e2e8f0 !important; 
        background: #fff !important; color: #475569 !important; font-weight: 700 !important; 
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #059669 !important; color: #fff !important; border-color: #059669 !important; }

    /* ─── Fallback Modal Styles from mismifhda ─── */
    .mm-modal {
        display: none; position: fixed; inset: 0; z-index: 9999;
        background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(5px);
        align-items: center; justify-content: center; padding: 20px;
    }
    .mm-modal-content {
        background: white; border-radius: 20px; width: 100%; max-width: 500px;
        max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        animation: modalPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .mm-modal-content.large { max-width: 800px; }
    .mm-modal-content.small { max-width: 400px; }
    @keyframes modalPop { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    
    .mm-modal-header {
        padding: 20px 24px; border-bottom: 1px solid #e2e8f0;
        display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10;
    }
    .mm-modal-header h3 { font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px; }
    .mm-modal-header .close-btn { background: none; border: none; cursor: pointer; color: #64748b; transition: 0.2s; padding: 5px; border-radius: 8px; }
    .mm-modal-header .close-btn:hover { background: #f1f5f9; color: #ef4444; }
    
    .mm-modal-body { padding: 24px; }
    .mm-modal-footer { padding: 20px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 12px; background: #f8fafc; border-radius: 0 0 20px 20px; }
    
    /* ─── Fallback Buttons from mismifhda ─── */
    .z-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 10px 18px; border-radius: 12px; font-weight: 700; font-size: 0.85rem;
        cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: none; text-decoration: none;
    }
    .z-btn-primary { background: #059669; color: white; box-shadow: 0 4px 10px rgba(5,150,105,0.2); }
    .z-btn-primary:hover { background: #047857; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(5,150,105,0.3); }
    .z-btn-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .z-btn-secondary:hover { background: #e2e8f0; color: #0f172a; border-color: #cbd5e1; }
    .z-btn-ghost { background: transparent; color: #64748b; }
    .z-btn-ghost:hover { background: #f1f5f9; color: #0f172a; }
    .z-btn-emerald-light { background: #ecfdf5; color: #059669; }
    .z-btn-emerald-light:hover { background: #d1fae5; color: #047857; }
    .z-btn-danger { background: #fef2f2; color: #e11d48; border: 1px solid #ffe4e6; }
    .z-btn-danger:hover { background: #ffe4e6; color: #be123c; }
    .z-btn.full-width { width: 100%; }
    .z-btn i { width: 18px; height: 18px; }

    /* ─── Components ─── */
    .guru-profile-cell { display: flex; align-items: center; gap: 10px; }
    .guru-avatar {
        width: 36px; height: 36px; border-radius: 10px; overflow: hidden;
        border: 2px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        position: relative; cursor: pointer; transition: 0.3s;
    }
    .guru-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .avatar-hover {
        position: absolute; inset: 0; background: rgba(0,0,0,0.4);
        display: flex; align-items: center; justify-content: center;
        color: white; opacity: 0; transition: 0.3s;
    }
    .guru-avatar:hover .avatar-hover { opacity: 1; }
    .avatar-actions button {
        background: #f1f5f9; border: none; width: 24px; height: 24px;
        border-radius: 6px; cursor: pointer; color: #475569; transition: 0.2s;
    }
    .avatar-actions button:hover { background: #e2e8f0; color: #0f172a; }

    .guru-info-main .name { font-weight: 800; color: #1e293b; font-size: 0.85rem; margin-bottom: 2px; }
    .guru-info-main .nip { font-size: 0.7rem; color: #94a3b8; font-family: 'JetBrains Mono', monospace; }
    
    .guru-contact { display: flex; flex-direction: column; gap: 2px; }
    .contact-item { display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #64748b; }
    .contact-item i { width: 11px; height: 11px; }

    .z-action-btns { display: flex; gap: 6px; justify-content: center; }
    .action-btn {
        width: 28px; height: 28px; border-radius: 8px; display: flex;
        align-items: center; justify-content: center; border: none;
        cursor: pointer; transition: 0.2s;
    }
    .card-btn { background: #ecfdf5; color: #059669; }
    .edit-btn { background: #eff6ff; color: #2563eb; }
    .delete-btn { background: #fff1f2; color: #e11d48; }
    .action-btn:hover { transform: translateY(-2px); filter: brightness(0.95); }

    /* ─── Forms Modernization ─── */
    .form-sections { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem; }
    .form-section {
        background: rgba(248, 250, 252, 0.6); backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8); border-radius: 20px;
        padding: 1.75rem; box-shadow: inset 0 2px 4px rgba(255,255,255,0.6), 0 8px 20px rgba(0,0,0,0.03);
    }
    .form-section h4 {
        display: flex; align-items: center; gap: 10px; font-size: 1.05rem; font-weight: 800;
        color: #0f172a; margin-bottom: 1.5rem; padding-bottom: 12px;
        border-bottom: 2px dashed #e2e8f0;
    }
    .form-section h4 i { width: 20px; height: 20px; color: #059669; padding: 6px; background: #ecfdf5; border-radius: 8px; }

    /* Bulk Action Bar */
    .bulk-bar {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 12px 12px 0 0; padding: 1rem 1.5rem;
        margin-bottom: 0; display: none; align-items: center; justify-content: space-between;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2); animation: slideDown 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        color: white;
    }
    .bulk-bar.active { display: flex; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .bulk-info { display: flex; align-items: center; gap: 8px; color: #34d399; font-weight: 800; font-size: 0.95rem; }
    .count-badge { background: transparent; color: inherit; padding: 0; border-radius: 0; font-weight: inherit; font-size: inherit; margin-right: 0; }
    .bulk-actions { display: flex; gap: 10px; }
    .bulk-actions .btn {
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);
        color: #f8fafc; padding: 6px 14px; border-radius: 8px;
        font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: 0.2s;
        display: flex; align-items: center; gap: 6px;
    }
    .bulk-actions .btn:hover { background: rgba(255,255,255,0.2); transform: translateY(-1px); }
    .bulk-actions .btn.btn-delete { color: #fb7185; border-color: rgba(251, 113, 133, 0.3); background: rgba(251, 113, 133, 0.1); }
    .bulk-actions .btn.btn-delete:hover { background: rgba(251, 113, 133, 0.2); }
    .bulk-actions .btn.btn-print { color: #38bdf8; border-color: rgba(56, 189, 248, 0.3); background: rgba(56, 189, 248, 0.1); }
    .bulk-actions .btn.btn-print:hover { background: rgba(56, 189, 248, 0.2); }
    
    .z-checkbox { width: 18px; height: 18px; accent-color: #10b981; cursor: pointer; }

    /* Modal Styling Adjustments */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem 1.25rem; }
    .form-group.full-width { grid-column: 1 / -1; }
    .form-group label { display: block; font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
    
    /* Modern Input */
    .z-input, .z-textarea {
        width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 12px;
        font-family: inherit; font-size: 0.9rem; color: #1e293b; background: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01); transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-sizing: border-box;
    }
    .z-input:hover, .z-textarea:hover { border-color: #94a3b8; }
    .z-input:focus, .z-textarea:focus { border-color: #059669; background: #fff; box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.15); outline: none; transform: translateY(-1px); }
    .z-textarea { resize: vertical; min-height: 80px; }

    /* Radios */
    .radio-group { display: flex; gap: 24px; padding: 8px 0; }
    .radio-item { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem; font-weight: 600; color: #334155; transition: 0.2s; }
    .radio-item:hover { color: #059669; }
    .radio-item input { width: 18px; height: 18px; accent-color: #059669; cursor: pointer; }
    
    /* Modern File Upload */
    .file-upload-box {
        border: 2px dashed #94a3b8; border-radius: 16px; padding: 2rem 1.5rem;
        text-align: center; color: #64748b; position: relative; cursor: pointer;
        transition: all 0.3s; background: rgba(255,255,255,0.5); display: flex; flex-direction: column; align-items: center; gap: 8px;
    }
    .file-upload-box i { width: 28px; height: 28px; color: #94a3b8; transition: 0.3s; }
    .file-upload-box:hover { border-color: #059669; background: #f0fdf4; color: #059669; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(5,150,105,0.1); }
    .file-upload-box:hover i { color: #059669; transform: scale(1.1); }
    .file-upload-box input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }

    /* ─── Export & Import ─── */
    .export-options { display: grid; gap: 12px; margin-bottom: 1.5rem; }
    .export-option {
        display: flex; align-items: center; gap: 15px; padding: 1.25rem;
        border: 2px solid #f1f5f9; border-radius: 16px; cursor: pointer; transition: 0.2s;
    }
    .export-option:hover { border-color: #059669; }
    .export-option input { width: 20px; height: 20px; accent-color: #059669; }
    .option-title { font-weight: 800; color: #1e293b; font-size: 0.95rem; }
    .option-desc { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }

    .import-layout { display: grid; grid-template-columns: 1fr 240px; gap: 2rem; }
    .import-textarea { height: 300px; font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; }
    .guide-card { background: #f8fafc; border-radius: 16px; padding: 1.25rem; border: 1px solid #e2e8f0; }
    .guide-card h5 { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: #1e293b; }
    .guide-card ol { padding-left: 1.25rem; font-size: 0.8rem; color: #64748b; margin-bottom: 15px; }
    .example-box { background: #fff; padding: 8px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.7rem; color: #94a3b8; font-family: monospace; }

    /* ─── Photo Viewer ─── */
    .photo-viewer .viewer-content { max-width: 90%; max-height: 90%; text-align: center; }
    .photo-viewer img { max-width: 100%; max-height: 80vh; border-radius: 24px; border: 6px solid white; box-shadow: 0 30px 60px rgba(0,0,0,0.5); }
    .viewer-name { color: white; margin-top: 1.5rem; font-size: 1.3rem; font-weight: 800; letter-spacing: 1px; }

    #modal-teacher-card.card-viewer { overflow-y: auto !important; display: none; align-items: flex-start !important; justify-content: center !important; padding: 2rem 1rem !important; }
    #modal-teacher-card .viewer-layout { display: flex; flex-direction: column; gap: 1.5rem; align-items: center; margin: auto; }
    .printable-area { display: flex; flex-direction: column; gap: 1.25rem; align-items: center; }
    .id-card {
        width: 85.6mm; height: 54mm; background: white; border-radius: 4mm;
        overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        position: relative; display: flex; flex-direction: column; border: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
    .id-card.front .card-header {
        padding: 8px 15px; display: flex; align-items: center; gap: 12px;
        background: linear-gradient(135deg, #064e3b 0%, #10b981 100%); color: white;
    }
    .inst-logo { height: 32px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); }
    .inst-info .card-label { font-size: 7px; font-weight: 600; opacity: 0.8; letter-spacing: 1px; }
    .inst-info .inst-name { display: block; font-size: 12px; font-weight: 800; }
    .card-body { flex: 1; padding: 12px 18px; display: flex; gap: 15px; }
    .photo-box { width: 24mm; height: 32mm; border-radius: 6px; overflow: hidden; background: #f1f5f9; border: 2px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); flex-shrink: 0; }
    .photo-box img { width: 100%; height: 100%; object-fit: cover; }
    .info-table { font-size: 8.5px; border-collapse: collapse; line-height: 1.4; flex: 1; }
    .info-table td:first-child { font-weight: 700; color: #64748b; width: 14mm; }
    .info-table td.bold { font-weight: 800; color: #0f172a; }
    .signature-box { display: flex; align-items: center; gap: 8px; margin-top: auto; }
    .qr-ttd { width: 13mm; height: 13mm; border: 1px solid #e2e8f0; border-radius: 2px; }
    .sig-text { font-size: 6px; color: #475569; display: flex; flex-direction: column; }
    .sig-text .name { font-weight: 800; color: #0f172a; border-bottom: 0.5px solid #0f172a; margin-top: 4px; padding-bottom: 1px; }
    .card-footer-bar { height: 2.5mm; background: linear-gradient(90deg, #064e3b, #10b981); }

    .id-card.back { padding: 5mm; align-items: center; justify-content: center; }
    .back-header { text-align: center; margin-bottom: 4mm; }
    .back-header .title { display: block; font-weight: 800; font-size: 11px; color: #1e293b; }
    .back-header .subtitle { font-size: 7px; color: #94a3b8; }
    .qr-box { background: white; padding: 2mm; border-radius: 3mm; border: 1px solid #e2e8f0; margin-bottom: 4mm; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .qr-box img { width: 25mm; height: 25mm; }
    .creds-box { width: 100%; display: flex; justify-content: space-between; border-top: 1px dashed #cbd5e1; padding-top: 4mm; }
    .cred .label { display: block; font-size: 6px; font-weight: 700; color: #94a3b8; margin-bottom: 2px; }
    .cred .value { font-weight: 800; font-size: 10px; color: #0f172a; font-family: monospace; }

    @media print {
        body * { visibility: hidden; }
        #modal-teacher-card, #modal-teacher-card * { visibility: visible; }
        #modal-teacher-card { position: absolute; left: 0; top: 0; background: white !important; display: block !important; padding: 0 !important; }
        .printable-area { display: block !important; }
        .id-card { box-shadow: none !important; margin-bottom: 10mm; page-break-inside: avoid; border: 0.5px solid #ddd !important; }
        .viewer-actions { display: none !important; }
        -webkit-print-color-adjust: exact;
    }

    /* ─── DataTables Custom ─── */
    .dataTables_wrapper .top { padding: 1rem 1.5rem 0; margin-bottom: 0.5rem; }
    .dataTables_wrapper .bottom { padding: 1rem 1.5rem; display: flex; justify-content: flex-end; }
    .dataTables_wrapper .dataTables_paginate { margin: 0; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { 
        border-radius: 12px !important; border: 1px solid #e2e8f0 !important; 
        background: #fff !important; color: #475569 !important; font-weight: 700 !important; 
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #059669 !important; color: #fff !important; border-color: #059669 !important; }

    /* ─── Modal Tabs ─── */
    .z-tabs { display: flex; border-bottom: 2px solid #e2e8f0; margin-bottom: 1.5rem; gap: 1rem; }
    .z-tab-btn { display: flex; align-items: center; gap: 6px; padding: 10px 16px; font-weight: 700; color: #64748b; background: none; border: none; border-bottom: 3px solid transparent; cursor: pointer; transition: 0.3s; margin-bottom: -2px; font-size: 0.9rem; }
    .z-tab-btn i { width: 16px; height: 16px; }
    .z-tab-btn:hover { color: #059669; }
    .z-tab-btn.active { color: #059669; border-bottom-color: #059669; }
    .z-tab-content { display: none; }
    .z-tab-content.active { display: block; animation: fadeInTab 0.3s ease; }
    @keyframes fadeInTab { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>



        <div class="modern-page-header">
            <div>
                <h1 class="mph-title">
                    <i data-lucide="users" style="color: #bfdbfe;"></i> Manajemen Data Guru
                </h1>
                <p class="mph-subtitle">Kelola profil, jabatan, dan cetak kartu identitas guru & tendik.</p>
            </div>
            <div class="mph-actions" style="flex-wrap: wrap; justify-content: flex-end;">
                <button class="btn" onclick="document.getElementById('modal-export-guru').style.display='flex'">
                    <i data-lucide="printer"></i> Cetak / Export
                </button>
                <button class="btn" onclick="document.getElementById('modal-import-guru').style.display='flex'">
                    <i data-lucide="file-up"></i> Smart-Paste
                </button>
                <a href="<?php echo \App\Core\Helper::url('/siakad/guru/cetak-kartu-massal?mode=horizontal'); ?>" target="_blank" class="btn" style="text-decoration:none;">
                    <i data-lucide="id-card"></i> Kartu (H)
                </a>
                <a href="<?php echo \App\Core\Helper::url('/siakad/guru/cetak-kartu-massal?mode=vertical'); ?>" target="_blank" class="btn" style="text-decoration:none;">
                    <i data-lucide="id-card"></i> Kartu (V)
                </a>
                <button class="btn btn-primary-white" onclick="openAddGuruModal()">
                    <i data-lucide="user-plus"></i> Tambah Guru
                </button>
            </div>
        </div>

        <?php if (isset($_SESSION['flash_success'])): ?>
            <div style="background: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 700;"><i data-lucide="check-circle" style="width:18px; vertical-align:middle; margin-right:8px; color:#10b981;"></i> <?php echo $_SESSION['flash_success']; ?></div>
                <button onclick="this.parentElement.style.display='none'" style="background:none; border:none; color:#065f46; cursor:pointer;"><i data-lucide="x"></i></button>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div style="background: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="font-weight: 700;"><i data-lucide="alert-circle" style="width:18px; vertical-align:middle; margin-right:8px; color:#ef4444;"></i> <?php echo $_SESSION['flash_error']; ?></div>
                <button onclick="this.parentElement.style.display='none'" style="background:none; border:none; color:#991b1b; cursor:pointer;"><i data-lucide="x"></i></button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <div class="z-panel">
            <form id="form-bulk-guru" action="<?php echo \App\Core\Helper::url('/siakad/guru/delete-bulk'); ?>" method="POST">
                <!-- Bulk Action Bar -->
                <div id="bulk-action-bar-guru" class="bulk-bar">
                    <div class="bulk-info">
                        <i data-lucide="check-square"></i> <span class="count-badge" id="selected-count-guru">0</span> Guru terpilih
                    </div>
                    <div class="bulk-actions">
                        <button type="button" onclick="confirmBulkActionGuru('print_h')" class="btn btn-print">
                            <i data-lucide="printer"></i> Cetak (H)
                        </button>
                        <button type="button" onclick="confirmBulkActionGuru('print_v')" class="btn btn-print">
                            <i data-lucide="printer"></i> Cetak (V)
                        </button>
                        <button type="button" onclick="confirmBulkActionGuru('delete')" class="btn btn-delete">
                            <i data-lucide="trash-2"></i> Hapus
                        </button>
                    </div>
                </div>

                <div class="z-table-wrap">
                    <table id="table-data-guru" class="">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 40px;">
                                    <input type="checkbox" id="check-all-guru" class="z-checkbox">
                                </th>
                                <th style="width: 70px;">Profil</th>
                                <th style="width: 150px;">NIK / NIY</th>
                                <th>Nama Lengkap</th>
                                <th>Jabatan</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($guruList as $g): ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="<?php echo $g['id']; ?>" class="check-guru z-checkbox" onchange="updateBulkGuru()">
                                </td>
                                <td>
                                    <div class="guru-profile-cell">
                                        <div class="guru-avatar" onclick="viewPhoto('<?php echo $g['foto']; ?>', '<?php echo addslashes($g['nama']); ?>')">
                                            <?php if($g['foto'] && file_exists(__DIR__ . "/../../../public/uploads/guru/" . $g['foto'])): ?>
                                                <?php $thumbName = pathinfo($g['foto'], PATHINFO_FILENAME) . '_thumb.' . pathinfo($g['foto'], PATHINFO_EXTENSION); ?>
                                                <img id="img-guru-<?php echo $g['id']; ?>" src="<?php echo \App\Core\Helper::url('/public/uploads/guru/' . $thumbName); ?>" onerror="this.onerror=null; this.src='<?php echo \App\Core\Helper::url('/public/uploads/guru/' . $g['foto']); ?>';" alt="Foto">
                                            <?php else: ?>
                                                <img id="img-guru-<?php echo $g['id']; ?>" src="https://ui-avatars.com/api/?name=<?php echo urlencode($g['nama']); ?>&background=059669&color=fff&bold=true" alt="Avatar">
                                            <?php endif; ?>
                                            <div class="avatar-hover">
                                                <i data-lucide="maximize-2"></i>
                                            </div>
                                        </div>
                                        <div class="avatar-actions">
                                            <button type="button" onclick="quickUpload('<?php echo $g['id']; ?>', '<?php echo addslashes($g['nama']); ?>')" title="Ganti Foto">
                                                <i data-lucide="camera"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="guru-info-main" style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: #475569; font-weight: 600;">
                                        NIK: <?php echo $g['nik'] ?: '-'; ?><br>
                                        <span style="font-size: 0.75rem; color: #94a3b8;">NIY: <?php echo !empty($g['niy']) ? $g['niy'] : '-'; ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="guru-info-main">
                                        <div class="name"><?php echo $g['nama']; ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="z-badge-outline"><?php echo $g['jabatan'] ?: 'Guru'; ?></span>
                                </td>
                                <td>
                                    <div class="z-action-btns">
                                        <button type="button" class="action-btn card-btn" data-id="<?php echo $g['id']; ?>" title="Lihat Kartu">
                                            <i data-lucide="contact"></i>
                                        </button>
                                        <a href="<?php echo \App\Core\Helper::url('/siakad/guru/edit/' . $g['id']); ?>" class="action-btn edit-btn" title="Edit Data" style="text-decoration:none; display:inline-flex;">
                                            <i data-lucide="edit-3"></i>
                                        </a>
                                        <button type="button" onclick="showAppModal({ title: 'Konfirmasi Hapus', message: 'Hapus guru ini secara permanen? Seluruh riwayat tugas, jadwal, jurnal, dan absennya juga akan terhapus bersih!', type: 'warning', buttons: [ { text: 'Batal', class: 'btn btn-secondary' }, { text: '<i data-lucide=\'trash-2\'></i> Ya, Hapus', class: 'btn btn-danger', href: '<?php echo \App\Core\Helper::url('/siakad/guru/delete/' . $g['id']); ?>' } ] });" class="action-btn delete-btn" title="Hapus">
                                            <i data-lucide="trash-2"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
<!-- Modal Add/Edit Guru -->
<div id="modal-add-guru" class="mm-modal">
    <div class="mm-modal-content large">
        <div class="mm-modal-header">
            <h3 id="modal-title-guru"><i data-lucide="user-plus"></i> Tambah Guru & Tendik</h3>
            <button onclick="document.getElementById('modal-add-guru').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="mm-modal-body">
            <form id="form-guru" action="<?php echo \App\Core\Helper::url('/siakad/guru/save'); ?>" method="POST" enctype="multipart/form-data">
                <div class="z-tabs">
                    <button type="button" class="z-tab-btn active" onclick="switchGuruTab(this, 'tab-guru-utama')"><i data-lucide="user"></i> Data Utama</button>
                    <button type="button" class="z-tab-btn" onclick="switchGuruTab(this, 'tab-guru-tambahan')"><i data-lucide="file-text"></i> Data Tambahan</button>
                </div>

                <div id="tab-guru-utama" class="z-tab-content active">
                    <div class="form-sections">
                    <div class="form-section">
                        <h4><i data-lucide="info"></i> Identitas Utama</h4>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label>Nama Lengkap & Gelar</label>
                                <input type="text" name="nama" required class="z-input" placeholder="Contoh: Ahmad Rifa'i, M.Pd">
                            </div>
                            <div class="form-group">
                                <label>NIK KTP (Wajib)</label>
                                <input type="text" name="nik" required class="z-input" placeholder="Masukkan 16 digit NIK">
                            </div>
                            <div class="form-group">
                                <label>NIP Guru (Opsional)</label>
                                <input type="text" name="nip" class="z-input" placeholder="Kosongkan jika tidak ada">
                            </div>
                            <div class="form-group full-width">
                                <label>NIY / Nomor Induk Yayasan (Opsional)</label>
                                <input type="text" name="niy" class="z-input" placeholder="Nomor Induk Yayasan">
                            </div>

                            <div class="form-group">
                                <label>Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="z-input" placeholder="Kota lahir">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="z-input">
                            </div>
                            <div class="form-group full-width">
                                <label>Jenis Kelamin</label>
                                <div class="radio-group">
                                    <label class="radio-item">
                                        <input type="radio" name="jenis_kelamin" value="L" checked>
                                        <span>Laki-laki</span>
                                    </label>
                                    <label class="radio-item">
                                        <input type="radio" name="jenis_kelamin" value="P">
                                        <span>Perempuan</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group full-width">
                                <label>Foto Profil</label>
                                <div class="file-upload-box">
                                    <i data-lucide="image"></i>
                                    <span>Klik untuk pilih foto</span>
                                    <input type="file" name="foto" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4><i data-lucide="briefcase"></i> Kontak & Jabatan</h4>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label>Jabatan / Peran</label>
                                <input type="text" name="jabatan" placeholder="Contoh: Guru Matematika / Wali Kelas" class="z-input">
                            </div>
                            <div class="form-group full-width">
                                <label style="display:flex; align-items:center; gap:12px; cursor:pointer; padding:12px 15px; background: linear-gradient(to right, #f0fdf4, #fff); border:1.5px solid #86efac; border-radius:12px;">
                                    <input type="checkbox" name="is_kamad" value="1" style="width:22px; height:22px; accent-color:#059669;">
                                    <div style="text-transform:none;">
                                        <div style="font-size:0.95rem; font-weight:800; color:#064e3b; margin-bottom:2px;"><i data-lucide="shield-check" style="width:16px;height:16px;display:inline-block;vertical-align:-3px;"></i> Akses Kepala Madrasah (Administrator)</div>
                                        <div style="font-size:0.75rem; color:#166534; font-weight:500; letter-spacing:0;">Centang ini untuk memberikan hak akses portal Admin penuh.</div>
                                    </div>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Nomor HP / WhatsApp</label>
                                <input type="text" name="no_hp" class="z-input" placeholder="08xxxxxx">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="z-input" placeholder="guru@sekolah.id">
                            </div>
                            <div class="form-group">
                                <label>Provinsi</label>
                                <input type="hidden" name="provinsi" id="guru_val_prov">
                                <select id="guru_sel_prov" class="z-input">
                                    <option value="">-- Pilih Provinsi --</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Kab/Kota</label>
                                <input type="hidden" name="kota" id="guru_val_kota">
                                <select id="guru_sel_kota" class="z-input" disabled>
                                    <option value="">-- Pilih Kab/Kota --</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Kecamatan</label>
                                <input type="hidden" name="kecamatan" id="guru_val_kec">
                                <select id="guru_sel_kec" class="z-input" disabled>
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Kelurahan/Desa</label>
                                <input type="hidden" name="desa" id="guru_val_desa">
                                <select id="guru_sel_desa" class="z-input" disabled>
                                    <option value="">-- Pilih Kelurahan --</option>
                                </select>
                            </div>
                            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; align-items: start;">
                                <div><label>RT</label><input type="text" name="rt" class="z-input"></div>
                                <div><label>RW</label><input type="text" name="rw" class="z-input"></div>
                            </div>
                            <div class="form-group full-width">
                                <label>Nama Jalan / Dusun</label>
                                <textarea name="alamat" class="z-textarea" placeholder="Cth: Jl. Sudirman No. 12"></textarea>
                            </div>
                            <div class="form-group full-width">
                                <label>Status Kepegawaian</label>
                                <select name="status_kepegawaian" class="z-input">
                                    <option value="Honorer">Honorer</option>
                                    <option value="Sertifikasi">Sertifikasi</option>
                                    <option value="PNS">PNS</option>
                                    <option value="P3K">P3K</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <div id="tab-guru-tambahan" class="z-tab-content">
                <div class="form-sections">
                    <div class="form-section">
                        <h4><i data-lucide="file-text"></i> Kepegawaian & Rekening</h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>NPK</label>
                                <input type="text" name="npk" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>NPWP</label>
                                <input type="text" name="npwp" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>No Rekening (BSI)</label>
                                <input type="text" name="no_rekening" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>Kode Pos</label>
                                <input type="text" name="kode_pos" class="z-input">
                            </div>
                        </div>
                    </div>
                    <div class="form-section">
                        <h4><i data-lucide="award"></i> Data Sertifikasi</h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>NRG</label>
                                <input type="text" name="nrg" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>No Peserta Sertifikasi</label>
                                <input type="text" name="no_peserta_sertifikasi" class="z-input">
                            </div>
                            <div class="form-group full-width">
                                <label>No Sertifikat</label>
                                <input type="text" name="no_sertifikat" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>Tgl Sertifikat</label>
                                <input type="date" name="tgl_sertifikat" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>Jenjang</label>
                                <select name="jenjang_sertifikat" class="z-input">
                                    <option value="">- Pilih -</option>
                                    <option value="RA">RA</option>
                                    <option value="MI">MI</option>
                                    <option value="MTs">MTs</option>
                                    <option value="MA">MA</option>
                                </select>
                            </div>
                            <div class="form-group full-width">
                                <label>Mapel Sertifikat</label>
                                <input type="text" name="mapel_sertifikat" class="z-input">
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="mm-modal-footer">
                    <button type="button" class="z-btn z-btn-ghost" onclick="document.getElementById('modal-add-guru').style.display='none'">Batal</button>
                    <button type="submit" class="z-btn z-btn-primary">Simpan & Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Export Guru -->
<div id="modal-export-guru" class="mm-modal">
    <div class="mm-modal-content small">
        <div class="mm-modal-header">
            <h3><i data-lucide="printer"></i> Export Data Guru</h3>
            <button onclick="document.getElementById('modal-export-guru').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="mm-modal-body">
            <form action="<?php echo \App\Core\Helper::url('/siakad/guru/export'); ?>" method="GET" target="_blank">
                <div class="export-options">
                    <label class="export-option">
                        <input type="radio" name="mode" value="public" checked>
                        <div class="option-content">
                            <div class="option-title">Data Publik (Umum)</div>
                            <div class="option-desc">NIP, Nama, Jabatan, dan Pendidikan.</div>
                        </div>
                    </label>
                    <label class="export-option">
                        <input type="radio" name="mode" value="full">
                        <div class="option-content">
                            <div class="option-title">Data Lengkap (Internal)</div>
                            <div class="option-desc">Seluruh biodata, NIK, No HP, dan Alamat.</div>
                        </div>
                    </label>
                </div>
                <div class="mm-modal-footer">
                    <button type="submit" class="z-btn z-btn-primary full-width">Buka Preview Cetak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Guru -->
<div id="modal-import-guru" class="mm-modal">
    <div class="mm-modal-content large">
        <div class="mm-modal-header">
            <h3><i data-lucide="file-up"></i> Import Smart-Paste</h3>
            <button onclick="document.getElementById('modal-import-guru').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="mm-modal-body">
            <form action="<?php echo \App\Core\Helper::url('/siakad/guru/import'); ?>" method="POST">
                <div class="import-layout" style="display: flex; flex-direction: column; gap: 1rem;">
                    <div class="guide-card" style="width: 100%; overflow-x: auto; white-space: nowrap; padding-bottom: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <h5 style="margin: 0;"><i data-lucide="help-circle"></i> Format Kolom Excel (Wajib Urut - 27 Kolom)</h5>
                            <a href="<?php echo \App\Core\Helper::url('/siakad/guru/template-import'); ?>" class="z-btn z-btn-ghost" style="padding: 5px 15px; font-size: 0.8rem; background: #e2e8f0; color: #0f172a; text-decoration: none;"><i data-lucide="download"></i> Download Template Excel</a>
                        </div>
                        <p style="margin-bottom: 10px; font-size: 0.8rem; color: #64748b;">Copy seluruh baris di Excel (termasuk kolom yang kosong) lalu paste di area bawah. Urutan kolom <b>harus</b> sesuai tabel ini:</p>
                        <table border="1" style="width: max-content; border-collapse: collapse; font-size: 0.75rem; text-align: center; border-color: #cbd5e1;">
                            <tr style="background: #f1f5f9; color: #0f172a; font-weight: bold;">
                                <td style="padding: 5px 10px;">1. NIK</td>
                                <td style="padding: 5px 10px;">2. NIP</td>
                                <td style="padding: 5px 10px;">3. Nama Lengkap</td>
                                <td style="padding: 5px 10px;">4. L/P</td>
                                <td style="padding: 5px 10px;">5. Tempat Lahir</td>
                                <td style="padding: 5px 10px;">6. Tgl Lahir (YYYY-MM-DD)</td>
                                <td style="padding: 5px 10px;">7. No HP</td>
                                <td style="padding: 5px 10px;">8. Email</td>
                                <td style="padding: 5px 10px;">9. Jabatan</td>
                                <td style="padding: 5px 10px;">10. Status Pegawai</td>
                                <td style="padding: 5px 10px;">11. NPK</td>
                                <td style="padding: 5px 10px;">12. NPWP</td>
                                <td style="padding: 5px 10px;">13. No Rekening</td>
                                <td style="padding: 5px 10px;">14. NRG</td>
                                <td style="padding: 5px 10px;">15. Alamat Jalan</td>
                                <td style="padding: 5px 10px;">16. RT</td>
                                <td style="padding: 5px 10px;">17. RW</td>
                                <td style="padding: 5px 10px;">18. Provinsi</td>
                                <td style="padding: 5px 10px;">19. Kab/Kota</td>
                                <td style="padding: 5px 10px;">20. Kecamatan</td>
                                <td style="padding: 5px 10px;">21. Desa</td>
                                <td style="padding: 5px 10px;">22. Kode Pos</td>
                                <td style="padding: 5px 10px;">23. No Peserta Serti.</td>
                                <td style="padding: 5px 10px;">24. No Sertifikat</td>
                                <td style="padding: 5px 10px;">25. Tgl Sertifikat</td>
                                <td style="padding: 5px 10px;">26. Jenjang</td>
                                <td style="padding: 5px 10px;">27. Mapel Sertifikasi</td>
                            </tr>
                            <tr style="color: #64748b;">
                                <td style="padding: 5px 10px;">12345...</td>
                                <td style="padding: 5px 10px;">1980...</td>
                                <td style="padding: 5px 10px;">Budi Santoso</td>
                                <td style="padding: 5px 10px;">L</td>
                                <td style="padding: 5px 10px;">Jakarta</td>
                                <td style="padding: 5px 10px;">1985-12-30</td>
                                <td style="padding: 5px 10px;">081234</td>
                                <td style="padding: 5px 10px;">budi@</td>
                                <td style="padding: 5px 10px;">Guru MTK</td>
                                <td style="padding: 5px 10px;">PNS</td>
                                <td colspan="17" style="padding: 5px 10px; color: #cbd5e1; font-style: italic;">... (Kosongkan sel jika tidak ada data) ...</td>
                            </tr>
                        </table>
                    </div>
                    <div class="import-main">
                        <label class="z-label">Paste Data Excel di Sini</label>
                        <textarea name="paste_data" class="z-textarea import-textarea" style="width: 100%; height: 200px;" placeholder="Paste data Anda dari Excel ke area ini..."></textarea>
                    </div>
                </div>
                <div class="mm-modal-footer">
                    <button type="submit" class="z-btn z-btn-primary">Proses Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Print Options -->
<div id="modal-print-options" class="mm-modal">
    <div class="mm-modal-content small">
        <div class="mm-modal-header">
            <h3><i data-lucide="printer"></i> Format Cetak Kartu</h3>
            <button onclick="document.getElementById('modal-print-options').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="mm-modal-body text-center" style="padding: 30px 20px;">
            <p style="margin-bottom: 25px; color: #64748b; font-size: 0.95rem;">Pilih orientasi cetak untuk kartu identitas.</p>
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button type="button" onclick="executePrintTeacherCard('horizontal')" style="flex: 1; padding: 20px; border-radius: 12px; border: 2px solid #e2e8f0; background: white; cursor: pointer; transition: 0.2s;" onmouseover="this.style.borderColor='#059669'; this.style.color='#059669'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#0f172a'">
                    <i data-lucide="layout-template" style="width: 32px; height: 32px; margin-bottom: 10px;"></i>
                    <div style="font-weight: 700;">Horizontal</div>
                </button>
                <button type="button" onclick="executePrintTeacherCard('vertical')" style="flex: 1; padding: 20px; border-radius: 12px; border: 2px solid #e2e8f0; background: white; cursor: pointer; transition: 0.2s;" onmouseover="this.style.borderColor='#059669'; this.style.color='#059669'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#0f172a'">
                    <i data-lucide="smartphone" style="width: 32px; height: 32px; margin-bottom: 10px;"></i>
                    <div style="font-weight: 700;">Vertikal</div>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lihat Foto -->
<div id="modal-view-photo" class="mm-modal photo-viewer" onclick="this.style.display='none'">
    <div class="viewer-content">
        <img id="view-photo-img" src="">
        <div id="view-photo-name" class="viewer-name"></div>
    </div>
</div>

<!-- Teacher Card Modal -->
<div id="modal-teacher-card" class="z-modal card-viewer">
    <div class="viewer-layout">
        <div id="printable-teacher-card" class="printable-area">
            <!-- Front Card -->
            <div class="id-card front">
                <div class="card-header">
                    <?php if($inst && $inst['logo']): ?>
                        <img src="<?php echo \App\Core\Helper::url('/public/uploads/logo/' . $inst['logo']); ?>" class="inst-logo">
                    <?php endif; ?>
                    <div class="inst-info">
                        <span class="card-label">KARTU IDENTITAS GURU</span>
                        <span class="inst-name"><?php echo $inst ? $inst['nama'] : 'GH-SSS ACADEMY'; ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="photo-box">
                        <img id="tc-photo" src="">
                    </div>
                    <div class="info-box">
                        <table class="info-table">
                            <tr><td>Nama</td><td>:</td><td id="tc-name" class="bold"></td></tr>
                            <tr><td>Jabatan</td><td>:</td><td id="tc-jabatan"></td></tr>
                            <tr><td>Tmpt Lahir</td><td>:</td><td id="tc-tempat-lahir"></td></tr>
                            <tr><td>Tgl Lahir</td><td>:</td><td id="tc-tanggal-lahir"></td></tr>
                            <tr><td>Alamat</td><td>:</td><td id="tc-alamat" class="truncate"></td></tr>
                        </table>
                        <div class="signature-box">
                            <img id="tc-ttd-qr" src="" class="qr-ttd">
                            <div class="sig-text">
                                <span class="date"><?php echo isset($inst['kota']) ? $inst['kota'] : 'Jakarta'; ?>, <?php echo date('d M Y'); ?></span>
                                <span class="title">Kepala Madrasah</span>
                                <span class="name"><?php echo isset($inst['nama_kepala']) ? $inst['nama_kepala'] : 'Kepala Sekolah'; ?></span>
                                <span class="nip">NIP. <?php echo isset($inst['nip_kepala']) ? $inst['nip_kepala'] : '-'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer-bar"></div>
            </div>

            <!-- Back Card -->
            <div class="id-card back">
                <div class="back-content">
                    <div class="back-header">
                        <span class="title">AKUN AKSES DIGITAL</span>
                        <span class="subtitle">Gunakan QR Code ini untuk verifikasi & login</span>
                    </div>
                    <div class="qr-box">
                        <img id="tc-qr" src="">
                    </div>
                </div>
            </div>
        </div>

        <div class="viewer-actions">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal-teacher-card').style.display='none'">Tutup</button>
            <button type="button" class="btn btn-primary" onclick="window.print()"><i data-lucide="printer"></i> Cetak Kartu</button>
        </div>
    </div>
</div>

<input type="file" id="quick-auto-input-guru" accept="image/*" hidden onchange="handleAutoUpload(this)">
<input type="hidden" id="quick-auto-guru-id">

<!-- JS Dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openAddGuruModal() {
        const modal = document.getElementById('modal-add-guru');
        document.getElementById('modal-title-guru').innerHTML = '<i data-lucide="user-plus"></i> Tambah Guru & Tendik';
        const form = document.getElementById('form-guru');
        form.action = '<?php echo \App\Core\Helper::url('/siakad/guru/save'); ?>';
        form.reset();
        form.querySelector('[name="nip"]').readOnly = false;
        if(form.querySelector('[name="username"]')) form.querySelector('[name="username"]').value = '';
        if(form.querySelector('[name="password"]')) form.querySelector('[name="password"]').value = '';
        const hid = form.querySelector('[name="id"]');
        if (hid) hid.remove();
        
        // Reset tabs
        if(document.querySelector('.z-tabs .z-tab-btn')) {
            document.querySelector('.z-tabs .z-tab-btn').click();
        }

        modal.style.display = 'flex';
        lucide.createIcons();
    }

    function switchGuruTab(btn, tabId) {
        const tabs = btn.closest('.z-tabs').querySelectorAll('.z-tab-btn');
        tabs.forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        
        const contents = btn.closest('form').querySelectorAll('.z-tab-content');
        contents.forEach(c => c.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
    }

    function viewPhoto(foto, nama, isUrl = false) {
        const img = document.getElementById('view-photo-img');
        const modal = document.getElementById('modal-view-photo');
        if(isUrl) img.src = foto;
        else img.src = foto ? '<?php echo \App\Core\Helper::url("/public/uploads/guru/"); ?>' + foto : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(nama) + '&background=059669&color=fff&bold=true';
        document.getElementById('view-photo-name').innerText = nama;
        modal.style.display = 'flex';
        lucide.createIcons();
    }

    function quickUpload(id, nama) {
        document.getElementById('quick-auto-guru-id').value = id;
        document.getElementById('quick-auto-input-guru').click();
    }

    async function handleAutoUpload(input) {
        if (!input.files || !input.files[0]) return;
        const id = document.getElementById('quick-auto-guru-id').value;
        const tableImg = document.getElementById('img-guru-' + id);
        tableImg.style.opacity = '0.5';
        const formData = new FormData();
        formData.append('id', id);
        formData.append('foto', input.files[0]);
        try {
            const response = await fetch('<?php echo \App\Core\Helper::url('/siakad/guru/update-foto'); ?>', { method: 'POST', body: formData });
            const res = await response.json();
            if(res.success) {
                const imgName = res.thumb ? res.thumb : res.foto;
                tableImg.src = '/uploads/guru/' + imgName + '?t=' + Date.now();
                ZNS.showSuccess('Foto profil berhasil diperbarui');
            } else ZNS.showError('Gagal: ' + res.message);
        } catch (error) { ZNS.showError('Kesalahan koneksi.'); }
        finally { tableImg.style.opacity = '1'; input.value = ''; }
    }

    let currentTeacherPrintId = null;
    function showTeacherCard(data) {
        currentTeacherPrintId = data.id;
        document.getElementById('modal-print-options').style.display = 'flex';
        lucide.createIcons();
    }

    function executePrintTeacherCard(mode) {
        document.getElementById('modal-print-options').style.display = 'none';
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo \App\Core\Helper::url('/siakad/guru/cetak-kartu-bulk?mode='); ?>' + mode;
        form.target = '_blank';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = currentTeacherPrintId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
        form.remove();
    }



    function updateBulkGuru() {
        const count = document.querySelectorAll('.check-guru:checked').length;
        document.getElementById('selected-count-guru').innerText = count;
        document.getElementById('bulk-action-bar-guru').style.display = count > 0 ? 'flex' : 'none';
    }

    document.getElementById('check-all-guru')?.addEventListener('change', function() {
        document.querySelectorAll('.check-guru').forEach(cb => cb.checked = this.checked);
        updateBulkGuru();
    });

    const ZNS = {
        confirm: function(msg, onConfirm) {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: msg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#e11d48',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'z-swal-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    onConfirm();
                }
            });
        },
        showSuccess: function(msg) {
            Swal.fire({ title: 'Berhasil!', text: msg, icon: 'success', confirmButtonColor: '#059669' });
        },
        showError: function(msg) {
            Swal.fire({ title: 'Oops...', text: msg, icon: 'error', confirmButtonColor: '#e11d48' });
        }
    };

    function confirmBulkActionGuru(action) {
        const form = document.getElementById('form-bulk-guru');
        if(action === 'delete') {
            showAppModal({
                title: 'Konfirmasi Hapus Massal',
                message: 'Apakah Anda yakin ingin menghapus <b>semua guru terpilih</b> secara permanen? Data yang dihapus tidak dapat dikembalikan!',
                type: 'warning',
                buttons: [
                    { text: 'Batal', class: 'btn btn-secondary' },
                    { text: '<i data-lucide="trash-2"></i> Ya, Hapus Semua', class: 'btn btn-danger', onClick: () => {
                        form.action = '<?php echo \App\Core\Helper::url("/siakad/guru/delete-bulk"); ?>';
                        form.target = '_self';
                        form.submit();
                    }}
                ]
            });
        } else if(action === 'print_v') {
            form.action = '<?php echo \App\Core\Helper::url('/siakad/guru/cetak-kartu-bulk?mode=vertical'); ?>';
            form.target = '_blank';
            form.submit();
        } else {
            form.action = '<?php echo \App\Core\Helper::url('/siakad/guru/cetak-kartu-bulk?mode=horizontal'); ?>';
            form.target = '_blank';
            form.submit();
        }
    }

    const allGuruData = <?php echo json_encode($guruList, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;

    document.addEventListener("DOMContentLoaded", function() {
        document.body.addEventListener('click', function(e) {
            const cardBtn = e.target.closest('.card-btn');
            if (cardBtn) {
                try {
                    let id = cardBtn.getAttribute('data-id');
                    let data = allGuruData.find(g => g.id == id);
                    if(data) showTeacherCard(data);
                } catch(err) {
                    alert("Error Cetak: " + err.message);
                }
            }
        });

        const selProv = document.getElementById('guru_sel_prov');
        const selKota = document.getElementById('guru_sel_kota');
        const selKec = document.getElementById('guru_sel_kec');
        const selDesa = document.getElementById('guru_sel_desa');
        
        const valProv = document.getElementById('guru_val_prov');
        const valKota = document.getElementById('guru_val_kota');
        const valKec = document.getElementById('guru_val_kec');
        const valDesa = document.getElementById('guru_val_desa');

        // Fetch Provinces
        if(selProv) {
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json`)
                .then(response => response.json())
                .then(provinces => {
                    let options = '<option value="">-- Pilih Provinsi --</option>';
                    provinces.forEach(p => {
                        options += `<option value="${p.id}" data-name="${p.name}">${p.name}</option>`;
                    });
                    selProv.innerHTML = options;
                }).catch(err => console.log('Error fetching provinces:', err));
                
            selProv.addEventListener('change', function() {
                selKota.innerHTML = '<option value="">-- Pilih Kab/Kota --</option>';
                selKec.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                selDesa.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
                selKota.disabled = true;
                selKec.disabled = true;
                selDesa.disabled = true;
                
                let selected = this.options[this.selectedIndex];
                valProv.value = selected.getAttribute('data-name') || '';

                if(this.value) {
                    selKota.disabled = false;
                    fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${this.value}.json`)
                        .then(response => response.json())
                        .then(regencies => {
                            let options = '<option value="">-- Pilih Kab/Kota --</option>';
                            regencies.forEach(r => {
                                options += `<option value="${r.id}" data-name="${r.name}">${r.name}</option>`;
                            });
                            selKota.innerHTML = options;
                        });
                } else {
                    valKota.value = ''; valKec.value = ''; valDesa.value = '';
                }
            });

            selKota.addEventListener('change', function() {
                selKec.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                selDesa.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
                selKec.disabled = true;
                selDesa.disabled = true;

                let selected = this.options[this.selectedIndex];
                valKota.value = selected.getAttribute('data-name') || '';

                if(this.value) {
                    selKec.disabled = false;
                    fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${this.value}.json`)
                        .then(response => response.json())
                        .then(districts => {
                            let options = '<option value="">-- Pilih Kecamatan --</option>';
                            districts.forEach(d => {
                                options += `<option value="${d.id}" data-name="${d.name}">${d.name}</option>`;
                            });
                            selKec.innerHTML = options;
                        });
                } else {
                    valKec.value = ''; valDesa.value = '';
                }
            });

            selKec.addEventListener('change', function() {
                selDesa.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
                selDesa.disabled = true;

                let selected = this.options[this.selectedIndex];
                valKec.value = selected.getAttribute('data-name') || '';

                if(this.value) {
                    selDesa.disabled = false;
                    fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${this.value}.json`)
                        .then(response => response.json())
                        .then(villages => {
                            let options = '<option value="">-- Pilih Kelurahan --</option>';
                            villages.forEach(v => {
                                options += `<option value="${v.id}" data-name="${v.name}">${v.name}</option>`;
                            });
                            selDesa.innerHTML = options;
                        });
                } else {
                    valDesa.value = '';
                }
            });

            selDesa.addEventListener('change', function() {
                let selected = this.options[this.selectedIndex];
                valDesa.value = selected.getAttribute('data-name') || '';
            });
        }
    });

    $(document).ready(function() {
        $('#table-data-guru').DataTable({
            dom: '<"top"f>rt<"bottom"p><"clear">',
            pageLength: 25,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            language: {
                search: "",
                searchPlaceholder: "Cari data guru...",
                lengthMenu: "Tampilkan _MENU_ data",
                paginate: { next: '<i data-lucide="chevron-right"></i>', previous: '<i data-lucide="chevron-left"></i>' }
            },
            columnDefs: [{ orderable: false, targets: [0, 1, 5] }],
            drawCallback: function(settings) {
                lucide.createIcons();
                var api = this.api();
                if (api.page.info().pages <= 1) {
                    $('.dataTables_wrapper .bottom').hide();
                } else {
                    $('.dataTables_wrapper .bottom').show();
                }
            }
        });
    });
</script>
