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
                    <span style="font-weight:700;color:#1e293b;">Rekap Bulanan</span>
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
            <?php 
            $namaBulanList = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
            $bulanStr = $namaBulanList[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?? $bulan;
            ?>
            <!-- Top 3 Instansi -->
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1rem;">
                <h3 style="margin:0; font-size:1.1rem; color:#1e293b; font-weight:800; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="award" style="color:#eab308;"></i> Top 3 Paling Rajin Se-Instansi (<?php echo $bulanStr . ' ' . $tahun; ?>)
                </h3>
                <div style="display:flex; gap:10px;">
                    <a href="/absen/cetak-top3-siswa?bulan=<?php echo htmlspecialchars($bulan); ?>&tahun=<?php echo htmlspecialchars($tahun); ?>" target="_blank" class="btn btn-outline btn-sm" style="display:flex; align-items:center; gap:6px;">
                        <i data-lucide="printer" style="width:16px; height:16px;"></i> Cetak Juara
                    </a>
                    <button onclick="document.getElementById('modalTop3Kelas').style.display='flex'" class="btn btn-primary btn-sm" style="display:flex; align-items:center; gap:6px;">
                        <i data-lucide="users" style="width:16px; height:16px;"></i> Top 3 Per Kelas
                    </button>
                </div>
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:1.5rem; margin-bottom: 2rem;">
                <?php $rank = 1; foreach($top3Instansi as $top): ?>
                <div class="z-card" style="padding: 1.5rem; display:flex; align-items:center; gap:1rem; background: linear-gradient(135deg, #ffffff, #f8fafc); border-left: 4px solid <?php echo $rank == 1 ? '#eab308' : ($rank == 2 ? '#94a3b8' : '#b45309'); ?>;">
                    <div style="width:40px; height:40px; border-radius:50%; background:<?php echo $rank == 1 ? '#fef08a' : ($rank == 2 ? '#e2e8f0' : '#fef3c7'); ?>; color:<?php echo $rank == 1 ? '#854d0e' : ($rank == 2 ? '#475569' : '#92400e'); ?>; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.2rem;">
                        <?php echo $rank; ?>
                    </div>
                    <div>
                        <div style="font-weight:800; color:#0f172a; font-size:1rem;"><?php echo htmlspecialchars($top['nama']); ?></div>
                        <?php 
                        $active_days = $class_active_days_count[$top['kelas_id']] ?? 1;
                        $persen = min(100, round(($top['total_hadir'] / max(1, $active_days)) * 100));
                        $avgTime = $top['avg_jam_masuk'] ? substr($top['avg_jam_masuk'], 0, 5) : '-';
                        ?>
                        <div style="color:#64748b; font-size:0.85rem; margin-top:4px;">
                            Kelas <?php echo htmlspecialchars($top['nama_kelas']); ?> &bull; 
                            <span style="font-weight:700; color:#10b981;">Hadir: <?php echo $top['total_hadir']; ?></span>
                        </div>
                        <div style="color:#64748b; font-size:0.75rem; margin-top:2px; display:flex; gap:10px;">
                            <span style="display:flex; align-items:center; gap:3px;"><i data-lucide="percent" style="width:12px; height:12px;"></i> <?php echo $persen; ?>% Kehadiran</span>
                            <span style="display:flex; align-items:center; gap:3px;"><i data-lucide="clock" style="width:12px; height:12px;"></i> Rata scan: <?php echo $avgTime; ?></span>
                        </div>
                    </div>
                </div>
                <?php $rank++; endforeach; ?>
                <?php if(empty($top3Instansi)): ?>
                    <div style="color:#64748b; font-style:italic;">Belum ada data absensi bulan ini.</div>
                <?php endif; ?>
            </div>

            <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <h2 style="margin:0; font-size:1.2rem; font-weight:800; color:#1e293b; display:flex; align-items:center; gap:8px;">
                        <i data-lucide="file-spreadsheet" style="color:#10b981;"></i> Rekap Bulanan Absensi
                    </h2>
                    <form method="GET" style="display:flex; gap:10px;">
                        <select name="bulan" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none;">
                            <?php 
                            $bulans = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
                            foreach($bulans as $num => $namaBulan): ?>
                                <option value="<?php echo $num; ?>" <?php echo $bulan == $num ? 'selected' : ''; ?>><?php echo $namaBulan; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="tahun" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none;">
                            <?php for($y=date('Y'); $y>=2024; $y--): ?>
                                <option value="<?php echo $y; ?>" <?php echo $tahun == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="kelas_id" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none;">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach($kelasList as $k): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo $kelas_id == $k['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" style="padding:8px 16px; background:#10b981; color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer;">Filter</button>
                        <a href="/absen/cetak-bulanan-siswa-detail?bulan=<?php echo htmlspecialchars($bulan); ?>&tahun=<?php echo htmlspecialchars($tahun); ?>&kelas_id=<?php echo htmlspecialchars($kelas_id); ?>" target="_blank" style="padding:8px 16px; background:#f59e0b; color:white; border:none; border-radius:8px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px;">
                            <i data-lucide="qr-code" style="width:16px; height:16px;"></i> Cetak QR
                        </a>
                        <a href="/absen/cetak-manual?kelas_id=<?php echo htmlspecialchars($kelas_id); ?>" target="_blank" style="padding:8px 16px; background:#3b82f6; color:white; border:none; border-radius:8px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px;">
                            <i data-lucide="printer" style="width:16px; height:16px;"></i> Cetak Manual
                        </a>
                    </form>
                </div>

                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:800px;">
                        <thead>
                            <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0; text-align:left;">
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase;">No</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase;">NIS</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase;">Nama Siswa</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase;">Kelas</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase; text-align:center;">Hadir</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase; text-align:center;">Sakit</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase; text-align:center;">Izin</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase; text-align:center;">Alpa</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase; text-align:center;">Bolos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rekap)): ?>
                            <tr>
                                <td colspan="9" style="padding:24px; text-align:center; color:#94a3b8; font-weight:600;">Data siswa tidak ditemukan.</td>
                            </tr>
                            <?php else: ?>
                                <?php $no=1; foreach($rekap as $r): ?>
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <td style="padding:12px 16px; color:#475569;"><?php echo $no++; ?></td>
                                    <td style="padding:12px 16px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($r['nis']); ?></td>
                                    <td style="padding:12px 16px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($r['nama']); ?></td>
                                    <td style="padding:12px 16px; color:#475569;"><?php echo htmlspecialchars($r['nama_kelas']); ?></td>
                                    <td style="padding:12px 16px; text-align:center; font-weight:700; color:#15803d; background:#f0fdf4;"><?php echo $r['rekap']['Hadir'] + $r['rekap']['Terlambat']; ?></td>
                                    <td style="padding:12px 16px; text-align:center; font-weight:700; color:#ca8a04; background:#fefce8;"><?php echo $r['rekap']['Sakit']; ?></td>
                                    <td style="padding:12px 16px; text-align:center; font-weight:700; color:#1d4ed8; background:#eff6ff;"><?php echo $r['rekap']['Izin']; ?></td>
                                    <?php 
                                        $active_days = $class_active_days_count[$r['kelas_id']] ?? 1;
                                        $total_scanned = $r['rekap']['Hadir'] + $r['rekap']['Terlambat'] + $r['rekap']['Sakit'] + $r['rekap']['Izin'] + $r['rekap']['Alpa'] + $r['rekap']['Bolos'];
                                        $alpa_sebenarnya = $r['rekap']['Alpa'];
                                        if ($total_scanned < $active_days) {
                                            $alpa_sebenarnya += ($active_days - $total_scanned);
                                        }
                                    ?>
                                    <td style="padding:12px 16px; text-align:center; font-weight:700; color:#b91c1c; background:#fef2f2;"><?php echo $alpa_sebenarnya; ?></td>
                                    <td style="padding:12px 16px; text-align:center; font-weight:700; color:#9a3412; background:#ffedd5;"><?php echo $r['rekap']['Bolos']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Top 3 Per Kelas -->
<div id="modalTop3Kelas" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.5); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div style="background:white; border-radius:16px; width:90%; max-width:800px; max-height:85vh; display:flex; flex-direction:column; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
        <div style="padding:1.5rem; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-size:1.2rem; font-weight:800; color:#1e293b; display:flex; align-items:center; gap:8px;">
                <i data-lucide="users" style="color:#3b82f6;"></i> Top 3 Siswa Rajin Per Kelas (<?php echo $bulanStr . ' ' . $tahun; ?>)
            </h3>
            <button onclick="document.getElementById('modalTop3Kelas').style.display='none'" style="background:none; border:none; color:#94a3b8; cursor:pointer;">
                <i data-lucide="x"></i>
            </button>
        </div>
        <div class="z-scroll" style="padding:1.5rem; overflow-y:auto; flex:1;">
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:1.5rem;">
                <?php foreach($kelasList as $kls): ?>
                <?php if(!empty($top3PerKelas[$kls['id']])): ?>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:1.2rem;">
                    <div style="font-weight:800; color:#0f172a; margin-bottom:1rem; padding-bottom:0.5rem; border-bottom:1px dashed #cbd5e1; display:flex; justify-content:space-between;">
                        <span>Kelas <?php echo htmlspecialchars($kls['nama_kelas']); ?></span>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.8rem;">
                        <?php $rk = 1; foreach($top3PerKelas[$kls['id']] as $tk): ?>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:24px; height:24px; border-radius:50%; background:<?php echo $rk==1?'#fef08a':($rk==2?'#e2e8f0':'#fef3c7'); ?>; color:<?php echo $rk==1?'#854d0e':($rk==2?'#475569':'#92400e'); ?>; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:0.75rem;">
                                <?php echo $rk; ?>
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:700; color:#1e293b; font-size:0.85rem;"><?php echo htmlspecialchars($tk['nama']); ?></div>
                                <?php 
                                $active_daysK = $class_active_days_count[$tk['kelas_id']] ?? 1;
                                $persenK = min(100, round(($tk['total_hadir'] / max(1, $active_daysK)) * 100));
                                $avgTimeK = $tk['avg_jam_masuk'] ? substr($tk['avg_jam_masuk'], 0, 5) : '-';
                                $tk_alpa_sebenarnya = $tk['total_alpa'];
                                if ($tk['total_hari_tercatat'] < $active_daysK) {
                                    $tk_alpa_sebenarnya += ($active_daysK - $tk['total_hari_tercatat']);
                                }
                                ?>
                                <div style="color:#64748b; font-size:0.75rem;">Hadir: <?php echo $tk['total_hadir']; ?> &bull; Alpa: <?php echo $tk_alpa_sebenarnya; ?></div>
                                <div style="color:#64748b; font-size:0.7rem; margin-top:2px;">
                                    <span><?php echo $persenK; ?>% Kehadiran &bull; Rata scan: <?php echo $avgTimeK; ?></span>
                                </div>
                            </div>
                        </div>
                        <?php $rk++; endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
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
