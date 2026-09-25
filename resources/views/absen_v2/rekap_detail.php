<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" onclick="zToggleSidebar()" style="background:none;border:none;cursor:pointer;color:#64748b;">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <span style="font-weight:800;letter-spacing:1px;font-size:0.8rem;color:#10b981;">ABSEN V2</span>
                    <i data-lucide="chevron-right" style="width:14px;color:#cbd5e1;"></i>
                    <span style="font-weight:700;color:#1e293b;">Rekap Detail (Timeline)</span>
                </div>
            </div>
            <div class="z-header-right" style="display:flex;align-items:center;gap:12px;">
                <div style="width:36px;height:36px;background:#10b981;color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;"><?php echo $initial; ?></div>
                <div>
                    <div style="font-weight:800;font-size:0.9rem;color:#0f172a;"><?php echo $nama; ?></div>
                </div>
            </div>
        </header>

        <div class="z-scroll" style="padding: 2rem;">
            <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap: wrap; gap: 15px;">
                    <h2 style="margin:0; font-size:1.2rem; font-weight:800; color:#1e293b; display:flex; align-items:center; gap:8px;">
                        <i data-lucide="git-commit" style="color:#3b82f6;"></i> Jejak Absensi Siswa
                    </h2>
                    <form method="GET" style="display:flex; gap:10px; flex-wrap: wrap;">
                        <input type="date" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none;">
                        <select name="kelas_id" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none; min-width: 150px;">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach($kelasList as $k): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo $kelas_id == $k['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" style="padding:8px 16px; background:#10b981; color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer;">Filter</button>
                        <a href="/absen/rekap-detail/cetak?tanggal=<?php echo $tanggal; ?>&kelas_id=<?php echo htmlspecialchars($kelas_id); ?>" target="_blank" style="padding:8px 16px; background:#3b82f6; color:white; border:none; border-radius:8px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px;">
                            <i data-lucide="printer" style="width:16px; height:16px;"></i> Cetak
                        </a>
                    </form>
                </div>
                
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px;">
                    <div style="font-weight: 700; color: #16a34a; font-size: 0.85rem; margin-bottom: 6px; display:flex; align-items:center; gap:6px;">
                        <i data-lucide="info" style="width:16px; height:16px;"></i> Panduan Kolom Timeline:
                    </div>
                    <ul style="margin: 0; padding-left: 20px; color: #15803d; font-size: 0.8rem; line-height: 1.6;">
                        <li><b>Masuk:</b> Jam scan QR di gerbang ('-' jika tidak scan gerbang)</li>
                        <li><b>Mapel:</b> Presensi kelas dari Jurnal Mengajar Guru</li>
                        <li><b>Status Pusat:</b> Status akhir kehadiran siswa</li>
                    </ul>
                </div>

                <?php if(empty($kelas_id)): ?>
                    <div style="padding:40px 20px; text-align:center; color:#94a3b8; font-weight:600; border:2px dashed #e2e8f0; border-radius:12px;">
                        Silakan pilih kelas dan tanggal untuk melihat jejak absensi siswa.
                    </div>
                <?php else: ?>
                    <div style="overflow-x:auto;">
                        <table style="width:100%; border-collapse:collapse; min-width:800px; white-space: nowrap;">
                            <thead>
                                <tr>
                                    <th colspan="<?php echo 3 + count($mapelColumns); ?>" style="background: #f8fafc; border:1px solid #e2e8f0; padding:10px; text-align:center; color:#3b82f6; font-weight:800; font-size: 0.85rem;">
                                        TIMELINE KEHADIRAN (<?php echo date('d F Y', strtotime($tanggal)); ?>) - KELAS <?php echo htmlspecialchars($selectedKelas); ?>
                                    </th>
                                </tr>
                                <tr style="background:#f1f5f9; text-align:center;">
                                    <th style="padding:12px; color:#64748b; font-size:0.75rem; font-weight:800; border:1px solid #e2e8f0; width:50px;">NO</th>
                                    <th style="padding:12px; color:#64748b; font-size:0.75rem; font-weight:800; border:1px solid #e2e8f0; text-align:left;">NAMA SISWA</th>
                                    <th style="padding:12px; color:#10b981; font-size:0.75rem; font-weight:800; border:1px solid #e2e8f0;">
                                        <i data-lucide="log-in" style="width:14px; height:14px; margin:0 auto; display:block; margin-bottom:4px;"></i>
                                        MASUK
                                    </th>
                                    
                                    <?php foreach($mapelColumns as $mc): ?>
                                    <th style="padding:12px; color:#6366f1; font-size:0.75rem; font-weight:800; border:1px solid #e2e8f0;" title="<?php echo htmlspecialchars($mc['nama_mapel']); ?>">
                                        <?php echo htmlspecialchars($mc['kode_mapel'] ?: substr($mc['nama_mapel'], 0, 7)); ?>
                                    </th>
                                    <?php endforeach; ?>
                                    
                                    <th style="padding:12px; color:#0f172a; font-size:0.75rem; font-weight:800; border:1px solid #e2e8f0;">STATUS PUSAT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($timelineData)): ?>
                                <tr>
                                    <td colspan="<?php echo 3 + count($mapelColumns); ?>" style="padding:24px; text-align:center; color:#94a3b8; font-weight:600; border:1px solid #e2e8f0;">Data siswa tidak ditemukan.</td>
                                </tr>
                                <?php else: ?>
                                    <?php $no=1; foreach($timelineData as $td): ?>
                                    <tr style="border-bottom:1px solid #e2e8f0; text-align:center; background:#fff;">
                                        <td style="padding:10px; color:#475569; border:1px solid #e2e8f0;"><?php echo $no++; ?></td>
                                        <td style="padding:10px; font-weight:700; color:#1e293b; text-align:left; border:1px solid #e2e8f0;"><?php echo htmlspecialchars($td['nama']); ?></td>
                                        
                                        <td style="padding:10px; color:#059669; font-weight:700; border:1px solid #e2e8f0;"><?php echo $td['masuk']; ?></td>
                                        
                                        <?php foreach($mapelColumns as $mc): 
                                            $st = $td['mapel_status'][$mc['mapel_id']] ?? '-';
                                            $st_color = '#64748b';
                                            if ($st == 'Hadir') $st_color = '#10b981';
                                            if ($st == 'Sakit') $st_color = '#eab308';
                                            if ($st == 'Izin') $st_color = '#3b82f6';
                                            if ($st == 'Alpa') $st_color = '#ef4444';
                                            if ($st == 'Bolos') $st_color = '#d97706';
                                            if ($st == '?') $st_color = '#cbd5e1';
                                        ?>
                                        <td style="padding:10px; font-weight:700; color:<?php echo $st_color; ?>; border:1px solid #e2e8f0;" title="<?php echo $st == '?' ? 'Jurnal belum diisi guru' : ''; ?>">
                                            <?php echo $st == 'Hadir' ? 'H' : ($st == 'Sakit' ? 'S' : ($st == 'Izin' ? 'I' : ($st == 'Alpa' ? 'A' : ($st == 'Bolos' ? 'B' : ($st == '?' ? '-' : '-'))))); ?>
                                        </td>
                                        <?php endforeach; ?>
                                        
                                        <?php 
                                            $ps = $td['status_pusat'];
                                            $bg_color = 'transparent';
                                            if ($ps == 'Hadir') $bg_color = '#dcfce7; color:#16a34a;';
                                            if ($ps == 'Terlambat') $bg_color = '#fef3c7; color:#b45309;';
                                            if ($ps == 'Sakit') $bg_color = '#fef9c3; color:#ca8a04;';
                                            if ($ps == 'Izin') $bg_color = '#dbeafe; color:#2563eb;';
                                            if ($ps == 'Alpa') $bg_color = '#fee2e2; color:#dc2626;';
                                            if ($ps == 'Bolos') $bg_color = '#ffedd5; color:#ea580c;';
                                        ?>
                                        <td style="padding:10px; font-weight:800; border:1px solid #e2e8f0;">
                                            <span style="background:<?php echo explode(';', $bg_color)[0]; ?>; color:<?php echo explode(':', explode(';', $bg_color)[1])[1]; ?>; padding:4px 10px; border-radius:6px; font-size:0.75rem;">
                                                <?php echo $ps ?: '-'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    lucide.createIcons();
    function zToggleSidebar() {
        document.getElementById('zSidebar').classList.toggle('active');
    }
</script>
