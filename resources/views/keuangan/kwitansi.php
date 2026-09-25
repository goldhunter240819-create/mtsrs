<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi - <?php echo htmlspecialchars($trx['nomor_kwitansi']); ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #f1f5f9;
            padding: 2rem;
            display: flex;
            justify-content: center;
        }

        .kwitansi-box {
            width: 100%;
            max-width: 650px;
            background: #ffffff;
            border: 2px dashed #0f172a;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            border-bottom: 2px double #0f172a;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .header h2 {
            margin: 0;
            font-size: 1.4rem;
            text-transform: uppercase;
        }

        .header p {
            margin: 4px 0 0;
            font-size: 0.85rem;
        }

        .kwitansi-title {
            text-align: center;
            font-weight: bold;
            font-size: 1.2rem;
            text-decoration: underline;
            margin-bottom: 1.5rem;
        }

        .row-detail {
            display: flex;
            margin-bottom: 0.8rem;
            font-size: 0.95rem;
        }

        .label {
            width: 170px;
            font-weight: bold;
        }

        .val {
            flex: 1;
        }

        .nominal-box {
            margin-top: 1.5rem;
            background: #f8fafc;
            border: 2px solid #0f172a;
            padding: 0.8rem 1.2rem;
            font-size: 1.3rem;
            font-weight: bold;
            display: inline-block;
        }

        .footer-sig {
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }

        @media print {
            body { background: white; padding: 0; }
            .kwitansi-box { border: 2px solid #000; box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="kwitansi-box">
        <div class="no-print" style="text-align: right; margin-bottom: 1rem;">
            <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">🖨️ Cetak Kwitansi</button>
        </div>

        <div class="header">
            <h2><?php echo htmlspecialchars($institusi['nama']); ?></h2>
            <p><?php echo htmlspecialchars($institusi['alamat']); ?> | Telp: <?php echo htmlspecialchars($institusi['telepon']); ?></p>
        </div>

        <div class="kwitansi-title">KWITANSI PEMBAYARAN</div>

        <div class="row-detail">
            <div class="label">No. Kwitansi</div>
            <div class="val">: <strong><?php echo htmlspecialchars($trx['nomor_kwitansi']); ?></strong></div>
        </div>

        <div class="row-detail">
            <div class="label">Telah Terima Dari</div>
            <div class="val">: <?php echo htmlspecialchars($trx['nama_siswa']); ?> (NIS: <?php echo htmlspecialchars($trx['nis']); ?> - Kelas <?php echo htmlspecialchars($trx['nama_kelas']); ?>)</div>
        </div>

        <div class="row-detail">
            <div class="label">Guna Pembayaran</div>
            <div class="val">: Pembayaran <strong><?php echo htmlspecialchars($trx['nama_komponen']); ?></strong> (<?php echo htmlspecialchars($trx['bulan'] ?: '-'); ?>)</div>
        </div>

        <div class="row-detail">
            <div class="label">Metode Bayar</div>
            <div class="val">: <?php echo htmlspecialchars($trx['metode_pembayaran']); ?></div>
        </div>

        <div class="row-detail">
            <div class="label">Tanggal Bayar</div>
            <div class="val">: <?php echo date('d F Y - H:i', strtotime($trx['tanggal_bayar'])); ?> WIB</div>
        </div>

        <div class="nominal-box">
            Rp <?php echo number_format($trx['nominal'], 0, ',', '.'); ?>,-
        </div>

        <div class="footer-sig">
            <div>
                <br>Siswa / Pembayar<br><br><br><br>
                ( <?php echo htmlspecialchars(explode(' ', $trx['nama_siswa'])[0]); ?> )
            </div>
            <div>
                Singojuru, <?php echo date('d F Y'); ?><br>Petugas Kasir / Bendahara<br><br><br><br>
                ( Bendahara MTs RS )
            </div>
        </div>
    </div>

</body>
</html>
