<?php
// Resolve the authorized bendahara name dynamically
$resolvedBendahara = '';
$resolvedNip = '';
$dbConnection = isset($db) ? $db : (isset($core_db) ? $core_db : null);
if ($dbConnection && isset($p['jenis_pembayaran'])) {
    try {
        // 1. Coba ambil dari petugas pemroses transaksi (paling akurat)
        if (!empty($p['petugas_id'])) {
            $stmtPetugas = $dbConnection->prepare("
                SELECT u.nama as u_nama, g.nama as g_nama, u.role_id 
                FROM users u 
                LEFT JOIN guru g ON u.id = g.user_id 
                WHERE u.id = ?
            ");
            $stmtPetugas->execute([$p['petugas_id']]);
            $ptg = $stmtPetugas->fetch(\PDO::FETCH_ASSOC);
            if ($ptg && $ptg['role_id'] == 2) {
                if (!empty($ptg['u_nama'])) {
                    $resolvedBendahara = $ptg['u_nama'];
                } elseif (!empty($ptg['g_nama'])) {
                    $resolvedBendahara = $ptg['g_nama'];
                }
            }
        }

        // Jika kosong (karena via Web/Admin atau petugas tidak valid) -> Gunakan Bendahara Umum
        if (empty($resolvedBendahara)) {
            $resolvedBendahara = $institusi['bendahara_nama'] ?? 'Bendahara Umum';
        }
    } catch (\Exception $e) {
        // Fallback jika query gagal
    }
}

if (!empty($resolvedBendahara)) {
    $adminNama = $resolvedBendahara;
} elseif (empty($adminNama)) {
    $adminNama = $institusi['bendahara_nama'] ?? 'Bendahara Umum';
}


function formatTanggalIndonesia($tanggal) {
    if (empty($tanggal) || $tanggal === '0000-00-00') return '-';
    
    $bulanIndo = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($tanggal);
    $tgl = date('d', $timestamp);
    $bln = (int)date('m', $timestamp);
    $thn = date('Y', $timestamp);
    
    return $tgl . ' ' . $bulanIndo[$bln] . ' ' . $thn;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - <?php echo htmlspecialchars($p['nama']); ?></title>
    <link rel="icon" type="image/png" href="/public/uploads/logo/<?= $institusi['logo'] ?>">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@500;600;700;800&display=swap');

        body {
            background-color: #f3f4f6;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #1f2937;
            margin: 0;
            padding: 30px;
        }

        /* Container of receipt */
        .receipt-container {
            width: 880px;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
        }

        .receipt-paper-wrapper {
            padding: 15px;
            background: #ffffff;
            border-radius: 8px;
            box-sizing: border-box;
            display: block;
            width: 100%;
        }

        /* The actual printable physical receipt sheet */
        .receipt-paper {
            width: 100%;
            min-height: 220px;
            border: 3px double #0f172a;
            border-radius: 6px;
            box-sizing: border-box;
            display: flex;
            position: relative;
            background: #fafcfc;
            background-image: linear-gradient(135deg, rgba(230, 242, 245, 0.25) 0%, rgba(255, 255, 255, 0.5) 100%);
            overflow: hidden;
        }

        /* Main Receipt */
        .receipt-main {
            flex: 1;
            padding: 12px 18px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        /* Header & Kop */
        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
        }
        .receipt-school {
            display: flex;
            align-items: center;
            max-width: 75%;
        }
        .receipt-logo {
            width: 45px;
            height: 45px;
            margin-right: 10px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .receipt-school-details {
            line-height: 1.3;
        }
        .receipt-school-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 15px;
            font-weight: 800;
            margin: 0;
            color: #0f172a;
            letter-spacing: 0.2px;
        }
        .receipt-school-addr {
            font-size: 9px;
            margin: 3px 0 0 0;
            color: #475569;
        }
        .receipt-meta {
            text-align: right;
            font-size: 12px;
        }
        .receipt-no {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            font-size: 15px;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 4px 10px;
            background: #fff;
            border-radius: 4px;
        }

        /* Title */
        .receipt-title-container {
            text-align: center;
            margin: 4px 0;
        }
        .receipt-title {
            font-family: 'Georgia', serif;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            border-bottom: 2px solid #0f172a;
            display: inline-block;
            padding: 0 15px 2px 15px;
            margin: 0;
            color: #0f172a;
        }

        /* Receipt Rows */
        .receipt-body {
            margin: 6px 0 15px 0;
        }
        .receipt-row {
            display: flex;
            align-items: flex-end;
            margin-bottom: 5px;
        }
        .receipt-label {
            width: 150px;
            font-weight: 700;
            font-size: 11px;
            color: #334155;
            flex-shrink: 0;
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .receipt-colon {
            width: 15px;
            font-weight: bold;
            flex-shrink: 0;
            color: #0f172a;
        }
        .receipt-value-container {
            flex: 1;
            border-bottom: 1.5px dotted #64748b;
            padding-bottom: 1px;
            min-height: 18px;
            display: flex;
            align-items: flex-end;
        }
        .receipt-value {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            font-size: 13px;
            color: #0f172a;
            line-height: 1.2;
        }

        /* Footer Section */
        .receipt-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
            position: relative;
        }
        .receipt-amount-box {
            border: 3px double #0f172a;
            padding: 6px 15px;
            font-size: 16px;
            font-weight: 800;
            background: #ffffff;
            font-family: 'Courier New', Courier, monospace;
            color: #0f172a;
            box-shadow: 3px 3px 0px rgba(15, 23, 42, 0.15);
            display: inline-block;
            z-index: 2;
        }

        .receipt-signatures {
            display: flex;
            gap: 40px;
            font-size: 11px;
            z-index: 2;
        }
        .receipt-sig-col {
            text-align: center;
            width: 140px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 60px;
        }
        .receipt-sig-title {
            margin: 0;
            color: #475569;
            font-weight: 600;
        }
        .receipt-sig-name {
            margin: 0;
            font-weight: 700;
            color: #0f172a;
        }

        /* Stamp Lunas styling - Placed in the spacious bottom-middle area */
        .receipt-stamp {
            position: absolute;
            bottom: 15px;
            left: 280px;
            transform: rotate(-10deg);
            border: 3px solid rgba(22, 163, 74, 0.65);
            color: rgba(22, 163, 74, 0.65);
            font-weight: 800;
            font-size: 26px;
            padding: 8px 24px;
            text-transform: uppercase;
            border-radius: 8px;
            letter-spacing: 5px;
            pointer-events: none;
            background-color: rgba(250, 252, 252, 0.85);
            box-shadow: 0 0 12px rgba(22, 163, 74, 0.05);
            font-family: 'Impact', 'Arial Black', sans-serif;
            z-index: 10;
            transition: all 0.3s ease;
        }
        .receipt-stamp.belum-lunas {
            border-color: rgba(37, 99, 235, 0.65);
            color: rgba(37, 99, 235, 0.65);
            letter-spacing: 3px;
        }

        /* Action Buttons styling */
        .action-buttons {
            margin-bottom: 25px;
            display: flex;
            justify-content: center;
            gap: 12px;
        }
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            cursor: pointer;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2), 0 2px 4px -2px rgba(37, 99, 235, 0.2);
            transition: all 0.2s ease;
        }
        .btn-print:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3), 0 4px 6px -4px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
        }
        .btn-download {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            cursor: pointer;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2), 0 2px 4px -2px rgba(16, 185, 129, 0.2);
            transition: all 0.2s ease;
        }
        .btn-download:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3), 0 4px 6px -4px rgba(16, 185, 129, 0.3);
            background: linear-gradient(135deg, #059669, #047857);
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            cursor: pointer;
            background: #ffffff;
            color: #4b5563;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-back:hover {
            background: #f9fafb;
            color: #1f2937;
            border-color: #9ca3af;
        }

        /* Cut Line Divider */
        .cut-line {
            margin: 35px 0;
            border-top: 2px dashed #cbd5e1;
            position: relative;
            text-align: center;
        }
        .cut-line::after {
            content: '✂ POTONG DI SINI ✂';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: #ffffff;
            padding: 0 15px;
            font-size: 10px;
            color: #94a3b8;
            font-weight: 800;
            letter-spacing: 1.5px;
        }

        /* Supplementary Info section */
        .receipt-supplementary {
            padding: 0 10px;
        }
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 20px;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);
        }
        .info-title {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 13px;
            font-weight: bold;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-title svg {
            color: #3b82f6;
            flex-shrink: 0;
        }

        /* Table styling */
        .rekap-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-top: 10px;
        }
        .rekap-table th {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            text-align: left;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        .rekap-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            color: #475569;
        }

        /* Printing logic */
        @media print {
            @page {
                size: 210mm auto;
                margin: 5mm;
            }
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }
            .z-sidebar, .z-header, .page-header, .btn-download, .no-print, .action-buttons {
                display: none !important;
            }
            .z-main {
                margin: 0 !important;
                padding: 0 !important;
            }
            .z-wrap {
                display: block !important;
            }
            .receipt-container {
                position: relative;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 auto !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
            }
            .receipt-paper-wrapper {
                padding: 0 !important;
                background: none !important;
            }
            .receipt-paper {
                border: 2px solid #000000;
                background: #ffffff !important;
                background-image: none !important;
                box-shadow: none;
                page-break-inside: avoid;
                height: auto !important;
                min-height: auto !important;
                box-sizing: border-box;
                max-width: 100%;
            }
            .receipt-value-container {
                border-bottom: 1.5px dotted #000000 !important;
            }
            .receipt-amount-box {
                border: 3px double #000000 !important;
                box-shadow: none;
            }
            .receipt-stamp {
                background: none !important;
                box-shadow: none;
            }
            .cut-line {
                border-top: 2px dashed #000000 !important;
            }
            .info-card {
                border: 1px solid #000000 !important;
                background: none !important;
                box-shadow: none;
            }
            .rekap-table th {
                background: none !important;
                border: 1px solid #000000 !important;
            }
            .rekap-table td {
                border: 1px solid #000000 !important;
            }
        }
        
        /* Responsive/Mobile/Modal styling */
        @media (max-width: 900px) {
            body {
                padding: 10px;
                background-color: transparent;
                overflow-x: hidden;
            }
            .receipt-container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 10px !important;
                box-shadow: none !important;
                border: none !important;
                background: transparent !important;
                overflow: hidden !important;
                margin: 0 auto !important;
                box-sizing: border-box !important;
            }
            .receipt-paper-wrapper {
                padding: 0 !important;
                background: none !important;
            }
            .receipt-paper {
                width: 834px !important; /* Fixed natural layout width */
                flex-shrink: 0 !important;
                transform-origin: top left;
                box-sizing: border-box !important;
            }
        }
    </style>
