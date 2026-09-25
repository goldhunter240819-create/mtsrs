<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu Token Pemilih - MTs Roudlotus Sholihin</title>
    <style>
        @media print { 
            @page { size: A4; margin: 10mm; } 
            body { width: 100%; height: 100%; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0; padding: 0; background: #f1f5f9;
        }
        .print-container {
            width: 210mm; min-height: 297mm;
            background: white;
            margin: 20px auto; padding: 10mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .card {
            border: 2px dashed #94a3b8;
            border-radius: 8px;
            padding: 15px;
            position: relative;
            background: #fff;
        }
        .card-header {
            text-align: center;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .card-header h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .card-header p { margin: 3px 0 0; font-size: 12px; }
        
        .card-body { font-size: 14px; }
        .card-body table { width: 100%; margin-bottom: 10px; }
        .card-body td { padding: 3px 0; }
        
        .token-box {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            text-align: center;
            padding: 10px;
            font-size: 24px;
            font-weight: bold;
            font-family: monospace;
            letter-spacing: 5px;
            color: #0f172a;
        }
        
        .print-btn {
            position: fixed; bottom: 20px; right: 20px;
            background: #2563eb; color: white; border: none;
            padding: 15px 30px; border-radius: 50px;
            font-size: 16px; font-weight: bold; cursor: pointer;
            box-shadow: 0 4px 10px rgba(37,99,235,0.3);
            display: flex; align-items: center; gap: 10px;
        }
    </style>
</head>
<body>
    <button class="no-print print-btn" onclick="window.print()">🖨️ Cetak Kartu Token</button>

    <div class="print-container">
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="margin: 0; font-size: 20px; text-transform: uppercase;">Kartu Token Pemilih</h1>
            <p style="margin: 5px 0 0; font-size: 14px;">Event: <strong><?= htmlspecialchars($eventData['nama_event']) ?></strong></p>
        </div>
        
        <div class="grid">
            <?php foreach($votersData as $v): ?>
            <div class="card">
                <div class="card-header">
                    <h2>BILIK SUARA E-VOTING</h2>
                    <p>MTs Roudlotus Sholihin</p>
                </div>
                <div class="card-body">
                    <table>
                        <tr>
                            <td width="30%">Nama</td>
                            <td width="5%">:</td>
                            <td width="65%"><strong><?= htmlspecialchars($v['nama']) ?></strong></td>
                        </tr>
                        <tr>
                            <td>Status/Kelas</td>
                            <td>:</td>
                            <td><?= $v['user_type'] ?> <?= !empty($v['keterangan']) ? ' - ' . htmlspecialchars($v['keterangan']) : '' ?></td>
                        </tr>
                    </table>
                    <div style="font-size: 12px; text-align: center; margin-bottom: 5px; color: #475569;">PIN RAHASIA (TOKEN)</div>
                    <div class="token-box">
                        <?= $v['token'] ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
