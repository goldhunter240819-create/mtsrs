<?php
/**
 * Database Patch for Asesmen & Ujian Module in MTs RS System
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

    // 1. Asesmen Event (STS/PTS, SAS/PAS, SAT/PAT, Asesmen Madrasah, dll)
    $pdo->exec("CREATE TABLE IF NOT EXISTS asesmen_event (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(150) NOT NULL,
        kode VARCHAR(50) NOT NULL,
        jenis ENUM('sts', 'sas', 'sat', 'am', 'tryout', 'lainnya') DEFAULT 'sts',
        tahun_ajaran_id INT NULL,
        semester ENUM('Ganjil', 'Genap') DEFAULT 'Ganjil',
        tingkat_kelas VARCHAR(50) DEFAULT 'Semua',
        tanggal_mulai DATE NULL,
        tanggal_selesai DATE NULL,
        keterangan TEXT NULL,
        status ENUM('aktif', 'arsip') DEFAULT 'aktif',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 2. Ruang Ujian & Denah
    $pdo->exec("CREATE TABLE IF NOT EXISTS asesmen_ruang (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NULL,
        nama_ruang VARCHAR(50) NOT NULL,
        kapasitas INT DEFAULT 20,
        tipe_meja TINYINT DEFAULT 1,
        jumlah_kolom INT DEFAULT 5,
        jumlah_baris INT DEFAULT 4,
        pola_penomoran VARCHAR(20) DEFAULT 'kolom',
        lokasi VARCHAR(100) NULL,
        keterangan VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Alter table if exists but missing columns
    try {
        $colsR = $pdo->query("SHOW COLUMNS FROM asesmen_ruang")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('tipe_meja', $colsR)) $pdo->exec("ALTER TABLE asesmen_ruang ADD COLUMN tipe_meja TINYINT DEFAULT 1 AFTER kapasitas");
        if (!in_array('jumlah_kolom', $colsR)) $pdo->exec("ALTER TABLE asesmen_ruang ADD COLUMN jumlah_kolom INT DEFAULT 5 AFTER tipe_meja");
        if (!in_array('jumlah_baris', $colsR)) $pdo->exec("ALTER TABLE asesmen_ruang ADD COLUMN jumlah_baris INT DEFAULT 4 AFTER jumlah_kolom");
        if (!in_array('pola_penomoran', $colsR)) $pdo->exec("ALTER TABLE asesmen_ruang ADD COLUMN pola_penomoran VARCHAR(20) DEFAULT 'kolom' AFTER jumlah_baris");
    } catch (\Exception $e) {}

    // 3. Distribusi Peserta & Nomor Meja
    $pdo->exec("CREATE TABLE IF NOT EXISTS asesmen_peserta (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        siswa_id INT NOT NULL,
        ruang_id INT NULL,
        nomor_peserta VARCHAR(50) NULL,
        nomor_meja INT NULL,
        posisi_baris INT NULL,
        posisi_kolom INT NULL,
        posisi_bangku INT DEFAULT 1,
        sesi INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_event_siswa (event_id, siswa_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    try {
        $colsP = $pdo->query("SHOW COLUMNS FROM asesmen_peserta")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('posisi_baris', $colsP)) $pdo->exec("ALTER TABLE asesmen_peserta ADD COLUMN posisi_baris INT NULL AFTER nomor_meja");
        if (!in_array('posisi_kolom', $colsP)) $pdo->exec("ALTER TABLE asesmen_peserta ADD COLUMN posisi_kolom INT NULL AFTER posisi_baris");
        if (!in_array('posisi_bangku', $colsP)) $pdo->exec("ALTER TABLE asesmen_peserta ADD COLUMN posisi_bangku INT DEFAULT 1 AFTER posisi_kolom");
    } catch (\Exception $e) {}

    // 4. Bank Soal & Kisi-Kisi
    $pdo->exec("CREATE TABLE IF NOT EXISTS asesmen_bank_soal (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NULL,
        mapel_id INT NOT NULL,
        guru_id INT NULL,
        tingkat VARCHAR(10) DEFAULT '7',
        judul VARCHAR(200) NOT NULL,
        kode_soal VARCHAR(50) NULL,
        kisi_kisi_file VARCHAR(255) NULL,
        naskah_soal_file VARCHAR(255) NULL,
        kunci_jawaban_file VARCHAR(255) NULL,
        jumlah_pg INT DEFAULT 40,
        jumlah_esai INT DEFAULT 5,
        status ENUM('draft', 'diajukan', 'disetujui') DEFAULT 'draft',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 5. Jadwal Ujian
    $pdo->exec("CREATE TABLE IF NOT EXISTS asesmen_jadwal (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        mapel_id INT NOT NULL,
        tingkat VARCHAR(10) DEFAULT 'Semua',
        kelas_id INT NULL,
        tanggal DATE NOT NULL,
        jam_mulai TIME NOT NULL,
        jam_selesai TIME NOT NULL,
        sesi_ke INT DEFAULT 1,
        durasi_menit INT DEFAULT 90,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    try {
        $colsJ = $pdo->query("SHOW COLUMNS FROM asesmen_jadwal")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('kelas_id', $colsJ)) $pdo->exec("ALTER TABLE asesmen_jadwal ADD COLUMN kelas_id INT NULL AFTER tingkat");
        if (!in_array('sesi_ke', $colsJ)) $pdo->exec("ALTER TABLE asesmen_jadwal ADD COLUMN sesi_ke INT DEFAULT 1 AFTER jam_selesai");
    } catch (\Exception $e) {}

    // 6. Berita Acara Ujian
    $pdo->exec("CREATE TABLE IF NOT EXISTS asesmen_berita_acara (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        ruang_id INT NOT NULL,
        jadwal_id INT NULL,
        tanggal DATE NOT NULL,
        jam_mulai TIME NULL,
        jam_selesai TIME NULL,
        pengawas_1_id INT NULL,
        pengawas_2_id INT NULL,
        jumlah_seharusnya INT DEFAULT 0,
        jumlah_hadir INT DEFAULT 0,
        jumlah_tidak_hadir INT DEFAULT 0,
        peserta_tidak_hadir TEXT NULL,
        catatan_kejadian TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 7. Presensi Scan Siswa Ujian Otomatis
    $pdo->exec("CREATE TABLE IF NOT EXISTS asesmen_presensi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        jadwal_id INT NULL,
        mapel_id INT NOT NULL,
        siswa_id INT NOT NULL,
        ruang_id INT NULL,
        nomor_meja INT NULL,
        tanggal DATE NOT NULL,
        jam_scan TIME NOT NULL,
        status ENUM('hadir', 'terlambat', 'izin', 'sakit', 'alpa') DEFAULT 'hadir',
        keterangan VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_presensi (event_id, mapel_id, tanggal, siswa_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Insert Default Sample Event if table is empty
    $check = $pdo->query("SELECT COUNT(*) FROM asesmen_event")->fetchColumn();
    if ($check == 0) {
        $pdo->exec("INSERT INTO asesmen_event (nama, kode, jenis, semester, tingkat_kelas, status, keterangan) 
        VALUES 
        ('Asesmen Sumatif Tengah Semester (STS) Ganjil', 'STS-GANJIL-26', 'sts', 'Ganjil', 'Semua (7, 8, 9)', 'aktif', 'Pelaksanaan STS Ganjil Tahun Ajaran 2026/2027'),
        ('Asesmen Sumatif Akhir Semester (SAS) Ganjil', 'SAS-GANJIL-26', 'sas', 'Ganjil', 'Semua (7, 8, 9)', 'aktif', 'Penilaian Sumatif Akhir Semester Ganjil'),
        ('Asesmen Madrasah (AM) Kelas IX', 'AM-2027', 'am', 'Genap', 'Kelas 9', 'aktif', 'Ujian Madrasah Kelulusan Santri & Siswa Kelas Akhir')");
    }

    echo "Tables for Asesmen & Ujian created successfully!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
