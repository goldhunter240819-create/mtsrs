<?php use App\Core\Helper; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rapor Akademik Siswa - <?php echo htmlspecialchars($siswa['nama']); ?></title>
    <!-- Tab Favicon -->
    <link rel="shortcut icon" href="<?php echo Helper::url('/assets/images/logo.png'); ?>?v=<?php echo time(); ?>" type="image/png">
    <link rel="icon" href="<?php echo Helper::url('/assets/images/logo.png'); ?>?v=<?php echo time(); ?>" type="image/png">

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 1.5rem;
            color: #000;
        }

        .header-kop {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header-kop img {
            width: 65px;
            height: 65px;
            object-fit: contain;
        }

        .header-kop h2 { margin: 0; font-size: 16pt; font-weight: 800; }
        .header-kop h3 { margin: 4px 0 0; font-size: 12pt; font-weight: normal; }

        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 10pt;
        }

        .rapor-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .rapor-table th, .rapor-table td {
            border: 1px solid #000;
            padding: 8px;
        }

        .rapor-table th { background: #f1f5f9; text-align: center; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: right; margin-bottom: 1rem;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">🖨️ Cetak Lembar Rapor</button>
    </div>

    <div class="header-kop">
        <img src="<?php echo Helper::url('/assets/images/logo.png'); ?>" alt="Logo MTs RS">
        <div style="text-align: center;">
            <h2>LEMBAR HASIL EVALUASI BELAJAR SISWA (RAPOR)</h2>
            <h3>MTs Roudlotus Sholihin - Tahun Ajaran 2025/2026</h3>
        </div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width:120px;"><strong>Nama Siswa</strong></td>
            <td>: <?php echo htmlspecialchars($siswa['nama']); ?></td>
            <td style="width:120px;"><strong>Kelas</strong></td>
            <td>: Kelas <?php echo htmlspecialchars($siswa['nama_kelas']); ?></td>
        </tr>
        <tr>
            <td><strong>NIS / NISN</strong></td>
            <td>: <?php echo htmlspecialchars($siswa['nis']); ?> / <?php echo htmlspecialchars($siswa['nisn'] ?: '-'); ?></td>
            <td><strong>Semester</strong></td>
            <td>: Ganjil</td>
        </tr>
    </table>

    <table class="rapor-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Mata Pelajaran</th>
                <th style="width: 80px;">Kelompok</th>
                <th style="width: 90px;">Nilai Akhir</th>
                <th style="width: 100px;">Predikat</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($nilaiList as $idx => $n): ?>
            <?php 
                $val = floatval($n['nilai']);
                $pred = ($val >= 90) ? 'Sangat Baik (A)' : (($val >= 80) ? 'Baik (B)' : (($val >= 70) ? 'Cukup (C)' : 'Kurang (D)'));
            ?>
            <tr>
                <td style="text-align:center;"><?php echo $idx + 1; ?></td>
                <td><?php echo htmlspecialchars($n['nama_mapel']); ?></td>
                <td style="text-align:center;"><?php echo htmlspecialchars($n['kelompok']); ?></td>
                <td style="text-align:center; font-weight:bold;"><?php echo round($val); ?></td>
                <td style="text-align:center;"><?php echo $pred; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top: 40px; display: flex; justify-content: space-between; font-size: 10pt;">
        <div>
            Mengetahui,<br>Orang Tua / Wali Siswa<br><br><br><br>
            ( ......................................... )
        </div>
        <div>
            Singojuru, <?php echo date('d F Y'); ?><br>Wali Kelas <?php echo htmlspecialchars($siswa['nama_kelas']); ?><br><br><br><br>
            ( ......................................... )
        </div>
    </div>

</body>
</html>
