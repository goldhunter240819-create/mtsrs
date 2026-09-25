<?php
$dir = 'c:/xampp/htdocs/mtsrs/resources/views/keuangan';
$files = glob($dir . '/*.php');

foreach ($files as $file) {
    $basename = basename($file);
    if ($basename === 'finance_dashboard.php' || $basename === 'tabungan_dashboard.php') {
        continue;
    }

    $c = file_get_contents($file);

    // Look for z-page-head-row
    // It usually looks like:
    // <div class="z-page-head-row"[^>]*>
    //     <div><h1>Title</h1><p>Subtitle</p></div>
    //     <div style="display:flex; gap:10px;">...buttons...</div>
    // </div>
    
    $pattern = '/<div class="z-page-head-row"[^>]*>\s*<div>\s*<h1>(.*?)<\/h1>\s*(?:<p>(.*?)<\/p>\s*)?<\/div>\s*<div[^>]*>(.*?)<\/div>\s*<\/div>/is';
    
    if (preg_match($pattern, $c, $matches)) {
        $title = $matches[1];
        $subtitle = isset($matches[2]) ? $matches[2] : 'Manajemen Keuangan Terpadu';
        $buttons = $matches[3];
        $full_match = $matches[0];
        
        // Pick an icon based on title
        $icon = 'layers';
        if (stripos($title, 'Tagihan') !== false) $icon = 'receipt';
        if (stripos($title, 'Laporan') !== false) $icon = 'file-text';
        if (stripos($title, 'Akses') !== false) $icon = 'shield-check';
        if (stripos($title, 'Biaya') !== false) $icon = 'sliders';
        if (stripos($title, 'Gaji') !== false) $icon = 'users';
        if (stripos($title, 'Pemasukan') !== false) $icon = 'arrow-down-circle';
        if (stripos($title, 'Pengeluaran') !== false) $icon = 'arrow-up-circle';
        if (stripos($title, 'Rekap') !== false) $icon = 'pie-chart';
        if (stripos($title, 'Tabungan') !== false) $icon = 'wallet';

        $modern_header = <<<HTML
<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="$icon"></i> $title</h1>
        <p class="mph-subtitle">$subtitle</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        $buttons
    </div>
</div>
HTML;
        
        // Replace the z-page-head-row with the modern-page-header
        // Wait! We also need to insert it ABOVE <div class="z-content-pad"> if possible, or just replace it in place.
        // If we replace in place, it will be INSIDE <div class="z-content-pad">.
        // Let's replace it in place, but then move it out of z-content-pad.
        
        $c = str_replace($full_match, '', $c); // Remove the old one
        
        // Find <div class="z-content-pad">
        $pad_pos = strpos($c, '<div class="z-content-pad">');
        if ($pad_pos !== false) {
            // Insert modern_header before z-content-pad
            $c = substr_replace($c, $modern_header . "\n", $pad_pos, 0);
        } else {
            // Just put it at the top
            $c = $modern_header . "\n" . $c;
        }
        
        file_put_contents($file, $c);
        echo "Restored and upgraded header in $basename\n";
    } else {
        // Try another pattern if the first div doesn't exactly match <h1>...</h1><p>...</p>
        $pattern2 = '/<div class="z-page-head-row"[^>]*>\s*<div>\s*<h[12]>(.*?)<\/h[12]>(.*?)<\/div>\s*<\/div>/is';
        if (preg_match($pattern2, $c, $matches)) {
            $title = $matches[1];
            $subtitle = strip_tags($matches[2]);
            if(!$subtitle) $subtitle = 'Manajemen Keuangan Terpadu';
            $full_match = $matches[0];
            
            $icon = 'layers';
            
            $modern_header = <<<HTML
<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="$icon"></i> $title</h1>
        <p class="mph-subtitle">$subtitle</p>
    </div>
</div>
HTML;
            $c = str_replace($full_match, '', $c);
            $pad_pos = strpos($c, '<div class="z-content-pad">');
            if ($pad_pos !== false) {
                $c = substr_replace($c, $modern_header . "\n", $pad_pos, 0);
            } else {
                $c = $modern_header . "\n" . $c;
            }
            file_put_contents($file, $c);
            echo "Restored header without buttons in $basename\n";
        }
    }
}
echo "Done.";
