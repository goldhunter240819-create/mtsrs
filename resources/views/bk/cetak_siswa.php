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
        .text-center { text-align: center !important; }
        .kop-surat { border-bottom: 3px solid #000; margin-bottom: 20px; padding-bottom: 10px; text-align: center; }
        .kop-surat h1 { margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .kop-surat h2 { margin: 5px 0; font-size: 18pt; font-weight: bold; }
        .kop-surat p { margin: 0; font-size: 11pt; }
        .surat-title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 0; font-size: 12pt; }
        .surat-no { text-align: center; margin-top: 2px; margin-bottom: 5px; font-size: 10pt; }
        .biodata-table { width: 100%; margin-bottom: 5px; font-size: 10pt; }
        .biodata-table td { padding: 1px 0; vertical-align: top; }
        .biodata-table td:first-child { width: 120px; }
        .biodata-table td:nth-child(2) { width: 20px; text-align: center; }
        .biodata-table td:nth-child(2) { width: 20px; text-align: center; }
        .no-print { display: block; padding: 15px; background: #f8fafc; border-bottom: 1px solid #cbd5e1; text-align: center; font-family: sans-serif; }
        .no-print form { display: inline-flex; gap: 10px; align-items: center; justify-content: center; flex-wrap: wrap; }
        .no-print select, .no-print input, .no-print button { padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .no-print button { background: #e11d48; color: white; border: none; cursor: pointer; font-weight: bold; }
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
            <label style="font-weight: bold;">Cari Siswa Lain:</label>
            <select name="siswa_id" id="select-siswa" required style="width: 250px;">
                <?php foreach($siswaList as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $s['id'] == $_GET['siswa_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['nama_siswa']) ?> (<?= htmlspecialchars($s['nama_kelas']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label style="font-weight: bold;">Guru BK:</label>
            <select name="guru_id" required style="width: 150px;">
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

        <div class="surat-title">SURAT PANGGILAN ORANG TUA / WALI MURID</div>
        <div class="surat-no">Nomor: ...... / BK / MTs-RS / <?= date('Y') ?></div>

        <p style="margin: 5px 0;">Kepada Yth.<br>Orang Tua / Wali Murid dari:</p>
        
        <table class="biodata-table">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><strong><?= htmlspecialchars($siswa['nama']) ?></strong></td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>:</td>
                <td><?= htmlspecialchars($siswa['nama_kelas']) ?></td>
            </tr>
            <tr>
                <td>Tahun Ajaran</td>
                <td>:</td>
                <td><?= htmlspecialchars(\App\Core\AcademicYear::current()['nama_tahun'] ?? '') ?></td>
            </tr>
            <tr>
                <td>NIS / NISN</td>
                <td>:</td>
                <td><?= htmlspecialchars($siswa['nis'] ?: '-') ?> / <?= htmlspecialchars($siswa['nisn'] ?: '-') ?></td>
            </tr>
        </table>

        <p style="margin: 5px 0;">Dengan hormat,</p>
        <p style="text-align: justify; margin: 5px 0;">Sehubungan dengan adanya evaluasi kedisiplinan dan tata tertib sekolah, bersama surat ini kami mengharap kehadiran Bapak/Ibu Orang Tua/Wali Murid ke sekolah pada:</p>
        
        <table class="biodata-table" style="margin-left: 20px; width: 90%;">
            <tr>
                <td style="width: 100px;">Hari / Tanggal</td>
                <td>:</td>
                <td>............................................................</td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>:</td>
                <td>............................................................</td>
            </tr>
            <tr>
                <td>Tempat</td>
                <td>:</td>
                <td>Ruang Bimbingan Konseling (BK) MTs RS</td>
            </tr>
            <tr>
                <td>Keperluan</td>
                <td>:</td>
                <td>Membicarakan perkembangan dan kedisiplinan siswa</td>
            </tr>
        </table>

        <p style="text-align: justify; margin: 5px 0;">Sebagai bahan evaluasi, berikut adalah riwayat catatan poin pelanggaran kedisiplinan yang telah dilakukan oleh siswa yang bersangkutan:</p>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 100px;">Tanggal</th>
                    <th>Pelanggaran / Keterangan</th>
                    <th style="width: 60px;">Poin</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($riwayat)): ?>
                <tr>
                    <td colspan="4" class="text-center">Siswa ini belum memiliki catatan pelanggaran.</td>
                </tr>
                <?php else: $no = 1; foreach($riwayat as $r): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
                    <td>
                        <strong><?= htmlspecialchars($r['nama_kategori']) ?></strong><br>
                        <span style="font-size: 10.5pt; color: #444;"><?= htmlspecialchars($r['keterangan']) ?></span>
                    </td>
                    <td class="text-center" style="<?= $r['tipe'] === 'pelanggaran' ? 'color: red; font-weight: bold;' : 'color: green;' ?>">
                        <?= $r['tipe'] === 'pelanggaran' ? '+' : '-' ?><?= $r['poin'] ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold; padding-right: 15px;">TOTAL POIN PELANGGARAN :</td>
                    <td class="text-center" style="font-weight: bold; color: red; font-size: 11pt;"><?= $totalPoin ?></td>
                </tr>
            </tbody>
        </table>

        <p style="text-align: justify; margin: 5px 0;">Demikian surat panggilan ini kami sampaikan. Mengingat pentingnya hal tersebut, kami sangat mengharapkan kehadiran Bapak/Ibu tepat pada waktunya. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.</p>

        <div style="margin-top: 10px; display: flex; justify-content: space-between;">
            <div style="width: 250px; text-align: center;">
                <p style="margin:0;">Mengetahui,<br>Wali Kelas</p>
                <br><br>
                <p style="text-decoration: underline; font-weight: bold; margin-bottom: 0;">
                    <?= htmlspecialchars($siswa['nama_wali'] ?: '..........................................') ?>
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

    <!-- Script Select2 untuk filter dropdown biar enak dicari -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#select-siswa').select2();
        });
    </script>
</body>
</html>
