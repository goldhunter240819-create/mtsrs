<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="layers"></i> Pengeluaran BOS</h1>
        <p class="mph-subtitle">Pencatatan dana keluar dari Bantuan Operasional Sekolah</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById('mOv').classList.add('open')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Pengeluaran</button>
    </div>
</div>
<div class="z-content-pad">

<?php
$total_keluar = 0;
foreach($pengeluaran as $p) {
    $total_keluar += $p['jumlah'];
}
?>
<div class="z-stats" style="grid-template-columns: repeat(2, 1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Transaksi</div><div class="z-stat-value"><?php echo count($pengeluaran); ?></div></div><div class="z-stat-icon zi-amber"><i data-lucide="arrow-up-right"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Dana Keluar</div><div class="z-stat-value" style="color:var(--z-danger);">Rp <?php echo number_format($total_keluar,0,',','.'); ?></div></div><div class="z-stat-icon zi-red"><i data-lucide="credit-card"></i></div></div>
</div>
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="arrow-up-right"></i>Daftar Pengeluaran BOS</div></div>
    <div class="z-table-wrap"><table id="tbl">
        <thead><tr><th>Tanggal</th><th>Keterangan</th><th>Kategori</th><th>Bukti</th><th>Jumlah</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach($pengeluaran as $p): ?>
            <tr>
                <td style="font-size:.82rem; color:var(--z-muted);"><?php echo date('d/m/Y',strtotime($p['tanggal'])); ?></td>
                <td style="font-weight:600;"><?php echo $p['keterangan']; ?></td>
                <td><span class="pill pill-purple"><?php echo $p['kategori']; ?></span></td>
                <td><a href="#" style="color:var(--z-info);text-decoration:underline;font-size:0.8rem;"><i data-lucide="paperclip" style="width:12px;height:12px;"></i> <?php echo $p['bukti']; ?></a></td>
                <td class="z-amount-red">Rp <?php echo number_format($p['jumlah'],0,',','.'); ?></td>
                <td>
                    <a href="/admin/finance/bos/transaksi/delete/<?php echo $p['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengeluaran ini?');">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($pengeluaran)): ?>
            <tr><td colspan="6" style="text-align:center;color:var(--z-muted);padding:20px;">Belum ada data pengeluaran.</td></tr>
            <?php endif; ?>
        </tbody>
    </table></div>
</div>
</div>
</div>