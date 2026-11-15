<?php
$title = $title ?? 'Kisi-Kisi Soal Ujian | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_kisi_kisi';

ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="list-checks" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Kisi-Kisi Soal Ujian
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Distribusi kompetensi, capaian pembelajaran (CP), dan indikator butir soal STS, SAS, dan Asesmen Madrasah.
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
                    <th>Tingkat</th>
                    <th>Event Terkait</th>
                    <th>Komposisi Soal</th>
                    <th>Guru Penyusun</th>
                    <th>Dokumen Kisi-Kisi</th>
                    <th style="text-align: right; width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($soalList)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--z-text-muted);">
                        Belum ada dokumen kisi-kisi soal yang diunggah. Silakan hubungi Guru Pengampu Mapel.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($soalList as $idx => $s): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td><strong style="color: #1e293b;"><?php echo htmlspecialchars($s['nama_mapel']); ?></strong></td>
                        <td><span class="pill pill-blue" style="font-size: 0.75rem;">Kelas <?php echo htmlspecialchars($s['tingkat']); ?></span></td>
                        <td><?php echo htmlspecialchars($s['nama_event'] ?: 'Semua Event'); ?></td>
                        <td>
                            <span style="font-weight: 600; font-size: 0.85rem; color: #334155;">
                                <?php echo $s['jumlah_pg']; ?> PG / <?php echo $s['jumlah_esai']; ?> Esai
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($s['nama_guru'] ?: '-'); ?></td>
                        <td>
                            <?php if(!empty($s['kisi_kisi_file'])): ?>
                                <a href="<?php echo \App\Core\Helper::url('/uploads/asesmen/' . $s['kisi_kisi_file']); ?>" target="_blank" class="btn btn-outline btn-sm">
                                    <i data-lucide="download" style="width: 14px; height: 14px;"></i> Unduh PDF
                                </a>
                            <?php else: ?>
                                <span style="color: var(--z-text-muted); font-size: 0.8rem;">Belum ada file</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <a href="<?php echo \App\Core\Helper::url('/asesmen/bank-soal'); ?>" class="btn btn-outline btn-sm" title="Lihat di Bank Soal">
                                <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Lihat
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
