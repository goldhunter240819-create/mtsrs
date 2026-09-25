<?php use App\Core\Helper; ?>
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="briefcase" style="color: #bfdbfe;"></i> Tugas Tambahan Guru
        </h1>
        <p class="mph-subtitle">Daftar penugasan struktural / tambahan bagi tenaga pendidik MTs RS.</p>
    </div>
    <div class="mph-actions" style="display: flex; gap: 10px;">
        <a href="/siakad/master-tugas" class="btn btn-outline" style="background: rgba(255,255,255,0.1); color: #fff; border-color: rgba(255,255,255,0.3);">
            <i data-lucide="database"></i> Master Tugas
        </a>
        <button class="btn btn-primary-white" onclick="openAddPenugasan()">
            <i data-lucide="plus-circle"></i> Penugasan Baru
        </button>
    </div>
</div>

<div class="z-panel">
    <div class="z-table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 150px;">NIP</th>
                    <th>Nama Guru</th>
                    <th>Jabatan Tugas Tambahan</th>
                    <th>Nomor SK Penugasan</th>
                    <th style="text-align: center; width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($tugasList)): ?>
                <tr><td colspan="6" style="text-align:center; padding:2rem; color:var(--z-muted);">Belum ada penugasan tambahan.</td></tr>
                <?php else: ?>
                    <?php foreach($tugasList as $idx => $t): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td style="font-family:monospace;"><?php echo htmlspecialchars($t['nip'] ?: '-'); ?></td>
                        <td style="font-weight:800; color: var(--z-primary);"><?php echo htmlspecialchars($t['nama_guru']); ?></td>
                        <td><span class="pill pill-purple"><?php echo htmlspecialchars($t['jabatan_tugas']); ?></span></td>
                        <td style="font-size:0.85rem; color: var(--z-muted);"><?php echo htmlspecialchars($t['sk_nomor'] ?: '-'); ?></td>
                        <td>
                            <div style="display:flex; justify-content:center; gap:8px;">
                                <button type="button" class="btn btn-outline btn-sm" style="padding:6px;" title="Edit" onclick="editPenugasan(<?php echo htmlspecialchars(json_encode($t)); ?>)">
                                    <i data-lucide="edit-3"></i>
                                </button>
                                <a href="#" class="btn btn-danger btn-sm" style="padding:6px;" title="Hapus" onclick="return confirm('Hapus tugas tambahan?')">
                                    <i data-lucide="trash-2"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalTugas" class="z-modal-ov" style="display:none;">
    <div class="z-modal">
        <div class="z-modal-head">
            <div class="z-modal-title"><i data-lucide="briefcase"></i> Tambah Tugas Tambahan Guru</div>
            <div class="z-modal-close" onclick="document.getElementById('modalTugas').style.display='none'"><i data-lucide="x"></i></div>
        </div>
        <div class="z-modal-body">
            <form id="formTugas" method="POST" action="<?php echo Helper::url('/siakad/guru/tugas/save'); ?>">
                <input type="hidden" name="id" id="edit_id" value="">
                <div class="z-form-group">
                    <label class="z-label">Pilih Guru</label>
                    <select name="guru_id" class="z-field" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach($guruList as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="z-form-group">
                    <label class="z-label">Jabatan / Tugas Tambahan</label>
                    <select name="jabatan_tugas" class="z-field" required>
                        <option value="">-- Pilih Tugas Tambahan --</option>
                        <?php foreach($masterTugas as $mt): ?>
                            <option value="<?php echo htmlspecialchars($mt['nama_tugas']); ?>"><?php echo htmlspecialchars($mt['nama_tugas']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div style="font-size: 0.8rem; color: var(--z-muted); margin-top: 5px;">Jika tugas tidak ada di pilihan, tambahkan dulu di menu Master Tugas Tambahan.</div>
                </div>
                <div class="z-form-group">
                    <label class="z-label">Nomor SK Penugasan</label>
                    <input type="text" name="sk_nomor" class="z-field" placeholder="Contoh: SK/001/MTs-RS/2026">
                </div>
            </form>
        </div>
        <div class="z-modal-foot">
            <button class="btn btn-ghost" onclick="document.getElementById('modalTugas').style.display='none'">Batal</button>
            <button class="btn btn-primary" onclick="document.getElementById('formTugas').submit()">Simpan Data</button>
        </div>
    </div>
</div>

<script>
function editPenugasan(data) {
    document.getElementById('edit_id').value = data.id;
    document.querySelector('select[name="guru_id"]').value = data.guru_id;
    document.querySelector('select[name="jabatan_tugas"]').value = data.jabatan_tugas;
    document.querySelector('input[name="sk_nomor"]').value = data.sk_nomor || '';
    
    document.querySelector('.z-modal-title').innerHTML = '<i data-lucide="edit"></i> Edit Penugasan Guru';
    document.getElementById('modalTugas').style.display = 'flex';
}

function openAddPenugasan() {
    document.getElementById('edit_id').value = '';
    document.querySelector('select[name="guru_id"]').value = '';
    document.querySelector('select[name="jabatan_tugas"]').value = '';
    document.querySelector('input[name="sk_nomor"]').value = '';
    
    document.querySelector('.z-modal-title').innerHTML = '<i data-lucide="briefcase"></i> Tambah Tugas Tambahan Guru';
    document.getElementById('modalTugas').style.display = 'flex';
}
</script>
