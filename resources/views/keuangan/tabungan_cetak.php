<?php
// Tampilan Cetak Struk Mutasi Tabungan
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Tabungan - <?php echo htmlspecialchars($mutasi['nama_siswa']); ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 14px; background: #eee; display: flex; justify-content: center; padding: 20px; }
        .struk-container { width: 300px; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 10px; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0 0; font-size: 12px; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .detail-label { font-weight: bold; }
        .divider { border-bottom: 1px dashed #000; margin: 10px 0; }
        .amount { font-size: 20px; font-weight: bold; text-align: right; margin: 15px 0; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #555; }
        @media print {
            body { background: #fff; padding: 0; }
            .struk-container { box-shadow: none; border: none; width: 100%; max-width: 300px; margin: 0 auto; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="struk-container">
    <div class="header">
        <h2>BANK MINI SEKOLAH</h2>
        <p>STRUK BUKTI <?php echo strtoupper($mutasi['jenis_mutasi']); ?> TUNAI</p>
    </div>

    <div class="detail-row">
        <span class="detail-label">Tanggal</span>
        <span><?php echo date('d-m-Y H:i', strtotime($mutasi['tanggal'])); ?></span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Petugas</span>
        <span><?php echo htmlspecialchars($mutasi['nama_petugas'] ? $mutasi['nama_petugas'] : 'Admin'); ?></span>
    </div>
    
    <div class="divider"></div>

    <div class="detail-row">
        <span class="detail-label">NIS</span>
        <span><?php echo htmlspecialchars($mutasi['nis']); ?></span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Nama</span>
        <span><?php echo htmlspecialchars($mutasi['nama_siswa']); ?></span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Kelas</span>
        <span><?php echo htmlspecialchars($mutasi['nama_kelas']); ?></span>
    </div>

    <div class="divider"></div>

    <div class="detail-row">
        <span class="detail-label">Saldo Awal</span>
        <span>Rp <?php echo number_format($mutasi['saldo_sebelum'], 0, ',', '.'); ?></span>
    </div>

    <div class="amount" style="color: <?php echo $mutasi['jenis_mutasi'] == 'Setor' ? '#10b981' : '#ef4444'; ?>">
        <?php echo $mutasi['jenis_mutasi'] == 'Setor' ? '+' : '-'; ?> Rp <?php echo number_format($mutasi['jumlah'], 0, ',', '.'); ?>
    </div>

    <div class="detail-row">
        <span class="detail-label">Saldo Akhir</span>
        <span>Rp <?php echo number_format($mutasi['saldo_sesudah'], 0, ',', '.'); ?></span>
    </div>

    <?php if(!empty($mutasi['keterangan'])): ?>
    <div class="divider"></div>
    <div style="font-size: 12px;">Ket: <?php echo htmlspecialchars($mutasi['keterangan']); ?></div>
    <?php endif; ?>

    <div class="divider"></div>
    
    <div class="footer">
        <p>Terima kasih telah menabung.</p>
        <p>Simpan struk ini sebagai bukti transaksi yang sah.</p>
    </div>
</div>

</body>
</html>
