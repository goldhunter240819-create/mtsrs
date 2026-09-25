<div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
    <div>
        <h1 class="mph-title" style="color: white;">
            <i data-lucide="layout-dashboard" style="color: rgba(255,255,255,0.8);"></i> Dashboard Kurikulum
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">Pantau pelaksanaan KBM, kelengkapan administrasi guru, dan instrumen penilaian.</p>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:1.5rem; margin-bottom:1.5rem;">
    <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02); display:flex; align-items:center; gap:1.25rem; cursor:pointer;" onclick="location.href='<?= \App\Core\Helper::url('/kurikulum/supervisi-administrasi') ?>'">
        <div style="width:50px; height:50px; background:#eff6ff; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#3b82f6;">
            <i data-lucide="users" style="width:24px;height:24px;"></i>
        </div>
        <div>
            <div style="font-size:0.8rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Total Guru</div>
            <div style="font-size:1.8rem; font-weight:800; color:#0f172a;"><?= number_format($statTotalGuru) ?></div>
        </div>
    </div>
    
    <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02); display:flex; align-items:center; gap:1.25rem; cursor:pointer;" onclick="location.href='<?= \App\Core\Helper::url('/kurikulum/supervisi-administrasi') ?>'">
        <div style="width:50px; height:50px; background:#f0fdf4; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#22c55e;">
            <i data-lucide="folder-check" style="width:24px;height:24px;"></i>
        </div>
        <div>
            <div style="font-size:0.8rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Supervisi Administrasi</div>
            <div style="font-size:1.8rem; font-weight:800; color:#0f172a;"><?= number_format($statAdminLengkap) ?> <span style="font-size:1rem; color:#94a3b8; font-weight:600;">/ <?= number_format($statTotalGuru) ?></span></div>
        </div>
    </div>
    
    <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02); display:flex; align-items:center; gap:1.25rem; cursor:pointer;" onclick="location.href='<?= \App\Core\Helper::url('/kurikulum/supervisi-kelas') ?>'">
        <div style="width:50px; height:50px; background:#fef2f2; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#ef4444;">
            <i data-lucide="monitor-play" style="width:24px;height:24px;"></i>
        </div>
        <div>
            <div style="font-size:0.8rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Supervisi Kelas</div>
            <div style="font-size:1.8rem; font-weight:800; color:#0f172a;"><?= number_format($statSupervisiKelas) ?> <span style="font-size:1rem; color:#94a3b8; font-weight:600;">/ <?= number_format($statTotalGuru) ?></span></div>
        </div>
    </div>
</div>

<div class="z-panel">
    <div class="z-panel-head">
        <div class="z-panel-title">
            <i data-lucide="clock"></i> Supervisi Kelas Terbaru
        </div>
        <a href="<?= \App\Core\Helper::url('/kurikulum/supervisi-kelas') ?>" class="btn btn-outline btn-sm">Lihat Semua</a>
    </div>
    <div class="z-panel-body" style="padding:0;">
        <div class="z-table-wrap">
            <table class="z-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Guru</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentSupervisi)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 2rem; color: #64748b;">Belum ada data supervisi kelas.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($recentSupervisi as $rs): ?>
                        <tr>
                            <td>
                                <div style="display: inline-block; background: #ecfdf5; color: #10b981; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">
                                    <?= date('d M Y', strtotime($rs['tanggal_supervisi'])) ?>
                                </div>
                            </td>
                            <td style="font-weight:700; color: #1e293b;"><?= htmlspecialchars($rs['nama_guru']) ?></td>
                            <td><?= htmlspecialchars($rs['mata_pelajaran']) ?></td>
                            <td><span class="pill pill-blue"><?= htmlspecialchars($rs['kelas']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
