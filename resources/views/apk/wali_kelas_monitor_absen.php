<div class="apk-header" style="background: transparent; padding-top: 30px; position: absolute; width: 100%; top: 0; z-index: 100;">
    <a href="javascript:history.back()" style="color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 700; background: rgba(0,0,0,0.2); padding: 8px 15px; border-radius: 20px; backdrop-filter: blur(5px); width: fit-content;">
        <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Kembali
    </a>
</div>

<div class="apk-vector-header" style="padding-bottom: 25px; padding-top: 90px; background-color: #8b5cf6;">
    <div class="apk-top-logo">
        <div style="width: 45px; height: 45px; border-radius: 12px; background: #fff; color: #8b5cf6; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <i data-lucide="book-check"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Monitoring Harian</div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #fff;"><?= htmlspecialchars($title ?? 'Absen Mapel Siswa') ?></div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; min-height: 70vh;">
    <!-- Form Filter Tanggal -->
    <form action="" method="GET" style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="date" name="tgl" value="<?= htmlspecialchars($today) ?>" style="flex: 1; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; outline: none;">
        <button type="submit" style="background: #8b5cf6; color: #fff; border: none; padding: 0 15px; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 5px;">
            <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Filter
        </button>
        <a href="/apk/wali-kelas/absen" style="background: #10b981; color: #fff; text-decoration: none; padding: 0 15px; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 5px;">
            <i data-lucide="calendar-check-2" style="width: 16px; height: 16px;"></i> Rekap Absen
        </a>
    </form>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0;">Monitoring Absen (Jurnal)</h3>
    </div>

    <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 30px;">
        <?php if(empty($kelasData)): ?>
            <div style="text-align: center; padding: 30px; color: #94a3b8; font-size: 0.9rem; font-weight: 600; background: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1;">
                Belum ada guru yang mengisi absensi/jurnal mapel pada tanggal ini.
            </div>
        <?php else: ?>
            <?php foreach($kelasData as $k_id => $k): ?>
            <div style="background: #ffffff; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; overflow: hidden;">
                <div style="padding: 12px 15px; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="users" style="color: #64748b; width: 16px;"></i> Kelas <?= htmlspecialchars($k['nama']) ?>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; background: #e2e8f0; padding: 3px 8px; border-radius: 8px;">
                            <?= $k['total'] ?> Siswa
                        </div>
                    </div>
                </div>
                
                <div style="padding: 15px; display: grid; grid-template-columns: repeat(5, 1fr); gap: 4px; border-bottom: 1px solid #f1f5f9;">
                    <!-- Hadir -->
                    <div onclick="filterStudents('Hadir')" style="cursor: pointer; text-align: center; background: #ecfdf5; padding: 6px 2px; border-radius: 8px; border: 1px solid #a7f3d0;">
                        <div style="font-size: 1rem; font-weight: 800; color: #059669; line-height: 1;"><?= $k['hadir'] ?></div>
                        <div style="font-size: 0.55rem; font-weight: 700; color: #047857; margin-top: 4px;">Hadir</div>
                    </div>
                    <!-- Sakit -->
                    <div onclick="filterStudents('Sakit')" style="cursor: pointer; text-align: center; background: #fff1f2; padding: 6px 2px; border-radius: 8px; border: 1px solid #fecdd3;">
                        <div style="font-size: 1rem; font-weight: 800; color: #e11d48; line-height: 1;"><?= $k['sakit'] ?></div>
                        <div style="font-size: 0.55rem; font-weight: 700; color: #be123c; margin-top: 4px;">Sakit</div>
                    </div>
                    <!-- Izin -->
                    <div onclick="filterStudents('Izin')" style="cursor: pointer; text-align: center; background: #e0f2fe; padding: 6px 2px; border-radius: 8px; border: 1px solid #bae6fd;">
                        <div style="font-size: 1rem; font-weight: 800; color: #0284c7; line-height: 1;"><?= $k['izin'] ?></div>
                        <div style="font-size: 0.55rem; font-weight: 700; color: #0369a1; margin-top: 4px;">Izin</div>
                    </div>
                    <!-- Alpha -->
                    <div onclick="filterStudents('Alpha')" style="cursor: pointer; text-align: center; background: #fef2f2; padding: 6px 2px; border-radius: 8px; border: 1px solid #fca5a5;">
                        <div style="font-size: 1rem; font-weight: 800; color: #b91c1c; line-height: 1;"><?= $k['alpha'] ?></div>
                        <div style="font-size: 0.55rem; font-weight: 700; color: #991b1b; margin-top: 4px;">Alpha</div>
                    </div>
                    <!-- Bolos -->
                    <div onclick="filterStudents('Bolos')" style="cursor: pointer; text-align: center; background: #fffbeb; padding: 6px 2px; border-radius: 8px; border: 1px solid #fde68a;">
                        <div style="font-size: 1rem; font-weight: 800; color: #d97706; line-height: 1;"><?= $k['bolos'] ?></div>
                        <div style="font-size: 0.55rem; font-weight: 700; color: #b45309; margin-top: 4px;">Bolos</div>
                    </div>
                    <!-- Tampilkan Semua -->
                    <div onclick="filterStudents('All')" style="cursor: pointer; text-align: center; background: #f8fafc; padding: 6px 2px; border-radius: 8px; border: 1px solid #cbd5e1; grid-column: span 5; margin-top: 4px;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: #475569;">Tampilkan Semua</div>
                    </div>
                </div>

                <!-- Student List -->
                <div id="studentListContainer" style="padding: 15px; display: flex; flex-direction: column; gap: 10px;">
                    <?php foreach($k['students'] as $s): 
                        $st = $s['final_status'];
                        $bgColor = '#f8fafc'; $color = '#64748b';
                        if ($st === 'Hadir') { $bgColor = '#ecfdf5'; $color = '#059669'; }
                        else if ($st === 'Bolos') { $bgColor = '#fffbeb'; $color = '#d97706'; }
                        else if ($st === 'Sakit') { $bgColor = '#fff1f2'; $color = '#dc2626'; }
                        else if ($st === 'Izin') { $bgColor = '#e0f2fe'; $color = '#0284c7'; }
                        else if ($st === 'Alpha') { $bgColor = '#fef2f2'; $color = '#b91c1c'; }
                        
                        $masukInfo = $s['masuk'] !== '-' ? $s['masuk'] : 'Belum Scan';
                        $masukColor = $s['masuk'] !== '-' ? '#059669' : '#b91c1c';
                    ?>
                    <div class="student-item" data-status="<?= $st ?>" style="display: flex; flex-direction: column; padding: 12px 15px; background: <?= $bgColor ?>; border-radius: 12px; border: 1px solid rgba(0,0,0,0.03);">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-size: 0.85rem; font-weight: 800; color: #1e293b;"><?= htmlspecialchars($s['nama']) ?></div>
                            <span style="font-size: 0.7rem; font-weight: 800; color: <?= $color ?>; text-transform: uppercase;"><?= $st ?></span>
                        </div>
                        
                        <div style="margin-top: 8px; display: flex; flex-direction: column; gap: 4px; border-top: 1px dashed rgba(0,0,0,0.1); padding-top: 8px;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; background: rgba(0,0,0,0.03); padding: 4px 6px; border-radius: 6px; margin-bottom: 4px;">
                                <span style="color: #475569; font-weight: 700;">Scan Masuk</span>
                                <span style="font-weight: 800; color: <?= $masukColor ?>;"><?= $masukInfo ?></span>
                            </div>
                            
                            <?php if(!empty($s['mapels'])): ?>
                                <?php foreach($s['mapels'] as $m): 
                                    $mColor = '#64748b';
                                    $mStatus = $m['status'];
                                    if($mStatus === 'Bolos' || $mStatus === 'Alpa') $mStatus = 'Alpha';
                                    if($mStatus == 'Hadir') $mColor = '#059669';
                                    else if($mStatus == 'Sakit') $mColor = '#dc2626';
                                    else if($mStatus == 'Izin') $mColor = '#0284c7';
                                    else if($mStatus == 'Alpha') $mColor = '#b91c1c';
                                ?>
                                <div style="display: flex; justify-content: space-between; font-size: 0.75rem; padding: 2px 6px;">
                                    <span style="color: #475569;"><?= htmlspecialchars($m['mapel']) ?></span>
                                    <span style="font-weight: 700; color: <?= $mColor ?>;"><?= $mStatus ?></span>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="font-size: 0.7rem; color: #94a3b8; font-style: italic; padding: 2px 6px;">Belum ada data mapel</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if(empty($k['students'])): ?>
                        <div style="text-align: center; color: #94a3b8; font-size: 0.85rem; padding: 30px; background: #f8fafc; border-radius: 12px;">Tidak ada data siswa</div>
                    <?php endif; ?>
                </div>

            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function filterStudents(statusFilter) {
    var items = document.querySelectorAll('.student-item');
    var visibleCount = 0;
    
    items.forEach(function(item) {
        if (statusFilter === 'All' || item.getAttribute('data-status') === statusFilter) {
            item.style.display = 'flex';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
}
</script>