</head>
<body>

<?php if (!isset($_GET['modal'])): ?>
<div class="no-print action-buttons">
    <?php if (empty($is_apk)): ?>
        <button onclick="window.print()" class="btn-print">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 9V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v5"/><rect width="12" height="8" x="6" y="14" rx="1" ry="1"/></svg>
            CETAK SEKARANG
        </button>
        <button onclick="downloadKwitansi()" class="btn-download">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            UNDUH PNG
        </button>
    <?php endif; ?>
    <button onclick="window.history.back()" class="btn-back">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
        KEMBALI
    </button>
</div>

<?php if (!empty($is_apk)): ?>
    <div class="no-print" style="text-align: center; margin-bottom: 16px; max-width: 880px; margin-left: auto; margin-right: auto; box-sizing: border-box; padding: 0 10px;">
        <button onclick="downloadKwitansi()" class="btn-download" style="width: 100%; max-width: 400px; font-size: 15px; padding: 13px 24px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            UNDUH KWITANSI (PNG)
        </button>
    </div>
<?php endif; ?>

<?php endif; ?>

<div class="receipt-container">
    <div class="receipt-paper-wrapper">
        <div class="receipt-paper">
        <!-- Main Receipt -->
        <div class="receipt-main">


            <!-- Header -->
            <div class="receipt-header">
                <div class="receipt-school">
                    <?php if(!empty($institusi['logo']) && file_exists(__DIR__.'/../../../public/uploads/logo/'.$institusi['logo'])): ?>
                        <img class="receipt-logo" src="/public/uploads/logo/<?php echo $institusi['logo']; ?>">
                    <?php else: ?>
                        <div class="receipt-logo" style="background: #eee; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: bold; border: 1px solid #ddd; border-radius: 4px;">LOGO</div>
                    <?php endif; ?>
                    <div class="receipt-school-details">
                        <h1 class="receipt-school-name"><?php echo isset($institusi['nama']) ? str_replace('MTS ', 'MTs ', strtoupper($institusi['nama'])) : 'MTs ROUDLOTUS SHOLIHIN'; ?></h1>
                        <p class="receipt-school-addr">
                            <?php echo isset($institusi['alamat']) ? $institusi['alamat'] : ''; ?>
                            <?php echo isset($institusi['desa']) ? ', '.$institusi['desa'] : ''; ?>
                            <?php echo isset($institusi['kecamatan']) ? ', Kec. '.$institusi['kecamatan'] : ''; ?>
                            <?php echo isset($institusi['kota']) ? ', '.$institusi['kota'] : ''; ?>
                            <?php echo isset($institusi['kodepos']) ? ' '.$institusi['kodepos'] : ''; ?><br>
                            Telp: <?php echo isset($institusi['telepon']) ? $institusi['telepon'] : '-'; ?> | Email: <?php echo isset($institusi['email']) ? $institusi['email'] : '-'; ?>
                        </p>
                    </div>
                </div>
                <div class="receipt-meta">
                    <div class="receipt-no">No: KW-<?php echo str_pad($p['id'], 5, '0', STR_PAD_LEFT); ?></div>
                </div>
            </div>

            <!-- Title -->
            <div class="receipt-title-container">
                <h2 class="receipt-title">Kwitansi Pembayaran</h2>
            </div>

            <!-- Fields -->
            <div class="receipt-body">
                <div class="receipt-row">
                    <span class="receipt-label">Telah terima dari</span>
                    <span class="receipt-colon">:</span>
                    <div class="receipt-value-container">
                        <span class="receipt-value"><?php echo $p['nama']; ?> (NIS: <?php echo $p['nis']; ?>) / Kelas: <?php echo $p['kelas']; ?></span>
                    </div>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Uang Sejumlah</span>
                    <span class="receipt-colon">:</span>
                    <div class="receipt-value-container">
                        <span class="receipt-value" style="font-style: italic; color: #1e3a8a; font-weight: bold;"><?php echo ucwords(terbilang($p['jumlah'])); ?> Rupiah</span>
                    </div>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Untuk Pembayaran</span>
                    <span class="receipt-colon">:</span>
                    <div class="receipt-value-container">
                        <span class="receipt-value"><?php echo $p['jenis_pembayaran']; ?></span>
                    </div>
                </div>
            </div>

            <!-- Footer of Receipt (Rp and Signatures) -->
            <div class="receipt-footer" style="display: flex; justify-content: space-between; align-items: flex-end;">
                <!-- Nominal and QR Code (Kiri) -->
                <div style="display: flex; align-items: center; gap: 15px;">
                    <?php 
                    $kw_id = isset($original_id) ? $original_id : $p['id'];
                    $salt = "M1ft4hulHud4_S3cur3";
                    $token = substr(md5($kw_id . $salt), 0, 10);
                    $baseUrl = "https://" . $_SERVER['HTTP_HOST'];
                    $kw_id_safe = str_replace(',', '-', $kw_id);
                    $qrData = rawurlencode($baseUrl . "/kwitansi/verifikasi/" . $kw_id_safe . "?token=" . $token);
                    ?>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=85x85&data=<?php echo $qrData; ?>" style="width: 85px; height: 85px; border: 1.5px solid #0f172a; padding: 3px; border-radius: 6px; background: #fff; display: block;" alt="QR Verification">
                    
                    <div class="receipt-amount-box">
                        Rp <?php echo number_format($p['jumlah'], 0, ',', '.'); ?>,-
                    </div>
                </div>
                
                <!-- Signature Text (Kanan) -->
                <div class="receipt-signatures" style="display: flex; flex-direction: column; justify-content: flex-start; text-align: center; min-width: 160px;">
                    <div>
                        <p style="margin: 0; font-size: 11px; font-weight: 700; color: #475569; line-height: 1.2;">Bendahara Madrasah,</p>
                        <span style="font-size: 9px; font-weight: normal; color: #64748b;"><?php echo formatTanggalIndonesia($p['tanggal_bayar']); ?></span>
                    </div>
                    <p style="margin: 0; font-size: 12px; font-weight: bold; color: #0f172a; margin-top: 20px; text-decoration: underline; text-underline-offset: 4px;">
                        <?php echo htmlspecialchars($adminNama, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<script src="/public/assets/js/html2canvas.min.js"></script>
<script>
function downloadKwitansi() {
    const btn = document.querySelector('.btn-download');
    const originalBtnHTML = btn.innerHTML;

<?php if (!empty($is_apk)): ?>
    btn.disabled = true;
    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Sedang Mengunduh...';

    // ── Inject <style> override — menang atas !important di media query ──
    // Inline style tidak bisa override media query !important,
    // tapi <style> yang diinject belakangan menang berdasarkan cascade order.
    const captureStyle = document.createElement('style');
    captureStyle.id = '__kwCapture';
    captureStyle.textContent = `
        .receipt-container {
            width: 920px !important;
            max-width: 920px !important;
            overflow: visible !important;
            height: auto !important;
            border: 1px solid #d1d5db !important;
            background: #ffffff !important;
            padding: 20px !important;
        }
        .receipt-paper-wrapper {
            padding: 15px !important;
            background: #ffffff !important;
        }
        .receipt-paper {
            transform: none !important;
            margin-left: 0 !important;
            width: 100% !important;
        }
        body { overflow-x: visible !important; }
    `;
    document.head.appendChild(captureStyle);

    // ── Sembunyikan gambar cross-origin (QR) agar canvas tidak tainted ──
    const crossImgs = Array.from(document.querySelectorAll('img')).filter(function(img) {
        const src = img.getAttribute('src') || '';
        return src.startsWith('http') && !src.includes(window.location.hostname);
    });
    crossImgs.forEach(function(img) { img.style.visibility = 'hidden'; });

    function _restoreState() {
        const el = document.getElementById('__kwCapture');
        if (el) el.remove();
        crossImgs.forEach(function(img) { img.style.visibility = ''; });
        if (typeof adjustMobileScale === 'function') adjustMobileScale();
    }

    // Tunggu 250ms agar browser re-paint setelah style di-inject
    setTimeout(function() {
        const target = document.querySelector('.receipt-paper-wrapper')
                    || document.querySelector('.receipt-container');

        html2canvas(target, {
            scale:        1.5,
            useCORS:      true,
            backgroundColor: '#ffffff',
            windowWidth:  920,
            windowHeight: Math.max(target.scrollHeight, 450)
        }).then(function(canvas) {
            _restoreState();

            const dataUrl = canvas.toDataURL('image/png');
            if (!dataUrl || dataUrl === 'data:,') {
                btn.disabled = false;
                btn.innerHTML = originalBtnHTML;
                alert('Gagal membuat gambar. Silakan coba lagi.');
                return;
            }

            const formData = new FormData();
            formData.append('id',    '<?php echo $p['id']; ?>');
            formData.append('image', dataUrl);

            fetch('/apk/save_kwitansi.php', { method: 'POST', body: formData })
            .then(function(res)  { return res.json(); })
            .then(function(data) {
                btn.disabled = false;
                btn.innerHTML = originalBtnHTML;
                if (data.status === 'success') {
                    window.location.href = '/apk/download_kwitansi.php?file='
                        + encodeURIComponent(data.filename)
                        + '&token=' + encodeURIComponent(data.token);
                } else {
                    alert('Gagal mengunduh kwitansi: ' + data.message);
                }
            })
            .catch(function(err) {
                btn.disabled = false;
                btn.innerHTML = originalBtnHTML;
                console.error('AJAX Error:', err);
                alert('Terjadi kesalahan jaringan saat menyimpan kwitansi.');
            });

        }).catch(function(err) {
            _restoreState();
            btn.disabled = false;
            btn.innerHTML = originalBtnHTML;
            console.error('html2canvas error:', err);
            alert('Gagal men-generate gambar kwitansi. Error: ' + err.message);
        });

    }, 250);

<?php else: ?>
    const paper = document.querySelector('.receipt-paper-wrapper');
    html2canvas(paper, {
        scale: 2,
        useCORS: true,
        backgroundColor: '#ffffff'
    }).then(function(canvas) {
        const dataUrl = canvas.toDataURL('image/png');
        const link = document.createElement('a');
        link.download = 'Kwitansi_KW-<?php echo str_pad($p['id'], 5, '0', STR_PAD_LEFT); ?>_<?php echo addslashes($p['nama']); ?>.png';
        link.href = dataUrl;
        link.click();
    }).catch(function(err) {
        console.error('Gagal men-generate kwitansi:', err);
        alert('Gagal men-generate gambar kwitansi. Error: ' + err.message);
    });
<?php endif; ?>
}
</script>


<?php
function terbilang($angka) {
    $angka = abs($angka);
    $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $terbilang = "";
    if ($angka < 12) {
        $terbilang = " " . $baca[$angka];
    } else if ($angka < 20) {
        $terbilang = terbilang($angka - 10) . " belas";
    } else if ($angka < 100) {
        $terbilang = terbilang($angka / 10) . " puluh" . terbilang($angka % 10);
    } else if ($angka < 200) {
        $terbilang = " seratus" . terbilang($angka - 100);
    } else if ($angka < 1000) {
        $terbilang = terbilang($angka / 100) . " ratus" . terbilang($angka % 100);
    } else if ($angka < 2000) {
        $terbilang = " seribu" . terbilang($angka - 1000);
    } else if ($angka < 1000000) {
        $terbilang = terbilang($angka / 1000) . " ribu" . terbilang($angka % 1000);
    } else if ($angka < 1000000000) {
        $terbilang = terbilang($angka / 1000000) . " juta" . terbilang($angka % 1000000);
    }
    return $terbilang;
}
?>

<script>
let rotationAngle = <?php echo !empty($is_apk) ? '0' : '0'; ?>; // 0, 90, 180, 270
let zoomFactor = 1.0;

window.setZoomAndRotation = function(zoom, rotation) {
    zoomFactor = zoom;
    rotationAngle = rotation;
    adjustMobileScale();
}

function adjustMobileScale() {
    const paper = document.querySelector('.receipt-paper');
    if (!paper) return;
    const container = document.querySelector('.receipt-container');
    if (!container) return;
    
    if (window.innerWidth > 900 && rotationAngle === 0 && zoomFactor === 1.0) {
        paper.style.transform = 'none';
        paper.style.marginLeft = '0';
        container.style.height = 'auto';
        return;
    }
    
    const W = 834; // Lock layout original width
    const H = 380; // Lock layout original height
    
    // Width of container minus its padding (10px left + 10px right = 20px)
    const containerWidth = container.clientWidth - 20;
    
    // Calculate base scale depending on rotation orientation
    let baseScale = 1.0;
    if (rotationAngle === 90 || rotationAngle === 270) {
        baseScale = containerWidth / H;
    } else {
        baseScale = containerWidth / W;
    }
    
    // Cap base scale on desktop viewport to avoid huge sizes
    if (window.innerWidth > 900 && baseScale > 1.0) {
        baseScale = 1.0;
    }
    
    const finalScale = baseScale * zoomFactor;
    paper.style.transformOrigin = 'top left';
    
    let visualWidth = 0;
    let visualHeight = 0;
    let transformStr = '';
    let offset = 0;
    
    if (rotationAngle === 0) {
        visualWidth = W * finalScale;
        visualHeight = H * finalScale;
        offset = (containerWidth - visualWidth) / 2;
        if (offset < 0) offset = 0;
        transformStr = `translateX(${offset}px) rotate(0deg) scale(${finalScale})`;
    } else if (rotationAngle === 90) {
        visualWidth = H * finalScale;
        visualHeight = W * finalScale;
        offset = (containerWidth - visualWidth) / 2;
        if (offset < 0) offset = 0;
        transformStr = `translateX(${(H * finalScale) + offset}px) rotate(90deg) scale(${finalScale})`;
    } else if (rotationAngle === 180) {
        visualWidth = W * finalScale;
        visualHeight = H * finalScale;
        offset = (containerWidth - visualWidth) / 2;
        if (offset < 0) offset = 0;
        transformStr = `translate(${(W * finalScale) + offset}px, ${H * finalScale}px) rotate(180deg) scale(${finalScale})`;
    } else if (rotationAngle === 270) {
        visualWidth = H * finalScale;
        visualHeight = W * finalScale;
        offset = (containerWidth - visualWidth) / 2;
        if (offset < 0) offset = 0;
        transformStr = `translate(${offset}px, ${W * finalScale}px) rotate(270deg) scale(${finalScale})`;
    }
    
    paper.style.transform = transformStr;
    paper.style.marginLeft = '0';
    
    // Adjust container height to fit the visual scaled height
    container.style.height = (visualHeight + 20) + 'px';
}

window.addEventListener('resize', adjustMobileScale);
window.addEventListener('load', adjustMobileScale);
document.addEventListener('DOMContentLoaded', adjustMobileScale);
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    adjustMobileScale();
}
</script>
</body>
</html>
