<div class="apk-header" style="background: transparent; padding-top: 30px; position: absolute; width: 100%; top: 0; z-index: 100;">
    <a href="/apk/aplikasi-saya" style="color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 700; background: rgba(0,0,0,0.2); padding: 8px 15px; border-radius: 20px; backdrop-filter: blur(5px);">
        <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Kembali
    </a>
</div>

<div class="apk-vector-header" style="padding-bottom: 25px; padding-top: 90px; background-color: #0ea5e9;">
    <div class="apk-top-logo">
        <div style="width: 45px; height: 45px; border-radius: 12px; background: #fff; color: #0ea5e9; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <i data-lucide="arrow-right-left"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Waka Kesiswaan</div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Mutasi Siswa</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; min-height: 70vh; padding: 20px;">
    <?php if (empty($mutasi)): ?>
        <div style="text-align: center; padding: 40px 20px; color: #64748b;">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 15px; color: #cbd5e1;"></i>
            <h4 style="margin: 0 0 10px 0;">Belum Ada Data</h4>
            <p style="margin: 0; font-size: 0.9rem;">Belum ada catatan mutasi siswa yang ditemukan.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($mutasi as $m): ?>
                <?php 
                $isMasuk = strtolower($m['jenis_mutasi']) == 'masuk';
                $bgColor = $isMasuk ? '#f0fdf4' : '#fef2f2';
                $borderColor = $isMasuk ? 'rgba(34,197,94,0.2)' : 'rgba(239,68,68,0.2)';
                $textColor = $isMasuk ? '#16a34a' : '#dc2626';
                $icon = $isMasuk ? 'log-in' : 'log-out';
                ?>
                <div style="background: <?= $bgColor ?>; padding: 15px; border-radius: 16px; border: 1px solid <?= $borderColor ?>; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 3px;">
                                <?= date('d M Y', strtotime($m['tanggal_mutasi'])) ?>
                            </div>
                            <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: #1e293b;">
                                <?= htmlspecialchars($m['nama_siswa'] ?? 'Siswa Tidak Diketahui') ?>
                            </h4>
                            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">NISN: <?= htmlspecialchars($m['nisn'] ?? '-') ?></div>
                        </div>
                        <div style="background: #fff; padding: 5px 10px; border-radius: 8px; font-weight: 800; font-size: 0.8rem; color: <?= $textColor ?>; border: 1px solid <?= $borderColor ?>; display: flex; align-items: center; gap: 5px; text-transform: uppercase;">
                            <i data-lucide="<?= $icon ?>" style="width: 14px; height: 14px;"></i> <?= htmlspecialchars($m['jenis_mutasi']) ?>
                        </div>
                    </div>
                    <?php if(!empty($m['sekolah_asal_tujuan'])): ?>
                    <div style="font-size: 0.85rem; color: #475569; margin-bottom: 5px;">
                        <strong>Sekolah <?= $isMasuk ? 'Asal' : 'Tujuan' ?>:</strong> <?= htmlspecialchars($m['sekolah_asal_tujuan']) ?>
                    </div>
                    <?php endif; ?>
                    <?php if(!empty($m['alasan'])): ?>
                    <div style="font-size: 0.85rem; color: #64748b; font-style: italic;">
                        "<?= htmlspecialchars($m['alasan']) ?>"
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
