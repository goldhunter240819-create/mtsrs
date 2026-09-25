<?php
$dir = 'c:/xampp/htdocs/mtsrs/resources/views/keuangan';
$files = glob($dir . '/*.php');

foreach ($files as $file) {
    $basename = basename($file);
    if ($basename === 'finance_dashboard.php' || $basename === 'tabungan_dashboard.php') {
        continue; // Keep the modern header on dashboards
    }

    $c = file_get_contents($file);
    // Find the modern-page-header block
    $pattern = '/<div class="modern-page-header">.*?<\/div>\s*<div class="z-content-pad">/s';
    
    if (preg_match($pattern, $c)) {
        $c = preg_replace($pattern, '<div class="z-content-pad">', $c);
        file_put_contents($file, $c);
        echo "Removed duplicate header from $basename\n";
    }
}
echo "Done.";
