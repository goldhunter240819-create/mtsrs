<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <a href="/apk/aplikasi-saya" style="color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 12px; margin-right: 15px;">
            <i data-lucide="arrow-left"></i>
        </a>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Guru Mata Pelajaran</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Nilai Rapor</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0;">
    
    <div style="margin-bottom: 20px;">
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0 0 5px 0; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="book-open" style="width: 20px; color: #0ea5e9;"></i> Mata Pelajaran Saya
        </h3>
        <p style="font-size: 0.8rem; color: #64748b; margin: 0;">Pilih kelas dan mata pelajaran untuk memonitor nilai rapor siswa.</p>
    </div>

    <div style="display: flex; flex-direction: column; gap: 12px; padding-bottom: 80px;">
        <?php if (empty($kombinasi_mengajar)): ?>
            <div style="text-align: center; padding: 40px 20px;">
                <div style="width: 60px; height: 60px; border-radius: 20px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: #94a3b8;">
                    <i data-lucide="folder-open" style="width: 30px; height: 30px;"></i>
                </div>
                <h4 style="margin: 0 0 5px 0; font-size: 0.95rem; color: #1e293b; font-weight: 700;">Belum Ada Jadwal</h4>
                <p style="margin: 0; font-size: 0.8rem; color: #64748b;">Anda belum memiliki jadwal mengajar pada tahun ajaran ini.</p>
            </div>
        <?php else: ?>
            <?php foreach ($kombinasi_mengajar as $km): ?>
                <a href="/apk/e-rapor/detail?kelas_id=<?= $km['kelas_id'] ?>&mapel_id=<?= $km['mapel_id'] ?>" style="display: flex; align-items: center; padding: 16px; background: #fff; border-radius: 16px; border: 1px solid #f1f5f9; box-shadow: 0 4px 15px rgba(0,0,0,0.02); text-decoration: none; color: inherit;">
                    <div style="width: 45px; height: 45px; border-radius: 12px; background: #fdf4ff; display: flex; align-items: center; justify-content: center; color: #d946ef; margin-right: 15px;">
                        <i data-lucide="book" style="width: 22px;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 4px;"><?= htmlspecialchars($km['nama_mapel']) ?></div>
                        <div style="display: inline-block; padding: 2px 8px; background: #e0f2fe; color: #0284c7; border-radius: 6px; font-size: 0.7rem; font-weight: 700;">
                            <?= htmlspecialchars($km['nama_kelas']) ?>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" style="color: #cbd5e1; width: 18px;"></i>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
