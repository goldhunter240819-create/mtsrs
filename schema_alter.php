<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect();

$db->exec("ALTER TABLE kalender_pendidikan MODIFY COLUMN kategori ENUM('KBM','Ujian','Libur','Kegiatan','Lainnya','Pulang Dipercepat') DEFAULT 'Kegiatan'");
$db->exec("ALTER TABLE kalender_pendidikan ADD COLUMN jam_pulang TIME NULL AFTER kategori");

echo "Schema updated!";
