<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()" style="background:none;border:none;cursor:pointer;color:#64748b;">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <span class="parent" style="font-weight:800;letter-spacing:1px;font-size:0.8rem;">ABSEN V2</span>
                    <i data-lucide="chevron-right" class="sep" style="width:14px;color:#cbd5e1;"></i>
                    <span class="current" style="font-weight:700;color:#1e293b;">Izin Guru</span>
                </div>
            </div>
            <div class="z-header-right" style="display:flex;align-items:center;gap:2rem;">
                <div class="z-user-info" style="text-align:right;">
                    <div class="name" style="font-weight:800;color:#0f172a;font-size:0.95rem;"><?php echo htmlspecialchars($nama ?? 'Admin'); ?></div>
                    <div class="role" style="font-size:0.75rem;color:#64748b;font-weight:600;">Guru Piket</div>
                </div>
                <div class="z-avatar-premium" style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg, #10b981 0%, #059669 100%);color:white;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.1rem;box-shadow:0 4px 10px rgba(16,185,129,0.3);">
                    <?php echo htmlspecialchars($initial ?? 'A'); ?>
                </div>
            </div>
        </header>

        <div class="z-content">

            <div class="modern-page-header" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <div>
                    <h1 class="mph-title" style="color: white;">
                        <i data-lucide="clipboard-check" style="color: rgba(255,255,255,0.8);"></i> Izin Guru & Inval Piket
                    </h1>
                    <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">Kelola pengajuan izin guru dan tugas inval kelas kosong.</p>
                </div>
            </div>

