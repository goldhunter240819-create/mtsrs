<?php
$file_in = 'C:/xampp/htdocs/mismifhda/app/Controllers/AbsenV2Controller.php';
$file_out = 'C:/xampp/htdocs/mtsrs/app/Controllers/AbsenV2Controller.php';
$content = file_get_contents($file_in);

$content = preg_replace('/Database::connect\([\'"][a-zA-Z0-9_]+[\'"]\)/', 'Database::connect()', $content);
$content = str_replace('core_db.', '', $content);
$content = str_replace('siakad.', '', $content);
$content = str_replace('/../../resources/views/admin/absen_v2/', '/../../resources/views/absen_v2/', $content);
$content = str_replace('/../../resources/views/siakad/layout.php', '/../../resources/views/layout.php', $content);

file_put_contents($file_out, $content);
echo "AbsenV2Controller.php ported successfully.\n";

// Copy views
$src_dir = 'C:/xampp/htdocs/mismifhda/resources/views/admin/absen_v2/';
$dest_dir = 'C:/xampp/htdocs/mtsrs/resources/views/absen_v2/';

if (!is_dir($dest_dir)) {
    mkdir($dest_dir, 0777, true);
}

$files = scandir($src_dir);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        copy($src_dir . $file, $dest_dir . $file);
        echo 'Copied: ' . $file . "\n";
    }
}
?>
