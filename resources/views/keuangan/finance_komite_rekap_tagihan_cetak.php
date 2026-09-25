<?php
use App\Core\Database;
if(!isset($db)) {
    $db = Database::connect('core');
}
$dbSiakad = Database::connect('siakad');
$institusi = $dbSiakad->query("SELECT * FROM institusi LIMIT 1")->fetch();

// get bendahara name
$bendahara = !empty($institusi['bendahara_nama']) ? $institusi['bendahara_nama'] : 'Bendahara Umum';

// construct full address
$alamat = $institusi['alamat'] ?? '';
$desa = !empty($institusi['desa']) ? 'Ds. ' . $institusi['desa'] : '';
$kecamatan = !empty($institusi['kecamatan']) ? 'Kec. ' . $institusi['kecamatan'] : '';
$kota = !empty($institusi['kota']) ? 'Kab. ' . $institusi['kota'] : '';
$provinsi = !empty($institusi['provinsi']) ? 'Prov. ' . $institusi['provinsi'] : '';
$kodepos = !empty($institusi['kodepos']) ? 'KP. ' . $institusi['kodepos'] : '';
$fullAddressArray = array_filter([$alamat, $desa, $kecamatan, $kota, $provinsi, $kodepos]);
$fullAddress = implode(', ', $fullAddressArray);

