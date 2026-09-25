<?php
$db = new PDO('mysql:host=localhost;dbname=db_mts_rs', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$columns = [
    'alamat' => 'TEXT NULL',
    'provinsi' => 'VARCHAR(100) NULL',
    'kota' => 'VARCHAR(100) NULL',
    'kecamatan' => 'VARCHAR(100) NULL',
    'desa' => 'VARCHAR(100) NULL',
    'rt' => 'VARCHAR(10) NULL',
    'rw' => 'VARCHAR(10) NULL',
    'status_kepegawaian' => "VARCHAR(50) NULL DEFAULT 'Honorer'",
    'npk' => 'VARCHAR(50) NULL',
    'npwp' => 'VARCHAR(50) NULL',
    'no_rekening' => 'VARCHAR(50) NULL',
    'kode_pos' => 'VARCHAR(20) NULL',
    'nrg' => 'VARCHAR(50) NULL',
    'no_peserta_sertifikasi' => 'VARCHAR(50) NULL',
    'no_sertifikat' => 'VARCHAR(50) NULL',
    'tgl_sertifikat' => 'DATE NULL',
    'jenjang_sertifikat' => 'VARCHAR(50) NULL',
    'mapel_sertifikat' => 'VARCHAR(100) NULL',
];

foreach ($columns as $col => $def) {
    try {
        $db->exec("ALTER TABLE guru ADD COLUMN $col $def");
        echo "Added column: $col\n";
    } catch (PDOException $e) {
        // Ignore if exists
        echo "Column $col already exists or error: " . $e->getMessage() . "\n";
    }
}
echo "Done.";
