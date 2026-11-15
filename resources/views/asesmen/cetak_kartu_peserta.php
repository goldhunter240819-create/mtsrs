<?php
use App\Core\Helper;

$instName = $institusi['nama'] ?? 'MTs ROUDLOTUS SHOLIHIN';
$yayasan = $institusi['yayasan'] ?? 'YAYASAN PENDIDIKAN ISLAM ROUDLOTUS SHOLIHIN';
$alamat = $institusi['alamat'] ?? '';
$desa = $institusi['desa'] ?? '';
$kec = $institusi['kecamatan'] ?? '';
$kab = $institusi['kabupaten'] ?? '';
$kota = $institusi['kota'] ?? 'Lampung';
$namaKepala = $institusi['nama_kepala'] ?? 'Kepala Madrasah';
$nipKepala = $institusi['nip_kepala'] ?? '-';
$logo = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
$faviconUrl = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Peserta Ujian | <?php echo htmlspecialchars($instName); ?></title>
    <link rel="icon" type="image/png" href="<?php echo $faviconUrl; ?>">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #e2e8f0;
            margin: 0;
            padding: 20px;
        }

        .print-btn-bar {
            max-width: 210mm;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            font-family: sans-serif;
        }

        .print-btn {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .sheet {
            width: 210mm;
            min-height: 297mm;
            padding: 8mm 6mm;
            margin: 0 auto 20px auto;
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 6mm;
            page-break-after: always;
        }

        .sheet:last-child {
            page-break-after: auto;
        }

        .card-peserta {
            border: 2px solid #000;
            border-radius: 8px;
            padding: 6mm 8mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            background: #fff;
            position: relative;
        }

        .card-kop {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .card-kop img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            margin-right: 8px;
        }

        .card-kop-text {
            flex: 1;
            text-align: center;
            line-height: 1.15;
        }

        .card-kop-text .yys {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .card-kop-text .madrasah {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .card-kop-text .alamat {
            font-size: 6.5pt;
            color: #333;
        }

        .card-title {
            text-align: center;
            font-weight: bold;
            font-size: 9.5pt;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .card-body {
            display: flex;
            gap: 10px;
            flex: 1;
        }

        .card-photo-box {
            width: 25mm;
            height: 33mm;
            border: 1px dashed #666;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 7.5pt;
            color: #666;
            flex-shrink: 0;
            background: #fafafa;
        }

        .card-photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-table {
            flex: 1;
            font-size: 8.5pt;
            border-collapse: collapse;
            line-height: 1.35;
        }

        .card-table td {
            vertical-align: top;
            padding: 1.5px 0;
        }

        .card-table td.label {
            width: 26mm;
            font-weight: bold;
        }

        .card-table td.colon {
            width: 3mm;
        }

        .card-table td.val {
            font-weight: bold;
        }

        .card-footer {
            margin-top: 6px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 7.5pt;
            line-height: 1.25;
        }

        .card-qr {
            width: 16mm;
            height: 16mm;
            object-fit: contain;
        }

        .card-ttd {
            text-align: center;
            min-width: 35mm;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .print-btn-bar {
                display: none;
            }
            .sheet {
                box-shadow: none;
                margin: 0;
                width: 100%;
                height: 100%;
                padding: 6mm 4mm;
            }
            @page {
                size: A4 portrait;
                margin: 8mm;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <div>
            <strong>KARTU PESERTA UJIAN MADRASAH</strong> &bull; Total: <?php echo count($pesertaList); ?> Kartu
        </div>
        <button class="print-btn" onclick="window.print()">
            &#128424; Cetak Dokumen (A4)
        </button>
    </div>

    <?php 
    $chunks = array_chunk($pesertaList, 4);
    if (empty($chunks)) {
        echo '<div style="text-align:center; padding: 4rem; background:white; max-width:210mm; margin:0 auto;">Tidak ada data peserta untuk dicetak.</div>';
    }
    foreach($chunks as $pageIndex => $fourStudents):
    ?>
    <div class="sheet">
        <?php foreach($fourStudents as $p): 
            $fotoSrc = !empty($p['foto']) ? Helper::url('/uploads/siswa/' . $p['foto']) : null;
            $qrData = $p['qr_token'] ?? $p['nomor_peserta'];
            $qrUrl = 'https://quickchart.io/qr?size=150&margin=0&text=' . urlencode($qrData);
        ?>
        <div class="card-peserta">
            <!-- Kop Kartu -->
            <div class="card-kop">
                <img src="<?php echo $logo; ?>" alt="Logo">
                <div class="card-kop-text">
                    <div class="yys"><?php echo htmlspecialchars($yayasan); ?></div>
                    <div class="madrasah"><?php echo htmlspecialchars($instName); ?></div>
                    <div class="alamat"><?php echo htmlspecialchars($alamat . ($desa ? ', ' . $desa : '') . ($kec ? ', Kec. ' . $kec : '')); ?></div>
                </div>
            </div>

            <!-- Judul Kartu -->
            <div class="card-title">KARTU PESERTA <?php echo strtoupper(htmlspecialchars($p['nama_event'] ?: 'UJIAN MADRASAH')); ?></div>

            <!-- Body Data -->
            <div class="card-body">
                <div class="card-photo-box">
                    <?php if ($fotoSrc): ?>
                        <img src="<?php echo $fotoSrc; ?>" alt="Foto">
                    <?php else: ?>
                        Foto 2x3<br>Siswa
                    <?php endif; ?>
                </div>

                <table class="card-table">
                    <tr>
                        <td class="label">No. Peserta</td>
                        <td class="colon">:</td>
                        <td class="val" style="color: #00008E;"><?php echo htmlspecialchars($p['nomor_peserta'] ?: '-'); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Nama Peserta</td>
                        <td class="colon">:</td>
                        <td class="val"><?php echo htmlspecialchars($p['nama_siswa']); ?></td>
                    </tr>
                    <tr>
                        <td class="label">NIS / NISN</td>
                        <td class="colon">:</td>
                        <td><?php echo htmlspecialchars($p['nis']); ?> / <?php echo htmlspecialchars($p['nisn'] ?: '-'); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Kelas Asal</td>
                        <td class="colon">:</td>
                        <td><?php echo htmlspecialchars($p['nama_kelas']); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Ruang / Meja</td>
                        <td class="colon">:</td>
                        <td class="val"><?php echo htmlspecialchars($p['nama_ruang']); ?> &bull; No. Meja <?php echo $p['nomor_meja']; ?></td>
                    </tr>
                </table>
            </div>

            <!-- Footer TTD -->
            <div class="card-footer">
                <div>
                    <img src="<?php echo $qrUrl; ?>" class="card-qr" alt="QR">
                </div>
                <div class="card-ttd">
                    <?php echo htmlspecialchars($kota); ?>, <?php echo date('d F Y'); ?><br>
                    Kepala Madrasah,<br><br><br>
                    <strong><u><?php echo htmlspecialchars($namaKepala); ?></u></strong><br>
                    NIP. <?php echo htmlspecialchars($nipKepala); ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

</body>
</html>
