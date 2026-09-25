<div class="apk-vector-header" style="padding-bottom: 70px; display: flex; align-items: flex-start; position: relative; background-image: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);">
    <a href="javascript:history.back()" style="color: #fff; margin-top: 2px;"><i data-lucide="arrow-left" style="width: 24px; height: 24px;"></i></a>
    <div style="flex: 1; text-align: center;">
        <div style="font-size: 1.1rem; font-weight: 700; color: #fff;">Monitoring Keuangan</div>
    </div>
    <div style="width: 24px;"></div> <!-- placeholder for centering -->
</div>

<div class="apk-main-board" style="margin-top: -30px; border-radius: 30px 30px 0 0; min-height: 70vh;">
    
    <div style="margin-bottom: 20px; padding-left: 5px;">
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; display: inline-block;">Keuangan Komite</h3>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
        <div style="background: #fff; border: 1px solid #e2e8f0; border-left: 4px solid #10b981; border-radius: 12px; padding: 15px 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
            <div style="font-size: 0.65rem; color: #64748b; font-weight: 700; margin-bottom: 5px;">Total Saldo Kas</div>
            <div style="font-size: 0.95rem; font-weight: 800; color: #10b981;">Rp <?= number_format($totalSaldoKas ?? 0, 0, ',', '.') ?></div>
        </div>

        <div style="background: #fff; border: 1px solid #e2e8f0; border-left: 4px solid #ef4444; border-radius: 12px; padding: 15px 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
            <div style="font-size: 0.65rem; color: #64748b; font-weight: 700; margin-bottom: 5px;">Tagihan Belum Lunas</div>
            <div style="font-size: 0.95rem; font-weight: 800; color: #ef4444;"><?= number_format($tagihanBelumLunas ?? 0, 0, ',', '.') ?> <span style="font-size: 0.65rem; font-weight: 600; color: #94a3b8;">tagihan</span></div>
        </div>
    </div>

    <h3 style="font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 15px; padding-left: 5px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 10px;">Saldo Kas Per Kategori</h3>
    
    <div style="display: flex; flex-direction: column; gap: 0;">
        <?php if(!empty($kategoriData)): ?>
            <?php foreach($kategoriData as $kd): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 5px; border-bottom: 1px solid #f1f5f9;">
                <div style="flex: 1;">
                    <div style="font-size: 0.8rem; font-weight: 800; color: #334155; margin-bottom: 4px; text-transform: uppercase;"><?= htmlspecialchars($kd['nama_kategori']) ?></div>
                    <div style="font-size: 0.6rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 2px;">PETUGAS</div>
                    <div style="display: flex; flex-direction: column; gap: 3px;">
                        <?php foreach($kd['petugas'] as $ptgs): ?>
                        <div style="font-size: 0.65rem; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 4px;">
                            <i data-lucide="user" style="width: 12px; height: 12px; color: #7c3aed;"></i> <?= htmlspecialchars($ptgs) ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 0.85rem; font-weight: 800; color: #0f172a; margin-bottom: 4px;">Rp <?= number_format($kd['saldo'], 0, ',', '.') ?></div>
                    <div style="font-size: 0.65rem; font-weight: 700; color: #10b981; margin-bottom: 2px;">Masuk: Rp <?= number_format($kd['masuk'], 0, ',', '.') ?></div>
                    <div style="font-size: 0.65rem; font-weight: 700; color: #ef4444;">Keluar: Rp <?= number_format($kd['keluar'], 0, ',', '.') ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 30px 0;">
                <p style="font-size: 0.8rem; color: #94a3b8; margin: 0;">Belum ada data kategori.</p>
            </div>
        <?php endif; ?>
    </div>

    <br><br>
</div>
