<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Absensi Manual <?php echo htmlspecialchars($nama_kelas ?? 'Kelas'); ?></title>
    <?php $logoUrl = !empty($inst['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $inst['logo']) : \App\Core\Helper::url('/assets/images/logo.png'); ?>
    <link rel="icon" type="image/png" href="<?php echo $logoUrl; ?>">
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 10px; margin: 0; padding: 0; color: #000; }
        .print-container { padding: 15px; }
        
        .kop-surat { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 5px; margin-bottom: 8px; }
        .kop-surat img { width: 60px; height: 60px; object-fit: contain; }
        .kop-text { text-align: center; flex: 1; }
        .kop-text h1 { margin: 0; font-size: 0.9rem; text-transform: uppercase; font-weight: bold; }
        .kop-text h2 { margin: 2px 0; font-size: 1rem; font-weight: bold; }
        .kop-text p { margin: 2px 0; font-size: 0.75rem; font-style: italic; }
        
        .header { text-align: center; margin-bottom: 5px; }
        .header h2 { margin: 0; font-size: 13px; text-transform: uppercase; text-decoration: underline; }
        
        .info-table { width: 100%; margin-bottom: 5px; font-weight: bold; font-size: 10px; }
        .info-table td { padding: 0; }
        
        .absen-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .absen-table th, .absen-table td { border: 1px solid #000; padding: 0px 1px; text-align: center; }
        .absen-table th { background: #f0f0f0; font-size: 7px; }
        .absen-table td.nama { text-align: left; padding-left: 3px; white-space: nowrap; font-size: 8px; overflow: hidden; text-overflow: ellipsis; max-width: 120px; }
        
        .libur-cell { background-color: #fca5a5; font-size: 8px; font-weight: bold; writing-mode: vertical-rl; text-orientation: mixed; white-space: nowrap; padding: 2px; letter-spacing: 1px; }
        
        @media print {
            @page { size: landscape; margin: 5mm 5mm 2mm 5mm; } 
            body { -webkit-print-color-adjust: exact; margin: 0; }
            .no-print { display: none !important; }
            html, body { height: 100%; }
            .month-page {
                page-break-after: always;
                page-break-inside: avoid;
                height: 190mm; /* Safe printable height for F4/A4 landscape */
                display: flex;
                flex-direction: column;
            }
            .month-page:last-child {
                page-break-after: auto;
            }
        }
        
        @media screen {
            .month-page {
                background: white;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                margin-bottom: 30px;
                padding: 20px;
                min-height: 600px;
                display: flex;
                flex-direction: column;
            }
        }

        .filter-box { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #cbd5e1; display: flex; gap: 10px; align-items: center; justify-content: center; }
        .filter-box select { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; font-size: 14px; }
        
        .table-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }
        .absen-table {
            width: 100%;
            height: 100%; /* Make table stretch to fill remaining space */
            border-collapse: collapse;
        }
    </style>
</head>
<body>
    <div class="print-container">
        
        <div class="no-print filter-box">
            <span style="font-family: sans-serif; font-size: 14px; font-weight: bold;">Pilih Kelas:</span>
            <select id="kelasFilter" onchange="changeFilter()">
                <option value="">-- Pilih Kelas --</option>
                <?php foreach($kelasList as $k): ?>
                <option value="<?php echo $k['id']; ?>" <?php echo $kelas_id == $k['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                <?php endforeach; ?>
            </select>
            
            <span style="font-family: sans-serif; font-size: 14px; font-weight: bold; margin-left: 15px;">Bulan:</span>
            <select id="bulanFilter" onchange="changeFilter()">
                <option value="">-- Semua Bulan --</option>
                <?php 
                $nama_bulan_indo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                foreach($nama_bulan_indo as $index => $nama): 
                ?>
                <option value="<?php echo $index + 1; ?>" <?php echo $bulan_filter == ($index + 1) ? 'selected' : ''; ?>><?php echo $nama; ?></option>
                <?php endforeach; ?>
            </select>
            
            <span style="font-family: sans-serif; font-size: 14px; font-weight: bold; margin-left: 15px;">Tahun:</span>
            <select id="tahunFilter" onchange="changeFilter()">
                <?php 
                $current_year = date('Y');
                for($y = $current_year - 1; $y <= $current_year + 1; $y++):
                ?>
                <option value="<?php echo $y; ?>" <?php echo $tahun_filter == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
            
            <span style="font-family: sans-serif; font-size: 14px; font-weight: bold; margin-left: 15px;">Format Cetak:</span>
            <select id="formatFilter" onchange="changeFilter()">
                <option value="kosongan" <?php echo $format == 'kosongan' ? 'selected' : ''; ?>>Kosongan (Manual)</option>
                <option value="berisi_data" <?php echo $format == 'berisi_data' ? 'selected' : ''; ?>>Berisi Data (Rekapan)</option>
            </select>
            
            <button onclick="window.print()" style="padding: 8px 16px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: bold; font-family: sans-serif; margin-left: 15px;">Cetak Lembar</button>
        </div>

        <?php if(empty($kelas_id)): ?>
            <div class="no-print" style="text-align: center; padding: 50px; font-family: sans-serif; font-size: 18px; color: #64748b;">
                Silakan pilih kelas terlebih dahulu melalui menu dropdown di atas.
            </div>
        <?php else: 
            if (!isset($nama_bulan_indo)) {
                $nama_bulan_indo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            }
            $count = 0;
            foreach($nama_bulan_indo as $bulan):
                $count++;
                if (!empty($bulan_filter) && $bulan_filter != $count) continue;
                
                $jml_hari = cal_days_in_month(CAL_GREGORIAN, $count, $tahun_filter);
                $last_date_str = sprintf('%02d %s %d', $jml_hari, $bulan, $tahun_filter);
        ?>
        
        <div class="month-page">
            <div class="kop-surat">
                <?php if(!empty($inst['logo'])): ?>
                    <img src="/public/uploads/logo/<?php echo htmlspecialchars($inst['logo']); ?>" alt="Logo">
                <?php else: ?>
                    <div style="width: 70px;"></div>
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
                    <h1 style="font-size: <?php echo intval($inst['kop_font_yayasan'] ?? 14) - 2; ?>px;"><?php echo htmlspecialchars($inst['yayasan'] ?? ''); ?></h1>
                    <h2 style="font-size: <?php echo intval($inst['kop_font_nama'] ?? 18) - 4; ?>px;"><?php echo htmlspecialchars($inst['nama'] ?? ''); ?></h2>
                    <p style="font-size: 0.65rem;"><?php echo htmlspecialchars($fullAddress); ?></p>
                    <p style="font-size: 0.65rem;">Website: <?php echo htmlspecialchars($inst['website'] ?? ''); ?> | Email: <?php echo htmlspecialchars($inst['email'] ?? ''); ?></p>
                </div>
                <div style="width: 70px;"></div>
            </div>

            <div class="header">
                <h2>LEMBAR ABSENSI KELAS</h2>
            </div>
            
            <table class="info-table">
                <tr>
                    <td width="40">Kelas</td>
                    <td width="10">:</td>
                    <td><?php echo htmlspecialchars($nama_kelas); ?></td>
                    <td width="40" align="right">Bulan</td>
                    <td width="10">:</td>
                    <td width="100" style="border-bottom: 1px dotted #000; font-size: 11px;"><?php echo $bulan . ' ' . $tahun_filter; ?></td>
                </tr>
            </table>
            
            <div class="table-wrapper">
                <table class="absen-table">
                    <thead>
                        <tr>
                            <th rowspan="2" width="20">No</th>
                            <th rowspan="2" width="50">NIS</th>
                            <th rowspan="2">Nama Siswa</th>
                            <th rowspan="2" width="15">L/P</th>
                            <th colspan="<?php echo $jml_hari; ?>">Tanggal</th>
                            <th colspan="5">Jumlah</th>
                            <th rowspan="2" width="25">%</th>
                        </tr>
                        <tr>
                            <?php for($i=1; $i<=$jml_hari; $i++): ?>
                            <th width="12"><?php echo $i; ?></th>
                            <?php endfor; ?>
                            <th width="12">H</th>
                            <th width="12">S</th>
                            <th width="12">I</th>
                            <th width="12">A</th>
                            <th width="12">B</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($siswaList)): ?>
                        <tr>
                            <td colspan="41" style="padding: 15px;">Tidak ada data siswa di kelas ini.</td>
                        </tr>
                        <?php else: ?>
                            <?php 
                            // Pre-calculate active days in this month
                            $active_days_in_month = [];
                            for($i=1; $i<=$jml_hari; $i++) {
                                $tgl_cek = sprintf('%04d-%02d-%02d', $tahun_filter, $count, $i);
                                $day_of_week = date('N', strtotime($tgl_cek));
                                if ($day_of_week == 5 || isset($holidays[$tgl_cek])) continue;

                                $has_scan = false;
                                foreach ($siswaList as $s_temp) {
                                    if (!empty($absenData[$s_temp['id']][$count][$i])) {
                                        $has_scan = true;
                                        break;
                                    }
                                }
                                if ($has_scan) {
                                    $active_days_in_month[$i] = true;
                                }
                            }

                            $no = 1; foreach ($siswaList as $s): 
                                $rek = ['H'=>0, 'S'=>0, 'I'=>0, 'A'=>0, 'B'=>0];
                                $hari_efektif = 0;
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($s['nis']); ?></td>
                                <td class="nama"><?php echo htmlspecialchars($s['nama']); ?></td>
                                <td><?php echo htmlspecialchars($s['jk'] ?? ''); ?></td>
                                <?php for($i=1; $i<=$jml_hari; $i++): 
                                    $tgl_cek = sprintf('%04d-%02d-%02d', $tahun_filter, $count, $i);
                                    $day_of_week = date('N', strtotime($tgl_cek));
                                    $is_friday = ($day_of_week == 5);
                                    $is_holiday = isset($holidays[$tgl_cek]);
                                    
                                    if ($is_friday || $is_holiday) {
                                        if ($no == 2) { // $no starts at 1, but is incremented to 2 right after printing
                                            $text_libur = $is_holiday ? $holidays[$tgl_cek] : "Libur Hari Jumat";
                                            echo '<td rowspan="'.count($siswaList).'" class="libur-cell">'.$text_libur.'</td>';
                                        }
                                        continue;
                                    }

                                    $hari_efektif++;
                                    $st = $absenData[$s['id']][$count][$i] ?? '';
                                    
                                    // Jika kosong padahal hari ini aktif (ada temannya yg absen) -> otomatis Alpa
                                    if (empty($st) && isset($active_days_in_month[$i])) {
                                        $st = 'Alpa';
                                    }

                                    $huruf = '';
                                    $bgColor = '';
                                    $textColor = '#000';
                                    if ($st == 'Hadir') { $huruf = 'H'; }
                                    elseif ($st == 'Sakit') { $huruf = 'S'; $bgColor = '#3b82f6'; $textColor = '#fff'; }
                                    elseif ($st == 'Izin') { $huruf = 'I'; $bgColor = '#eab308'; $textColor = '#fff'; }
                                    elseif ($st == 'Alpa' || $st == 'Alpha') { $huruf = 'A'; $bgColor = '#ef4444'; $textColor = '#fff'; }
                                    elseif ($st == 'Bolos') { $huruf = 'B'; $bgColor = '#ef4444'; $textColor = '#fff'; }
                                    
                                    if ($huruf && isset($rek[$huruf])) {
                                        $rek[$huruf]++;
                                    }
                                ?>
                                <td style="background-color: <?php echo $bgColor; ?>;"><span style="font-size:7px; font-weight:bold; color: <?php echo $textColor; ?>;"><?php echo $huruf; ?></span></td>
                                <?php endfor; ?>
                                
                                <td style="font-size:7px; font-weight:bold; background-color:#f8fafc;"><?php echo $rek['H'] ?: ''; ?></td>
                                <td style="font-size:7px; font-weight:bold; background-color:#eff6ff; color:#1d4ed8;"><?php echo $rek['S'] ?: ''; ?></td>
                                <td style="font-size:7px; font-weight:bold; background-color:#fefce8; color:#a16207;"><?php echo $rek['I'] ?: ''; ?></td>
                                <td style="font-size:7px; font-weight:bold; background-color:#fef2f2; color:#b91c1c;"><?php echo $rek['A'] ?: ''; ?></td>
                                <td style="font-size:7px; font-weight:bold; background-color:#fef2f2; color:#b91c1c;"><?php echo $rek['B'] ?: ''; ?></td>
                                <?php 
                                    $persentase = '';
                                    $hari_tercatat = $rek['H'] + $rek['S'] + $rek['I'] + $rek['A'] + $rek['B'];
                                    if ($format == 'berisi_data' && $hari_tercatat > 0) {
                                        $persentase = round(($rek['H'] / $hari_tercatat) * 100) . '%';
                                    }
                                ?>
                                <td style="font-size:7px; font-weight:bold; background-color:#f8fafc; color:#0f172a;"><?php echo $persentase; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-top: 5px; padding: 0 50px;">
                <div style="text-align: center; width: 250px;">
                    <p style="margin-bottom: 35px; margin-top:0;">Mengetahui,<br>Kepala Madrasah,</p>
                    <p style="font-weight: bold; text-decoration: underline; margin:0;"><?php echo htmlspecialchars($inst['nama_kepala'] ?? '...................................................'); ?></p>
                </div>
                <div style="text-align: center; width: 250px;">
                    <p style="margin-bottom: 35px; margin-top:0;"><?php echo !empty($inst['desa']) ? $inst['desa'] : 'Gunung Terang'; ?>, <?php echo $last_date_str; ?><br>Wali Kelas,</p>
                    <p style="font-weight: bold; text-decoration: underline; margin:0;"><?php echo htmlspecialchars($nama_wali_kelas); ?></p>
                </div>
            </div>
        </div>
        
        <?php endforeach; endif; ?>
    </div>
    <script>
        function changeFilter() {
            var kls = document.getElementById('kelasFilter').value;
            var bln = document.getElementById('bulanFilter').value;
            var thn = document.getElementById('tahunFilter').value;
            var fmt = document.getElementById('formatFilter').value;
            window.location.href = '/absen/cetak-manual?kelas_id=' + kls + '&bulan=' + bln + '&tahun=' + thn + '&format=' + fmt;
        }
    </script>
</body>
</html>
