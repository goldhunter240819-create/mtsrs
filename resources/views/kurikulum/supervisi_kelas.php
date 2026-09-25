<div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
    <div>
        <h1 class="mph-title" style="color: white;">
            <i data-lucide="monitor-play" style="color: rgba(255,255,255,0.8);"></i> Supervisi Pelaksanaan Pembelajaran
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">Instrumen penilaian pedagogik, kepribadian, dan sosial guru di dalam kelas.</p>
    </div>
</div>

<div class="z-card" style="margin-top: 2rem;">
    <div class="z-table-wrap">
        <table class="z-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: center; width: 50px;">No</th>
                    <th>Nama Guru</th>
                    <th style="width: 150px;">NUPTK/NIP</th>
                    <th style="text-align: center; width: 180px;">Supervisi Terakhir</th>
                    <th style="text-align: right; padding-right: 20px; width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($guru_list)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: var(--z-muted);">Tidak ada data guru.</td>
                </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($guru_list as $g): ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td>
                            <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($g['nama']) ?></div>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; color: var(--z-muted);"><?= htmlspecialchars($g['nip'] ?: '-') ?></div>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($g['tanggal_supervisi']): ?>
                                <div style="display: inline-block; background: #ecfdf5; color: #10b981; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">
                                    <i data-lucide="check-circle" style="width: 12px; height: 12px; margin-right: 2px;"></i>
                                    <?= date('d/m/Y', strtotime($g['tanggal_supervisi'])) ?>
                                </div>
                            <?php else: ?>
                                <span style="font-size: 0.8rem; color: #94a3b8; font-style: italic;">Belum</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right; padding-right: 20px;">
                            <a href="<?= \App\Core\Helper::url('/kurikulum/supervisi-kelas/evaluasi?guru_id=' . $g['id']) ?>" class="btn btn-primary btn-sm" style="text-decoration: none;">
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
