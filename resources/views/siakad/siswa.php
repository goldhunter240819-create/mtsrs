<?php
// data variables: $siswas, $kelas, $selected_kelas, $title
$db = \App\Core\Database::connect('core');
$inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
?>

<style>
    /* ─── DataTable Controls Override ─── */
    .dt-controls-wrap { display: flex; align-items: center; gap: 15px; margin: 0; }
    .dt-controls-wrap .dataTables_length, .dt-controls-wrap .dataTables_filter { margin: 0 !important; float: none !important; text-align: left !important; display: flex; align-items: center; gap: 10px; }
    .dt-controls-wrap .dataTables_length select, .dt-controls-wrap .dataTables_filter input { 
        padding: 6px 12px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-size: 0.85rem; 
    }
    .dt-controls-wrap .dataTables_filter input { width: 150px; }
    
    .filter-card {
        display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; margin-bottom: 1.5rem; flex-wrap: nowrap; overflow-x: auto; scrollbar-width: none; gap: 20px;
    }

    /* ─── DataTables Custom ─── */
    .dataTables_wrapper .top { padding: 1rem 1.5rem 0; margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center; }
    .dataTables_filter { margin: 0; text-align: right; }
    .dataTables_filter label { font-weight: 600; color: #475569; display: flex; align-items: center; gap: 8px; }
    .dataTables_filter input, .dataTables_length select {
        padding: 8px 14px; border: 1.5px solid #cbd5e1; border-radius: 10px;
        font-family: inherit; font-size: 0.9rem; color: #1e293b; background: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01); transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
    }
    .dataTables_filter input { width: 250px; margin-left: 8px; }
    .dataTables_filter input:focus, .dataTables_length select:focus {
        border-color: #059669; background: #fff; box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.15); outline: none;
    }
    .dataTables_wrapper .bottom { padding: 1rem 1.5rem; display: flex; justify-content: flex-end; }
    .dataTables_wrapper .dataTables_paginate { margin: 0; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { 
        border-radius: 12px !important; border: 1px solid #e2e8f0 !important; 
        background: #fff !important; color: #475569 !important; font-weight: 700 !important; 
        padding: 5px 12px; margin: 0 3px; transition: 0.2s; cursor: pointer;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #059669 !important; color: #fff !important; border-color: #059669 !important; }
    .dt-controls-wrap { display: flex; align-items: center; gap: 15px; margin-left: auto; justify-content: flex-end; width: 100%; }
    .filter-group { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
    .summary-pill { flex-shrink: 0; margin-left: 15px !important; }
    
    /* ─── Layout ─── */
    /* z-main background diatur di z-style.css (body gradient) */

    /* ─── Elite Page Header ─── */
    .elite-page-header {
        position: relative; padding: 2rem; margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 24px; overflow: hidden; color: #fff;
        box-shadow: 0 15px 30px -10px rgba(15, 23, 42, 0.3);
        border: 1px solid rgba(255,255,255,0.05);
    }
    .header-bg-shapes {
        position: absolute; top: 0; right: 0; bottom: 0; left: 0;
        background-image: radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.15) 0%, transparent 40%),
                          radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.1) 0%, transparent 40%);
        z-index: 1;
    }
    .header-bg-shapes::after {
        content: ''; position: absolute; inset: 0; opacity: 0.15;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M54.627 0l.83.83L1.244 55.443L.414 54.613L54.627 0zm-3.883 0l.83.83L0 51.573L0 50.743L50.744 0h3.883zM0 54.613l.83.83L4.713 60H3.883L0 56.117v-1.504zM0 46.857l.83.83L12.426 60H11.596L0 48.404v-1.547zM0 39.102l.83.83L20.14 60h-.83L0 40.648v-1.546zm0-7.746l.83.83L27.853 60h-.83L0 32.893v-1.546zm0-7.746l.83.83L35.566 60h-.83L0 25.138v-1.546zm0-7.746l.83.83L43.28 60h-.83L0 17.382v-1.546zM0 8.39l.83.83L50.993 60h-.83L0 9.728V8.39zM8.39 0l.83.83L60 50.78v.83L9.728 0H8.39zM16.136 0l.83.83L60 43.033v.83L17.474 0h-1.338zm7.746 0l.83.83L60 35.287v.83L25.22 0h-1.338zm7.746 0l.83.83L60 27.54v.83L32.966 0h-1.338zm7.746 0l.83.83L60 19.794v.83L40.712 0h-1.338zm7.746 0l.83.83L60 12.048v.83L48.458 0h-1.338zm7.746 0l.83.83L60 4.302v.83L56.204 0h-1.338z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
    }
    .header-content-inner { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; }
    .header-badge {
        display: inline-block; padding: 4px 10px; background: rgba(16, 185, 129, 0.2);
        color: #10b981; font-size: 0.6rem; font-weight: 800; border-radius: 20px;
        border: 1px solid rgba(16, 185, 129, 0.3); margin-bottom: 0.75rem; letter-spacing: 0.5px;
    }
    .header-text-premium h1 { font-size: 1.6rem; font-weight: 900; margin: 0; letter-spacing: -1px; line-height: 1.2; }
    .header-text-premium p { font-size: 0.85rem; opacity: 0.8; margin-top: 0.5rem; max-width: 500px; font-weight: 500; line-height: 1.5; }

    .header-actions-premium { display: flex; gap: 8px; }
    .e-btn {
        display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;
        border-radius: 12px; font-weight: 700; font-size: 0.8rem; cursor: pointer;
        border: none; transition: 0.2s ease;
        font-family: inherit; text-decoration: none;
    }
    .e-btn:hover { transform: translateY(-5px) scale(1.02); }
    .e-btn i { width: 18px; height: 18px; }

    .e-btn-primary { background: #10b981; color: #fff; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3); }
    .e-btn-dark { background: #0f172a; color: #fff; border: 1px solid rgba(255,255,255,0.1); }
    .e-btn-glass { background: rgba(255, 255, 255, 0.1); color: #fff; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
    .e-btn-emerald-light { background: #ecfdf5; color: #059669; }
    .e-btn-glass:hover { background: rgba(255, 255, 255, 0.2); }

    /* ─── Filter & Search ─── */
    .filter-card { 
        padding: 1rem 1.5rem; border-radius: 16px; 
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(226, 232, 240, 0.6); margin-bottom: 1.5rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.03);
        display: flex; align-items: center;
    }
    .filter-group { display: flex; align-items: center; gap: 12px; }
    .filter-label { font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .z-select-premium {
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 8px 16px; font-weight: 700; color: #1e293b; font-size: 0.85rem;
        outline: none; cursor: pointer; min-width: 150px; transition: 0.2s;
    }
    .z-select-premium:focus { border-color: #10b981; background: #fff; box-shadow: 0 0 0 4px rgba(16,185,129,0.1); }
    .summary-pill { background: #f0fdf4; color: #16a34a; padding: 8px 16px; border-radius: 50px; font-size: 0.85rem; font-weight: 800; border: 1px solid rgba(22,163,74,0.1); }

    /* ─── Table Modern Elite ─── */
    .table-card { 
        border-radius: 16px; overflow: hidden; 
        background: rgba(255, 255, 255, 0.75); 
        backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(226, 232, 240, 0.6);
        box-shadow: 0 2px 12px rgba(0,0,0,0.03); 
        padding: 0.5rem;
    }
    .z-table-wrap { border-radius: 12px; overflow: hidden; border: 1px solid #f1f5f9; background: #fff; margin: 0; }
    
    .bulk-action-bar {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 1rem 1.5rem; display: none;
        justify-content: space-between; align-items: center; 
        border-radius: 12px 12px 0 0;
        animation: slideDown 0.3s ease-out;
        color: white;
    }
    @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .selected-text { color: #34d399; font-weight: 800; font-size: 0.95rem; display: flex; align-items: center; gap: 8px; }
    .bulk-btns { display: flex; gap: 10px; }
    .bulk-btns .bulk-btn {
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);
        color: #f8fafc; padding: 6px 14px; border-radius: 8px;
        font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: 0.2s;
        display: flex; align-items: center; gap: 6px;
    }
    .bulk-btns .bulk-btn:hover { background: rgba(255,255,255,0.2); transform: translateY(-1px); }
    .bulk-btns .bulk-btn.btn-print { color: #38bdf8; border-color: rgba(56, 189, 248, 0.3); background: rgba(56, 189, 248, 0.1); }
    .bulk-btns .bulk-btn.btn-print:hover { background: rgba(56, 189, 248, 0.2); }
    .bulk-btns .bulk-btn.btn-reset { color: #a78bfa; border-color: rgba(167, 139, 250, 0.3); background: rgba(167, 139, 250, 0.1); }
    .bulk-btns .bulk-btn.btn-reset:hover { background: rgba(167, 139, 250, 0.2); }
    .bulk-btns .bulk-btn.btn-delete { color: #fb7185; border-color: rgba(251, 113, 133, 0.3); background: rgba(251, 113, 133, 0.1); }
    .bulk-btns .bulk-btn.btn-delete:hover { background: rgba(251, 113, 133, 0.2); }
    .z-table-modern thead th {
        background: #f8fafc; padding: 0.75rem 1rem; text-align: left;
        font-size: 0.7rem; font-weight: 800; color: #475569;
        text-transform: uppercase; letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0;
    }
    .z-table-modern tbody td { padding: 0.5rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: 0.85rem; }
    .z-table-modern tr:last-child td { border-bottom: none; }
    .z-table-modern tr:hover td { background: #f8fafc; }
    
    .z-table-modern tbody tr { animation: fadeInUp 0.4s ease-out forwards; opacity: 0; }
    .z-table-modern tbody tr:nth-child(1) { animation-delay: 0.05s; }
    .z-table-modern tbody tr:nth-child(2) { animation-delay: 0.1s; }
    .z-table-modern tbody tr:nth-child(3) { animation-delay: 0.15s; }
    .z-table-modern tbody tr:nth-child(4) { animation-delay: 0.2s; }
    .z-table-modern tbody tr:nth-child(5) { animation-delay: 0.25s; }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

    .avatar-group { display: flex; align-items: center; gap: 10px; }
    .avatar-box {
        width: 36px; height: 36px; border-radius: 10px; background: #f1f5f9;
        overflow: hidden; border: 1px solid #e2e8f0;
        cursor: pointer; flex-shrink: 0; transition: 0.2s;
    }
    .avatar-box:hover { transform: scale(1.05); }
    .avatar-img { width: 100%; height: 100%; object-fit: cover; }
    .avatar-actions { display: flex; flex-direction: row; gap: 4px; margin-left: 5px; }
    .av-btn {
        width: 24px; height: 24px; border-radius: 6px; border: 1px solid #e2e8f0;
        background: white; color: #64748b; cursor: pointer; transition: 0.2s;
        display: flex; align-items: center; justify-content: center;
    }
    .av-btn i { width: 12px; }
    .av-btn:hover { background: #f8fafc; color: #10b981; border-color: #10b981; }

    .name-box .main-name { font-weight: 700; color: #0f172a; font-size: 0.85rem; }
    .name-box .sub-info { font-size: 0.7rem; color: #64748b; font-family: 'JetBrains Mono', monospace; margin-top: 2px; }
    
    .contact-box { font-size: 0.8rem; color: #64748b; line-height: 1.6; }
    .contact-box i { width: 14px; vertical-align: middle; margin-right: 6px; opacity: 0.7; }

    .z-badge-emerald {
        background: #ecfdf5; color: #059669; padding: 4px 10px;
        border-radius: 10px; font-size: 0.75rem; font-weight: 800;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .z-action-btns {
        display: flex; gap: 6px; align-items: center; justify-content: center;
    }
    .action-btn {
        width: 30px; height: 30px; border-radius: 8px; display: flex;
        align-items: center; justify-content: center; border: none;
        cursor: pointer; transition: 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.06); }
    .action-btn i { width: 14px; height: 14px; }
    .card-btn { background: #f0fdf4; color: #16a34a; }
    .edit-btn { background: #eff6ff; color: #2563eb; }
    .delete-btn { background: #fff1f2; color: #e11d48; }

    .z-modal-tabs { 
        display: flex; gap: 8px; background: #f1f5f9; 
        padding: 8px; border-radius: 20px; margin: 1.5rem 2rem 0.5rem;
        border: 1px solid #e2e8f0;
    }
    .z-tab-btn {
        flex: 1; padding: 12px; font-size: 0.9rem; font-weight: 800; color: #64748b;
        cursor: pointer; position: relative; transition: 0.3s;
        text-align: center; border-radius: 14px;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .z-tab-btn:hover { color: #10b981; background: #fff; }
    .z-tab-btn.active { 
        color: #fff; background: #10b981; 
        box-shadow: 0 10px 20px rgba(16,185,129,0.2);
    }
    .z-tab-content { display: none; padding: 2rem; max-height: calc(90vh - 250px); overflow-y: auto; scrollbar-width: thin; }
    .z-tab-content.active { display: block; animation: slideUpFade 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes slideUpFade { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

    .elite-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 2.5rem 3rem; color: #fff; border: none;
        position: relative; overflow: hidden;
    }
    .elite-header::before {
        content: ''; position: absolute; right: -30px; top: -30px;
        width: 150px; height: 150px; background: rgba(16, 185, 129, 0.2);
        border-radius: 50%; filter: blur(40px);
    }
    .elite-header h3 { font-size: 1.6rem; font-weight: 950; letter-spacing: -1px; margin: 0; display: flex; align-items: center; gap: 15px; }
    .elite-header p { margin: 8px 0 0; font-size: 0.95rem; opacity: 0.7; font-weight: 500; }
    .elite-header .close-btn { color: #fff !important; opacity: 0.8; }
    .elite-header .close-btn:hover { opacity: 1; transform: rotate(90deg); }

    /* ─── Elite Form ─── */
    .z-form-grid-3, .z-form-grid-2, .z-form-grid-4 { 
        display: grid; gap: 1.5rem; 
    }
    .z-form-grid-3 { grid-template-columns: repeat(3, 1fr); }
    .z-form-grid-2 { grid-template-columns: repeat(2, 1fr); }
    .z-form-grid-4 { grid-template-columns: repeat(4, 1fr); }

    .z-tab-content label {
        display: block; font-size: 0.75rem; font-weight: 800;
        color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;
        margin-bottom: 10px; margin-top: 5px;
    }
    .z-input {
        width: 100%; padding: 14px 18px; border-radius: 16px;
        border: 2px solid #f1f5f9; background: #f8fafc;
        font-weight: 700; color: #1e293b; transition: 0.3s;
        font-size: 0.95rem !important;
    }
    .z-input:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 5px rgba(16,185,129,0.1);
        background: #fff; outline: none;
    }
    
    .form-section { background: #f8fafc; padding: 1.5rem; border-radius: 20px; border: 1px solid #e2e8f0; }
    .form-section h4 { 
        margin: 0 0 1.25rem 0; color: #10b981; font-size: 0.95rem; font-weight: 900;
        display: flex; align-items: center; gap: 8px;
    }
    .form-section h4::before { content: ''; width: 4px; height: 16px; background: #10b981; border-radius: 4px; }
    
    .upload-area {
        text-align: center; padding: 4rem 2rem; background: #f8fafc;
        border: 2px dashed #cbd5e1; border-radius: 24px; color: #64748b;
        transition: 0.3s; cursor: pointer;
    }
    .upload-area:hover { border-color: var(--z-primary); background: #f0fdf4; color: var(--z-primary); }
    .upload-area i { width: 48px; height: 48px; margin-bottom: 1rem; color: #94a3b8; transition: 0.3s; }
    .upload-area:hover i { transform: translateY(-5px); color: var(--z-primary); }

    .col-span-2 { grid-column: span 2; }
    .col-span-3 { grid-column: span 3; }
    .col-span-4 { grid-column: span 4; }

    /* ─── Modal Override (Core diatur di layout.php) ─── */
    /* Hanya override visual spesifik halaman ini */
    .z-modal-content.large {
        border-radius: 32px !important;
        overflow: hidden !important;
    }
    @keyframes modalEntrance {
        from { opacity: 0; transform: scale(0.92) translateY(25px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .z-modal-content {
        animation: modalEntrance 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    /* ─── Export Modal ─── */
    .export-icon { width: 64px; height: 64px; background: #f0f9ff; color: #0284c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
    .export-icon i { width: 32px; height: 32px; }
    .export-desc { color: #64748b; margin-bottom: 2rem; }
    .export-options { display: flex; flex-direction: column; gap: 12px; margin-bottom: 2rem; }
    .export-label {
        display: flex; align-items: center; gap: 15px; padding: 1.25rem;
        border: 2px solid #e2e8f0; border-radius: 16px; cursor: pointer; transition: 0.2s; text-align: left;
    }
    .export-label:hover { border-color: #10b981; background: #fafffe; }
    .export-label input[type="radio"] { width: 20px; height: 20px; accent-color: #10b981; }
    .option-title { display: block; font-weight: 800; color: #1e293b; font-size: 0.95rem; }
    .option-sub { font-size: 0.75rem; color: #94a3b8; }

    /* ─── Card Print ─── */
    #modal-student-card { overflow-y: auto !important; display: none; align-items: flex-start !important; justify-content: center !important; padding: 2rem 1rem !important; }
    #modal-student-card .card-print-container { display: flex; flex-direction: column; align-items: center; gap: 1.5rem; margin: auto; }
    .card-side { width: 85.6mm; height: 54mm; background: white; border-radius: 4mm; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.3); border: 1px solid #e2e8f0; position: relative; flex-shrink: 0; }
    .card-header-bg { height: 16mm; background: linear-gradient(135deg, #059669 0%, #10b981 100%); display: flex; align-items: center; padding: 0 4mm; color: white; }
    .card-logo { width: 12mm; height: 12mm; object-fit: contain; margin-right: 3mm; }
    .card-type { font-weight: 800; font-size: 11px; letter-spacing: 1px; }
    .inst-name { font-weight: 700; font-size: 10px; }
    .inst-addr { font-size: 6px; opacity: 0.8; }
    
    .card-body { display: flex; padding: 3mm 4mm; gap: 4mm; }
    .photo-area { width: 22mm; height: 30mm; border: 1px solid #e2e8f0; border-radius: 1mm; overflow: hidden; padding: 1mm; background: white; }
    .photo-area img { width: 100%; height: 100%; object-fit: cover; }
    .data-area { flex: 1; text-align: left; }
    .card-table { width: 100%; font-size: 8px; line-height: 1.5; }
    .card-table td { vertical-align: top; }
    .card-table td.bold { font-weight: 800; color: #0f172a; }
    
    .seal-area { display: flex; align-items: flex-end; gap: 2mm; margin-top: 2mm; }
    .qr-ttd { width: 12mm; height: 12mm; }
    .seal-text { font-size: 5px; color: #64748b; line-height: 1.3; }
    .head-name { font-weight: 800; color: #0f172a; margin-top: 1.5mm; border-bottom: 0.5px solid #0f172a; display: inline-block; }
    
    .back { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4mm; }
    .back-title { font-weight: 800; font-size: 10px; color: #059669; margin-bottom: 3mm; }
    .back-qr-box { background: white; padding: 2mm; border: 1px solid #e2e8f0; border-radius: 2mm; margin-bottom: 3mm; }
    .main-qr { width: 25mm; height: 25mm; }
    .back-credentials { width: 100%; border-top: 1px dashed #e2e8f0; padding-top: 2mm; display: flex; justify-content: space-between; }
    .cred { text-align: left; }
    .cred span { display: block; font-size: 6px; color: #94a3b8; font-weight: 700; }
    .cred b { font-size: 9px; color: #0f172a; letter-spacing: 1px; }

    /* ─── Modal View Photo ─── */
    .z-modal-photo {
        display: none; position: fixed; inset: 0; z-index: 110000;
        background: rgba(0,0,0,0.9); backdrop-filter: blur(20px);
        align-items: center; justify-content: center; padding: 20px;
    }
    .photo-wrapper { position: relative; max-width: 90vw; max-height: 90vh; text-align: center; }
    .photo-wrapper img { max-width: 100%; max-height: 80vh; border-radius: 20px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); border: 3px solid rgba(255,255,255,0.1); }
    #view-photo-name { color: #fff; margin-top: 20px; font-weight: 800; font-size: 1.2rem; }

    @media print {
        body * { visibility: hidden; }
        #printable-student-card, #printable-student-card * { visibility: visible; }
        #printable-student-card { position: fixed; left: 0; top: 0; width: 210mm; height: 297mm; display: block !important; padding: 20mm !important; }
        .card-side { margin-bottom: 10mm; box-shadow: none !important; border: 1px solid #ccc !important; }
        .print-actions { display: none !important; }
    }

    /* ─── MISMIFHDA Modal Structure Overrides ─── */
    div.z-modal {
        display: none; position: fixed; inset: 0; 
        background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); 
        z-index: 100000; align-items: center; justify-content: center;
        width: 100%; max-width: none; border-radius: 0; box-shadow: none; border: none;
    }
    .z-modal-content {
        background: #fff; border-radius: 24px; width: 90%; max-height: 90vh;
        display: flex; flex-direction: column; position: relative;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }
    .z-modal-content.small { max-width: 480px; }
    .z-modal-content.large { max-width: 900px; }
    .z-modal-header {
        padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
    }
    .z-modal-header h3 { margin: 0; font-size: 1.1rem; font-weight: 800; }
    .z-modal-header .close-btn {
        background: none; border: none; cursor: pointer; color: #94a3b8; transition: 0.2s;
    }
    .z-modal-header .close-btn:hover { color: #e11d48; }
    .z-modal-body { padding: 1.5rem 2rem; overflow-y: auto; flex: 1; }
    .z-modal-footer {
        padding: 1.5rem 2rem; border-top: 1px solid #f1f5f9; background: #f8fafc;
        display: flex; align-items: center; justify-content: flex-end; gap: 10px;
        border-radius: 0 0 24px 24px;
    }
</style>


            <!-- DataTables CSS & jQuery (since DataTables requires jQuery) -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
            <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
            
        <div class="modern-page-header">
            <div>
                <h1 class="mph-title">
                    <i data-lucide="users" style="color: #bfdbfe;"></i> Data Siswa
                </h1>
                <p class="mph-subtitle">Manajemen biodata, identitas, dan cetak kartu pelajar.</p>
            </div>
            <div class="mph-actions" style="flex-wrap: wrap; justify-content: flex-end;">
                <button class="btn" onclick="document.getElementById('modal-export-siswa').style.display='flex'">
                    <i data-lucide="printer"></i> Export / PDF
                </button>
                <a href="/siakad/siswa/cetak-kartu<?php echo $selected_kelas ? '?kelas_id='.$selected_kelas : ''; ?>" target="_blank" class="btn" style="text-decoration:none;">
                    <i data-lucide="id-card"></i> Cetak Massal
                </a>
                <button class="btn" onclick="document.getElementById('modal-import-siswa').style.display='flex'">
                    <i data-lucide="copy-plus"></i> Import Data
                </button>
                <button class="btn" onclick="document.getElementById('modal-upload-foto-masal').style.display='flex'">
                    <i data-lucide="image-plus"></i> Upload Foto Masal
                </button>
                <button type="button" onclick="openAddSiswaModal()" class="btn btn-primary-white">
                    <i data-lucide="user-plus"></i> Tambah Siswa
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

        <?php if (isset($_GET['upload_sukses']) || isset($_GET['upload_gagal'])): ?>
            <div style="background: #ecfeff; border: 1px solid #06b6d4; color: #164e63; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 700;">
                    <i data-lucide="info" style="width:18px; vertical-align:middle; margin-right:8px; color:#06b6d4;"></i> 
                    Upload Foto Masal Selesai. 
                    <span style="color: #10b981;">Berhasil: <?php echo intval($_GET['upload_sukses'] ?? 0); ?> foto</span>, 
                    <span style="color: #ef4444;">Gagal/Tidak Ditemukan: <?php echo intval($_GET['upload_gagal'] ?? 0); ?> foto</span>.
                </div>
                <button onclick="this.parentElement.style.display='none'" style="background:none; border:none; color:#164e63; cursor:pointer;"><i data-lucide="x"></i></button>
            </div>
        <?php endif; ?>

        <!-- Filter & Summary -->
        <div class="z-card filter-card">
            <div style="display: flex; align-items: center; gap: 20px; flex: 1;">
                <form action="" method="GET" class="filter-form" style="display: flex; align-items: center; gap: 15px; margin: 0;">
                    <div class="filter-group" style="display: flex; align-items: center; gap: 10px;">
                        <span class="filter-label" style="font-weight: 700; color: #475569; font-size: 0.85rem;">Filter Kelas:</span>
                        <select name="kelas_id" onchange="this.form.submit()" class="z-select-premium" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-weight: 600; outline: none;">
                            <option value="">-- Pilih Kelas --</option>
                            <option value="all" <?php echo $selected_kelas === 'all' ? 'selected' : ''; ?>>Tampilkan Semua</option>
                            <?php foreach($kelas as $k): ?>
                                <option value="<?php echo $k['id']; ?>" <?php echo $selected_kelas == $k['id'] ? 'selected' : ''; ?>>
                                    KELAS <?php echo $k['nama_kelas']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
                <div id="dt-custom-controls" class="dt-controls-wrap"></div>
            </div>
            <div style="display: flex; align-items: center; gap: 15px; flex-shrink: 0;">
                <button type="button" onclick="const e = document.getElementById('rekap-kelas-container'); e.style.display = e.style.display === 'none' ? 'flex' : 'none';" style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 50px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; color: #3b82f6; font-size: 0.85rem; padding: 8px 16px; transition: 0.2s;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                    <i data-lucide="bar-chart-2" style="width: 16px;"></i> Rekap Kelas
                </button>
                <div class="summary-pill" style="margin: 0;">
                    <i data-lucide="users" style="width: 16px; margin-right: 5px; vertical-align: middle;"></i>
                    <b><?php echo count($siswas); ?></b> Siswa Aktif
                </div>
            </div>
        </div>

    <!-- MODAL UPLOAD FOTO MASAL -->
    <div class="z-modal" id="modal-upload-foto-masal">
        <div class="z-modal-content small" style="animation: slideUpFade 0.4s ease-out;">
            <div class="z-modal-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white;">
                <h3 style="display: flex; align-items: center; gap: 10px;"><i data-lucide="image-plus" style="color: #60a5fa;"></i> Upload Foto Masal</h3>
                <button class="close-btn" onclick="document.getElementById('modal-upload-foto-masal').style.display='none'" style="color:white;"><i data-lucide="x"></i></button>
            </div>
            <form action="#" method="POST" enctype="multipart/form-data" id="form-foto-masal">
                <div class="z-modal-body">
                    <p style="font-size: 0.85rem; color: #64748b; line-height: 1.5; margin-bottom: 1.5rem;">
                        <strong>Catatan Penting:</strong> Pastikan nama file foto menggunakan <strong>NISN</strong> atau <strong>NIS</strong> siswa yang bersangkutan. Contoh: <code>1234567890.jpg</code>. <br>
                        Format yang didukung: JPG, JPEG, PNG.
                    </p>
                    <label style="font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Pilih File Foto</label>
                    <div style="position: relative; margin-top: 10px;">
                        <input type="file" id="foto_masal_input" multiple accept="image/*" class="z-input" required onchange="document.getElementById('foto-masal-count').textContent = this.files.length + ' file terpilih';">
                    </div>
                    <div id="foto-masal-count" style="margin-top: 8px; font-size: 0.85rem; font-weight: 700; color: #10b981;">0 file terpilih</div>
                    
                    <!-- Progress Bar Container -->
                    <div id="upload-progress-container" style="display: none; margin-top: 20px;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 700; margin-bottom: 5px; color: #475569;">
                            <span id="upload-status-text">Mengupload...</span>
                            <span id="upload-percentage">0%</span>
                        </div>
                        <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                            <div id="upload-progress-bar" style="width: 0%; height: 100%; background: #10b981; transition: width 0.3s ease;"></div>
                        </div>
                        <div id="upload-log" style="margin-top: 10px; font-size: 0.75rem; color: #64748b; max-height: 100px; overflow-y: auto; background: #f8fafc; padding: 8px; border-radius: 8px;"></div>
                    </div>
                </div>
                <div class="z-modal-footer">
                    <button type="button" class="btn" id="btn-cancel-upload" onclick="document.getElementById('modal-upload-foto-masal').style.display='none'">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-start-upload" onclick="startAjaxUpload()">
                        <i data-lucide="upload"></i> Upload Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

        <!-- Rekapitulasi Siswa -->
        <div id="rekap-kelas-container" style="display: none; flex-wrap: wrap; gap: 15px; margin-bottom: 1.5rem; padding-bottom: 5px;">
            <?php
            $rekap_query = $db->query("
                SELECT k.nama_kelas, 
                       SUM(CASE WHEN s.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as jml_l,
                       SUM(CASE WHEN s.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as jml_p,
                       COUNT(s.id) as total
                FROM kelas k
                LEFT JOIN (
                    SELECT s.id, s.jenis_kelamin, COALESCE(rks.kelas_id, s.kelas_id) as actual_kelas_id
                    FROM siswa s 
                    LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                        AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$active_year_name')
                    WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')
                ) s ON s.actual_kelas_id = k.id
                GROUP BY k.id, k.nama_kelas, k.tingkat
                ORDER BY k.tingkat ASC, k.nama_kelas ASC
            ")->fetchAll(\PDO::FETCH_ASSOC);

            $total_l = 0; $total_p = 0; $total_all = 0;
            foreach($rekap_query as $r):
                $total_l += $r['jml_l'];
                $total_p += $r['jml_p'];
                $total_all += $r['total'];
            ?>
            <div style="background: rgba(255,255,255,0.75); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(226,232,240,0.6); border-radius: 12px; padding: 12px 16px; min-width: 140px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); flex-shrink: 0;">
                <div style="font-weight: 800; color: #1e293b; font-size: 0.9rem; margin-bottom: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 4px;">Kelas <?php echo htmlspecialchars($r['nama_kelas']); ?></div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #64748b; margin-bottom: 3px;">
                    <span>Laki-laki:</span> <span style="font-weight: 700; color: #3b82f6;"><?php echo $r['jml_l'] ?: 0; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #64748b; margin-bottom: 3px;">
                    <span>Perempuan:</span> <span style="font-weight: 700; color: #ec4899;"><?php echo $r['jml_p'] ?: 0; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 800; color: #0f172a; margin-top: 5px; padding-top: 5px; border-top: 1px solid #f1f5f9;">
                    <span>Total:</span> <span><?php echo $r['total']; ?></span>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Total Keseluruhan -->
            <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 12px; padding: 12px 16px; min-width: 140px; box-shadow: 0 4px 10px rgba(16,185,129,0.2); flex-shrink: 0;">
                <div style="font-weight: 800; font-size: 0.9rem; margin-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 4px;">TOTAL SEMUA</div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 3px; opacity: 0.9;">
                    <span>Laki-laki:</span> <span style="font-weight: 700;"><?php echo $total_l; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 3px; opacity: 0.9;">
                    <span>Perempuan:</span> <span style="font-weight: 700;"><?php echo $total_p; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 800; margin-top: 5px; padding-top: 5px; border-top: 1px solid rgba(255,255,255,0.2);">
                    <span>Total:</span> <span><?php echo $total_all; ?></span>
                </div>
            </div>
        </div>

        <?php if($selected_kelas === ''): ?>
            <div class="z-card table-card" style="padding: 4rem; text-align: center; color: #64748b;">
                <i data-lucide="filter" style="width: 48px; height: 48px; opacity: 0.2; margin-bottom: 1rem;"></i>
                <h3 style="margin: 0 0 10px; color: #1e293b;">Pilih Kelas Terlebih Dahulu</h3>
                <p style="margin: 0; font-size: 0.95rem;">Silakan pilih kelas pada filter di atas untuk menampilkan data siswa.</p>
            </div>
        <?php else: ?>
        <form id="form-bulk-siswa" action="/siakad/siswa/delete-bulk" method="POST">
            <div class="z-card table-card">
                <div class="bulk-action-bar" id="bulk-action-bar-siswa">
                    <span class="selected-text">
                        <i data-lucide="check-square"></i> <span id="selected-count-siswa">0</span> siswa terpilih
                    </span>
                    <div class="bulk-btns">
                        <button type="button" onclick="confirmBulkActionSiswa('print')" class="bulk-btn btn-print">
                            <i data-lucide="printer"></i> Cetak Kartu
                        </button>
                        <button type="button" onclick="confirmBulkActionSiswa('reset-password')" class="bulk-btn btn-reset">
                            <i data-lucide="refresh-cw"></i> Reset Akun
                        </button>
                        <button type="button" onclick="confirmBulkActionSiswa('delete')" class="bulk-btn btn-delete">
                            <i data-lucide="trash-2"></i> Hapus
                        </button>
                    </div>
                </div>

                <div class="z-table-wrap">
                    <table id="table-data-siswa" class="z-table-modern">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 40px;"><input type="checkbox" id="check-all-siswa" class="z-check"></th>
                                <th style="width: 120px;">Foto</th>
                                <th style="width: 100px;">NIS</th>
                                <th style="width: 100px;">NISN</th>
                                <th>Nama Lengkap</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($siswas as $s): ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="<?php echo $s['id']; ?>" class="check-siswa z-check">
                                </td>
                                <td>
                                    <div class="avatar-group">
                                        <?php 
                                            $fotoUrl = ($s['foto'] && file_exists(__DIR__ . "/../../../public/uploads/siswa/" . $s['foto'])) 
                                                ? '/public/uploads/siswa/'.$s['foto'] 
                                                : 'https://ui-avatars.com/api/?name='.urlencode($s['nama']).'&background=065f46&color=fff&bold=true&size=128';
                                            $jsonData = htmlspecialchars(json_encode(['id' => $s['id'], 'nama' => $s['nama'], 'url' => $fotoUrl], JSON_PARTIAL_OUTPUT_ON_ERROR), ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <div class="avatar-box btn-view-photo" data-siswa='<?php echo $jsonData; ?>'>
                                            <?php if($s['foto'] && file_exists(__DIR__ . "/../../../public/uploads/siswa/" . $s['foto'])): ?>
                                                <?php $thumbName = pathinfo($s['foto'], PATHINFO_FILENAME) . '_thumb.' . pathinfo($s['foto'], PATHINFO_EXTENSION); ?>
                                                <img id="img-siswa-<?php echo $s['id']; ?>" src="/public/uploads/siswa/<?php echo $thumbName; ?>" onerror="this.onerror=null; this.src='/public/uploads/siswa/<?php echo $s['foto']; ?>';" loading="lazy" class="avatar-img" data-full="/public/uploads/siswa/<?php echo $s['foto']; ?>">
                                            <?php else: ?>
                                                <img id="img-siswa-<?php echo $s['id']; ?>" src="https://ui-avatars.com/api/?name=<?php echo urlencode($s['nama']); ?>&background=065f46&color=fff&bold=true&size=128" loading="lazy" class="avatar-img" data-full="https://ui-avatars.com/api/?name=<?php echo urlencode($s['nama']); ?>&background=065f46&color=fff&bold=true&size=128">
                                            <?php endif; ?>
                                        </div>
                                        <div class="avatar-actions">
                                            <button type="button" class="av-btn view-btn btn-view-photo" data-siswa='<?php echo $jsonData; ?>'><i data-lucide="eye"></i></button>
                                            <button type="button" class="av-btn upload-btn btn-quick-upload" data-siswa='<?php echo $jsonData; ?>'><i data-lucide="upload"></i></button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; font-weight: 700; color: #475569;">
                                        <?php echo $s['nis']; ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; font-weight: 700; color: #94a3b8;">
                                        <?php echo $s['nisn'] ?: '-'; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="name-box">
                                        <div class="main-name"><?php echo $s['nama']; ?></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="z-badge-emerald"><?php echo $s['nama_kelas']; ?></span>
                                </td>
                                <td>
                                    <div class="z-action-btns">
                                        <?php $actionJson = htmlspecialchars(json_encode(['id' => $s['id'], 'nama' => $s['nama']], JSON_PARTIAL_OUTPUT_ON_ERROR), ENT_QUOTES, 'UTF-8'); ?>
                                        <button type="button" class="action-btn card-btn btn-print-card" data-siswa='<?php echo $actionJson; ?>' title="Cetak Kartu Pelajar">
                                            <i data-lucide="id-card"></i>
                                        </button>
                                        <a href="/siakad/siswa/edit/<?php echo $s['id']; ?>" class="action-btn edit-btn" title="Edit">
                                            <i data-lucide="edit-3"></i>
                                        </a>
                                        <button type="button" class="action-btn btn-mutasi" style="background:#fff7ed;color:#ea580c;" data-siswa='<?php echo $actionJson; ?>' title="Mutasi Keluar">
                                            <i data-lucide="arrow-right-left"></i>
                                        </button>
                                        <button type="button" onclick="showAppModal({ title: 'Konfirmasi Hapus', message: 'Hapus siswa ini secara permanen?', type: 'warning', buttons: [ { text: 'Batal', class: 'btn btn-secondary' }, { text: '<i data-lucide=\'trash-2\'></i> Ya, Hapus', class: 'btn btn-danger', href: '<?php echo \App\Core\Helper::url('/siakad/siswa/delete/' . $s['id']); ?>' } ] });" class="action-btn delete-btn" title="Hapus">
                                            <i data-lucide="trash-2"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
        <?php endif; ?>


<input type="file" id="quick-auto-input" accept="image/*" style="display: none;" onchange="handleAutoUpload(this)">
<input type="hidden" id="quick-auto-siswa-id">

<!-- Modal Export -->
<div id="modal-export-siswa" class="z-modal">
    <div class="z-modal-content small">
        <div class="z-modal-header">
            <h3>Export Data Siswa</h3>
            <button onclick="document.getElementById('modal-export-siswa').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body text-center">
            <div class="export-icon"><i data-lucide="printer"></i></div>
            <p class="export-desc">Pilih jenis data yang ingin diexport / dicetak.</p>
            <form action="/siakad/siswa/export" method="GET" target="_blank">
                <input type="hidden" name="kelas_id" value="<?php echo $selected_kelas; ?>">
                <div class="export-options">
                    <label class="export-label">
                        <input type="radio" name="mode" value="public" checked>
                        <div class="option-content">
                            <span class="option-title">Data Publik</span>
                            <span class="option-sub">Identitas dasar (Puskesmas/Dinas)</span>
                        </div>
                    </label>
                    <label class="export-label">
                        <input type="radio" name="mode" value="full">
                        <div class="option-content">
                            <span class="option-title">Data Lengkap</span>
                            <span class="option-sub">Biodata, Alamat & Data Orang Tua</span>
                        </div>
                    </label>
                </div>
                <div class="z-modal-footer">
                    <button type="button" class="z-btn z-btn-ghost" onclick="document.getElementById('modal-export-siswa').style.display='none'">Batal</button>
                    <button type="submit" class="z-btn z-btn-primary">PROSES EXPORT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Mutasi Keluar -->
<div id="modal-mutasi" class="z-modal">
    <div class="z-modal-content small">
        <div class="z-modal-header" style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); color: white; border-radius: 20px 20px 0 0;">
            <h3><i data-lucide="arrow-right-left"></i> Proses Mutasi Keluar</h3>
            <button onclick="document.getElementById('modal-mutasi').style.display='none'" class="close-btn" style="color:white;"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <p style="margin-bottom: 20px; font-size: 0.95rem; color: #64748b;">
                Anda akan memutasi siswa <strong id="mutasi-nama-siswa" style="color: #0f172a;"></strong>. Data tidak akan dihapus dan masih bisa diaktifkan kembali dari menu Mutasi Siswa.
            </p>
            <form id="form-mutasi">
                <input type="hidden" name="siswa_id" id="mutasi-siswa-id">
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:8px; font-weight:700; color:#475569; font-size:0.85rem;">Tanggal Mutasi</label>
                    <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" class="z-input" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:8px; font-weight:700; color:#475569; font-size:0.85rem;">Sekolah Tujuan (Opsional)</label>
                    <input type="text" name="sekolah_tujuan" class="z-input" placeholder="Contoh: SMPN 1 Jakarta">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:8px; font-weight:700; color:#475569; font-size:0.85rem;">Alasan / Keterangan</label>
                    <textarea name="alasan" class="z-input" rows="3" placeholder="Alasan mutasi/pindah sekolah" required></textarea>
                </div>
                <div style="display:flex; gap:10px;">
                    <button type="button" class="z-btn z-btn-ghost" style="flex:1;" onclick="document.getElementById('modal-mutasi').style.display='none'">Batal</button>
                    <button type="button" class="z-btn z-btn-primary" style="flex:1; background:#ea580c;" onclick="submitMutasi()">PROSES MUTASI</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Print Options -->
<div id="modal-print-options" class="z-modal">
    <div class="z-modal-content small">
        <div class="z-modal-header">
            <h3><i data-lucide="printer"></i> Format Cetak Kartu</h3>
            <button onclick="document.getElementById('modal-print-options').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body text-center" style="padding: 30px 20px;">
            <p style="margin-bottom: 25px; color: #64748b; font-size: 0.95rem;">Pilih orientasi cetak untuk kartu identitas.</p>
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button type="button" onclick="executePrintCard('horizontal')" style="flex: 1; padding: 20px; border-radius: 12px; border: 2px solid #e2e8f0; background: white; cursor: pointer; transition: 0.2s;" onmouseover="this.style.borderColor='#059669'; this.style.color='#059669'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#0f172a'">
                    <i data-lucide="layout-template" style="width: 32px; height: 32px; margin-bottom: 10px;"></i>
                    <div style="font-weight: 700;">Horizontal</div>
                </button>
                <button type="button" onclick="executePrintCard('vertical')" style="flex: 1; padding: 20px; border-radius: 12px; border: 2px solid #e2e8f0; background: white; cursor: pointer; transition: 0.2s;" onmouseover="this.style.borderColor='#059669'; this.style.color='#059669'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#0f172a'">
                    <i data-lucide="smartphone" style="width: 32px; height: 32px; margin-bottom: 10px;"></i>
                    <div style="font-weight: 700;">Vertikal</div>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add/Edit Siswa -->
<div id="modal-add-siswa" class="z-modal">
    <div class="z-modal-content large">
        <div class="z-modal-header elite-header">
            <div class="header-content">
                <h3><i data-lucide="file-text"></i> Form Biodata Siswa</h3>
                <p>Lengkapi profil peserta didik untuk database institusi.</p>
            </div>
            <button onclick="document.getElementById('modal-add-siswa').style.display='none'" class="close-btn white"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-tabs">
            <div class="z-tab-btn active" onclick="switchTab('tab-identitas', this)"><i data-lucide="user"></i> Identitas</div>
            <div class="z-tab-btn" onclick="switchTab('tab-legal', this)"><i data-lucide="graduation-cap"></i> Pendidikan</div>
            <div class="z-tab-btn" onclick="switchTab('tab-keluarga', this)"><i data-lucide="users"></i> Orang Tua</div>
            <div class="z-tab-btn" onclick="switchTab('tab-alamat', this)"><i data-lucide="map-pin"></i> Alamat</div>
            <div class="z-tab-btn" onclick="switchTab('tab-foto', this)"><i data-lucide="camera"></i> Foto</div>
        </div>
        <div class="z-modal-body has-tabs">
            <form action="/siakad/siswa/save" method="POST" enctype="multipart/form-data" id="sf-form">
                <input type="hidden" name="id">
                
                <div id="tab-identitas" class="z-tab-content active">
                    <div class="z-form-grid-3">
                        <div class="col-span-2">
                            <label>Nama Lengkap (Sesuai Akta/KK)</label>
                            <input type="text" name="nama" class="z-input" required>
                        </div>
                        <div>
                            <label>NIK (16 Digit)</label>
                            <input type="text" name="nik" class="z-input" maxlength="16">
                        </div>
                        <div>
                            <label>No. Kartu Keluarga (KK)</label>
                            <input type="text" name="no_kk" class="z-input" maxlength="16">
                        </div>
                        <div>
                            <label>NIS (Sekolah)</label>
                            <input type="text" name="nis" class="z-input" required>
                        </div>
                        <div>
                            <label>NISN (Nasional)</label>
                            <input type="text" name="nisn" class="z-input">
                        </div>
                        <div>
                            <label>Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="z-input">
                        </div>
                        <div>
                            <label>Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="z-input">
                        </div>
                        <div>
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="z-input">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label>Agama</label>
                            <select name="agama" class="z-input">
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Budha">Budha</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="tab-legal" class="z-tab-content">
                    <div class="z-form-grid-2">
                        <div>
                            <label>Pendidikan Terakhir</label>
                            <input type="text" name="pendidikan_terakhir" class="z-input" placeholder="SD/MI">
                        </div>
                        <div>
                            <label>Kelas Saat Ini</label>
                            <select name="kelas_id" class="z-input">
                                <option value="">Pilih Kelas</option>
                                <?php foreach($kelas as $k): ?>
                                    <option value="<?php echo $k['id']; ?>"><?php echo $k['nama_kelas']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label>Sekolah Asal</label>
                            <input type="text" name="sekolah_asal" class="z-input">
                        </div>
                        <div>
                            <label>No. Ijazah</label>
                            <input type="text" name="no_ijazah" class="z-input">
                        </div>
                    </div>
                </div>

                <div id="tab-keluarga" class="z-tab-content">
                    <div class="z-form-grid-2">
                        <div class="form-section">
                            <h4>Data Ayah</h4>
                            <label>Nama Ayah</label>
                            <input type="text" name="nama_ayah" class="z-input">
                            <label>Pekerjaan Ayah</label>
                            <input type="text" name="pekerjaan_ayah" class="z-input">
                        </div>
                        <div class="form-section">
                            <h4>Data Ibu</h4>
                            <label>Nama Ibu</label>
                            <input type="text" name="nama_ibu" class="z-input">
                            <label>Pekerjaan Ibu</label>
                            <input type="text" name="pekerjaan_ibu" class="z-input">
                        </div>
                        <div class="col-span-2">
                            <label>Nama Wali (Jika ada)</label>
                            <input type="text" name="nama_wali" class="z-input">
                        </div>
                    </div>
                </div>

                <div id="tab-alamat" class="z-tab-content">
                    <div class="z-form-grid-4">
                        <div class="col-span-4">
                            <label>Alamat Jalan / Dusun</label>
                            <input type="text" name="alamat" class="z-input">
                        </div>
                        <div><label>RT</label><input type="text" name="rt" class="z-input"></div>
                        <div><label>RW</label><input type="text" name="rw" class="z-input"></div>
                        <div class="col-span-2"><label>Kelurahan</label><input type="text" name="desa" class="z-input"></div>
                        <div class="col-span-2"><label>Kecamatan</label><input type="text" name="kecamatan" class="z-input"></div>
                        <div class="col-span-2"><label>Kabupaten/Kota</label><input type="text" name="kota" class="z-input"></div>
                        <div class="col-span-4"><label>No. HP Orang Tua</label><input type="text" name="no_hp_ortu" class="z-input"></div>
                    </div>
                </div>

                <div id="tab-foto" class="z-tab-content">
                    <div class="upload-area">
                        <i data-lucide="image"></i>
                        <h3>Pilih Foto Siswa</h3>
                        <input type="file" name="foto" accept="image/*">
                    </div>
                </div>

                <div class="z-modal-footer" style="padding: 2rem; background: #f8fafc; border-top: 1px solid #e2e8f0; gap: 15px;">
                    <button type="button" class="e-btn e-btn-glass" style="color: #64748b; border: 1px solid #cbd5e1;" onclick="document.getElementById('modal-add-siswa').style.display='none'">Batal</button>
                    <button type="submit" class="e-btn e-btn-primary" style="padding: 14px 40px; font-size: 1rem;">
                        <i data-lucide="save"></i> SIMPAN BIODATA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div id="modal-import-siswa" class="z-modal">
    <div class="z-modal-content large">
        <div class="z-modal-header elite-header">
            <div class="header-content">
                <h3><i data-lucide="upload-cloud"></i> Import Siswa (Smart-Paste)</h3>
                <p>Salin data dari Excel dan paste ke area di bawah ini.</p>
            </div>
            <button onclick="document.getElementById('modal-import-siswa').style.display='none'" class="close-btn white"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body" style="padding: 1.5rem;">
            <form action="/siakad/siswa/import" method="POST">
                <div class="import-layout" style="display: flex; flex-direction: column; gap: 1.2rem;">
                    
                    <div class="form-section" style="background: #ecfdf5; border: 1px solid #a7f3d0; padding: 1rem; border-radius: 12px; margin: 0;">
                        <h4 style="color: #059669; font-size: 0.9rem; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;"><i data-lucide="info" style="width: 16px; height: 16px;"></i> Legend ID Kelas</h4>
                        <p style="font-size: 0.85rem; color: #047857; margin: 0; line-height: 1.6;">
                            <?php foreach($kelas as $k) echo "<span style='display:inline-block; margin-right:10px; background:#d1fae5; padding:2px 8px; border-radius:4px;'>{$k['nama_kelas']} = <b>{$k['id']}</b></span>"; ?>
                            <br><i style="opacity: 0.8; margin-top: 5px; display: inline-block;">* Gunakan angka ID Kelas ini untuk diisi di Kolom 12 (ID Kelas) pada file Excel.</i>
                        </p>
                    </div>

                    <div class="guide-card form-section" style="padding: 1rem; margin: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <h4 style="margin: 0; font-size: 0.9rem; color: #334155;"><i data-lucide="help-circle" style="width: 16px; height: 16px;"></i> Format Kolom Excel (Wajib Urut - 34 Kolom)</h4>
                            <a href="<?php echo \App\Core\Helper::url('/siakad/siswa/template-import'); ?>" class="e-btn e-btn-glass" style="padding: 6px 15px; font-size: 0.8rem; text-decoration: none; display: flex; align-items: center; gap: 6px; border: 1px solid #cbd5e1; color: #0f172a; background: #f8fafc;"><i data-lucide="download" style="width:14px; height:14px;"></i> Template Excel</a>
                        </div>
                        <p style="margin-bottom: 12px; font-size: 0.85rem; color: #64748b; font-weight: 500;">Copy seluruh baris di Excel (termasuk sel yang kosong) lalu paste di area teks bawah. Urutan <b>harus</b> persis sesuai tabel ini:</p>
                        
                        <div style="width: 100%; overflow-x: auto; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff;">
                            <table style="width: max-content; border-collapse: collapse; font-size: 0.8rem; text-align: left;">
                                <thead>
                                    <tr style="background: #f8fafc; color: #475569; font-weight: 600; border-bottom: 2px solid #e2e8f0;">
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">1. NIK</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">2. NIS</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">3. NISN</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">4. Nama Lengkap</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">5. L/P</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">6. Tempat Lahir</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">7. Tgl Lahir (YYYY-MM-DD)</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">8. Agama</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">9. Gol Darah</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">10. Warga Negara</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">11. Anak Ke-</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0; background: #ecfdf5; color: #047857;">12. ID Kelas</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">13. Pend. Terakhir</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">14. Sekolah Asal</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">15. No Ijazah</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">16. No Paspor</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">17. No KITAS</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">18. Nama Ayah</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">19. Pekerjaan Ayah</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">20. Nama Ibu</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">21. Pekerjaan Ibu</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">22. Gaji Ortu</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">23. No HP Ortu</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">24. Nama Wali</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">25. Pekerjaan Wali</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">26. No HP Wali</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">27. Alamat Jalan</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">28. RT</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">29. RW</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">30. Provinsi</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">31. Kota</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">32. Kecamatan</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">33. Desa</td>
                                        <td style="padding: 8px 12px;">34. Kode Pos</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="color: #64748b; font-family: monospace;">
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">3201...</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">23001</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">0051...</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0; color: #0f172a; font-weight: 600;">Ahmad Subagyo</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">L</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">Jakarta</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">2010-05-15</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">Islam</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">O</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">WNI</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0;">1</td>
                                        <td style="padding: 8px 12px; border-right: 1px solid #e2e8f0; background: #f0fdf4; color: #059669; font-weight: bold;">2</td>
                                        <td colspan="22" style="padding: 8px 12px; color: #94a3b8; font-style: italic;">... (Kosongkan sel jika tidak ada data) ...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="import-main">
                        <textarea name="paste_data" placeholder="[Klik di sini, lalu tekan Ctrl+V untuk Paste data dari Excel]" class="z-textarea" style="width: 100%; height: 180px; padding: 15px; border-radius: 12px; border: 2px dashed #cbd5e1; font-family: 'Courier New', Courier, monospace; font-size: 0.9rem; background: #f8fafc; resize: vertical; transition: 0.3s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);" onfocus="this.style.borderColor='#10b981'; this.style.background='#ffffff';" onblur="this.style.borderColor='#cbd5e1'; this.style.background='#f8fafc';" required></textarea>
                    </div>
                </div>
                <div class="z-modal-footer" style="padding: 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; gap: 15px; margin-top: 1.5rem; border-radius: 0 0 20px 20px; display: flex; justify-content: flex-end;">
                    <button type="button" class="e-btn e-btn-glass" style="color: #64748b; border: 1px solid #cbd5e1; padding: 12px 25px;" onclick="document.getElementById('modal-import-siswa').style.display='none'">Batal</button>
                    <button type="submit" class="e-btn e-btn-primary" style="padding: 12px 35px; font-size: 1rem;">
                        <i data-lucide="check-circle"></i> PROSES IMPORT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Student Card -->
<div id="modal-student-card" class="z-modal">
    <div id="printable-student-card" class="card-print-container">
        <!-- Front Side -->
        <div class="card-side front">
            <div class="card-header-bg">
                <?php if($inst && $inst['logo']): ?>
                    <img src="/uploads/logo/<?php echo $inst['logo']; ?>" class="card-logo">
                <?php endif; ?>
                <div class="card-header-text">
                    <div class="card-type">KARTU PELAJAR</div>
                    <div class="inst-name"><?php echo $inst ? $inst['nama'] : 'GH-SSS ACADEMY'; ?></div>
                    <div class="inst-addr"><?php echo $inst ? $inst['alamat'] : ''; ?></div>
                </div>
            </div>
            <div class="card-body">
                <div class="photo-area">
                    <img id="sc-photo" src="">
                </div>
                <div class="data-area">
                    <table class="card-table">
                        <tr><td>Nama</td><td>:</td><td style="max-width: 38mm; overflow: hidden; white-space: nowrap;"><div id="sc-name" class="bold auto-fit-modal" style="font-size: 8px;"></div></td></tr>
                        <tr><td>NIS / NISN</td><td>:</td><td id="sc-nisn"></td></tr>
                        <tr><td>TTL</td><td>:</td><td id="sc-ttl"></td></tr>
                    </table>
                    <div class="seal-area">
                        <img id="sc-ttd-qr" class="qr-ttd">
                        <div class="seal-text">
                            <div><?php echo $inst ? $inst['kota'] : 'Jakarta'; ?>, <?php echo date('d M Y'); ?></div>
                            <div class="head-title">Kepala Sekolah</div>
                            <div class="head-name"><?php echo $inst ? $inst['nama_kepala'] : ''; ?></div>
                            <div class="head-nip">NIP. <?php echo $inst ? $inst['nip_kepala'] : ''; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Back Side -->
        <div class="card-side back">
            <div class="back-title">KARTU PELAJAR ELEKTRONIK</div>
            <div class="back-qr-box">
                <img id="sc-qr" class="main-qr">
            </div>

        </div>
        <div class="print-actions">
            <button class="z-btn z-btn-black" onclick="window.print()"><i data-lucide="printer"></i> CETAK SEKARANG</button>
            <button class="z-btn z-btn-ghost" onclick="document.getElementById('modal-student-card').style.display='none'">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal View Photo -->
<div id="modal-view-photo" class="z-modal-photo" onclick="this.style.display='none'">
    <div class="photo-wrapper">
        <img id="view-photo-img" src="">
        <div id="view-photo-name"></div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    function switchTab(tabId, btn) {
        document.querySelectorAll('.z-tab-content').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.z-tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
        btn.classList.add('active');
    }

    function updateBulkSiswa() {
        let count = document.querySelectorAll('.check-siswa:checked').length;
        document.getElementById('selected-count-siswa').innerText = count;
        document.getElementById('bulk-action-bar-siswa').style.display = count > 0 ? 'flex' : 'none';
    }

    document.body.addEventListener('click', function(e) {
        try {
            // Check all
            if(e.target.id === 'check-all-siswa') {
                document.querySelectorAll('.check-siswa').forEach(cb => cb.checked = e.target.checked);
                updateBulkSiswa();
            }
            
            // Check individual
            if(e.target.classList && e.target.classList.contains('check-siswa')) {
                updateBulkSiswa();
            }

            // Print
            let printBtn = e.target.closest('.btn-print-card');
            if(printBtn) {
                let data = JSON.parse(printBtn.getAttribute('data-siswa'));
                printSingleCard(data.id);
            }

            // Mutasi
            let mutasiBtn = e.target.closest('.btn-mutasi');
            if(mutasiBtn) {
                let data = JSON.parse(mutasiBtn.getAttribute('data-siswa'));
                openMutasiModal(data.id, data.nama);
            }

            // View Photo
            let photoBtn = e.target.closest('.btn-view-photo');
            if(photoBtn) {
                let data = JSON.parse(photoBtn.getAttribute('data-siswa'));
                viewPhoto(data.url, data.nama, true);
            }
            
            // Quick Upload
            let uploadBtn = e.target.closest('.btn-quick-upload');
            if(uploadBtn) {
                let data = JSON.parse(uploadBtn.getAttribute('data-siswa'));
                quickUpload(data.id, data.nama);
            }
        } catch(err) {
            console.error("Action error:", err);
            alert("Error Detail: " + err.message);
        }
    });

    function confirmBulkActionSiswa(action) {
        let form = document.getElementById('form-bulk-siswa');
        if(action === 'delete') {
            showAppModal({
                title: 'Konfirmasi Hapus Massal',
                message: 'Apakah Anda yakin ingin menghapus <b>semua siswa terpilih</b> secara permanen? Data tidak dapat dikembalikan!',
                type: 'warning',
                buttons: [
                    { text: 'Batal', class: 'btn btn-secondary' },
                    { text: '<i data-lucide="trash-2"></i> Ya, Hapus Semua', class: 'btn btn-danger', onClick: () => {
                        form.action = '/siakad/siswa/delete-bulk';
                        form.submit();
                    }}
                ]
            });
        } else if(action === 'print') {
            currentPrintId = 'bulk';
            document.getElementById('modal-print-options').style.display = 'flex';
            lucide.createIcons();
        } else if(action === 'print_v') {
            form.action = '/siakad/siswa/cetak-kartu-bulk?mode=vertical';
            form.target = '_blank';
            form.submit();
        } else if(action === 'print_h') {
            form.action = '/siakad/siswa/cetak-kartu-bulk?mode=horizontal';
            form.target = '_blank';
            form.submit();
        } else if(action === 'reset-password') {
            showAppModal({
                title: 'Konfirmasi Reset Password',
                message: 'Reset username dan password untuk siswa terpilih menjadi <b>NIK</b> mereka masing-masing?',
                type: 'warning',
                buttons: [
                    { text: 'Batal', class: 'btn btn-secondary' },
                    { text: '<i data-lucide="refresh-cw"></i> Ya, Reset', class: 'btn btn-primary', onClick: () => {
                        form.action = '/siakad/siswa/reset-password-bulk';
                        form.target = '';
                        form.submit();
                    }}
                ]
            });
        }
    }

    function viewPhoto(src, name, fromAction = false) {
        document.getElementById('view-photo-img').src = src;
        document.getElementById('view-photo-name').innerText = name;
        document.getElementById('modal-view-photo').style.display = 'flex';
    }

    function quickUpload(id, name) {
        document.getElementById('quick-auto-siswa-id').value = id;
        document.getElementById('quick-auto-input').click();
    }

    function handleAutoUpload(input) {
        if (input.files && input.files[0]) {
            let id = document.getElementById('quick-auto-siswa-id').value;
            let formData = new FormData();
            formData.append('foto', input.files[0]);
            formData.append('id', id);

            let imgEl = document.getElementById('img-siswa-' + id);
            if(imgEl) {
                imgEl.style.opacity = '0.5';
                imgEl.style.transition = '0.3s';
            }

            fetch('/siakad/siswa/update-foto', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (imgEl) {
                        const imgName = data.thumb ? data.thumb : data.foto;
                        imgEl.src = '/uploads/siswa/' + imgName + '?t=' + new Date().getTime();
                        imgEl.style.opacity = '1';
                    }
                    input.value = ''; // Reset input so same file can be uploaded again if needed
                } else {
                    if (imgEl) imgEl.style.opacity = '1';
                    alert('Gagal upload foto: ' + data.message);
                }
            })
            .catch(error => {
                if (imgEl) imgEl.style.opacity = '1';
                console.error('Error:', error);
                alert('Terjadi kesalahan saat upload.');
            });
        }
    }

    function openAddSiswaModal() {
        const form = document.getElementById('sf-form');
        form.reset();
        form.action = '/siakad/siswa/save';
        document.getElementById('modal-add-siswa').style.display = 'flex';
        switchTab('tab-identitas', document.querySelector('.z-tab-btn'));
        lucide.createIcons();
    }

    function editSiswa(data) {
        const form = document.getElementById('sf-form');
        form.action = '/siakad/siswa/update';
        
        // Map data to inputs
        for (let key in data) {
            let input = form.querySelector(`[name="${key}"]`);
            if (input && input.type !== 'file') {
                input.value = data[key] || '';
            }
        }
        
        document.getElementById('modal-add-siswa').style.display = 'flex';
        switchTab('tab-identitas', document.querySelector('.z-tab-btn'));
        lucide.createIcons();
    }

    function showStudentCard(s) {
        document.getElementById('sc-photo').src = s.foto ? '/uploads/siswa/' + s.foto : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(s.nama) + '&background=065f46&color=fff';
        
        let scName = document.getElementById('sc-name');
        scName.innerText = s.nama;
        scName.style.fontSize = '8px'; // reset
        
        document.getElementById('sc-nisn').innerText = s.nis + (s.nisn ? ' / ' + s.nisn : '');
        
        let ttl = '-';
        if (s.tempat_lahir || s.tanggal_lahir) {
            ttl = (s.tempat_lahir ? s.tempat_lahir + ', ' : '') + (s.tanggal_lahir || '');
        }
        document.getElementById('sc-ttl').innerText = ttl;
        

        
        // Generate QR Belakang
        let qrBox = document.getElementById('sc-qr');
        qrBox.innerHTML = '';
        let txtSiswa = s.qr_token || 'INVALID_TOKEN';
        new QRCode(qrBox, { text: txtSiswa, width: 128, height: 128, colorDark: "#0f172a", colorLight: "#ffffff", correctLevel: QRCode.CorrectLevel.M });
        
        // Generate QR TTD
        let qrTtdBox = document.getElementById('sc-ttd-qr');
        qrTtdBox.removeAttribute('src');
        let ttdWrapper = document.createElement('div');
        let tglCetak = new Date().toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'});
        new QRCode(ttdWrapper, { text: "Telah disahkan pada " + tglCetak, width: 128, height: 128, colorDark: "#475569", colorLight: "#ffffff", correctLevel: QRCode.CorrectLevel.L });
        setTimeout(() => { 
            let canvas = ttdWrapper.querySelector('canvas');
            if (canvas) qrTtdBox.src = canvas.toDataURL(); 
        }, 50);

        document.getElementById('modal-student-card').style.display = 'flex';
        
        // Auto-fit name
        setTimeout(() => {
            let parent = scName.parentElement;
            let fontSize = 8;
            while(scName.scrollWidth > parent.clientWidth && fontSize > 4.5) {
                fontSize -= 0.2;
                scName.style.fontSize = fontSize + 'px';
            }
        }, 100);
    }

    let currentPrintId = null;
    function printSingleCard(id) {
        currentPrintId = id;
        document.getElementById('modal-print-options').style.display = 'flex';
        lucide.createIcons();
    }

    function executePrintCard(mode) {
        document.getElementById('modal-print-options').style.display = 'none';
        
        if (currentPrintId === 'bulk') {
            let form = document.getElementById('form-bulk-siswa');
            form.action = '/siakad/siswa/cetak-kartu-bulk?mode=' + mode;
            form.target = '_blank';
            form.submit();
        } else {
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '/siakad/siswa/cetak-kartu-bulk?mode=' + mode;
            form.target = '_blank';
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = currentPrintId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
            form.remove();
        }
    }

    function openMutasiModal(id, nama) {
        document.getElementById('mutasi-siswa-id').value = id;
        document.getElementById('mutasi-nama-siswa').textContent = nama;
        document.getElementById('modal-mutasi').style.display = 'flex';
    }

    function submitMutasi() {
        const formData = new FormData(document.getElementById('form-mutasi'));
        
        fetch('/admin/siakad/mutasi/keluar', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                alert('Siswa berhasil dimutasi keluar!');
                location.reload();
            } else {
                alert('Gagal: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        });
    }

    // Initialize DataTable
    $(document).ready(function() {
        if($('#table-data-siswa').length) {
            var table = $('#table-data-siswa').DataTable({
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                "language": {
                    "search": "Cari Cepat:",
                    "lengthMenu": "Tampilkan _MENU_ siswa",
                    "info": "Menampilkan _START_ s/d _END_ dari _TOTAL_ siswa",
                    "paginate": { "next": "Next", "previous": "Prev" }
                },
                "columnDefs": [ { "orderable": false, "targets": [0, 1, 6] } ],
                "drawCallback": function() { lucide.createIcons(); }
            });

            // Pindahkan kontrol DataTables ke atas sebelah filter kelas
            $('.dataTables_length, .dataTables_filter').appendTo('#dt-custom-controls');
        }
    });

    // AJAX Upload Masal
    async function startAjaxUpload() {
        const input = document.getElementById('foto_masal_input');
        const files = input.files;
        if (files.length === 0) {
            alert('Pilih file terlebih dahulu!');
            return;
        }

        const btnStart = document.getElementById('btn-start-upload');
        const btnCancel = document.getElementById('btn-cancel-upload');
        const progressContainer = document.getElementById('upload-progress-container');
        const progressBar = document.getElementById('upload-progress-bar');
        const percentageText = document.getElementById('upload-percentage');
        const statusText = document.getElementById('upload-status-text');
        const logArea = document.getElementById('upload-log');

        btnStart.disabled = true;
        btnStart.innerHTML = '<i data-lucide="loader" class="spin"></i> Mengupload...';
        btnCancel.disabled = true;
        input.disabled = true;
        
        progressContainer.style.display = 'block';
        logArea.innerHTML = '';
        
        let successCount = 0;
        let failedCount = 0;
        const total = files.length;

        for (let i = 0; i < total; i++) {
            const file = files[i];
            const formData = new FormData();
            formData.append('foto', file);

            statusText.textContent = `Mengupload ${i + 1} dari ${total}...`;
            
            try {
                const response = await fetch('/siakad/siswa/upload-foto-masal-ajax', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    successCount++;
                    logArea.innerHTML += `<div style="color: #10b981;">✓ ${result.file}: ${result.message}</div>`;
                } else {
                    failedCount++;
                    logArea.innerHTML += `<div style="color: #ef4444;">✗ ${result.file}: ${result.message}</div>`;
                }
            } catch (error) {
                failedCount++;
                logArea.innerHTML += `<div style="color: #ef4444;">✗ ${file.name}: Gagal koneksi ke server</div>`;
            }
            
            const percent = Math.round(((i + 1) / total) * 100);
            progressBar.style.width = percent + '%';
            percentageText.textContent = percent + '%';
            
            logArea.scrollTop = logArea.scrollHeight;
        }

        statusText.textContent = 'Upload Selesai!';
        btnStart.innerHTML = '<i data-lucide="check"></i> Selesai';
        
        // Redirect dengan parameter sukses/gagal agar notifikasi muncul
        setTimeout(() => {
            window.location.href = `/siakad/siswa?upload_sukses=${successCount}&upload_gagal=${failedCount}`;
        }, 1500);
    }

    // Initialize Lucide
    lucide.createIcons();
</script>
