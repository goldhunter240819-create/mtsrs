<?php

$file = 'app/Controllers/KurikulumController.php';
$content = file_get_contents($file);

$insert = <<<'EOD'
    public function monitoringNilaiGlobal() {
        $db = Database::connect();
        $institusi = Branding::getInstitusi();
        $activeYear = AcademicYear::current();
        
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? $activeYear['id'];
        $semester = $institusi['semester'] ?? 'Ganjil';
        
        // 1. Ambil KKM
        $stmt_kkm = $db->prepare("SELECT mapel_id, tingkat, nilai_kkm FROM kurikulum_kkm WHERE tahun_ajaran_id = ?");
        $stmt_kkm->execute([$tahun_ajaran_id]);
        $kkm_raw = $stmt_kkm->fetchAll(\PDO::FETCH_ASSOC);
        $kkm_map = [];
        foreach ($kkm_raw as $k) {
            $kkm_map[$k['mapel_id'] . '_' . $k['tingkat']] = $k['nilai_kkm'];
        }

        // 2. Ambil Kelas
        $stmt_kelas = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat ASC, nama_kelas ASC");
        $kelas_list = $stmt_kelas->fetchAll(\PDO::FETCH_ASSOC);

        // 3. Ambil Mapel per Kelas
        $stmt_mapel = $db->query("SELECT j.kelas_id, j.mapel_id, m.nama_mapel FROM jadwal_pelajaran j JOIN mapel m ON m.id = j.mapel_id ORDER BY m.nama_mapel ASC");
        $mapel_raw = $stmt_mapel->fetchAll(\PDO::FETCH_ASSOC);
        $mapel_kelas = [];
        foreach ($mapel_raw as $m) {
            $mapel_kelas[$m['kelas_id']][] = [
                'id' => $m['mapel_id'],
                'nama' => $m['nama_mapel']
            ];
        }

        // 4. Ambil Siswa
        $stmt_siswa = $db->query("SELECT id, kelas_id, nis, nama FROM siswa WHERE status = 'Aktif' ORDER BY nama ASC");
        $siswa_raw = $stmt_siswa->fetchAll(\PDO::FETCH_ASSOC);
        $siswa_kelas = [];
        foreach ($siswa_raw as $s) {
            $siswa_kelas[$s['kelas_id']][] = $s;
        }

        // 5. Ambil Semua Nilai
        $stmt_nilai = $db->prepare("SELECT siswa_id, mapel_id, jenis_evaluasi, nilai FROM nilai_harian WHERE tahun_ajaran_id = ? AND semester = ?");
        $stmt_nilai->execute([$tahun_ajaran_id, $semester]);
        $nilai_raw = $stmt_nilai->fetchAll(\PDO::FETCH_ASSOC);
        
        $nilai_map = [];
        $ph_counts = []; // Untuk mengetahui max PH per kelas per mapel
        
        foreach ($nilai_raw as $n) {
            $nilai_map[$n['siswa_id']][$n['mapel_id']][$n['jenis_evaluasi']] = $n['nilai'];
            
            if (strpos($n['jenis_evaluasi'], 'PH ') === 0) {
                // Ekstrak angka PH
                $num = (int) str_replace('PH ', '', $n['jenis_evaluasi']);
                if (!isset($ph_counts[$n['mapel_id']]) || $num > $ph_counts[$n['mapel_id']]) {
                    $ph_counts[$n['mapel_id']] = $num;
                }
            }
        }

        require __DIR__ . '/../../resources/views/kurikulum/monitoring_nilai_global.php';
    }
EOD;

$content = preg_replace('/\}\s*$/', "\n$insert\n}", $content);
file_put_contents($file, $content);
echo "Method added.";
