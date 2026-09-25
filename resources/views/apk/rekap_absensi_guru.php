<div class="apk-vector-header" style="padding-bottom: 25px; background-color: #ef4444;">
    <div class="apk-top-logo">
        <a href="/apk/aplikasi-saya" style="color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 12px; margin-right: 15px;">
            <i data-lucide="arrow-left"></i>
        </a>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Riwayat Kehadiran</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Rekap Absensi Saya</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding: 20px;">
    
    <!-- Tombol Ajukan Izin -->
    <a href="/apk/izin-guru" style="display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 14px; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; border-radius: 14px; font-weight: 700; font-size: 0.95rem; text-decoration: none; box-shadow: 0 4px 15px rgba(37,99,235,0.3); margin-bottom: 20px; transition: all 0.2s;">
        <i data-lucide="file-edit" style="width: 20px; height: 20px;"></i> Ajukan Izin
    </a>
    
    <?php if (empty($absensiGrouped)): ?>
        <div style="text-align: center; padding: 40px 20px; background: white; border-radius: 16px; border: 1px dashed #cbd5e1;">
            <i data-lucide="inbox" style="width: 48px; height: 48px; color: #cbd5e1; margin-bottom: 15px; opacity: 0.5;"></i>
            <div style="font-weight: 700; color: #475569; font-size: 1.1rem;">Belum Ada Data</div>
            <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 5px;">Tidak ada riwayat kehadiran ditemukan.</div>
        </div>
    <?php else: ?>
        
        <?php foreach ($absensiGrouped as $bulanKey => $absensiList): 
            // Calculate stats for this month
            $stats = ['Hadir' => 0, 'Terlambat' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
            foreach($absensiList as $a) {
                if ($a['status'] && isset($stats[$a['status']])) {
                    $stats[$a['status']]++;
                }
            }
            $monthName = date('F Y', strtotime($bulanKey . '-01'));
            $monthId = 'month_' . str_replace('-', '_', $bulanKey);
        ?>
            <!-- Month Card -->
            <div style="background: white; border-radius: 16px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; overflow: hidden;">
                <!-- Header (Clickable) -->
                <div onclick="toggleDropdown('<?php echo $monthId; ?>')" style="padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; background: #fafafa;">
                    <div>
                        <div style="font-weight: 800; color: #0f172a; font-size: 1rem; margin-bottom: 4px;">
                            <?php echo $monthName; ?>
                        </div>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px;">
                            <div style="background: #ecfdf5; border: 1px solid #d1fae5; padding: 4px 8px; border-radius: 8px; text-align: center; min-width: 42px;">
                                <div style="font-size: 0.9rem; font-weight: 800; color: #059669;"><?php echo $stats['Hadir']; ?></div>
                                <div style="font-size: 0.55rem; font-weight: 700; color: #10b981; text-transform: uppercase;">Hadir</div>
                            </div>
                            <div style="background: #fffbeb; border: 1px solid #fef3c7; padding: 4px 8px; border-radius: 8px; text-align: center; min-width: 42px;">
                                <div style="font-size: 0.9rem; font-weight: 800; color: #d97706;"><?php echo $stats['Terlambat']; ?></div>
                                <div style="font-size: 0.55rem; font-weight: 700; color: #f59e0b; text-transform: uppercase;">Telat</div>
                            </div>
                            <div style="background: #eff6ff; border: 1px solid #dbeafe; padding: 4px 8px; border-radius: 8px; text-align: center; min-width: 42px;">
                                <div style="font-size: 0.9rem; font-weight: 800; color: #2563eb;"><?php echo $stats['Sakit']; ?></div>
                                <div style="font-size: 0.55rem; font-weight: 700; color: #3b82f6; text-transform: uppercase;">Sakit</div>
                            </div>
                            <div style="background: #fdf4ff; border: 1px solid #fce7f3; padding: 4px 8px; border-radius: 8px; text-align: center; min-width: 42px;">
                                <div style="font-size: 0.9rem; font-weight: 800; color: #c026d3;"><?php echo $stats['Izin']; ?></div>
                                <div style="font-size: 0.55rem; font-weight: 700; color: #d946ef; text-transform: uppercase;">Izin</div>
                            </div>
                            <div style="background: #fef2f2; border: 1px solid #fee2e2; padding: 4px 8px; border-radius: 8px; text-align: center; min-width: 42px;">
                                <div style="font-size: 0.9rem; font-weight: 800; color: #e11d48;"><?php echo $stats['Alpa']; ?></div>
                                <div style="font-size: 0.55rem; font-weight: 700; color: #ef4444; text-transform: uppercase;">Alpa</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <i data-lucide="chevron-down" id="icon_<?php echo $monthId; ?>" style="width: 20px; height: 20px; color: #94a3b8; transition: transform 0.3s;"></i>
                    </div>
                </div>

                <!-- Dropdown Content (Daily List) -->
                <div id="detail_<?php echo $monthId; ?>" style="display: none; padding: 15px 20px;">
                    <?php foreach ($absensiList as $idx => $a): 
                        $statusColor = '#94a3b8'; // default
                        $statusBg = '#f1f5f9';
                        if ($a['status'] == 'Hadir') { $statusColor = '#10b981'; $statusBg = '#ecfdf5'; }
                        elseif ($a['status'] == 'Terlambat') { $statusColor = '#f59e0b'; $statusBg = '#fffbeb'; }
                        elseif ($a['status'] == 'Sakit' || $a['status'] == 'Izin') { $statusColor = '#3b82f6'; $statusBg = '#eff6ff'; }
                        elseif ($a['status'] == 'Alpa') { $statusColor = '#ef4444'; $statusBg = '#fef2f2'; }
                        
                        $fmtTgl = date('d M', strtotime($a['tanggal']));
                        $fmtHari = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')[date('w', strtotime($a['tanggal']))];
                    ?>
                        <div style="margin-bottom: 12px; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: white; border-left: 4px solid <?php echo $statusColor; ?>; padding: 12px 15px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <div style="font-weight: 800; color: #1e293b; font-size: 0.9rem; margin-bottom: 4px;">
                                        <?php echo $fmtHari . ', ' . $fmtTgl; ?>
                                    </div>
                                    <div style="display: flex; gap: 15px; font-size: 0.75rem; color: #64748b; font-weight: 700;">
                                        <?php if (!empty($a['jam_masuk'])): ?>
                                            <div style="display: flex; align-items: center; gap: 4px;">
                                                <i data-lucide="clock" style="width: 14px; color: #10b981;"></i>
                                                <?php echo date('H:i', strtotime($a['jam_masuk'])); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <span style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; color: <?php echo $statusColor; ?>; background: <?php echo $statusBg; ?>;">
                                        <?php echo htmlspecialchars($a['status'] ? $a['status'] : 'Belum Absen'); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if(!empty($a['keterangan'])): ?>
                                <div style="background: #f8fafc; padding: 8px 10px; border-radius: 8px; font-size: 0.75rem; color: #475569; font-style: italic; border: 1px solid #e2e8f0; margin-top: 10px;">
                                    <i data-lucide="info" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle; margin-right: 4px; color: #64748b;"></i>
                                    <?php echo htmlspecialchars($a['keterangan']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function toggleDropdown(id) {
    const detail = document.getElementById('detail_' + id);
    const icon = document.getElementById('icon_' + id);
    
    if (detail.style.display === 'none') {
        detail.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    } else {
        detail.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>

<script>
function toggleDropdown(id) {
    const detail = document.getElementById('detail_' + id);
    const icon = document.getElementById('icon_' + id);
    
    if (detail.style.display === 'none') {
        detail.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    } else {
        detail.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
