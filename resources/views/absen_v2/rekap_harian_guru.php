<style>
    .z-avatar-premium { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .header-breadcrumb .parent { color: #3b82f6; }
    .welcome-hero { background: linear-gradient(135deg, #1e3a5f 0%, #1e293b 100%); border-bottom: 5px solid #3b82f6; }
    .hero-badge { background: rgba(59,130,246,0.2); color: #60a5fa; border-color: rgba(59,130,246,0.3); }
    .hero-btn.primary { background: #3b82f6; box-shadow: 0 10px 20px rgba(59,130,246,0.2); }

    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 8px; font-size: 0.72rem; font-weight: 700;
    }
    .status-hadir    { background: #dcfce7; color: #15803d; }
    .status-terlambat{ background: #fef3c7; color: #92400e; }
    .status-sakit    { background: #dbeafe; color: #1d4ed8; }
    .status-izin     { background: #f3e8ff; color: #7e22ce; }
    .status-alpa     { background: #fee2e2; color: #dc2626; }
    .status-belum    { background: #f1f5f9; color: #64748b; }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" onclick="zToggleSidebar()" style="background:none;border:none;cursor:pointer;color:#64748b;"><i data-lucide="menu"></i></button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <span class="parent" style="font-weight:800;letter-spacing:1px;font-size:0.8rem;">ABSEN V2</span>
                    <i data-lucide="chevron-right" style="width:14px;color:#cbd5e1;"></i>
                    <span style="font-weight:700;color:#1e293b;">Rekap Harian Guru</span>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="z-avatar-premium" style="width:36px;height:36px;color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;"><?php echo $initial; ?></div>
                <div>
                    <div style="font-weight:800;font-size:0.9rem;color:#0f172a;"><?php echo $nama; ?></div>
                    <div style="font-size:0.75rem;color:#64748b;">Administrator</div>
                </div>
            </div>
        </header>

        <div class="z-scroll" style="padding: 1.5rem 2rem;">
            
            <!-- Filter Bar -->
            <div style="background:white; border-radius:16px; padding:1.25rem; border:1px solid #f1f5f9; margin-bottom:1.5rem;">
                <form method="GET" action="" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <i data-lucide="calendar" style="width:16px;height:16px;color:#64748b;"></i>
                        <label style="font-size:0.8rem;font-weight:700;color:#64748b;">Tanggal:</label>
                        <input type="date" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>"
                               style="border:1.5px solid #e2e8f0;border-radius:10px;padding:7px 12px;font-weight:700;color:#0f172a;font-size:0.85rem;outline:none;">
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <i data-lucide="briefcase" style="width:16px;height:16px;color:#64748b;"></i>
                        <label style="font-size:0.8rem;font-weight:700;color:#64748b;">Jabatan:</label>
                        <select name="jabatan" style="border:1.5px solid #e2e8f0;border-radius:10px;padding:7px 12px;font-weight:700;color:#0f172a;font-size:0.85rem;outline:none;">
                            <option value="">Semua Jabatan</option>
                            <?php foreach($jabatanList as $jab): ?>
                            <option value="<?php echo htmlspecialchars($jab); ?>" <?php echo $jabatan == $jab ? 'selected' : ''; ?>><?php echo htmlspecialchars($jab); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" style="background:#3b82f6;color:white;border:none;padding:8px 18px;border-radius:10px;font-weight:700;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;gap:6px;">
                        <i data-lucide="filter" style="width:14px;height:14px;"></i> Filter
                    </button>
                    <a href="?tanggal=<?php echo $tanggal; ?>" onclick="window.print(); return false;"
                       style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;padding:8px 14px;border-radius:10px;font-weight:700;font-size:0.85rem;cursor:pointer;text-decoration:none;display:flex;align-items:center;gap:6px;">
                        <i data-lucide="printer" style="width:14px;height:14px;"></i> Cetak
                    </a>
                </form>
            </div>

            <!-- Summary Cards -->
            <?php
            $totalHadir     = count($absensiGuru);
            $totalBelum     = count($guruBelumAbsen);
            $countHadir     = array_reduce($absensiGuru, fn($c, $r) => $c + ($r['status'] === 'Hadir' ? 1 : 0), 0);
            $countTerlambat = array_reduce($absensiGuru, fn($c, $r) => $c + ($r['status'] === 'Terlambat' ? 1 : 0), 0);
            $countSakit     = array_reduce($absensiGuru, fn($c, $r) => $c + ($r['status'] === 'Sakit' ? 1 : 0), 0);
            $countIzin      = array_reduce($absensiGuru, fn($c, $r) => $c + ($r['status'] === 'Izin' ? 1 : 0), 0);
            $countAlpa      = array_reduce($absensiGuru, fn($c, $r) => $c + ($r['status'] === 'Alpa' ? 1 : 0), 0);
            ?>
            <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:1rem;margin-bottom:1.5rem;">
                <?php
                $cards = [
                    ['label'=>'Hadir','val'=>$countHadir,'bg'=>'#dcfce7','color'=>'#15803d','icon'=>'user-check'],
                    ['label'=>'Terlambat','val'=>$countTerlambat,'bg'=>'#fef3c7','color'=>'#92400e','icon'=>'clock'],
                    ['label'=>'Izin','val'=>$countIzin,'bg'=>'#f3e8ff','color'=>'#7e22ce','icon'=>'file-text'],
                    ['label'=>'Sakit','val'=>$countSakit,'bg'=>'#dbeafe','color'=>'#1d4ed8','icon'=>'activity'],
                    ['label'=>'Alpa','val'=>$countAlpa,'bg'=>'#fee2e2','color'=>'#dc2626','icon'=>'user-x'],
                    ['label'=>'Belum Absen','val'=>$totalBelum,'bg'=>'#f1f5f9','color'=>'#475569','icon'=>'circle-dashed'],
                ];
                foreach ($cards as $c): ?>
                <div style="background:white;border-radius:14px;padding:1.2rem;border:1px solid #f1f5f9;display:flex;align-items:center;gap:1rem;">
                    <div style="width:40px;height:40px;border-radius:10px;background:<?php echo $c['bg']; ?>;color:<?php echo $c['color']; ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i data-lucide="<?php echo $c['icon']; ?>" style="width:18px;height:18px;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.7rem;font-weight:700;color:#64748b;"><?php echo $c['label']; ?></div>
                        <div style="font-size:1.3rem;font-weight:800;color:#0f172a;"><?php echo $c['val']; ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Tabel Guru Sudah Absen -->
            <?php if (!empty($absensiGuru)): ?>
            <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;overflow:hidden;margin-bottom:1.5rem;">
                <div style="padding:1rem 1.25rem;background:#eff6ff;border-bottom:1px solid #dbeafe;display:flex;align-items:center;gap:10px;">
                    <i data-lucide="user-check" style="width:18px;color:#3b82f6;"></i>
                    <h3 style="margin:0;font-size:0.9rem;font-weight:800;color:#1e3a5f;">Guru Sudah Absen (<?php echo count($absensiGuru); ?>)</h3>
                </div>
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc;text-align:left;">
                            <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">No</th>
                            <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Nama Guru</th>
                            <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Jabatan</th>
                            <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Status</th>
                            <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Jam Masuk</th>
                            <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($absensiGuru as $row): ?>
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:0.8rem 1rem;color:#64748b;font-size:0.82rem;"><?php echo $no++; ?></td>
                            <td style="padding:0.8rem 1rem;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <?php
                                    $fotoGuru = 'https://ui-avatars.com/api/?name=' . urlencode($row['nama']) . '&background=2563eb&color=fff&size=40';
                                    if (!empty($row['foto']) && file_exists(__DIR__ . '/../../../../public/uploads/guru/' . $row['foto'])) {
                                        $fotoGuru = '/public/uploads/guru/' . $row['foto'];
                                    }
                                    ?>
                                    <img src="<?php echo $fotoGuru; ?>" style="width:34px;height:34px;border-radius:50%;object-fit:cover;">
                                    <span style="font-weight:700;color:#0f172a;font-size:0.85rem;"><?php echo htmlspecialchars($row['nama']); ?></span>
                                </div>
                            </td>
                            <td style="padding:0.8rem 1rem;font-size:0.82rem;color:#64748b;"><?php echo htmlspecialchars($row['jabatan'] ?? '-'); ?></td>
                            <td style="padding:0.8rem 1rem;">
                                <?php
                                $st = $row['status'];
                                $stClass = 'status-' . strtolower($st);
                                ?>
                                <span class="status-badge <?php echo $stClass; ?>"><?php echo $st; ?></span>
                            </td>
                            <td style="padding:0.8rem 1rem;font-size:0.85rem;font-weight:700;color:#0f172a;font-family:monospace;">
                                <?php echo $row['jam_masuk'] ? substr($row['jam_masuk'], 0, 5) : '-'; ?>
                            </td>
                            <td style="padding:0.8rem 1rem;font-size:0.8rem;color:#64748b;"><?php echo htmlspecialchars($row['keterangan'] ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Tabel Guru Belum Absen -->
            <?php if (!empty($guruBelumAbsen)): ?>
            <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;overflow:hidden;">
                <div style="padding:1rem 1.25rem;background:#f8fafc;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px;">
                    <i data-lucide="circle-dashed" style="width:18px;color:#94a3b8;"></i>
                    <h3 style="margin:0;font-size:0.9rem;font-weight:800;color:#475569;">Guru Belum Absen (<?php echo count($guruBelumAbsen); ?>)</h3>
                </div>
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc;text-align:left;">
                            <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">No</th>
                            <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Nama Guru</th>
                            <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Jabatan</th>
                            <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no2 = 1; foreach($guruBelumAbsen as $g): ?>
                        <tr style="border-bottom:1px solid #f1f5f9;opacity:0.7;">
                            <td style="padding:0.8rem 1rem;color:#94a3b8;font-size:0.82rem;"><?php echo $no2++; ?></td>
                            <td style="padding:0.8rem 1rem;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <?php
                                    $fotoG = 'https://ui-avatars.com/api/?name=' . urlencode($g['nama']) . '&background=94a3b8&color=fff&size=40';
                                    if (!empty($g['foto']) && file_exists(__DIR__ . '/../../../../public/uploads/guru/' . $g['foto'])) {
                                        $fotoG = '/public/uploads/guru/' . $g['foto'];
                                    }
                                    ?>
                                    <img src="<?php echo $fotoG; ?>" style="width:34px;height:34px;border-radius:50%;object-fit:cover;filter:grayscale(0.5);">
                                    <span style="font-weight:700;color:#64748b;font-size:0.85rem;"><?php echo htmlspecialchars($g['nama']); ?></span>
                                </div>
                            </td>
                            <td style="padding:0.8rem 1rem;font-size:0.82rem;color:#94a3b8;"><?php echo htmlspecialchars($g['jabatan'] ?? '-'); ?></td>
                            <td style="padding:0.8rem 1rem;">
                                <?php if(isset($g['ada_jam']) && !$g['ada_jam']): ?>
                                <span class="status-badge" style="background:#f3f4f6; color:#9ca3af; border: 1px solid #e5e7eb;"><i data-lucide="calendar-x" style="width:12px;"></i> Tidak Ada Jam</span>
                                <?php else: ?>
                                <span class="status-badge status-belum"><i data-lucide="minus-circle" style="width:12px;"></i> Belum Absen</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php if (empty($absensiGuru) && empty($guruBelumAbsen)): ?>
            <div style="text-align:center;padding:4rem;background:white;border-radius:16px;border:1px solid #f1f5f9;">
                <i data-lucide="inbox" style="width:48px;height:48px;color:#cbd5e1;margin-bottom:1rem;"></i>
                <div style="font-weight:700;color:#64748b;">Tidak ada data guru ditemukan.</div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
