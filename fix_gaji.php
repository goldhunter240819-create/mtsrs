<?php
// Fix finance_gaji.php
$file = 'c:/xampp/htdocs/mtsrs/resources/views/keuangan/finance_gaji.php';
$c = file_get_contents($file);
$c = preg_replace('/<p class="mph-subtitle">.*?<\/div>\s*<\/div>\s*<div class="z-content-pad">\s*<div class="z-stat-icon zi-blue"><i data-lucide="users"><\/i><\/div><\/div>/is',
'<p class="mph-subtitle">Kelola penggajian guru dan staf sekolah</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById(\'mOv\').classList.add(\'open\')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Data</button>
    </div>
</div>
<div class="z-content-pad">

<?php
$sudah = 0;
$totalGaji = 0;
foreach($gaji as $g) {
    if($g[\'status\'] === \'Sudah Dibayar\') $sudah++;
    $totalGaji += $g[\'gaji_pokok\'] + $g[\'tunjangan\'];
}
?>
<div class="z-stats" style="grid-template-columns: repeat(3, 1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Karyawan</div><div class="z-stat-value"><?php echo count($gaji); ?></div></div><div class="z-stat-icon zi-blue"><i data-lucide="users"></i></div></div>', $c);
file_put_contents($file, $c);
echo "Fixed gaji.\n";
