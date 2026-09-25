<div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
    <div>
        <h1 class="mph-title" style="color: white;">
            <i data-lucide="book-check" style="color: rgba(255,255,255,0.8);"></i> Monitoring Jurnal Mengajar
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">Memastikan guru disiplin mengisi absensi kelas dan materi setelah mengajar.</p>
    </div>
</div>

<!-- Filter Box -->
<div style="background: #fff; padding: 1rem 1.25rem; border-radius: 14px; border: 1px solid #e2e8f0; margin-bottom: 1.25rem;">
    <form action="" method="GET" style="display: flex; align-items: flex-end; gap: 15px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; color: #64748b; font-size: 0.75rem; font-weight: 700; margin-bottom: 6px;">Dari Tanggal:</label>
            <input type="date" name="tanggal_mulai" class="z-input" value="<?php echo htmlspecialchars($filter_tanggal_mulai); ?>" style="width: 100%; height: 38px;">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; color: #64748b; font-size: 0.75rem; font-weight: 700; margin-bottom: 6px;">Sampai Tanggal:</label>
            <input type="date" name="tanggal_akhir" class="z-input" value="<?php echo htmlspecialchars($filter_tanggal_akhir); ?>" style="width: 100%; height: 38px;">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; color: #64748b; font-size: 0.75rem; font-weight: 700; margin-bottom: 6px;">Filter Kelas:</label>
            <select name="kelas_id" class="z-input" style="width: 100%; height: 38px;">
                <option value="">Semua Kelas</option>
                <?php foreach($kelasList as $k): ?>
                    <option value="<?php echo $k['id']; ?>" <?php echo $filter_kelas == $k['id'] ? 'selected' : ''; ?>>
                        Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; color: #64748b; font-size: 0.75rem; font-weight: 700; margin-bottom: 6px;">Filter Guru:</label>
            <select name="guru_id" class="z-input" style="width: 100%; height: 38px;">
                <option value="">Semua Guru</option>
                <?php foreach($guruList as $g): ?>
                    <option value="<?php echo $g['id']; ?>" <?php echo (isset($filter_guru) && $filter_guru == $g['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($g['nama']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; color: #64748b; font-size: 0.75rem; font-weight: 700; margin-bottom: 6px;">Filter Mapel:</label>
            <select name="mapel_id" class="z-input" style="width: 100%; height: 38px;">
                <option value="">Semua Mapel</option>
                <?php foreach($mapelList as $m): ?>
                    <option value="<?php echo $m['id']; ?>" <?php echo (isset($filter_mapel) && $filter_mapel == $m['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($m['nama_mapel']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" style="height: 38px; padding: 0 20px; font-weight: 600; border-radius: 8px; border: none; cursor: pointer; color: white; background: #3b82f6; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);">
                <i data-lucide="filter" style="width:16px; height:16px; margin-right:4px;"></i> Filter
            </button>
            <button type="button" onclick="bukaCetak()" style="height: 38px; padding: 0 20px; font-weight: 600; border-radius: 8px; border: 1px solid #10b981; cursor: pointer; color: #10b981; background: #f0fdf4; display: flex; align-items: center;">
                <i data-lucide="printer" style="width:16px; height:16px; margin-right:4px;"></i> Cetak
            </button>
        </div>
    </form>
</div>

<script>
function bukaCetak() {
    const tanggal_mulai = document.querySelector('input[name="tanggal_mulai"]').value;
    const tanggal_akhir = document.querySelector('input[name="tanggal_akhir"]').value;
    const kelas_id = document.querySelector('select[name="kelas_id"]').value;
    const guru_id = document.querySelector('select[name="guru_id"]').value;
    const mapel_id = document.querySelector('select[name="mapel_id"]').value;
    
    let url = `/siakad/jurnal-global/cetak?tanggal_mulai=${tanggal_mulai}&tanggal_akhir=${tanggal_akhir}`;
    if (kelas_id) url += `&kelas_id=${kelas_id}`;
    if (guru_id) url += `&guru_id=${guru_id}`;
    if (mapel_id) url += `&mapel_id=${mapel_id}`;
    
    window.open(url, '_blank');
}
</script>

<div class="z-panel">
    <div class="z-table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 110px;">Tanggal</th>
                    <th>Guru & Pelajaran</th>
                    <th>Materi Pembelajaran</th>
                    <th style="width: 250px;">Statistik Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($jurnalList)): ?>
                <tr>
                    <td colspan="4" style="text-align:center; color: var(--z-muted); padding: 2rem;">Belum ada log jurnal mengajar guru.</td>
                </tr>
                <?php endif; ?>
                <?php foreach($jurnalList as $j): ?>
                <tr>
                    <td style="font-family:monospace; font-weight:600; vertical-align: top; padding-top: 1rem;">
                        <?php echo date('d/m/Y', strtotime($j['tanggal'])); ?>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px; display: flex; align-items: center; gap: 3px;">
                            <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                            <span><?php echo !empty($j['created_at']) ? date('H:i', strtotime($j['created_at'])) . ' WIB' : '-'; ?></span>
                        </div>
                    </td>
                    <td style="vertical-align: top; padding-top: 1rem;">
                        <div style="font-weight:800; color: var(--z-primary); margin-bottom: 4px;"><?php echo htmlspecialchars($j['nama_guru']); ?></div>
                        <div style="font-size:0.85rem; font-weight:700; color:var(--z-text);"><?php echo htmlspecialchars($j['nama_mapel']); ?></div>
                        <div style="margin-top: 4px;"><span class="pill pill-blue">Kelas <?php echo htmlspecialchars($j['nama_kelas']); ?></span></div>
                    </td>
                    <td style="vertical-align: top; padding-top: 1rem; font-size:0.85rem; color: var(--z-text);">
                        <div style="line-height: 1.5; font-weight: 600;"><?php echo nl2br(htmlspecialchars($j['materi'])); ?></div>
                        <?php if (!empty($j['keterangan'])): ?>
                            <div style="background:#fef3c7; padding:8px 10px; border-radius:6px; margin-top:8px; border-left:3px solid #f59e0b; font-size:0.8rem; color:#78350f; font-style:italic;">
                                <?php echo nl2br(htmlspecialchars($j['keterangan'])); ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td style="vertical-align: top; padding-top: 1rem;">
                        <div style="display: flex; gap: 4px; flex-wrap: wrap; margin-bottom: 8px;">
                            <span style="background:#dcfce7; color:#16a34a; padding:2px 8px; border-radius:12px; font-size:0.7rem; font-weight:700;" title="Hadir">H: <?php echo $j['hadir']; ?></span>
                            <span style="background:#fef3c7; color:#d97706; padding:2px 8px; border-radius:12px; font-size:0.7rem; font-weight:700;" title="Sakit">S: <?php echo $j['sakit']; ?></span>
                            <span style="background:#dbeafe; color:#1d4ed8; padding:2px 8px; border-radius:12px; font-size:0.7rem; font-weight:700;" title="Izin">I: <?php echo $j['izin']; ?></span>
                            <span style="background:#fee2e2; color:#dc2626; padding:2px 8px; border-radius:12px; font-size:0.7rem; font-weight:700;" title="Alpha">A: <?php echo $j['alpha']; ?></span>
                        </div>
                        <?php if (!empty($j['absent_list'])): ?>
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 8px; font-size: 0.75rem;">
                                <div style="color: #64748b; font-weight: 700; margin-bottom: 4px; font-size: 0.7rem;">Tidak Hadir:</div>
                                <ul style="margin: 0; padding-left: 15px; color: #334155;">
                                    <?php foreach ($j['absent_list'] as $ab): 
                                        $col = $ab['status'] == 'Sakit' ? '#d97706' : ($ab['status'] == 'Izin' ? '#1d4ed8' : '#dc2626');
                                    ?>
                                        <li style="margin-bottom: 2px;">
                                            <b><?php echo htmlspecialchars($ab['nama']); ?></b> 
                                            <span style="color: <?php echo $col; ?>; font-weight: 700; font-size: 0.7rem;">(<?php echo in_array($ab['status'], ['Alpha', 'Bolos']) ? 'Alpa' : $ab['status']; ?>)</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
