<?php
use App\Core\Helper;
?>
<div class="apk-vector-header">
    <div class="apk-top-logo">
        <img src="<?= htmlspecialchars($faviconSrc) ?>" alt="Logo">
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; opacity: 0.8; letter-spacing: 0.5px;">Selamat Datang,</div>
            <div style="font-size: 1.2rem; font-weight: 800; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($siswa['nama']) ?></div>
        </div>
    </div>
    
    <div style="position: relative;">
        <?php if(!empty($siswa['foto'])): ?>
            <img src="<?= Helper::url('/uploads/siswa/' . $siswa['foto']) ?>" alt="Foto" class="apk-avatar-top">
        <?php else: ?>
            <div class="apk-avatar-top" style="background: rgba(255,255,255,0.2); display:flex; justify-content:center; align-items:center; color: #fff;">
                <i data-lucide="user"></i>
            </div>
        <?php endif; ?>
        <div style="position: absolute; bottom: -2px; right: -2px; width: 14px; height: 14px; background: #10b981; border-radius: 50%; border: 3px solid var(--apk-primary);"></div>
    </div>
</div>

<div class="apk-main-board">
    <!-- Info Banner -->
    <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 20px; padding: 20px; color: white; box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3); margin-bottom: 20px; position: relative; overflow: hidden;">
        <div style="position: relative; z-index: 2;">
            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 800; opacity: 0.9; margin-bottom: 5px;">Tahun Ajaran Aktif</div>
            <div style="font-size: 1.2rem; font-weight: 800; margin-bottom: 15px;"><?= htmlspecialchars($tahun_ajaran_aktif ?? 'Semester Genap') ?></div>
            <div style="display: flex; gap: 10px;">
                <div style="background: rgba(255,255,255,0.2); backdrop-filter: blur(5px); padding: 5px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; display: flex; align-items: center; gap: 5px;">
                    <i data-lucide="shield-check" style="width: 14px; height: 14px;"></i> Data Terenkripsi
                </div>
            </div>
        </div>
        <i data-lucide="graduation-cap" style="position: absolute; right: -20px; bottom: -20px; width: 120px; height: 120px; opacity: 0.1; transform: rotate(-15deg);"></i>
    </div>

    <!-- Quick Stats -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
        <!-- Card Tagihan -->
        <a href="/apk/tagihan" style="background: #fff; border-radius: 20px; padding: 15px; text-decoration: none; color: inherit; box-shadow: 0 5px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 10px; transition: transform 0.2s;">
            <div style="background: #e0f2fe; color: #0284c7; width: 38px; height: 38px; border-radius: 12px; display: flex; justify-content: center; align-items: center;">
                <i data-lucide="receipt"></i>
            </div>
            <div>
                <div style="font-size: 0.7rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Tagihan Aktif</div>
                <?php if ($total_tunggakan <= 0): ?>
                    <div style="font-size: 1rem; font-weight: 800; color: #10b981; display: flex; align-items: center; gap: 4px;"><i data-lucide="check-circle-2" style="width: 16px; height: 16px;"></i> Lunas</div>
                <?php else: ?>
                    <div style="font-size: 1rem; font-weight: 800; color: #ef4444;">Rp <?= number_format($total_tunggakan, 0, ',', '.') ?></div>
                <?php endif; ?>
            </div>
        </a>

        <!-- Card Absen -->
        <a href="/apk/absen" style="background: #fff; border-radius: 20px; padding: 15px; text-decoration: none; color: inherit; box-shadow: 0 5px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 10px; transition: transform 0.2s;">
            <div style="background: #ecfdf5; color: #10b981; width: 38px; height: 38px; border-radius: 12px; display: flex; justify-content: center; align-items: center;">
                <i data-lucide="scan-face"></i>
            </div>
            <div>
                <div style="font-size: 0.7rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Absen Hari Ini</div>
                <div style="font-size: 1rem; font-weight: 800; color: <?= ($absen_hari_ini['kode']=='H' ? '#10b981' : ($absen_hari_ini['kode']=='-' ? '#f59e0b' : '#ef4444')) ?>;">
                    <?= htmlspecialchars($absen_hari_ini['status']) ?>
                </div>
            </div>
        </a>
    </div>

    <!-- Mading Pengumuman -->
    <?php if (count($pengumumans) > 0): ?>
    <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="megaphone" style="color: #f59e0b; width: 20px;"></i> Pengumuman
    </h3>
    <div style="margin-bottom: 25px;">
        <?php foreach ($pengumumans as $p): ?>
        <div style="background: #ffffff; border-radius: 16px; padding: 15px; margin-bottom: 10px; border-left: 4px solid #3b82f6; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-top: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($p['judul']) ?></h4>
                <div style="font-size: 0.7rem; color: #94a3b8; white-space: nowrap; margin-left: 10px;"><i data-lucide="clock" style="width: 12px; height: 12px; display: inline; vertical-align: middle;"></i> <?= date('d M', strtotime($p['tanggal_dibuat'])) ?></div>
            </div>
            <p style="margin: 0; font-size: 0.85rem; color: #64748b; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($p['pesan']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Widget Area -->
    <div style="margin-bottom: 15px;">
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="book-open" style="color: var(--apk-primary); width: 20px;"></i> Pelajaran Hari Ini
        </h3>
        
        <div style="background: #fff; border-radius: 20px; padding: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; text-align: center; color: #64748b;">
            <div style="background: #f1f5f9; width: 50px; height: 50px; border-radius: 15px; display: flex; justify-content: center; align-items: center; margin: 0 auto 10px;">
                <i data-lucide="calendar-x" style="color: #94a3b8;"></i>
            </div>
            <p style="font-weight: 700; font-size: 0.9rem; color: #475569; margin-bottom: 4px;">Tidak ada jadwal hari ini</p>
            <p style="font-size: 0.8rem; margin: 0;">Selamat beristirahat atau belajar mandiri!</p>
        </div>
    </div>
</div>

<script>
    if (typeof Android !== "undefined" && typeof Android.setOneSignalUser === "function") {
        Android.setOneSignalUser("siswa_<?= addslashes($siswa_id) ?>");
    }
</script>
