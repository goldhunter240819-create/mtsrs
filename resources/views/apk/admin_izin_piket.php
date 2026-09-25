<style>
    .tabs {
        display: flex;
        background: #f1f5f9;
        padding: 5px;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    .tab {
        flex: 1;
        text-align: center;
        padding: 10px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #64748b;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .tab.active {
        background: white;
        color: #2563eb;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .izin-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
    }
    .izin-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
    .badge-pending { background: #fffbeb; color: #d97706; padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 700; }
    .badge-approved { background: #ecfdf5; color: #059669; padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 700; }
    .badge-rejected { background: #fef2f2; color: #ef4444; padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 700; }
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        font-size: 0.95rem;
        color: #1e293b;
        outline: none;
        transition: all 0.2s ease;
        appearance: none;
    }
    .form-control:focus {
        border-color: #2563eb;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    .select-wrapper {
        position: relative;
    }
    .select-wrapper::after {
        content: '\25BC';
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        font-size: 0.7rem;
        color: #64748b;
        pointer-events: none;
    .btn-primary {
        display: block;
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        text-align: center;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        text-decoration: none;
        border: none;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        transition: all 0.2s ease;
    }
    .btn-primary:active { transform: scale(0.98); }
    
    .btn-danger {
        display: block;
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        text-align: center;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        text-decoration: none;
        border: none;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        transition: all 0.2s ease;
    }
    .btn-danger:active { transform: scale(0.98); }
</style>

<!-- Header -->
<div class="apk-vector-header" style="padding-bottom: 25px; background-color: #2563eb;">
    <div class="apk-top-logo">
        <a href="/apk/aplikasi-saya" style="color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 12px; margin-right: 15px;">
            <i data-lucide="arrow-left"></i>
        </a>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Admin Izin & Inval</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Izin Guru</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0;">
    <div class="tabs">
        <div class="tab active" id="tabPersetujuan" onclick="switchTabIzin(this, 'persetujuan')">
            Persetujuan
            <?php if (!empty($pendingCount)): ?>
                <span style="background: #ef4444; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.65rem; margin-left: 5px;"><?php echo $pendingCount; ?></span>
            <?php endif; ?>
        </div>
        <div class="tab" id="tabInval" onclick="switchTabIzin(this, 'inval')">Tugas Inval</div>
    </div>

    <!-- TAB PERSETUJUAN -->
    <div id="contentPersetujuan">
        <?php if ($isRiwayat): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <a href="?riwayat=0" class="btn btn-primary btn-sm" style="background: white; color: #2563eb; border: 1px solid #bfdbfe; padding: 6px 12px; font-size: 0.8rem; text-decoration: none; border-radius: 8px; font-weight: 700; box-shadow: none;">&larr; Kembali</a>
                <form method="GET" action="" style="display: flex; align-items: center;">
                    <input type="hidden" name="riwayat" value="1">
                    <input type="month" name="bulan" value="<?php echo htmlspecialchars($bulanRiwayat); ?>" onchange="this.form.submit()" style="padding: 4px 8px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.8rem; outline: none;">
                </form>
            </div>
            <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 15px;">Riwayat Persetujuan (<?php echo date('M Y', strtotime($bulanRiwayat . '-01')); ?>)</h3>
        <?php else: ?>
            <div style="display: flex; justify-content: flex-end; margin-bottom: 15px;">
                <a href="?riwayat=1" style="display: inline-flex; align-items: center; gap: 5px; font-size: 0.8rem; font-weight: 700; color: #64748b; background: #f1f5f9; padding: 6px 12px; border-radius: 20px; text-decoration: none;">
                    <i data-lucide="history" style="width: 14px; height: 14px;"></i> Lihat Riwayat
                </a>
            </div>
        <?php endif; ?>

        <?php if (empty($pengajuanList)): ?>
            <div style="text-align: center; padding: 40px 20px; background: white; border-radius: 16px; border: 1px solid #f1f5f9;">
                <div style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                    <i data-lucide="inbox" style="width: 28px; height: 28px; color: #94a3b8;"></i>
                </div>
                <div style="font-weight: 700; color: #475569; font-size: 1rem;">Belum ada pengajuan izin</div>
                <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 5px;">Daftar pengajuan izin guru akan muncul di sini.</div>
            </div>
        <?php else: ?>
            <div style="padding-bottom: 20px;">
                <?php 
                $lastDate = '';
                foreach ($pengajuanList as $p): 
                    $currentDate = $p['tanggal'];
                    if ($currentDate !== $lastDate) {
                        $hariArr = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        $dateStr = $hariArr[date('w', strtotime($currentDate))] . ', ' . date('d M Y', strtotime($currentDate));
                        echo '<div style="margin: 20px 0 10px 0; font-size: 0.85rem; font-weight: 800; color: #475569; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px;">' . $dateStr . '</div>';
                        $lastDate = $currentDate;
                    }
                    $statusClass = 'badge-pending';
                    if ($p['status_approval'] === 'Disetujui') $statusClass = 'badge-approved';
                    elseif ($p['status_approval'] === 'Ditolak') $statusClass = 'badge-rejected';
                    $tugasData = json_decode($p['tugas_inval'], true) ?: [];
                ?>
                    <div class="izin-card">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                            <div>
                                <div style="font-weight: 800; font-size: 1rem; color: #0f172a; margin-bottom: 2px;"><?php echo htmlspecialchars($p['nama_guru']); ?></div>
                                <div style="font-size: 0.8rem; color: #64748b;">
                                    <strong><?php echo htmlspecialchars($p['jenis_izin']); ?></strong>
                                </div>
                            </div>
                            <span class="<?php echo $statusClass; ?>"><?php echo $p['status_approval']; ?></span>
                        </div>

                        <?php if (!empty($tugasData)): ?>
                            <div style="margin-bottom: 12px; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;">
                                <div onclick="toggleTugasPersetujuan(this)" style="padding: 10px 12px; background: #f8fafc; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                                    <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Lihat Tugas yang Ditinggalkan (<?php echo count($tugasData); ?>)</div>
                                    <i data-lucide="chevron-down" style="width: 16px; height: 16px; color: #64748b; transition: transform 0.3s;" class="tugas-chevron"></i>
                                </div>
                                <div class="tugas-body" style="display: none; padding: 10px 12px; background: white; border-top: 1px solid #e2e8f0;">
                                    <?php foreach ($tugasData as $t): ?>
                                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 12px; margin-bottom: 6px; font-size: 0.85rem;">
                                            <strong style="color: #1e293b;"><?php echo htmlspecialchars($t['nama_kelas'] ?? 'Kelas'); ?></strong>
                                            <span style="color: #64748b;"> &mdash; <?php echo htmlspecialchars($t['nama_mapel'] ?? 'Mapel'); ?></span>
                                            <div style="margin-top: 5px; color: #475569; font-style: italic;">📝 <?php echo htmlspecialchars($t['materi'] ?? '-'); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($p['status_approval'] === 'Pending'): ?>
                            <div style="display: flex; gap: 8px;">
                                <form method="POST" action="<?php echo \App\Core\Helper::url('/apk/approve-izin'); ?>" style="flex: 1;" onsubmit="confirmAction(event, 'Setujui izin ini?')">
                                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 6px; border: none; cursor: pointer; padding: 10px;">
                                        <i data-lucide="check" style="width:16px;height:16px;"></i> Setujui
                                    </button>
                                </form>
                                <form method="POST" action="<?php echo \App\Core\Helper::url('/apk/approve-izin'); ?>" style="flex: 1;" onsubmit="confirmAction(event, 'Tolak izin ini?')">
                                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 6px; border: none; cursor: pointer; padding: 10px;">
                                        <i data-lucide="x" style="width:16px;height:16px;"></i> Tolak
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB TUGAS INVAL -->
    <div id="contentInval" style="display: none;">
        <!-- Laporkan Guru Alpa -->
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 16px; padding: 20px; margin-bottom: 20px;">
            <div style="font-weight: 800; font-size: 0.9rem; color: #b91c1c; margin-bottom: 12px;">
                <i data-lucide="alert-triangle" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:4px;"></i> Laporkan Guru Alpa (Tanpa Izin)
            </div>
            <form method="POST" action="<?php echo \App\Core\Helper::url('/apk/lapor-alpa'); ?>" style="display: flex; gap: 8px; align-items: stretch;" onsubmit="confirmAction(event, 'Laporkan guru ini sebagai Alpa hari ini?')">
                <div class="select-wrapper" style="flex: 1;">
                    <select name="guru_id" required class="form-control">
                        <option value="">Pilih Guru...</option>
                        <?php foreach ($guruList as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" style="background: #ef4444; color: white; padding: 0 20px; border-radius: 8px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px; border: none; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2);">
                    <i data-lucide="alert-circle" style="width:16px;height:16px;"></i> Lapor
                </button>
            </form>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0;">
                <i data-lucide="book-open" style="width:18px;height:18px;display:inline-block;vertical-align:middle;margin-right:6px;color:#2563eb;"></i>
                Tugas Kelas (Inval)
            </h3>
            <div style="display: flex; align-items: center; background: white; border-radius: 8px; overflow: hidden; border: 1px solid #cbd5e1;">
                <input type="date" id="tanggalInvalFilter" name="tanggal_inval" value="<?php echo htmlspecialchars($tanggal_inval ?? date('Y-m-d')); ?>" onchange="loadInvalByDate(this)" style="padding: 4px 8px; border: none; font-size: 0.8rem; outline: none; font-weight: 600; color: #475569;">
            </div>
        </div>

        <div id="invalListContainer">
            <?php include __DIR__ . '/admin_izin_piket_inval_list.php'; ?>
        </div>
    </div>

    <script>
    function loadInvalByDate(inputElem) {
        const dateVal = inputElem.value;
        const container = document.getElementById('invalListContainer');
        
        container.innerHTML = '<div style="text-align:center; padding: 20px;"><i data-lucide="loader-2" class="lucide-spin" style="width:24px;height:24px;color:#94a3b8;animation: spin 1s linear infinite;"></i><div style="font-size:0.8rem;color:#94a3b8;margin-top:10px;">Memuat data...</div></div>';
        if (typeof lucide !== 'undefined') lucide.createIcons();

        fetch(`?ajax=inval&tanggal_inval=${dateVal}`)
            .then(res => res.text())
            .then(html => {
                container.innerHTML = html;
                if (typeof lucide !== 'undefined') lucide.createIcons();
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = '<div style="text-align:center; color:#ef4444; padding:20px;">Gagal memuat data.</div>';
            });
    }

    function toggleTugasPersetujuan(headerElement) {
        const body = headerElement.nextElementSibling;
        const chevron = headerElement.querySelector('.tugas-chevron');
        if (body.style.display === 'none' || body.style.display === '') {
            body.style.display = 'block';
            chevron.style.transform = 'rotate(180deg)';
        } else {
            body.style.display = 'none';
            chevron.style.transform = 'rotate(0deg)';
        }
    }

    function toggleInvalGuru(headerElement) {
        const body = headerElement.nextElementSibling;
        const chevron = headerElement.querySelector('.inval-chevron');
        if (body.style.display === 'none' || body.style.display === '') {
            body.style.display = 'block';
            chevron.style.transform = 'rotate(180deg)';
        } else {
            body.style.display = 'none';
            chevron.style.transform = 'rotate(0deg)';
        }
    }
    </script>
    <style>
    @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>
    </div>
</div>

<script>
function switchTabIzin(btn, tabId) {
    document.getElementById('contentPersetujuan').style.display = tabId === 'persetujuan' ? 'block' : 'none';
    document.getElementById('contentInval').style.display = tabId === 'inval' ? 'block' : 'none';
    document.querySelectorAll('.tabs .tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function confirmAction(e, message) {
    e.preventDefault();
    const form = e.target.closest('form');
    Swal.fire({
        title: 'Konfirmasi',
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'z-card'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
</script>
