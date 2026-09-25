<div class="apk-vector-header" style="padding-bottom: 70px; justify-content: center; text-align: center; position: relative; background-image: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);">
    <div>
        <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Monitor Absensi</div>
        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.8); margin-top: 5px;"><?= date('d F Y') ?></div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -30px; border-radius: 30px 30px 0 0; min-height: 70vh;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-left: 5px;">
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="users" style="color: #0ea5e9; width: 20px;"></i> Rekap Siswa
        </h3>
        <div style="font-size: 0.75rem; font-weight: 800; background: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 12px;">Total: <?= $siswa_aktif ?></div>
    </div>

    <!-- Kotak Siswa -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 30px;">
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.02); border-bottom: 4px solid #10b981;">
            <div style="font-size: 1.5rem; font-weight: 800; color: #059669;"><?= $s_hadir ?></div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700;">Hadir</div>
        </div>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.02); border-bottom: 4px solid #f59e0b;">
            <div style="font-size: 1.5rem; font-weight: 800; color: #d97706;"><?= $s_izin ?></div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700;">Izin / Sakit</div>
        </div>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.02); border-bottom: 4px solid #ef4444;">
            <div style="font-size: 1.5rem; font-weight: 800; color: #dc2626;"><?= $s_alpa ?></div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700;">Alpa</div>
        </div>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.02); border-bottom: 4px solid #cbd5e1;">
            <div style="font-size: 1.5rem; font-weight: 800; color: #475569;"><?= $s_belum ?></div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700;">Belum Scan</div>
        </div>
    </div>

    <!-- Progress Siswa -->
    <div style="margin-bottom: 35px; background: #f8fafc; padding: 15px; border-radius: 16px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.75rem; font-weight: 800; color: #475569;">
            <span>Persentase Kehadiran</span>
            <span style="color: #0ea5e9;"><?= $siswa_aktif > 0 ? round(($s_hadir/$siswa_aktif)*100) : 0 ?>%</span>
        </div>
        <div style="width: 100%; height: 10px; background: #e2e8f0; border-radius: 5px; overflow: hidden; display: flex;">
            <div style="height: 100%; background: #10b981; width: <?= $siswa_aktif > 0 ? ($s_hadir/$siswa_aktif)*100 : 0 ?>%;"></div>
            <div style="height: 100%; background: #f59e0b; width: <?= $siswa_aktif > 0 ? ($s_izin/$siswa_aktif)*100 : 0 ?>%;"></div>
            <div style="height: 100%; background: #ef4444; width: <?= $siswa_aktif > 0 ? ($s_alpa/$siswa_aktif)*100 : 0 ?>%;"></div>
        </div>
        <div style="display: flex; gap: 15px; margin-top: 10px; justify-content: center; font-size: 0.7rem; color: #64748b; font-weight: 700;">
            <div style="display: flex; align-items: center; gap: 4px;"><div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div> Hadir</div>
            <div style="display: flex; align-items: center; gap: 4px;"><div style="width: 8px; height: 8px; background: #f59e0b; border-radius: 50%;"></div> Izin/Skt</div>
            <div style="display: flex; align-items: center; gap: 4px;"><div style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%;"></div> Alpa</div>
            <div style="display: flex; align-items: center; gap: 4px;"><div style="width: 8px; height: 8px; background: #cbd5e1; border-radius: 50%;"></div> Blm</div>
        </div>
    </div>


    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-left: 5px;">
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="briefcase" style="color: #10b981; width: 20px;"></i> Rekap Guru & Staf
        </h3>
        <div style="font-size: 0.75rem; font-weight: 800; background: #ecfdf5; color: #059669; padding: 4px 10px; border-radius: 12px;">Total: <?= $guru_aktif ?></div>
    </div>

    <!-- Kotak Guru -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 30px;">
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.02); border-bottom: 4px solid #10b981;">
            <div style="font-size: 1.5rem; font-weight: 800; color: #059669;"><?= $g_hadir ?></div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700;">Hadir</div>
        </div>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.02); border-bottom: 4px solid #f59e0b;">
            <div style="font-size: 1.5rem; font-weight: 800; color: #d97706;"><?= $g_izin ?></div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700;">Izin / Sakit</div>
        </div>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.02); border-bottom: 4px solid #ef4444;">
            <div style="font-size: 1.5rem; font-weight: 800; color: #dc2626;"><?= $g_alpa ?></div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700;">Alpa</div>
        </div>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.02); border-bottom: 4px solid #cbd5e1;">
            <div style="font-size: 1.5rem; font-weight: 800; color: #475569;"><?= $g_belum ?></div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700;">Belum Scan</div>
        </div>
    </div>

    <!-- Progress Guru -->
    <div style="margin-bottom: 35px; background: #f8fafc; padding: 15px; border-radius: 16px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.75rem; font-weight: 800; color: #475569;">
            <span>Persentase Kehadiran</span>
            <span style="color: #10b981;"><?= $guru_aktif > 0 ? round(($g_hadir/$guru_aktif)*100) : 0 ?>%</span>
        </div>
        <div style="width: 100%; height: 10px; background: #e2e8f0; border-radius: 5px; overflow: hidden; display: flex;">
            <div style="height: 100%; background: #10b981; width: <?= $guru_aktif > 0 ? ($g_hadir/$guru_aktif)*100 : 0 ?>%;"></div>
            <div style="height: 100%; background: #f59e0b; width: <?= $guru_aktif > 0 ? ($g_izin/$guru_aktif)*100 : 0 ?>%;"></div>
            <div style="height: 100%; background: #ef4444; width: <?= $guru_aktif > 0 ? ($g_alpa/$guru_aktif)*100 : 0 ?>%;"></div>
        </div>
        <div style="display: flex; gap: 15px; margin-top: 10px; justify-content: center; font-size: 0.7rem; color: #64748b; font-weight: 700;">
            <div style="display: flex; align-items: center; gap: 4px;"><div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div> Hadir</div>
            <div style="display: flex; align-items: center; gap: 4px;"><div style="width: 8px; height: 8px; background: #f59e0b; border-radius: 50%;"></div> Izin/Skt</div>
            <div style="display: flex; align-items: center; gap: 4px;"><div style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%;"></div> Alpa</div>
            <div style="display: flex; align-items: center; gap: 4px;"><div style="width: 8px; height: 8px; background: #cbd5e1; border-radius: 50%;"></div> Blm</div>
        </div>
    </div>

</div>
