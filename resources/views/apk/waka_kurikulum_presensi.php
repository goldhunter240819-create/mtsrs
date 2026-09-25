<div class="apk-header" style="background: transparent; padding-top: 30px; position: absolute; width: 100%; top: 0; z-index: 100;">
    <a href="/apk/aplikasi-saya" style="color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 700; background: rgba(0,0,0,0.2); padding: 8px 15px; border-radius: 20px; backdrop-filter: blur(5px);">
        <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Kembali
    </a>
</div>

<div class="apk-vector-header" style="padding-bottom: 25px; padding-top: 90px; background-color: #3b82f6;">
    <div class="apk-top-logo">
        <div style="width: 45px; height: 45px; border-radius: 12px; background: #fff; color: #3b82f6; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <i data-lucide="file-check"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Monitoring Kehadiran</div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #fff;"><?= htmlspecialchars($title ?? 'Monitor Absen Guru') ?></div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; min-height: 70vh; padding: 20px;">

    <!-- Form Filter Tanggal -->
    <form action="" method="GET" style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="date" name="tgl" value="<?= htmlspecialchars($tanggal) ?>" style="flex: 1; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; outline: none;">
        <button type="submit" style="background: #3b82f6; color: #fff; border: none; padding: 0 15px; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 5px;">
            <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Filter
        </button>
        <a href="/apk/kamad/rekap-absen-guru" style="background: #10b981; color: #fff; text-decoration: none; padding: 0 15px; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 5px;">
            <i data-lucide="calendar-check-2" style="width: 16px; height: 16px;"></i> Rekap
        </a>
    </form>
    
    <!-- Statistik -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 20px;">
        <div style="background: #f0fdf4; border: 1px solid rgba(34,197,94,0.2); padding: 10px; border-radius: 12px; text-align: center;">
            <div style="font-size: 1.2rem; font-weight: 800; color: #16a34a;"><?= $hadir ?></div>
            <div style="font-size: 0.7rem; font-weight: 700; color: #16a34a; text-transform: uppercase;">Hadir</div>
        </div>
        <div style="background: #fffbeb; border: 1px solid rgba(245,158,11,0.2); padding: 10px; border-radius: 12px; text-align: center;">
            <div style="font-size: 1.2rem; font-weight: 800; color: #d97706;"><?= $terlambat ?></div>
            <div style="font-size: 0.7rem; font-weight: 700; color: #d97706; text-transform: uppercase;">T.Lambat</div>
        </div>
        <div style="background: #f8fafc; border: 1px solid rgba(100,116,139,0.2); padding: 10px; border-radius: 12px; text-align: center;">
            <div style="font-size: 1.2rem; font-weight: 800; color: #64748b;"><?= $belum_absen ?></div>
            <div style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase;">B.Absen</div>
        </div>
    </div>

    <!-- Daftar Guru -->
    <h3 style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin-bottom: 12px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;">Daftar Presensi</h3>
    <div style="display: flex; flex-direction: column; gap: 10px;">
        <?php foreach ($rekap as $r): ?>
            <?php 
                $bgColor = '#fff';
                $statusColor = '#94a3b8';
                $statusText = 'Belum Absen';
                
                if ($r['status'] == 'Hadir') {
                    $statusColor = '#22c55e';
                    $statusText = 'Hadir';
                } elseif ($r['status'] == 'Terlambat') {
                    $statusColor = '#f59e0b';
                    $statusText = 'Terlambat';
                } elseif ($r['status'] == 'Sakit') {
                    $statusColor = '#3b82f6';
                    $statusText = 'Sakit';
                } elseif ($r['status'] == 'Izin') {
                    $statusColor = '#8b5cf6';
                    $statusText = 'Izin';
                } elseif ($r['status'] == 'Alpa') {
                    $statusColor = '#ef4444';
                    $statusText = 'Alpa';
                    $bgColor = '#fef2f2';
                }
            ?>
            <div style="background: <?= $bgColor ?>; padding: 12px; border-radius: 14px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.01);">
                <div>
                    <div style="font-weight: 800; color: #1e293b; font-size: 0.9rem; margin-bottom: 3px;"><?= htmlspecialchars($r['nama']) ?></div>
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">
                        Masuk: <span style="font-weight: 800; color: #3b82f6;"><?= $r['jam_masuk'] ? substr($r['jam_masuk'], 0, 5) : '--:--' ?></span>
                    </div>
                </div>
                <div style="background: rgba(0,0,0,0.03); color: <?= $statusColor ?>; padding: 5px 10px; border-radius: 8px; font-weight: 800; font-size: 0.75rem; border: 1px solid <?= $statusColor ?>;">
                    <?= $statusText ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>
