<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Harian Tabungan</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 14px; background: #fff; color: #333; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 22px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 14px; }
        .filter-info { margin-bottom: 20px; font-size: 14px; display: flex; justify-content: space-between; }
        .filter-info div { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #000; }
        th { background-color: #f0f0f0; padding: 10px; font-size: 14px; text-align: center; }
        td { padding: 8px 10px; font-size: 14px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { display: flex; justify-content: flex-end; margin-top: 40px; }
        .ttd { text-align: center; width: 250px; }
        .ttd p { margin: 0 0 70px; }
        @media print {
            @page { margin: 1cm; }
            body { padding: 0; }
            button, .no-print { display: none !important; }
        }
        
        .no-print {
            background: #f8fafc;
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
        }
        .form-control {
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
        }
        .btn-filter {
            background: #3b82f6;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        .btn-print {
            background: #10b981;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="no-print">
        <form method="GET" action="/admin/tabungan/rekap/cetak" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 0;">
            <?php if (in_array($_SESSION['role_id'] ?? 0, [1, 99])): ?>
            <select name="petugas_id" class="form-control">
                <option value="0">Semua Petugas</option>
                <?php foreach($petugasList as $p): ?>
                    <option value="<?php echo $p['id']; ?>" <?php echo $petugas_id == $p['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['nama']); ?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
            <select name="kelas_id" class="form-control" style="width: 150px;">
                <option value="0">Semua Kelas</option>
                <?php foreach($kelasList as $k): ?>
                    <option value="<?php echo $k['id']; ?>" <?php echo $kelas_id == $k['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="mulai" class="form-control" value="<?php echo htmlspecialchars($tanggal_mulai); ?>">
            <span style="font-weight: 600;">s/d</span>
            <input type="date" name="akhir" class="form-control" value="<?php echo htmlspecialchars($tanggal_akhir); ?>">
            <button type="submit" class="btn-filter">Terapkan Filter</button>
            <button type="button" class="btn-print" onclick="window.print()">Cetak Sekarang</button>
        </form>
    </div>

    <div class="header">
        <h2>Rekapitulasi Harian Tabungan Siswa</h2>
        <p>Laporan Transaksi Mutasi Tabungan</p>
    </div>

    <div class="filter-info">
        <div>
            <div><strong>Periode:</strong> <?php echo date('d-m-Y', strtotime($tanggal_mulai)); ?> s/d <?php echo date('d-m-Y', strtotime($tanggal_akhir)); ?></div>
            <div><strong>Petugas:</strong> <?php echo htmlspecialchars($label_petugas); ?></div>
            <div><strong>Kelas:</strong> <?php echo htmlspecialchars($label_kelas); ?></div>
        </div>
        <div>
            <div><strong>Tgl Cetak:</strong> <?php echo date('d-m-Y H:i'); ?></div>
            <div><strong>Pencetak:</strong> <?php echo htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin'); ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="18%">Siswa</th>
                <th width="10%">Kelas</th>
                <th width="15%">Petugas</th>
                <th width="12%">Setor (Rp)</th>
                <th width="12%">Tarik (Rp)</th>
                <th width="10%">Saldo</th>
                <th width="6%">Trx</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if (empty($rekap)): 
            ?>
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada data mutasi pada periode ini.</td>
                </tr>
            <?php 
                else: 
                    $no = 1;
                    $g_setor = 0;
                    $g_tarik = 0;
                    $g_trx = 0;
                    foreach($rekap as $r): 
                        $g_setor += $r['total_setor'];
                        $g_tarik += $r['total_tarik'];
                        $g_trx += $r['jumlah_transaksi'];
            ?>
                <tr>
                    <td class="text-center"><?php echo $no++; ?></td>
                    <td class="text-center"><?php echo date('d-m-Y', strtotime($r['tgl'])); ?></td>
                    <td><?php echo htmlspecialchars($r['nama_siswa']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($r['nama_kelas']); ?></td>
                    <td><?php echo htmlspecialchars($r['nama_petugas']); ?></td>
                    <td class="text-right"><?php echo number_format($r['total_setor'], 0, ',', '.'); ?></td>
                    <td class="text-right"><?php echo number_format($r['total_tarik'], 0, ',', '.'); ?></td>
                    <td class="text-right"><?php echo number_format($r['total_setor'] - $r['total_tarik'], 0, ',', '.'); ?></td>
                    <td class="text-center"><?php echo $r['jumlah_transaksi']; ?></td>
                </tr>
            <?php 
                    endforeach; 
            ?>
                <tr style="font-weight: bold; background-color: #f9f9f9;">
                    <td colspan="5" class="text-right">TOTAL KESELURUHAN</td>
                    <td class="text-right">Rp <?php echo number_format($g_setor, 0, ',', '.'); ?></td>
                    <td class="text-right">Rp <?php echo number_format($g_tarik, 0, ',', '.'); ?></td>
                    <td class="text-right">Rp <?php echo number_format($g_setor - $g_tarik, 0, ',', '.'); ?></td>
                    <td class="text-center"><?php echo $g_trx; ?></td>
                </tr>
            <?php 
                endif; 
            ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="ttd">
            <p>Mengetahui,<br>Admin/Petugas Keuangan</p>
            <p>__________________________</p>
        </div>
    </div>

</body>
</html>
