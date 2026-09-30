<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Pembayaran Siswa</title>
    <?php if(!empty($institusi['logo'])): ?>
    <link rel="icon" type="image/png" href="/public/uploads/logo/<?php echo $institusi['logo']; ?>">
    <?php endif; ?>
    <style>
        body { font-family: 'Times New Roman', Times, serif; margin: 0; padding: 20px; background: #fff; }
        .print-container { max-width: 100%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 1.5rem; text-transform: uppercase; }
        .header h3 { margin: 5px 0 0 0; font-size: 1.2rem; font-weight: normal; }
        .header p { margin: 5px 0 0 0; font-size: 0.9rem; }
        
        @media print {
            @page { size: landscape; margin: 10mm; } 
            body { -webkit-print-color-adjust: exact; margin: 0; padding: 0; }
            .no-print { display: none !important; }
        }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 0.85rem; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background: #f8fafc; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .btn-print {
            display: inline-flex; align-items: center; gap: 8px;
            background: #2563eb; color: #fff; padding: 10px 20px; 
            text-decoration: none; border-radius: 8px; font-family: sans-serif;
            font-weight: bold; cursor: pointer; border: none; margin-bottom: 20px;
        }
        .btn-print:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="no-print" style="text-align: right;">
            <button class="btn-print" onclick="window.print()">🖨️ Cetak Laporan</button>
        </div>

        <div class="header">
            <?php if(!empty($institusi['yayasan'])): ?>
                <h3>YAYASAN <?php echo $institusi['yayasan']; ?></h3>
            <?php endif; ?>
            <h2><?php echo !empty($institusi['nama_institusi']) ? $institusi['nama_institusi'] : 'MTS ROUDLOTUS SHOLIHIN'; ?></h2>
            <p><?php echo !empty($institusi['alamat']) ? $institusi['alamat'] : 'Jl. Sukarno Hatta No. 123, Demak'; ?></p>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 1.2rem; text-decoration: underline;">LAPORAN PEMBAYARAN SISWA</h3>
            <p style="margin: 5px 0 0 0; font-weight: bold;">
                Kelas: <?= htmlspecialchars($kelas_nama) ?>
            </p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th>Nama Siswa</th>
                    <th width="80">NIS</th>
                    <th width="60">Kelas</th>
                    <th width="90">Tanggal Bayar</th>
                    <th>Periode / Kategori</th>
                    <th width="100">Status</th>
                    <th width="120">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $total = 0;
                if (!empty($pembayaran)):
                    foreach ($pembayaran as $p): 
                        $total += $p['jumlah'];
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($p['nama']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($p['nis']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($p['kelas']) ?></td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($p['tanggal'])) ?></td>
                    <td>
                        <strong><?= htmlspecialchars($p['jenis']) ?></strong>
                        <?php if($p['periode'] !== '-'): ?>
                            <br><small><?= htmlspecialchars($p['periode']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= htmlspecialchars($p['status']) ?></td>
                    <td class="text-right">Rp <?= number_format($p['jumlah'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="7" class="text-right">TOTAL</th>
                    <th class="text-right">Rp <?= number_format($total, 0, ',', '.') ?></th>
                </tr>
                <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Belum ada data pembayaran untuk filter ini.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Tanda Tangan -->
        <table style="width:100%; border:none; margin-top:50px; text-align:center;">
            <tr>
                <td style="border:none; width:50%;"></td>
                <td style="border:none; width:50%;">
                    <?php echo !empty($institusi['kota']) ? $institusi['kota'] : 'Demak'; ?>, <?php echo date('d F Y'); ?><br>
                    Bendahara,<br><br><br><br><br>
                    <strong><?php echo !empty($institusi['bendahara_nama']) ? $institusi['bendahara_nama'] : '...............................'; ?></strong>
                </td>
            </tr>
        </table>
    </div>
    
    <script>
        // Auto print pas halamannya dibuka
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
