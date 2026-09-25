<?php
use App\Core\Helper;
?>
<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <img src="<?= htmlspecialchars($faviconSrc) ?>" alt="Logo">
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">MTs RS Apps</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Isi Jurnal Mengajar</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0;">
    <?php if(!empty($message)): ?>
        <?php if($status == 'success'): ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: '<?= addslashes($message) ?>',
                        icon: 'success',
                        confirmButtonText: 'Oke',
                        confirmButtonColor: '#10b981',
                        timer: 3000
                    });
                });
            </script>
        <?php else: ?>
            <div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px;">
                <i data-lucide="alert-circle" style="color: #ef4444; width: 20px;"></i>
                <p style="margin:0; color: #b91c1c; font-size: 0.85rem; font-weight: 600; line-height: 1.5;"><?= htmlspecialchars($message) ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div style="display: flex; gap: 10px; margin-bottom: 25px;">
        <a href="/apk/guru/rekap-jurnal" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 15px; background: #fdf4ff; border-radius: 12px; text-decoration: none; border: 1px solid #f5d0fe; color: #86198f; font-weight: 700; font-size: 0.85rem;">
            <i data-lucide="clipboard-list" style="width: 16px;"></i> Rekap Jurnal
        </a>

        <a href="/apk/guru/rekap-absensi" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 15px; background: #eff6ff; border-radius: 12px; text-decoration: none; border: 1px solid #bfdbfe; color: #1e40af; font-weight: 700; font-size: 0.85rem;">
            <i data-lucide="bar-chart-2" style="width: 16px;"></i> Rekap Absensi
        </a>
    </div>

    <?php if (!empty($institusi['strict_jurnal_mode']) && $institusi['strict_jurnal_mode'] == 1): ?>
        <div style="background: #fffbeb; border-left: 4px solid #f59e0b; padding: 12px 15px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px;">
            <i data-lucide="clock" style="color: #d97706; width: 18px;"></i>
            <p style="margin:0; color: #92400e; font-size: 0.75rem; font-weight: 600; line-height: 1.4;">
                <b>Perhatian:</b> Jurnal akan otomatis dikunci <b style="color:#b45309;"><?= $institusi['jurnal_grace_period'] ?? 30 ?> menit</b> setelah jam pelajaran selesai.
            </p>
        </div>
    <?php endif; ?>

    <?php if (isset($is_form_locked_global) && $is_form_locked_global): ?>
        <div style="background: #ffffff; border-radius: 20px; padding: 40px 20px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; margin-bottom: 30px;">
            <div style="background: #fee2e2; width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i data-lucide="lock" style="color: #dc2626; width: 35px; height: 35px;"></i>
            </div>
            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 10px;">Jurnal Terkunci</h3>
            <p style="color: #64748b; font-size: 0.9rem; line-height: 1.6; margin-bottom: 0;">
                Maaf, Anda tidak memiliki jadwal mengajar yang aktif saat ini atau batas waktu pengisian jurnal telah habis. <br><br>
                <b>(Strict Mode Aktif)</b>
            </p>
        </div>
    <?php else: ?>
    <form action="" method="POST" id="formJurnal" onsubmit="return submitForm()">
        <input type="hidden" name="jurnal_id" value="<?= $jurnal_edit['id'] ?? 0 ?>">
        
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="info" style="color: #3b82f6; width: 20px;"></i> Informasi Jurnal
        </h3>
        
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; margin-bottom: 25px;">
            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 700; color: #475569; font-size: 0.85rem;">Kelas <span style="color:red">*</span></label>
                <select name="kelas_id" id="kelas_id" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 0.95rem; background: #fff; color: #1e293b; outline: none; transition: border-color 0.2s;" onchange="onKelasChange()">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= (isset($jurnal_edit) && $jurnal_edit['kelas_id'] == $k['id']) ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 700; color: #475569; font-size: 0.85rem;">Mata Pelajaran <span style="color:red">*</span></label>
                <select name="mapel_id" id="mapel_id" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 0.95rem; background: #fff; color: #1e293b; outline: none;">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 700; color: #475569; font-size: 0.85rem;">Tanggal <span style="color:red">*</span></label>
                <input type="date" name="tanggal" required value="<?= $jurnal_edit ? $jurnal_edit['tanggal'] : date('Y-m-d') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 0.95rem; background: #fff; color: #1e293b; outline: none;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 700; color: #475569; font-size: 0.85rem;">Materi Pembelajaran <span style="color:red">*</span></label>
                <textarea name="materi" required rows="3" placeholder="Bab / Topik materi..." style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 0.95rem; background: #fff; color: #1e293b; outline: none; font-family: inherit; resize: vertical;"><?= htmlspecialchars($jurnal_edit['materi'] ?? '') ?></textarea>
            </div>
            
            <div style="margin-bottom: 5px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 700; color: #475569; font-size: 0.85rem;">Catatan Tambahan</label>
                <textarea name="keterangan" rows="2" placeholder="Opsional..." style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 0.95rem; background: #fff; color: #1e293b; outline: none; font-family: inherit; resize: vertical;"><?= htmlspecialchars($jurnal_edit['hambatan'] ?? '') ?></textarea>
            </div>
        </div>
        
        <div id="wrapAbsensi" style="display: none;">
            <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="users" style="color: #10b981; width: 20px;"></i> Absensi Siswa
            </h3>
            
            <div style="background: #eff6ff; padding: 12px; border-radius: 12px; margin-bottom: 15px; font-size: 0.8rem; color: #1e40af; border: 1px solid #bfdbfe; display: flex; gap: 10px; align-items: flex-start;">
                <i data-lucide="info" style="width: 16px; min-width: 16px; margin-top: 2px;"></i>
                <div>Seluruh siswa secara <i>default</i> ditandai <b>Hadir</b>. Ubah status bagi yang berhalangan saja.</div>
            </div>
            
            <div id="loadingSiswa" style="text-align: center; padding: 20px; display: none;">
                <i data-lucide="loader-2" class="lucide-spin" style="width: 32px; height: 32px; color: #0ea5e9; margin: 0 auto 10px;"></i>
                <p style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Memuat daftar siswa...</p>
            </div>
            
            <div id="listSiswa" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 25px;">
                <!-- Diisi lewat JS -->
            </div>
            
            <button type="submit" id="btnSubmit" style="width: 100%; padding: 16px; background: linear-gradient(135deg, #0ea5e9, #2563eb); color: white; border: none; border-radius: 16px; font-size: 1rem; font-weight: 800; margin-bottom: 20px; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(37,99,235,0.2);">
                <i data-lucide="save" style="width: 18px;"></i> <?= $jurnal_edit ? 'Simpan Perubahan' : 'Simpan Jurnal & Absensi' ?>
            </button>
        </div>
    </form>
    <?php endif; ?>
