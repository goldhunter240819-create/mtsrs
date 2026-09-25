<?php
use App\Core\Helper;
?>
<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <a href="<?= Helper::url('/apk/profile') ?>" style="color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 12px; margin-right: 15px;">
            <i data-lucide="arrow-left"></i>
        </a>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Menu Profil</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Rekap Absen & Jurnal</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0;">
    
    <div style="background: linear-gradient(135deg, #8b5cf6, #6366f1); padding: 15px; border-radius: 16px; color: white; display: flex; gap: 12px; align-items: center; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);">
        <div style="background: rgba(255,255,255,0.2); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i data-lucide="filter" style="width: 20px;"></i>
        </div>
        <div>
            <div style="font-size: 0.8rem; opacity: 0.9; font-weight: 600;">Filter Data Rekap</div>
            <div style="font-size: 0.95rem; font-weight: 800;">Pilih Kelas & Mapel</div>
        </div>
    </div>

    <form action="" method="GET" id="filterForm" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; margin-bottom: 25px; display: flex; gap: 10px;">
        <div style="flex: 1;">
            <select name="kelas_id" id="kelas_id" required style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 0.9rem; background: #fff; color: #1e293b; outline: none;" onchange="onKelasChange()">
                <option value="">-- Pilih Kelas --</option>
                <?php foreach($kelasList as $k): ?>
                <option value="<?= $k['id'] ?>" <?= $filter_kelas == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div style="flex: 1;">
            <select name="mapel_id" id="mapel_id" required style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 0.9rem; background: #fff; color: #1e293b; outline: none;" onchange="this.form.submit()" <?= empty($filter_kelas) ? 'disabled' : '' ?>>
                <option value="">-- Pilih Mapel --</option>
            </select>
        </div>
    </form>

    <?php if (isset($_GET['kelas_id']) && isset($_GET['mapel_id'])): ?>
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="list-checks" style="color: #10b981; width: 20px;"></i> Hasil Pencarian
        </h3>

        <?php if (empty($jurnalList)): ?>
            <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1;">
                <i data-lucide="folder-search-2" style="width: 48px; height: 48px; color: #94a3b8; margin-bottom: 15px;"></i>
                <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 600;">Belum ada rekap jurnal untuk kelas & mapel ini.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 30px;">
                <?php foreach ($jurnalList as $j): ?>
                <?php 
                    $border_color = isset($j['is_duplicate']) && $j['is_duplicate'] ? '#ef4444' : '#3b82f6';
                    $bg_header = isset($j['is_duplicate']) && $j['is_duplicate'] ? '#fef2f2' : '#ffffff';
                ?>
                <div style="background: #ffffff; border-radius: 16px; padding: 15px; border: 1px solid #f1f5f9; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-left: 4px solid <?= $border_color ?>;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; background: <?= $bg_header ?>; padding: 8px; border-radius: 8px; margin: -8px -8px 10px -8px;">
                        <div>
                            <div style="font-weight: 800; font-size: 0.95rem; color: #1e293b; margin-bottom: 2px;">
                                <?= date('d M Y', strtotime($j['tanggal'])) ?>
                                <?php if(isset($j['is_duplicate']) && $j['is_duplicate']): ?>
                                    <span style="font-size: 0.65rem; background: #ef4444; color: white; padding: 2px 6px; border-radius: 4px; margin-left: 5px; vertical-align: top;">DOBEL</span>
                                <?php endif; ?>
                            </div>
                            <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                <?= htmlspecialchars($j['nama_mapel']) ?> - <?= htmlspecialchars($j['nama_kelas']) ?>
                            </div>
                        </div>
                        <div style="display: flex; gap: 5px;">
                            <a href="/apk/guru/jurnal?edit_id=<?= $j['id'] ?>" style="background: #f1f5f9; color: #475569; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                <i data-lucide="edit" style="width: 16px;"></i>
                            </a>
                            <a href="#" onclick="confirmDelete(<?= $j['id'] ?>)" style="background: #fee2e2; color: #ef4444; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                <i data-lucide="trash-2" style="width: 16px;"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div style="background: #f8fafc; padding: 10px 12px; border-radius: 10px; margin-bottom: 15px; border: 1px dashed #e2e8f0;">
                        <div style="font-size: 0.85rem; color: #334155; font-weight: 600; line-height: 1.5;">
                            "<?= htmlspecialchars($j['materi']) ?>"
                        </div>
                        <?php if (!empty($j['keterangan'])): ?>
                            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 5px; font-style: italic;">
                                Ket: <?= htmlspecialchars($j['keterangan']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;">
                        <div style="text-align: center; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 6px; cursor: pointer;" onclick="showNames('Hadir', '<?= addslashes($j['names_hadir']) ?>')">
                            <div style="font-size: 1rem; font-weight: 800; color: #059669;"><?= $j['hadir'] ?></div>
                            <div style="font-size: 0.65rem; font-weight: 800; color: #047857;">Hadir</div>
                        </div>
                        <div style="text-align: center; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 6px; cursor: pointer;" onclick="showNames('Sakit', '<?= addslashes($j['names_sakit']) ?>')">
                            <div style="font-size: 1rem; font-weight: 800; color: #2563eb;"><?= $j['sakit'] ?></div>
                            <div style="font-size: 0.65rem; font-weight: 800; color: #1d4ed8;">Sakit</div>
                        </div>
                        <div style="text-align: center; background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 6px; cursor: pointer;" onclick="showNames('Izin', '<?= addslashes($j['names_izin']) ?>')">
                            <div style="font-size: 1rem; font-weight: 800; color: #d97706;"><?= $j['izin'] ?></div>
                            <div style="font-size: 0.65rem; font-weight: 800; color: #b45309;">Izin</div>
                        </div>
                        <div style="text-align: center; background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px; padding: 6px; cursor: pointer;" onclick="showNames('Alpa', '<?= addslashes($j['names_alpha']) ?>')">
                            <div style="font-size: 1rem; font-weight: 800; color: #dc2626;"><?= $j['alpha'] ?></div>
                            <div style="font-size: 0.65rem; font-weight: 800; color: #b91c1c;">Alpa</div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
<?php if(isset($_SESSION['apk_jurnal_msg'])): ?>
    Swal.fire({
        title: 'Berhasil!',
        text: '<?= addslashes($_SESSION['apk_jurnal_msg']) ?>',
        icon: '<?= $_SESSION['apk_jurnal_status'] ?>',
        confirmButtonText: 'Oke',
        confirmButtonColor: '#10b981',
        timer: 3000
    });
    <?php unset($_SESSION['apk_jurnal_msg']); unset($_SESSION['apk_jurnal_status']); ?>
<?php endif; ?>

const mengajarData = <?= json_encode($mengajarData ?? []) ?>;
const filterMapelId = <?= $filter_mapel ?>;

function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Jurnal?',
        text: 'Jurnal dan data absen di dalamnya akan dihapus secara permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const urlParams = new URLSearchParams(window.location.search);
            const kelas_id = urlParams.get('kelas_id') || 0;
            const mapel_id = urlParams.get('mapel_id') || 0;
            window.location.href = `/apk/guru/rekap-jurnal/delete?id=${id}&kelas_id=${kelas_id}&mapel_id=${mapel_id}`;
        }
    })
}

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

function showNames(status, names) {
    if (names === '-') {
        Swal.fire({
            title: `Tidak ada siswa yang ${status}`,
            icon: 'info',
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'Tutup'
        });
        return;
    }
    
    // Convert comma separated to unordered list
    let listHtml = '<ul style="text-align:left; max-height: 200px; overflow-y: auto; background: #f8fafc; padding: 15px 15px 15px 30px; border-radius: 10px; margin: 0;">';
    names.split(', ').forEach(name => {
        listHtml += `<li style="margin-bottom: 5px; font-weight: 600; color: #334155;">${name}</li>`;
    });
    listHtml += '</ul>';

    Swal.fire({
        title: `Daftar Siswa ${status}`,
        html: listHtml,
        confirmButtonColor: '#3b82f6',
        confirmButtonText: 'Tutup'
    });
}

document.addEventListener('DOMContentLoaded', () => {
    updateMapelDropdown();
});
</script>
