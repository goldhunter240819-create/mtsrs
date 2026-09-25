<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <img src="<?= htmlspecialchars($faviconSrc) ?>" alt="Logo">
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Admin Delegasi</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Monitor Scan QR</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding-bottom: 30px;">
    <form method="GET" action="/apk/kamad/monitor-qr-siswa" style="margin-bottom:20px; display:flex; gap:10px;">
        <select name="kelas_id" onchange="this.form.submit()" style="flex:1; padding:12px; border-radius:12px; border:1px solid #e2e8f0; font-weight:700; color:#1e293b; background:#f8fafc;">
            <?php foreach($kelasList as $k): ?>
                <option value="<?= $k['id'] ?>" <?= $k['id'] == $kelas_id ? 'selected' : '' ?>>
                    Kelas <?= htmlspecialchars($k['nama_kelas']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <div style="margin-bottom:20px; font-weight:700; color:#475569; font-size:0.9rem;">
        Log Scan QR Siswa (<?= date('d M Y') ?>)
    </div>

    <?php if(empty($absen_list)): ?>
        <div style="text-align:center; padding:30px; background:#f8fafc; border-radius:15px; color:#94a3b8;">
            <i data-lucide="inbox" style="width:40px; height:40px; margin-bottom:10px;"></i><br>
            Belum ada data scan siswa untuk kelas ini.
        </div>
    <?php else: ?>
        <div style="display:flex; flex-direction:column; gap:12px;">
            <?php foreach($absen_list as $row): 
                $has_scanned = !empty($row['status']);
                $st = $has_scanned ? $row['status'] : 'Belum Scan';
                
                $st_color = '#94a3b8';
                $st_bg = '#f1f5f9';
                if ($st === 'Hadir') { $st_color = '#10b981'; $st_bg = '#ecfdf5'; }
                if ($st === 'Terlambat') { $st_color = '#f59e0b'; $st_bg = '#fffbeb'; }
                if ($st === 'Sakit') { $st_color = '#3b82f6'; $st_bg = '#eff6ff'; }
                if ($st === 'Izin') { $st_color = '#8b5cf6'; $st_bg = '#f5f3ff'; }
                if ($st === 'Alpa' || $st === 'Alpha') { $st_color = '#ef4444'; $st_bg = '#fef2f2'; }
            ?>
            <div style="background:#fff; border:1px solid #f1f5f9; border-radius:15px; padding:15px; box-shadow:0 4px 10px rgba(0,0,0,0.02); display:flex; align-items:center; gap:12px;">
                <?php
                $fotoS = 'https://ui-avatars.com/api/?name='.urlencode($row['nama']).'&background=e2e8f0&color=475569';
                if(!empty($row['foto']) && file_exists(__DIR__.'/../../../public/uploads/siswa/'.$row['foto'])) {
                    $fotoS = '/public/uploads/siswa/'.$row['foto'];
                }
                ?>
                <img src="<?= $fotoS ?>" style="width:45px; height:45px; border-radius:50%; object-fit:cover;">
                
                <div style="flex:1;">
                    <div style="font-weight:800; color:#1e293b; font-size:0.9rem; margin-bottom:3px;"><?= htmlspecialchars($row['nama']) ?></div>
                    <div style="font-size:0.75rem; color:#64748b;"><?= htmlspecialchars($row['nis']) ?></div>
                </div>

                <div style="text-align:right;">
                    <div style="display:inline-block; padding:4px 10px; border-radius:20px; font-size:0.7rem; font-weight:800; background:<?= $st_bg ?>; color:<?= $st_color ?>;">
                        <?= $st ?>
                    </div>
                    <?php if($has_scanned && !empty($row['created_at'])): ?>
                    <div style="font-size:0.7rem; color:#94a3b8; margin-top:5px; font-weight:700;">
                        <i data-lucide="clock" style="width:10px; margin-right:3px;"></i><?= substr($row['created_at'], 11, 5) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    setTimeout(function() {
        lucide.createIcons();
    }, 100);
</script>
