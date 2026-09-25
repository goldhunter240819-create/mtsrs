<div class="apk-vector-header" style="padding-bottom: 25px; background-color: #10b981;">
    <div class="apk-top-logo">
        <a href="/apk/aplikasi-saya" style="color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 12px; margin-right: 15px;">
            <i data-lucide="arrow-left"></i>
        </a>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Absensi & Perizinan</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Izin Siswa</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding: 20px; min-height: 80vh;">
    
    <div style="background: #fff; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 25px;">
        <h3 style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin: 0 0 15px 0;">Pilih Kelas & Tanggal</h3>
        
        <form method="GET" action="/apk/izin-siswa" id="filterForm">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">TANGGAL</label>
                <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" onchange="document.getElementById('filterForm').submit()" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; font-size: 0.95rem; color: #1e293b; outline: none;">
            </div>
            
            <div style="margin-bottom: 5px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">KELAS</label>
                <select name="kelas_id" onchange="document.getElementById('filterForm').submit()" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; font-size: 0.95rem; color: #1e293b; outline: none;">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelasList as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $kelas_id == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <?php if ($kelas_id): ?>
    <div style="background: #fff; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <h3 style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin: 0 0 15px 0;">Input Izin Siswa</h3>
        
        <form id="formIzinSiswa">
            <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">NAMA SISWA</label>
                <select name="siswa_id" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; font-size: 0.95rem; color: #1e293b; outline: none;">
                    <option value="">-- Pilih Siswa --</option>
                    <?php foreach ($siswaList as $s): 
                        $curStat = $statusAbsen[$s['id']] ?? 'Belum';
                        $statText = $curStat !== 'Belum' ? " ($curStat)" : "";
                    ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama']) ?><?= $statText ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">STATUS</label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                    <label style="cursor: pointer;">
                        <input type="radio" name="status" value="Sakit" class="status-radio">
                        <div class="status-btn" style="text-align: center; padding: 12px 5px; border-radius: 10px; border: 2px solid #e2e8f0; font-weight: 700; color: #64748b; font-size: 0.9rem; transition: 0.2s;">Sakit</div>
                    </label>
                    <label style="cursor: pointer;">
                        <input type="radio" name="status" value="Izin" class="status-radio">
                        <div class="status-btn" style="text-align: center; padding: 12px 5px; border-radius: 10px; border: 2px solid #e2e8f0; font-weight: 700; color: #64748b; font-size: 0.9rem; transition: 0.2s;">Izin</div>
                    </label>
                    <label style="cursor: pointer;">
                        <input type="radio" name="status" value="Alpa" class="status-radio">
                        <div class="status-btn" style="text-align: center; padding: 12px 5px; border-radius: 10px; border: 2px solid #e2e8f0; font-weight: 700; color: #64748b; font-size: 0.9rem; transition: 0.2s;">Alpa</div>
                    </label>
                </div>
            </div>
            
            <button type="submit" style="display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 14px; background: #10b981; color: white; border: none; border-radius: 14px; font-weight: 700; font-size: 0.95rem; cursor: pointer; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); transition: all 0.2s;">
                <i data-lucide="save" style="width: 18px; height: 18px;"></i> Simpan Status
            </button>
        </form>
    </div>
    
    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 15px; margin-top: 20px; display: flex; gap: 12px; align-items: flex-start;">
        <i data-lucide="info" style="color: #d97706; width: 24px; flex-shrink: 0;"></i>
        <div style="font-size: 0.8rem; color: #92400e; line-height: 1.5;">
            <strong>Status Mutlak:</strong> Input status dari halaman ini akan memperbarui status pusat siswa dan membatalkan rekap absen/jurnal harian guru untuk tanggal tersebut.
        </div>
    </div>
    
    <div style="background: #fff; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-top: 25px;">
        <h3 style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin: 0 0 15px 0;">Histori Izin Hari Ini</h3>
        
        <?php
        $hasHistory = false;
        foreach ($siswaList as $s) {
            $curStat = $statusAbsen[$s['id']] ?? 'Belum';
            if (in_array($curStat, ['Sakit', 'Izin', 'Alpa', 'Bolos'])) {
                $hasHistory = true;
                break;
            }
        }
        ?>
        
        <?php if (!$hasHistory): ?>
            <div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 0.85rem; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                Belum ada data izin/sakit/alpa yang diinput hari ini.
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($siswaList as $s): 
                    $curStat = $statusAbsen[$s['id']] ?? 'Belum';
                    if (!in_array($curStat, ['Sakit', 'Izin', 'Alpa', 'Bolos'])) continue;
                    
                    $bgColors = [
                        'Sakit' => 'background: #fffbeb; border: 1px solid #fde68a; color: #d97706;',
                        'Izin' => 'background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb;',
                        'Alpa' => 'background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;',
                        'Bolos' => 'background: #fff7ed; border: 1px solid #ffedd5; color: #ea580c;'
                    ];
                    $stStyle = $bgColors[$curStat] ?? '';
                ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div style="font-size: 0.85rem; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($s['nama']) ?></div>
                    <div style="padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; <?= $stStyle ?>"><?= $curStat ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<style>
.status-radio { position: absolute; opacity: 0; width: 0; height: 0; }
.status-radio:checked[value="Sakit"] + .status-btn { border-color: #f59e0b; background: #fffbeb; color: #d97706; }
.status-radio:checked[value="Izin"] + .status-btn { border-color: #3b82f6; background: #eff6ff; color: #2563eb; }
.status-radio:checked[value="Alpa"] + .status-btn { border-color: #ef4444; background: #fef2f2; color: #dc2626; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    // Add visual feedback to radio buttons
    const radios = document.querySelectorAll('.status-radio');
    radios.forEach(r => {
        r.addEventListener('change', function() {
            document.querySelectorAll('.status-btn').forEach(b => {
                b.style.borderColor = '#e2e8f0';
                b.style.background = 'transparent';
                b.style.color = '#64748b';
            });
            
            const btn = this.nextElementSibling;
            if(this.value === 'Sakit') { btn.style.borderColor = '#f59e0b'; btn.style.background = '#fffbeb'; btn.style.color = '#d97706'; }
            if(this.value === 'Izin') { btn.style.borderColor = '#3b82f6'; btn.style.background = '#eff6ff'; btn.style.color = '#2563eb'; }
            if(this.value === 'Alpa') { btn.style.borderColor = '#ef4444'; btn.style.background = '#fef2f2'; btn.style.color = '#dc2626'; }
        });
    });
    
    const form = document.getElementById('formIzinSiswa');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            if (!formData.get('status')) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Status',
                    text: 'Silakan pilih status Izin, Sakit, atau Alpa terlebih dahulu.'
                });
                return;
            }
            
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Harap tunggu',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            fetch('/apk/izin-siswa/save', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
                }
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem.' });
            });
        });
    }
});
</script>
