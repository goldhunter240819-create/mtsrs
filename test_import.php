<?php
require 'app/Core/Database.php';
require 'app/Models/AcademicYear.php';

$db = \App\Core\Database::connect();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$data_raw = "1234567890123456\t12345\t1234567890\tTest Import\tL\tBandung\t2010-01-01\tIslam\tO\tWNI\t1\t1\tSMP\tSMP 1\t123\t\t\tAyah\tWiraswasta\tIbu\tIRT\t\t\tWali\t\t\tAlamat\t1\t1\tJabar\tBandung\tCicendo\tCicendo\t40171";
$lines = explode("\n", $data_raw);
$activeYear = \App\Models\AcademicYear::current();
$default_kelas_id = 1;

foreach ($lines as $line) {
    $cols = explode("\t", trim($line));
    echo "Count cols: " . count($cols) . "\n";
    if (count($cols) < 4) continue;
    
    $nik = trim($cols[0] ?? '');
    $nis = trim($cols[1] ?? '');
    $nisn = trim($cols[2] ?? '');
    $nama = trim($cols[3] ?? '');
    $jk = trim($cols[4] ?? 'L');
    $tempat_lahir = trim($cols[5] ?? '');
    $tanggal_lahir = trim($cols[6] ?? null);
    $agama = trim($cols[7] ?? 'Islam');
    $gol_darah = trim($cols[8] ?? '-');
    $kewarganegaraan = trim($cols[9] ?? 'WNI');
    $anak_ke = trim($cols[10] ?? '');
    
    $parsed_kelas_id = trim($cols[11] ?? '');
    $kelas_id = (!empty($parsed_kelas_id) && is_numeric($parsed_kelas_id)) ? intval($parsed_kelas_id) : $default_kelas_id;
    
    $pendidikan_terakhir = trim($cols[12] ?? '');
    $sekolah_asal = trim($cols[13] ?? '');
    $no_ijazah = trim($cols[14] ?? '');
    $no_paspor = trim($cols[15] ?? '');
    $no_kitas = trim($cols[16] ?? '');
    
    $nama_ayah = trim($cols[17] ?? '');
    $pekerjaan_ayah = trim($cols[18] ?? '');
    $nama_ibu = trim($cols[19] ?? '');
    $pekerjaan_ibu = trim($cols[20] ?? '');
    $penghasilan_ortu = trim($cols[21] ?? '');
    $no_hp_ortu = trim($cols[22] ?? '');
    $nama_wali = trim($cols[23] ?? '');
    $pekerjaan_wali = trim($cols[24] ?? '');
    $no_hp_wali = trim($cols[25] ?? '');
    
    $alamat = trim($cols[26] ?? '');
    $rt = trim($cols[27] ?? '');
    $rw = trim($cols[28] ?? '');
    $provinsi = trim($cols[29] ?? '');
    $kota = trim($cols[30] ?? '');
    $kecamatan = trim($cols[31] ?? '');
    $desa = trim($cols[32] ?? '');
    $kode_pos = trim($cols[33] ?? '');
    
    if (empty($nik) || empty($nama)) {
        echo "Empty NIK or Nama\n";
        continue;
    }

    $check = $db->prepare("SELECT id FROM users WHERE username = ?");
    $check->execute([$nik]);
    if ($check->fetch()) {
        echo "Exists\n";
        continue;
    }

    $username = $nik;
    $passwordHash = password_hash($nik, PASSWORD_BCRYPT);
    $qrToken = uniqid('MTSRS-S-', true) . bin2hex(random_bytes(4));

    $stmt = $db->prepare("INSERT INTO users (username, password_hash, role_id, qr_token, status) VALUES (?, ?, 3, ?, 'active')");
    $stmt->execute([$username, $passwordHash, $qrToken]);
    $userId = $db->lastInsertId();
    
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
    
    $tanggal_lahir = !empty($tanggal_lahir) ? $tanggal_lahir : null;
    $stmt = $db->prepare($sqlInsert);
    $stmt->execute([
        $userId, $nik, $nis, $nisn, $nama, $jk, $tempat_lahir, $tanggal_lahir,
        $agama, $gol_darah, $kewarganegaraan, $anak_ke, $kelas_id, $pendidikan_terakhir,
        $sekolah_asal, $no_ijazah, $no_paspor, $no_kitas, $nama_ayah, $pekerjaan_ayah,
        $nama_ibu, $pekerjaan_ibu, $penghasilan_ortu, $no_hp_ortu, $nama_wali,
        $pekerjaan_wali, $no_hp_wali, $alamat, $rt, $rw, $provinsi, $kota, $kecamatan,
        $desa, $kode_pos
    ]);
    
    $siswaId = $db->lastInsertId();
    
    if ($activeYear && $kelas_id > 0) {
        $db->prepare("INSERT INTO riwayat_kelas_siswa (siswa_id, kelas_id, tahun_ajaran_id) VALUES (?, ?, ?)")->execute([$siswaId, $kelas_id, $activeYear['id']]);
    }
    $db->prepare("INSERT INTO keuangan_tabungan (siswa_id, saldo) VALUES (?, 0.00)")->execute([$siswaId]);
    echo "Inserted $siswaId\n";
}
