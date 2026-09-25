<?php
require 'app/Core/Database.php';

$db = \App\Core\Database::connect();

$today = date('Y-m-d');
$hari_ini = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][date('w')];

// 1. Guru Hadir
$stmt = $db->prepare("SELECT COUNT(DISTINCT guru_id) as total FROM absensi_guru WHERE tanggal = ?");
$stmt->execute([$today]);
$guruHadir = $stmt->fetch()['total'] ?? 0;

// 2. Kelas Kosong
$stmt = $db->prepare("SELECT COUNT(id) as total FROM jadwal_pelajaran WHERE hari = ?");
$stmt->execute([$hari_ini]);
$totalJadwal = $stmt->fetch()['total'] ?? 0;

$stmt = $db->prepare("SELECT COUNT(DISTINCT jadwal_id) as total FROM jurnal_guru WHERE tanggal = ?");
$stmt->execute([$today]);
$jurnalTerisi = $stmt->fetch()['total'] ?? 0;
$kelasKosong = max(0, $totalJadwal - $jurnalTerisi);

// 3. Siswa Hadir
$stmt = $db->prepare("SELECT COUNT(DISTINCT siswa_id) as total FROM absensi_siswa WHERE tanggal = ? AND status = 'H' ");
$stmt->execute([$today]);
$siswaHadir = $stmt->fetch()['total'] ?? 0;

// 4. Pelanggaran Baru
$stmt = $db->prepare("SELECT COUNT(id) as total FROM bk_poin WHERE tanggal = ?");
$stmt->execute([$today]);
$pelanggaranBaru = $stmt->fetch()['total'] ?? 0;

echo "Guru Hadir: $guruHadir\n";
echo "Kelas Kosong: $kelasKosong\n";
echo "Siswa Hadir: $siswaHadir\n";
echo "Pelanggaran Baru: $pelanggaranBaru\n";
