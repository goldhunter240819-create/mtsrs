<?php use App\Core\Helper; ?>
<div class="modern-page-header" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="mph-title">
                <i data-lucide="eye" style="color: #bfdbfe;"></i> Detail Monitoring Nilai
            </h1>
            <p class="mph-subtitle">Pantau progres nilai harian siswa secara detail.</p>
        </div>
        <a href="<?= Helper::url('/siakad/monitoring-nilai') ?>" class="btn btn-outline">
            <i data-lucide="arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="z-container" style="padding-top: 0;">
    <div class="z-card" style="padding: 20px;">
        
        <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 20px; margin-bottom: 20px; border-bottom: 1px solid var(--z-border); padding-bottom: 15px;">
            <!-- Kiri: Info Dasar -->
            <div style="flex: 1; min-width: 350px;">
                <table style="width: 100%; font-size: 0.95rem;">
                    <tr><td style="width: 140px; font-weight: bold; color: var(--z-muted); white-space: nowrap; padding: 4px 0;">Mata Pelajaran</td><td style="padding: 4px 0;">: <?= htmlspecialchars($mapel_info['nama_mapel']) ?></td></tr>
                    <tr><td style="font-weight: bold; color: var(--z-muted); white-space: nowrap; padding: 4px 0;">Kelas</td><td style="padding: 4px 0;">: <?= htmlspecialchars($kelas['nama_kelas']) ?></td></tr>
                    <tr><td style="font-weight: bold; color: var(--z-muted); white-space: nowrap; padding: 4px 0;">Guru Pengampu</td><td style="padding: 4px 0;">: <?= htmlspecialchars($mapel_info['guru_pengampu']) ?></td></tr>
                    <tr><td style="font-weight: bold; color: var(--z-muted); white-space: nowrap; padding: 4px 0;">Wali Kelas</td><td style="padding: 4px 0;">: <?= htmlspecialchars($kelas['wali_kelas']) ?></td></tr>
                    <tr><td style="font-weight: bold; color: var(--z-muted); white-space: nowrap; padding: 4px 0;">Tahun Ajaran</td><td style="padding: 4px 0;">: <?= htmlspecialchars($activeYear['name'] ?? $tahun_ajaran_id) ?></td></tr>
                    <tr><td style="font-weight: bold; color: var(--z-muted); white-space: nowrap; padding: 4px 0;">Semester</td><td style="padding: 4px 0;">: <?= htmlspecialchars($semester) ?></td></tr>
                </table>
            </div>

            <!-- Kanan: Materi -->
            <div style="flex: 1; min-width: 350px;">
                <p style="font-weight: bold; margin-bottom: 10px; color: var(--z-text); font-size: 0.95rem;">
                    <i data-lucide="info" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-top: -2px; color: #3b82f6;"></i> Keterangan Materi:
                </p>
                <div style="background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <table class="z-table" style="width: 100%; font-size: 0.85rem; margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th style="width: 80px; padding: 8px 12px; background: #f1f5f9; border-bottom: 1px solid #e2e8f0;">Evaluasi</th>
                                <th style="padding: 8px 12px; background: #f1f5f9; border-bottom: 1px solid #e2e8f0;">Materi Pembelajaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ph_list as $ph): ?>
                            <tr>
                                <td style="font-weight: bold; padding: 8px 12px; border-bottom: 1px solid #f1f5f9;"><?= htmlspecialchars(str_replace('PH ', 'PH', $ph)) ?></td>
                                <td style="padding: 8px 12px; border-bottom: 1px solid #f1f5f9; <?= empty($ph_materi[$ph]) ? 'font-style: italic; color: #94a3b8;' : '' ?>">
                                    <?= !empty($ph_materi[$ph]) ? htmlspecialchars($ph_materi[$ph]) : '-' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div style="overflow-x: auto; margin-top: 20px;">
            <table class="z-table" style="font-size: 0.9rem; width: 100%;">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 50px; text-align: center; vertical-align: middle;">No</th>
                        <th rowspan="2" style="width: 100px; text-align: center; vertical-align: middle;">NIS</th>
                        <th rowspan="2" style="vertical-align: middle;">Nama Lengkap Siswa</th>
                        <th colspan="<?= count($ph_list) ?>" style="text-align: center;">Penilaian Harian (PH)</th>
                        <th colspan="2" style="text-align: center;">Evaluasi Sumatif</th>
                    </tr>
                    <tr>
                        <?php foreach($ph_list as $ph): ?>
                            <th style="width: 70px; text-align: center;"><?= htmlspecialchars(str_replace('PH ', 'PH', $ph)) ?></th>
                        <?php endforeach; ?>
                        <th style="width: 70px; text-align: center;">PTS</th>
                        <th style="width: 70px; text-align: center;">PAS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($siswa_list)): ?>
                        <tr>
                            <td colspan="<?= 3 + count($ph_list) + 2 ?>" style="text-align: center; padding: 20px;">Belum ada data siswa di kelas ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no=1; foreach($siswa_list as $s): ?>
                        <tr>
                            <td style="text-align: center;"><?= $no++ ?></td>
                            <td style="text-align: center;"><?= htmlspecialchars($s['nis'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($s['nama']) ?></td>
                            
                            <?php foreach($ph_list as $ph): 
                                $val = $nilai_grouped[$s['id']][$ph] ?? '';
                            ?>
                                <td style="text-align: center; font-weight: bold;"><?= $val !== '' ? $val : '-' ?></td>
                            <?php endforeach; ?>
                            
                            <!-- Placeholder untuk PTS / PAS (sesuai template cetak) -->
                            <td style="text-align: center; color: #cbd5e1;">-</td>
                            <td style="text-align: center; color: #cbd5e1;">-</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
