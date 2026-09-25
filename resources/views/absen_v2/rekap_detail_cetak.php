<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Detail Timeline</title>
    <?php $logoUrl = !empty($inst['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $inst['logo']) : \App\Core\Helper::url('/assets/images/logo.png'); ?>
    <link rel="icon" type="image/png" href="<?php echo $logoUrl; ?>">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 20px;
            color: #0f172a;
        }
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .kop-logo {
            width: 80px;
            margin-right: 20px;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-text h2 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .kop-text h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .kop-text p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #334155;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 20px;
            text-decoration: underline;
        }
        .info {
            margin-bottom: 10px;
            font-size: 13px;
        }
        .info span {
            display: inline-block;
            width: 100px;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
        }
        th {
            background-color: #f1f5f9;
            font-weight: 800;
            text-align: center;
        }
        td.center {
            text-align: center;
        }
        .ttd {
            margin-top: 40px;
            width: 300px;
            float: right;
            text-align: center;
            font-size: 13px;
        }
        .ttd .nama {
            margin-top: 60px;
            font-weight: 800;
            text-decoration: underline;
        }
        @media print {
            @page {
                size: landscape;
                margin: 1cm;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="kop-surat">
        <?php 
            $logoUrl = !empty($inst['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $inst['logo']) : \App\Core\Helper::url('/assets/images/logo.png');
        ?>
        <img src="<?php echo $logoUrl; ?>" alt="Logo" class="kop-logo">
        <?php 
            $alamat = $inst['alamat'] ?? '';
            $desa = !empty($inst['desa']) ? 'Ds. ' . $inst['desa'] : '';
            $kecamatan = !empty($inst['kecamatan']) ? 'Kec. ' . $inst['kecamatan'] : '';
            $kota = !empty($inst['kota']) ? 'Kab. ' . $inst['kota'] : '';
            $provinsi = !empty($inst['provinsi']) ? 'Prov. ' . $inst['provinsi'] : '';
            $kodepos = !empty($inst['kodepos']) ? 'KP. ' . $inst['kodepos'] : '';
            
            $fullAddressArray = array_filter([$alamat, $desa, $kecamatan, $kota, $provinsi, $kodepos]);
            $fullAddress = implode(', ', $fullAddressArray);
        ?>
        <div class="kop-text">
            <h2 style="font-size: <?php echo intval($inst['kop_font_yayasan'] ?? 14); ?>px;"><?php echo htmlspecialchars($inst['yayasan'] ?? ''); ?></h2>
            <h1 style="font-size: <?php echo intval($inst['kop_font_nama'] ?? 18); ?>px;"><?php echo htmlspecialchars($inst['nama'] ?? ''); ?></h1>
            <p><?php echo htmlspecialchars($fullAddress); ?></p>
            <p>Website: <?php echo htmlspecialchars($inst['website'] ?? ''); ?> | Email: <?php echo htmlspecialchars($inst['email'] ?? ''); ?></p>
        </div>
    </div>

    <div class="title">REKAP DETAIL (TIMELINE) KEHADIRAN SISWA</div>

    <div class="info">
        <div><span>Tanggal</span>: <?php echo date('d F Y', strtotime($tanggal)); ?></div>
        <div><span>Kelas</span>: <?php echo htmlspecialchars($selectedKelas); ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width:30px;">NO</th>
                <th rowspan="2">NAMA SISWA</th>
                <th rowspan="2" style="width:60px;">MASUK</th>
                <?php if(count($mapelColumns) > 0): ?>
                <th colspan="<?php echo count($mapelColumns); ?>">JURNAL MENGAJAR (MAPEL)</th>
                <?php endif; ?>
                <th rowspan="2" style="width:80px;">STATUS</th>
            </tr>
            <tr>
                <?php foreach($mapelColumns as $mc): ?>
                <th style="width:50px;"><?php echo htmlspecialchars($mc['kode_mapel'] ?: substr($mc['nama_mapel'], 0, 7)); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($timelineData)): ?>
            <tr>
                <td colspan="<?php echo 3 + count($mapelColumns); ?>" class="center">Data siswa tidak ditemukan.</td>
            </tr>
            <?php else: ?>
                <?php $no=1; foreach($timelineData as $td): ?>
                <tr>
                    <td class="center"><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($td['nama']); ?></td>
                    <td class="center"><?php echo $td['masuk']; ?></td>
                    
                    <?php foreach($mapelColumns as $mc): 
                        $st = $td['mapel_status'][$mc['mapel_id']] ?? '-';
                        $stShort = $st == 'Hadir' ? 'H' : ($st == 'Sakit' ? 'S' : ($st == 'Izin' ? 'I' : ($st == 'Alpa' ? 'A' : ($st == 'Bolos' ? 'B' : ($st == '?' ? '-' : '-')))));
                    ?>
                    <td class="center"><?php echo $stShort; ?></td>
                    <?php endforeach; ?>
                    
                    <td class="center" style="font-weight:bold;"><?php echo $td['status_pusat'] ?: '-'; ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="ttd">
        <div>Mengetahui,</div>
        <div>Wali Kelas <?php echo htmlspecialchars($selectedKelas); ?></div>
        <div class="nama">_______________________</div>
    </div>

</body>
</html>
