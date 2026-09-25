<div class="apk-header" style="background: transparent; padding-top: 30px; position: absolute; width: 100%; top: 0; z-index: 100;">
    <a href="/apk/aplikasi-saya" style="color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 700; background: rgba(0,0,0,0.2); padding: 8px 15px; border-radius: 20px; backdrop-filter: blur(5px);">
        <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Kembali
    </a>
</div>

<div class="apk-vector-header" style="padding-bottom: 25px; padding-top: 90px; background-color: #4338ca;">
    <div class="apk-top-logo">
        <div style="width: 45px; height: 45px; border-radius: 12px; background: #fff; color: #4338ca; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <i data-lucide="pie-chart"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Waka Kesiswaan</div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Statistik Absensi</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; min-height: 70vh; padding: 20px;">
    
    <div style="background: #fff; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 10px rgba(0,0,0,0.02); text-align: center; margin-bottom: 20px;">
        <h4 style="margin: 0 0 10px 0; font-size: 0.9rem; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">Kehadiran Hari Ini</h4>
        <div style="position: relative; width: 120px; height: 120px; margin: 0 auto; border-radius: 50%; background: <?= $conicGradient ?>; display: flex; align-items: center; justify-content: center;">
            <div style="width: 100px; height: 100px; background: #fff; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <span style="font-size: 1.8rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= $persentase ?>%</span>
                <span style="font-size: 0.7rem; font-weight: 700; color: #64748b; margin-top: 2px;">Hadir</span>
            </div>
        </div>
        <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600; margin-top: 15px;">
            Dari total <?= $totalSiswa ?> siswa aktif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; margin-bottom: 25px;">
        <div style="background: #f0fdf4; border: 1px solid rgba(34,197,94,0.2); border-radius: 12px; padding: 10px 2px; text-align: center;">
            <div style="font-size: 1.1rem; font-weight: 800; color: #16a34a;"><?= $hadir ?></div>
            <div style="font-size: 0.55rem; font-weight: 800; color: #22c55e; text-transform: uppercase;">Hadir</div>
        </div>
        <div style="background: #fffbeb; border: 1px solid rgba(245,158,11,0.2); border-radius: 12px; padding: 10px 2px; text-align: center;">
            <div style="font-size: 1.1rem; font-weight: 800; color: #d97706;"><?= $sakit ?></div>
            <div style="font-size: 0.55rem; font-weight: 800; color: #f59e0b; text-transform: uppercase;">Sakit</div>
        </div>
        <div style="background: #eff6ff; border: 1px solid rgba(59,130,246,0.2); border-radius: 12px; padding: 10px 2px; text-align: center;">
            <div style="font-size: 1.1rem; font-weight: 800; color: #2563eb;"><?= $izin ?></div>
            <div style="font-size: 0.55rem; font-weight: 800; color: #3b82f6; text-transform: uppercase;">Izin</div>
        </div>
        <div style="background: #fef2f2; border: 1px solid rgba(239,68,68,0.2); border-radius: 12px; padding: 10px 2px; text-align: center;">
            <div style="font-size: 1.1rem; font-weight: 800; color: #dc2626;"><?= $alpha ?></div>
            <div style="font-size: 0.55rem; font-weight: 800; color: #ef4444; text-transform: uppercase;">Alpha</div>
        </div>
        <div style="background: #fffbeb; border: 1px solid #fcd34d; border-radius: 12px; padding: 10px 2px; text-align: center;">
            <div style="font-size: 1.1rem; font-weight: 800; color: #b45309;"><?= $bolos ?></div>
            <div style="font-size: 0.55rem; font-weight: 800; color: #d97706; text-transform: uppercase;">Bolos</div>
        </div>
    </div>

    <h4 style="font-size: 0.9rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px;">Tren 7 Hari Terakhir</h4>
    <div style="background: #fff; padding: 15px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 2px 5px rgba(0,0,0,0.02); display: flex; align-items: flex-end; gap: 8px; height: 150px; overflow-x: auto;">
        <?php 
        $maxVal = max($grafik) ?: 1; 
        foreach($grafik as $tgl => $val): 
            $height = ($val / $maxVal) * 100;
        ?>
        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; min-width: 30px; height: 100%;">
            <div style="font-size: 0.7rem; font-weight: 700; color: #4338ca; margin-bottom: 5px;"><?= $val ?></div>
            <div style="width: 100%; background: #c7d2fe; border-radius: 6px 6px 0 0; height: <?= max($height, 5) ?>%; position: relative; overflow: hidden;">
                <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to top, #4338ca, #6366f1); opacity: 0.8;"></div>
            </div>
            <div style="font-size: 0.6rem; font-weight: 600; color: #94a3b8; margin-top: 5px; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%;"><?= $tgl ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
