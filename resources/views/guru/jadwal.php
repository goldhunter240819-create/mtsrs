<div class="z-card" style="padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(0,0,0,0.05); box-shadow: none;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 1.4rem; font-weight: 800; color: var(--z-text); margin-bottom: 0.3rem;">Jadwal Mengajar</h2>
            <p style="color: var(--z-muted); font-size: 0.9rem; margin: 0;">Tahun Ajaran <?php echo $active_year['name'] ?? ''; ?> - Semester <?php echo $active_year['semester'] ?? 'Ganjil'; ?></p>
        </div>
        <div style="background: rgba(37,99,235,0.1); color: var(--z-primary); width: 48px; height: 48px; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
            <i data-lucide="calendar-days"></i>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; align-items: start;">
    <?php foreach ($jadwal as $hari => $mapels): ?>
        <div style="background: white; border: 1px solid rgba(0,0,0,0.05); border-radius: 12px; padding: 1.2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--z-text); border-bottom: 2px solid rgba(0,0,0,0.05); padding-bottom: 0.5rem; margin-top: 0; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--z-primary);"></span>
                Hari <?php echo $hari; ?>
            </h3>
            
            <?php if (empty($mapels)): ?>
                <div style="background: rgba(0,0,0,0.02); border-radius: 8px; padding: 1rem; text-align: center; color: var(--z-muted); font-size: 0.85rem; font-style: italic;">
                    Tidak ada jadwal mengajar
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                    <?php foreach ($mapels as $m): ?>
                        <div style="border: 1px solid rgba(0,0,0,0.05); border-radius: 8px; padding: 0.8rem; display: flex; align-items: center; gap: 1rem; transition: all 0.2s ease;" onmouseover="this.style.borderColor='var(--z-primary)';" onmouseout="this.style.borderColor='rgba(0,0,0,0.05)';">
                            <div style="background: rgba(37,99,235,0.05); color: var(--z-primary); font-size: 0.75rem; font-weight: 800; padding: 0.4rem; border-radius: 6px; min-width: 45px; text-align: center;">
                                Ke-<?php echo $m['jam_ke'] == 0 ? '?' : $m['jam_ke']; ?>
                            </div>
                            <div style="flex-grow: 1;">
                                <div style="font-weight: 800; color: var(--z-text); font-size: 0.9rem; margin-bottom: 0.2rem;"><?php echo htmlspecialchars($m['nama_mapel']); ?></div>
                                <div style="font-size: 0.75rem; color: var(--z-muted); display: flex; flex-wrap: wrap; gap: 0.8rem;">
                                    <span style="display: flex; align-items: center; gap: 4px;"><i data-lucide="users" style="width: 12px; height: 12px;"></i> Kls <?php echo htmlspecialchars($m['nama_kelas']); ?></span>
                                    <span style="display: flex; align-items: center; gap: 4px;"><i data-lucide="clock" style="width: 12px; height: 12px;"></i> <?php echo substr($m['jam_mulai'], 0, 5); ?> - <?php echo substr($m['jam_selesai'], 0, 5); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
