<?php
require 'app/Core/Database.php';

try {
    $db = \App\Core\Database::connect();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sqlInsert = "INSERT INTO siswa (
        user_id, nik, nis, nisn, nama, jenis_kelamin, tempat_lahir, tanggal_lahir, 
        agama, gol_darah, kewarganegaraan, anak_ke, kelas_id, pendidikan_terakhir, 
        sekolah_asal, no_ijazah, no_paspor, no_kitas, nama_ayah, pekerjaan_ayah, 
        nama_ibu, pekerjaan_ibu, penghasilan_ortu, no_hp_ortu, nama_wali, 
        pekerjaan_wali, no_hp_wali, alamat, rt, rw, provinsi, kota, kecamatan, 
        desa, kode_pos, status
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, 
        ?, ?, ?, ?, ?, ?, 
        ?, ?, ?, ?, ?, ?, 
        ?, ?, ?, ?, ?, 
        ?, ?, ?, ?, ?, ?, ?, ?, 
        ?, ?, 'Aktif'
    )";
    $stmt = $db->prepare($sqlInsert);
    $data = array_fill(0, 35, 'test');
    $data[0] = 1;
    $data[7] = '2000-01-01';
    $data[12] = 1;
    $stmt->execute($data);
    echo "Success!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
