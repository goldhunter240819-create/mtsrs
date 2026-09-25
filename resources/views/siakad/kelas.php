<?php use App\Core\Helper; ?>
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="building-2" style="color: #bfdbfe;"></i> Data Rombel Kelas
        </h1>
        <p class="mph-subtitle">Kelola daftar kelas dan tingkat pendidikan MTs RS.</p>
    </div>
    <div class="mph-actions">
        <button class="btn btn-primary-white" onclick="addKelas()">
            <i data-lucide="plus-circle"></i> Tambah Kelas
        </button>
    </div>
</div>

<div class="z-panel">
    <div style="overflow-x: auto;">
        <table class="z-table">
            <thead>
                <tr>
                    <th>Tingkat</th>
                    <th>Nama Rombel</th>
                    <th>Wali Kelas</th>
                    <th>Jumlah Siswa</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($kelasList)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; color:var(--z-muted); padding:20px;">Belum ada data rombel kelas.</td>
                </tr>
                <?php endif; ?>
                <?php foreach($kelasList as $k): 
                    $wk = isset($wali_map['Kelas ' . $k['nama_kelas']]) ? $wali_map['Kelas ' . $k['nama_kelas']] : (isset($wali_map[$k['nama_kelas']]) ? $wali_map[$k['nama_kelas']] : null); 
                ?>
                <tr>
                    <td><span class="pill pill-blue">Tingkat <?php echo $k['tingkat']; ?></span></td>
                    <td style="font-weight: 800; color:var(--z-text);">Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?></td>
                    <td>
                        <?php if($wk): ?>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div style="width:24px; height:24px; background:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:bold; overflow:hidden;">
                                    <?php if(!empty($wk['guru_foto']) && file_exists(__DIR__ . '/../../../public/uploads/guru/' . $wk['guru_foto'])): ?>
                                        <img src="<?php echo Helper::url('/public/uploads/guru/' . $wk['guru_foto']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                    <?php else: ?>
                                        <?php echo substr($wk['guru_nama'], 0, 2); ?>
                                    <?php endif; ?>
                                </div>
                                <span style="font-weight: 600;"><?php echo htmlspecialchars($wk['guru_nama']); ?></span>
                            </div>
                        <?php else: ?>
                            <span style="color:var(--z-warning); font-style:italic; font-size:0.85rem;"><i data-lucide="alert-circle" style="width:14px;height:14px;"></i> Belum diset</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:4px; color:var(--z-muted);">
                            <i data-lucide="users" style="width:14px;height:14px;"></i> <?php echo number_format($k['total_siswa']); ?> Siswa
                        </div>
                    </td>
                    <td style="text-align: right;">
                        <div style="display:flex; justify-content:flex-end; gap:6px;">
                            <button class="btn btn-outline btn-sm" style="padding: 4px 10px;" onclick='setWaliKelas("<?php echo htmlspecialchars($k['nama_kelas']); ?>", "<?php echo $wk ? $wk['guru_id'] : ''; ?>")' title="Set Wali Kelas">
                                <i data-lucide="user-check"></i> Set Wali
                            </button>
                            <button class="btn btn-outline btn-sm" style="padding: 4px 8px;" onclick='editKelas(<?php echo json_encode($k); ?>, "<?php echo $wk ? $wk['guru_id'] : ''; ?>")' title="Edit Kelas">
                                <i data-lucide="edit-3"></i>
                            </button>
                            <a href="<?php echo Helper::url('/siakad/kelas/delete/' . $k['id']); ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-sm" style="padding: 4px 8px; background:#fee2e2; color:#ef4444; border:1px solid #fca5a5;" title="Hapus Kelas">
                                <i data-lucide="trash-2"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit Kelas -->
