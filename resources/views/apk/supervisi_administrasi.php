<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <img src="<?= htmlspecialchars($faviconSrc) ?>" alt="Logo">
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Waka Kurikulum</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Supervisi Adm.</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding-bottom: 30px;">
    
    <div style="margin-bottom: 20px;">
        <h3 style="margin: 0 0 5px; color: #1e293b; font-size: 1.1rem; font-weight: 800;">Daftar Guru</h3>
        <p style="margin: 0; color: #64748b; font-size: 0.8rem; font-weight: 600;">Klik evaluasi untuk mengecek kelengkapan administrasi guru.</p>
    </div>

    <?php if(empty($guru_list)): ?>
        <div style="text-align:center; padding:30px; background:#f8fafc; border-radius:15px; color:#94a3b8; margin-top:20px;">
            <i data-lucide="users" style="width:40px; height:40px; margin-bottom:10px;"></i><br>
            Belum ada data guru.
        </div>
    <?php else: ?>
        <div style="display:flex; flex-direction:column; gap:12px;">
            <?php foreach($guru_list as $g): ?>
                <div style="background:#fff; border:1px solid #e2e8f0; border-radius:15px; padding:15px; display:flex; flex-direction:column; gap:12px; box-shadow:0 2px 4px rgba(0,0,0,0.02);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div style="font-weight:800; color:#1e293b; font-size:0.95rem; margin-bottom:3px;"><?= htmlspecialchars($g['nama']) ?></div>
                            <div style="font-size:0.75rem; color:#64748b; font-weight:700;">NIP: <?= htmlspecialchars($g['nip'] ?? '-') ?></div>
                        </div>
                        <?php if($g['supervisi_id']): ?>
                            <div style="background:#dcfce7; color:#16a34a; padding:4px 10px; border-radius:20px; font-size:0.7rem; font-weight:800; display:flex; align-items:center; gap:4px;">
                                <i data-lucide="check-circle" style="width:12px; height:12px;"></i> Selesai
                            </div>
                        <?php else: ?>
                            <div style="background:#fef2f2; color:#ef4444; padding:4px 10px; border-radius:20px; font-size:0.7rem; font-weight:800; display:flex; align-items:center; gap:4px;">
                                <i data-lucide="clock" style="width:12px; height:12px;"></i> Belum
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px dashed #e2e8f0; padding-top:10px;">
                        <div>
                            <?php if($g['supervisi_id']): ?>
                                <div style="font-size:0.75rem; color:#64748b; font-weight:600;">Skor: <strong style="color:#1e293b;"><?= number_format($g['nilai_akhir'], 1) ?></strong> / 100</div>
                            <?php else: ?>
                                <div style="font-size:0.75rem; color:#94a3b8; font-weight:600; font-style:italic;">Menunggu Evaluasi</div>
                            <?php endif; ?>
                        </div>
                        <a href="/apk/supervisi-administrasi/evaluasi?guru_id=<?= $g['id'] ?>" style="background:<?= $g['supervisi_id'] ? '#f8fafc' : '#3b82f6' ?>; color:<?= $g['supervisi_id'] ? '#64748b' : 'white' ?>; border:<?= $g['supervisi_id'] ? '1px solid #e2e8f0' : 'none' ?>; padding:8px 16px; border-radius:10px; font-size:0.8rem; font-weight:800; text-decoration:none; display:flex; align-items:center; gap:5px; transition:all 0.2s;">
                            <i data-lucide="clipboard-edit" style="width:14px; height:14px;"></i> <?= $g['supervisi_id'] ? 'Ubah' : 'Evaluasi' ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    lucide.createIcons();
</script>
