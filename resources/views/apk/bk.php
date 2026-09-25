<div class="apk-header" style="background: transparent; padding-top: 30px; position: absolute; width: 100%; top: 0; z-index: 100;">
    <a href="/apk/aplikasi-saya" style="color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 700; background: rgba(0,0,0,0.2); padding: 8px 15px; border-radius: 20px; backdrop-filter: blur(5px);">
        <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Kembali
    </a>
</div>

<div class="apk-vector-header" style="padding-bottom: 25px; padding-top: 90px; background-color: #059669;">
    <div class="apk-top-logo">
        <div style="width: 45px; height: 45px; border-radius: 12px; background: #fff; color: #059669; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <i data-lucide="heart-handshake"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Aplikasi Khusus</div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Bimbingan Konseling</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; min-height: 70vh;">
    <h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 20px; margin-top: 0;">Dashboard BK</h3>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
        <div style="background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%); padding: 20px; border-radius: 20px; border: 1px solid rgba(6,182,212,0.1); box-shadow: 0 10px 20px -5px rgba(6,182,212,0.15);">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: #0891b2; color: #fff; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                <i data-lucide="messages-square" style="width: 20px; height: 20px;"></i>
            </div>
            <div style="font-size: 1.8rem; font-weight: 800; color: #164e63; line-height: 1;"><?= $sesiKonseling ?></div>
            <div style="font-size: 0.75rem; font-weight: 600; color: #0891b2; margin-top: 5px;">Sesi Konseling</div>
        </div>
        
        <div style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); padding: 20px; border-radius: 20px; border: 1px solid rgba(220,38,38,0.1); box-shadow: 0 10px 20px -5px rgba(220,38,38,0.15);">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: #dc2626; color: #fff; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                <i data-lucide="siren" style="width: 20px; height: 20px;"></i>
            </div>
            <div style="font-size: 1.8rem; font-weight: 800; color: #7f1d1d; line-height: 1;"><?= $perluPerhatian ?></div>
            <div style="font-size: 0.75rem; font-weight: 600; color: #dc2626; margin-top: 5px;">Siswa Perlu Perhatian</div>
        </div>
    </div>

    <div style="background: #fff; border-radius: 20px; padding: 5px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;">
        <a href="/apk/bk/jurnal" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; text-decoration: none; border-bottom: 1px solid #f1f5f9;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: #f0fdfa; color: #14b8a6; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="users" style="width: 20px; height: 20px;"></i>
                </div>
                <div>
                    <div style="font-weight: 800; color: #1e293b; font-size: 0.95rem;">Jurnal Konseling</div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Catat sesi & tindak lanjut</div>
                </div>
            </div>
            <i data-lucide="chevron-right" style="color: #cbd5e1; width: 20px; height: 20px;"></i>
        </a>
        <a href="/apk/bk/poin" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; text-decoration: none;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: #ede9fe; color: #8b5cf6; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="award" style="width: 20px; height: 20px;"></i>
                </div>
                <div>
                    <div style="font-weight: 800; color: #1e293b; font-size: 0.95rem;">Poin Kedisiplinan</div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Pelanggaran & prestasi</div>
                </div>
            </div>
            <i data-lucide="chevron-right" style="color: #cbd5e1; width: 20px; height: 20px;"></i>
        </a>
    </div>

</div>