<div id="modal-kelas" class="z-modal-ov" style="display: none;">
    <div class="z-modal">
        <div class="z-modal-head">
            <div class="z-modal-title" id="kelas-modal-title"><i data-lucide="building-2"></i> Tambah Kelas</div>
            <div class="z-modal-close" onclick="document.getElementById('modal-kelas').style.display='none'"><i data-lucide="x"></i></div>
        </div>
        <div class="z-modal-body">
            <form id="formKelas" method="POST" action="<?php echo Helper::url('/siakad/kelas/save'); ?>">
                <input type="hidden" name="id" id="kelas-id">
                <div class="z-form-group">
                    <label class="z-label">Tingkat Kelas</label>
                    <select name="tingkat" id="kelas-tingkat" class="z-field" required>
                        <option value="7">Kelas 7</option>
                        <option value="8">Kelas 8</option>
                        <option value="9">Kelas 9</option>
                    </select>
                </div>
                <div class="z-form-group">
                    <label class="z-label">Nama Rombel / Kelas</label>
                    <input type="text" name="nama_kelas" id="kelas-nama" class="z-field" placeholder="Misal: 7-A atau 7-Tahfidz" required>
                </div>
                <div class="z-form-group">
                    <label class="z-label">Pilih Wali Kelas <span style="font-size:0.7rem; font-weight:normal; color:var(--z-muted);">(Opsional)</span></label>
                    <select name="wali_kelas_id" id="kelas-wali-id" class="z-field">
                        <option value="">-- Kosongkan Wali Kelas --</option>
                        <?php foreach($gurus as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
        <div class="z-modal-foot">
            <button class="btn btn-ghost" onclick="document.getElementById('modal-kelas').style.display='none'">Batal</button>
            <button class="btn btn-primary" onclick="document.getElementById('formKelas').submit()">Simpan Data</button>
        </div>
    </div>
</div>

<!-- Modal Set Wali Kelas -->
<div id="modal-wali" class="z-modal-ov" style="display: none;">
    <div class="z-modal">
        <div class="z-modal-head">
            <div class="z-modal-title"><i data-lucide="user-check"></i> Set Wali Kelas</div>
            <div class="z-modal-close" onclick="document.getElementById('modal-wali').style.display='none'"><i data-lucide="x"></i></div>
        </div>
        <div class="z-modal-body">
            <form id="formWali" method="POST" action="<?php echo Helper::url('/siakad/kelas/assign-wali'); ?>">
                <div class="z-form-group">
                    <label class="z-label">Nama Kelas / Rombel</label>
                    <input type="text" name="nama_kelas" id="wali-kelas-nama" class="z-field" readonly style="background:#f1f5f9; color:var(--z-muted); cursor:not-allowed;">
                </div>
                <div class="z-form-group">
                    <label class="z-label">Pilih Guru (Wali Kelas)</label>
                    <select name="guru_id" id="wali-guru-id" class="z-field">
                        <option value="">-- Kosongkan Wali Kelas --</option>
                        <?php foreach($gurus as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
        <div class="z-modal-foot">
            <button class="btn btn-ghost" onclick="document.getElementById('modal-wali').style.display='none'">Batal</button>
            <button class="btn btn-primary" onclick="document.getElementById('formWali').submit()">Simpan Wali Kelas</button>
        </div>
    </div>
</div>

<script>
function addKelas() {
    document.getElementById('kelas-modal-title').innerHTML = '<i data-lucide="building-2"></i> Tambah Kelas';
    document.getElementById('formKelas').action = '<?php echo Helper::url("/siakad/kelas/save"); ?>';
    document.getElementById('kelas-id').value = '';
    document.getElementById('kelas-tingkat').value = '7';
    document.getElementById('kelas-nama').value = '';
    document.getElementById('kelas-wali-id').value = '';
    document.getElementById('modal-kelas').style.display = 'flex';
    lucide.createIcons();
}

function editKelas(data, waliId) {
    document.getElementById('kelas-modal-title').innerHTML = '<i data-lucide="edit-3"></i> Edit Kelas';
    document.getElementById('formKelas').action = '<?php echo Helper::url("/siakad/kelas/update"); ?>';
    document.getElementById('kelas-id').value = data.id;
    document.getElementById('kelas-tingkat').value = data.tingkat;
    document.getElementById('kelas-nama').value = data.nama_kelas;
    document.getElementById('kelas-wali-id').value = waliId;
    document.getElementById('modal-kelas').style.display = 'flex';
    lucide.createIcons();
}

function setWaliKelas(namaKelas, guruId) {
    document.getElementById('wali-kelas-nama').value = 'Kelas ' + namaKelas;
    document.getElementById('wali-guru-id').value = guruId;
    document.getElementById('modal-wali').style.display = 'flex';
    lucide.createIcons();
}
</script>