// get logo
$logoUrl = !empty($institusi['logo']) ? '/public/uploads/logo/'.$institusi['logo'] : '/public/assets/images/logo.png';
$tanggal_cetak = !empty($_GET['tanggal_cetak']) ? $_GET['tanggal_cetak'] : date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Tagihan Siswa</title>
    <link rel="icon" type="image/png" href="<?php echo $logoUrl; ?>">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #000; font-size: 12px; }
        @media print {
            @page { size: A4 landscape; margin: 5mm; }
            body { padding: 0; }
            .no-print { display: none !important; }
        }
        
        /* Kop Surat */
        .kop-surat { display: flex; align-items: center; border-bottom: 2px solid #000; padding-bottom: 2px; margin-bottom: 5px; }
        .kop-surat img { width: 55px; height: 55px; object-fit: contain; }
        .kop-surat .teks-kop { text-align: center; flex: 1; padding: 0 5px; }
        .kop-surat h1 { margin: 0; font-size: <?php echo intval($institusi['kop_font_yayasan'] ?? 14) - 3; ?>px; text-transform: uppercase; font-weight: bold; font-family: "Times New Roman", Times, serif; }
        .kop-surat h2 { margin: 0; font-size: <?php echo intval($institusi['kop_font_nama'] ?? 18) - 4; ?>px; font-weight: bold; font-family: "Times New Roman", Times, serif; }
        .kop-surat p { margin: 0; font-size: 9px; font-family: "Times New Roman", Times, serif; }
        
        .header-laporan { text-align: center; margin-bottom: 5px; margin-top: 5px; }
        .header-laporan h3 { margin: 0; font-size: 13px; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9px; }
        th, td { border: 1px solid #000; padding: 2px 4px; text-align: left; }
        th { background-color: #f1f5f9; font-weight: bold; text-align: center; }
        td.num { text-align: right; }
        td.center { text-align: center; }
        
        .ttd { margin-top: 15px; float: right; width: 200px; text-align: center; font-size: 10px; }
        .ttd p { margin: 2px 0; }
        .ttd .nama-ttd { font-weight: bold; text-decoration: underline; margin-top: 40px; }
        
        .btn-print { background: #2563eb; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-size: 12px; margin-bottom: 10px; display: inline-flex; align-items: center; gap: 8px; font-weight: bold; }
        .filter-box { background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0; display: inline-flex; gap: 10px; align-items: center; margin-bottom: 10px; flex-wrap: wrap; }
        .filter-box select, .filter-box input { padding: 5px; border-radius: 4px; border: 1px solid #cbd5e1; font-size: 12px; min-width: 120px; }
        
        .info-filter { margin-bottom: 5px; font-size: 10px; }
        .info-filter table { width: auto; border: none; font-size: 10px; margin-bottom: 0; }
        .info-filter table th, .info-filter table td { border: none; padding: 1px 10px 1px 0; background: transparent; text-align: left; font-weight: normal; }
        .info-filter table th { font-weight: bold; width: 80px; }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center;">
        <form action="" method="GET" class="filter-box">
            <input type="hidden" name="tagihan_nama" value="<?php echo htmlspecialchars($tagihan_nama ?? ''); ?>">
            <div>
                <label style="font-weight: bold; display: block; text-align: left; margin-bottom: 5px;">Pilih Kelas:</label>
                <select name="kelas_id" onchange="this.form.submit()">
                    <option value="">- Pilih Kelas -</option>
                    <?php 
                    $kelasList = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();
                    foreach($kelasList as $k): 
                    ?>
                        <option value="<?php echo $k['id']; ?>" <?php echo (isset($_GET['kelas_id']) && $_GET['kelas_id'] == $k['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($k['nama_kelas']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="font-weight: bold; display: block; text-align: left; margin-bottom: 5px;">Tgl Cetak (TTD):</label>
                <input type="text" name="tanggal_cetak" value="<?php echo htmlspecialchars($tanggal_cetak); ?>" onchange="this.form.submit()">
            </div>
            <button type="button" class="btn-print" style="margin-bottom: 0; margin-top: 20px;" onclick="window.print()">🖨️ Cetak Laporan</button>
        </form>
    </div>

    <!-- Kop Surat -->
    <div class="kop-surat">
        <img src="<?php echo $logoUrl; ?>" alt="Logo">
        <div class="teks-kop">
            <h1><?php echo htmlspecialchars($institusi['yayasan'] ?? ''); ?></h1>
            <h2><?php echo htmlspecialchars($institusi['nama'] ?? ''); ?></h2>
            <p><?php echo htmlspecialchars($fullAddress); ?></p>
            <p>Website: <?php echo htmlspecialchars($institusi['website'] ?? ''); ?> | Email: <?php echo htmlspecialchars($institusi['email'] ?? ''); ?></p>
        </div>
        <div style="width: 65px;"></div>
    </div>
    
    <div class="header-laporan">
        <h3>REKAPITULASI TAGIHAN SISWA</h3>
    </div>
    
    <div class="info-filter">
        <table>
            <tr>
                <th>Kelas</th>
                <td>: <?php echo htmlspecialchars($kelas_nama); ?></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 80px;">NIS</th>
                <th>Nama Siswa</th>
                <th style="width: 100px;">Total Tagihan</th>
                <th style="width: 100px;">Total Terbayar</th>
                <th style="width: 100px;">Sisa Tagihan</th>
                <th style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $grand_tagihan = 0;
            $grand_terbayar = 0;
            $grand_sisa = 0;
            
            if(empty($rekap)): 
            ?>
            <tr>
                <td colspan="7" class="center" style="padding: 15px;">Tidak ada data tagihan ditemukan.</td>
            </tr>
            <?php else: ?>
                <?php foreach($rekap as $p): 
                    $grand_tagihan += $p['jumlah_tagihan'];
                    $grand_terbayar += $p['terbayar'];
                    $grand_sisa += $p['sisa'];
                ?>
                <tr>
                    <td class="center"><?php echo $no++; ?></td>
                    <td class="center"><?php echo htmlspecialchars($p['nis']); ?></td>
                    <td><?php echo htmlspecialchars($p['nama']); ?></td>
                    <td class="num">Rp <?php echo number_format($p['jumlah_tagihan'],0,',','.'); ?></td>
                    <td class="num">Rp <?php echo number_format($p['terbayar'],0,',','.'); ?></td>
                    <td class="num" style="color: <?php echo $p['sisa'] > 0 ? '#dc2626' : '#000'; ?>">Rp <?php echo number_format($p['sisa'],0,',','.'); ?></td>
                    <td class="center"><?php echo $p['status']; ?></td>
                </tr>
                <?php endforeach; ?>
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="3" style="text-align: right; padding-right: 15px;">GRAND TOTAL</td>
                    <td class="num">Rp <?php echo number_format($grand_tagihan,0,',','.'); ?></td>
                    <td class="num">Rp <?php echo number_format($grand_terbayar,0,',','.'); ?></td>
                    <td class="num" style="color: <?php echo $grand_sisa > 0 ? '#dc2626' : '#000'; ?>">Rp <?php echo number_format($grand_sisa,0,',','.'); ?></td>
                    <td></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="ttd">
        <p>Gunung Terang, <?php echo htmlspecialchars($tanggal_cetak); ?></p>
        <p>Bendahara Umum,</p>
        <div class="nama-ttd"><?php echo htmlspecialchars($bendahara); ?></div>
    </div>
    
    <div style="clear: both;"></div>
</body>
</html>
