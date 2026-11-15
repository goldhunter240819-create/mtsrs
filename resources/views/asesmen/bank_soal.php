<?php
$title = $title ?? 'Bank Soal & Kisi-Kisi | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_bank_soal';

ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="file-question" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Bank Soal & Kisi-Kisi
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Kompilasi master naskah soal, kunci jawaban, dan kisi-kisi per mata pelajaran untuk STS, SAS, dan Asesmen Madrasah.
        </p>
    </div>
</div>

<div class="z-card" style="padding: 1.75rem; border-radius: 16px;">
    <div style="overflow-x: auto;">
        <table class="z-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Mata Pelajaran</th>
                    <th>Judul Naskah / Kode</th>
                    <th>Tingkat</th>
                    <th>Event Terkait</th>
                    <th>Komposisi Soal</th>
                    <th>Guru Pengampu</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($soalList)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--z-text-muted);">
                        Belum ada bank soal yang diinputkan.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($soalList as $idx => $s): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td><strong style="color: #1e293b;"><?php echo htmlspecialchars($s['nama_mapel']); ?></strong></td>
                        <td>
                            <div><?php echo htmlspecialchars($s['judul']); ?></div>
                            <?php if(!empty($s['kode_soal'])): ?>
                                <small style="color: var(--z-text-muted); font-family: monospace;"><?php echo htmlspecialchars($s['kode_soal']); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><span class="pill pill-blue" style="font-size: 0.75rem;">Kelas <?php echo htmlspecialchars($s['tingkat']); ?></span></td>
                        <td><?php echo htmlspecialchars($s['nama_event'] ?: 'Semua Event'); ?></td>
                        <td>
                            <span style="font-weight: 600; font-size: 0.85rem; color: #334155;">
                                <?php echo $s['jumlah_pg']; ?> PG / <?php echo $s['jumlah_esai']; ?> Esai
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($s['nama_guru'] ?: '-'); ?></td>
                        <td>
                            <?php if ($s['status'] === 'disetujui'): ?>
                                <span class="pill pill-green" style="font-size: 0.75rem;">Disetujui</span>
                            <?php elseif ($s['status'] === 'diajukan'): ?>
                                <span class="pill pill-yellow" style="font-size: 0.75rem;">Diajukan</span>
                            <?php else: ?>
                                <span class="pill pill-gray" style="font-size: 0.75rem;">Draft</span>
                            <?php endif; ?>
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
