<?php
$title = $title ?? 'Berita Acara Ujian | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_berita_acara';

ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="file-text" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Berita Acara & Presensi Ujian
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Dokumen resmi Berita Acara Pelaksanaan Ujian, Presensi Peserta, dan Pengawas Ruang.
        </p>
    </div>
</div>

<div class="z-card" style="padding: 1.75rem; border-radius: 16px;">
    <div style="overflow-x: auto;">
        <table class="z-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Tanggal & Waktu</th>
                    <th>Ruang</th>
                    <th>Event Ujian</th>
                    <th>Mata Pelajaran</th>
                    <th>Pengawas Ruang</th>
                    <th>Kehadiran Peserta</th>
                    <th style="text-align: right; width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($baList)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--z-text-muted);">
                        Belum ada Berita Acara ujian yang tersimpan.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($baList as $idx => $ba): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td>
                            <strong><?php echo date('d/m/Y', strtotime($ba['tanggal'])); ?></strong>
                            <?php if(!empty($ba['jam_mulai'])): ?>
                                <div style="font-size: 0.78rem; color: var(--z-text-muted);"><?php echo substr($ba['jam_mulai'], 0, 5); ?> - <?php echo substr($ba['jam_selesai'], 0, 5); ?> WIB</div>
                            <?php endif; ?>
                        </td>
                        <td><span class="pill pill-purple" style="font-size: 0.78rem;"><?php echo htmlspecialchars($ba['nama_ruang']); ?></span></td>
                        <td><?php echo htmlspecialchars($ba['nama_event'] ?: '-'); ?></td>
                        <td><strong><?php echo htmlspecialchars($ba['nama_mapel'] ?: '-'); ?></strong></td>
                        <td>
                            <div>1. <?php echo htmlspecialchars($ba['nama_pengawas1'] ?: '-'); ?></div>
                            <?php if(!empty($ba['nama_pengawas2'])): ?>
                                <div>2. <?php echo htmlspecialchars($ba['nama_pengawas2']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="pill pill-green" style="font-size: 0.75rem;">Hadir: <?php echo $ba['jumlah_hadir']; ?></span>
                            <?php if($ba['jumlah_tidak_hadir'] > 0): ?>
                                <span class="pill pill-red" style="font-size: 0.75rem;">Absen: <?php echo $ba['jumlah_tidak_hadir']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <button type="button" class="btn btn-outline btn-sm" onclick="alert('Fitur cetak Berita Acara siap dikembangkan')" title="Cetak Berita Acara">
                                <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Cetak
                            </button>
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
