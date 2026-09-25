<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="receipt"></i> Master Kategori Tagihan</h1>
        <p class="mph-subtitle">Kelola daftar kategori laporan keuangan (misal: Seragam, Komite, Buku)</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById('mOv').classList.add('open')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Kategori</button>
    
    </div>
</div>
<div class="z-content-pad">



<?php if(isset($_GET['msg'])): ?>
    <div style="background: <?php echo $_GET['msg']=='deleted' ? '#fee2e2' : '#dcfce7'; ?>; color: <?php echo $_GET['msg']=='deleted' ? '#b91c1c' : '#15803d'; ?>; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 0.85rem; border: 1px solid <?php echo $_GET['msg']=='deleted' ? '#fca5a5' : '#bbf7d0'; ?>;">
        <?php if($_GET['msg'] == 'saved'): ?>Berhasil menyimpan data kategori!
        <?php elseif($_GET['msg'] == 'deleted'): ?>Kategori berhasil dihapus!
        <?php endif; ?>
    </div>
<?php endif; ?>
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="list"></i>Daftar Kategori</div></div>
    <div class="z-table-wrap"><table id="tbl">
        <thead><tr><th style="width:50px;text-align:center;">No</th><th>Nama Kategori</th><th>Petugas (Penanggung Jawab)</th><th style="width:150px;text-align:center;">Aksi</th></tr></thead>
        <tbody>
            <?php $no=1; foreach($kategoriList as $k): ?>
            <tr>
                <td style="text-align:center;"><?php echo $no++; ?></td>
                <td style="font-weight:600;"><?php echo htmlspecialchars($k['nama_kategori']); ?></td>
                <td style="color:#64748b;">
                    <?php if (!empty($k['petugas_names_array'])): ?>
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <?php foreach ($k['petugas_names_array'] as $p_nama): ?>
                                <div><i data-lucide="user" style="width: 14px; height: 14px; color: var(--z-primary); margin-right: 4px; vertical-align: middle;"></i><?php echo htmlspecialchars($p_nama); ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <span style="font-style:italic;font-size:0.8rem;">Belum diset</span>
                    <?php endif; ?>
                </td>
                <td style="text-align:center;">
                    <?php if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])): ?>
                        <div style="display:flex; gap:5px; justify-content:center;">
                            <button type="button" class="btn btn-primary btn-sm" onclick="openEditModal('<?php echo $k['id']; ?>', '<?php echo htmlspecialchars($k['nama_kategori'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars(json_encode($k['guru_id_array']), ENT_QUOTES); ?>')">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="openDeleteModal('<?php echo $k['id']; ?>', '<?php echo htmlspecialchars($k['nama_kategori'], ENT_QUOTES); ?>')">Hapus</button>
                        </div>
                    <?php else: ?>
                        <span class="pill pill-amber" style="font-size:10px;">Akses Terbatas</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($kategoriList)): ?>
            <tr><td colspan="4" style="text-align:center;color:var(--z-muted);padding:20px;">Belum ada master kategori.</td></tr>
            <?php endif; ?>
        </tbody>
    </table></div>
</div>
</div>

<!-- Modal Tambah/Edit Kategori -->
<div id="mOv" class="z-modal-ov">
    <div class="z-modal" style="max-width: 450px;">
        <div class="z-modal-head">
            <div class="z-modal-title" id="mTitle"><i data-lucide="tag"></i> Tambah Kategori</div>
            <div class="z-modal-close" onclick="document.getElementById('mOv').classList.remove('open')"><i data-lucide="x"></i></div>
        </div>
        <form action="/keuangan/komite/kategori/save" method="POST">
            <div class="z-modal-body">
                <input type="hidden" name="id" id="k_id" value="">
                
                <div class="z-form-group">
                    <label class="z-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori" id="k_nama" class="z-field" placeholder="Misal: LKS / Seragam" required>
                </div>
                
                <div class="z-form-group">
                    <label class="z-label">Petugas (Penanggung Jawab)</label>
                    <select name="guru_ids[]" id="k_guru" class="z-field" multiple style="height: 120px;">
                        <option value="">-- Tidak Ada / Kosong --</option>
                        <?php foreach($gurus as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color:var(--z-muted); font-size:0.8rem; display:block; margin-top:5px;">Pilih guru jika pembayaran kategori ini bisa diangsur/ditangani oleh guru khusus.</small>
                </div>
            </div>
            <div class="z-modal-foot">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('mOv').classList.remove('open')">Batal</button>
                <button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:14px;height:14px;"></i> Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<script>
function moveModal() {
    var m = document.getElementById('mOv');
    if(m && m.parentElement !== document.body) {
        document.body.appendChild(m);
    }
}
document.addEventListener('DOMContentLoaded', moveModal);

function resetModal() {
    moveModal();
    document.getElementById('mTitle').innerHTML = '<i data-lucide="plus"></i> Tambah Kategori';
    document.getElementById('k_id').value = '';
    document.getElementById('k_nama').value = '';
    document.getElementById('k_guru').value = '';
    if(typeof lucide !== 'undefined') lucide.createIcons();
}
function openEditModal(id, nama, guru_ids_json) {
    moveModal();
    document.getElementById('mTitle').innerHTML = '<i data-lucide="edit-3"></i> Edit Kategori';
    document.getElementById('k_id').value = id;
    document.getElementById('k_nama').value = nama;
    
    var select = document.getElementById('k_guru');
    for (var i = 0; i < select.options.length; i++) {
        select.options[i].selected = false;
    }
    
    try {
        var ids = JSON.parse(guru_ids_json);
        if (Array.isArray(ids)) {
            for (var i = 0; i < select.options.length; i++) {
                if (ids.includes(select.options[i].value)) {
                    select.options[i].selected = true;
                }
            }
        }
    } catch(e) {}
    
    document.getElementById('mOv').classList.add('open');
    if(typeof lucide !== 'undefined') lucide.createIcons();
}
function openDeleteModal(id, nama) {
    Swal.fire({
        title: 'Hapus Kategori?',
        text: "Kategori '"+nama+"' akan dihapus. Data tagihan yang sudah memakai kategori ini mungkin terdampak.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/keuangan/komite/kategori/delete/' + id;
        }
    });
}
</script>

</div>