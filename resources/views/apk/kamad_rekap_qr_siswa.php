<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <img src="<?= htmlspecialchars($faviconSrc) ?>" alt="Logo">
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Admin Delegasi</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Rekap QR Siswa</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding-bottom: 30px;">
    
    <form method="GET" action="/apk/kamad/rekap-qr-siswa" style="margin-bottom:20px;">
        <div style="display:flex; gap:10px; margin-bottom:10px;">
            <div style="flex:1;">
                <label style="font-size:0.7rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Pilih Kelas</label>
                <select name="kelas_id" style="width:100%; padding:10px; border-radius:12px; border:1px solid #e2e8f0; font-weight:700; color:#1e293b; background:#f8fafc;">
                    <?php foreach($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $kelas_id ? 'selected' : '' ?>>
                            Kelas <?= htmlspecialchars($c['nama_kelas']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div style="display:flex; gap:10px; margin-bottom:15px;">
            <div style="flex:1;">
                <label style="font-size:0.7rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Dari Tanggal</label>
                <input type="date" name="start_date" value="<?= $start_date ?>" style="width:100%; padding:10px; border-radius:12px; border:1px solid #e2e8f0; font-weight:700; color:#1e293b; background:#f8fafc;">
            </div>
            <div style="flex:1;">
                <label style="font-size:0.7rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Sampai Tanggal</label>
                <input type="date" name="end_date" value="<?= $end_date ?>" style="width:100%; padding:10px; border-radius:12px; border:1px solid #e2e8f0; font-weight:700; color:#1e293b; background:#f8fafc;">
            </div>
        </div>
        <button type="submit" style="width:100%; background:linear-gradient(135deg, #0ea5e9, #0284c7); color:white; padding:12px; border:none; border-radius:12px; font-weight:800; display:flex; align-items:center; justify-content:center; gap:8px;">
            <i data-lucide="filter" style="width:18px;"></i> Tampilkan Data
        </button>
    </form>

    <?php if($kelas_id && !empty($absen_list)): ?>
        <div style="margin-bottom:15px; font-weight:800; color:#475569; font-size:0.9rem; padding-bottom:10px; border-bottom:2px dashed #e2e8f0;">
            Rekap QR: Kelas <?= htmlspecialchars($selected_class_name) ?>
        </div>

        <div style="display:flex; flex-direction:column; gap:12px;">
            <?php foreach($absen_list as $a): ?>
            <div style="background:#fff; border:1px solid #f1f5f9; border-radius:15px; padding:15px; box-shadow:0 4px 10px rgba(0,0,0,0.02); display:flex; gap:15px;">
                <?php
                $foto_s = 'https://ui-avatars.com/api/?name='.urlencode($a['nama']).'&background=e2e8f0&color=475569';
                if(!empty($a['foto']) && file_exists(__DIR__.'/../../../public/uploads/siswa/'.$a['foto'])) {
                    $foto_s = '/public/uploads/siswa/'.$a['foto'];
                }
                ?>
                <img src="<?= $foto_s ?>" style="width:50px; height:50px; border-radius:50%; object-fit:cover; margin-top:5px;">
                
                <div style="flex:1;">
                    <div style="font-weight:800; color:#1e293b; font-size:0.9rem; margin-bottom:3px;"><?= htmlspecialchars($a['nama']) ?></div>
                    <div style="font-size:0.75rem; color:#64748b; margin-bottom:10px;">NIS: <?= htmlspecialchars($a['nis']) ?></div>
                    
                    <div style="display:grid; grid-template-columns:repeat(5, 1fr); gap:5px; text-align:center;">
                        <div style="background:#ecfdf5; border:1px solid #d1fae5; border-radius:8px; padding:5px 0;">
                            <div style="font-size:0.6rem; color:#059669; font-weight:800; text-transform:uppercase;">H</div>
                            <div style="font-size:0.9rem; color:#047857; font-weight:800; margin-top:2px;"><?= $a['hadir'] ?></div>
                        </div>
                        <div style="background:#fffbeb; border:1px solid #fef3c7; border-radius:8px; padding:5px 0;">
                            <div style="font-size:0.6rem; color:#d97706; font-weight:800; text-transform:uppercase;">T</div>
                            <div style="font-size:0.9rem; color:#b45309; font-weight:800; margin-top:2px;"><?= $a['terlambat'] ?></div>
                        </div>
                        <div style="background:#eff6ff; border:1px solid #dbeafe; border-radius:8px; padding:5px 0;">
                            <div style="font-size:0.6rem; color:#2563eb; font-weight:800; text-transform:uppercase;">S</div>
                            <div style="font-size:0.9rem; color:#1d4ed8; font-weight:800; margin-top:2px;"><?= $a['sakit'] ?></div>
                        </div>
                        <div style="background:#f5f3ff; border:1px solid #ede9fe; border-radius:8px; padding:5px 0;">
                            <div style="font-size:0.6rem; color:#7c3aed; font-weight:800; text-transform:uppercase;">I</div>
                            <div style="font-size:0.9rem; color:#6d28d9; font-weight:800; margin-top:2px;"><?= $a['izin'] ?></div>
                        </div>
                        <div style="background:#fef2f2; border:1px solid #fee2e2; border-radius:8px; padding:5px 0;">
                            <div style="font-size:0.6rem; color:#dc2626; font-weight:800; text-transform:uppercase;">A</div>
                            <div style="font-size:0.9rem; color:#b91c1c; font-weight:800; margin-top:2px;"><?= $a['alpa'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align:center; padding:30px; background:#f8fafc; border-radius:15px; color:#94a3b8; margin-top:20px;">
            <i data-lucide="calendar-x" style="width:40px; height:40px; margin-bottom:10px;"></i><br>
            Tidak ada data rekap QR untuk kelas dan tanggal tersebut.
        </div>
    <?php endif; ?>
</div>

<script>
    setTimeout(function() {
        lucide.createIcons();
    }, 100);
</script>
