<style>
    .z-avatar-premium { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .header-breadcrumb .parent { color: #3b82f6; }
    .status-badge { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:6px;font-size:0.65rem;font-weight:800; }
    .s-h { background:#dcfce7;color:#15803d; }
    .s-t { background:#fef3c7;color:#92400e; }
    .s-s { background:#dbeafe;color:#1d4ed8; }
    .s-i { background:#f3e8ff;color:#7e22ce; }
    .s-a { background:#fee2e2;color:#dc2626; }
    
    /* Print styles */
    @media print {
        .z-sidebar, .z-header, .no-print { display: none !important; }
        .z-main { margin-left: 0 !important; }
        .z-scroll { padding: 0 !important; }
        table { font-size: 0.8rem; }
    }
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
                    <span style="font-weight:700;color:#1e293b;">Rekap Bulanan Guru</span>
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

            <!-- Filter -->
            <div class="no-print" style="background:white;border-radius:16px;padding:1.25rem;border:1px solid #f1f5f9;margin-bottom:1.5rem;">
                <form method="GET" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                    <?php
                    $months = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni',
                               '07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                    ?>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <i data-lucide="calendar" style="width:16px;color:#64748b;"></i>
                        <label style="font-size:0.8rem;font-weight:700;color:#64748b;">Bulan:</label>
                        <select name="bulan" style="border:1.5px solid #e2e8f0;border-radius:10px;padding:7px 12px;font-weight:700;color:#0f172a;font-size:0.85rem;outline:none;">
                            <?php foreach($months as $k => $v): ?>
                            <option value="<?php echo $k; ?>" <?php echo $bulan == $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <label style="font-size:0.8rem;font-weight:700;color:#64748b;">Tahun:</label>
                        <select name="tahun" style="border:1.5px solid #e2e8f0;border-radius:10px;padding:7px 12px;font-weight:700;color:#0f172a;font-size:0.85rem;outline:none;">
                            <?php for($y = date('Y'); $y >= date('Y')-3; $y--): ?>
                            <option value="<?php echo $y; ?>" <?php echo $tahun == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <label style="font-size:0.8rem;font-weight:700;color:#64748b;">Jabatan:</label>
                        <select name="jabatan" style="border:1.5px solid #e2e8f0;border-radius:10px;padding:7px 12px;font-weight:700;color:#0f172a;font-size:0.85rem;outline:none;">
                            <option value="">Semua Jabatan</option>
                            <?php foreach($jabatanList as $jab): ?>
                            <option value="<?php echo htmlspecialchars($jab); ?>" <?php echo $jabatan == $jab ? 'selected' : ''; ?>><?php echo htmlspecialchars($jab); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <label style="font-size:0.8rem;font-weight:700;color:#64748b;">Guru:</label>
                        <select name="guru_id" style="border:1.5px solid #e2e8f0;border-radius:10px;padding:7px 12px;font-weight:700;color:#0f172a;font-size:0.85rem;outline:none;max-width:180px;">
                            <option value="">Semua Guru</option>
                            <?php foreach($semuaGuru as $gItem): ?>
                            <option value="<?php echo $gItem['id']; ?>" <?php echo (isset($guru_id) && $guru_id == $gItem['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($gItem['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" style="background:#3b82f6;color:white;border:none;padding:8px 18px;border-radius:10px;font-weight:700;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;gap:6px;">
                        <i data-lucide="filter" style="width:14px;height:14px;"></i> Tampilkan
                    </button>
                    <?php $guruParam = (isset($guru_id) && $guru_id > 0) ? "&guru_id=".$guru_id : ""; ?>
                    <a href="/absen/cetak-bulanan-guru-detail?bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>&jabatan=<?php echo urlencode($jabatan); ?><?php echo $guruParam; ?>" target="_blank" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;padding:8px 14px;border-radius:10px;font-weight:700;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;gap:6px;text-decoration:none;">
                        <i data-lucide="printer" style="width:14px;height:14px;"></i> Cetak Laporan
                    </a>
                </form>
            </div>

            <!-- Tabel Rekap -->
            <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;overflow:hidden;">
                <div style="padding:1rem 1.25rem;background:#eff6ff;border-bottom:1px solid #dbeafe;display:flex;align-items:center;justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <i data-lucide="table-2" style="width:18px;color:#3b82f6;"></i>
                        <h3 style="margin:0;font-size:0.9rem;font-weight:800;color:#1e3a5f;">
                            Rekap Absensi Guru — <?php echo $months[$bulan] ?? $bulan; ?> <?php echo $tahun; ?>
                        </h3>
                    </div>
                    <span style="font-size:0.75rem;font-weight:700;color:#3b82f6;"><?php echo count($rekap); ?> Guru</span>
                </div>
                <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;min-width:700px;">
                    <thead>
                        <tr style="background:#f8fafc;text-align:left;">
                            <th style="padding:0.8rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;width:30px;">No</th>
                            <th style="padding:0.8rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Nama Guru</th>
                            <th style="padding:0.8rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Jabatan</th>
                            <th style="padding:0.8rem 1rem;font-size:0.7rem;font-weight:800;color:#15803d;text-transform:uppercase;text-align:center;">Hadir</th>
                            <th style="padding:0.8rem 1rem;font-size:0.7rem;font-weight:800;color:#92400e;text-transform:uppercase;text-align:center;">Terlambat</th>
                            <th style="padding:0.8rem 1rem;font-size:0.7rem;font-weight:800;color:#1d4ed8;text-transform:uppercase;text-align:center;">Sakit</th>
                            <th style="padding:0.8rem 1rem;font-size:0.7rem;font-weight:800;color:#7e22ce;text-transform:uppercase;text-align:center;">Izin</th>
                            <th style="padding:0.8rem 1rem;font-size:0.7rem;font-weight:800;color:#dc2626;text-transform:uppercase;text-align:center;">Alpa</th>
                            <th style="padding:0.8rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;text-align:center;">Total</th>
                            <th style="padding:0.8rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rekap)): ?>
                        <tr><td colspan="10" style="padding:3rem;text-align:center;color:#94a3b8;">
                            <i data-lucide="inbox" style="width:36px;height:36px;margin-bottom:8px;"></i><br>
                            Belum ada data absensi guru untuk bulan ini.
                        </td></tr>
                        <?php else: $no = 1; foreach($rekap as $g): 
                            $r = $g['rekap'];
                            $total = array_sum($r);
                        ?>
                        <tr style="border-bottom:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                            <td style="padding:0.75rem 1rem;color:#94a3b8;font-size:0.8rem;"><?php echo $no++; ?></td>
                            <td style="padding:0.75rem 1rem;">
                                <div style="font-weight:700;color:#0f172a;font-size:0.85rem;"><?php echo htmlspecialchars($g['nama']); ?></div>
                            </td>
                            <td style="padding:0.75rem 1rem;font-size:0.8rem;color:#64748b;"><?php echo htmlspecialchars($g['jabatan'] ?? '-'); ?></td>
                            <td style="padding:0.75rem 1rem;text-align:center;"><span class="status-badge s-h"><?php echo $r['Hadir']; ?></span></td>
                            <td style="padding:0.75rem 1rem;text-align:center;"><span class="status-badge s-t"><?php echo $r['Terlambat']; ?></span></td>
                            <td style="padding:0.75rem 1rem;text-align:center;"><span class="status-badge s-s"><?php echo $r['Sakit']; ?></span></td>
                            <td style="padding:0.75rem 1rem;text-align:center;"><span class="status-badge s-i"><?php echo $r['Izin']; ?></span></td>
                            <td style="padding:0.75rem 1rem;text-align:center;"><span class="status-badge s-a"><?php echo $r['Alpa']; ?></span></td>
                            <td style="padding:0.75rem 1rem;text-align:center;"><b style="color:#0f172a;"><?php echo $total; ?></b></td>
                            <td style="padding:0.75rem 1rem;text-align:center;">
                                <a href="/absen/cetak-bulanan-guru-detail?bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>&guru_id=<?php echo $g['id']; ?>" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;background:#eff6ff;color:#3b82f6;border-radius:6px;text-decoration:none;" title="Cetak Detail">
                                    <i data-lucide="printer" style="width:14px;"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
                </div>
            </div>

        </div>
    </div>
</div>
