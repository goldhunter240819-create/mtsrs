<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <?php $favicon = !empty($institusi['logo']) ? '/public/uploads/logo/'.$institusi['logo'] : '/public/assets/images/logo.png'; ?>
    <link rel="icon" type="image/png" href="<?= $favicon ?>">
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #000; line-height: 1.2; font-size: 10pt; }
        .print-container { max-width: 800px; margin: 0 auto; padding: 10px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 5px; margin-bottom: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 3px 4px; text-align: left; vertical-align: top; }
        table.data-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        h2 { text-align: center; margin-bottom: 3px; font-size: 12pt; margin-top: 8px; }
        h3 { text-align: center; margin-top: 0; font-weight: normal; font-size: 10pt; }
        .text-center { text-align: center !important; }
        .kop-surat { border-bottom: 3px solid #000; margin-bottom: 20px; padding-bottom: 10px; text-align: center; }
        .kop-surat h1 { margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .kop-surat h2 { margin: 5px 0; font-size: 18pt; font-weight: bold; }
        .kop-surat p { margin: 0; font-size: 11pt; }
        .no-print { display: block; padding: 15px; background: #f8fafc; border-bottom: 1px solid #cbd5e1; text-align: center; font-family: sans-serif; }
        .no-print form { display: inline-flex; gap: 10px; align-items: center; justify-content: center; flex-wrap: wrap; }
        .no-print select, .no-print input, .no-print button { padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .no-print button { background: #2563eb; color: white; border: none; cursor: pointer; font-weight: bold; }
        @media print {
            body { margin: 0; padding: 0; }
            .print-container { width: 100%; max-width: 100%; padding: 0; }
            .no-print { display: none !important; }
            @page { size: A4; margin: 10mm; }
        }
    </style>
</head>
<body onload="window.print()">
    <!-- Control Panel Filter (Hide on Print) -->
    <div class="no-print">
        <form method="GET" action="">
            <label style="font-weight: bold;">Filter Kelas:</label>
            <select name="kelas_id" required>
                <?php foreach($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $k['id'] == $_GET['kelas_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nama_kelas']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label style="font-weight: bold;">Guru BK:</label>
            <select name="guru_id" required style="width: 180px;">
                <option value="">- Pilih Guru -</option>
                <?php foreach($listGuru as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= $g['id'] == $guru_id_cetak ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label style="font-weight: bold;">Tempat & Tgl:</label>
            <input type="text" name="tempat" value="<?= htmlspecialchars($tempat_cetak) ?>" style="width: 120px;" required>
            <input type="date" name="tanggal" value="<?= htmlspecialchars($tgl_cetak) ?>" required>
            
            <button type="submit">Terapkan & Cetak Ulang</button>
        </form>
    </div>

    <div class="print-container">
        <div style="display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 5px; margin-bottom: 10px;">
            <?php $logoUrl = !empty($institusi['logo']) ? '/public/uploads/logo/'.$institusi['logo'] : '/public/assets/images/logo.png'; ?>
            <img src="<?php echo $logoUrl; ?>" alt="Logo" style="width: 65px; height: 65px; object-fit: contain;">
            
            <div style="text-align: center; flex: 1; padding: 0 10px;">
                <h1 style="margin: 0; font-size: <?php echo max(12, intval($institusi['kop_font_yayasan'] ?? 14) - 2); ?>px; text-transform: uppercase; font-weight: bold; font-family: 'Times New Roman', Times, serif; color: #000;">
                    <?php echo htmlspecialchars($institusi['yayasan'] ?? ''); ?>
                </h1>
                <h2 style="margin: 2px 0; font-size: <?php echo max(14, intval($institusi['kop_font_nama'] ?? 18) - 2); ?>px; font-weight: bold; font-family: 'Times New Roman', Times, serif; color: #000;">
                    <?php echo htmlspecialchars($institusi['nama'] ?? ''); ?>
                </h2>
                <?php 
                    $alamat = $institusi['alamat'] ?? '';
                    $desa = !empty($institusi['desa']) ? 'Ds. ' . $institusi['desa'] : '';
                    $kecamatan = !empty($institusi['kecamatan']) ? 'Kec. ' . $institusi['kecamatan'] : '';
                    $kota = !empty($institusi['kota']) ? 'Kab. ' . $institusi['kota'] : '';
                    $provinsi = !empty($institusi['provinsi']) ? 'Prov. ' . $institusi['provinsi'] : '';
                    $kodepos = !empty($institusi['kodepos']) ? 'KP. ' . $institusi['kodepos'] : '';
                    
                    $fullAddressArray = array_filter([$alamat, $desa, $kecamatan, $kota, $provinsi, $kodepos]);
                    $fullAddress = implode(', ', $fullAddressArray);
                ?>
                <p style="margin: 2px 0; font-size: 10px; font-family: 'Times New Roman', Times, serif; color: #000;">
                    <?php echo htmlspecialchars($fullAddress); ?>
                </p>
                <p style="margin: 2px 0; font-size: 10px; font-family: 'Times New Roman', Times, serif; color: #000;">
                    Website: <?php echo htmlspecialchars($institusi['website'] ?? ''); ?> | Email: <?php echo htmlspecialchars($institusi['email'] ?? ''); ?>
                </p>
            </div>
            
            <div style="width: 65px;"></div>
        </div>

        <h2>REKAPITULASI POIN KEDISIPLINAN SISWA</h2>
        <h3>Kelas: <?= htmlspecialchars($kelas['nama_kelas']) ?> | Tahun Ajaran: <?= htmlspecialchars(\App\Core\AcademicYear::current()['nama_tahun'] ?? '') ?></h3>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">NIS/NISN</th>
                    <th>Nama Siswa</th>
                    <th style="width: 150px;">Total Poin Pelanggaran</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($siswaList)): ?>
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data siswa.</td>
                </tr>
                <?php else: $no = 1; foreach($siswaList as $s): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center"><?= htmlspecialchars($s['nis'] ?: ($s['nisn'] ?: '-')) ?></td>
                    <td><?= htmlspecialchars($s['nama']) ?></td>
                    <td class="text-center" style="font-weight: bold; <?= $s['total_poin'] > 0 ? 'color: #e11d48;' : '' ?>">
                        <?= $s['total_poin'] ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>

        <div style="margin-top: 10px; display: flex; justify-content: space-between;">
            <div style="width: 250px; text-align: center;">
                <p style="margin:0;">Mengetahui,<br>Wali Kelas</p>
                <br><br>
                <p style="text-decoration: underline; font-weight: bold; margin-bottom: 0;">
                    <?= htmlspecialchars($kelas['nama_wali'] ?: '..........................................') ?>
                </p>
            </div>
            <div style="width: 250px; text-align: center;">
                <p style="margin:0;"><?= htmlspecialchars($tempat_cetak) ?>, <?= date('d F Y', strtotime($tgl_cetak)) ?><br>Guru Bimbingan Konseling</p>
                <br><br>
                <p style="text-decoration: underline; font-weight: bold; margin-bottom: 0;">
                    <?= htmlspecialchars($guruBk ?: '..........................................') ?>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
