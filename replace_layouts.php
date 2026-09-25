<?php
$file = 'C:/xampp/htdocs/mtsrs/app/Controllers/AbsenV2Controller.php';
$content = file_get_contents($file);
$content = str_replace("include __DIR__ . '/../../resources/views/layout.php';", "include __DIR__ . '/../../resources/views/absen_v2_layout.php';", $content);
file_put_contents($file, $content);
echo "Replaced all layouts in AbsenV2Controller.php\n";
?>
