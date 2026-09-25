<div class="apk-header" style="background: transparent; padding-top: 30px; position: absolute; width: 100%; top: 0; z-index: 100;">
    <a href="/apk/aplikasi-saya" style="color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 700; background: rgba(0,0,0,0.2); padding: 8px 15px; border-radius: 20px; backdrop-filter: blur(5px);">
        <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Kembali
    </a>
</div>

<div class="apk-vector-header" style="padding-bottom: 25px; padding-top: 90px; background-color: #f43f5e;">
    <div class="apk-top-logo">
        <div style="width: 45px; height: 45px; border-radius: 12px; background: #fff; color: #f43f5e; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <i data-lucide="book-open-check"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Aplikasi Khusus</div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Waka Kurikulum</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; min-height: 70vh;">
    <h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 20px; margin-top: 0;">Dashboard Kurikulum</h3>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
        <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); padding: 20px; border-radius: 20px; border: 1px solid rgba(59,130,246,0.1); box-shadow: 0 10px 20px -5px rgba(59,130,246,0.15);">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: #3b82f6; color: #fff; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                <i data-lucide="users" style="width: 20px; height: 20px;"></i>
            </div>
            <div style="font-size: 1.8rem; font-weight: 800; color: #1e3a8a; line-height: 1;"><?= $guruHadir ?></div>
            <div style="font-size: 0.75rem; font-weight: 600; color: #3b82f6; margin-top: 5px;">Guru Hadir Hari Ini</div>
        </div>
        
        <div style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); padding: 20px; border-radius: 20px; border: 1px solid rgba(239,68,68,0.1); box-shadow: 0 10px 20px -5px rgba(239,68,68,0.15);">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: #ef4444; color: #fff; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                <i data-lucide="door-open" style="width: 20px; height: 20px;"></i>
            </div>
            <div style="font-size: 1.8rem; font-weight: 800; color: #7f1d1d; line-height: 1;"><?= $kelasKosong ?></div>
            <div style="font-size: 0.75rem; font-weight: 600; color: #ef4444; margin-top: 5px;">Kelas Kosong</div>
        </div>
    </div>

</div>
