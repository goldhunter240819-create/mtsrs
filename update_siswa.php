<?php
$db = new PDO("mysql:host=127.0.0.1;dbname=db_mts_rs;charset=utf8", 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "
ALTER TABLE siswa 
ADD COLUMN nik VARCHAR(20) NULL AFTER nisn,
ADD COLUMN no_kk VARCHAR(20) NULL AFTER nik,
ADD COLUMN agama VARCHAR(20) NULL AFTER jenis_kelamin,
ADD COLUMN gol_darah VARCHAR(5) NULL AFTER agama,
ADD COLUMN kewarganegaraan VARCHAR(50) NULL AFTER gol_darah,
ADD COLUMN anak_ke INT(11) NULL AFTER kewarganegaraan,
ADD COLUMN pendidikan_terakhir VARCHAR(50) NULL AFTER kelas_id,
ADD COLUMN sekolah_asal VARCHAR(100) NULL AFTER pendidikan_terakhir,
ADD COLUMN no_ijazah VARCHAR(50) NULL AFTER sekolah_asal,
ADD COLUMN no_paspor VARCHAR(50) NULL AFTER no_ijazah,
ADD COLUMN no_kitas VARCHAR(50) NULL AFTER no_paspor,
ADD COLUMN pekerjaan_ayah VARCHAR(100) NULL AFTER nama_ayah,
ADD COLUMN pekerjaan_ibu VARCHAR(100) NULL AFTER nama_ibu,
ADD COLUMN penghasilan_ortu VARCHAR(100) NULL AFTER pekerjaan_ibu,
CHANGE COLUMN telepon_ortu no_hp_ortu VARCHAR(50) NULL,
ADD COLUMN nama_wali VARCHAR(150) NULL AFTER no_hp_ortu,
ADD COLUMN pekerjaan_wali VARCHAR(100) NULL AFTER nama_wali,
ADD COLUMN no_hp_wali VARCHAR(50) NULL AFTER pekerjaan_wali,
ADD COLUMN alamat TEXT NULL AFTER no_hp_wali,
ADD COLUMN rt VARCHAR(10) NULL AFTER alamat,
ADD COLUMN rw VARCHAR(10) NULL AFTER rt,
ADD COLUMN provinsi VARCHAR(100) NULL AFTER rw,
ADD COLUMN kota VARCHAR(100) NULL AFTER provinsi,
ADD COLUMN kecamatan VARCHAR(100) NULL AFTER kota,
ADD COLUMN desa VARCHAR(100) NULL AFTER kecamatan,
ADD COLUMN kode_pos VARCHAR(20) NULL AFTER desa;
";

try {
    $db->exec($sql);
    echo "Siswa table updated successfully!\n";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