</div>

<script>
function submitForm() {
    var btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader-2" class="lucide-spin" style="width: 18px;"></i> Menyimpan...';
    lucide.createIcons();
    return true;
}

const mengajarData = <?= json_encode($mengajarData ?? []) ?>;
const editMapelId = <?= $jurnal_edit['mapel_id'] ?? 0 ?>;
const absensiEdit = <?= json_encode($absensi_edit ?? []) ?>;

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('kelas_id').value) {
        onKelasChange();
    }
});

function onKelasChange() {
    updateMapelDropdown();
    loadSiswa();
}

function updateMapelDropdown() {
    var kelas_id = document.getElementById('kelas_id').value;
    var mapelSelect = document.getElementById('mapel_id');
    var currentVal = mapelSelect.value;
    
    mapelSelect.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
    
    if (!kelas_id) return;
    
    var filteredMapel = mengajarData.filter(function(item) {
        return item.kelas_id == kelas_id;
    });
    
    filteredMapel.forEach(function(item) {
        var option = document.createElement('option');
        option.value = item.mapel_id;
        option.text = item.nama_mapel;
        if (item.mapel_id == currentVal || item.mapel_id == editMapelId) {
            option.selected = true;
        }
        mapelSelect.appendChild(option);
    });
}

function loadSiswa() {
    var kelas_id = document.getElementById('kelas_id').value;
    var wrapAbsensi = document.getElementById('wrapAbsensi');
    var loadingSiswa = document.getElementById('loadingSiswa');
    var listSiswa = document.getElementById('listSiswa');
    
    if (!kelas_id) {
        wrapAbsensi.style.display = 'none';
        return;
    }
    
    wrapAbsensi.style.display = 'block';
    listSiswa.style.display = 'none';
    loadingSiswa.style.display = 'block';
    
    fetch('/apk/api/siswa-kelas?kelas_id=' + kelas_id, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        loadingSiswa.style.display = 'none';
        listSiswa.style.display = 'flex';
        listSiswa.innerHTML = '';
        
        if (data.error) {
            listSiswa.innerHTML = '<div style="color: #ef4444; text-align: center; font-size: 0.85rem; padding: 10px;">Error: ' + data.error + '</div>';
            return;
        }
        
        if (data.length === 0) {
            listSiswa.innerHTML = '<div style="color: #64748b; text-align: center; font-size: 0.85rem; padding: 10px; background:#f8fafc; border-radius:8px;">Tidak ada siswa di kelas ini.</div>';
            return;
        }
        
        data.forEach(function(siswa, index) {
            var row = document.createElement('div');
            row.style.cssText = 'background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; display: flex; flex-direction: column; gap: 12px;';
            row.id = 'row-' + siswa.id;
            
            var infoDiv = document.createElement('div');
            infoDiv.style.cssText = 'display: flex; align-items: center; justify-content: space-between;';
            infoDiv.innerHTML = `
                <div>
                    <div style="font-weight: 800; color: #1e293b; font-size: 0.95rem;">${siswa.nama}</div>
                    <div style="font-size: 0.75rem; color: #64748b;">NIS: ${siswa.nis || '-'}</div>
                </div>
            `;
            
            var optionsDiv = document.createElement('div');
            optionsDiv.style.cssText = 'display: flex; gap: 6px;';
            
            const options = [
                { value: 'Hadir', label: 'Hadir', color: '#047857', bg: '#d1fae5', activeColor: '#fff', activeBg: '#10b981' },
                { value: 'Sakit', label: 'Sakit', color: '#0369a1', bg: '#e0f2fe', activeColor: '#fff', activeBg: '#0ea5e9' },
                { value: 'Izin', label: 'Izin', color: '#b45309', bg: '#fef3c7', activeColor: '#fff', activeBg: '#f59e0b' },
                { value: 'Alpa', label: 'Alpa', color: '#b91c1c', bg: '#fee2e2', activeColor: '#fff', activeBg: '#ef4444' }
            ];
            
            var optionsHtml = '';
            var defaultStatus = absensiEdit[siswa.id] || 'Hadir';
            options.forEach(opt => {
                var checked = opt.value === defaultStatus ? 'checked' : '';
                optionsHtml += `
                    <label style="flex:1; text-align:center; padding: 8px 0; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; transition: 0.2s; border: 1px solid ${checked ? opt.activeBg : opt.bg}; background: ${checked ? opt.activeBg : opt.bg}; color: ${checked ? opt.activeColor : opt.color};" onclick="selectAbsen(${siswa.id}, '${opt.value}')" id="lbl-${siswa.id}-${opt.value}">
                        <input type="radio" name="absensi[${siswa.id}]" value="${opt.value}" ${checked} style="display:none;">
                        ${opt.label}
                    </label>
                `;
            });
            
            optionsDiv.innerHTML = optionsHtml;
            
            row.appendChild(infoDiv);
            row.appendChild(optionsDiv);
            listSiswa.appendChild(row);
        });
        
    })
    .catch(error => {
        loadingSiswa.style.display = 'none';
        listSiswa.style.display = 'block';
        listSiswa.innerHTML = '<div style="color: #ef4444; text-align: center; font-size: 0.85rem;">Terjadi kesalahan memuat data.</div>';
    });
}

