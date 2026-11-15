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

$t = (int)($ruang['tipe_meja'] ?? 1);
$kolom = (int)($ruang['jumlah_kolom'] ?? 5);
$baris = (int)($ruang['jumlah_baris'] ?? 6);
$kapasitas = (int)($ruang['kapasitas'] ?? ($t * $kolom * $baris));
$pola = $ruang['pola_penomoran'] ?? 'kolom';

// Grid desk map
$gridMap = [];
if ($t == 1) {
    if ($pola === 'kolom') {
        $no = 1;
        for ($k = 1; $k <= $kolom; $k++) {
            for ($b = 1; $b <= $baris; $b++) {
                if ($no <= $kapasitas) {
                    $gridMap[$b][$k][1] = $no++;
                }
            }
        }
    } elseif ($pola === 'baris') {
        $no = 1;
        for ($b = 1; $b <= $baris; $b++) {
            for ($k = 1; $k <= $kolom; $k++) {
                if ($no <= $kapasitas) {
                    $gridMap[$b][$k][1] = $no++;
                }
            }
        }
    } else { // zigzag
        $no = 1;
        for ($b = 1; $b <= $baris; $b++) {
            $kRange = ($b % 2 == 1) ? range(1, $kolom) : range($kolom, 1);
            foreach ($kRange as $k) {
                if ($no <= $kapasitas) {
                    $gridMap[$b][$k][1] = $no++;
                }
            }
        }
    }
} else {
    $no = 1;
    for ($b = 1; $b <= $baris; $b++) {
        for ($k = 1; $k <= $kolom; $k++) {
            if ($no <= $kapasitas) {
                $gridMap[$b][$k][1] = $no++;
            }
            if ($no <= $kapasitas) {
                $gridMap[$b][$k][2] = $no++;
            }
        }
    }
}

$bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tglFormatted = date('j') . ' ' . $bulanIndo[(int)date('n')] . ' ' . date('Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Denah Tempat Duduk - <?php echo htmlspecialchars($ruang['nama_ruang'] ?? 'Ruang Ujian'); ?> | <?php echo htmlspecialchars($instName); ?></title>
    <link rel="icon" type="image/png" href="<?php echo $faviconUrl; ?>">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #e2e8f0;
            margin: 0;
            padding: 15px;
        }

        .print-btn-bar {
            max-width: 297mm;
            margin: 0 auto 12px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            font-family: sans-serif;
        }

        .print-btn {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 8px 18px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }

        .paper-a4 {
            width: 297mm;
            min-height: 210mm;
            background: white;
            margin: 0 auto;
            padding: 10mm 12mm;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
            position: relative;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #000;
            margin-bottom: 10px;
            padding-bottom: 8px;
        }
        .kop-logo {
            width: 75px;
            text-align: center;
            vertical-align: middle;
        }
        .kop-logo img {
            width: 70px;
            height: auto;
        }
        .kop-text {
            text-align: center;
            vertical-align: middle;
        }
        .kop-yayasan {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-nama {
            font-size: 17pt;
            font-weight: bold;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .kop-alamat {
            font-size: 9.5pt;
            font-style: italic;
            margin: 0;
        }

        .doc-title {
            text-align: center;
            margin: 6px 0 10px 0;
        }
        .doc-title h2 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title p {
            font-size: 10.5pt;
            margin: 3px 0 0 0;
            font-weight: bold;
        }

        .info-meta {
            width: 100%;
            margin-bottom: 10px;
            font-size: 10pt;
        }
        .info-meta td {
            padding: 2px 4px;
        }

        /* Classroom Board */
        .board-box {
            border: 2px solid #000;
            background: #f1f5f9;
            text-align: center;
            font-weight: bold;
            padding: 6px 0;
            margin: 0 auto 12px auto;
            width: 80%;
            font-size: 10.5pt;
            letter-spacing: 1px;
        }

        /* Desk Card Grid */
        .grid-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .grid-container td {
            padding: 4px;
            vertical-align: top;
        }

        .desk-box {
            border: 1.5px solid #000;
            border-radius: 4px;
            padding: 4px;
            height: 58px;
            background: #fff;
            text-align: center;
            font-size: 8.5pt;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .desk-box.empty {
            border: 1px dashed #666;
            background: #f9f9f9;
            color: #777;
        }
        .desk-num {
            font-weight: bold;
            font-size: 9pt;
            border-bottom: 1px solid #ccc;
            padding-bottom: 1px;
        }
        .desk-student {
            font-weight: bold;
            font-size: 8.5pt;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .desk-meta {
            font-size: 7.5pt;
            color: #333;
        }

        /* Double desk cell */
        .double-cell {
            display: flex;
            gap: 4px;
        }
        .double-cell .desk-box {
            flex: 1;
        }

        /* Signatures */
        .sig-table {
            width: 100%;
            margin-top: 15px;
            font-size: 10pt;
        }
        .sig-table td {
            text-align: center;
            vertical-align: top;
            width: 50%;
        }

        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .print-btn-bar {
                display: none !important;
            }
            .paper-a4 {
                width: 100%;
                min-height: 100%;
                margin: 0;
                padding: 6mm 10mm;
                box-shadow: none;
            }
            @page {
                size: A4 landscape;
                margin: 6mm;
            }
        }
    </style>
</head>
<body>

<div class="print-btn-bar">
    <div>
        <strong>Denah Meja Ruangan: <?php echo htmlspecialchars($ruang['nama_ruang'] ?? '-'); ?></strong>
        <span style="color: #64748b; margin-left: 10px;">(Formasi: <?php echo $t; ?> &times; <?php echo $kolom; ?> &times; <?php echo $baris; ?> = <?php echo $kapasitas; ?> Peserta)</span>
    </div>
    <div>
        <button class="print-btn" onclick="window.print()">
            Cetak Denah (A4 Landscape)
        </button>
    </div>
</div>

<div class="paper-a4">
    <!-- KOP SURAT -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                <img src="<?php echo $logo; ?>" alt="Logo">
            </td>
            <td class="kop-text">
                <p class="kop-yayasan"><?php echo htmlspecialchars($yayasan); ?></p>
                <h1 class="kop-nama"><?php echo htmlspecialchars($instName); ?></h1>
                <p class="kop-alamat"><?php echo htmlspecialchars($alamat . ($desa ? ', Ds. ' . $desa : '') . ($kec ? ', Kec. ' . $kec : '') . ($kota ? ', ' . $kota : '')); ?></p>
            </td>
            <td style="width: 75px;"></td>
        </tr>
    </table>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title">
        <h2>DENAH TEMPAT DUDUK PESERTA UJIAN</h2>
        <p><?php echo strtoupper(htmlspecialchars($event['nama'] ?? 'ASESMEN MADRASAH')); ?></p>
    </div>

    <!-- META INFO -->
    <table class="info-meta">
        <tr>
            <td style="width: 14%;"><strong>Ruangan</strong></td>
            <td style="width: 1%;">:</td>
            <td style="width: 35%;"><strong><?php echo htmlspecialchars($ruang['nama_ruang'] ?? '-'); ?></strong> (<?php echo htmlspecialchars($ruang['lokasi'] ?: 'Lantai 1'); ?>)</td>
            <td style="width: 15%;"><strong>Formasi Meja</strong></td>
            <td style="width: 1%;">:</td>
            <td style="width: 34%;"><?php echo $t; ?> &times; <?php echo $kolom; ?> &times; <?php echo $baris; ?> (<?php echo $t == 1 ? '1 Siswa Mandiri' : '2 Siswa Meja Gandeng'; ?>)</td>
        </tr>
        <tr>
            <td><strong>Semester / TA</strong></td>
            <td>:</td>
            <td><?php echo htmlspecialchars($event['semester'] ?? 'Ganjil'); ?> / 2026/2027</td>
            <td><strong>Total Peserta</strong></td>
            <td>:</td>
            <td><strong><?php echo count($pesertaMeja); ?></strong> dari kapasitas <?php echo $kapasitas; ?> meja</td>
        </tr>
    </table>

    <!-- PAPAN TULIS & MEJA PENGAWAS -->
    <div class="board-box">
        [ PINTU MASUK ] &nbsp;&nbsp;&nbsp;&mdash;&mdash;&mdash;&nbsp;&nbsp;&nbsp; PAPAN TULIS DEPAN & MEJA PENGAWAS &nbsp;&nbsp;&nbsp;&mdash;&mdash;&mdash;&nbsp;&nbsp;&nbsp; [ MEJA PENGAWAS ]
    </div>

    <!-- GRID MEJA -->
    <table class="grid-container">
        <?php for ($b = 1; $b <= $baris; $b++): ?>
        <tr>
            <?php for ($k = 1; $k <= $kolom; $k++): ?>
                <?php if ($t == 1): 
                    $noMeja = $gridMap[$b][$k][1] ?? null;
                    $st = ($noMeja && isset($pesertaMeja[$noMeja])) ? $pesertaMeja[$noMeja] : null;
                ?>
                    <td style="width: <?php echo round(100 / $kolom, 2); ?>%;">
                        <?php if ($noMeja): ?>
                            <div class="desk-box <?php echo $st ? '' : 'empty'; ?>">
                                <div class="desk-num">MEJA <?php echo sprintf("%02d", $noMeja); ?></div>
                                <?php if ($st): ?>
                                    <div class="desk-student"><?php echo htmlspecialchars($st['nama_siswa']); ?></div>
                                    <div class="desk-meta">
                                        <?php echo htmlspecialchars($st['nama_kelas']); ?> &bull; <?php echo htmlspecialchars($st['nomor_peserta'] ?: $st['nis']); ?>
                                    </div>
                                <?php else: ?>
                                    <div style="margin: auto; font-size: 8pt;">( KOSONG )</div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </td>
                <?php else: 
                    $noMeja1 = $gridMap[$b][$k][1] ?? null;
                    $noMeja2 = $gridMap[$b][$k][2] ?? null;
                    $st1 = ($noMeja1 && isset($pesertaMeja[$noMeja1])) ? $pesertaMeja[$noMeja1] : null;
                    $st2 = ($noMeja2 && isset($pesertaMeja[$noMeja2])) ? $pesertaMeja[$noMeja2] : null;
                ?>
                    <td style="width: <?php echo round(100 / $kolom, 2); ?>%;">
                        <div class="double-cell">
                            <!-- Bangku 1 (Kiri) -->
                            <div class="desk-box <?php echo $st1 ? '' : 'empty'; ?>">
                                <div class="desk-num">MEJA <?php echo $noMeja1 ? sprintf("%02d", $noMeja1) : '-'; ?> (L)</div>
                                <?php if ($st1): ?>
                                    <div class="desk-student"><?php echo htmlspecialchars($st1['nama_siswa']); ?></div>
                                    <div class="desk-meta"><?php echo htmlspecialchars($st1['nama_kelas']); ?> &bull; <?php echo htmlspecialchars($st1['nomor_peserta'] ?: $st1['nis']); ?></div>
                                <?php else: ?>
                                    <div style="margin: auto; font-size: 7.5pt;">(Kosong)</div>
                                <?php endif; ?>
                            </div>
                            <!-- Bangku 2 (Kanan) -->
                            <div class="desk-box <?php echo $st2 ? '' : 'empty'; ?>">
                                <div class="desk-num">MEJA <?php echo $noMeja2 ? sprintf("%02d", $noMeja2) : '-'; ?> (R)</div>
                                <?php if ($st2): ?>
                                    <div class="desk-student"><?php echo htmlspecialchars($st2['nama_siswa']); ?></div>
                                    <div class="desk-meta"><?php echo htmlspecialchars($st2['nama_kelas']); ?> &bull; <?php echo htmlspecialchars($st2['nomor_peserta'] ?: $st2['nis']); ?></div>
                                <?php else: ?>
                                    <div style="margin: auto; font-size: 7.5pt;">(Kosong)</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                <?php endif; ?>
            <?php endfor; ?>
        </tr>
        <?php endfor; ?>
    </table>

    <div style="text-align: center; font-size: 9pt; font-weight: bold; letter-spacing: 1px; color: #555; margin-bottom: 10px;">
        &mdash;&mdash;&mdash; BAGIAN BELAKANG RUANGAN &mdash;&mdash;&mdash;
    </div>

    <!-- SIGNATURES -->
    <table class="sig-table">
        <tr>
            <td>
                Mengetahui,<br>
                Ketua Panitia Ujian Madrasah<br><br><br><br>
                <strong><u>WARNO, S.Pd.I., M.Pd.Gr.</u></strong><br>
                NIP. 198203152009011008
            </td>
            <td>
                Gunung Terang, <?php echo $tglFormatted; ?><br>
                Pengawas Ruang Ujian<br><br><br><br>
                <strong><u>( ............................................................. )</u></strong><br>
                NIP/NIY. .................................................
            </td>
        </tr>
    </table>
</div>

</body>
</html>
