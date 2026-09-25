<?php
$dbCore = \App\Core\Database::connect('core');
$inst = $dbCore->query("SELECT disable_web_trx FROM institusi LIMIT 1")->fetch();
$disableWebTrx = !empty($inst['disable_web_trx']);
?>
<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="layers"></i> Pengeluaran</h1>
        <p class="mph-subtitle">Pencatatan dana keluar dari kas sekolah</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        <?php if(!$disableWebTrx): ?>
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById('mOv').classList.add('open')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Pengeluaran</button>
        <?php else: ?>
        <div style="background:#fee2e2; color:#dc2626; padding:8px 15px; border-radius:8px; font-size:0.85rem; font-weight:600;"><i data-lucide="shield-alert" style="width:14px;height:14px;margin-right:5px;vertical-align:-2px;"></i>Pencatatan Web Dinonaktifkan</div>
        <?php endif; ?>
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
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="arrow-up-right"></i>Daftar Pengeluaran</div></div>
    <div class="z-table-wrap"><table id="tbl">
        <thead><tr><th>Tanggal</th><th>Keterangan</th><th>Kategori</th><th>Bukti</th><th>Jumlah</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach($pengeluaran as $p): ?>
            <tr>
                <td style="font-size:.82rem; color:var(--z-muted);"><?php echo date('d/m/Y H:i',strtotime($p['created_at'])); ?></td>
                <td style="font-weight:600;"><?php echo $p['keterangan']; ?></td>
                <td><span class="pill pill-purple"><?php echo $p['kategori']; ?></span></td>
                <td>
                    <?php if(!empty($p['bukti'])): ?>
                        <a href="/uploads/finance/<?php echo $p['bukti']; ?>" target="_blank" style="color:var(--z-info);text-decoration:underline;font-size:0.8rem;"><i data-lucide="paperclip" style="width:12px;height:12px;"></i> Lihat Bukti</a>
                    <?php else: ?>
                        <span style="color:var(--z-muted);font-size:0.8rem;">Tidak ada bukti</span>
                    <?php endif; ?>
                </td>
                <td class="z-amount-red">Rp <?php echo number_format($p['jumlah'],0,',','.'); ?></td>
                <td>
                    <?php if(isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])): ?>
                        <button class="btn btn-primary btn-sm" onclick="editPengeluaran(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars($p['keterangan'], ENT_QUOTES); ?>', '<?php echo $p['tanggal']; ?>', <?php echo $p['jumlah']; ?>, '<?php echo htmlspecialchars($p['kategori'], ENT_QUOTES); ?>')">Edit</button>
                        <a href="/admin/finance/komite/transaksi/delete/<?php echo $p['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengeluaran ini?');">Hapus</a>
                    <?php else: ?>
                        <span class="pill pill-blue" style="font-size:0.7rem;">Hubungi Developer</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($pengeluaran)): ?>
            <tr><td colspan="6" style="text-align:center;color:var(--z-muted);padding:20px;">Belum ada data pengeluaran.</td></tr>
            <?php endif; ?>
        </tbody>
    </table></div>
</div>

<!-- Modal Pengeluaran -->
<div id="mOv" class="z-modal-ov">
    <div class="z-modal" style="max-width: 500px;">
        <div class="z-modal-head">
            <div class="z-modal-title" id="mTitle"><i data-lucide="plus"></i> Tambah Pengeluaran</div>
            <div class="z-modal-close" onclick="document.getElementById('mOv').classList.remove('open')"><i data-lucide="x"></i></div>
        </div>
        <form action="/admin/finance/komite/transaksi/save" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="jenis" value="Pengeluaran">
            <input type="hidden" name="id" id="t_id" value="0">
            <div class="z-modal-body">
                <div class="z-form-group">
                    <label class="z-label">Kategori Pengeluaran</label>
                    <select name="kategori" id="t_kategori" class="z-field" required>
                        <?php foreach($kategoriList as $k): ?>
                        <option value="<?php echo htmlspecialchars($k); ?>"><?php echo htmlspecialchars($k); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="z-form-group">
                    <label class="z-label">Keterangan / Rincian</label>
                    <input type="text" name="keterangan" id="t_keterangan" class="z-field" required placeholder="Contoh: Beli ATK">
                </div>
                <div class="z-form-group">
                    <label class="z-label">Tanggal Transaksi</label>
                    <input type="date" name="tanggal_transaksi" id="t_tanggal" class="z-field" required value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="z-form-group">
                    <label class="z-label">Jumlah (Rp)</label>
                    <input type="text" name="jumlah" id="t_jumlah" class="z-field" required autocomplete="off" onkeyup="formatRupiah(this);">
                </div>
                <div class="z-form-group">
                    <label class="z-label">Upload Bukti Nota (Opsional)</label>
                    <input type="file" name="bukti" class="z-field" accept="image/*,application/pdf">
                </div>
            </div>
            <div class="z-modal-foot">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('mOv').classList.remove('open')">Batal</button>
                <button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:14px;height:14px;"></i> Simpan Pengeluaran</button>
            </div>
        </form>
    </div>
</div>

<script>
function formatRupiah(angka) {
    var number_string = angka.value.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    angka.value = rupiah;
}

function resetModal() {
    document.getElementById('t_id').value = '0';
    document.getElementById('t_keterangan').value = '';
    document.getElementById('t_tanggal').value = '<?php echo date("Y-m-d"); ?>';
    document.getElementById('t_jumlah').value = '';
    document.getElementById('mTitle').innerHTML = '<i data-lucide="plus"></i> Tambah Pengeluaran';
}

function editPengeluaran(id, ket, tgl, jml, kat) {
    document.getElementById('t_id').value = id;
    document.getElementById('t_keterangan').value = ket;
    document.getElementById('t_tanggal').value = tgl;
    
    var rupiah = parseInt(jml).toLocaleString('id-ID').replace(/,/g, '.');
    document.getElementById('t_jumlah').value = rupiah;
    
    document.getElementById('t_kategori').value = kat;
    document.getElementById('mTitle').innerHTML = '<i data-lucide="edit"></i> Edit Pengeluaran';
    document.getElementById('mOv').classList.add('open');
    if(typeof lucide !== 'undefined') lucide.createIcons();
}
</script>

</div>