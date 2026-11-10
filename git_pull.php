<?php
// Endpoint sinkronisasi update terbaru dari GitHub
header('Content-Type: text/plain; charset=utf-8');
echo "==============================================\n";
echo "  SINKRONISASI SERVER MTs ROUDLOTUS SHOLIHIN\n";
echo "==============================================\n\n";

if (!function_exists('shell_exec')) {
    echo "Fungsi shell_exec dinonaktifkan di hosting ini.\n";
    echo "Silakan gunakan cPanel > Git Version Control > Update from Remote.\n";
    exit;
}

echo "[1/2] Menyimpan perubahan lokal (git stash)...\n";
$stash = @shell_exec('git stash 2>&1');
echo ($stash ? trim($stash) : 'OK') . "\n\n";

echo "[2/2] Menarik update terbaru dari GitHub (git pull origin main)...\n";
$pull = @shell_exec('git pull origin main 2>&1');
echo ($pull ? trim($pull) : 'Tidak ada respon.') . "\n\n";

echo "==============================================\n";
echo "  SELESAI!\n";
echo "==============================================\n";

