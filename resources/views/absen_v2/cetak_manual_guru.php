<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Lembar Absensi Guru & Tenaga Kependidikan</title>
    <?php 
        $logoUrl = !empty($inst['logo']) ? '/public/uploads/logo/' . htmlspecialchars($inst['logo']) : '/assets/images/logo.png'; 
    ?>
    <link rel="icon" type="image/png" href="<?php echo $logoUrl; ?>">
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 10px; margin: 0; padding: 0; color: #000; }
        .print-container { padding: 15px; }
        
        .kop-surat { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 5px; margin-bottom: 8px; }
        .kop-surat img { width: 60px; height: 60px; object-fit: contain; }
        .kop-text { text-align: center; flex: 1; }
        .kop-text h1 { margin: 0; font-size: 0.95rem; text-transform: uppercase; font-weight: bold; }
        .kop-text h2 { margin: 2px 0; font-size: 1.05rem; font-weight: bold; }
        .kop-text p { margin: 2px 0; font-size: 0.72rem; }
        
        .header { text-align: center; margin-bottom: 5px; }
        .header h2 { margin: 0; font-size: 13px; text-transform: uppercase; text-decoration: underline; letter-spacing: 0.5px; }
        
        .info-table { width: 100%; margin-bottom: 5px; font-weight: bold; font-size: 9.5px; }
        .info-table td { padding: 1px 0; }
        
        .table-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            margin-bottom: 8px;
        }
        .absen-table {
            width: 100%;
            border-collapse: collapse;
        }
        .absen-table th, .absen-table td { border: 1px solid #000; padding: 1.5px 1px; text-align: center; }
        .absen-table th { background: #f0f0f0; font-size: 7.5px; font-weight: bold; }
        .absen-table td.nama { text-align: left; padding-left: 4px; white-space: nowrap; font-size: 8px; overflow: hidden; text-overflow: ellipsis; max-width: 140px; font-weight: 600; }
        .absen-table td.jabatan { font-size: 7.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 65px; color: #334155; }
        
        .libur-cell {
            background-color: #fca5a5 !important;
            font-size: 7.5px;
            font-weight: bold;
            color: #991b1b;
            writing-mode: vertical-rl;
            text-orientation: mixed;
            white-space: nowrap;
            padding: 2px;
            letter-spacing: 1px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        @media print {
            @page { size: landscape; margin: 5mm 5mm 2mm 5mm; } 
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; margin: 0; }
            .no-print { display: none !important; }
            html, body { height: 100%; }
            .month-page {
                page-break-after: always;
                page-break-inside: avoid;
                height: 190mm;
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
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                margin-bottom: 30px;
                padding: 20px;
                min-height: 600px;
                display: flex;
                flex-direction: column;
                border-radius: 4px;
            }
        }

        .filter-box {
            background: #f8fafc;
            padding: 12px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #cbd5e1;
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }
        .filter-box select {
            padding: 7px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            outline: none;
            font-size: 13px;
            font-family: sans-serif;
            font-weight: 600;
            background: #fff;
            color: #0f172a;
        }
        .filter-box button {
            padding: 7px 16px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
            font-family: sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: 0.15s;
        }
        .filter-box button:hover { background: #059669; }
        .filter-box .btn-back {
            background: #64748b;
            color: white;
            text-decoration: none;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            font-family: sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .filter-box .btn-back:hover { background: #475569; }
    </style>
</head>
<body>
    <div class="print-container">
        
        <!-- Filter Bar (No Print) -->
        <div class="no-print filter-box">
            <span style="font-family: sans-serif; font-size: 13px; font-weight: bold; color:#334155;">Jabatan:</span>
            <select id="jabatanFilter" onchange="changeFilter()">
                <option value="">-- Semua Jabatan --</option>
                <?php foreach($jabatanList as $j): ?>
                <option value="<?php echo htmlspecialchars($j); ?>" <?php echo $jabatan == $j ? 'selected' : ''; ?>><?php echo htmlspecialchars($j); ?></option>
                <?php endforeach; ?>
            </select>

            <span style="font-family: sans-serif; font-size: 13px; font-weight: bold; color:#334155;">Guru:</span>
            <select id="guruFilter" onchange="changeFilter()" style="max-width:180px;">
                <option value="">-- Semua Guru --</option>
                <?php foreach($allGuru as $gItem): ?>
                <option value="<?php echo $gItem['id']; ?>" <?php echo $guru_id == $gItem['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($gItem['nama']); ?></option>
                <?php endforeach; ?>
            </select>
            
            <span style="font-family: sans-serif; font-size: 13px; font-weight: bold; color:#334155;">Bulan:</span>
            <select id="bulanFilter" onchange="changeFilter()">
                <option value="">-- Semua Bulan --</option>
                <?php 
                $nama_bulan_indo = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
                foreach($nama_bulan_indo as $num => $nama): 
                ?>
                <option value="<?php echo $num; ?>" <?php echo $bulan_filter == $num ? 'selected' : ''; ?>><?php echo $nama; ?></option>
                <?php endforeach; ?>
            </select>
            
            <span style="font-family: sans-serif; font-size: 13px; font-weight: bold; color:#334155;">Tahun:</span>
            <select id="tahunFilter" onchange="changeFilter()">
                <?php 
                $current_year = date('Y');
                for($y = $current_year - 1; $y <= $current_year + 1; $y++):
                ?>
                <option value="<?php echo $y; ?>" <?php echo $tahun_filter == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
            
            <span style="font-family: sans-serif; font-size: 13px; font-weight: bold; color:#334155;">Format Cetak:</span>
            <select id="formatFilter" onchange="changeFilter()">
                <option value="berisi_data" <?php echo $format == 'berisi_data' ? 'selected' : ''; ?>>Berisi Data (Rekapan)</option>
                <option value="kosongan" <?php echo $format == 'kosongan' ? 'selected' : ''; ?>>Kosongan (Manual)</option>
            </select>
            
            <button onclick="window.print()">
                🖨️ Cetak Lembar
            </button>
            <a href="/absen/rekap-bulanan-guru?bulan=<?php echo $bulan_filter; ?>&tahun=<?php echo $tahun_filter; ?>" class="btn-back">
                ↩ Kembali ke Rekap Guru
            </a>
        </div>

        <?php if(empty($guruList)): ?>
            <div class="no-print" style="text-align: center; padding: 50px; font-family: sans-serif; font-size: 16px; color: #64748b;">
                Tidak ada data guru yang sesuai dengan filter di atas.
            </div>
        <?php else: 
            $monthsToRender = !empty($bulan_filter) ? [$bulan_filter => $nama_bulan_indo[$bulan_filter]] : $nama_bulan_indo;
            
            foreach($monthsToRender as $blnNum => $blnNama):
                $jml_hari = cal_days_in_month(CAL_GREGORIAN, $blnNum, $tahun_filter);
                $last_date_str = sprintf('%02d %s %d', $jml_hari, $blnNama, $tahun_filter);
        ?>
        
        <div class="month-page">
            <!-- Kop Surat Resmi -->
            <div class="kop-surat">
                <?php if(!empty($inst['logo'])): ?>
                    <img src="/public/uploads/logo/<?php echo htmlspecialchars($inst['logo']); ?>" alt="Logo">
                <?php else: ?>
                    <div style="width: 60px;"></div>
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
                    <h1 style="font-size: <?php echo intval($inst['kop_font_yayasan'] ?? 14) - 2; ?>px;"><?php echo htmlspecialchars($inst['yayasan'] ?? 'YAYASAN PENDIDIKAN ISLAM ROUDLOTUS SHOLIHIN'); ?></h1>
                    <h2 style="font-size: <?php echo intval($inst['kop_font_nama'] ?? 18) - 4; ?>px;"><?php echo htmlspecialchars($inst['nama'] ?? 'MTs ROUDLOTUS SHOLIHIN'); ?></h2>
                    <p><?php echo htmlspecialchars($fullAddress); ?></p>
                    <p>Website: <?php echo htmlspecialchars($inst['website'] ?? 'mtsroudlotussholihin.sch.id'); ?> | Email: <?php echo htmlspecialchars($inst['email'] ?? 'mtsroudlotussholihin@gmail.com'); ?></p>
                </div>
                <div style="width: 60px;"></div>
            </div>

            <!-- Judul Halaman -->
            <div class="header">
                <h2>LEMBAR ABSENSI GURU & TENAGA KEPENDIDIKAN</h2>
            </div>
            
            <!-- Info Satuan & Bulan -->
            <table class="info-table">
                <tr>
                    <td width="95">Satuan Pendidikan</td>
                    <td width="10">:</td>
                    <td><?php echo htmlspecialchars($inst['nama'] ?? 'MTs Roudlotus Sholihin'); ?></td>
                    <td width="40" align="right">Bulan</td>
                    <td width="10">:</td>
                    <td width="130" style="border-bottom: 1px dotted #000; font-size: 11px;"><?php echo $blnNama . ' ' . $tahun_filter; ?></td>
                </tr>
                <tr>
                    <td>Tahun Pelajaran</td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($active_year_name); ?></td>
                    <td align="right">Unit</td>
                    <td>:</td>
                    <td style="border-bottom: 1px dotted #000;"><?php echo !empty($jabatan) ? htmlspecialchars($jabatan) : 'Semua Guru & Tenaga Kependidikan'; ?></td>
                </tr>
            </table>
            
            <!-- Tabel Matriks Absensi -->
            <div class="table-wrapper">
                <table class="absen-table">
                    <thead>
                        <tr>
                            <th rowspan="2" width="20">No</th>
                            <th rowspan="2" width="65">NIP / NPK</th>
                            <th rowspan="2">Nama Guru / Pegawai</th>
                            <th rowspan="2" width="60">Jabatan</th>
                            <th colspan="<?php echo $jml_hari; ?>">Tanggal</th>
                            <th colspan="5">Jumlah</th>
                            <th rowspan="2" width="25">%</th>
                        </tr>
                        <tr>
                            <?php for($i=1; $i<=$jml_hari; $i++): ?>
                            <th width="12"><?php echo $i; ?></th>
                            <?php endfor; ?>
                            <th width="12" title="Hadir">H</th>
                            <th width="12" title="Terlambat">T</th>
                            <th width="12" title="Sakit">S</th>
                            <th width="12" title="Izin">I</th>
                            <th width="12" title="Alpa">A</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rowIndex = 0;
                        foreach ($guruList as $g): 
                            $rek = ['H'=>0, 'T'=>0, 'S'=>0, 'I'=>0, 'A'=>0];
                            $nipDisplay = !empty($g['nip']) ? $g['nip'] : (!empty($g['npk']) ? $g['npk'] : '-');
                        ?>
                        <tr>
                            <td><?php echo $rowIndex + 1; ?></td>
                            <td style="font-size:7.5px;"><?php echo htmlspecialchars($nipDisplay); ?></td>
                            <td class="nama"><?php echo htmlspecialchars($g['nama']); ?></td>
                            <td class="jabatan"><?php echo htmlspecialchars($g['jabatan'] ?? '-'); ?></td>
                            
                            <?php for($i=1; $i<=$jml_hari; $i++): 
                                $tgl_cek = sprintf('%04d-%02d-%02d', $tahun_filter, $blnNum, $i);
                                $day_of_week = date('N', strtotime($tgl_cek));
                                $is_friday = ($day_of_week == 5);
                                $is_holiday = isset($holidays[$tgl_cek]);
                                
                                if ($is_friday || $is_holiday) {
                                    if ($rowIndex === 0) {
                                        $text_libur = $is_holiday ? $holidays[$tgl_cek] : "Libur Hari Jumat";
                                        echo '<td rowspan="'.count($guruList).'" class="libur-cell">'.htmlspecialchars($text_libur).'</td>';
                                    }
                                    continue;
                                }

                                $huruf = '';
                                $bgColor = '';
                                $textColor = '#000';

                                if ($format === 'berisi_data') {
                                    $st = $absenData[$g['id']][$blnNum][$i] ?? '';
                                    
                                    if ($st == 'Hadir') {
                                        $huruf = 'H';
                                        $bgColor = '#f0fdf4';
                                        $textColor = '#15803d';
                                        $rek['H']++;
                                    } elseif ($st == 'Terlambat') {
                                        $huruf = 'T';
                                        $bgColor = '#fefce8';
                                        $textColor = '#a16207';
                                        $rek['T']++;
                                    } elseif ($st == 'Sakit') {
                                        $huruf = 'S';
                                        $bgColor = '#eff6ff';
                                        $textColor = '#1d4ed8';
                                        $rek['S']++;
                                    } elseif ($st == 'Izin') {
                                        $huruf = 'I';
                                        $bgColor = '#f5f3ff';
                                        $textColor = '#7c3aed';
                                        $rek['I']++;
                                    } elseif ($st == 'Alpa' || $st == 'Alpha') {
                                        $huruf = 'A';
                                        $bgColor = '#fef2f2';
                                        $textColor = '#dc2626';
                                        $rek['A']++;
                                    } elseif ($tgl_cek <= date('Y-m-d')) {
                                        // Belum tercatat di hari lampau
                                        $huruf = '-';
                                        $textColor = '#94a3b8';
                                    }
                                }
                            ?>
                            <td style="background-color: <?php echo $bgColor; ?>;">
                                <span style="font-size:7px; font-weight:bold; color: <?php echo $textColor; ?>;"><?php echo $huruf; ?></span>
                            </td>
                            <?php endfor; ?>
                            
                            <!-- Kolom Rekapitulasi -->
                            <td style="font-size:7px; font-weight:bold; background-color:#f0fdf4; color:#15803d;"><?php echo $rek['H'] ?: ''; ?></td>
                            <td style="font-size:7px; font-weight:bold; background-color:#fefce8; color:#a16207;"><?php echo $rek['T'] ?: ''; ?></td>
                            <td style="font-size:7px; font-weight:bold; background-color:#eff6ff; color:#1d4ed8;"><?php echo $rek['S'] ?: ''; ?></td>
                            <td style="font-size:7px; font-weight:bold; background-color:#f5f3ff; color:#7c3aed;"><?php echo $rek['I'] ?: ''; ?></td>
                            <td style="font-size:7px; font-weight:bold; background-color:#fef2f2; color:#dc2626;"><?php echo $rek['A'] ?: ''; ?></td>
                            
                            <?php 
                                $persentase = '';
                                $hari_tercatat = $rek['H'] + $rek['T'] + $rek['S'] + $rek['I'] + $rek['A'];
                                if ($format == 'berisi_data' && $hari_tercatat > 0) {
                                    $persentase = round((($rek['H'] + $rek['T']) / $hari_tercatat) * 100) . '%';
                                }
                            ?>
                            <td style="font-size:7px; font-weight:bold; background-color:#f8fafc; color:#0f172a;"><?php echo $persentase; ?></td>
                        </tr>
                        <?php 
                            $rowIndex++;
                        endforeach; 
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Tanda Tangan -->
            <div style="display: flex; justify-content: space-between; margin-top: 8px; padding: 0 40px; font-size: 10px;">
                <div style="text-align: center; width: 250px;">
                    <p style="margin-bottom: 38px; margin-top:0;">Mengetahui,<br>Kepala Madrasah,</p>
                    <p style="font-weight: bold; text-decoration: underline; margin:0;"><?php echo htmlspecialchars($inst['nama_kepala'] ?? '...................................................'); ?></p>
                    <p style="margin:2px 0 0 0; font-size:9px;">NIP. <?php echo htmlspecialchars($inst['nip_kepala'] ?? '-'); ?></p>
                </div>
                <div style="text-align: center; width: 250px;">
                    <p style="margin-bottom: 38px; margin-top:0;"><?php echo !empty($inst['desa']) ? $inst['desa'] : 'Gunung Terang'; ?>, <?php echo $last_date_str; ?><br>Koordinator Absensi / Waka,</p>
                    <p style="font-weight: bold; text-decoration: underline; margin:0;"><?php echo htmlspecialchars($inst['nama_waka'] ?? '...................................................'); ?></p>
                    <p style="margin:2px 0 0 0; font-size:9px;">NIP. -</p>
                </div>
            </div>
        </div>
        
        <?php endforeach; endif; ?>
    </div>
    
    <script>
        function changeFilter() {
            var jab = document.getElementById('jabatanFilter').value;
            var guru = document.getElementById('guruFilter').value;
            var bln = document.getElementById('bulanFilter').value;
            var thn = document.getElementById('tahunFilter').value;
            var fmt = document.getElementById('formatFilter').value;
            window.location.href = '/absen/cetak-manual-guru?jabatan=' + encodeURIComponent(jab) + '&guru_id=' + guru + '&bulan=' + bln + '&tahun=' + thn + '&format=' + fmt;
        }
    </script>
</body>
</html>

