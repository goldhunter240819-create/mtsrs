<?php use App\Core\Helper; ?>
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="book-open" style="color: #bfdbfe;"></i> Mata Pelajaran
        </h1>
        <p class="mph-subtitle">Kelola kurikulum dan daftar mata pelajaran MTs RS.</p>
    </div>
    <div class="mph-actions">
        <button class="btn btn-primary-white" onclick="openAddModal()">
            <i data-lucide="plus-circle"></i> Tambah Mapel
        </button>
    </div>
</div>

<div class="z-panel">
    <div class="z-table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 150px;">Kode Mapel</th>
                    <th>Nama Mata Pelajaran</th>
                    <th>Kelompok</th>
                    <th style="text-align: center; width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($mapelList)): ?>
                <tr><td colspan="5" style="text-align:center; padding:2rem; color:var(--z-muted);">Belum ada data mata pelajaran.</td></tr>
                <?php else: ?>
                    <?php foreach($mapelList as $idx => $m): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td><span style="font-family: monospace; font-weight: 800; color: var(--z-primary); background: var(--z-primary-light); padding: 4px 8px; border-radius: 8px; font-size: 0.8rem; border: 1px solid rgba(37,99,235,0.2);"><?php echo htmlspecialchars($m['kode_mapel']); ?></span></td>
                        <td style="font-weight:700;"><?php echo htmlspecialchars($m['nama_mapel']); ?></td>
                        <td><span class="pill pill-blue"><?php echo htmlspecialchars($m['kelompok']); ?></span></td>
                        <td>
                            <div style="display:flex; justify-content:center; gap:8px;">
                                <a href="javascript:void(0)" class="btn btn-outline btn-sm edit-btn" style="padding:6px;" title="Edit" data-id="<?php echo $m['id']; ?>" data-kode="<?php echo htmlspecialchars($m['kode_mapel']); ?>" data-nama="<?php echo htmlspecialchars($m['nama_mapel']); ?>" data-kelompok="<?php echo htmlspecialchars($m['kelompok']); ?>">
                                    <i data-lucide="edit-3"></i>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-danger btn-sm del-btn" style="padding:6px;" title="Hapus" data-id="<?php echo $m['id']; ?>" data-nama="<?php echo htmlspecialchars($m['nama_mapel']); ?>">
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

<!-- Modal Tambah Mapel -->
<div id="modal-mapel" class="z-modal-ov" style="display: none;">
    <div class="z-modal">
        <div class="z-modal-head">
            <div class="z-modal-title"><i data-lucide="book-open"></i> <span id="modal-title-text">Tambah Mata Pelajaran</span></div>
            <div class="z-modal-close" onclick="document.getElementById('modal-mapel').style.display='none'"><i data-lucide="x"></i></div>
        </div>
        <div class="z-modal-body">
            <form id="formMapel" method="POST" action="<?php echo Helper::url('/siakad/mapel/save'); ?>">
                <input type="hidden" name="id" id="mapel_id" value="">
                <div class="z-form-group">
                    <label class="z-label">Kode Mata Pelajaran</label>
                    <input type="text" name="kode_mapel" class="z-field" placeholder="Misal: BIND" required>
                </div>
                <div class="z-form-group">
                    <label class="z-label">Nama Mata Pelajaran</label>
                    <input type="text" name="nama_mapel" class="z-field" placeholder="Misal: Bahasa Indonesia" required>
                </div>
                <div class="z-form-group">
                    <label class="z-label">Kelompok</label>
                    <select name="kelompok" class="z-field" required>
                        <option value="A">Kelompok A (Wajib)</option>
                        <option value="B">Kelompok B (Muatan Lokal/Keahlian)</option>
                        <option value="C">Kelompok C (Peminatan)</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="z-modal-foot">
            <button class="btn btn-ghost" onclick="document.getElementById('modal-mapel').style.display='none'">Batal</button>
            <button class="btn btn-primary" onclick="document.getElementById('formMapel').submit()">Simpan Data</button>
        </div>
    </div>
</div>
</div>

<form id="deleteForm" method="POST" action="<?php echo Helper::url('/siakad/mapel/delete'); ?>" style="display:none;">
    <input type="hidden" name="id" id="deleteId">
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openAddModal() {
    document.getElementById('modal-title-text').innerText = 'Tambah Mata Pelajaran';
    document.getElementById('mapel_id').value = '';
    document.getElementById('formMapel').reset();
    document.getElementById('modal-mapel').style.display = 'flex';
}

document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('modal-title-text').innerText = 'Edit Mata Pelajaran';
        document.getElementById('mapel_id').value = this.dataset.id;
        document.querySelector('input[name="kode_mapel"]').value = this.dataset.kode;
        document.querySelector('input[name="nama_mapel"]').value = this.dataset.nama;
        document.querySelector('select[name="kelompok"]').value = this.dataset.kelompok;
        document.getElementById('modal-mapel').style.display = 'flex';
    });
});

document.querySelectorAll('.del-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        let id = this.dataset.id;
        let nama = this.dataset.nama;
        Swal.fire({
            title: 'Hapus Mata Pelajaran?',
            text: "Mapel " + nama + " akan dihapus permanen. Lanjutkan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        });
    });
});
</script>
