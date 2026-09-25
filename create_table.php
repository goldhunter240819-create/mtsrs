<?php
$db = new PDO('mysql:host=localhost;dbname=db_mts_rs', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "CREATE TABLE IF NOT EXISTS `guru_tugas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guru_id` int(11) NOT NULL,
  `tugas` varchar(100) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `tahun_ajaran` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$db->exec($sql);
echo "Table guru_tugas created successfully.";