function selectAbsen(siswaId, value) {
    const options = [
        { value: 'Hadir', color: '#047857', bg: '#d1fae5', activeColor: '#fff', activeBg: '#10b981' },
        { value: 'Sakit', color: '#0369a1', bg: '#e0f2fe', activeColor: '#fff', activeBg: '#0ea5e9' },
        { value: 'Izin', color: '#b45309', bg: '#fef3c7', activeColor: '#fff', activeBg: '#f59e0b' },
        { value: 'Alpa', color: '#b91c1c', bg: '#fee2e2', activeColor: '#fff', activeBg: '#ef4444' }
    ];
    
    options.forEach(opt => {
        var lbl = document.getElementById(`lbl-${siswaId}-${opt.value}`);
        if(lbl) {
            if(opt.value === value) {
                lbl.style.background = opt.activeBg;
                lbl.style.color = opt.activeColor;
                lbl.style.borderColor = opt.activeBg;
            } else {
                lbl.style.background = opt.bg;
                lbl.style.color = opt.color;
                lbl.style.borderColor = opt.bg;
            }
        }
    });
    
    var row = document.getElementById('row-' + siswaId);
    if(row) {
        if (value === 'Hadir') {
            row.style.background = '#fff';
            row.style.borderColor = '#e2e8f0';
        } else if (value === 'Sakit') {
            row.style.background = '#f0f9ff';
            row.style.borderColor = '#bae6fd';
        } else if (value === 'Izin') {
            row.style.background = '#fffbeb';
            row.style.borderColor = '#fde68a';
        } else if (value === 'Alpa') {
            row.style.background = '#fef2f2';
            row.style.borderColor = '#fecaca';
        }
    }
}
</script>
