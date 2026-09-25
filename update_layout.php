<?php
$file = 'c:/xampp/htdocs/mtsrs/resources/views/layout.php';
$c = file_get_contents($file);

// 1. Add $isTabungan and $isBos
$c = str_replace(
    '$isKeuangan = (strpos($activeMenu, \'keuangan\') === 0);',
    "\$isKeuangan = (strpos(\$activeMenu, 'keuangan') === 0 && strpos(\$activeMenu, 'keuangan_tabungan') === false && strpos(\$activeMenu, 'keuangan_bos') === false);\n\$isTabungan = (strpos(\$activeMenu, 'tabungan') === 0 || strpos(\$activeMenu, 'keuangan_tabungan') === 0);\n\$isBos = (strpos(\$activeMenu, 'bos') === 0 || strpos(\$activeMenu, 'keuangan_bos') === 0);",
    $c
);

// 2. Separate sidebar for BOS
$bos_start = '<!-- Dropdown: BOS -->';
$bos_end_pos = strpos($c, '<!-- Dropdown: Komite -->');
if ($bos_end_pos !== false) {
    $c = preg_replace('/(<!-- Dropdown: BOS -->.*?)(<!-- Dropdown: Komite -->)/s', 
    "<?php endif; ?>\n\n<?php if (\$isBos): ?>\n<div class=\"z-nav-cat\">MANAJEMEN DANA BOS</div>\n$1\n<?php endif; ?>\n\n<?php if (\$isKeuangan): ?>\n$2", 
    $c);
}

// 3. Separate sidebar for Tabungan
$c = preg_replace('/(<!-- Dropdown: Tabungan -->.*?)(<\/div>\s*<\/div>\s*<\/div>\s*<!-- Main Content -->)/s', 
"<?php endif; ?>\n\n<?php if (\$isTabungan): ?>\n<div class=\"z-nav-cat\">TABUNGAN SISWA</div>\n$1\n<?php endif; ?>\n$2", 
$c);

file_put_contents($file, $c);
echo "layout.php updated.\n";
