<?php
$title = $title ?? 'Jadwal Pelaksanaan Ujian | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_jadwal';

ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="clock" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Jadwal Pelaksanaan Ujian
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Waktu pelaksanaan, durasi pengerjaan, dan mata pelajaran per sesi ujian madrasah.
        </p>
    </div>
</div>

<div class="z-card" style="padding: 1.75rem; border-radius: 16px;">
    <div style="overflow-x: auto;">
        <table class="z-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Hari & Tanggal</th>
                    <th>Jam / Waktu</th>
                    <th>Durasi</th>
                    <th>Event Ujian</th>
                    <th>Mata Pelajaran</th>
                    <th>Tingkat</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($jadwalList)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--z-text-muted);">
                        Belum ada jadwal pelaksanaan ujian yang diatur.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($jadwalList as $idx => $j): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td><strong><?php echo date('d/m/Y', strtotime($j['tanggal'])); ?></strong></td>
                        <td><?php echo substr($j['jam_mulai'], 0, 5); ?> - <?php echo substr($j['jam_selesai'], 0, 5); ?> WIB</td>
                        <td><span class="pill pill-purple" style="font-size: 0.75rem;"><?php echo $j['durasi_menit']; ?> Menit</span></td>
                        <td><?php echo htmlspecialchars($j['nama_event'] ?: '-'); ?></td>
                        <td><strong style="color: #1e293b;"><?php echo htmlspecialchars($j['nama_mapel'] ?: '-'); ?></strong></td>
                        <td><span class="pill pill-blue" style="font-size: 0.75rem;"><?php echo htmlspecialchars($j['tingkat'] ?: 'Semua'); ?></span></td>
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
