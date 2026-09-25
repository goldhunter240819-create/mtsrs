<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="layers"></i> Master Petugas Keuangan</h1>
        <p class="mph-subtitle">Daftar guru yang memiliki wewenang untuk mencatat tagihan dan pembayaran</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById('mOv').classList.add('open')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Petugas</button>
    
    </div>
</div>
<div class="z-content-pad">


<div class="z-panel" style="max-width: 800px;">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="users"></i>Daftar Petugas Aktif</div></div>
    <div class="z-table-wrap"><table id="tbl">
        <thead><tr><th style="width:50px;text-align:center;">No</th><th>Nama Guru</th><th>NIP</th><th style="width:150px;text-align:center;">Aksi</th></tr></thead>
        <tbody>
            <?php $no=1; foreach($petugasList as $p): ?>
            <tr>
                <td style="text-align:center;"><?php echo $no++; ?></td>
                <td style="font-weight:600;"><?php echo htmlspecialchars($p['nama']); ?></td>
                <td style="color:var(--z-muted);"><?php echo htmlspecialchars($p['nip']) ?: '-'; ?></td>
                <td style="text-align:center;">
                    <?php if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])): ?>
                        <div style="display:flex; gap:5px; justify-content:center;">
                            <button type="button" class="btn btn-danger btn-sm" onclick="openDeleteModal('<?php echo $p['id']; ?>', '<?php echo htmlspecialchars($p['nama'], ENT_QUOTES); ?>')">Hapus Akses</button>
                        </div>
                    <?php else: ?>
                        <span class="pill pill-amber" style="font-size:10px;">Akses Terbatas</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($petugasList)): ?>
            <tr><td colspan="4" style="text-align:center;color:var(--z-muted);padding:20px;">Belum ada master petugas yang ditambahkan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table></div>
</div>
</div>

</div>