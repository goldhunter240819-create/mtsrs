<?php
use App\Core\Helper;
?>
<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <a href="<?= Helper::url('/apk/guru/jurnal') ?>" style="color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 12px; margin-right: 15px;">
            <i data-lucide="arrow-left"></i>
        </a>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Menu Jurnal</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Rekap Absensi Persentase</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0;">
    
    <div style="background: linear-gradient(135deg, #3b82f6, #2563eb); padding: 15px; border-radius: 16px; color: white; display: flex; gap: 12px; align-items: center; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);">
        <div style="background: rgba(255,255,255,0.2); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i data-lucide="bar-chart-2" style="width: 20px;"></i>
        </div>
        <div>
            <div style="font-size: 0.8rem; opacity: 0.9; font-weight: 600;">Filter Kehadiran</div>
            <div style="font-size: 0.95rem; font-weight: 800;">Pilih Tanggal, Kelas & Mapel</div>
        </div>
    </div>

    <form action="" method="GET" id="filterForm" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; margin-bottom: 25px;">
        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="display:block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.75rem;">Mulai</label>
                <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars($start_date) ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.85rem; outline: none; font-family: inherit;">
            </div>
            <div style="flex: 1;">
                <label style="display:block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.75rem;">Akhir</label>
                <input type="date" name="end_date" id="end_date" value="<?= htmlspecialchars($end_date) ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.85rem; outline: none; font-family: inherit;">
            </div>
        </div>

        <div style="display: flex; gap: 10px;">
            <div style="flex: 1;">
                <select name="kelas_id" id="kelas_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.85rem; background: #fff; outline: none;" onchange="onKelasChange()">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $filter_kelas == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="flex: 1;">
                <select name="mapel_id" id="mapel_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.85rem; background: #fff; outline: none;" onchange="this.form.submit()" <?= empty($filter_kelas) ? 'disabled' : '' ?>>
                    <option value="">-- Pilih Mapel --</option>
                </select>
            </div>
        </div>
    </form>

    <?php if (isset($_GET['kelas_id']) && isset($_GET['mapel_id'])): ?>
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="check-square" style="color: #3b82f6; width: 20px;"></i> Persentase Kehadiran
        </h3>
        <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 20px;">
            Total Pertemuan (direkam): <strong style="color: #0f172a;"><?= $totalPertemuan ?> kali</strong>
        </p>

        <?php if (empty($studentStats)): ?>
            <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1;">
                <i data-lucide="users-2" style="width: 48px; height: 48px; color: #94a3b8; margin-bottom: 15px;"></i>
                <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 600;">Belum ada siswa atau absensi di kelas ini.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 30px;">
                <?php foreach ($studentStats as $s): ?>
                <?php
                    // Color logic for percentage
                    $pColor = '#10b981'; // green for >= 80%
                    $pBg = '#ecfdf5';
                    if ($s['persentase'] < 80 && $s['persentase'] >= 60) {
                        $pColor = '#f59e0b'; // yellow
                        $pBg = '#fffbeb';
                    } else if ($s['persentase'] < 60) {
                        $pColor = '#ef4444'; // red
                        $pBg = '#fee2e2';
                    }
                ?>
                <div style="background: #ffffff; border-radius: 16px; padding: 15px; border: 1px solid #f1f5f9; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <div>
                            <div style="font-weight: 800; font-size: 0.95rem; color: #1e293b;"><?= htmlspecialchars($s['nama']) ?></div>
                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">NIS: <?= htmlspecialchars($s['nis'] ?? '-') ?></div>
                        </div>
                        <div style="background: <?= $pBg ?>; color: <?= $pColor ?>; font-weight: 800; font-size: 0.95rem; padding: 6px 12px; border-radius: 10px; border: 1px solid <?= $pColor ?>;">
                            <?= $s['persentase'] ?>%
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; background: #f8fafc; border-radius: 10px; padding: 10px; font-size: 0.8rem; font-weight: 700; border: 1px solid #e2e8f0;">
                        <span style="color: #059669;">H: <?= $s['hadir'] ?></span>
                        <span style="color: #2563eb;">S: <?= $s['sakit'] ?></span>
                        <span style="color: #d97706;">I: <?= $s['izin'] ?></span>
                        <span style="color: #dc2626;">A: <?= $s['alpa'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
const mengajarData = <?= json_encode($mengajarData ?? []) ?>;
const filterMapelId = <?= $filter_mapel ?>;

function onKelasChange() {
    const mapelSelect = document.getElementById('mapel_id');
    const kelas_id = document.getElementById('kelas_id').value;
    if (kelas_id) {
        mapelSelect.disabled = false;
        const matchFound = updateMapelDropdown(true);
        if (matchFound) {
            document.getElementById('filterForm').submit();
        }
    } else {
        mapelSelect.innerHTML = '<option value="">-- Pilih Mapel --</option>';
        mapelSelect.disabled = true;
    }
}

function updateMapelDropdown(isFromKelasChange = false) {
    const kelas_id = document.getElementById('kelas_id').value;
    const mapelSelect = document.getElementById('mapel_id');
    
    let prevMapelId = filterMapelId;
    if (isFromKelasChange && mapelSelect.value) {
        prevMapelId = mapelSelect.value;
    } else if (isFromKelasChange && !mapelSelect.value && filterMapelId) {
        prevMapelId = filterMapelId;
    }
    
    mapelSelect.innerHTML = '<option value="">-- Pilih Mapel --</option>';
    let hasMatch = false;
    
    if (kelas_id) {
        const filteredMapel = mengajarData.filter(item => item.kelas_id == kelas_id);
        
        filteredMapel.forEach(item => {
            const option = document.createElement('option');
            option.value = item.mapel_id;
            option.textContent = item.nama_mapel;
            if (item.mapel_id == prevMapelId) {
                option.selected = true;
                hasMatch = true;
            }
            mapelSelect.appendChild(option);
        });
    }
    return hasMatch;
}

document.addEventListener('DOMContentLoaded', () => {
    updateMapelDropdown();
});
</script>
