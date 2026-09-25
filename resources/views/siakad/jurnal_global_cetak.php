<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Jurnal Global</title>
    <?php 
        $logoUrl = !empty($inst['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $inst['logo']) : \App\Core\Helper::url('/assets/images/logo.png'); 
    ?>
    <link rel="icon" type="image/png" href="<?php echo $logoUrl; ?>">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .kop-surat .logo {
            width: 90px;
            height: auto;
            margin-right: 20px;
        }
        .kop-surat .kop-text {
            flex: 1;
            text-align: center;
            padding-right: 110px; /* offset for logo to keep text centered */
        }
        .kop-surat .kop-text h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .kop-surat .kop-text h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-surat .kop-text p {
            margin: 3px 0 0 0;
            font-size: 13px;
        }
        .judul-dokumen {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .filter-info {
            margin-bottom: 15px;
        }
        .filter-info table {
            width: 100%;
            border: none;
        }
        .filter-info td {
            padding: 3px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 13px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        table.data-table th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .signature-box {
            float: right;
            width: 300px;
            text-align: center;
            margin-top: 30px;
        }
        .signature-name {
            margin-top: 70px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        /* Filter form styling for screen only */
        .screen-filter {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .screen-filter form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .screen-filter select, .screen-filter button {
            padding: 6px 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 13px;
        }
        .screen-filter button {
            background: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            padding: 7px 12px;
        }
        .screen-filter button:hover {
            background: #0056b3;
        }
        .screen-filter .btn-print {
            background: #28a745;
        }
        .screen-filter .btn-print:hover {
            background: #218838;
        }
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            margin-top: 20px;
            color: #64748b;
        }
        .empty-state h3 {
            color: #334155;
            margin-bottom: 10px;
        }
        
        @media print {
            .screen-filter {
                display: none;
            }
            .no-print {
                display: none;
            }
            .empty-state {
                display: none;
            }
            @page {
                size: landscape;
                margin: 1cm;
            }
        }
    </style>
</head>
<body>

    <div class="screen-filter no-print">
        <form method="GET" action="">
            <label style="margin-right:10px;"><strong>Opsi Cetak:</strong></label>
            <select name="jenis_jurnal" id="filter-jenis" required style="background:#eef2ff; border-color:#a5b4fc; font-weight:bold;" onchange="toggleFilters()">
                <option value="">-- Pilih Jenis Cetakan --</option>
                <option value="kelas" <?= ($filter_jenis == 'kelas') ? 'selected' : '' ?>>Buku Jurnal Kelas</option>
                <option value="guru" <?= ($filter_jenis == 'guru') ? 'selected' : '' ?>>Buku Jurnal Guru (Pribadi)</option>
                <option value="cover_kelas" <?= ($filter_jenis == 'cover_kelas') ? 'selected' : '' ?>>Cover Buku Jurnal Kelas</option>
                <option value="cover_guru" <?= ($filter_jenis == 'cover_guru') ? 'selected' : '' ?>>Cover Buku Jurnal Guru</option>
            </select>
            
            <div id="filter-common" style="display: <?= !empty($filter_jenis) ? 'flex' : 'none' ?>; gap: 10px; align-items: center;">
                <input type="date" name="tanggal_mulai" value="<?= htmlspecialchars($filter_tanggal_mulai) ?>" style="padding: 6px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px;" required>
                <span>s/d</span>
                <input type="date" name="tanggal_akhir" value="<?= htmlspecialchars($filter_tanggal_sampai) ?>" style="padding: 6px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px;" required>
                <select name="kelas_id" id="filter-kelas">
                    <option value="">Semua Kelas</option>
                    <?php foreach($kelasList as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= ($filter_kelas == $k['id']) ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="filter-guru-mapel" style="display: <?= ($filter_jenis == 'guru' || $filter_jenis == 'cover_guru') ? 'flex' : 'none' ?>; gap: 10px; align-items: center;">
                <select name="guru_id">
                    <option value="">Semua Guru</option>
                    <?php foreach($guruList as $g): ?>
                        <option value="<?= $g['id'] ?>" <?= ($filter_guru == $g['id']) ? 'selected' : '' ?>><?= htmlspecialchars($g['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="mapel_id">
                    <option value="">Semua Mapel</option>
                    <?php foreach($mapelList as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= ($filter_mapel == $m['id']) ? 'selected' : '' ?>><?= htmlspecialchars($m['nama_mapel']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="filter-buttons" style="display: <?= !empty($filter_jenis) ? 'flex' : 'none' ?>; gap: 10px; margin-left: auto;">
                <button type="submit">Terapkan Filter</button>
                <?php if(!empty($filter_jenis)): ?>
                <button type="button" class="btn-print" onclick="window.print()">Cetak Halaman (Ctrl+P)</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <script>
    function toggleFilters() {
        var jenis = document.getElementById('filter-jenis').value;
        var common = document.getElementById('filter-common');
        var guruMapel = document.getElementById('filter-guru-mapel');
        var buttons = document.getElementById('filter-buttons');

        // Reset filter lainnya ketika jenis cetakan diubah
        document.getElementById('filter-kelas').value = '';
        document.querySelector('select[name="guru_id"]').value = '';
        document.querySelector('select[name="mapel_id"]').value = '';

        if (jenis === '') {
            common.style.display = 'none';
            guruMapel.style.display = 'none';
            buttons.style.display = 'none';
        } else {
            common.style.display = 'flex';
            guruMapel.style.display = (jenis === 'guru' || jenis === 'cover_guru') ? 'flex' : 'none';
            buttons.style.display = 'flex';
        }
        
        // Auto-submit form when Opsi Cetak is changed to ensure a fresh clean state in the URL
        document.querySelector('.screen-filter form').submit();
    }
    </script>

    <?php if(empty($filter_jenis)): ?>
        <div class="empty-state">
            <h3>Pilih Jenis Cetakan</h3>
            <p>Silakan pilih jenis cetakan <strong>Buku Jurnal Kelas</strong> atau <strong>Buku Jurnal Guru</strong>, lalu klik <strong>Terapkan Filter</strong> untuk melihat pratinjau dokumen dan mencetak.</p>
        </div>
    <?php else: ?>

    <?php if($filter_jenis == 'cover_kelas' || $filter_jenis == 'cover_guru'): ?>
        <!-- TAMPILAN COVER MODERN -->
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800;900&display=swap');
            
            .modern-cover {
                position: relative;
                width: 100%;
                background: #ffffff;
                overflow: hidden;
                padding: 30px 40px;
                box-sizing: border-box;
                font-family: 'Inter', sans-serif;
                color: #1e293b;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.08);
                margin-top: 20px;
                min-height: 17cm;
            }
            @media print {
                .modern-cover {
                    border: none;
                    box-shadow: none;
                    border-radius: 0;
                    margin-top: 0;
                    padding: 0;
                    min-height: 16cm;
                }
            }
            .modern-cover::before {
                content: '';
                position: absolute;
                top: -100px;
                left: -100px;
                width: 350px;
                height: 350px;
                background: linear-gradient(135deg, #0ea5e9, #2563eb);
                border-radius: 50%;
                z-index: 0;
                opacity: 0.08;
            }
            .modern-cover::after {
                content: '';
                position: absolute;
                bottom: -150px;
                right: -80px;
                width: 500px;
                height: 500px;
                background: linear-gradient(135deg, #f59e0b, #ea580c);
                border-radius: 50%;
                z-index: 0;
                opacity: 0.08;
            }
            .cover-content {
                position: relative;
                z-index: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100%;
            }
            .cover-header {
                text-align: center;
                margin-bottom: 25px;
                width: 100%;
            }
            .cover-header img {
                width: 110px;
                margin-bottom: 15px;
                filter: drop-shadow(0 4px 8px rgba(0,0,0,0.15));
            }
            .cover-header .inst-name {
                font-size: 24px;
                font-weight: 900;
                color: #0f172a;
                letter-spacing: 1.5px;
                margin-bottom: 5px;
            }
            .cover-header .inst-sub {
                font-size: 14px;
                color: #475569;
                text-transform: uppercase;
                letter-spacing: 3px;
                font-weight: 600;
            }
            .cover-title-area {
                text-align: center;
                margin: 20px 0;
                width: 100%;
                padding: 30px 0;
                background: linear-gradient(90deg, transparent, rgba(37,99,235,0.06), transparent);
                border-top: 3px solid #3b82f6;
                border-bottom: 3px solid #f59e0b;
            }
            .cover-title-area h1 {
                font-size: 38px;
                font-weight: 900;
                color: #1e3a8a;
                margin: 0 0 10px 0;
                letter-spacing: 4px;
                text-transform: uppercase;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.05);
            }
            .cover-title-area h2 {
                font-size: 24px;
                font-weight: 800;
                color: #f59e0b;
                margin: 0;
                letter-spacing: 3px;
                text-transform: uppercase;
            }
            .cover-details {
                width: 75%;
                max-width: 600px;
                background: rgba(255, 255, 255, 0.9);
                padding: 25px 40px;
                border-radius: 16px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.06);
                border-left: 6px solid #3b82f6;
                backdrop-filter: blur(10px);
            }
            .cover-details table {
                width: 100%;
                font-size: 16px;
                border-collapse: separate;
                border-spacing: 0 12px;
            }
            .cover-details td {
                vertical-align: middle;
            }
            .cover-details td.label {
                width: 35%;
                font-weight: 800;
                color: #475569;
                text-transform: uppercase;
                font-size: 13px;
                letter-spacing: 1.5px;
            }
            .cover-details td.colon {
                width: 5%;
                color: #cbd5e1;
                text-align: center;
                font-weight: bold;
            }
            .cover-details td.value {
                font-weight: 800;
                color: #0f172a;
                font-size: 17px;
                border-bottom: 2px dashed #e2e8f0;
                padding-bottom: 4px;
            }
            .cover-footer {
                margin-top: 35px;
                text-align: center;
                font-size: 14px;
                color: #64748b;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 2px;
            }
        </style>

        <div class="modern-cover">
            <div class="cover-content">
                <div class="cover-header">
                    <?php 
                        $logoUrl = !empty($inst['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $inst['logo']) : \App\Core\Helper::url('/assets/images/logo.png'); 
                    ?>
                    <img src="<?php echo $logoUrl; ?>" alt="Logo">
                    <div class="inst-name"><?= str_replace(['MTS ', 'Mts '], 'MTs ', strtoupper(htmlspecialchars($inst['nama_instansi'] ?? 'MTs ROUDLOTUS SHOLIHIN'))) ?></div>
                    <div class="inst-sub">Roudlotus Sholihin</div>
                </div>
                
                <div class="cover-title-area">
                    <h1>BUKU JURNAL MENGAJAR</h1>
                    <h2><?= ($filter_jenis == 'cover_kelas') ? 'KELAS ' . htmlspecialchars(isset($kelasMap[$filter_kelas]) ? $kelasMap[$filter_kelas] : '...') : 'GURU PRIBADI' ?></h2>
                </div>
                
                <div class="cover-details">
                    <table>
                        <?php if($filter_jenis == 'cover_guru'): ?>
                        <tr>
                            <td class="label">Nama Guru</td>
                            <td class="colon">:</td>
                            <td class="value">
                                <?php 
                                    $guruName = '...................................................';
                                    $guruNip = '...................................................';
                                    if ($filter_guru > 0) {
                                        foreach($guruList as $g) { 
                                            if($g['id'] == $filter_guru) {
                                                $guruName = $g['nama']; 
                                                $guruNip = !empty($g['nip']) ? $g['nip'] : (!empty($g['npk']) ? $g['npk'] : '-');
                                            }
                                        }
                                    }
                                    echo htmlspecialchars($guruName);
                                ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if($filter_jenis == 'cover_kelas'): ?>
                        <tr>
                            <td class="label">Kelas</td>
                            <td class="colon">:</td>
                            <td class="value"><?= htmlspecialchars(isset($kelasMap[$filter_kelas]) ? $kelasMap[$filter_kelas] : 'Semua Kelas') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Wali Kelas</td>
                            <td class="colon">:</td>
                            <td class="value"><?= htmlspecialchars($nama_wali_kelas ?? '...................................................') ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <tr>
                            <td class="label">Semester</td>
                            <td class="colon">:</td>
                            <td class="value"><?= htmlspecialchars($active_semester ?? 'Ganjil / Genap') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Tahun Ajaran</td>
                            <td class="colon">:</td>
                            <td class="value"><?= htmlspecialchars($active_ta ?? '2025/2026') ?></td>
                        </tr>
                    </table>
                </div>
                
                <div class="cover-footer">
                    MTs Roudlotus Sholihin &copy; <?= date('Y') ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- TAMPILAN DATA JURNAL -->
    <div class="kop-surat">
        <?php 
            $logoUrl = !empty($inst['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $inst['logo']) : \App\Core\Helper::url('/assets/images/logo.png'); 
            $alamat = $inst['alamat'] ?? '';
            $desa = !empty($inst['desa']) ? 'Ds. ' . $inst['desa'] : '';
            $kecamatan = !empty($inst['kecamatan']) ? 'Kec. ' . $inst['kecamatan'] : '';
            $kota = !empty($inst['kota']) ? 'Kab. ' . $inst['kota'] : (!empty($inst['kabupaten']) ? 'Kab. ' . $inst['kabupaten'] : '');
            $provinsi = !empty($inst['provinsi']) ? 'Prov. ' . $inst['provinsi'] : '';
            $kodepos = !empty($inst['kodepos']) ? 'KP. ' . $inst['kodepos'] : '';
            
            $fullAddressArray = array_filter([$alamat, $desa, $kecamatan, $kota, $provinsi, $kodepos]);
            $fullAddress = implode(', ', $fullAddressArray);
        ?>
        <img src="<?php echo $logoUrl; ?>" class="logo" alt="Logo">
        <div class="kop-text">
            <h2 style="font-size: <?php echo intval($inst['kop_font_yayasan'] ?? 14); ?>px;"><?php echo htmlspecialchars($inst['yayasan'] ?? ''); ?></h2>
            <h1 style="font-size: <?php echo intval($inst['kop_font_nama'] ?? 18); ?>px;"><?php echo htmlspecialchars($inst['nama'] ?? ''); ?></h1>
            <p><?php echo htmlspecialchars($fullAddress ?: 'Jl. Raya Pendidikan No. 123'); ?></p>
            <p>Website: <?php echo htmlspecialchars($inst['website'] ?? '-'); ?> | Email: <?php echo htmlspecialchars($inst['email'] ?? '-'); ?></p>
        </div>
    </div>
    
    <div class="judul-dokumen">
        <?php if($filter_jenis == 'kelas'): ?>
            BUKU JURNAL KELAS
        <?php else: ?>
            BUKU JURNAL MENGAJAR GURU
        <?php endif; ?>
    </div>

    <div class="filter-info">
        <table style="width:100%; font-size:14px;">
            <tr>
                <td width="15%"><strong>Periode Tanggal</strong></td>
                <td width="2%">:</td>
                <td width="33%">
                    <?= date('d-m-Y', strtotime($filter_tanggal_mulai)) . ' s/d ' . date('d-m-Y', strtotime($filter_tanggal_sampai)) ?>
                </td>
                
                <td width="15%"><strong>Kelas/Mapel</strong></td>
                <td width="2%">:</td>
                <td width="33%">
                    <?php 
                        $kName = !empty($filter_kelas) && isset($kelasMap[$filter_kelas]) ? $kelasMap[$filter_kelas] : 'Semua Kelas';
                        $mpName = !empty($filter_mapel) && isset($mapelMap[$filter_mapel]) ? $mapelMap[$filter_mapel] : 'Semua Mapel';
                        echo htmlspecialchars($kName . ' / ' . $mpName);
                    ?>
                </td>
            </tr>
            
            <tr>
                <td><strong>Jenis Cetakan</strong></td>
                <td>:</td>
                <td><?= ($filter_jenis == 'kelas') ? 'Jurnal Kelas' : 'Jurnal Guru Pribadi' ?></td>
                
                <td><strong>Guru</strong></td>
                <td>:</td>
                <td>
                    <?php 
                        if ($filter_guru > 0) {
                            $guruName = 'Unknown';
                            foreach($guruList as $g) { if($g['id'] == $filter_guru) $guruName = $g['nama']; }
                            echo htmlspecialchars($guruName);
                        } else {
                            echo "Semua Guru";
                        }
                    ?>
                </td>
            </tr>
        </table>
    </div>

    <?php if($filter_jenis == 'kelas'): ?>
    <!-- FORMAT BUKU JURNAL KELAS -->
    <?php if(empty($jurnal_global)): ?>
        <table class="data-table">
            <tr>
                <td class="text-center">Tidak ada aktivitas mengajar pada periode ini.</td>
            </tr>
        </table>
    <?php else: ?>
        <?php 
        $nama_hari = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
        
        // Grouping by date
        $jurnal_by_date = [];
        foreach($jurnal_global as $j) {
            $jurnal_by_date[$j['tanggal']][] = $j;
        }
        
        foreach($jurnal_by_date as $tanggal => $jurnals): 
            $dayEng = date('l', strtotime($tanggal));
            $dayInd = $nama_hari[$dayEng] ?? $dayEng;
            $dateStr = date('d-m-Y', strtotime($tanggal));
        ?>
        <h4 style="text-align: left; margin-bottom: 5px; margin-top: 20px; font-size: 14px;">Hari/Tanggal: <?= $dayInd . ', ' . $dateStr ?></h4>
        <table class="data-table" style="margin-bottom: 5px;">
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="15%">Mata Pelajaran</th>
                    <th width="37%">Materi Pembahasan</th>
                    <th width="20%">Nama Guru</th>
                    <th width="15%">Presensi</th>
                    <th width="10%">Ket</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($jurnals as $j): ?>
                
                <?php if(!empty($j['is_libur'])): ?>
                <tr>
                    <td colspan="6" class="text-center" style="color:red; font-weight:bold; padding: 10px;">
                        LIBUR NASIONAL: <?= htmlspecialchars($j['keterangan_libur']) ?>
                    </td>
                </tr>
                <?php elseif(!empty($j['is_kosong'])): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($j['nama_mapel']) ?></td>
                    <?php if(!empty($j['is_kegiatan_mapel'])): ?>
                        <td style="color:#0ea5e9; font-style:italic; font-weight:bold;"><?= htmlspecialchars($j['keterangan_kegiatan']) ?></td>
                    <?php else: ?>
                        <td style="color:red; font-style:italic;">Belum mengisi jurnal mengajar</td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($j['nama_guru']) ?></td>
                    <td class="text-center">-</td>
                    <td>-</td>
                </tr>
                <?php else: ?>
                <?php
                    $presensi = [];
                    if ($j['absen']['Hadir'] > 0) $presensi[] = "H: " . $j['absen']['Hadir'];
                    if ($j['absen']['Sakit'] > 0) $presensi[] = "S: " . $j['absen']['Sakit'];
                    if ($j['absen']['Izin'] > 0) $presensi[] = "I: " . $j['absen']['Izin'];
                    if ($j['absen']['Alpa'] > 0) $presensi[] = "A: " . $j['absen']['Alpa'];
                    $presensiStr = empty($presensi) ? "Belum Absen" : implode(", ", $presensi);
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($j['nama_mapel']) ?></td>
                    <td><?= nl2br(htmlspecialchars($j['materi'])) ?></td>
                    <td><?= htmlspecialchars($j['nama_guru']) ?></td>
                    <td class="text-center"><?= $presensiStr ?><br><small>Tot: <?= $j['total_siswa'] ?></small></td>
                    <td><?= nl2br(htmlspecialchars($j['keterangan'] ?? '')) ?></td>
                </tr>
                <?php endif; ?>
                
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php elseif($filter_jenis == 'guru'): ?>
    <!-- FORMAT BUKU JURNAL GURU -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="10%">Hari/Tanggal</th>
                <th width="8%">Kelas</th>
                <th width="14%">Mata Pelajaran</th>
                <th width="30%">Materi Pembahasan</th>
                <th width="15%">Presensi</th>
                <th width="10%">Ket</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($jurnal_global)): ?>
            <tr>
                <td colspan="7" class="text-center">Tidak ada aktivitas mengajar pada periode ini.</td>
            </tr>
            <?php else: ?>
                <?php 
                $no = 1; 
                $nama_hari = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
                foreach($jurnal_global as $j): 
                    $dayEng = date('l', strtotime($j['tanggal']));
                    $dayInd = $nama_hari[$dayEng] ?? $dayEng;
                    $dateStr = date('d-m-Y', strtotime($j['tanggal']));
                ?>
                
                <?php if(!empty($j['is_libur'])): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= $dayInd . '<br>' . $dateStr ?></td>
                    <td colspan="5" class="text-center" style="color:red; font-weight:bold; padding: 10px;">
                        <?= htmlspecialchars($j['keterangan_libur']) ?>
                    </td>
                </tr>
                <?php elseif(!empty($j['is_kosong'])): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= $dayInd . '<br>' . $dateStr ?></td>
                    <td class="text-center"><?= htmlspecialchars($j['nama_kelas'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($j['nama_mapel']) ?></td>
                    <td colspan="3" class="text-center" style="color:red; font-style:italic;">
                        <?php if(!empty($j['is_kegiatan_mapel'])): ?>
                            <span style="color: #0284c7; font-weight:bold; font-style:normal;"><?= htmlspecialchars($j['keterangan_kegiatan']) ?></span>
                        <?php else: ?>
                            Belum mengisi jurnal mengajar
                        <?php endif; ?>
                    </td>
                </tr>
                <?php else: ?>
                <?php 
                    $presensi = [];
                    if (isset($j['absen'])) {
                        if (isset($j['absen']['Hadir']) && $j['absen']['Hadir'] > 0) $presensi[] = "H: " . $j['absen']['Hadir'];
                        if (isset($j['absen']['Sakit']) && $j['absen']['Sakit'] > 0) $presensi[] = "S: " . $j['absen']['Sakit'];
                        if (isset($j['absen']['Izin']) && $j['absen']['Izin'] > 0) $presensi[] = "I: " . $j['absen']['Izin'];
                        if (isset($j['absen']['Alpa']) && $j['absen']['Alpa'] > 0) $presensi[] = "A: " . $j['absen']['Alpa'];
                    }
                    $presensiStr = empty($presensi) ? "Belum Absen" : implode(", ", $presensi);
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= $dayInd . '<br>' . $dateStr ?></td>
                    <td class="text-center"><?= htmlspecialchars($j['nama_kelas'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($j['nama_mapel']) ?></td>
                    <td><?= nl2br(htmlspecialchars($j['materi'] ?? '')) ?></td>
                    <td class="text-center"><?= $presensiStr ?><br><small>Tot: <?= $j['total_siswa'] ?? 0 ?></small></td>
                    <td><?= nl2br(htmlspecialchars($j['keterangan'] ?? '')) ?></td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <table style="width: 100%; margin-top: 30px; page-break-inside: avoid;">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: bottom;">
                <p style="margin-bottom: 0;">Mengetahui,</p>
                <p style="margin-top: 0;">Kepala Madrasah<br><br><br><br><br></p>
                <div class="signature-name" style="text-decoration: underline; font-weight: bold;"><?= htmlspecialchars($kepsek['nama'] ?? '_______________________') ?></div>
                <?php if(!empty($kepsek['nip']) && $kepsek['nip'] !== '-'): ?>
                    <div>NIP. <?= htmlspecialchars($kepsek['nip']) ?></div>
                <?php endif; ?>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: bottom;">
                <?php 
                    $nama_bulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                    $tgl_cetak = date('d', strtotime($filter_tanggal_sampai)) . ' ' . $nama_bulan[date('m', strtotime($filter_tanggal_sampai))] . ' ' . date('Y', strtotime($filter_tanggal_sampai));
                    $desa_cetak = htmlspecialchars($inst['desa'] ?? 'Gunung Terang');
                ?>
                <p style="margin-bottom: 0;"><?= $desa_cetak ?>, <?= $tgl_cetak ?></p>
                
                <?php if($filter_jenis == 'kelas' && !empty($filter_kelas)): ?>
                    <p style="margin-top: 0;">Wali Kelas <?= htmlspecialchars(isset($kelasMap[$filter_kelas]) ? $kelasMap[$filter_kelas] : '') ?><br><br><br><br><br></p>
                    <div class="signature-name" style="text-decoration: underline; font-weight: bold;"><?= htmlspecialchars($nama_wali_kelas) ?></div>
                <?php elseif($filter_jenis == 'guru'): ?>
                    <p style="margin-top: 0;">Guru Mata Pelajaran<br><br><br><br><br></p>
                    <div class="signature-name" style="text-decoration: underline; font-weight: bold;">
                        <?php 
                            $gName = '...................................................';
                            if ($filter_guru > 0) {
                                foreach($guruList as $g) { if($g['id'] == $filter_guru) $gName = $g['nama']; }
                            }
                            echo htmlspecialchars($gName);
                        ?>
                    </div>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <?php endif; // End of Tampilan Data Jurnal ?>

    <?php endif; // End of empty($filter_jenis) ?>

</body>
</html>
