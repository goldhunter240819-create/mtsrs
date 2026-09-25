<div class="apk-vector-header" style="padding-bottom: 70px; justify-content: center; text-align: center; position: relative; background-image: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
    <div>
        <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Monitor Dana BOS</div>
        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.8); margin-top: 5px;">Rekapitulasi Kas Operasional Sekolah</div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -30px; border-radius: 30px 30px 0 0; min-height: 70vh;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-left: 5px;">
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0;">Saldo Kas Utama</h3>
        <div style="font-size: 0.75rem; font-weight: 800; background: #eef2ff; color: #4f46e5; padding: 4px 10px; border-radius: 12px;"><?= date('Y') ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 20px; padding: 25px; color: white; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.3); margin-bottom: 25px; position: relative; overflow: hidden;">
        <div style="position: relative; z-index: 2;">
            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; opacity: 0.7; margin-bottom: 5px;">Total Saldo Tersedia</div>
            <div style="font-size: 1.8rem; font-weight: 800; margin-bottom: 20px; letter-spacing: -0.5px;">Rp 45.750.000</div>
            
            <div style="display: flex; gap: 15px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                <div style="flex: 1;">
                    <div style="font-size: 0.65rem; opacity: 0.7; margin-bottom: 3px;">Kas Masuk (YTD)</div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: #34d399;">+ Rp 120.000.000</div>
                </div>
                <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
                <div style="flex: 1;">
                    <div style="font-size: 0.65rem; opacity: 0.7; margin-bottom: 3px;">Kas Keluar (YTD)</div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: #f87171;">- Rp 74.250.000</div>
                </div>
            </div>
        </div>
        <i data-lucide="pie-chart" style="position: absolute; right: -20px; bottom: -20px; width: 150px; height: 150px; opacity: 0.05; transform: rotate(-15deg);"></i>
    </div>

    <h3 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-bottom: 15px; padding-left: 5px;">Pengeluaran Terakhir</h3>
    
    <div style="display: flex; flex-direction: column; gap: 12px;">
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #64748b;">
                    <i data-lucide="shopping-cart" style="width: 20px;"></i>
                </div>
                <div>
                    <div style="font-size: 0.85rem; font-weight: 800; color: #1e293b;">Belanja ATK</div>
                    <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 2px;">02 Agustus 2026</div>
                </div>
            </div>
            <div style="font-size: 0.85rem; font-weight: 800; color: #ef4444;">- Rp 550.000</div>
        </div>

        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #64748b;">
                    <i data-lucide="monitor" style="width: 20px;"></i>
                </div>
                <div>
                    <div style="font-size: 0.85rem; font-weight: 800; color: #1e293b;">Pemeliharaan Lab</div>
                    <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 2px;">28 Juli 2026</div>
                </div>
            </div>
            <div style="font-size: 0.85rem; font-weight: 800; color: #ef4444;">- Rp 1.200.000</div>
        </div>
    </div>

</div>
