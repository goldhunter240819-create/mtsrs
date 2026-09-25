<?php
/**
 * Database Table Patch for Complete SIAKAD Module in MTs RS System
 * Database Name: db_mts_rs
 */

date_default_timezone_set('Asia/Jakarta');

$host = '127.0.0.1';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=db_mts_rs;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // 1. Ekstrakurikuler Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS ekstrakurikuler (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_ekstra VARCHAR(100) NOT NULL,
         pembina_id INT NULL,
        hari VARCHAR(20) DEFAULT 'Sabtu',
        jam_kegiatan VARCHAR(50) DEFAULT '14:00 - 16:00',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 2. Kalender Pendidikan Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS kalender_pendidikan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tanggal_mulai DATE NOT NULL,
        tanggal_selesai DATE NOT NULL,
        kegiatan VARCHAR(255) NOT NULL,
        kategori ENUM('KBM', 'Ujian', 'Libur', 'Kegiatan', 'Lainnya') DEFAULT 'Kegiatan',
        keterangan TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 3. Mutasi Siswa Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS mutasi_siswa (
        id INT AUTO_INCREMENT PRIMARY KEY,
        siswa_id INT NOT NULL,
        jenis_mutasi ENUM('Masuk', 'Keluar', 'Lulus', 'DO') NOT NULL,
        tanggal_mutasi DATE NOT NULL,
        sekolah_asal_tujuan VARCHAR(150) NULL,
        alasan TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 4. Tugas Tambahan Guru Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS guru_tugas_tambahan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        guru_id INT NOT NULL,
        jabatan_tugas VARCHAR(100) NOT NULL,
        sk_nomor VARCHAR(100) NULL,
        tahun_ajaran_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 5. Penugasan Mengajar Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS penugasan_mengajar (
        id INT AUTO_INCREMENT PRIMARY KEY,
        guru_id INT NOT NULL,
        mapel_id INT NOT NULL,
        kelas_id INT NOT NULL,
        jumlah_jam INT DEFAULT 2,
        tahun_ajaran_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 6. Rapat Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS rapat (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_rapat VARCHAR(200) NOT NULL,
        tanggal DATE NOT NULL,
        waktu VARCHAR(50) DEFAULT '09:00 WIB',
        tempat VARCHAR(100) DEFAULT 'Ruang Guru',
        agenda TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 7. Rapat Peserta Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS rapat_peserta (
        id INT AUTO_INCREMENT PRIMARY KEY,
        rapat_id INT NOT NULL,
        guru_id INT NOT NULL,
        status_kehadiran ENUM('Hadir', 'Izin', 'Sakit', 'Alpa') DEFAULT 'Hadir',
        waktu_presensi DATETIME NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 8. Broadcast Notifikasi Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS broadcast_notifikasi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        judul VARCHAR(200) NOT NULL,
        pesan TEXT NOT NULL,
        target_role VARCHAR(50) DEFAULT 'Semua',
        pengirim_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Seed dummy data for new tables
    $pdo->exec("INSERT INTO ekstrakurikuler (nama_ekstra, pembina_id, hari, jam_kegiatan) VALUES 
        ('Pramuka Pasus', 1, 'Sabtu', '14:00 - 16:00'),
        ('PMR / KSR', 1, 'Jumat', '15:00 - 17:00'),
        ('Kesenian Hadrah', 1, 'Kamis', '15:30 - 17:00')
        ON DUPLICATE KEY UPDATE id=id;");

    $pdo->exec("INSERT INTO kalender_pendidikan (tanggal_mulai, tanggal_selesai, kegiatan, kategori) VALUES 
        ('2026-07-13', '2026-07-15', 'MATSAMA (Masa Ta\'aruf Siswa Madrasah)', 'Kegiatan'),
        ('2026-08-17', '2026-08-17', 'Upacara HUT Kemerdekaan RI', 'Kegiatan'),
        ('2026-12-01', '2026-12-10', 'Penilaian Akhir Semester (PAS) Ganjil', 'Ujian')
        ON DUPLICATE KEY UPDATE id=id;");

    echo "SIAKAD Tables Patched Successfully!\n";

} catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
