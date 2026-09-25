<?php
// Fix finance_komite_pembayaran_siswa.php
$file = 'c:/xampp/htdocs/mtsrs/resources/views/keuangan/finance_komite_pembayaran_siswa.php';
$c = file_get_contents($file);
$c = preg_replace('/<p class="mph-subtitle">.*?<\/div>\s*<\/div>\s*<div class="z-content-pad">\s*<div class="z-stat-icon zi-blue"><i data-lucide="users"><\/i><\/div><\/div>/is',
'<p class="mph-subtitle">Pencatatan uang SPP / tagihan dari siswa</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById(\'mOv\').classList.add(\'open\')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Pembayaran</button>
    </div>
</div>
<div class="z-content-pad">

<?php
$total_dana = 0;
foreach($pembayaran as $p) {
    $total_dana += $p[\'jumlah\'];
}
?>
<div class="z-stats" style="grid-template-columns: repeat(2, 1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Siswa Telah Bayar</div><div class="z-stat-value"><?php echo count($pembayaran); ?></div></div><div class="z-stat-icon zi-blue"><i data-lucide="users"></i></div></div>', $c);
file_put_contents($file, $c);

// Fix finance_laporan.php
$file = 'c:/xampp/htdocs/mtsrs/resources/views/keuangan/finance_laporan.php';
$c = file_get_contents($file);
$c = preg_replace('/<p class="mph-subtitle">.*?<\/div>\s*<\/div>\s*<div class="z-content-pad">\s*<div class="z-stat-icon zi-green"><i data-lucide="trending-up"><\/i><\/div><\/div>/is',
'<p class="mph-subtitle">Rekapitulasi pemasukan dan pengeluaran sekolah</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        <button type="button" class="btn btn-outline" onclick="window.print()"><i data-lucide="printer" style="width:14px;height:14px;"></i>Cetak</button>
    </div>
</div>
<div class="z-content-pad">
<div class="z-stats" style="grid-template-columns: repeat(3, 1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Pemasukan</div><div class="z-stat-value">Rp <?php echo number_format($totalPemasukan,0,\',\',\'.\'); ?></div></div><div class="z-stat-icon zi-green"><i data-lucide="trending-up"></i></div></div>', $c);
file_put_contents($file, $c);

echo "Fixed others.\n";
