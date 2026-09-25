<div class="apk-vector-header" style="padding-bottom: 25px; background-color: #059669;">
    <div class="apk-top-logo">
        <a href="/apk/admin-izin-piket" style="color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 12px; margin-right: 15px;">
            <i data-lucide="arrow-left"></i>
        </a>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Inval Piket</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Eksekusi Inval</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding: 20px; background: #fff; flex: 1;">

<div class="z-card" style="margin: 2rem auto; max-width: 900px; padding: 1.5rem;">
    
    <!-- Info Guru yang Izin/Alpa -->
    <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 14px; padding: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #92400e; text-transform: uppercase;">Guru Mata Pelajaran</div>
                <div style="font-weight: 800; color: #0f172a; font-size: 1rem;"><?php echo htmlspecialchars($namaGuruIzin); ?></div>
                <div style="font-size: 0.8rem; color: #64748b;">Status: <strong style="color: <?php echo (($sumberIzin ?? '') === 'Alpa') ? '#ef4444' : '#d97706'; ?>;"><?php echo htmlspecialchars($sumberIzin ?? 'Izin'); ?></strong></div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.75rem; color: #64748b;"><?php echo date('l, d M Y', strtotime($tanggal)); ?></div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #0f172a;"><?php echo $jamMulai . ' - ' . $jamSelesai; ?></div>
            </div>
        </div>
    </div>

    <?php if (!empty($materiGuru)): ?>
        <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 14px; padding: 1rem; margin-bottom: 1.5rem;">
            <div style="font-size: 0.75rem; font-weight: 700; color: #0369a1; text-transform: uppercase; margin-bottom: 6px;">📝 Tugas / Materi dari Guru</div>
            <div style="font-size: 0.9rem; color: #0c4a6e; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($materiGuru)); ?></div>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo \App\Core\Helper::url('/apk/eksekusi-inval'); ?>" id="formInval">
        <input type="hidden" name="pengajuan_id" value="<?php echo $pengajuanId; ?>">
        <input type="hidden" name="kelas_id" value="<?php echo $kelasId; ?>">
        <input type="hidden" name="mapel_id" value="<?php echo $mapelId; ?>">
        <input type="hidden" name="tanggal" value="<?php echo $tanggal; ?>">
        <input type="hidden" name="guru_izin_id" value="<?php echo $guruIzinId; ?>">
        
        <!-- Absensi Siswa -->
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="users" style="width:18px;height:18px;color:#2563eb;"></i> Absensi Siswa
        </h3>

<div style="margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 12px;">
    <?php 
    $options = [
        ['value' => 'Hadir', 'label' => 'Hadir', 'color' => '#047857', 'bg' => '#d1fae5', 'activeColor' => '#fff', 'activeBg' => '#10b981'],
        ['value' => 'Sakit', 'label' => 'Sakit', 'color' => '#0369a1', 'bg' => '#e0f2fe', 'activeColor' => '#fff', 'activeBg' => '#0ea5e9'],
        ['value' => 'Izin', 'label' => 'Izin', 'color' => '#b45309', 'bg' => '#fef3c7', 'activeColor' => '#fff', 'activeBg' => '#f59e0b'],
        ['value' => 'Alpha', 'label' => 'Alpha', 'color' => '#b91c1c', 'bg' => '#fee2e2', 'activeColor' => '#fff', 'activeBg' => '#ef4444']
    ];
    ?>
    <?php foreach ($siswaList as $idx => $s): ?>
        <div style="padding: 12px; border: 1px solid var(--z-border); border-radius: 12px; background: #fff;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                <div>
                    <div style="font-weight: 800; color: #0f172a; font-size: 0.95rem;"><?php echo ($idx + 1) . '. ' . htmlspecialchars($s['nama']); ?></div>
                    <div style="font-size: 0.75rem; color: #64748b;"><?php echo htmlspecialchars($s['nis']); ?></div>
                </div>
            </div>
            <div style="display: flex; gap: 6px;">
                <?php foreach($options as $opt): ?>
                    <?php 
                    $isChecked = ($opt['value'] === 'Hadir'); 
                    $currentBg = $isChecked ? $opt['activeBg'] : $opt['bg'];
                    $currentColor = $isChecked ? $opt['activeColor'] : $opt['color'];
                    $currentBorder = $isChecked ? $opt['activeBg'] : $opt['bg'];
                    ?>
                    <label style="flex:1; text-align:center; padding: 8px 0; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; transition: 0.2s; border: 1px solid <?php echo $currentBorder; ?>; background: <?php echo $currentBg; ?>; color: <?php echo $currentColor; ?>;" 
                           onclick="selectAbsen(<?php echo $s['id']; ?>, '<?php echo $opt['value']; ?>')" 
                           id="lbl-<?php echo $s['id']; ?>-<?php echo $opt['value']; ?>">
                        <input type="radio" name="absen[<?php echo $s['id']; ?>]" value="<?php echo $opt['value']; ?>" <?php echo $isChecked ? 'checked' : ''; ?> style="display:none;">
                        <?php echo $opt['label']; ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
function selectAbsen(siswaId, value) {
    const options = [
        { value: 'Hadir', color: '#047857', bg: '#d1fae5', activeColor: '#fff', activeBg: '#10b981' },
        { value: 'Sakit', color: '#0369a1', bg: '#e0f2fe', activeColor: '#fff', activeBg: '#0ea5e9' },
        { value: 'Izin', color: '#b45309', bg: '#fef3c7', activeColor: '#fff', activeBg: '#f59e0b' },
        { value: 'Alpha', color: '#b91c1c', bg: '#fee2e2', activeColor: '#fff', activeBg: '#ef4444' }
    ];
    
    options.forEach(opt => {
        var lbl = document.getElementById(`lbl-${siswaId}-${opt.value}`);
        if(lbl) {
            if(opt.value === value) {
                lbl.style.background = opt.activeBg;
                lbl.style.color = opt.activeColor;
                lbl.style.border = `1px solid ${opt.activeBg}`;
            } else {
                lbl.style.background = opt.bg;
                lbl.style.color = opt.color;
                lbl.style.border = `1px solid ${opt.bg}`;
            }
        }
    });
}
</script>

        <!-- Jurnal Kelas -->
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="book-open" style="width:18px;height:18px;color:#059669;"></i> Jurnal Kelas
        </h3>
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">Catatan / Materi yang Diajarkan</label>
            <textarea name="jurnal_materi" rows="4" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none; font-size: 0.9rem; box-sizing: border-box; resize: vertical; font-family: inherit;" placeholder="Contoh: Mengerjakan tugas dari guru..."><?php echo htmlspecialchars($materiGuru ?? ''); ?></textarea>
        </div>

        <div style="background: #f1f5f9; border-radius: 10px; padding: 12px; margin-bottom: 1.5rem; font-size: 0.8rem; color: #475569;">
            <i data-lucide="info" style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:4px;color:#3b82f6;"></i>
            Jurnal akan tercatat atas nama <strong>Anda (Guru Piket)</strong> dengan catatan inval otomatis di sistem.
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <i data-lucide="check-circle" style="width:18px;height:18px;"></i> Simpan Absensi & Jurnal
        </button>
    </form>
</div>
</div>
