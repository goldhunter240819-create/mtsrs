<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect('siakad');

try {
    // 1. Create jam_absen table
    $db->exec("CREATE TABLE IF NOT EXISTS jam_absen (
        id INT AUTO_INCREMENT PRIMARY KEY,
        jam_masuk_mulai TIME NOT NULL DEFAULT '06:00:00',
        jam_masuk_akhir TIME NOT NULL DEFAULT '07:30:00',
        jam_masuk_batas TIME NOT NULL DEFAULT '07:00:00',
        jam_pulang_mulai TIME NOT NULL DEFAULT '13:00:00',
        jam_pulang_akhir TIME NOT NULL DEFAULT '15:00:00',
        jam_pulang_kamis TIME NOT NULL DEFAULT '12:00:00',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Check if empty
    $count = $db->query("SELECT COUNT(*) FROM jam_absen")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO jam_absen (jam_masuk_mulai, jam_masuk_akhir, jam_masuk_batas, jam_pulang_mulai, jam_pulang_akhir, jam_pulang_kamis) 
            VALUES ('06:00:00', '07:30:00', '07:00:00', '13:00:00', '15:00:00', '12:00:00')");
    }
    echo "jam_absen OK.\n";
    
    // 2. Create absensi_siswa table if not exist
    $db->exec("CREATE TABLE IF NOT EXISTS absensi_siswa (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tanggal DATE NOT NULL,
        siswa_id INT NOT NULL,
        kelas_id INT NOT NULL,
        status ENUM('Hadir','Terlambat','Sakit','Izin','Alpa') DEFAULT 'Hadir',
        jam_masuk TIME NULL,
        jam_pulang TIME NULL,
        keterangan VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_tanggal_siswa (tanggal, siswa_id)
    )");
    
    // 3. Add tahun_ajaran_id to absensi_siswa
    try {
        $db->query("ALTER TABLE absensi_siswa ADD COLUMN tahun_ajaran_id INT NULL");
        echo "Added tahun_ajaran_id to absensi_siswa.\n";
    } catch(Exception $e) {
        // Ignore if already exists
    }
    
    echo "All fixed for siswa!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
