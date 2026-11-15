<?php
use App\Core\Helper;

$instName = $institusi['nama'] ?? 'MTs ROUDLOTUS SHOLIHIN';
$yayasan = $institusi['yayasan'] ?? 'YAYASAN PENDIDIKAN ISLAM ROUDLOTUS SHOLIHIN';
$logo = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
$faviconUrl = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Nomor Meja Ujian | <?php echo htmlspecialchars($instName); ?></title>
    <link rel="icon" type="image/png" href="<?php echo $faviconUrl; ?>">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
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
        }

        .print-btn {
            background: #f59e0b;
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
            padding: 10mm;
            margin: 0 auto 20px auto;
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: repeat(3, 1fr);
            gap: 8mm;
            page-break-after: always;
        }

        .sheet:last-child {
            page-break-after: auto;
        }

        .desk-card {
            border: 2px dashed #000;
            border-radius: 10px;
            padding: 10px 14px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #fff;
            text-align: center;
        }

        .dc-head {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 6px;
        }

        .dc-head img {
            width: 32px;
            height: 32px;
            object-fit: contain;
            margin-right: 8px;
        }

        .dc-head-text {
            flex: 1;
            text-align: left;
            line-height: 1.15;
        }

        .dc-head-text .school {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .dc-head-text .event {
            font-size: 6.5pt;
            color: #333;
            font-weight: bold;
        }

        .dc-ruang-badge {
            background: #000;
            color: #fff;
            font-size: 8pt;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
        }

        .dc-number {
            font-size: 38pt;
            font-weight: 900;
            color: #0f172a;
            line-height: 1;
            margin: 4px 0;
            letter-spacing: -1px;
        }

        .dc-sub {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            font-weight: bold;
        }

        .dc-info {
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
            margin-top: 4px;
            font-size: 8pt;
            line-height: 1.25;
        }

        .dc-name {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #000;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dc-code {
            font-family: monospace;
            font-weight: bold;
            color: #4338ca;
            font-size: 8.5pt;
        }

        @media print {
            body { background: white; padding: 0; }
            .print-btn-bar { display: none; }
            .sheet {
                box-shadow: none;
                margin: 0;
                width: 100%;
                height: 100%;
                padding: 8mm 6mm;
            }
            @page { size: A4 portrait; margin: 8mm; }
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <div>
            <strong>LABEL NOMOR MEJA UJIAN</strong> &bull; Total: <?php echo count($pesertaList); ?> Meja
        </div>
        <button class="print-btn" onclick="window.print()">
            &#128424; Cetak Label Meja (A4)
        </button>
    </div>

    <?php 
    $chunks = array_chunk($pesertaList, 6);
    if (empty($chunks)) {
        echo '<div style="text-align:center; padding: 4rem; background:white; max-width:210mm; margin:0 auto;">Tidak ada data nomor meja untuk dicetak.</div>';
    }
    foreach($chunks as $sixStudents):
    ?>
    <div class="sheet">
        <?php foreach($sixStudents as $p): ?>
        <div class="desk-card">
            <div class="dc-head">
                <img src="<?php echo $logo; ?>" alt="Logo">
                <div class="dc-head-text">
                    <div class="school"><?php echo htmlspecialchars($instName); ?></div>
                    <div class="event"><?php echo htmlspecialchars($p['nama_event'] ?: 'UJIAN MADRASAH'); ?></div>
                </div>
                <div class="dc-ruang-badge">
                    <?php echo htmlspecialchars($p['nama_ruang'] ?: 'RUANG'); ?>
                </div>
            </div>

            <div class="dc-sub">NOMOR MEJA</div>
            <div class="dc-number"><?php echo sprintf("%02d", $p['nomor_meja']); ?></div>

            <div class="dc-info">
                <div class="dc-code"><?php echo htmlspecialchars($p['nomor_peserta'] ?: '-'); ?></div>
                <div class="dc-name"><?php echo htmlspecialchars($p['nama_siswa']); ?></div>
                <div style="color: #475569; font-size: 7.5pt;"><?php echo htmlspecialchars($p['nama_kelas']); ?> &bull; NIS: <?php echo htmlspecialchars($p['nis']); ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

</body>
</html>
