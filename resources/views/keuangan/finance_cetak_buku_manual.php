<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Buku Manual Keuangan</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 20px;
            background: #f1f5f9;
        }
        .print-container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        .kop-surat { display: flex; align-items: center; justify-content: center; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 15px; text-align: center; }
        .kop-surat img { width: 70px; height: auto; margin-right: 20px; }
        .kop-text h1 { margin: 0; font-size: 16px; font-weight: bold; }
        .kop-text h2 { margin: 5px 0; font-size: 20px; font-weight: bold; }
        .kop-text p { margin: 0; font-size: 12px; }
        
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 16px; text-decoration: underline; text-transform: uppercase; }
        
        .info-table { width: 100%; margin-bottom: 15px; font-size: 13px; font-weight: bold; }
        .info-table td { padding: 3px; }
        
        @media print {
            @page { size: landscape; margin: 10mm; } 
            body { -webkit-print-color-adjust: exact; margin: 0; background: white; }
            .no-print { display: none !important; }
            html, body { height: 100%; }
            .month-page {
                page-break-after: always;
                page-break-inside: avoid;
                height: 185mm; /* Safe printable height for F4/A4 landscape */
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
        
        .absen-table th, .absen-table td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 12px;
            text-align: center;
        }
        .absen-table th { background: #f3f4f6; font-weight: bold; }
        .absen-table td.nama { text-align: left; padding-left: 8px; font-weight: bold; font-size: 11px; white-space: nowrap; }
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
            
            <span style="font-family: sans-serif; font-size: 14px; font-weight: bold; margin-left: 15px;">Kategori Pembayaran:</span>
            <select id="kategoriFilter" onchange="changeFilter()">
                <option value="">-- Semua Kategori --</option>
                <?php foreach($kategoriList as $k): ?>
                <option value="<?php echo htmlspecialchars($k); ?>" <?php echo $kategori == $k ? 'selected' : ''; ?>><?php echo htmlspecialchars($k); ?></option>
                <?php endforeach; ?>
            </select>
            
            <span style="font-family: sans-serif; font-size: 14px; font-weight: bold; margin-left: 15px;">Tipe Cetak:</span>
            <select id="tipeCetak" onchange="changeFilter()" style="padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;">
                <option value="kosong" <?php echo (!isset($_GET['tipe']) || $_GET['tipe'] == 'kosong') ? 'selected' : ''; ?>>Kosongan (Manual)</option>
                <option value="data" <?php echo (isset($_GET['tipe']) && $_GET['tipe'] == 'data') ? 'selected' : ''; ?>>Berisi Data Transaksi</option>
            </select>
            
            <button onclick="window.print()" style="padding: 8px 16px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: bold; font-family: sans-serif; margin-left: 10px;">Cetak Buku</button>
            <a href="/admin/finance" style="padding: 8px 16px; background: #64748b; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: bold; font-family: sans-serif; margin-left: 10px; text-decoration: none;">Kembali</a>
        </div>

        <?php if(empty($kelas_id)): ?>
            <div class="no-print" style="text-align: center; padding: 50px; font-family: sans-serif; font-size: 18px; color: #64748b;">
                Silakan pilih kelas terlebih dahulu melalui menu dropdown di atas.
            </div>
        <?php else: ?>
        
        <div class="month-page">
            <div class="kop-surat">
                <?php if(!empty($inst['logo'])): ?>
                    <img src="/public/uploads/logo/<?php echo htmlspecialchars($inst['logo']); ?>" alt="Logo">
                <?php else: ?>
                    <div style="width: 70px;"></div>
                <?php endif; ?>
                <div class="kop-text">
                    <h1>YAYASAN PONDOK PESANTREN MIFTAHUL HUDA MATHLA'UL ANWAR</h1>
                    <h2>MADRASAH IBTIDAIYAH MIFTAHUL HUDA</h2>
                    <p><?php echo htmlspecialchars($inst['alamat'] ?? ''); ?>, Kec. <?php echo htmlspecialchars($inst['kecamatan'] ?? ''); ?>, <?php echo htmlspecialchars($inst['kota'] ?? ''); ?> <?php echo htmlspecialchars($inst['kodepos'] ?? ''); ?></p>
                </div>
                <div style="width: 70px;"></div>
            </div>

            <div class="header">
                <h2>BUKU KONTROL PEMBAYARAN KEUANGAN</h2>
                <div style="font-size: 14px; margin-top: 5px; font-weight: bold;">Kategori: <?php echo $kategori ? htmlspecialchars($kategori) : 'SEMUA KATEGORI'; ?></div>
            </div>
            
            <table class="info-table">
                <tr>
                    <td width="40">Kelas</td>
                    <td width="10">:</td>
                    <td><?php echo htmlspecialchars($nama_kelas); ?></td>
                    <!-- Bulan dihapus karena ini buku kontrol tahunan -->
                </tr>
            </table>
            
            <div class="table-wrapper">
                <table class="absen-table">
                    <thead>
                        <tr>
                            <th rowspan="2" width="30">No</th>
                            <th rowspan="2" width="60">NIS</th>
                            <th rowspan="2">Nama Siswa</th>
                            <th rowspan="2" width="30">Ket</th>
                            <th colspan="4">Angsuran / Pembayaran</th>
                            <th rowspan="2" width="10%">Lunas / Paraf</th>
                        </tr>
                        <tr>
                            <th width="14%">1</th>
                            <th width="14%">2</th>
                            <th width="14%">3</th>
                            <th width="14%">4</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($siswaList)): ?>
                        <tr>
                            <td colspan="10" style="padding: 15px;">Tidak ada data siswa di kelas ini.</td>
                        </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($siswaList as $s): 
                                $payments = isset($s['pembayaran']) ? $s['pembayaran'] : [];
                            ?>
                            <tr>
                                <td rowspan="2"><?php echo $no++; ?></td>
                                <td rowspan="2"><?php echo htmlspecialchars($s['nis']); ?></td>
                                <td class="nama" rowspan="2"><?php echo htmlspecialchars($s['nama']); ?></td>
                                <td style="font-size: 9px; font-weight: bold; color: #555;">Tgl</td>
                                <td><?php echo isset($payments[0]) ? date('d/m/y', strtotime($payments[0]['tanggal_bayar'])) : ''; ?></td>
                                <td><?php echo isset($payments[1]) ? date('d/m/y', strtotime($payments[1]['tanggal_bayar'])) : ''; ?></td>
                                <td><?php echo isset($payments[2]) ? date('d/m/y', strtotime($payments[2]['tanggal_bayar'])) : ''; ?></td>
                                <td><?php echo isset($payments[3]) ? date('d/m/y', strtotime($payments[3]['tanggal_bayar'])) : ''; ?></td>
                                <td rowspan="2"></td>
                            </tr>
                            <tr>
                                <td style="font-size: 9px; font-weight: bold; color: #555;">Rp</td>
                                <td><?php echo isset($payments[0]) ? number_format($payments[0]['jumlah'], 0, ',', '.') : ''; ?></td>
                                <td><?php echo isset($payments[1]) ? number_format($payments[1]['jumlah'], 0, ',', '.') : ''; ?></td>
                                <td><?php echo isset($payments[2]) ? number_format($payments[2]['jumlah'], 0, ',', '.') : ''; ?></td>
                                <td><?php echo isset($payments[3]) ? number_format($payments[3]['jumlah'], 0, ',', '.') : ''; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            
        </div>
        
        <?php endif; ?>
    </div>
    <script>
        function changeFilter() {
            var kls = document.getElementById('kelasFilter').value;
            var kat = document.getElementById('kategoriFilter').value;
            var tipe = document.getElementById('tipeCetak').value;
            if (kls) {
                window.location.href = '?kelas_id=' + kls + '&kategori=' + encodeURIComponent(kat) + '&tipe=' + tipe;
            }
        }
    </script>
</body>
</html>
