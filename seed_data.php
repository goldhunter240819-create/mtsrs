<?php
require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

$db = Database::connect();

// Hapus yang lama kalau ada
$db->exec("DELETE FROM guru WHERE nip IN ('1234567890', '0987654321')");
$db->exec("DELETE FROM siswa WHERE nis IN ('1001', '1002')");
$db->exec("DELETE FROM users WHERE username IN ('1234567890', '0987654321', '1001', '1002')");

$hash = password_hash('123456', PASSWORD_DEFAULT);

// Buat users untuk guru
$db->exec("INSERT INTO users (username, password_hash, role_id, qr_token) VALUES ('1234567890', '$hash', 2, 'QR-GURU-1')");
$u1 = $db->lastInsertId();
$db->exec("INSERT INTO users (username, password_hash, role_id, qr_token) VALUES ('0987654321', '$hash', 2, 'QR-GURU-2')");
$u2 = $db->lastInsertId();

// Buat users untuk siswa
$db->exec("INSERT INTO users (username, password_hash, role_id, qr_token) VALUES ('1001', '$hash', 3, 'QR-SISWA-1')");
$u3 = $db->lastInsertId();
$db->exec("INSERT INTO users (username, password_hash, role_id, qr_token) VALUES ('1002', '$hash', 3, 'QR-SISWA-2')");
$u4 = $db->lastInsertId();

// Insert guru
$db->exec("INSERT INTO guru (nama, nip, user_id) VALUES ('Ahmad Subarjo, S.Pd', '1234567890', $u1)");
$db->exec("INSERT INTO guru (nama, nip, user_id) VALUES ('Siti Aminah, M.Pd', '0987654321', $u2)");

// Insert siswa
$db->exec("INSERT INTO siswa (nama, nis, nisn, user_id) VALUES ('Budi Santoso', '1001', '0010010010', $u3)");
$db->exec("INSERT INTO siswa (nama, nis, nisn, user_id) VALUES ('Ayu Lestari', '1002', '0020020020', $u4)");

echo "Data berhasil ditambahkan!";