<style>
.tab-izin { display: flex; gap: 0; margin-bottom: 1.5rem; background: #f1f5f9; border-radius: 12px; padding: 4px; }
.tab-izin .tab-btn { flex: 1; padding: 10px 16px; border: none; background: transparent; font-weight: 700; font-size: 0.85rem; color: #64748b; cursor: pointer; border-radius: 10px; transition: 0.2s; }
.tab-izin .tab-btn.active { background: white; color: #0f172a; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.izin-card { background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 1rem; transition: 0.2s; }
.izin-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
.badge-pending { background: #fffbeb; color: #d97706; padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 700; }
.badge-approved { background: #ecfdf5; color: #059669; padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 700; }
.badge-rejected { background: #fef2f2; color: #ef4444; padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 700; }
</style>

<div class="z-card" style="margin: 2rem auto; max-width: 900px; padding: 1.5rem;">
    <!-- Tabs -->
    <div class="tab-izin">
        <button class="tab-btn active" onclick="switchTabIzin('persetujuan')">
            <i data-lucide="check-circle" style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:4px;"></i> Persetujuan
            <?php if (!empty($pendingCount)): ?>
                <span style="background: #ef4444; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.65rem; margin-left: 5px;"><?php echo $pendingCount; ?></span>
            <?php endif; ?>
        </button>
        <button class="tab-btn" onclick="switchTabIzin('inval')">
            <i data-lucide="users" style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:4px;"></i> Tugas Inval
        </button>
    </div>

    <!-- Tab Persetujuan -->
    <div id="tabPersetujuan">
        <?php if (empty($pengajuanList)): ?>
            <div style="text-align: center; padding: 40px 20px;">
                <i data-lucide="inbox" style="width: 48px; height: 48px; color: #cbd5e1; margin-bottom: 10px;"></i>
                <div style="font-weight: 700; color: #475569; font-size: 1rem;">Belum ada pengajuan izin</div>
                <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 5px;">Daftar pengajuan izin guru akan muncul di sini.</div>
            </div>
        <?php else: ?>
            <?php foreach ($pengajuanList as $p): 
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
                                <i data-lucide="calendar" style="width:13px;height:13px;display:inline-block;vertical-align:middle;margin-right:3px;"></i>
                                <?php 
                                    $hariArr = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                    echo $hariArr[date('w', strtotime($p['tanggal']))] . ', ' . date('d M Y', strtotime($p['tanggal'])); 
                                ?> &bull; <strong><?php echo htmlspecialchars($p['jenis_izin']); ?></strong>
                            </div>
                        </div>
                        <span class="<?php echo $statusClass; ?>"><?php echo $p['status_approval']; ?></span>
                    </div>

                    <?php if (!empty($tugasData)): ?>
                        <div style="margin-bottom: 12px;">
                            <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Tugas yang Ditinggalkan:</div>
                            <?php foreach ($tugasData as $t): ?>
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 12px; margin-bottom: 6px; font-size: 0.85rem;">
                                    <strong style="color: #1e293b;"><?php echo htmlspecialchars($t['nama_kelas'] ?? 'Kelas'); ?></strong>
                                    <span style="color: #64748b;"> — <?php echo htmlspecialchars($t['nama_mapel'] ?? 'Mapel'); ?></span>
                                    <div style="margin-top: 5px; color: #475569; font-style: italic;">📝 <?php echo htmlspecialchars($t['materi'] ?? '-'); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($p['status_approval'] === 'Pending'): ?>
                        <div style="display: flex; gap: 8px;">
                            <form method="POST" action="<?php echo Helper::url('/absen/approve-izin'); ?>" style="flex: 1;" onsubmit="return confirm('Setujui izin ini?')">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                    <i data-lucide="check" style="width:16px;height:16px;"></i> Setujui
                                </button>
                            </form>
                            <form method="POST" action="<?php echo Helper::url('/absen/approve-izin'); ?>" style="flex: 1;" onsubmit="return confirm('Tolak izin ini?')">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-danger" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                    <i data-lucide="x" style="width:16px;height:16px;"></i> Tolak
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Tab Tugas Inval -->
    <div id="tabInval" style="display: none;">
        <!-- Laporkan Guru Alpa -->
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 14px; padding: 1rem; margin-bottom: 1.5rem;">
            <div style="font-weight: 800; font-size: 0.85rem; color: #b91c1c; margin-bottom: 10px;">
                <i data-lucide="alert-triangle" style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:4px;"></i> Laporkan Guru Alpa (Tidak Masuk Tanpa Izin)
            </div>
            <form method="POST" action="<?php echo Helper::url('/absen/lapor-alpa'); ?>" style="display: flex; gap: 8px; align-items: flex-end;">
                <div style="flex: 1;">
                    <select name="guru_id" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <option value="">Pilih Guru...</option>
                        <?php foreach ($guruList as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-danger" style="padding: 10px 20px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px;" onclick="return confirm('Laporkan guru ini sebagai Alpa hari ini?')">
                    <i data-lucide="alert-circle" style="width:16px;height:16px;"></i> Laporkan
                </button>
            </form>
        </div>

        <!-- Tugas Inval Hari Ini -->
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 15px;">
            <i data-lucide="book-open" style="width:18px;height:18px;display:inline-block;vertical-align:middle;margin-right:6px;color:#f59e0b;"></i>
            Tugas Kelas (Inval) Hari Ini
        </h3>

        <?php if (empty($tugasInvalList)): ?>
            <div style="text-align: center; padding: 40px 20px;">
                <i data-lucide="book-check" style="width: 48px; height: 48px; color: #cbd5e1; margin-bottom: 10px;"></i>
                <div style="font-weight: 700; color: #475569; font-size: 1rem;">Tidak ada kelas kosong</div>
                <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 5px;">Semua kelas dilaporkan amar hari ini.</div>
            </div>
        <?php else: ?>
            <?php foreach ($tugasInvalList as $ti): ?>
                <div class="izin-card" style="border-left: 4px solid <?php echo ($ti['sumber'] === 'Alpa') ? '#ef4444' : '#f59e0b'; ?>;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                        <div>
                            <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a;"><?php echo htmlspecialchars($ti['nama_kelas']); ?></div>
                            <div style="font-size: 0.8rem; color: #64748b;"><?php echo htmlspecialchars($ti['nama_mapel']); ?> &bull; <?php echo $ti['jam_mulai'] . ' - ' . $ti['jam_selesai']; ?></div>
                            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;">Guru: <strong><?php echo htmlspecialchars($ti['nama_guru']); ?></strong> (<?php echo $ti['sumber']; ?>)</div>
                        </div>
                        <?php if (empty($ti['sudah_inval'])): ?>
                            <a href="<?php echo Helper::url('/absen/eksekusi-inval?id=' . $ti['pengajuan_id'] . '&kelas_id=' . $ti['kelas_id'] . '&mapel_id=' . $ti['mapel_id'] . '&tanggal=' . $ti['tanggal']); ?>" class="btn btn-primary btn-sm" style="display: flex; align-items: center; gap: 5px;">
                                <i data-lucide="play" style="width:14px;height:14px;"></i> Laksanakan
                            </a>
                        <?php else: ?>
                            <span class="badge-approved">✓ Selesai</span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($ti['materi'])): ?>
                        <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 8px 10px; font-size: 0.8rem; color: #92400e;">
                            📝 <strong>Tugas dari Guru:</strong> <?php echo htmlspecialchars($ti['materi']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function switchTabIzin(tab) {
    document.getElementById('tabPersetujuan').style.display = tab === 'persetujuan' ? 'block' : 'none';
    document.getElementById('tabInval').style.display = tab === 'inval' ? 'block' : 'none';
    document.querySelectorAll('.tab-izin .tab-btn').forEach(b => b.classList.remove('active'));
    event.target.closest('.tab-btn').classList.add('active');
}
</script>

        </div> <!-- /z-content -->
    </div> <!-- /z-main -->
</div> <!-- /siakad-container -->
