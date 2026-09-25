<?php use App\Core\Helper; ?>
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="briefcase" style="color: #bfdbfe;"></i> Master Tugas Tambahan
        </h1>
        <p class="mph-subtitle">Kelola daftar tugas tambahan yang valid untuk penugasan guru.</p>
    </div>
    <div class="mph-actions" style="display: flex; gap: 10px;">
        <a href="/siakad/guru/tugas" class="btn btn-outline" style="background: rgba(255,255,255,0.1); color: #fff; border-color: rgba(255,255,255,0.3);">
            <i data-lucide="arrow-left"></i> Kembali
        </a>
        <button class="btn btn-primary-white" onclick="openAddTugas()">
            <i data-lucide="plus-circle"></i> Tambah Tugas
        </button>
    </div>
</div>

<div class="z-panel">
    <div class="z-table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Nama Tugas Tambahan</th>
                    <th>Keterangan</th>
                    <th style="text-align: center; width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($masterList)): ?>
                <tr><td colspan="4" style="text-align:center; padding:2rem; color:var(--z-muted);">Belum ada data tugas tambahan.</td></tr>
                <?php else: ?>
                    <?php foreach($masterList as $idx => $m): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td style="font-weight:700; color: var(--z-primary);"><?php echo htmlspecialchars($m['nama_tugas']); ?></td>
                        <td><?php echo htmlspecialchars($m['keterangan'] ?? '-'); ?></td>
                        <td>
                            <div style="display:flex; justify-content:center; gap:8px;">
                                <button type="button" class="btn btn-outline btn-sm" style="padding:6px;" title="Edit" onclick="editTugas(<?php echo htmlspecialchars(json_encode($m)); ?>)">
                                    <i data-lucide="edit-3"></i>
                                </button>
                                <form method="POST" action="<?php echo Helper::url('/siakad/master-tugas/delete'); ?>" onsubmit="return confirm('Yakin ingin menghapus tugas ini?');" style="margin:0;">
                                    <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding:6px;" title="Hapus">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Tugas -->
<div id="modal-tugas" class="z-modal-ov" style="display: none;">
    <div class="z-modal">
        <div class="z-modal-head">
            <div class="z-modal-title"><i data-lucide="briefcase"></i> Tambah Tugas Tambahan</div>
            <div class="z-modal-close" onclick="document.getElementById('modal-tugas').style.display='none'"><i data-lucide="x"></i></div>
        </div>
        <div class="z-modal-body">
            <form id="formTugas" method="POST" action="<?php echo Helper::url('/siakad/master-tugas/save'); ?>">
                <input type="hidden" name="id" id="edit_id" value="">
                <div class="z-form-group">
                    <label class="z-label">Nama Tugas Tambahan</label>
                    <input type="text" name="nama_tugas" class="z-field" placeholder="Misal: Waka Kesiswaan" required>
                </div>
                <div class="z-form-group">
                    <label class="z-label">Keterangan (Opsional)</label>
                    <textarea name="keterangan" id="edit_keterangan" class="z-field" placeholder="Keterangan singkat mengenai tugas ini" rows="3"></textarea>
                </div>
            </form>
        </div>
        <div class="z-modal-foot">
            <button class="btn btn-ghost" onclick="document.getElementById('modal-tugas').style.display='none'">Batal</button>
            <button class="btn btn-primary" onclick="document.getElementById('formTugas').submit()">Simpan Data</button>
        </div>
    </div>
</div>

<script>
function editTugas(data) {
    document.getElementById('edit_id').value = data.id;
    document.querySelector('input[name="nama_tugas"]').value = data.nama_tugas;
    document.getElementById('edit_keterangan').value = data.keterangan || '';
    
    document.querySelector('.z-modal-title').innerHTML = '<i data-lucide="edit"></i> Edit Tugas Tambahan';
    document.getElementById('modal-tugas').style.display = 'flex';
}

function openAddTugas() {
    document.getElementById('edit_id').value = '';
    document.querySelector('input[name="nama_tugas"]').value = '';
    document.getElementById('edit_keterangan').value = '';
    
    document.querySelector('.z-modal-title').innerHTML = '<i data-lucide="briefcase"></i> Tambah Tugas Tambahan';
    document.getElementById('modal-tugas').style.display = 'flex';
}
</script>
