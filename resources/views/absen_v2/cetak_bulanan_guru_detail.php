<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Bulanan Guru</title>
    <link rel="icon" type="image/png" href="/public/uploads/logo/logo_1778151980.png?v=2026071917">
    <script src="<?php echo \App\Core\Helper::url('/public/assets/js/lucide.min.js'); ?>"></script>
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #000; margin: 0; padding: 0; background: #f8fafc; }
        .page { background: white; width: 210mm; min-height: 297mm; padding: 5mm 10mm; margin: 5mm auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); box-sizing: border-box; }
        
        .kop-surat { display: flex; align-items: center; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 5px; }
        .kop-surat img { width: 65px; height: 65px; object-fit: contain; }
        .kop-text { flex: 1; text-align: center; }
        .kop-text h2, .kop-text h3, .kop-text p { margin: 0; line-height: 1.1; }
        .kop-text h3 { font-size: 12pt; font-weight: bold; text-transform: uppercase; }
        .kop-text h2 { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 2px 0; }
        .kop-text p { font-size: 9pt; }
        
        .judul { text-align: center; margin: 8px 0; }
        .judul h4 { margin: 0; font-size: 11pt; text-transform: uppercase; text-decoration: underline; }
        .judul p { margin: 2px 0 0 0; font-size: 9pt; }
        
        .info-guru { width: 100%; margin-bottom: 8px; font-size: 9pt; }
        .info-guru td { padding: 1px 0; }
        
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 9pt; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 3px; text-align: center; }
        .table-data th { background: #f1f5f9; font-weight: bold; }
        
        .summary-box { display: flex; justify-content: flex-start; gap: 30px; margin-bottom: 10px; font-size: 9pt; }
        .summary-box div { display: flex; flex-direction: column; line-height: 1.3; }
        
        .ttd { width: 100%; margin-top: 10px; font-size: 10pt; }
        .ttd-space { height: 45px; }
        
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .page { margin: 0; box-shadow: none; padding: 5mm 10mm; width: 100%; min-height: auto; page-break-after: always; }
            .page:last-child { page-break-after: avoid; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; padding: 20px; background: #1e293b; color: white;">
        <form method="GET" action="" style="margin-bottom:15px; display:inline-flex; align-items:center; gap:10px; background:rgba(255,255,255,0.1); padding:10px 20px; border-radius:10px;">
            <input type="hidden" name="bulan" value="<?php echo htmlspecialchars($bulan); ?>">
            <input type="hidden" name="tahun" value="<?php echo htmlspecialchars($tahun); ?>">
            <label style="font-weight:bold; color:#cbd5e1;">Pilih Guru:</label>
            <select name="guru_id" onchange="this.form.submit()" style="padding:8px 12px; border-radius:6px; border:none; outline:none; min-width:220px; font-weight:bold; color:#0f172a;">
                <option value="">Semua Guru</option>
                <?php 
                $dbCore = \App\Core\Database::connect('core');
                $listGuru = $dbCore->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll();
                foreach($listGuru as $lg): ?>
                <option value="<?php echo $lg['id']; ?>" <?php echo (isset($guru_id) && $guru_id == $lg['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($lg['nama']); ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <br>
        <button onclick="window.print()" style="background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 16px; cursor: pointer; font-weight: bold;">
            🖨️ Cetak Dokumen
        </button>
        <p style="margin-top: 10px; font-size: 14px; color: #cbd5e1;">Pilih pengaturan kertas A4 pada mode Portrait.</p>
    </div>

    <?php foreach ($rekapPerGuru as $g): ?>
    <div class="page">
        <!-- KOP SURAT -->
        <div style="display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 10px;">
            <?php $logoUrl = !empty($inst['logo']) ? '/public/uploads/logo/'.$inst['logo'] : '/public/assets/images/logo.png'; ?>
            <img src="<?php echo $logoUrl; ?>" alt="Logo" style="width: 80px; height: 80px; object-fit: contain;">
            
            <div style="text-align: center; flex: 1; padding: 0 10px;">
                <h1 style="margin: 0; font-size: <?php echo intval($inst['kop_font_yayasan'] ?? 14); ?>px; text-transform: uppercase; font-weight: bold; font-family: 'Times New Roman', Times, serif; color: #000;">
                    <?php echo htmlspecialchars($inst['yayasan'] ?? ''); ?>
                </h1>
                <h2 style="margin: 3px 0; font-size: <?php echo intval($inst['kop_font_nama'] ?? 18); ?>px; font-weight: bold; font-family: 'Times New Roman', Times, serif; color: #000;">
                    <?php echo htmlspecialchars($inst['nama'] ?? ''); ?>
                </h2>
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
                <p style="margin: 3px 0; font-size: 11px; font-family: 'Times New Roman', Times, serif; color: #000;">
                    <?php echo htmlspecialchars($fullAddress); ?>
                </p>
                <p style="margin: 3px 0; font-size: 11px; font-family: 'Times New Roman', Times, serif; color: #000;">
                    Website: <?php echo htmlspecialchars($inst['website'] ?? ''); ?> | Email: <?php echo htmlspecialchars($inst['email'] ?? ''); ?>
                </p>
            </div>
        </div>

        <div class="judul">
            <h4>LAPORAN REKAPITULASI ABSENSI GURU</h4>
            <p>Bulan: <?php echo $bulan_name . ' ' . $tahun; ?></p>
        </div>

        <table class="info-guru">
            <tr>
                <td width="150">Nama Guru</td>
                <td width="10">:</td>
                <td style="font-weight: bold;"><?php echo htmlspecialchars($g['nama']); ?></td>
            </tr>
            <tr>
                <td>NPK</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($g['npk'] ?? '-'); ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($g['jabatan'] ?? '-'); ?></td>
            </tr>
        </table>

        <table class="table-data">
            <thead>
                <tr>
                    <th width="40">No</th>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Status</th>
                    <th>Jam Masuk</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($g['detail'] as $d): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo date('d-m-Y', strtotime($d['tanggal'])); ?></td>
                    <td><?php echo $d['hari']; ?></td>
                    <td style="font-weight:bold;"><?php echo $d['status']; ?></td>
                    <td><?php echo $d['jam_masuk']; ?></td>
                    <td><?php echo $d['keterangan']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="font-weight: bold; margin-bottom: 5px;">Ringkasan Kehadiran:</div>
        <div class="summary-box">
            <div>
                <span>Hadir: <b><?php echo $g['summary']['Hadir']; ?></b> hari</span>
                <span>Terlambat: <b><?php echo $g['summary']['Terlambat']; ?></b> hari</span>
            </div>
            <div>
                <span>Sakit: <b><?php echo $g['summary']['Sakit']; ?></b> hari</span>
                <span>Izin: <b><?php echo $g['summary']['Izin']; ?></b> hari</span>
            </div>
            <div>
                <span>Alpa: <b><?php echo $g['summary']['Alpa']; ?></b> hari</span>
                <span>Tidak Ada Jam: <b><?php echo $g['summary']['TidakAdaJam']; ?></b> hari</span>
            </div>
        </div>

        <table class="ttd">
            <tr>
                <td style="width: 50%; text-align: left; vertical-align: bottom; padding-left: 20px;">
                    <?php $qrData = "https://mismifhda.sch.id/verify-absen?guru_id=" . $g['id'] . "&bulan=" . $bulan . "&tahun=" . $tahun; ?>
                    <div style="background: white; padding: 2.5mm; border-radius: 3mm; border: 1.5px solid #d4af37; box-shadow: 0 8px 20px rgba(0,0,0,0.08); margin-bottom: 2.5mm; z-index: 3; position: relative; width: fit-content;">
                        <img src="https://quickchart.io/qr?size=150&margin=0&text=<?php echo urlencode($qrData); ?>" style="width: 25mm; height: 25mm; object-fit: contain; display: block;">
                    </div>
                    <div style="font-size: 8pt; color: #666; margin-top: 3px;">Scan Validasi</div>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: bottom;">
                    <?php 
                        $nama_kota = htmlspecialchars($inst['kota'] ?? 'Kota');
                        echo $nama_kota . ', ' . $daysInMonth . ' ' . $bulan_name . ' ' . $tahun; 
                    ?><br>
                    Kepala Madrasah,
                    <div class="ttd-space"></div>
                    <b><u><?php echo htmlspecialchars($inst['nama_kepala'] ?? '____________________'); ?></u></b>
                </td>
            </tr>
        </table>
    </div>
    <?php endforeach; ?>

    <script>
        lucide.createIcons();
        
        function applyPrintSettings() {
            // Placeholder for print utility
        }
    </script>
</body>
</html>
