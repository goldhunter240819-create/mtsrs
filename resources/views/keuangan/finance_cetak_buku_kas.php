<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Buku Kas Umum Keuangan</title>
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
        
        .header { text-align: center; margin-bottom: 20px; margin-top: 10px; }
        
        @media print {
            @page { size: landscape; margin: 10mm; } 
            body { -webkit-print-color-adjust: exact; margin: 0; background: white; }
            .no-print { display: none !important; }
            html, body { height: 100%; }
            .page-container {
                page-break-after: always;
                page-break-inside: avoid;
                height: 190mm; /* A4 landscape height minus 10mm margins */
                display: flex;
                flex-direction: column;
            }
            .page-container:last-child {
                page-break-after: auto;
            }
        }
        
        @media screen {
            .page-container {
                background: white;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                margin: 0 auto 30px;
                padding: 20px;
                height: 190mm; /* Match print height exactly */
                display: flex;
                flex-direction: column;
            }
        }

        .filter-box { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #cbd5e1; display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: center; }
        
        .table-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }
        .buku-table {
            width: 100%;
            height: 100%; /* Make table stretch to fill remaining space */
            border-collapse: collapse;
        }
        
        .buku-table th, .buku-table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 12px;
        }
        .buku-table th { background: #f3f4f6; font-weight: bold; text-align: center; }
        
        .input-inline {
            border: none;
            border-bottom: 1px dotted #000;
            outline: none;
            font-family: inherit;
            font-size: 13px;
            font-weight: bold;
            width: 150px;
            background: transparent;
        }
    </style>
</head>
<body>
    <div class="print-container">
        
        <div class="no-print filter-box">
            <span style="font-family: sans-serif; font-size: 14px; font-weight: bold;">Kategori:</span>
            <select id="kategoriFilter" onchange="changeFilter()" style="padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;">
                <option value="">Semua Kategori</option>
                <?php if(isset($kategoriList)): foreach($kategoriList as $k): ?>
                <option value="<?php echo htmlspecialchars($k); ?>" <?php echo (isset($_GET['kategori']) && $_GET['kategori'] == $k) ? 'selected' : ''; ?>><?php echo htmlspecialchars($k); ?></option>
                <?php endforeach; endif; ?>
            </select>
            
            <span style="font-family: sans-serif; font-size: 14px; font-weight: bold; margin-left:15px;">Tipe Cetak:</span>
            <select id="tipeCetak" onchange="changeFilter()" style="padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;">
                <option value="kosong" <?php echo (!isset($_GET['tipe']) || $_GET['tipe'] == 'kosong') ? 'selected' : ''; ?>>Kosongan (Manual)</option>
                <option value="data" <?php echo (isset($_GET['tipe']) && $_GET['tipe'] == 'data') ? 'selected' : ''; ?>>Berisi Data Transaksi</option>
            </select>
            
            <span style="font-family: sans-serif; font-size: 14px; font-weight: bold; margin-left:15px;">Dari:</span>
            <input type="date" id="startDate" onchange="changeFilter()" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>" style="padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;">
            
            <span style="font-family: sans-serif; font-size: 14px; font-weight: bold; margin-left:15px;">Sampai:</span>
            <input type="date" id="endDate" onchange="changeFilter()" value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>" style="padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;">

            <span style="font-family: sans-serif; font-size: 14px; font-weight: bold; margin-left:15px;">Jml Baris:</span>
            <input type="number" id="rowCount" value="18" min="0" max="50" onchange="renderRows()" onkeyup="renderRows()" style="padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; width: 60px; font-size: 14px; text-align: center;">
            <button onclick="window.print()" style="padding: 8px 16px; background: #059669; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: bold; font-family: sans-serif; margin-left: 10px;">Cetak Halaman</button>
            <a href="/admin/finance" style="padding: 8px 16px; background: #64748b; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: bold; font-family: sans-serif; margin-left: 10px; text-decoration: none;">Kembali</a>
        </div>

        <div class="page-container">
            
            <div class="table-wrapper">
                <table class="buku-table">
                    <thead>
                        <tr>
                            <th width="30">No</th>
                            <th width="80">Tanggal</th>
                            <th>Uraian / Keterangan</th>
                            <th width="120">Penerimaan (Rp)</th>
                            <th width="120">Pengeluaran (Rp)</th>
                            <th width="120">Saldo (Rp)</th>
                            <th width="120">Ket / Bukti Kas</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if(isset($tipe) && $tipe === 'data'): 
                            $no = 1;
                            $saldo = isset($saldo_awal) ? $saldo_awal : 0;
                            
                            // Baris Saldo Awal (Pindahan)
                            if (isset($saldo_awal) && isset($_GET['start_date']) && !empty($_GET['start_date'])):
                        ?>
                        <tr class="data-row" style="font-weight: bold; background: #fdfdfd;">
                            <td align="center">-</td>
                            <td>-</td>
                            <td>SALDO PINDAHAN (Sebelum <?php echo date('d/m/Y', strtotime($_GET['start_date'])); ?>)</td>
                            <td align="right">-</td>
                            <td align="right">-</td>
                            <td align="right"><?php echo number_format($saldo, 0, ',', '.'); ?></td>
                            <td></td>
                        </tr>
                        <?php endif; ?>

                        <?php if(!empty($transaksi)): foreach($transaksi as $t): 
                                $pemasukan = $t['jenis'] === 'Pemasukan' ? $t['jumlah'] : 0;
                                $pengeluaran = $t['jenis'] === 'Pengeluaran' ? $t['jumlah'] : 0;
                                $saldo = $saldo + $pemasukan - $pengeluaran;
                        ?>
                        <tr class="data-row">
                            <td align="center"><?php echo $no++; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($t['tanggal'])); ?></td>
                            <td><?php echo htmlspecialchars($t['keterangan']); ?></td>
                            <td align="right"><?php echo $pemasukan > 0 ? number_format($pemasukan, 0, ',', '.') : '-'; ?></td>
                            <td align="right"><?php echo $pengeluaran > 0 ? number_format($pengeluaran, 0, ',', '.') : '-'; ?></td>
                            <td align="right"><?php echo number_format($saldo, 0, ',', '.'); ?></td>
                            <td></td>
                        </tr>
                        <?php endforeach; endif; ?>
                        <?php endif; ?>
                        <!-- Blank Rows will be generated by JS -->
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
    
    <script>
        function renderRows() {
            var tbody = document.getElementById('tableBody');
            // Retain existing data rows
            var dataRows = '';
            var existingData = tbody.querySelectorAll('.data-row');
            existingData.forEach(function(row) {
                dataRows += row.outerHTML;
            });
            
            var count = parseInt(document.getElementById('rowCount').value) || 0;
            var html = dataRows;
            for (var i = 0; i < count; i++) {
                html += '<tr>' +
                        '<td align="center"></td>' +
                        '<td></td>' +
                        '<td></td>' +
                        '<td></td>' +
                        '<td></td>' +
                        '<td></td>' +
                        '<td></td>' +
                        '</tr>';
            }
            tbody.innerHTML = html;
        }
        
        function changeFilter() {
            var tipe = document.getElementById('tipeCetak').value;
            var kat = document.getElementById('kategoriFilter').value;
            var start = document.getElementById('startDate').value;
            var end = document.getElementById('endDate').value;
            
            var url = '?tipe=' + tipe + '&kategori=' + encodeURIComponent(kat);
            if (start) {
                url += '&start_date=' + encodeURIComponent(start);
            }
            if (end) {
                url += '&end_date=' + encodeURIComponent(end);
            }
            
            window.location.href = url;
        }
        
        // Initial render
        renderRows();
    </script>
</body>
</html>
