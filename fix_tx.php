<?php
// Fix finance_komite_pemasukan.php
$file = 'c:/xampp/htdocs/mtsrs/resources/views/keuangan/finance_komite_pemasukan.php';
$c = file_get_contents($file);
$c = preg_replace('/<p class="mph-subtitle">.*?<\/div>\s*<\/div>\s*<div class="z-content-pad">\s*<div class="z-stat-icon zi-blue"><i data-lucide="arrow-down-left"><\/i><\/div><\/div>/is', 
'<p class="mph-subtitle">Pencatatan pemasukan, donasi, dan sumber dana sekolah</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById(\'mOv\').classList.add(\'open\')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Pemasukan</button>
    </div>
</div>
<div class="z-content-pad">

<?php
$total_dana = 0;
foreach($pemasukan as $p) {
    $total_dana += $p[\'jumlah\'];
}
?>
<div class="z-stats" style="grid-template-columns: repeat(2, 1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Transaksi</div><div class="z-stat-value"><?php echo count($pemasukan); ?></div></div><div class="z-stat-icon zi-blue"><i data-lucide="arrow-down-left"></i></div></div>', $c);
file_put_contents($file, $c);

// Fix finance_komite_pengeluaran.php
$file = 'c:/xampp/htdocs/mtsrs/resources/views/keuangan/finance_komite_pengeluaran.php';
$c = file_get_contents($file);
$c = preg_replace('/<p class="mph-subtitle">.*?<\/div>\s*<\/div>\s*<div class="z-content-pad">\s*<div class="z-stat-icon zi-amber"><i data-lucide="arrow-up-right"><\/i><\/div><\/div>/is',
'<p class="mph-subtitle">Pencatatan dana keluar dari kas sekolah</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById(\'mOv\').classList.add(\'open\')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Pengeluaran</button>
    </div>
</div>
<div class="z-content-pad">

<?php
$total_keluar = 0;
foreach($pengeluaran as $p) {
    $total_keluar += $p[\'jumlah\'];
}
?>
<div class="z-stats" style="grid-template-columns: repeat(2, 1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Transaksi</div><div class="z-stat-value"><?php echo count($pengeluaran); ?></div></div><div class="z-stat-icon zi-amber"><i data-lucide="arrow-up-right"></i></div></div>', $c);
file_put_contents($file, $c);

// Fix finance_bos_pemasukan.php
$file = 'c:/xampp/htdocs/mtsrs/resources/views/keuangan/finance_bos_pemasukan.php';
$c = file_get_contents($file);
$c = preg_replace('/<p class="mph-subtitle">.*?<\/div>\s*<\/div>\s*<div class="z-content-pad">\s*<div class="z-stat-icon zi-blue"><i data-lucide="arrow-down-left"><\/i><\/div><\/div>/is',
'<p class="mph-subtitle">Pencatatan dana masuk dari Bantuan Operasional Sekolah</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById(\'mOv\').classList.add(\'open\')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Pemasukan</button>
    </div>
</div>
<div class="z-content-pad">

<?php
$total_dana = 0;
foreach($pemasukan as $p) {
    $total_dana += $p[\'jumlah\'];
}
?>
<div class="z-stats" style="grid-template-columns: repeat(2, 1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Transaksi</div><div class="z-stat-value"><?php echo count($pemasukan); ?></div></div><div class="z-stat-icon zi-blue"><i data-lucide="arrow-down-left"></i></div></div>', $c);
file_put_contents($file, $c);

// Fix finance_bos_pengeluaran.php
$file = 'c:/xampp/htdocs/mtsrs/resources/views/keuangan/finance_bos_pengeluaran.php';
$c = file_get_contents($file);
$c = preg_replace('/<p class="mph-subtitle">.*?<\/div>\s*<\/div>\s*<div class="z-content-pad">\s*<div class="z-stat-icon zi-amber"><i data-lucide="arrow-up-right"><\/i><\/div><\/div>/is',
'<p class="mph-subtitle">Pencatatan dana keluar dari Bantuan Operasional Sekolah</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById(\'mOv\').classList.add(\'open\')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Pengeluaran</button>
    </div>
</div>
<div class="z-content-pad">

<?php
$total_keluar = 0;
foreach($pengeluaran as $p) {
    $total_keluar += $p[\'jumlah\'];
}
?>
<div class="z-stats" style="grid-template-columns: repeat(2, 1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Transaksi</div><div class="z-stat-value"><?php echo count($pengeluaran); ?></div></div><div class="z-stat-icon zi-amber"><i data-lucide="arrow-up-right"></i></div></div>', $c);
file_put_contents($file, $c);

echo "Fixed 4 transaction files.\n";
