<?php
$title = $title ?? 'Dashboard E-BK | MTs RS';
$activeMenu = $activeMenu ?? 'bk_dashboard';

ob_start();
?>

<div class="modern-page-header">
    <div>
        <h1 class="mph-title"><i data-lucide="shield"></i> Dashboard E-BK & Kedisiplinan</h1>
        <p class="mph-subtitle">Pantau poin kedisiplinan dan prestasi siswa tahun ajaran ini.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Top 5 Pelanggar -->
    <div class="z-panel" style="margin-bottom: 0;">
        <div class="z-panel-head">
            <h3 class="z-panel-title">Siswa Poin Tertinggi (Top 5)</h3>
        </div>
        <div class="z-panel-body" style="padding: 0;">
            <table class="z-table">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th style="text-align: right;">Total Poin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($topPelanggar)): ?>
                        <tr><td colspan="3" style="text-align:center; padding: 2rem;">Belum ada data pelanggaran tahun ini.</td></tr>
                    <?php else: ?>
                        <?php foreach($topPelanggar as $p): ?>
                        <tr>
                            <td style="font-weight: 600; color: #b91c1c;"><?php echo htmlspecialchars($p['nama_siswa']); ?></td>
                            <td><?php echo htmlspecialchars($p['nama_kelas']); ?></td>
                            <td style="text-align: right; font-weight: bold;"><?php echo $p['total_poin']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Poin Masuk Terbaru -->
    <div class="z-panel" style="margin-bottom: 0;">
        <div class="z-panel-head">
            <h3 class="z-panel-title">Riwayat Poin Terbaru</h3>
        </div>
        <div class="z-panel-body" style="padding: 0;">
            <table class="z-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Kategori</th>
                        <th style="text-align: right;">Poin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentLog)): ?>
                        <tr><td colspan="4" style="text-align:center; padding: 2rem;">Belum ada log terbaru.</td></tr>
                    <?php else: ?>
                        <?php foreach($recentLog as $r): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($r['tanggal'])); ?></td>
                            <td><?php echo htmlspecialchars($r['nama_siswa']); ?></td>
                            <td><?php echo htmlspecialchars($r['nama_kategori']); ?></td>
                            <td style="text-align: right; font-weight: bold; color: <?php echo $r['tipe'] == 'pelanggaran' ? '#b91c1c' : '#15803d'; ?>;">
                                <?php echo $r['tipe'] == 'pelanggaran' ? '+' : '-'; ?><?php echo $r['poin']; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php'; 
?>
