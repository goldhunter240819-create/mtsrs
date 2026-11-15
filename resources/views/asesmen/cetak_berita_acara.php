<?php
use App\Core\Helper;

$instName = $institusi['nama'] ?? 'MTs ROUDLOTUS SHOLIHIN';
$yayasan = $institusi['yayasan'] ?? 'YAYASAN PENDIDIKAN ISLAM ROUDLOTUS SHOLIHIN';
$alamat = $institusi['alamat'] ?? '';
$desa = $institusi['desa'] ?? '';
$kec = $institusi['kecamatan'] ?? '';
$kota = $institusi['kota'] ?? 'Lampung';
$namaKepala = $institusi['nama_kepala'] ?? 'Kepala Madrasah';
$nipKepala = $institusi['nip_kepala'] ?? '-';
$logo = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
$faviconUrl = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');

$hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

$timeTgl = strtotime($ba['tanggal'] ?? date('Y-m-d'));
$namaHari = $hariIndo[date('w', $timeTgl)];
$tglFormatted = date('j', $timeTgl) . ' ' . $bulanIndo[(int)date('n', $timeTgl)] . ' ' . date('Y', $timeTgl);
$tahunPelajaran = '2026/2027';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Pelaksanaan Ujian | <?php echo htmlspecialchars($instName); ?></title>
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
            padding: 12mm 18mm;
            margin: 0 auto 20px auto;
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 16px;
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
            margin-bottom: 20px;
        }

        .doc-title h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .content-text {
            font-size: 11pt;
            line-height: 1.6;
            text-align: justify;
        }

        .ba-table {
            width: 100%;
            font-size: 11pt;
            margin: 10px 0 16px 0;
            border-collapse: collapse;
        }

        .ba-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .box-catatan {
            border: 1px solid #000;
            min-height: 35mm;
            padding: 8px 12px;
            margin: 8px 0 20px 0;
            font-size: 10.5pt;
            line-height: 1.5;
        }

        .ttd-box {
            display: flex;
            justify-content: space-between;
            font-size: 11pt;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .ttd-column {
            text-align: center;
            width: 65mm;
        }

        @media print {
            body { background: white; padding: 0; }
            .print-btn-bar { display: none; }
            .sheet {
                box-shadow: none;
                margin: 0;
                width: 100%;
                min-height: auto;
                padding: 10mm 15mm;
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
            <strong>BERITA ACARA PELAKSANAAN UJIAN</strong> &bull; <?php echo htmlspecialchars($ba['nama_ruang'] ?? 'Ruang Ujian'); ?>
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
            <h3>BERITA ACARA PELAKSANAAN UJIAN</h3>
            <div style="font-size: 10.5pt; font-weight: bold; margin-top: 3px;">
                <?php echo strtoupper(htmlspecialchars($ba['nama_event'] ?? 'UJIAN MADRASAH')); ?> TAHUN PELAJARAN <?php echo $tahunPelajaran; ?>
            </div>
        </div>

        <!-- Narasi Pembuka -->
        <div class="content-text">
            Pada hari ini <strong><?php echo $namaHari; ?></strong> tanggal <strong><?php echo $tglFormatted; ?></strong>, telah diselenggarakan ujian madrasah untuk:
        </div>

        <!-- Data Pelaksanaan -->
        <table class="ba-table">
            <tr>
                <td style="width: 48mm; font-weight: bold;">Mata Pelajaran</td>
                <td style="width: 4mm;">:</td>
                <td style="font-weight: bold;"><?php echo htmlspecialchars($ba['nama_mapel'] ?? '-'); ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Pukul / Waktu</td>
                <td>:</td>
                <td><?php echo substr($ba['jam_mulai'] ?? '07:30', 0, 5); ?> s.d. <?php echo substr($ba['jam_selesai'] ?? '09:00', 0, 5); ?> WIB</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Ruang Ujian</td>
                <td>:</td>
                <td style="font-weight: bold;"><?php echo htmlspecialchars($ba['nama_ruang'] ?? '-'); ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Jumlah Peserta Seharusnya</td>
                <td>:</td>
                <td><?php echo (int)($ba['jumlah_seharusnya'] ?? 20); ?> Orang</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Jumlah Peserta Hadir</td>
                <td>:</td>
                <td><?php echo (int)($ba['jumlah_hadir'] ?? 20); ?> Orang</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Jumlah Peserta Tidak Hadir</td>
                <td>:</td>
                <td><?php echo (int)($ba['jumlah_tidak_hadir'] ?? 0); ?> Orang</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Nama / No. Peserta Absen</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($ba['peserta_tidak_hadir'] ?: '-'); ?></td>
            </tr>
        </table>

        <!-- Catatan Khusus -->
        <div class="content-text" style="font-weight: bold; margin-bottom: 2px;">
            Catatan Selama Pelaksanaan Ujian:
        </div>
        <div class="box-catatan">
            <?php echo nl2br(htmlspecialchars($ba['catatan_kejadian'] ?: 'Ujian berlangsung secara tertib, aman, dan kondusif. Seluruh sampul naskah dibuka di depan peserta dalam keadaan baik.')); ?>
        </div>

        <div class="content-text">
            Demikian Berita Acara ini dibuat dengan sebenarnya dalam rangkap secukupnya untuk dapat dipergunakan sebagaimana mestinya.
        </div>

        <!-- Tanda Tangan -->
        <div class="ttd-box">
            <div class="ttd-column">
                <br>
                Pengawas Ruang I,<br><br><br><br>
                <strong><u><?php echo htmlspecialchars($ba['nama_pengawas1'] ?: '..........................................'); ?></u></strong><br>
                NIP/NIY: <?php echo htmlspecialchars($ba['niy_pengawas1'] ?: '-'); ?>
            </div>

            <div class="ttd-column">
                <?php echo htmlspecialchars($kota); ?>, <?php echo $tglFormatted; ?><br>
                Pengawas Ruang II,<br><br><br><br>
                <strong><u><?php echo htmlspecialchars($ba['nama_pengawas2'] ?: '..........................................'); ?></u></strong><br>
                NIP/NIY: <?php echo htmlspecialchars($ba['niy_pengawas2'] ?: '-'); ?>
            </div>
        </div>
    </div>

</body>
</html>
