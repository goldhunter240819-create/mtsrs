<?php
use App\Core\Helper;

$instName = $institusi['nama'] ?? 'MTs ROUDLOTUS SHOLIHIN';
$yayasan = $institusi['yayasan'] ?? 'YAYASAN PENDIDIKAN ISLAM ROUDLOTUS SHOLIHIN';
$alamat = $institusi['alamat'] ?? '';
$desa = $institusi['desa'] ?? '';
$kec = $institusi['kecamatan'] ?? '';
$kota = $institusi['kota'] ?? 'Lampung';
$logo = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
$faviconUrl = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');

$hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

$timeTgl = strtotime($tanggal);
$namaHari = $hariIndo[date('w', $timeTgl)];
$tglFormatted = date('j', $timeTgl) . ' ' . $bulanIndo[(int)date('n', $timeTgl)] . ' ' . date('Y', $timeTgl);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir Peserta Ujian | <?php echo htmlspecialchars($instName); ?></title>
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
            padding: 12mm 15mm;
            margin: 0 auto 20px auto;
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        /* Kop Surat Standar MTs RS */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 14px;
        }

        .kop-surat img {
            width: 75px;
            height: 75px;
            object-fit: contain;
            margin-right: 15px;
        }

        .kop-text {
            flex: 1;
            text-align: center;
            line-height: 1.2;
        }

        .kop-text h2 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-text h1 {
            margin: 2px 0;
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-text p {
            margin: 2px 0;
            font-size: 8.5pt;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 12px;
        }

        .doc-title h3 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .meta-table {
            width: 100%;
            font-size: 9.5pt;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 20px;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
        }

        .data-table th {
            background: #f1f5f9;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
        }

        .ttd-box {
            display: flex;
            justify-content: space-between;
            font-size: 9.5pt;
            page-break-inside: avoid;
        }

        .ttd-column {
            text-align: center;
            width: 60mm;
        }

        @media print {
            body { background: white; padding: 0; }
            .print-btn-bar { display: none; }
            .sheet {
                box-shadow: none;
                margin: 0;
                width: 100%;
                min-height: auto;
                padding: 10mm 12mm;
            }
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <div>
            <strong>DAFTAR HADIR PESERTA UJIAN</strong> &bull; <?php echo htmlspecialchars($ruang['nama_ruang'] ?? 'Semua Ruang'); ?>
        </div>
        <button class="print-btn" onclick="window.print()">
            &#128424; Cetak Dokumen (A4)
        </button>
    </div>

    <div class="sheet">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <img src="<?php echo $logo; ?>" alt="Logo">
            <div class="kop-text">
                <h2><?php echo htmlspecialchars($yayasan); ?></h2>
                <h1><?php echo htmlspecialchars($instName); ?></h1>
                <p><?php echo htmlspecialchars($alamat . ($desa ? ', ' . $desa : '') . ($kec ? ', Kec. ' . $kec : '') . ($kota ? ', ' . $kota : '')); ?></p>
            </div>
        </div>

        <!-- Judul Dokumen -->
        <div class="doc-title">
            <h3>DAFTAR HADIR PESERTA <?php echo strtoupper(htmlspecialchars($event['nama'] ?? 'UJIAN MADRASAH')); ?></h3>
            <div style="font-size: 10pt; font-weight: bold; margin-top: 2px;">TAHUN PELAJARAN 2026/2027</div>
        </div>

        <!-- Meta Info -->
        <table class="meta-table">
            <tr>
                <td style="width: 25mm; font-weight: bold;">Mata Pelajaran</td>
                <td style="width: 3mm;">:</td>
                <td style="width: 70mm; font-weight: bold;"><?php echo htmlspecialchars($mapel['nama_mapel'] ?? '-'); ?></td>
                <td style="width: 25mm; font-weight: bold;">Hari, Tanggal</td>
                <td style="width: 3mm;">:</td>
                <td><?php echo $namaHari . ', ' . $tglFormatted; ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Ruang Ujian</td>
                <td>:</td>
                <td style="font-weight: bold;"><?php echo htmlspecialchars($ruang['nama_ruang'] ?? '-'); ?></td>
                <td style="font-weight: bold;">Waktu</td>
                <td>:</td>
                <td>07.30 - 09.00 WIB</td>
            </tr>
        </table>

        <!-- Tabel Peserta -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 28px;">No</th>
                    <th style="width: 75px;">No. Meja</th>
                    <th style="width: 105px;">No. Peserta</th>
                    <th>Nama Peserta Didik</th>
                    <th style="width: 60px;">Kelas</th>
                    <th colspan="2" style="width: 150px;">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pesertaList)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem;">Tidak ada peserta terdaftar pada ruangan ini.</td>
                </tr>
                <?php else: ?>
                    <?php foreach($pesertaList as $idx => $p): 
                        $no = $idx + 1;
                        $isOdd = ($no % 2 !== 0);
                    ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $no; ?></td>
                        <td style="text-align: center; font-weight: bold;">Meja <?php echo sprintf("%02d", $p['nomor_meja']); ?></td>
                        <td style="text-align: center; font-family: monospace; font-weight: bold;"><?php echo htmlspecialchars($p['nomor_peserta'] ?: '-'); ?></td>
                        <td style="font-weight: bold;"><?php echo htmlspecialchars($p['nama_siswa']); ?></td>
                        <td style="text-align: center;"><?php echo htmlspecialchars($p['nama_kelas']); ?></td>
                        <td style="width: 75px; height: 32px; font-size: 8pt; vertical-align: bottom;">
                            <?php if ($isOdd): ?>
                                <?php echo $no; ?>. ....................
                            <?php endif; ?>
                        </td>
                        <td style="width: 75px; height: 32px; font-size: 8pt; vertical-align: bottom;">
                            <?php if (!$isOdd): ?>
                                <?php echo $no; ?>. ....................
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- TTD Pengawas -->
        <div class="ttd-box">
            <div class="ttd-column">
                <br>
                Pengawas Ruang I,<br><br><br><br>
                ( .................................................... )<br>
                NIP/NIY.
            </div>

            <div class="ttd-column">
                <?php echo htmlspecialchars($kota); ?>, <?php echo $tglFormatted; ?><br>
                Pengawas Ruang II,<br><br><br><br>
                ( .................................................... )<br>
                NIP/NIY.
            </div>
        </div>
    </div>

</body>
</html>
