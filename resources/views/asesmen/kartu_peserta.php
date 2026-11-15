<?php
$title = $title ?? 'Cetak Kartu & Nomor Meja | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_kartu_peserta';

ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="id-card" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Cetak Kartu Peserta & Nomor Meja
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Cetak Kartu Tanda Peserta Ujian lengkap dengan foto & barcode serta label nomor meja ujian.
        </p>
    </div>
</div>

<!-- Filter & Print Action Bar -->
<div class="z-card" style="padding: 1.5rem; border-radius: 16px; margin-bottom: 1.5rem;">
    <form method="GET" action="<?php echo \App\Core\Helper::url('/asesmen/kartu-peserta'); ?>" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        <div style="flex: 1; min-width: 220px;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--z-text); margin-bottom: 6px;">Pilih Event Ujian:</label>
            <select name="event_id" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <?php foreach($events as $ev): ?>
                    <option value="<?php echo $ev['id']; ?>" <?php echo $eventId == $ev['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($ev['nama']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1; min-width: 180px;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--z-text); margin-bottom: 6px;">Pilih Ruangan:</label>
            <select name="ruang_id" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <option value="0">Semua Ruangan</option>
                <?php foreach($ruangs as $r): ?>
                    <option value="<?php echo $r['id']; ?>" <?php echo $ruangId == $r['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($r['nama_ruang']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="padding: 10px 20px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Filter
            </button>
        </div>

        <?php if (!empty($pesertaList)): ?>
        <div style="display: flex; gap: 8px; margin-left: auto;">
            <a href="<?php echo \App\Core\Helper::url('/asesmen/kartu-peserta/cetak?event_id=' . $eventId . ($ruangId ? '&ruang_id=' . $ruangId : '')); ?>" target="_blank" class="btn" style="background: #10b981; color: white; padding: 10px 18px; border-radius: 8px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="printer" style="width: 16px; height: 16px;"></i> Cetak Kartu Peserta
            </a>
            <a href="<?php echo \App\Core\Helper::url('/asesmen/nomor-meja/cetak?event_id=' . $eventId . ($ruangId ? '&ruang_id=' . $ruangId : '')); ?>" target="_blank" class="btn" style="background: #f59e0b; color: white; padding: 10px 18px; border-radius: 8px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="tag" style="width: 16px; height: 16px;"></i> Cetak Nomor Meja
            </a>
        </div>
        <?php endif; ?>
    </form>
</div>

<!-- Preview Table -->
<div class="z-card" style="padding: 1.75rem; border-radius: 16px;">
    <div style="overflow-x: auto;">
        <table class="z-table">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Nomor Peserta</th>
                    <th>Meja</th>
                    <th>Ruang</th>
                    <th>Nama Peserta</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    <th style="text-align: right; width: 140px;">Aksi Cepat</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pesertaList)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 3rem; color: var(--z-text-muted);">
                        Belum ada peserta yang didistribusikan pada pilihan ini.<br>
                        Silakan atur di menu <a href="<?php echo \App\Core\Helper::url('/asesmen/peserta'); ?>" style="color: #4f46e5; font-weight: 700;">Distribusi Peserta</a>.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($pesertaList as $idx => $p): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td><span style="font-family: monospace; font-weight: 800; color: #4338ca; background: #eef2ff; padding: 3px 8px; border-radius: 6px;"><?php echo htmlspecialchars($p['nomor_peserta'] ?: '-'); ?></span></td>
                        <td><span class="pill pill-green" style="font-weight: 700;">Meja <?php echo $p['nomor_meja']; ?></span></td>
                        <td><strong><?php echo htmlspecialchars($p['nama_ruang']); ?></strong></td>
                        <td><strong style="color: #1e293b;"><?php echo htmlspecialchars($p['nama_siswa']); ?></strong></td>
                        <td><?php echo htmlspecialchars($p['nis']); ?></td>
                        <td><span class="pill pill-blue"><?php echo htmlspecialchars($p['nama_kelas']); ?></span></td>
                        <td style="text-align: right;">
                            <a href="<?php echo \App\Core\Helper::url('/asesmen/kartu-peserta/cetak?event_id=' . $eventId . '&ruang_id=' . $p['ruang_id']); ?>" target="_blank" class="btn btn-outline btn-sm">
                                <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Lihat Kartu
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
