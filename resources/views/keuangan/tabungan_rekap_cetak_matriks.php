<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Matriks Tabungan</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; color: #000; background: #fff; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 4px double #000; padding-bottom: 15px; margin-bottom: 25px; position: relative; min-height: 80px;}
        .header img { width: 85px; position: absolute; left: 10px; top: -15px; }
        .header h2 { margin: 5px 0; font-size: 22px; letter-spacing: 1px; padding-top: 10px;}
        .header h4 { margin: 5px 0; font-size: 16px; text-transform: uppercase; }
        .filter-info { margin-bottom: 20px; }
        .filter-info table { width: 100%; border: none; font-size: 12px; }
        .filter-info td { padding: 4px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 4px; text-align: center; vertical-align: middle; line-height: 1.2;}
        table.data-table td.text-left { text-align: left; }
        table.data-table th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .sign-table { width: 100%; margin-top: 60px; border-collapse: collapse; page-break-inside: avoid; }
        .sign-table td { width: 50%; text-align: center; vertical-align: top; font-size: 14px; padding: 0; border: none; line-height: 1.5;}
        
        .screen-filter { background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; margin-bottom: 25px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; }
        .screen-filter form { display: flex; gap: 15px; align-items: center; margin: 0; }
        .screen-filter input[type="date"], .screen-filter select { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; }
        .screen-filter button[type="submit"] { background: #0f172a; color: white; border: none; padding: 9px 15px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 14px;}
        .btn-print { background: #0ea5e9; color: white; border: none; padding: 9px 15px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 14px;}
        
        @media print {
            @page { size: landscape; margin: 1.5cm; }
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="screen-filter no-print">
        <form method="GET" action="/admin/tabungan/rekap/cetak-matriks">
            <select name="petugas_id">
                <option value="0">Semua Petugas</option>
                <?php foreach($petugasList ?? [] as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $petugas_id == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nama']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="kelas_id">
                <option value="0">Semua Kelas</option>
                <?php foreach($kelasList ?? [] as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $kelas_id == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="tampil_semua">
                <option value="0" <?= (isset($_GET['tampil_semua']) && $_GET['tampil_semua'] == '0') ? 'selected' : '' ?>>Yang Mutasi Saja</option>
                <option value="1" <?= (isset($_GET['tampil_semua']) && $_GET['tampil_semua'] == '1') ? 'selected' : '' ?>>Semua Siswa</option>
            </select>
            <input type="date" name="mulai" value="<?= htmlspecialchars($tanggal_mulai) ?>">
            <span style="font-weight:bold;">s/d</span>
            <input type="date" name="akhir" value="<?= htmlspecialchars($tanggal_akhir) ?>">
            <button type="submit">Terapkan Filter</button>
        </form>
        <button class="btn-print" onclick="window.print()">Cetak Halaman (Ctrl+P)</button>
    </div>

    <div class="header">
        <?php if(!empty($inst['logo']) && file_exists(__DIR__ . '/../../../public/uploads/' . $inst['logo'])): ?>
            <img src="/uploads/<?= $inst['logo'] ?>" alt="Logo">
        <?php endif; ?>
        <h2><?= htmlspecialchars($inst['nama_institusi'] ?? 'MI MIFTAHUL HUDA') ?></h2>
        <h4>REKAPITULASI MATRIKS TABUNGAN SISWA</h4>
    </div>

    <div class="filter-info">
        <table style="width:100%; font-size:14px;">
            <tr>
                <td width="15%"><strong>Periode Tanggal</strong></td>
                <td width="2%">:</td>
                <td width="45%"><?= date('d/m/Y', strtotime($tanggal_mulai)) ?> s/d <?= date('d/m/Y', strtotime($tanggal_akhir)) ?></td>
                <td width="15%"><strong>Tgl Cetak</strong></td>
                <td width="2%">:</td>
                <td><?= date('d/m/Y H:i') ?></td>
            </tr>
            <tr>
                <td><strong>Petugas</strong></td>
                <td>:</td>
                <td><?= htmlspecialchars($label_petugas) ?></td>
                <td><strong>Pencetak</strong></td>
                <td>:</td>
                <td><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></td>
            </tr>
            <tr>
                <td><strong>Kelas</strong></td>
                <td>:</td>
                <td><?= htmlspecialchars($label_kelas) ?></td>
                <td></td><td></td><td></td>
            </tr>
        </table>
    </div>

    <?php if(empty($siswaData)): ?>
        <table class="data-table">
            <tr>
                <td class="text-center" style="padding: 20px;">Tidak ada data mutasi pada rentang tanggal ini.</td>
            </tr>
        </table>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 3%;">No</th>
                    <th rowspan="2" style="width: 15%;" class="text-left">Nama Siswa</th>
                    <th rowspan="2" style="width: 5%;">Kelas</th>
                    <th rowspan="2" style="width: 8%;">Saldo Sebelumnya</th>
                    <?php if (count($dateRangeList) > 0): ?>
                        <th colspan="<?= count($dateRangeList) ?>">Rincian Harian (Tarik/Setor)</th>
                    <?php else: ?>
                        <th rowspan="2">Rincian Harian (Tarik/Setor)</th>
                    <?php endif; ?>
                    <th colspan="3" style="width: 15%;">Total Kumulatif</th>
                </tr>
                <tr>
                    <?php if (count($dateRangeList) > 0): ?>
                        <?php foreach($dateRangeList as $dt): ?>
                            <th style="font-size: 9px; min-width: 25px; padding: 4px 2px;"><?= date('d/m', strtotime($dt)) ?></th>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <th style="width: 5%;">Tot. Setor</th>
                    <th style="width: 5%;">Tot. Tarik</th>
                    <th style="width: 5%;">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1; 
                $g_setor = 0;
                $g_tarik = 0;
                $g_saldo_awal = 0;
                foreach($siswaData as $s): 
                    $g_setor += $s['total_setor_all'];
                    $g_tarik += $s['total_tarik_all'];
                    $g_saldo_awal += $s['saldo_awal'];
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="text-left"><?= htmlspecialchars($s['nama_siswa']) ?></td>
                    <td><?= htmlspecialchars($s['nama_kelas']) ?></td>
                    <td class="text-center" style="font-weight: bold;"><?= number_format($s['saldo_awal'], 0, ',', '.') ?></td>
                    
                    <?php if (count($dateRangeList) > 0): ?>
                        <?php foreach($dateRangeList as $dt): 
                            if(isset($s['dates'][$dt])) {
                                $cellHtml = "";
                                foreach($s['dates'][$dt] as $trx) {
                                    if ($trx['jenis'] == 'Setor') {
                                        $cellHtml .= "<div style='color: #10b981; margin-bottom: 2px;'>+" . number_format($trx['jumlah'], 0, ',', '.') . "</div>";
                                    } else {
                                        $cellHtml .= "<div style='color: #f43f5e; margin-bottom: 2px;'>-" . number_format($trx['jumlah'], 0, ',', '.') . "</div>";
                                    }
                                }
                                echo "<td>{$cellHtml}</td>";
                            } else {
                                echo "<td><span style='color:#cbd5e1;'>-</span></td>";
                            }
                        ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <td><span style='color:#cbd5e1;'>-</span></td>
                    <?php endif; ?>

                    <td style="color: #10b981; font-weight: bold;"><?= number_format($s['total_setor_all'], 0, ',', '.') ?></td>
                    <td style="color: #f43f5e; font-weight: bold;"><?= number_format($s['total_tarik_all'], 0, ',', '.') ?></td>
                    <td style="font-weight: bold;"><?= number_format($s['saldo_awal'] + $s['total_setor_all'] - $s['total_tarik_all'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc; font-weight: bold;">
                    <td colspan="<?= 4 + (count($dateRangeList) > 0 ? count($dateRangeList) : 1) ?>" class="text-left" style="padding-right: 15px; text-align: right;">GRAND TOTAL</td>
                    <td style="color: #10b981;"><?= number_format($g_setor, 0, ',', '.') ?></td>
                    <td style="color: #f43f5e;"><?= number_format($g_tarik, 0, ',', '.') ?></td>
                    <td><?= number_format($g_saldo_awal + $g_setor - $g_tarik, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <table class="sign-table">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala Madrasah<br><br><br><br><br>
                <strong><?= htmlspecialchars($inst['kepala_madrasah'] ?? '____________________') ?></strong><br>
                NIP. <?= htmlspecialchars($inst['nip_kepala'] ?? '-') ?>
            </td>
            <td>
                Petugas Tabungan<br><br><br><br><br>
                <strong><?= htmlspecialchars($label_petugas !== 'Semua Petugas' ? $label_petugas : ($_SESSION['nama'] ?? '____________________')) ?></strong>
            </td>
        </tr>
    </table>

</body>
</html>
