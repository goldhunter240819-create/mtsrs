<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect('siakad');
$db->exec('DROP TABLE IF EXISTS catatan_wali_kelas');
$sql = "CREATE TABLE catatan_wali_kelas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT,
    kelas_id INT,
    tahun_ajaran_id INT,
    semester VARCHAR(50),
    tanggal DATE,
    jenis VARCHAR(50),
    catatan TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";
$db->exec($sql);
echo 'Success';
