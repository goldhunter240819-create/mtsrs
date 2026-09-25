<div class="apk-vector-header" style="padding-bottom: 70px; justify-content: center; text-align: center; position: relative; background-image: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
    <div>
        <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Jurnal Kelas</div>
        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.8); margin-top: 5px;">Kelas <?= htmlspecialchars($selected_class_name) ?></div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -30px; border-radius: 30px 30px 0 0; min-height: 70vh;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-left: 5px;">
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0;">Monitoring Jurnal</h3>
        <form method="GET" style="margin: 0; display: flex; gap: 8px;">
            <select name="kelas_id" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.8rem; background: white; outline: none; font-weight: 600; color: #334155;">
                <?php foreach($classes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $kelas_id == $c['id'] ? 'selected' : '' ?>>Kelas <?= htmlspecialchars($c['nama_kelas']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="month" name="bulan" value="<?= htmlspecialchars($bulan) ?>" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.8rem; background: white; outline: none; font-weight: 600; color: #334155;">
        </form>
    </div>
    <p style="font-size: 0.75rem; color: #64748b; margin-top: 0; margin-bottom: 15px; padding-left: 5px;">Membandingkan Jadwal Pelajaran vs Realisasi Pengisian Jurnal oleh Guru Mata Pelajaran.</p>



    <?php if (empty($final_display)): ?>
        <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1;">
            <div style="width: 64px; height: 64px; background: #fffbeb; color: #f59e0b; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                <i data-lucide="calendar-x" style="width: 32px; height: 32px;"></i>
            </div>
            <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 600;">Belum ada data jadwal/jurnal untuk bulan ini.</p>
        </div>
    <?php else: ?>
        <div style="position: relative; padding-left: 15px; border-left: 2px solid #e2e8f0; margin-left: 5px; margin-bottom: 30px; margin-top: 20px;">
            <?php foreach ($final_display as $idx => $day): 
                $fmt_date = date('d', strtotime($day['tanggal'])) . ' ' . $months_indo[date('m', strtotime($day['tanggal']))] . ' ' . date('Y', strtotime($day['tanggal']));
            ?>
                
                <div style="margin-left: -25px; margin-bottom: 15px; margin-top: <?= $idx === 0 ? '0' : '25px' ?>;">
                    <span style="font-size: 0.75rem; font-weight: 800; color: white; background: #d97706; padding: 6px 12px; border-radius: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: inline-flex; align-items: center; gap: 6px;">
                        <i data-lucide="calendar" style="width: 14px; height: 14px;"></i> <?= $day['hari_indo'] . ', ' . $fmt_date ?>
                    </span>
                </div>

                <div style="position: relative; margin-bottom: 15px;">
                    <div style="position: absolute; left: -21px; top: 15px; width: 10px; height: 10px; border-radius: 50%; background: #f59e0b; border: 2px solid white; box-shadow: 0 0 0 1px #f59e0b;"></div>
                    <div style="background: white; border-radius: 16px; padding: 15px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
                        
                        <?php if ($day['is_special']): ?>
                            <div style="display: flex; align-items: center; gap: 12px; background: <?= $day['is_special']['kategori'] === 'Libur' ? '#fef2f2' : '#eff6ff' ?>; padding: 12px; border-radius: 12px; border: 1px solid <?= $day['is_special']['kategori'] === 'Libur' ? '#fecaca' : '#bfdbfe' ?>;">
                                <div style="background: <?= $day['is_special']['kategori'] === 'Libur' ? '#fee2e2' : '#dbeafe' ?>; color: <?= $day['is_special']['kategori'] === 'Libur' ? '#dc2626' : '#2563eb' ?>; padding: 8px; border-radius: 8px;">
                                    <i data-lucide="<?= $day['is_special']['kategori'] === 'Libur' ? 'calendar-off' : 'calendar-heart' ?>"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 800; color: <?= $day['is_special']['kategori'] === 'Libur' ? '#991b1b' : '#1e40af' ?>; font-size: 0.95rem;"><?= htmlspecialchars($day['is_special']['kegiatan'] ?: $day['is_special']['kategori']) ?></div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Kewajiban Jurnal Guru & Absen Mapel ditiadakan</div>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($day['items'] as $itemIndex => $item): 
                                $jadwal = $item['jadwal'];
                                $jurnal = $item['jurnal'];
                                $absen = $item['absen'];
                                $is_filled = $jurnal !== null;
                            ?>
                                <div style="<?= $itemIndex > 0 ? 'margin-top: 15px; padding-top: 15px; border-top: 1px dashed #e2e8f0;' : '' ?>">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                        <div>
                                            <div style="font-weight: 800; color: #0f172a; font-size: 0.95rem; display: flex; align-items: center; gap: 6px;">
                                                <?= htmlspecialchars($jadwal['nama_mapel']) ?>
                                                <?php if ($is_filled): ?>
                                                    <i data-lucide="check-circle" style="color: #10b981; width: 14px; height: 14px;"></i>
                                                <?php else: ?>
                                                    <?php if ($day['pulang_dipercepat'] !== null && $jadwal['jam_mulai'] >= $day['pulang_dipercepat']['jam_pulang']): ?>
                                                        <i data-lucide="info" style="color: #0284c7; width: 14px; height: 14px;"></i>
                                                    <?php else: ?>
                                                        <i data-lucide="x-circle" style="color: #ef4444; width: 14px; height: 14px;"></i>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                                                <i data-lucide="clock" style="width: 12px; height: 12px;"></i> <?= substr($jadwal['jam_mulai'], 0, 5) ?> - <?= substr($jadwal['jam_selesai'], 0, 5) ?>
                                                &nbsp;|&nbsp; <i data-lucide="user" style="width: 12px; height: 12px;"></i> 
                                                <?php 
                                                $tampil_guru = $jadwal['nama_guru'];
                                                if ($is_filled && !empty($jurnal['nama_guru_jurnal'])) {
                                                    $tampil_guru = $jurnal['nama_guru_jurnal'];
                                                }
                                                echo htmlspecialchars($tampil_guru ?? '-');
                                                ?>
                                            </div>
                                        </div>
                                        <?php if (!$is_filled): ?>
                                            <?php if ($day['pulang_dipercepat'] !== null && $jadwal['jam_mulai'] >= $day['pulang_dipercepat']['jam_pulang']): ?>
                                                <div style="background: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700;">
                                                    <?= htmlspecialchars($day['pulang_dipercepat']['kegiatan'] ?? 'Pulang Dipercepat') ?>
                                                </div>
                                            <?php else: ?>
                                                <div style="background: #fef2f2; color: #ef4444; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700;">
                                                    Belum Isi Jurnal
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($is_filled): ?>
                                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; margin-top: 12px;">
                                            <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 4px; font-weight: 700;">Materi / Topik:</div>
                                            <p style="margin: 0; font-size: 0.85rem; color: #1e293b; line-height: 1.5; white-space: pre-wrap; font-weight: 500;"><?= htmlspecialchars($jurnal['materi']) ?></p>
                                            
                                            <?php if (!empty($jurnal['hambatan'])): ?>
                                                <div style="background: #fffbeb; padding: 8px 10px; border-radius: 8px; border-left: 3px solid #f59e0b; margin-top: 10px;">
                                                    <div style="font-size: 0.7rem; color: #d97706; margin-bottom: 2px; font-weight: 700;">Hambatan:</div>
                                                    <div style="font-size: 0.75rem; color: #92400e; font-style: italic;"><?= htmlspecialchars($jurnal['hambatan']) ?></div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div style="margin-top: 12px; border-top: 1px solid #cbd5e1; padding-top: 10px; display: flex; gap: 6px; flex-wrap: wrap; align-items: center;">
                                                <span style="font-size:0.7rem; color:#475569; font-weight:700; margin-right: 4px;">Absen:</span>
                                                <span style="background:white; border: 1px solid #86efac; color:#16a34a; padding:2px 8px; border-radius:6px; font-size:0.7rem; font-weight:700;">H: <?= $absen['hadir'] ?></span>
                                                <span style="background:white; border: 1px solid #fde047; color:#d97706; padding:2px 8px; border-radius:6px; font-size:0.7rem; font-weight:700;">S: <?= $absen['sakit'] ?></span>
                                                <span style="background:white; border: 1px solid #93c5fd; color:#2563eb; padding:2px 8px; border-radius:6px; font-size:0.7rem; font-weight:700;">I: <?= $absen['izin'] ?></span>
                                                <span style="background:white; border: 1px solid #fca5a5; color:#dc2626; padding:2px 8px; border-radius:6px; font-size:0.7rem; font-weight:700;">A: <?= $absen['alpha'] ?></span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
