<?php
$title = $title ?? 'Cetak Nomor Meja Ujian | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_nomor_meja';

ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="tag" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Cetak Nomor Meja Ujian
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Cetak label nomor meja ujian per ruangan. Kartu Peserta Ujian langsung menggunakan Kartu Pelajar Siswa SIAKAD.
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="<?php echo \App\Core\Helper::url('/asesmen/scan'); ?>" class="btn" style="background: #10b981; color: white; font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(16,185,129,0.3);">
            <i data-lucide="qr-code" style="width: 18px; height: 18px;"></i> Buka Scanner Presensi
        </a>
    </div>
</div>

<!-- Notice Bar: Presensi Ujian Otomatis -->
<div class="z-card" style="padding: 1.25rem 1.5rem; border-radius: 14px; margin-bottom: 1.5rem; background: #f0fdf4; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 14px;">
    <div style="width: 38px; height: 38px; border-radius: 10px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        <i data-lucide="check-check" style="width: 22px; height: 22px;"></i>
    </div>
    <div style="font-size: 0.88rem; color: #166534; line-height: 1.5;">
        <strong>Presensi Ujian Otomatis via QR Scan:</strong> Label nomor meja di bawah ini ditempel pada masing-masing meja ruang ujian. Saat ujian berlangsung, pengawas cukup menggunakan <strong>Scanner Presensi Ujian</strong> untuk men-scan barcode/QR siswa secara instan sesuai jadwal sesi dan mata pelajaran aktif.
    </div>
</div>

<!-- Filter & Cetak Action Bar -->
<div class="z-card" style="padding: 1.5rem; border-radius: 16px; margin-bottom: 1.5rem;">
    <form method="GET" action="<?php echo \App\Core\Helper::url('/asesmen/nomor-meja'); ?>" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
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
        <div style="margin-left: auto;">
            <a href="<?php echo \App\Core\Helper::url('/asesmen/nomor-meja/cetak?event_id=' . $eventId . ($ruangId ? '&ruang_id=' . $ruangId : '')); ?>" target="_blank" class="btn" style="background: #f59e0b; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 700; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);">
                <i data-lucide="printer" style="width: 16px; height: 16px;"></i> Cetak Label Nomor Meja (A4)
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
                    <th>Nomor Meja</th>
                    <th>Ruangan</th>
                    <th>Nomor Peserta</th>
                    <th>Nama Peserta Didik</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pesertaList)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem; color: var(--z-text-muted);">
                        Belum ada data meja pada pilihan ruangan ini.<br>
                        Silakan atur di menu <a href="<?php echo \App\Core\Helper::url('/asesmen/peserta'); ?>" style="color: #4f46e5; font-weight: 700;">Distribusi Peserta</a>.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($pesertaList as $idx => $p): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td>
                            <span class="pill pill-green" style="font-weight: 800; font-size: 0.85rem;">
                                Meja <?php echo sprintf("%02d", $p['nomor_meja']); ?>
                            </span>
                        </td>
                        <td><strong><?php echo htmlspecialchars($p['nama_ruang']); ?></strong></td>
                        <td><span style="font-family: monospace; font-weight: 800; color: #4338ca; background: #eef2ff; padding: 3px 8px; border-radius: 6px;"><?php echo htmlspecialchars($p['nomor_peserta'] ?: '-'); ?></span></td>
                        <td><strong style="color: #1e293b;"><?php echo htmlspecialchars($p['nama_siswa']); ?></strong></td>
                        <td><?php echo htmlspecialchars($p['nis']); ?></td>
                        <td><span class="pill pill-blue"><?php echo htmlspecialchars($p['nama_kelas']); ?></span></td>
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
