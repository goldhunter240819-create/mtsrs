<style>
    .z-avatar-premium { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .header-breadcrumb .parent { color: #f59e0b; }
    
    .absen-row { border-bottom: 1px solid #f1f5f9; transition: 0.15s; }
    .absen-row:hover { background: #fafafa; }
    
    select.status-sel {
        border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 6px 10px;
        font-size: 0.8rem; font-weight: 700; outline: none;
        appearance: none; cursor: pointer; transition: all 0.2s ease;
    }
    select.status-sel.s-hadir    { border-color: #86efac; background: #f0fdf4; color: #15803d; }
    select.status-sel.s-terlambat{ border-color: #fcd34d; background: #fefce8; color: #92400e; }
    select.status-sel.s-sakit    { border-color: #93c5fd; background: #eff6ff; color: #1d4ed8; }
    select.status-sel.s-izin     { border-color: #c4b5fd; background: #f5f3ff; color: #7c3aed; }
    select.status-sel.s-alpa     { border-color: #fca5a5; background: #fef2f2; color: #dc2626; }
    
    input[type=time] {
        border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 6px 10px;
        font-size: 0.8rem; font-weight: 700; outline: none; color: #0f172a;
        width: 100px; transition: all 0.2s ease;
    }

    /* Aksi Kolektif Toolbar */
    .kolektif-toolbar {
        padding: 0.85rem 1.25rem;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .kt-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .kt-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .kt-buttons {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .btn-kt {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
        border: 1.5px solid transparent;
    }
    .btn-kt:hover {
        transform: translateY(-1px);
    }
    .btn-kt:active {
        transform: translateY(0);
    }
    .btn-kt-hadir {
        background: #ecfdf5;
        color: #047857;
        border-color: #a7f3d0;
    }
    .btn-kt-hadir:hover {
        background: #059669;
        color: #ffffff;
        border-color: #059669;
        box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25);
    }
    .btn-kt-random {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }
    .btn-kt-random:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }
    .btn-kt-combo {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: #ffffff;
        border: none;
        box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25);
    }
    .btn-kt-combo:hover {
        background: linear-gradient(135deg, #047857 0%, #064e3b 100%);
        box-shadow: 0 4px 10px rgba(5, 150, 105, 0.35);
    }
    .kt-right {
        font-size: 0.74rem;
        color: #64748b;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Tombol Aksi Cepat pada Header Kolom Tabel */
    .btn-th-kolektif {
        font-size: 0.65rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s ease;
        text-transform: none;
        letter-spacing: normal;
        border: 1px solid transparent;
    }
    .btn-th-kolektif:hover {
        transform: translateY(-1px);
    }
    .btn-th-hadir {
        background: #ecfdf5;
        color: #047857;
        border-color: #86efac;
    }
    .btn-th-hadir:hover {
        background: #059669;
        color: #ffffff;
        border-color: #059669;
    }
    .btn-th-random {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #93c5fd;
    }
    .btn-th-random:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    /* Animasi Highlight Saat Waktu Diacak */
    @keyframes timeFlash {
        0% { background-color: #dcfce7; border-color: #10b981; transform: scale(1.06); }
        50% { background-color: #f0fdf4; border-color: #86efac; }
        100% { background-color: #ffffff; border-color: #e2e8f0; transform: scale(1); }
    }
    .time-updated {
        animation: timeFlash 0.5s ease-out;
    }

    /* Animasi Highlight Saat Status Diubah */
    @keyframes statusFlash {
        0% { transform: scale(1.04); }
        100% { transform: scale(1); }
    }
    .status-updated {
        animation: statusFlash 0.3s ease-out;
    }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" onclick="zToggleSidebar()" style="background:none;border:none;cursor:pointer;color:#64748b;"><i data-lucide="menu"></i></button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <span class="parent" style="font-weight:800;letter-spacing:1px;font-size:0.8rem;">ABSEN V2</span>
                    <i data-lucide="chevron-right" style="width:14px;color:#cbd5e1;"></i>
                    <span style="font-weight:700;color:#1e293b;">Input Manual Absensi Guru</span>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="z-avatar-premium" style="width:36px;height:36px;color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;"><?php echo $initial; ?></div>
                <div>
                    <div style="font-weight:800;font-size:0.9rem;color:#0f172a;"><?php echo $nama; ?></div>
                    <div style="font-size:0.75rem;color:#64748b;">Administrator</div>
                </div>
            </div>
        </header>

        <div class="z-scroll" style="padding: 1.5rem 2rem;">

            <?php if (isset($msg) && $msg === 'success'): ?>
            <div style="background:#dcfce7;border:1px solid #86efac;border-radius:12px;padding:1rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;color:#15803d;font-weight:700;">
                <i data-lucide="check-circle" style="width:18px;"></i> Data absensi guru berhasil disimpan!
            </div>
            <?php endif; ?>

            <!-- Pilih Tanggal -->
            <div style="background:white;border-radius:16px;padding:1.25rem;border:1px solid #f1f5f9;margin-bottom:1.5rem;display:flex;align-items:center;gap:12px;">
                <i data-lucide="calendar" style="color:#f59e0b;width:20px;"></i>
                <label style="font-weight:700;font-size:0.9rem;color:#0f172a;">Tanggal Absensi:</label>
                <input type="date" id="tanggalInput" value="<?php echo htmlspecialchars($tanggal); ?>"
                       style="border:1.5px solid #e2e8f0;border-radius:10px;padding:7px 12px;font-weight:700;color:#0f172a;font-size:0.85rem;outline:none;"
                       onchange="window.location.href='?tanggal='+this.value">
                <div style="margin-left:auto;font-size:0.8rem;color:#64748b;font-weight:600;">
                    <i data-lucide="info" style="width:14px;vertical-align:middle;"></i>
                    Ganti tanggal untuk memuat data hari berbeda.
                </div>
            </div>

            <form method="POST" action="/absen/input-absen-guru/save">
                <input type="hidden" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>">
                
                <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;overflow:hidden;">
                    <!-- Card Header -->
                    <div style="padding:1rem 1.5rem;background:linear-gradient(135deg,#fffbeb,#fef3c7);border-bottom:1px solid #fde68a;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <i data-lucide="clipboard-edit" style="color:#d97706;width:18px;"></i>
                            <h3 style="margin:0;font-size:0.9rem;font-weight:800;color:#92400e;">
                                Input Absensi Guru — <?php echo date('d F Y', strtotime($tanggal)); ?>
                            </h3>
                        </div>
                        <span style="font-size:0.75rem;color:#92400e;font-weight:700;background:rgba(255,255,255,0.7);padding:4px 10px;border-radius:20px;border:1px solid #fde68a;">
                            <?php echo count($guruList); ?> Guru Terdaftar
                        </span>
                    </div>

                    <!-- Toolbar Aksi Kolektif -->
                    <div class="kolektif-toolbar">
                        <div class="kt-left">
                            <div class="kt-badge">
                                <i data-lucide="zap" style="width:13px;height:13px;"></i>
                                <span>Aksi Kolektif</span>
                            </div>
                            <div class="kt-buttons">
                                <button type="button" class="btn-kt btn-kt-hadir" onclick="setKolektifStatus('Hadir')" title="Ubah status semua guru menjadi Hadir">
                                    <i data-lucide="check-check" style="width:14px;height:14px;"></i>
                                    <span>Hadirkan Semua Guru</span>
                                </button>
                                <button type="button" class="btn-kt btn-kt-random" onclick="acakJamMasuk(25, 30)" title="Isi jam masuk semua guru secara acak antara 07:25 s/d 07:30">
                                    <i data-lucide="shuffle" style="width:14px;height:14px;"></i>
                                    <span>Acak Jam (07:25 - 07:30)</span>
                                </button>
                                <button type="button" class="btn-kt btn-kt-combo" onclick="kolektifHadirDanAcak()" title="1-Klik: Set semua Hadir & acak jam masuk 07:25-07:30">
                                    <i data-lucide="sparkles" style="width:14px;height:14px;"></i>
                                    <span>1-Klik: Hadir Semua & Acak Jam</span>
                                </button>
                            </div>
                        </div>
                        <div class="kt-right">
                            <i data-lucide="shield-check" style="width:14px;height:14px;color:#059669;"></i>
                            <span style="color:#047857;">Aksi kolektif otomatis melindungi data: Terlambat, Sakit, Izin, Alpa & jam asli tidak ditimpa.</span>
                        </div>
                    </div>

                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8fafc;text-align:left;">
                                <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;width:40px;">No</th>
                                <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Nama Guru</th>
                                <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Jabatan</th>
                                <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;min-width:170px;">
                                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                                        <span>Status</span>
                                        <button type="button" onclick="setKolektifStatus('Hadir')" class="btn-th-kolektif btn-th-hadir" title="Klik untuk set Hadir (Abaikan Terlambat/Sakit/Izin/Alpa)">
                                            <i data-lucide="check-check" style="width:11px;height:11px;"></i> Semua Hadir
                                        </button>
                                    </div>
                                </th>
                                <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;min-width:180px;">
                                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                                        <span>Jam Masuk</span>
                                        <button type="button" onclick="acakJamMasuk(25, 30)" class="btn-th-kolektif btn-th-random" title="Klik untuk acak jam masuk yang masih kosong (07:25 - 07:30)">
                                            <i data-lucide="shuffle" style="width:11px;height:11px;"></i> Acak 07:25-07:30
                                        </button>
                                    </div>
                                </th>
                                <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach($guruList as $g): 
                                $ex = $existingAbsenByGuruId[$g['id']] ?? null;
                                $hasExisting = ($ex !== null);
                                $existingStatus = $ex ? $ex['status'] : '';
                                $existingJam = ($ex && !empty($ex['jam_masuk'])) ? substr($ex['jam_masuk'], 0, 5) : '';
                                $curStatus = $ex['status'] ?? 'Hadir';
                            ?>
                            <tr class="absen-row"
                                data-has-existing="<?php echo $hasExisting ? '1' : '0'; ?>"
                                data-initial-status="<?php echo htmlspecialchars($existingStatus); ?>"
                                data-initial-jam="<?php echo htmlspecialchars($existingJam); ?>">
                                <td style="padding:0.75rem 1rem;color:#94a3b8;font-size:0.8rem;"><?php echo $no++; ?></td>
                                <td style="padding:0.75rem 1rem;">
                                    <div style="font-weight:700;color:#0f172a;font-size:0.85rem;"><?php echo htmlspecialchars($g['nama']); ?></div>
                                </td>
                                <td style="padding:0.75rem 1rem;font-size:0.8rem;color:#64748b;"><?php echo htmlspecialchars($g['jabatan'] ?? '-'); ?></td>
                                <td style="padding:0.75rem 1rem;">
                                    <select name="absen[<?php echo $g['id']; ?>][status]"
                                            class="status-sel s-<?php echo strtolower($curStatus); ?>"
                                            onchange="updateStyle(this)">
                                        <option value="Hadir"     <?php echo $curStatus=='Hadir'?'selected':''; ?>>✅ Hadir</option>
                                        <option value="Terlambat" <?php echo $curStatus=='Terlambat'?'selected':''; ?>>⚠️ Terlambat</option>
                                        <option value="Sakit"     <?php echo $curStatus=='Sakit'?'selected':''; ?>>🤒 Sakit</option>
                                        <option value="Izin"      <?php echo $curStatus=='Izin'?'selected':''; ?>>📝 Izin</option>
                                        <option value="Alpa"      <?php echo $curStatus=='Alpa'?'selected':''; ?>>❌ Alpa</option>
                                    </select>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <input type="time" name="absen[<?php echo $g['id']; ?>][jam_masuk]"
                                           class="jam-masuk-input"
                                           value="<?php echo htmlspecialchars($existingJam); ?>">
                                </td>

                                <td style="padding:0.75rem 1rem;">
                                    <input type="text" name="absen[<?php echo $g['id']; ?>][keterangan]"
                                           value="<?php echo htmlspecialchars($ex['keterangan'] ?? ''); ?>"
                                           placeholder="Opsional..."
                                           style="border:1.5px solid #e2e8f0;border-radius:8px;padding:6px 10px;font-size:0.8rem;outline:none;width:130px;">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div style="padding:1.25rem;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                        <div style="font-size:0.8rem;color:#64748b;">
                            <i data-lucide="check-circle-2" style="width:14px;vertical-align:middle;color:#10b981;"></i>
                            Pastikan data sudah sesuai sebelum menekan tombol simpan.
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <a href="/absen" style="text-decoration:none;background:#f1f5f9;color:#475569;padding:10px 20px;border-radius:10px;font-weight:700;font-size:0.85rem;">Batal</a>
                            <button type="submit" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:white;border:none;padding:10px 24px;border-radius:10px;font-weight:700;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;gap:8px;box-shadow:0 2px 8px rgba(217,119,6,0.3);">
                                <i data-lucide="save" style="width:16px;"></i> Simpan Semua Data Absensi
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function updateStyle(el) {
    el.className = 'status-sel s-' + el.value.toLowerCase();
    const row = el.closest('tr');
    if (row) {
        const timeInput = row.querySelector('.jam-masuk-input');
        if (timeInput) {
            if (['Sakit', 'Izin', 'Alpa'].includes(el.value)) {
                timeInput.style.opacity = '0.4';
                timeInput.style.filter = 'grayscale(1)';
            } else {
                timeInput.style.opacity = '1';
                timeInput.style.filter = 'none';
            }
        }
    }
}

/**
 * Mengubah status guru menjadi Hadir secara kolektif
 * Menjaga data yang sudah ada (Terlambat, Sakit, Izin, Alpa) agar TIDAK tertimpa
 */
function setKolektifStatus(status = 'Hadir', showToast = true) {
    let updatedCount = 0;
    let skippedCount = 0;
    const protectedStatuses = ['Terlambat', 'Sakit', 'Izin', 'Alpa'];

    const rows = document.querySelectorAll('tr.absen-row');
    rows.forEach(row => {
        const sel = row.querySelector('select.status-sel');
        if (!sel) return;

        const currentVal = sel.value;
        const initialStatus = row.getAttribute('data-initial-status') || '';

        // JANGAN TIMPA jika status saat ini atau data awal dari DB adalah Terlambat, Sakit, Izin, atau Alpa
        if (protectedStatuses.includes(currentVal) || protectedStatuses.includes(initialStatus)) {
            skippedCount++;
            return;
        }

        sel.value = status;
        updateStyle(sel);
        sel.classList.add('status-updated');
        setTimeout(() => sel.classList.remove('status-updated'), 300);
        updatedCount++;
    });

    if (showToast && typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Status Diperbarui!',
            html: `<b>${updatedCount} guru</b> diset Hadir.<br><small style="color:#64748b;">${skippedCount} guru (Terlambat / Sakit / Izin / Alpa) aman tidak ditimpa.</small>`,
            timer: 2300,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }

    return { updatedCount, skippedCount };
}

/**
 * Mengacak jam masuk antara 07:25 sampai 07:30
 * Hanya untuk guru yang HADIR dan jam masuknya BELUM TERISI (jangan timpa yang sudah ada)
 */
function acakJamMasuk(startMin = 25, endMin = 30, showToast = true) {
    let updatedCount = 0;
    let skippedCount = 0;
    const protectedStatuses = ['Terlambat', 'Sakit', 'Izin', 'Alpa'];

    const rows = document.querySelectorAll('tr.absen-row');
    rows.forEach(row => {
        const sel = row.querySelector('select.status-sel');
        const input = row.querySelector('.jam-masuk-input');
        if (!input) return;

        const currentStatus = sel ? sel.value : '';
        const initialStatus = row.getAttribute('data-initial-status') || '';
        const initialJam = row.getAttribute('data-initial-jam') || '';

        // 1. Abaikan jika status bukan Hadir (misal Terlambat, Sakit, Izin, Alpa)
        if (protectedStatuses.includes(currentStatus) || protectedStatuses.includes(initialStatus)) {
            skippedCount++;
            return;
        }

        // 2. Abaikan jika jam masuk SUDAH TERISI sebelumnya (data asli jangan ditimpa)
        if (initialJam !== '' || (input.value && input.value !== '' && input.value !== '--:--')) {
            skippedCount++;
            return;
        }

        // 3. Random menit antara startMin (25) sampai endMin (30)
        const minute = Math.floor(Math.random() * (endMin - startMin + 1)) + startMin;
        const timeStr = '07:' + String(minute).padStart(2, '0');
        input.value = timeStr;
        
        // Animasi highlight saat jam berubah
        input.classList.remove('time-updated');
        void input.offsetWidth; // trigger reflow
        input.classList.add('time-updated');
        updatedCount++;
    });

    if (showToast && typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Jam Masuk Diacak!',
            html: `<b>${updatedCount} guru</b> diisi jam acak (07:25 - 07:30).<br><small style="color:#64748b;">${skippedCount} guru (yang sudah punya jam / Terlambat / Izin / Sakit / Alpa) aman tidak ditimpa.</small>`,
            timer: 2500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }

    return { updatedCount, skippedCount };
}

/**
 * 1-Klik Combo: Hadirkan semua guru & acak jam masuk yang masih kosong (07:25 - 07:30)
 * Data yang sudah ada (Terlambat, Sakit, Izin, Alpa, & Jam Terisi) dijamin tidak akan tertimpa
 */
function kolektifHadirDanAcak() {
    const resStatus = setKolektifStatus('Hadir', false);
    const resJam = acakJamMasuk(25, 30, false);

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Aksi Kolektif Berhasil!',
            html: `<b>${resStatus.updatedCount} guru</b> diset Hadir & <b>${resJam.updatedCount} guru</b> diisi jam acak (07:25 - 07:30).<br><small style="color:#047857;font-weight:700;">Data Terlambat, Sakit, Izin, Alpa & jam yang sudah ada aman tidak ditimpa!</small>`,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
}

document.addEventListener("DOMContentLoaded", function () {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    // Inisialisasi style awal semua select
    document.querySelectorAll('select.status-sel').forEach(sel => {
        updateStyle(sel);
    });
});
</script>
