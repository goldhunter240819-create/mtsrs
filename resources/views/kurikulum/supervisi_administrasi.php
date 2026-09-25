<div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
    <div>
        <h1 class="mph-title" style="color: white;">
            <i data-lucide="folder-check" style="color: rgba(255,255,255,0.8);"></i> Supervisi Administrasi
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">Monitoring kelengkapan perangkat mengajar guru (Silabus, RPP, Prota, Promes, dll).</p>
    </div>
</div>

<div class="z-card" style="margin-top: 2rem;">
    <div class="z-table-wrap">
        <table class="z-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Nama Guru</th>
                    <th>Status</th>
                    <th style="text-align: center;">Nilai</th>
                    <th style="text-align: center;">Tgl Supervisi</th>
                    <th style="text-align: right; padding-right: 20px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($guru_list)): ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding: 2rem; color: #64748b;">Belum ada data guru.</td>
                </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($guru_list as $g): ?>
                    <tr>
                        <td style="text-align: center; color: #64748b;"><?= $no++ ?></td>
                        <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($g['nama']) ?></td>
                        <td>
                            <?php if ($g['supervisi_id']): ?>
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f0fdf4; color: #16a34a; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                    <i data-lucide="check-circle-2" style="width: 14px; height: 14px;"></i> Selesai
                                </span>
                            <?php else: ?>
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fef2f2; color: #dc2626; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                    <i data-lucide="clock" style="width: 14px; height: 14px;"></i> Belum
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($g['supervisi_id']): ?>
                                <span style="font-weight: 700; color: <?= $g['nilai_akhir'] >= 75 ? '#16a34a' : '#f59e0b' ?>; font-size: 1.1rem;"><?= $g['nilai_akhir'] ?></span>
                            <?php else: ?>
                                <span style="color: #94a3b8;">-</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center; color: #475569;">
                            <?= $g['tanggal_supervisi'] ? date('d/m/Y', strtotime($g['tanggal_supervisi'])) : '-' ?>
                        </td>
                        <td style="text-align: right; padding-right: 20px;">
                            <a href="<?= \App\Core\Helper::url('/kurikulum/supervisi-administrasi/evaluasi?guru_id=' . $g['id']) ?>" class="btn btn-primary btn-sm" style="text-decoration: none;">
                                <i data-lucide="clipboard-edit" style="width: 14px; height: 14px;"></i> Evaluasi
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
