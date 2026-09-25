<div class="apk-vector-header" style="padding-bottom: 70px; justify-content: space-between; align-items: flex-start; position: relative; background-image: linear-gradient(135deg, #10b981 0%, #059669 100%);">
    <div style="text-align: left;">
        <div style="font-size: 0.75rem; color: rgba(255,255,255,0.8); margin-bottom: 2px;">Buku Wali Kelas</div>
        <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Rekap Kehadiran</div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -30px; border-radius: 30px 30px 0 0; min-height: 70vh; padding: 20px;">
    
    <!-- Filter Card -->
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
        <form action="" method="GET">
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.7rem; color: #64748b; font-weight: 700; margin-bottom: 4px;">Dari Tanggal</label>
                    <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.8rem; outline: none; background: #f8fafc; color: #1e293b;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.7rem; color: #64748b; font-weight: 700; margin-bottom: 4px;">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.8rem; outline: none; background: #f8fafc; color: #1e293b;">
                </div>
            </div>
            <button type="submit" style="width: 100%; background: #10b981; color: #fff; border: none; border-radius: 8px; padding: 10px; font-size: 0.85rem; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer;">
                <i data-lucide="filter" style="width: 16px;"></i> Tampilkan Data
            </button>
        </form>
    </div>

    <!-- List Siswa -->
    <?php if (empty($absen_list)): ?>
        <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1;">
            <i data-lucide="user-x" style="width: 48px; height: 48px; color: #94a3b8; margin-bottom: 15px;"></i>
            <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 600;">Belum ada siswa di kelas ini.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($absen_list as $s): 
                $hadir = (int)$s['hadir'];
                $sakit = (int)$s['sakit'];
                $izin = (int)$s['izin'];
                $alpa = (int)$s['alpa'];
                $bolos = (int)$s['bolos'];
                
                $total = $hadir + $sakit + $izin + $alpa + $bolos;
                $persentase = $total > 0 ? round(($hadir / $total) * 100) : 0;
            ?>
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                    <!-- Header Siswa -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; overflow: hidden; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; color: #64748b;">
                                <?php if (!empty($s['foto']) && file_exists(__DIR__ . '/../../../public/uploads/siswa/' . $s['foto'])): ?>
                                    <img src="<?= \App\Core\Helper::url('/public/uploads/siswa/' . $s['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <i data-lucide="user" style="width: 20px;"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #1e293b; line-height: 1.3; max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($s['nama']) ?></div>
                                <div style="font-size: 0.65rem; color: #94a3b8; margin-top: 2px;">NIS: <?= htmlspecialchars($s['nis'] ?? '-') ?></div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.95rem; font-weight: 800; color: <?= $persentase >= 80 ? '#10b981' : ($persentase >= 50 ? '#f59e0b' : '#ef4444') ?>;"><?= $persentase ?>%</div>
                            <div style="font-size: 0.65rem; color: #94a3b8;">Kehadiran</div>
                        </div>
                    </div>
                    
                    <!-- Kotak Status -->
                    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 4px;">
                        <!-- Hadir -->
                        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 6px 2px; text-align: center;">
                            <div style="font-size: 0.9rem; font-weight: 800; color: #059669; margin-bottom: 2px;"><?= $hadir ?></div>
                            <div style="font-size: 0.6rem; font-weight: 700; color: #10b981;">Hadir</div>
                        </div>
                        <!-- Sakit -->
                        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 6px 2px; text-align: center;">
                            <div style="font-size: 0.9rem; font-weight: 800; color: #d97706; margin-bottom: 2px;"><?= $sakit ?></div>
                            <div style="font-size: 0.6rem; font-weight: 700; color: #f59e0b;">Sakit</div>
                        </div>
                        <!-- Izin -->
                        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 6px 2px; text-align: center;">
                            <div style="font-size: 0.9rem; font-weight: 800; color: #2563eb; margin-bottom: 2px;"><?= $izin ?></div>
                            <div style="font-size: 0.6rem; font-weight: 700; color: #3b82f6;">Izin</div>
                        </div>
                        <!-- Alpa -->
                        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 6px 2px; text-align: center;">
                            <div style="font-size: 0.9rem; font-weight: 800; color: #dc2626; margin-bottom: 2px;"><?= $alpa ?></div>
                            <div style="font-size: 0.6rem; font-weight: 700; color: #ef4444;">Alpa</div>
                        </div>
                        <!-- Bolos -->
                        <div style="background: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px; padding: 6px 2px; text-align: center;">
                            <div style="font-size: 0.9rem; font-weight: 800; color: #b45309; margin-bottom: 2px;"><?= $bolos ?></div>
                            <div style="font-size: 0.6rem; font-weight: 700; color: #d97706;">Bolos</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
