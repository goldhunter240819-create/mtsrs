
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="layout-dashboard" style="color: #bfdbfe;"></i> Dashboard Sistem Informasi Akademik
        </h1>
        <p class="mph-subtitle">Kelola data siswa, dewan guru, rombel kelas, kurikulum mata pelajaran, serta nilai harian madrasah secara terintegrasi dan efisien.</p>
    </div>
</div>

<div class="z-stats" style="margin-bottom: 1.5rem;">
    <div class="z-stat" onclick="location.href='/siakad/siswa'" style="cursor:pointer; border-left: 4px solid #3b82f6;">
        <div class="z-stat-left">
            <div class="z-stat-label">Data Siswa</div>
            <div class="z-stat-value" style="color: #1e293b;"><?php echo number_format($statSiswa); ?></div>
            <div class="z-stat-trend z-trend-up"><i data-lucide="trending-up" style="width:12px;height:12px;"></i> Terdaftar Aktif</div>
        </div>
        <div class="z-stat-icon" style="background: linear-gradient(135deg, #eff6ff, #dbeafe); color: #2563eb; width: 44px; height: 44px; border-radius: 12px; display:flex; align-items:center; justify-content:center;">
            <i data-lucide="users" style="width:20px;height:20px;"></i>
        </div>
    </div>

    <div class="z-stat" onclick="location.href='/siakad/guru'" style="cursor:pointer; border-left: 4px solid #8b5cf6;">
        <div class="z-stat-left">
            <div class="z-stat-label">Data Guru</div>
            <div class="z-stat-value" style="color: #1e293b;"><?php echo number_format($statGuru); ?></div>
            <div class="z-stat-trend z-trend-up"><i data-lucide="check-circle-2" style="width:12px;height:12px;"></i> Tenaga Pendidik</div>
        </div>
        <div class="z-stat-icon" style="background: linear-gradient(135deg, #f5f3ff, #ede9fe); color: #7c3aed; width: 44px; height: 44px; border-radius: 12px; display:flex; align-items:center; justify-content:center;">
            <i data-lucide="user-check" style="width:20px;height:20px;"></i>
        </div>
    </div>

    <div class="z-stat" onclick="location.href='/siakad/kelas'" style="cursor:pointer; border-left: 4px solid #0ea5e9;">
        <div class="z-stat-left">
            <div class="z-stat-label">Data Kelas</div>
            <div class="z-stat-value" style="color: #1e293b;"><?php echo number_format($statKelas); ?></div>
            <div class="z-stat-trend z-trend-up"><i data-lucide="layers" style="width:12px;height:12px;"></i> Rombel Aktif</div>
        </div>
        <div class="z-stat-icon" style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe); color: #0284c7; width: 44px; height: 44px; border-radius: 12px; display:flex; align-items:center; justify-content:center;">
            <i data-lucide="building-2" style="width:20px;height:20px;"></i>
        </div>
    </div>

    <div class="z-stat" onclick="location.href='/siakad/mapel'" style="cursor:pointer; border-left: 4px solid #f59e0b;">
        <div class="z-stat-left">
            <div class="z-stat-label">Mata Pelajaran</div>
            <div class="z-stat-value" style="color: #1e293b;"><?php echo number_format($statMapel); ?></div>
            <div class="z-stat-trend z-trend-up" style="color:#d97706;"><i data-lucide="book-open" style="width:12px;height:12px;"></i> Kurikulum</div>
        </div>
        <div class="z-stat-icon" style="background: linear-gradient(135deg, #fffbeb, #fef3c7); color: #d97706; width: 44px; height: 44px; border-radius: 12px; display:flex; align-items:center; justify-content:center;">
            <i data-lucide="book" style="width:20px;height:20px;"></i>
        </div>
    </div>
</div>

<div class="z-panel">
    <div class="z-panel-head">
        <div class="z-panel-title">
            <i data-lucide="clock"></i> Siswa Aktif Terbaru
        </div>
        <a href="/siakad/siswa" class="btn btn-outline btn-sm">Lihat Semua</a>
    </div>
    <div class="z-panel-body" style="padding:0;">
        <div class="z-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>L/P</th>
                        <th>Kelas</th>
                        <th>QR Token</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($siswaList as $s): ?>
                    <tr>
                        <td style="font-weight:700; color: var(--z-primary);"><?php echo htmlspecialchars($s['nis']); ?></td>
                        <td style="font-weight:700;"><?php echo htmlspecialchars($s['nama']); ?></td>
                        <td><?php echo $s['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                        <td><span class="pill pill-blue"><?php echo htmlspecialchars($s['nama_kelas'] ?? '-'); ?></span></td>
                        <td style="font-family: monospace; font-size: 0.85rem; color: var(--z-purple);"><?php echo htmlspecialchars($s['qr_token'] ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
