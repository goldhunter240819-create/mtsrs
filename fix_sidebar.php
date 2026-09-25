<?php
$file = 'C:/xampp/htdocs/mtsrs/resources/views/absen_v2/sidebar.php';
$content = file_get_contents($file);
$content = str_replace('/admin/absen_v2', '/absen', $content);
$content = str_replace('/absen_v2/scanner', '/absen/scanner', $content);
file_put_contents($file, $content);
echo "Sidebar fixed.\n";
?>
