<?php
function tgl_indo($tanggal){
    $bulan = array (
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $pecahkan = explode('-', date('Y-m-d', strtotime($tanggal)));
    return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Detail Laporan Kas</title>
    <?php $logoUrl = !empty($inst['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $inst['logo']) : \App\Core\Helper::url('/assets/images/logo.png'); ?>
    <link rel="icon" type="image/png" href="<?php echo $logoUrl; ?>">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background-color: #fff;
            color: #000;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .kop-text {
            flex: 1;
            text-align: center;
            padding-right: 80px;
        }
        .kop-text h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .kop-text p {
            margin: 5px 0 0;
            font-size: 12px;
        }
        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        .filter-info {
            text-align: center;
            font-size: 12px;
            margin-bottom: 20px;
        }
        .summary-box {
            display: flex;
            justify-content: space-between;
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            -webkit-print-color-adjust: exact;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 250px;
        }
        .signature-name {
            margin-top: 80px;
            font-weight: bold;
            text-decoration: underline;
        }
        @media print {
            @page { margin: 1cm; }
            body { font-size: 11px; }
            th { background-color: #eee !important; -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
        .filter-box { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #cbd5e1; display: flex; gap: 10px; align-items: center; justify-content: center; flex-wrap: wrap; }
        .filter-box select, .filter-box input { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; font-size: 14px; }
        .kop-text h2 { margin: 2px 0; font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="no-print filter-box">
        <form method="GET" action="" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin:0;">
            <?php if (empty($is_restricted)): ?>
            <select name="petugas_id">
                <option value="">Semua Petugas</option>
                <?php if(isset($petugasList)): foreach($petugasList as $p): ?>
                <option value="<?php echo $p['id']; ?>" <?php echo (isset($_GET['petugas_id']) && $_GET['petugas_id']==$p['id'])?'selected':''; ?>><?php echo htmlspecialchars($p['nama']); ?></option>
                <?php endforeach; endif; ?>
            </select>
            <?php endif; ?>
            
            <select name="kategori">
                <option value="">Semua Kategori</option>
                <?php if(isset($kategoriList)): foreach($kategoriList as $k): ?>
                <option value="<?php echo htmlspecialchars($k['kategori']); ?>" <?php echo (isset($_GET['kategori']) && $_GET['kategori']==$k['kategori'])?'selected':''; ?>><?php echo htmlspecialchars($k['kategori']); ?></option>
                <?php endforeach; endif; ?>
            </select>
            
            <input type="date" name="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
            <span style="font-family: sans-serif; font-size: 14px; font-weight: bold;">s.d</span>
            <input type="date" name="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
            
            <button type="submit" style="padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: bold; font-family: sans-serif;">Filter</button>
        </form>
        <button onclick="window.print()" style="padding: 8px 16px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: bold; font-family: sans-serif; margin-left: 15px;">Cetak Dokumen</button>
    </div>

    <div class="kop-surat">
        <?php if(!empty($inst['logo'])): ?>
            <img src="/public/uploads/logo/<?php echo htmlspecialchars($inst['logo']); ?>" class="kop-logo" alt="Logo">
        <?php else: ?>
            <div style="width: 80px; margin-right: 20px;"></div>
        <?php endif; ?>
        <?php 
            $alamat = $inst['alamat'] ?? '';
            $desa = !empty($inst['desa']) ? 'Ds. ' . $inst['desa'] : '';
            $kecamatan = !empty($inst['kecamatan']) ? 'Kec. ' . $inst['kecamatan'] : '';
            $kota = !empty($inst['kota']) ? 'Kab. ' . $inst['kota'] : '';
            $provinsi = !empty($inst['provinsi']) ? 'Prov. ' . $inst['provinsi'] : '';
            $kodepos = !empty($inst['kodepos']) ? 'KP. ' . $inst['kodepos'] : '';
            
            $fullAddressArray = array_filter([$alamat, $desa, $kecamatan, $kota, $provinsi, $kodepos]);
            $fullAddress = implode(', ', $fullAddressArray);
        ?>
        <div class="kop-text">
            <h1 style="font-size: <?php echo intval($inst['kop_font_yayasan'] ?? 14); ?>px; font-weight: normal; margin-bottom: 5px;"><?php echo htmlspecialchars($inst['yayasan'] ?? ''); ?></h1>
            <h2 style="font-size: <?php echo intval($inst['kop_font_nama'] ?? 18); ?>px; text-transform: uppercase;"><?php echo htmlspecialchars($inst['nama'] ?? ''); ?></h2>
            <p style="font-size: 12px; margin-top: 5px;"><?php echo htmlspecialchars($fullAddress); ?></p>
            <p style="font-size: 12px;">Website: <?php echo htmlspecialchars($inst['website'] ?? ''); ?> | Email: <?php echo htmlspecialchars($inst['email'] ?? ''); ?></p>
        </div>
        <div style="width: 80px; margin-left: 20px;"></div>
    </div>

    <div class="report-title">LAPORAN RINCIAN KAS <?php echo htmlspecialchars($title_suffix); ?></div>
    
    <div class="filter-info">
        <?php if(!empty($start_date) && !empty($end_date)): ?>
            Periode: <?php echo tgl_indo($start_date); ?> s.d <?php echo tgl_indo($end_date); ?><br>
        <?php else: ?>
            Periode: Semua Waktu (Hingga <?php echo tgl_indo(date('Y-m-d')); ?>)<br>
        <?php endif; ?>
    </div>

    <div class="summary-box">
        <div>Total Pemasukan: Rp <?php echo number_format($totalMasuk, 0, ',', '.'); ?></div>
        <div>Total Pengeluaran: Rp <?php echo number_format($totalKeluar, 0, ',', '.'); ?></div>
        <div>Total Saldo Kas: Rp <?php echo number_format($saldo, 0, ',', '.'); ?></div>
    </div>

    <div class="section-title">A. Rincian Pemasukan Kas</div>
    <?php if(empty($detailPemasukan)): ?>
        <p style="text-align:center; font-style:italic;">Tidak ada data pemasukan pada periode ini.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Keterangan</th>
                    <th style="width: 100px;">Kategori</th>
                    <th style="width: 100px;">Petugas</th>
                    <th class="text-right" style="width: 120px;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($detailPemasukan as $row): ?>
                <tr>
                    <td class="text-center"><?php echo $no++; ?></td>
                    <td><?php echo tgl_indo($row['tanggal']); ?></td>
                    <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                    <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                    <td><?php echo htmlspecialchars($row['petugas']); ?></td>
                    <td class="text-right"><?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-right">TOTAL PEMASUKAN</th>
                    <th class="text-right">Rp <?php echo number_format($totalMasuk, 0, ',', '.'); ?></th>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <div class="section-title" style="page-break-before: auto;">B. Rincian Pengeluaran Kas</div>
    <?php if(empty($detailPengeluaran)): ?>
        <p style="text-align:center; font-style:italic;">Tidak ada data pengeluaran pada periode ini.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Keterangan</th>
                    <th style="width: 100px;">Kategori</th>
                    <th style="width: 100px;">Petugas</th>
                    <th class="text-right" style="width: 120px;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($detailPengeluaran as $row): ?>
                <tr>
                    <td class="text-center"><?php echo $no++; ?></td>
                    <td><?php echo tgl_indo($row['tanggal']); ?></td>
                    <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                    <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                    <td><?php echo htmlspecialchars($row['petugas']); ?></td>
                    <td class="text-right"><?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-right">TOTAL PENGELUARAN</th>
                    <th class="text-right">Rp <?php echo number_format($totalKeluar, 0, ',', '.'); ?></th>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <div class="signatures">
        <div class="signature-box">
            <p>Kepala Madrasah</p>
            <p class="signature-name">( <?php echo !empty($inst['nama_kepala']) ? htmlspecialchars($inst['nama_kepala']) : '_________________________'; ?> )</p>
        </div>
        <div class="signature-box">
            <p><?php echo isset($inst['kota']) ? $inst['kota'] : 'Kota/Kab'; ?>, <?php echo tgl_indo(date('Y-m-d')); ?><br>Bendahara <?php echo !empty($title_suffix) ? htmlspecialchars($title_suffix) : 'Komite'; ?></p>
            <p class="signature-name">( <?php echo !empty($bendahara_nama) ? htmlspecialchars($bendahara_nama) : '_________________________'; ?> )</p>
        </div>
    </div>

</body>
</html>
