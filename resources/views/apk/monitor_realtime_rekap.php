<div class="apk-header" style="background: transparent; padding-top: 30px; position: absolute; width: 100%; top: 0; z-index: 100;">
    <a href="javascript:history.back()" style="color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 700; background: rgba(0,0,0,0.2); padding: 8px 15px; border-radius: 20px; backdrop-filter: blur(5px); width: fit-content;">
        <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Kembali
    </a>
</div>

<div class="apk-vector-header" style="padding-bottom: 25px; padding-top: 90px; background-color: #8b5cf6;">
    <div class="apk-top-logo">
        <div style="width: 45px; height: 45px; border-radius: 12px; background: #fff; color: #8b5cf6; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <i data-lucide="calendar-check-2"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Rekapitulasi</div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #fff;"><?= htmlspecialchars($title ?? 'Rekap QR Siswa') ?></div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; min-height: 70vh;">
    <!-- Form Filter Tanggal -->
    <form action="" method="GET" style="display: flex; gap: 10px; margin-bottom: 25px;">
        <div style="flex: 1; display: flex; flex-direction: column; gap: 8px;">
            <select name="kelas_id" style="padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc; font-size: 0.85rem; outline: none; width: 100%;">
                <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                <?php foreach($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $filter_kelas_id == $k['id'] ? 'selected' : '' ?>>
                        Kelas <?= htmlspecialchars($k['nama_kelas']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" style="padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc; font-size: 0.85rem; outline: none; width: 100%;">
            <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" style="padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc; font-size: 0.85rem; outline: none; width: 100%;">
        </div>
        <button type="submit" style="background: #8b5cf6; color: #fff; border: none; padding: 0 20px; border-radius: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 5px;">
            <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Filter
        </button>
    </form>

    <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 30px;">
        <?php if(empty($rekap_list)): ?>
            <div style="text-align: center; padding: 30px; color: #94a3b8; font-size: 0.9rem; font-weight: 600; background: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1;">
                <?php if(empty($filter_kelas_id)): ?>
                    Silakan pilih kelas terlebih dahulu untuk melihat data rekap absensi.
                <?php else: ?>
                    Belum ada data rekap di kelas ini pada rentang tanggal tersebut.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php 
            $current_kelas = '';
            foreach ($rekap_list as $r): 
                if ($current_kelas != $r['nama_kelas']):
                    $current_kelas = $r['nama_kelas'];
            ?>
                <!-- Header Kelas -->
                <div style="margin-top: 15px; background: #f1f5f9; padding: 10px 15px; border-radius: 12px; font-weight: 800; color: #334155; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="users" style="width: 16px; color: #64748b;"></i> Kelas <?= htmlspecialchars($current_kelas) ?>
                </div>
            <?php endif; ?>
                
                <div style="background: #ffffff; padding: 15px; border-radius: 16px; border: 1px solid #f1f5f9; box-shadow: 0 4px 12px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 10px;">
                    <div style="font-weight: 800; color: #1e293b; font-size: 0.95rem; border-bottom: 1px solid #f8fafc; padding-bottom: 8px;">
                        <?= htmlspecialchars($r['nama']) ?>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 4px;">
                        <!-- Hadir -->
                        <div onclick="showRekapModal('<?= $r['siswa_id'] ?>', 'Hadir')" style="cursor: pointer; background: #ecfdf5; padding: 8px 4px; border-radius: 8px; border: 1px solid #a7f3d0; text-align: center;">
                            <div style="font-size: 1.1rem; font-weight: 800; color: #059669;"><?= $r['hadir'] ?></div>
                            <div style="font-size: 0.6rem; font-weight: 700; color: #047857; margin-top: 3px;">Hadir</div>
                        </div>
                        
                        <!-- Terlambat -->
                        <div onclick="showRekapModal('<?= $r['siswa_id'] ?>', 'Terlambat')" style="cursor: pointer; background: #fffbeb; padding: 8px 4px; border-radius: 8px; border: 1px solid #fde68a; text-align: center;">
                            <div style="font-size: 1.1rem; font-weight: 800; color: #d97706;"><?= $r['terlambat'] ?></div>
                            <div style="font-size: 0.6rem; font-weight: 700; color: #b45309; margin-top: 3px;">Terlambat</div>
                        </div>
                        
                        <!-- Sakit -->
                        <div onclick="showRekapModal('<?= $r['siswa_id'] ?>', 'Sakit')" style="cursor: pointer; background: #fff1f2; padding: 8px 4px; border-radius: 8px; border: 1px solid #fecaca; text-align: center;">
                            <div style="font-size: 1.1rem; font-weight: 800; color: #dc2626;"><?= $r['sakit'] ?></div>
                            <div style="font-size: 0.6rem; font-weight: 700; color: #b91c1c; margin-top: 3px;">Sakit</div>
                        </div>
                        
                        <!-- Izin -->
                        <div onclick="showRekapModal('<?= $r['siswa_id'] ?>', 'Izin')" style="cursor: pointer; background: #eff6ff; padding: 8px 4px; border-radius: 8px; border: 1px solid #bfdbfe; text-align: center;">
                            <div style="font-size: 1.1rem; font-weight: 800; color: #3b82f6;"><?= $r['izin'] ?></div>
                            <div style="font-size: 0.6rem; font-weight: 700; color: #1d4ed8; margin-top: 3px;">Izin</div>
                        </div>

                        <!-- Belum -->
                        <div onclick="showRekapModal('<?= $r['siswa_id'] ?>', 'Belum')" style="cursor: pointer; background: #f8fafc; padding: 8px 4px; border-radius: 8px; border: 1px solid #cbd5e1; text-align: center;">
                            <div style="font-size: 1.1rem; font-weight: 800; color: #64748b;"><?= $r['belum'] ?></div>
                            <div style="font-size: 0.6rem; font-weight: 700; color: #334155; margin-top: 3px;">Belum</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Rekap Detail -->
<div id="rekapModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(3px);">
    <div style="background: #fff; width: 100%; max-width: 400px; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; max-height: 85vh; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        <div style="padding: 18px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <div id="modalRekapTitle" style="font-weight: 800; color: #1e293b; font-size: 1.05rem;">Detail</div>
            <button onclick="closeRekapModal()" style="background: #e2e8f0; border: none; color: #64748b; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.2rem; font-weight: bold;">&times;</button>
        </div>
        <div id="modalRekapContent" style="padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
            <!-- content goes here -->
        </div>
    </div>
</div>

<script>
var rekapData = <?= json_encode($rekap_list) ?>;
var dataMap = {};
for (var i = 0; i < rekapData.length; i++) {
    dataMap[rekapData[i].siswa_id] = rekapData[i];
}

function showRekapModal(siswa_id, status) {
    var r = dataMap[siswa_id];
    if (!r) return;
    
    document.getElementById('modalRekapTitle').innerText = r.nama + ' - ' + status;
    var details = [];
    
    if (status === 'Hadir') details = r.detail_hadir;
    else if (status === 'Terlambat') details = r.detail_terlambat;
    else if (status === 'Sakit') details = r.detail_sakit;
    else if (status === 'Izin') details = r.detail_izin;
    else if (status === 'Belum') details = r.detail_belum;
    
    var content = '';
    
    if (details.length === 0) {
        content = '<div style="text-align: center; color: #94a3b8; font-size: 0.85rem; padding: 30px; background: #f8fafc; border-radius: 12px;">Tidak ada data</div>';
    } else {
        var bgColor = '#f8fafc'; var color = '#64748b';
        if (status === 'Hadir') { bgColor = '#ecfdf5'; color = '#059669'; }
        else if (status === 'Terlambat') { bgColor = '#fffbeb'; color = '#d97706'; }
        else if (status === 'Sakit') { bgColor = '#fff1f2'; color = '#dc2626'; }
        else if (status === 'Izin') { bgColor = '#eff6ff'; color = '#2563eb'; }
        else if (status === 'Belum') { bgColor = '#f8fafc'; color = '#64748b'; }
        
        for (var j = 0; j < details.length; j++) {
            var d = details[j];
            var jamHtml = '';
            if (d.jam) {
                jamHtml = '<span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; background: #fff; padding: 3px 8px; border-radius: 6px;">Jam ' + d.jam + '</span>';
            }
            
            content += `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; background: ${bgColor}; border-radius: 12px; border: 1px solid rgba(0,0,0,0.03);">
                    <div style="font-size: 0.9rem; font-weight: 800; color: #1e293b;">${d.tanggal}</div>
                    ${jamHtml}
                </div>
            `;
        }
    }
    
    document.getElementById('modalRekapContent').innerHTML = content;
    document.getElementById('rekapModal').style.display = 'flex';
}

function closeRekapModal() {
    document.getElementById('rekapModal').style.display = 'none';
}
</script>
