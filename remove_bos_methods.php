<?php

$file = 'c:/xampp/htdocs/mtsrs/app/Controllers/AdminFinanceController.php';
$content = file_get_contents($file);

// Remove bosPemasukan, bosPengeluaran, bosLaporan
$content = preg_replace('/\/\/ --- MODUL BOS ---.*?(\/\/ --- MODUL KOMITE ---)/s', '$1', $content);

// Remove saveBosTransaksi
$content = preg_replace('/public static function saveBosTransaksi\(\) \{.*?\n    \}\n/s', '', $content);

// Remove deleteBosTransaksi
$content = preg_replace('/public static function deleteBosTransaksi\(\$id\) \{.*?\n    \}\n/s', '', $content);

file_put_contents($file, $content);
echo "Removed BOS methods from AdminFinanceController.\n";

