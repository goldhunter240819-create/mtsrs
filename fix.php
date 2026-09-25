<?php

$file = 'app/Controllers/KurikulumController.php';
$content = file_get_contents($file);

// Cut from start up to simpanBobotNilai() end
$marker1 = "echo json_encode(['status' => 'success', 'message' => 'Bobot berhasil disimpan']);\n    }\n";
$pos1 = strpos($content, $marker1);
if ($pos1 === false) {
    die("Marker 1 not found");
}
$top_part = substr($content, 0, $pos1 + strlen($marker1));

// Find the start of monitoringNilaiCetak() to preserve the bottom part
$marker2 = "    public function monitoringNilaiCetak() {";
$pos2 = strpos($content, $marker2);
if ($pos2 === false) {
    die("Marker 2 not found");
}
$bottom_part = substr($content, $pos2);

$middle_part = <<<'EOD'

    public function monitoringNilaiDetail() {
        $db = Database::connect();
        $institusi = Branding::getInstitusi();
        $activeYear = AcademicYear::current();
        
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? $activeYear['id'];
        $semester = $institusi['semester'] ?? 'Ganjil';
        
        $kelas_id = $_GET['kelas_id'] ?? 0;
        $mapel_id = $_GET['mapel_id'] ?? 0;

        if (!$kelas_id || !$mapel_id) {
            die("Kelas dan Mapel harus dipilih.");
        }

        // Ambil data Kelas
        $stmt = $db->prepare("SELECT * FROM kelas WHERE id = ?");
        $stmt->execute([$kelas_id]);
        $kelas = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Ambil Wali Kelas
        $stmt_wali = $db->prepare("SELECT g.nama FROM guru_tugas gt JOIN guru g ON gt.guru_id = g.id WHERE gt.tugas = 'Wali Kelas' AND gt.keterangan = ? AND gt.tahun_ajaran = ?");
        $stmt_wali->execute(['Kelas ' . $kelas['nama_kelas'], $activeYear['name'] ?? $institusi['tahun_ajaran_id']]);
        $wali = $stmt_wali->fetchColumn();
        $kelas['wali_kelas'] = $wali ?: '-';

        // Ambil data Mapel & Guru Pengampu
        $stmt = $db->prepare("SELECT m.nama_mapel, g.nama as guru_pengampu FROM jadwal_pelajaran j 
            JOIN mapel m ON m.id = j.mapel_id 
            LEFT JOIN guru g ON g.id = j.guru_id 
            WHERE j.kelas_id = ? AND j.mapel_id = ? LIMIT 1");
        $stmt->execute([$kelas_id, $mapel_id]);
        $mapel_info = $stmt->fetch(\PDO::FETCH_ASSOC) ?: ['nama_mapel' => 'Tidak Diketahui', 'guru_pengampu' => '-'];

        // Ambil KKM
        $tingkat = (int) substr($kelas['nama_kelas'] ?? '7', 0, 1);
        if (!in_array($tingkat, [7, 8, 9])) $tingkat = 7;
        $stmt_kkm = $db->prepare("SELECT nilai_kkm FROM kurikulum_kkm WHERE tahun_ajaran_id = ? AND mapel_id = ? AND tingkat = ?");
        $stmt_kkm->execute([$tahun_ajaran_id, $mapel_id, $tingkat]);
        $kkm = $stmt_kkm->fetchColumn();
        if (!$kkm) $kkm = 70;

        // Ambil Siswa
        $stmt = $db->prepare("SELECT id, nis, nama FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
        $stmt->execute([$kelas_id]);
        $siswa_list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Ambil jenis PH apa saja yang sudah diinput untuk kelas dan mapel ini
        $stmt = $db->prepare("SELECT jenis_evaluasi, MAX(materi) as materi FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ? AND jenis_evaluasi LIKE 'PH %' GROUP BY jenis_evaluasi ORDER BY jenis_evaluasi ASC");
        $stmt->execute([$kelas_id, $mapel_id, $tahun_ajaran_id, $semester]);
        $ph_list_raw = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $ph_list = [];
        $ph_materi = [];
        foreach ($ph_list_raw as $ph) {
            $ph_list[] = $ph['jenis_evaluasi'];
            if (!empty($ph['materi'])) {
                $ph_materi[$ph['jenis_evaluasi']] = $ph['materi'];
            }
        }
        if (empty($ph_list)) {
            $ph_list = ['PH 1'];
        }

        // Ambil Semua Nilai
        $stmt = $db->prepare("SELECT siswa_id, jenis_evaluasi, nilai FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ?");
        $stmt->execute([$kelas_id, $mapel_id, $tahun_ajaran_id, $semester]);
        $nilai_raw = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $nilai_grouped = [];
        foreach ($nilai_raw as $n) {
            $nilai_grouped[$n['siswa_id']][$n['jenis_evaluasi']] = $n['nilai'];
        }

        $title = "Detail Nilai Harian - " . htmlspecialchars($mapel_info['nama_mapel'] ?? '');
        $activeMenu = "kurikulum_monitoring_nilai";

        ob_start();
        require __DIR__ . '/../../resources/views/kurikulum/monitoring_nilai_detail.php';
        $content = ob_get_clean();
        require __DIR__ . '/../../resources/views/layout.php';
    }

EOD;

file_put_contents($file, $top_part . $middle_part . $bottom_part);
echo "Fixed";
