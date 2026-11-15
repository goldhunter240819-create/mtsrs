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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelaksanaan Ujian | <?php echo htmlspecialchars($instName); ?></title>
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
            margin-bottom: 16px;
        }

        .doc-title h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 25px;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }

        .data-table th {
            background: #f1f5f9;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9.5pt;
        }

        .ttd-box {
            display: flex;
            justify-content: flex-end;
            font-size: 10.5pt;
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
            <strong>JADWAL PELAKSANAAN UJIAN MADRASAH</strong>
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
            <h3>JADWAL PELAKSANAAN UJIAN MADRASAH</h3>
            <div style="font-size: 10.5pt; font-weight: bold; margin-top: 3px;">TAHUN PELAJARAN 2026/2027</div>
        </div>

        <!-- Tabel Jadwal -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th style="width: 140px;">Hari, Tanggal</th>
                    <th style="width: 110px;">Waktu</th>
                    <th>Mata Pelajaran</th>
                    <th style="width: 80px;">Tingkat</th>
                    <th style="width: 80px;">Durasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($jadwalList)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada jadwal ujian yang terdaftar.</td>
                </tr>
                <?php else: ?>
                    <?php foreach($jadwalList as $idx => $j): 
                        $timeTgl = strtotime($j['tanggal']);
                        $nHari = $hariIndo[date('w', $timeTgl)];
                        $tglFmt = date('j', $timeTgl) . ' ' . $bulanIndo[(int)date('n', $timeTgl)] . ' ' . date('Y', $timeTgl);
                    ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $idx + 1; ?></td>
                        <td><strong><?php echo $nHari; ?></strong>, <?php echo $tglFmt; ?></td>
                        <td style="text-align: center;"><?php echo substr($j['jam_mulai'], 0, 5); ?> - <?php echo substr($j['jam_selesai'], 0, 5); ?> WIB</td>
                        <td><strong><?php echo htmlspecialchars($j['nama_mapel']); ?></strong></td>
                        <td style="text-align: center;">Kelas <?php echo htmlspecialchars($j['tingkat']); ?></td>
                        <td style="text-align: center;"><?php echo $j['durasi_menit']; ?> Menit</td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- TTD Kepala -->
        <div class="ttd-box">
            <div class="ttd-column">
                <?php echo htmlspecialchars($kota); ?>, <?php echo date('d F Y'); ?><br>
                Kepala Madrasah,<br><br><br><br>
                <strong><u><?php echo htmlspecialchars($namaKepala); ?></u></strong><br>
                NIP. <?php echo htmlspecialchars($nipKepala); ?>
            </div>
        </div>
    </div>

</body>
</html>
