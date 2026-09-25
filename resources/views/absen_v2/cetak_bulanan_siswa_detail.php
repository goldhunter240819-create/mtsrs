<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Daftar Scan QR Siswa</title>
    <?php $faviconUrl = !empty($inst['logo']) ? '/public/uploads/logo/'.$inst['logo'] : '/public/assets/images/logo.png'; ?>
    <link rel="icon" type="image/png" href="<?php echo $faviconUrl; ?>">
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #000; margin: 0; padding: 0; background: #f8fafc; }
        .page { background: white; padding: 5mm; margin: 0 auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); box-sizing: border-box; width: 100%; } /* Landscape layout */
        
        .kop-surat { display: flex; align-items: center; border-bottom: 2px double #000; padding-bottom: 5px; margin-bottom: 10px; }
        .kop-surat img { width: 60px; height: 60px; object-fit: contain; }
        .kop-text { flex: 1; text-align: center; padding: 0 10px; }
        .kop-text h1 { margin: 0; font-size: 14pt; text-transform: uppercase; font-weight: bold; }
        .kop-text h2 { margin: 3px 0; font-size: 18pt; font-weight: bold; text-transform: uppercase; }
        .kop-text p { margin: 3px 0; font-size: 9pt; }
        
        .judul { text-align: center; margin: 10px 0; }
        .judul h4 { margin: 0; font-size: 11pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; }
        .judul p { margin: 3px 0 0 0; font-size: 9pt; }
        
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 7.5pt; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 2px 1px; text-align: center; }
        .table-data th { background: #f1f5f9; font-weight: bold; }
        .table-data td.nama-siswa { text-align: left; padding-left: 5px; white-space: nowrap; }
        
        .ontime { color: #059669; font-weight: bold; }
        .late { color: #d97706; font-weight: bold; }
        
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .page { margin: 0; box-shadow: none; padding: 5mm; width: 100%; page-break-after: always; }
            .page:last-child { page-break-after: avoid; }
            .no-print { display: none; }
            @page { size: landscape; margin: 5mm; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; padding: 15px; background: #1e293b; color: white;">
        <div style="display: flex; justify-content: center; align-items: center; gap: 15px;">
            <form method="GET" style="display: inline-flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.1); padding: 10px 20px; border-radius: 10px; margin: 0;">
                <input type="hidden" name="bulan" value="<?php echo htmlspecialchars($bulan); ?>">
                <input type="hidden" name="tahun" value="<?php echo htmlspecialchars($tahun); ?>">
                <label style="font-weight: bold; color: #cbd5e1;">Pilih Kelas:</label>
                <select name="kelas_id" onchange="this.form.submit()" style="padding: 6px 12px; border-radius: 6px; border: none; outline: none; min-width: 150px; font-weight: bold; color: #0f172a;">
                    <option value="">-- Semua Kelas --</option>
                    <?php foreach($kelasList as $k): ?>
                    <option value="<?php echo $k['id']; ?>" <?php echo (isset($kelas_id) && $kelas_id == $k['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <button onclick="window.print()" style="background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 15px; cursor: pointer; font-weight: bold;">
                🖨️ Cetak Dokumen
            </button>
        </div>
        <p style="margin-top: 10px; margin-bottom: 0; font-size: 13px; color: #94a3b8;">Gunakan pengaturan kertas A4 pada mode Landscape.</p>
    </div>

    <div class="page">
        <!-- KOP SURAT -->
        <div class="kop-surat">
            <?php $logoUrl = !empty($inst['logo']) ? '/public/uploads/logo/'.$inst['logo'] : '/public/assets/images/logo.png'; ?>
            <img src="<?php echo $logoUrl; ?>" alt="Logo">
            
            <div class="kop-text">
                <h1><?php echo htmlspecialchars($inst['yayasan'] ?? ''); ?></h1>
                <h2><?php echo htmlspecialchars($inst['nama'] ?? ''); ?></h2>
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
                <p><?php echo htmlspecialchars($fullAddress); ?></p>
                <p>Website: <?php echo htmlspecialchars($inst['website'] ?? ''); ?> | Email: <?php echo htmlspecialchars($inst['email'] ?? ''); ?></p>
            </div>
        </div>

        <div class="judul">
            <h4>LAPORAN DAFTAR SCAN QR SISWA</h4>
            <p>Bulan: <strong><?php echo $bulan_name . ' ' . $tahun; ?></strong> &nbsp; | &nbsp; Kelas: <strong><?php echo htmlspecialchars($nama_kelas_dipilih); ?></strong></p>
        </div>

        <table class="table-data">
            <thead>
                <tr>
                    <th rowspan="2" width="20">No</th>
                    <th rowspan="2" width="50">NIS</th>
                    <th rowspan="2" width="150">Nama Siswa</th>
                    <?php if (empty($kelas_id)): ?><th rowspan="2" width="40">Kelas</th><?php endif; ?>
                    <th colspan="<?php echo count($dates); ?>">Tanggal</th>
                </tr>
                <tr>
                    <?php foreach ($dates as $d): ?>
                        <th style="<?php echo $d['is_libur'] ? 'background:#fee2e2;color:#ef4444;' : ''; ?>"><?php echo $d['tgl']; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($siswaList as $s): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($s['nis']); ?></td>
                    <td class="nama-siswa"><?php echo htmlspecialchars($s['nama']); ?></td>
                    <?php if (empty($kelas_id)): ?><td><?php echo htmlspecialchars($s['nama_kelas']); ?></td><?php endif; ?>
                    
                    <?php foreach ($dates as $d): ?>
                        <?php 
                        $jam = '-';
                        $class = '';
                        $is_libur = $d['is_libur'];
                        $tgl = $d['date'];
                        
                        if (isset($absenBulanIni[$s['id']][$tgl])) {
                            $absen = $absenBulanIni[$s['id']][$tgl];
                            if ($absen['jam_masuk']) {
                                $jam = substr($absen['jam_masuk'], 0, 5);
                                if ($absen['status'] == 'Terlambat' || $jam > substr($batas_terlambat, 0, 5)) {
                                    $class = 'late';
                                } else {
                                    $class = 'ontime';
                                }
                            } else {
                                $jam = substr($absen['status'], 0, 1); // S/I/A
                            }
                        }
                        
                        $bg = $is_libur ? 'background:#fff1f2;' : '';
                        ?>
                        <td style="<?php echo $bg; ?>" class="<?php echo $class; ?>"><?php echo $jam; ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($siswaList)): ?>
                <tr>
                    <td colspan="<?php echo 3 + (empty($kelas_id) ? 1 : 0) + count($dates); ?>">Tidak ada data siswa.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</body>
</html>
