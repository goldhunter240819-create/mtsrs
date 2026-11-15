<div class="apk-header" style="background: transparent; padding-top: 30px; position: absolute; width: 100%; top: 0; z-index: 100;">
    <a href="/apk/aplikasi-saya" style="color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 700; background: rgba(0,0,0,0.2); padding: 8px 15px; border-radius: 20px; backdrop-filter: blur(5px);">
        <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Kembali
    </a>
</div>

<div class="apk-vector-header" style="padding-bottom: 25px; padding-top: 90px; background-color: #3b82f6;">
    <div class="apk-top-logo">
        <div style="width: 45px; height: 45px; border-radius: 12px; background: #fff; color: #3b82f6; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <i data-lucide="calendar-check"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Waka Kurikulum</div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Jadwal KBM: <?= htmlspecialchars($displayHari ?? $pilih_hari) ?></div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; min-height: 70vh; padding: 20px;">
    <!-- Tab Pemilihan Hari -->
    <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 12px; margin-bottom: 15px; scrollbar-width: none; -webkit-overflow-scrolling: touch;">
        <?php 
        $daftar_hari = [
            'Ahad'   => 'Ahad (Minggu)',
            'Senin'  => 'Senin',
            'Selasa' => 'Selasa',
            'Rabu'   => 'Rabu',
            'Kamis'  => 'Kamis',
            'Sabtu'  => 'Sabtu'
        ];
        foreach ($daftar_hari as $hk => $hl):
            $isActive = ($pilih_hari === $hk || ($hk === 'Ahad' && $pilih_hari === 'Minggu'));
        ?>
        <a href="?hari=<?= $hk ?>" style="text-decoration: none; padding: 8px 16px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; white-space: nowrap; transition: all 0.2s; <?= $isActive ? 'background: #2563eb; color: #ffffff; box-shadow: 0 3px 8px rgba(37,99,235,0.3);' : 'background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;' ?>">
            <?= $hl ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Info Jumlah Jadwal -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <span style="font-size: 0.8rem; font-weight: 700; color: #64748b;">
            Hari <?= htmlspecialchars($displayHari ?? $pilih_hari) ?>
        </span>
        <span style="font-size: 0.75rem; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 4px 10px; border-radius: 12px;">
            <?= count($jadwal) ?> Jadwal KBM
        </span>
    </div>

    <?php if (empty($jadwal)): ?>
        <div style="text-align: center; padding: 40px 20px; color: #64748b; background: #fff; border-radius: 16px; border: 1px dashed #cbd5e1;">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 15px; color: #cbd5e1;"></i>
            <h4 style="margin: 0 0 10px 0; color: #1e293b;">Belum Ada Jadwal</h4>
            <p style="margin: 0; font-size: 0.9rem;">Tidak ada jadwal KBM yang ditemukan untuk hari <?= htmlspecialchars($displayHari ?? $pilih_hari) ?>.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($jadwal as $j): ?>
                <div style="background: #fff; padding: 15px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 700; color: #3b82f6; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px;">
                                Jam <?= htmlspecialchars(substr($j['jam_mulai'], 0, 5)) ?> - <?= htmlspecialchars(substr($j['jam_selesai'], 0, 5)) ?>
                            </div>
                            <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: #1e293b;">
                                <?= htmlspecialchars($j['nama_mapel'] ?? 'Mapel Tidak Diketahui') ?>
                            </h4>
                        </div>
                        <div style="background: #f1f5f9; padding: 5px 10px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; color: #475569;">
                            Kelas <?= htmlspecialchars($j['nama_kelas'] ?? '-') ?>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: #64748b;">
                        <i data-lucide="user" style="width: 14px; height: 14px;"></i>
                        <?= htmlspecialchars($j['nama_guru'] ?? 'Guru Belum Ditentukan') ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
