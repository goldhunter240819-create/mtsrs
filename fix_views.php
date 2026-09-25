<?php
$dir = 'C:/xampp/htdocs/mtsrs/resources/views/absen_v2/';
$files = glob($dir . '*.php');
foreach ($files as $file) {
    $content = file_get_contents($file);
    $content = str_replace('/admin/absen_v2', '/absen', $content);
    $content = str_replace('/absen/save-absen-guru', '/absen/input-absen-guru/save', $content);
    file_put_contents($file, $content);
}
echo "All views routes updated.\n";
?>
