<?php
$dir = 'c:/xampp/htdocs/mtsrs/resources/views/keuangan/';
$files = glob($dir . '{finance_*.php,tabungan_*.php}', GLOB_BRACE);

foreach ($files as $file) {
    if (strpos($file, '_cetak') !== false || strpos($file, 'kwitansi') !== false) {
        continue; // skip print views
    }
    
    $content = file_get_contents($file);
    
    // Find <div class="z-scroll">
    $startStr = '<div class="z-scroll">';
    $start = strpos($content, $startStr);
    
    if ($start !== false) {
        $content = substr($content, $start + strlen($startStr));
        
        // Remove trailing wrappers
        $endStr1 = '</div><!-- /.z-scroll -->';
        $endStr2 = '</main>';
        $end = strrpos($content, $endStr2);
        if ($end !== false) {
            $content = substr($content, 0, $end);
        }
        
        // Sometimes it's nested, so remove the last 2 </div> if they appear before </main>
        $content = preg_replace('/<\/div>\s*<\/main>\s*<\/div>\s*<\?php include .*?\?>\s*<\/body><\/html>/i', '', $content);
        $content = preg_replace('/<\/div>\s*<\/main>\s*<\/div>\s*<script>.*?<\/script>\s*<\/body><\/html>/is', '', $content);
        $content = preg_replace('/<\/div>\s*<\/main>\s*<\/div>.*$/is', '', $content); // catch-all for bottom
        
        // Find title
        $title = '';
        if (preg_match('/<div class="z-page-title">\s*(?:<i[^>]*><\/i>\s*)?(.*?)\s*<\/div>/is', $content, $m)) {
            $title = strip_tags(trim($m[1]));
        } else if (preg_match('/<div[^>]*font-size:[^>]*>(.*?)<\/div>/is', $content, $m)) {
             $title = strip_tags(trim($m[1]));
        } else if (preg_match('/\$page_title\s*=\s*[\'"](.*?)[\'"]/i', file_get_contents($file), $m)) {
             $title = trim($m[1]);
        }
        
        // Replace z-page-head
        $content = preg_replace('/<div class="z-page-head".*?>.*?<\/div>\s*<\/div>/is', '', $content, 1);
        
        // Prepend modern-page-header
        $header = '
<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="layers"></i> ' . htmlspecialchars($title) . '</h1>
        <p class="mph-subtitle">Manajemen Keuangan Terpadu</p>
    </div>
</div>
<div class="z-content-pad">
';
        $content = ltrim($header) . $content . "\n</div>";
        file_put_contents($file, $content);
        echo "Refactored $file\n";
    }
}
