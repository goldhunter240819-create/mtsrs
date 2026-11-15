<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Identitas Guru | SIAKAD</title>
    <link rel="icon" href="<?php echo \App\Core\Helper::url('/public/uploads/logo/' . (isset($inst['logo']) ? $inst['logo'] : '')); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="<?php echo \App\Core\Helper::url('/public/assets/js/lucide.min.js'); ?>"></script>
    <script src="<?php echo \App\Core\Helper::url('/public/assets/js/html2canvas.min.js'); ?>"></script>
    <script src="<?php echo \App\Core\Helper::url('/public/assets/js/jszip.min.js'); ?>"></script>
    <script>
        if (typeof html2canvas === 'undefined') {
            document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"><\/script>');
        }
        if (typeof JSZip === 'undefined') {
            document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"><\/script>');
        }
    </script>
    <style>
        :root {
            --emerald-900: #064e3b;
            --emerald-800: #065f46;
            --emerald-700: #047857;
            --gold-500: #f59e0b;
            --gold-400: #fbbf24;
            --gold-300: #fcd34d;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        * { box-sizing: border-box; }

        @media print {
            @page { size: 300mm 200mm; margin: 5mm 2mm 5mm 8mm; }
            body { background: white !important; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .container { 
                padding: 0 !important; column-gap: 3mm !important; row-gap: 5mm !important; max-width: none !important;
                display: grid !important; width: 100% !important; justify-content: center !important;
            }
            .card { 
                break-inside: avoid; margin: 0 !important;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
                box-shadow: none !important;
                border: 0.5pt solid #ccc !important;
            }
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            margin: 0; padding: 40px 20px;
            display: flex; flex-direction: column; align-items: center;
        }

        .container {
            display: grid; column-gap: 3px; row-gap: 5mm; width: 100%; max-width: 900px; justify-content: center;
        }

        .card {
            box-sizing: border-box;
            border-radius: 3mm;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: relative;
            display: flex;
            flex-direction: column;
            background-color: var(--emerald-900);
            /* Premium Abstract Background SVG */
            background-image: radial-gradient(circle at 15% 50%, rgba(251, 191, 36, 0.08), transparent 25%),
                              radial-gradient(circle at 85% 30%, rgba(255, 255, 255, 0.05), transparent 25%),
                              url("data:image/svg+xml,%3Csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Cpattern id='p' width='100' height='100' patternUnits='userSpaceOnUse'%3E%3Cpath d='M0 100 V 50 Q 25 25 50 50 T 100 50 V 100 z' fill='rgba(255,255,255,0.02)'/%3E%3Cpath d='M0 100 V 20 Q 25 -5 50 20 T 100 20 V 100 z' fill='rgba(0,0,0,0.1)'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width='100%25' height='100%25' fill='url(%23p)'/%3E%3C/svg%3E");
            background-size: cover;
            background-position: center;
            flex-shrink: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        <?php if ($mode === 'vertical'): ?>
        .card { width: 55mm; height: 87mm; }
        .container { grid-template-columns: repeat(5, 55mm); }
        @media print { .container { grid-template-columns: repeat(5, 55mm) !important; } }
        <?php else: ?>
        .card { width: 87mm; height: 55mm; }
        .container { grid-template-columns: repeat(2, 87mm); }
        @media print { .container { grid-template-columns: repeat(2, 87mm) !important; } }
        <?php endif; ?>

        /* Glassmorphism Panel */
        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-top: 1px solid var(--glass-border);
            border-radius: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 2;
        }

        /* Gold Accents */
        .gold-line {
            height: 1.5mm;
            background: linear-gradient(90deg, var(--gold-500), var(--gold-300), var(--gold-500));
            width: 100%;
            position: relative;
            z-index: 3;
        }

        .print-btn {
            background: var(--emerald-800); color: white; border: none;
            padding: 12px 25px; border-radius: 50px; font-weight: 700;
            cursor: pointer; box-shadow: 0 4px 14px 0 rgba(6, 95, 70, 0.4);
            transition: all 0.3s; text-transform: uppercase;
            letter-spacing: 1px; display: flex; align-items: center; gap: 10px;
        }
        .print-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(6, 95, 70, 0.6); }

        .switch-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); }
        .download-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(2, 132, 199, 0.5); }

        .card { position: relative; }
        .card:hover .card-quick-dl { opacity: 1; pointer-events: auto; }
        .card-quick-dl {
            position: absolute;
            top: 5px;
            right: 5px;
            display: flex;
            gap: 3px;
            z-index: 100;
            opacity: 0;
            transition: opacity 0.2s ease;
            pointer-events: none;
        }
        .card-back[style*="rotate(180deg)"] .card-quick-dl {
            transform: rotate(180deg);
            top: auto;
            right: auto;
            bottom: 5px;
            left: 5px;
        }
        .card-quick-dl button {
            background: rgba(15, 23, 42, 0.85);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 4px;
            padding: 3px 6px;
            font-size: 8px;
            font-weight: 800;
            letter-spacing: 0.5px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.25);
            backdrop-filter: blur(4px);
            transition: all 0.15s ease;
        }
        .card-quick-dl button:hover {
            background: #f59e0b;
            color: #0f172a;
            border-color: #fbbf24;
            transform: scale(1.08);
        }
        @keyframes popIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

    </style>
</head>
<body>
    <!-- TOOLBAR -->
    <div class="no-print" style="max-width: <?php echo $mode === 'vertical' ? '750px' : '900px'; ?>; margin: 20px auto 30px auto; background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #cbd5e1; padding: 15px 20px; display: flex; flex-direction: column; gap: 15px;">
        
        <div style="display: flex; flex-direction: column; gap: 10px; flex: 1; min-width: 300px;">
            <div style="font-weight: 800; color: var(--emerald-900); font-size: 14px; display: flex; align-items: center; gap: 6px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Panel Cetak Kartu
            </div>
            
            <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                
                <div style="flex: 1; min-width: 160px;">
                    <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 5px;">Bagian yang Dicetak:</label>
                    <select id="printSideSelect" onchange="applyPrintSettings()" style="width: 100%; padding: 7px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-family: inherit; font-size: 11px; font-weight: 600; outline: none; color: #0f172a; cursor: pointer;">
                        <option value="both">Keduanya (Depan & Belakang)</option>
                        <option value="front">Halaman Depan Saja</option>
                        <option value="back">Halaman Belakang Saja</option>
                    </select>
                </div>
                
                <?php if ($mode === 'vertical'): ?>
                <div style="flex: 1; min-width: 180px; display: flex; align-items: flex-end; padding-bottom: 7px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 700; color: #475569; cursor: pointer; user-select: none;">
                        <input type="checkbox" id="showBackInfo" onchange="applyPrintSettings()" style="width: 14px; height: 14px; cursor: pointer;">
                        Tampilkan Info Guru di Belakang
                    </label>
                </div>
                <?php else: ?>
                <div style="flex: 1.5; min-width: 200px;">
                    <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 5px;">Tempat & Tanggal (Tanda Tangan):</label>
                    <div style="display: flex; gap: 5px;">
                        <input type="text" id="inputKota" value="<?php echo htmlspecialchars($inst['kota'] ?? 'Jakarta'); ?>" oninput="updateTtdText()" style="flex: 1; min-width: 0; width: 100%; padding: 7px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 11px; font-family: inherit; outline: none;">
                        <input type="text" id="inputTgl" value="<?php echo date('d M Y'); ?>" oninput="updateTtdText()" style="flex: 1; min-width: 0; width: 100%; padding: 7px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 11px; font-family: inherit; outline: none;">
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Kolom Kanan: Tombol -->
            <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end; padding-bottom: 1px;">
                <?php
                    $currentUrl = $_SERVER['REQUEST_URI'];
                    $isVertical = $mode === 'vertical';
                    $newMode = $isVertical ? 'horizontal' : 'vertical';
                    
                    if (strpos($currentUrl, 'mode=') !== false) {
                        $switchUrl = preg_replace('/mode=[^&]+/', 'mode=' . $newMode, $currentUrl);
                    } else {
                        $separator = strpos($currentUrl, '?') !== false ? '&' : '?';
                        $switchUrl = $currentUrl . $separator . 'mode=' . $newMode;
                    }
                    
                    $isBulkPost = ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($currentUrl, 'cetak-kartu-bulk') !== false);
                ?>
                <?php if ($isBulkPost): ?>
                    <form id="switchModeForm" action="<?php echo htmlspecialchars($switchUrl); ?>" method="POST" style="display: none;">
                        <?php 
                        if (!empty($_POST['ids'])) {
                            foreach((array)$_POST['ids'] as $id) {
                                echo '<input type="hidden" name="ids[]" value="' . htmlspecialchars($id) . '">';
                            }
                        }
                        ?>
                    </form>
                    <a href="#" onclick="document.getElementById('switchModeForm').submit(); return false;" class="switch-btn" style="background: white; color: var(--emerald-800); border: 2px solid var(--emerald-800); padding: 7px 15px; border-radius: 50px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 6px; transition: all 0.3s; font-size: 11px; text-transform: uppercase;">
                <?php else: ?>
                    <a href="<?php echo htmlspecialchars($switchUrl); ?>" class="switch-btn" style="background: white; color: var(--emerald-800); border: 2px solid var(--emerald-800); padding: 7px 15px; border-radius: 50px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 6px; transition: all 0.3s; font-size: 11px; text-transform: uppercase;">
                <?php endif; ?>
                    <?php if ($isVertical): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    <?php else: ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                    <?php endif; ?>
                    <?php echo $isVertical ? 'Versi Horizontal' : 'Versi Vertikal'; ?>
                </a>
                <button type="button" class="download-btn" onclick="openDownloadModal()" style="padding: 7px 15px; font-size: 11px; border: none; background: #0284c7; color: white; border-radius: 50px; cursor: pointer; display: flex; align-items: center; gap: 6px; text-transform: uppercase; font-weight: 700; transition: all 0.3s; box-shadow: 0 4px 14px 0 rgba(2, 132, 199, 0.4);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Download Gambar
                </button>
                <button class="print-btn" onclick="window.print()" style="padding: 7px 15px; font-size: 11px; border: none; background: var(--emerald-800); color: white; border-radius: 50px; cursor: pointer; display: flex; align-items: center; gap: 6px; text-transform: uppercase; font-weight: 700; transition: all 0.3s;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    Cetak Sekarang
                </button>
            </div>
        </div>
    </div>

    <div class="container">
        <?php 
        $bulanIndo = [1=>'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        if ($mode === 'vertical'):
            // Mode Vertikal: Kelompokkan per 5 guru (like Siswa)
            $chunks = array_chunk($gurus, 5);
            foreach($chunks as $group):
                // === BARIS KARTU DEPAN ===
                foreach($group as $g):
                    $foto = $g['foto'] ? \App\Core\Helper::url('/public/uploads/guru/' . $g['foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($g['nama']) . '&background=065f46&color=fff&font-size=0.33';
                    $instName = $inst ? $inst['nama'] : 'GH-SSS ACADEMY';
        ?>
        <!-- SISI DEPAN VERTIKAL -->
        <div class="card card-front" data-id="<?php echo $g['id']; ?>" data-name="<?php echo htmlspecialchars($g['nama']); ?>" data-nis="<?php echo htmlspecialchars($g['niy'] ?? ''); ?>" data-side="depan">
                <div class="card-quick-dl no-print">
                    <button type="button" onclick="event.stopPropagation(); downloadCardElement(this.closest('.card'), 'png')" title="Unduh Sisi Depan (PNG)">PNG</button>
                    <button type="button" onclick="event.stopPropagation(); downloadCardElement(this.closest('.card'), 'jpg')" title="Unduh Sisi Depan (JPG)">JPG</button>
                </div>
                <!-- VERTIKAL HEADER -->
                <div style="height: 19mm; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5mm; color: white; position: relative; z-index: 2;">
                    <?php if($inst && $inst['logo']): ?>
                        <img src="<?php echo \App\Core\Helper::url('/public/uploads/logo'); ?>/<?php echo $inst['logo']; ?>" style="width: 9mm; height: 9mm; object-fit: contain; margin-bottom: 0.8mm; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">
                    <?php else: ?>
                        <div style="width: 9mm; height: 9mm; display: flex; align-items: center; justify-content: center; margin-bottom: 0.8mm;"><i data-lucide="graduation-cap" style="width: 7mm; color: var(--gold-400);"></i></div>
                    <?php endif; ?>
                    <div style="text-align: center; line-height: 1.15;">
                        <div style="font-weight: 800; font-size: 10px; letter-spacing: 1px; color: var(--gold-400);">KARTU IDENTITAS GURU</div>
                        <div style="font-weight: 700; font-size: 8px; opacity: 0.95; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo $instName; ?></div>
                    </div>
                </div>
                
                <div class="gold-line"></div>

                <!-- VERTIKAL BODY (Glassmorphism) -->
                <div class="glass-panel" style="padding: 3mm; align-items: center; position: relative;">
                    <?php if($inst && $inst['logo']): ?>
                    <!-- Center Watermark -->
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 65%; height: 65%; background-image: url('<?php echo \App\Core\Helper::url('/public/uploads/logo/' . $inst['logo']); ?>'); background-size: contain; background-repeat: no-repeat; background-position: center; opacity: 0.12; pointer-events: none;"></div>
                    <?php endif; ?>
                    
                    <!-- Foto -->
                    <div style="width: 27mm; height: 36mm; border: 2px solid var(--gold-400); border-radius: 2mm; overflow: hidden; padding: 0.5mm; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 3.5mm; margin-top: 2.5mm; z-index: 5; position: relative;">
                        <img src="<?php echo $foto; ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 1.5mm;">
                    </div>
                    
                    <div style="text-align: center; width: 100%; position: relative; z-index: 5; margin-bottom: 1mm;">
                        <?php 
                            $nama_guru = htmlspecialchars($g['nama']);
                            $len = mb_strlen($nama_guru);
                            $fs = $len > 22 ? '10px' : ($len > 16 ? '12px' : '15px');
                        ?>
                        <div style="font-weight: 900; color: #0f172a; font-size: <?php echo $fs; ?>; line-height: 1.15; margin-bottom: 1.5mm;"><?php echo $nama_guru; ?></div>
                        <div style="font-size: 11px; color: #1e293b; font-weight: 800; letter-spacing: 0.5px;">NIY. <?php echo !empty($g['niy']) ? htmlspecialchars($g['niy']) : '-'; ?></div>
                    </div>
                </div>
        </div>
        <?php endforeach; ?>
        
        <?php 
            // PADDING UNTUK DEPAN (Agar belakang turun ke baris bawah)
            $emptyCount = 5 - count($group);
            for($i = 0; $i < $emptyCount; $i++) {
                echo '<div class="card-front" style="width: 54mm; height: 86mm; visibility: hidden; flex-shrink: 0;"></div>';
            }
        ?>

        <?php // === BARIS KARTU BELAKANG ===
            foreach($group as $g):
                $instName = $inst ? $inst['nama'] : 'GH-SSS ACADEMY';
                $qrDataGuru = $g['qr_token'] ?? 'NO-TOKEN';
                $qrUrlBack = 'https://quickchart.io/qr?size=350&margin=0&ecLevel=M&text=' . urlencode($qrDataGuru);
        ?>
        <!-- SISI BELAKANG VERTIKAL -->
        <div class="card card-back" data-id="<?php echo $g['id']; ?>" data-name="<?php echo htmlspecialchars($g['nama']); ?>" data-nis="<?php echo htmlspecialchars($g['niy'] ?? ''); ?>" data-side="belakang" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4mm; transform: rotate(180deg);">
            <div class="card-quick-dl no-print">
                <button type="button" onclick="event.stopPropagation(); downloadCardElement(this.closest('.card'), 'png')" title="Unduh Sisi Belakang (PNG)">PNG</button>
                <button type="button" onclick="event.stopPropagation(); downloadCardElement(this.closest('.card'), 'jpg')" title="Unduh Sisi Belakang (JPG)">JPG</button>
            </div>
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: var(--emerald-900); z-index: 1;"></div>
            <!-- Pattern Back -->
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(circle at 50% 50%, rgba(251, 191, 36, 0.1), transparent 50%); z-index: 2;"></div>
            
            <div style="width: 100%; text-align: center; margin-bottom: 4mm; z-index: 3;">
                <div style="font-weight: 800; font-size: 12px; letter-spacing: 1.5px; color: var(--gold-400);">AKSES DIGITAL</div>
                <div style="font-size: 7px; color: #cbd5e1; margin-top: 1mm; font-weight: 500; letter-spacing: 0.5px;">Pindai QR Code untuk verifikasi identitas</div>
            </div>
            
            <div style="background: white; padding: 2.5mm; border-radius: 2mm; border: 2px solid var(--gold-400); box-shadow: 0 10px 25px rgba(0,0,0,0.3); z-index: 3;">
                <img src="<?php echo $qrUrlBack; ?>" style="width: 27mm; height: 27mm; display: block;">
            </div>

            <div class="back-guru-info" style="display: none; margin-top: 3mm; text-align: center; background: white; padding: 1.5mm 3mm; border-radius: 2mm; border: 1px solid var(--gold-400); box-shadow: 0 2px 5px rgba(0,0,0,0.2); width: 90%; z-index: 3;">
                <div style="font-weight: 900; font-size: 8px; color: var(--emerald-900);"><?php echo htmlspecialchars($g['nama']); ?></div>
                <div style="font-size: 6px; color: #000; margin-top: 0.5mm; font-weight: 700;">NIY: <?php echo !empty($g['niy']) ? htmlspecialchars($g['niy']) : '-'; ?></div>
            </div>
            
            <div style="margin-top: 4mm; z-index: 3; text-align: center; color: #94a3b8; font-size: 5.5px; line-height: 1.5; padding: 0 4mm;">
                Kartu ini diterbitkan oleh <strong><?php echo $instName; ?></strong>.<br>
                Barang siapa menemukan kartu ini, harap mengembalikan ke Tata Usaha Madrasah.
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php 
            // PADDING UNTUK BELAKANG
            for($i = 0; $i < $emptyCount; $i++) {
                echo '<div class="card-back" style="width: 54mm; height: 86mm; visibility: hidden; flex-shrink: 0;"></div>';
            }
        ?>
        <?php endforeach; ?>
        
        <?php else: ?>
        <?php // Mode Horizontal: tetap depan-belakang per guru
            foreach($gurus as $g):
                $foto = $g['foto'] ? \App\Core\Helper::url('/public/uploads/guru/' . $g['foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($g['nama']) . '&background=065f46&color=fff&font-size=0.33';
                $tglCetak = date('d M Y');
                $kotaCetak = isset($inst['kota']) && $inst['kota'] ? $inst['kota'] : 'Jakarta';
                $namaKepala = isset($inst['nama_kepala']) && $inst['nama_kepala'] ? $inst['nama_kepala'] : 'Kepala Sekolah';
                $instName = $inst ? $inst['nama'] : 'GH-SSS ACADEMY';
                
                $qrDataGuru = $g['qr_token'] ?? 'NO-TOKEN';
                $qrUrlBack = 'https://quickchart.io/qr?size=350&margin=0&ecLevel=M&text=' . urlencode($qrDataGuru);

                $tgl_lahir_indo = '-';
                if (!empty($g['tanggal_lahir'])) {
                    $p = explode('-', $g['tanggal_lahir']);
                    if (count($p) == 3) {
                        $tgl_lahir_indo = (int)$p[2] . ' ' . $bulanIndo[(int)$p[1]] . ' ' . $p[0];
                    }
                }
        ?>
        <!-- SISI DEPAN HORIZONTAL -->
        <div class="card card-front" data-id="<?php echo $g['id']; ?>" data-name="<?php echo htmlspecialchars($g['nama']); ?>" data-nis="<?php echo htmlspecialchars($g['niy'] ?? ''); ?>" data-side="depan">
                <div class="card-quick-dl no-print">
                    <button type="button" onclick="event.stopPropagation(); downloadCardElement(this.closest('.card'), 'png')" title="Unduh Sisi Depan (PNG)">PNG</button>
                    <button type="button" onclick="event.stopPropagation(); downloadCardElement(this.closest('.card'), 'jpg')" title="Unduh Sisi Depan (JPG)">JPG</button>
                </div>
                <!-- HORIZONTAL HEADER -->
                <div style="height: 14mm; display: flex; align-items: center; padding: 0 4mm; color: white; position: relative; z-index: 2;">
                    <?php if($inst && $inst['logo']): ?>
                        <img src="<?php echo \App\Core\Helper::url('/public/uploads/logo'); ?>/<?php echo $inst['logo']; ?>" style="width: 10mm; height: 10mm; object-fit: contain; margin-right: 3mm; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">
                    <?php else: ?>
                        <div style="width: 10mm; height: 10mm; margin-right: 3mm; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.1); border-radius: 50%; border: 1px solid rgba(255,255,255,0.2);"><i data-lucide="graduation-cap" style="width: 6mm; color: var(--gold-400);"></i></div>
                    <?php endif; ?>
                    <div style="flex: 1; line-height: 1.15;">
                        <div style="font-weight: 800; font-size: 10.5px; letter-spacing: 1px; color: var(--gold-400);">KARTU IDENTITAS GURU</div>
                        <div style="font-weight: 700; font-size: 9px; opacity: 0.95; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo $instName; ?></div>
                    </div>
                </div>
                
                <div class="gold-line"></div>

                <!-- HORIZONTAL BODY (Glassmorphism) -->
                <div class="glass-panel" style="padding: 3mm 4mm; display: flex; flex-direction: row; gap: 4mm;">
                    <?php if($inst && $inst['logo']): ?>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 50%; height: 75%; background-image: url('<?php echo \App\Core\Helper::url('/public/uploads/logo/' . $inst['logo']); ?>'); background-size: contain; background-repeat: no-repeat; background-position: center; opacity: 0.08; pointer-events: none;"></div>
                    <?php endif; ?>
                    <!-- Foto Kiri -->
                    <div style="width: 22.5mm; flex-shrink: 0; border: 2px solid var(--gold-400); border-radius: 1.5mm; overflow: hidden; padding: 0.5mm; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); height: 30mm; align-self: flex-start; z-index: 5; position: relative;">
                        <img src="<?php echo $foto; ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 1mm;">
                    </div>
                    <!-- Data Kanan -->
                    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <table style="width: 100%; font-size: 8px; border-collapse: collapse; line-height: 1.3; color: #1e293b;">
                            <tr>
                                <td style="font-weight: 700; width: 17mm; color: #475569; vertical-align: top;">Nama</td>
                                <td style="width: 1.5mm; vertical-align: top;">:</td>
                                <?php 
                                    $nama_guru = htmlspecialchars($g['nama']);
                                    $len = mb_strlen($nama_guru);
                                    $fs_horiz = $len > 25 ? '7.5px' : ($len > 18 ? '8.5px' : '9.5px');
                                ?>
                                <td style="font-weight: 800; font-size: <?php echo $fs_horiz; ?>; vertical-align: top; color: #0f172a; position: relative; z-index: 5;"><?php echo $nama_guru; ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700; color: #475569; vertical-align: top;">Jabatan</td>
                                <td style="vertical-align: top;">:</td>
                                <td style="font-weight: 700; color: var(--emerald-700); vertical-align: top; position: relative; z-index: 5;"><?php echo htmlspecialchars($g['jabatan'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700; color: #475569; vertical-align: top;">Tempat Lahir</td>
                                <td style="vertical-align: top;">:</td>
                                <td style="font-weight: 700; vertical-align: top; position: relative; z-index: 5;"><?php echo htmlspecialchars($g['tempat_lahir'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700; color: #475569; vertical-align: top;">Tanggal Lahir</td>
                                <td style="vertical-align: top;">:</td>
                                <td style="font-weight: 700; vertical-align: top; position: relative; z-index: 5;"><?php echo $tgl_lahir_indo; ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700; color: #475569; vertical-align: top;">Alamat</td>
                                <td style="vertical-align: top;">:</td>
                                <td style="font-weight: 700; vertical-align: top; line-height: 1.1; max-width: 35mm; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; position: relative; z-index: 5;"><?php echo isset($g['alamat']) && $g['alamat'] ? htmlspecialchars($g['alamat']) : '-'; ?></td>
                            </tr>
                        </table>
                        
                        <?php 
                            $qrDataTTD = "DOKUMEN SAH\nDitandatangani oleh:\nNama: {$namaKepala}\nJabatan: Kepala Madrasah\nLokasi: {$kotaCetak}\nTanggal: {$tglCetak}";
                            $qrUrlFront = 'https://quickchart.io/qr?size=150&margin=0&text=' . urlencode($qrDataTTD);
                        ?>
                        <!-- QR TTD + Pengesahan -->
                        <div style="display: flex; align-items: flex-end; gap: 2mm; margin-top: auto;">
                            <div style="padding: 1mm; background: white; border-radius: 1mm; border: 1px solid var(--gold-400);">
                                <img src="<?php echo $qrUrlFront; ?>" style="width: 11mm; height: 11mm; object-fit: contain; display: block;">
                            </div>
                            <div style="text-align: left;">
                                <div class="ttd-text-dynamic" style="font-size: 5px; color: #475569; line-height: 1.35;"><?php echo htmlspecialchars($kotaCetak); ?>, <?php echo htmlspecialchars($tglCetak); ?><br>Kepala Madrasah</div>
                                <div style="font-size: 6px; font-weight: 800; color: #0f172a; margin-top: 2.5mm; border-bottom: 0.5px solid #0f172a; display: inline-block; padding-bottom: 0.5px;"><?php echo htmlspecialchars($namaKepala); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        <!-- SISI BELAKANG HORIZONTAL -->
        <div class="card card-back" data-id="<?php echo $g['id']; ?>" data-name="<?php echo htmlspecialchars($g['nama']); ?>" data-nis="<?php echo htmlspecialchars($g['niy'] ?? ''); ?>" data-side="belakang" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4mm;">
            <div class="card-quick-dl no-print">
                <button type="button" onclick="event.stopPropagation(); downloadCardElement(this.closest('.card'), 'png')" title="Unduh Sisi Belakang (PNG)">PNG</button>
                <button type="button" onclick="event.stopPropagation(); downloadCardElement(this.closest('.card'), 'jpg')" title="Unduh Sisi Belakang (JPG)">JPG</button>
            </div>
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: var(--emerald-900); z-index: 1;"></div>
            <!-- Pattern Back -->
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(circle at 50% 50%, rgba(251, 191, 36, 0.1), transparent 50%); z-index: 2;"></div>
            
            <div style="width: 100%; text-align: center; margin-bottom: 4mm; z-index: 3;">
                <div style="font-weight: 800; font-size: 11px; letter-spacing: 1.5px; color: var(--gold-400);">AKSES DIGITAL</div>
                <div style="font-size: 7px; color: #cbd5e1; margin-top: 1mm; font-weight: 500; letter-spacing: 0.5px;">Pindai QR Code untuk verifikasi identitas</div>
            </div>
            
            <div style="background: white; padding: 2.5mm; border-radius: 2mm; border: 2px solid var(--gold-400); box-shadow: 0 10px 25px rgba(0,0,0,0.3); z-index: 3;">
                <img src="<?php echo $qrUrlBack; ?>" style="width: 22mm; height: 22mm; display: block;">
            </div>
            
            <div style="margin-top: 4mm; z-index: 3; text-align: center; color: #94a3b8; font-size: 5.5px; line-height: 1.5; padding: 0 4mm;">
                Kartu ini diterbitkan oleh <strong><?php echo $instName; ?></strong>.<br>
                Barang siapa menemukan kartu ini, harap mengembalikan ke Tata Usaha Madrasah.
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Modal Download Gambar -->
    <div id="modalDownloadGambar" class="no-print" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); z-index: 99999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div style="background: white; border-radius: 16px; width: 92%; max-width: 480px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; overflow: hidden; animation: popIn 0.2s ease;">
            <div style="background: linear-gradient(135deg, #0284c7, #0369a1); padding: 18px 22px; color: white; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 14px; font-weight: 800; letter-spacing: 0.3px;">Unduh Kartu Identitas Guru</h3>
                        <p style="margin: 2px 0 0 0; font-size: 11px; opacity: 0.9;">Export kartu ke file gambar JPG / PNG</p>
                    </div>
                </div>
                <button type="button" onclick="closeDownloadModal()" style="background: none; border: none; color: white; opacity: 0.8; cursor: pointer; font-size: 22px; line-height: 1; padding: 4px;">&times;</button>
            </div>

            <div style="padding: 20px; display: flex; flex-direction: column; gap: 16px;">
                <!-- Format Gambar -->
                <div>
                    <label style="font-size: 11px; font-weight: 800; color: #334155; display: block; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Pilih Format Gambar:</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <label id="lblFmtPng" style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; border: 2px solid #0284c7; background: #f0f9ff; border-radius: 8px; cursor: pointer; font-size: 12px; font-weight: 700; color: #0369a1;">
                            <input type="radio" name="dlFormat" value="png" checked onchange="updateFmtRadios()" style="accent-color: #0284c7;">
                            <span>PNG <span style="font-size: 10px; font-weight: 500; color: #0284c7;">(HD / Rekomendasi)</span></span>
                        </label>
                        <label id="lblFmtJpg" style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; font-size: 12px; font-weight: 700; color: #475569;">
                            <input type="radio" name="dlFormat" value="jpg" onchange="updateFmtRadios()" style="accent-color: #0284c7;">
                            <span>JPG <span style="font-size: 10px; font-weight: 500; color: #64748b;">(Ringan)</span></span>
                        </label>
                    </div>
                </div>

                <!-- Bagian Kartu -->
                <div>
                    <label style="font-size: 11px; font-weight: 800; color: #334155; display: block; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Bagian Kartu:</label>
                    <select id="dlSideSelect" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-family: inherit; font-size: 12px; font-weight: 600; outline: none; color: #0f172a;">
                        <option value="both">Keduanya (Depan & Belakang)</option>
                        <option value="front">Sisi Depan Saja</option>
                        <option value="back">Sisi Belakang Saja</option>
                    </select>
                </div>

                <!-- Target Guru -->
                <div>
                    <label style="font-size: 11px; font-weight: 800; color: #334155; display: block; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Pilih Target Guru:</label>
                    <select id="dlTargetSelect" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-family: inherit; font-size: 12px; font-weight: 600; outline: none; color: #0f172a;">
                        <option value="all">⭐ Semua Guru Ditampilkan (ZIP Kolektif)</option>
                        <?php if (!empty($gurus)): ?>
                            <optgroup label="Pilih Satu Guru:">
                            <?php foreach($gurus as $idx => $gItem): ?>
                                <option value="<?php echo $gItem['id']; ?>">
                                    <?php echo ($idx + 1) . '. ' . htmlspecialchars($gItem['nama']) . ' (' . htmlspecialchars($gItem['niy'] ?? '-') . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Progress Box -->
                <div id="dlProgressBarBox" style="display: none; background: #f8fafc; border-radius: 8px; padding: 12px; border: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span id="dlProgressText" style="font-size: 11px; font-weight: 700; color: #0369a1;">Sedang menyiapkan...</span>
                    </div>
                    <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 99px; overflow: hidden;">
                        <div id="dlProgressBar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #0284c7, #10b981); transition: width 0.2s ease;"></div>
                    </div>
                </div>
            </div>

            <div style="background: #f8fafc; padding: 14px 20px; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeDownloadModal()" style="padding: 8px 16px; font-size: 12px; font-weight: 700; border: 1px solid #cbd5e1; background: white; color: #64748b; border-radius: 8px; cursor: pointer;">Batal</button>
                <button type="button" id="btnStartDownload" onclick="startBulkDownload()" style="padding: 8px 18px; font-size: 12px; font-weight: 700; border: none; background: #0284c7; color: white; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Mulai Unduh
                </button>
            </div>
        </div>
    </div>

    <!-- Script to handle dynamic updates (Toolbar) -->
    <script>
        lucide.createIcons();
        
        function updateTtdText() {
            const kota = document.getElementById('inputKota').value;
            const tgl = document.getElementById('inputTgl').value;
            const els = document.querySelectorAll('.ttd-text-dynamic');
            els.forEach(el => {
                el.innerHTML = kota + ', ' + tgl + '<br>Kepala Madrasah';
            });
        }
        
        function applyPrintSettings() {
            // Toggle Front/Back Cards
            const side = document.getElementById('printSideSelect').value;
            const frontCards = document.querySelectorAll('.card-front');
            const backCards = document.querySelectorAll('.card-back');
            
            frontCards.forEach(c => {
                c.style.display = (side === 'both' || side === 'front') ? 'flex' : 'none';
            });
            backCards.forEach(c => {
                c.style.display = (side === 'both' || side === 'back') ? 'flex' : 'none';
            });

            // Toggle Back Info (Only vertical mode for now based on layout)
            const showInfo = document.getElementById('showBackInfo');
            if (showInfo) {
                const infoEls = document.querySelectorAll('.back-guru-info');
                infoEls.forEach(el => {
                    el.style.display = showInfo.checked ? 'block' : 'none';
                });
            }
        }

        // ================= DOWNLOAD HANDLERS =================
        function openDownloadModal() {
            document.getElementById('modalDownloadGambar').style.display = 'flex';
        }

        function closeDownloadModal() {
            document.getElementById('modalDownloadGambar').style.display = 'none';
        }

        function updateFmtRadios() {
            const isPng = document.querySelector('input[name="dlFormat"]:checked').value === 'png';
            const lblPng = document.getElementById('lblFmtPng');
            const lblJpg = document.getElementById('lblFmtJpg');
            if (isPng) {
                lblPng.style.border = '2px solid #0284c7';
                lblPng.style.background = '#f0f9ff';
                lblPng.style.color = '#0369a1';
                lblJpg.style.border = '1px solid #cbd5e1';
                lblJpg.style.background = 'white';
                lblJpg.style.color = '#475569';
            } else {
                lblJpg.style.border = '2px solid #0284c7';
                lblJpg.style.background = '#f0f9ff';
                lblJpg.style.color = '#0369a1';
                lblPng.style.border = '1px solid #cbd5e1';
                lblPng.style.background = 'white';
                lblPng.style.color = '#475569';
            }
        }

        // Single Card Direct Download
        async function downloadCardElement(cardEl, format = 'png', customName = null) {
            if (!cardEl) return;
            
            // Check if vertical card-back has 180deg rotation
            const originalTransform = cardEl.style.transform;
            const isBackVertical = cardEl.classList.contains('card-back') && cardEl.style.transform.includes('180deg');
            if (isBackVertical) {
                cardEl.style.transform = 'none';
            }
            
            // Hide dl buttons inside card during capture
            const dlElements = cardEl.querySelectorAll('.no-print');
            dlElements.forEach(el => el.style.visibility = 'hidden');

            try {
                const canvas = await html2canvas(cardEl, {
                    scale: 3,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: cardEl.classList.contains('card-front') ? '#00008E' : '#ffffff',
                    logging: false
                });

                const isJpg = format.toLowerCase() === 'jpg' || format.toLowerCase() === 'jpeg';
                const mimeType = isJpg ? 'image/jpeg' : 'image/png';
                const dataUrl = canvas.toDataURL(mimeType, 0.95);

                const guruName = (cardEl.dataset.name || 'kartu_guru').replace(/[^a-zA-Z0-9_-]/g, '_');
                const sideName = cardEl.dataset.side || (cardEl.classList.contains('card-front') ? 'depan' : 'belakang');
                const ext = isJpg ? 'jpg' : 'png';
                const filename = customName || `Kartu_${guruName}_${sideName}.${ext}`;

                const a = document.createElement('a');
                a.href = dataUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                return true;
            } catch (err) {
                console.error('Download card error:', err);
                alert('Gagal mendownload kartu: ' + err.message);
                return false;
            } finally {
                if (isBackVertical) {
                    cardEl.style.transform = originalTransform;
                }
                dlElements.forEach(el => el.style.visibility = '');
            }
        }

        // Bulk / Modal Download
        async function startBulkDownload() {
            const format = document.querySelector('input[name="dlFormat"]:checked').value;
            const side = document.getElementById('dlSideSelect').value;
            const targetScope = document.getElementById('dlTargetSelect').value;
            
            // Collect matching cards
            const allCards = document.querySelectorAll('.card');
            let cardsToProcess = [];
            
            allCards.forEach(card => {
                if (card.style.display === 'none' || card.offsetParent === null) return;
                
                const isFront = card.classList.contains('card-front');
                const isBack = card.classList.contains('card-back');
                
                if (side === 'front' && !isFront) return;
                if (side === 'back' && !isBack) return;
                
                if (targetScope !== 'all' && card.dataset.id !== targetScope) return;
                
                cardsToProcess.push(card);
            });

            if (cardsToProcess.length === 0) {
                alert('Tidak ada kartu yang sesuai dengan pilihan untuk diunduh.');
                return;
            }

            const btnStart = document.getElementById('btnStartDownload');
            const progressBox = document.getElementById('dlProgressBarBox');
            const progressBar = document.getElementById('dlProgressBar');
            const progressText = document.getElementById('dlProgressText');
            
            btnStart.disabled = true;
            btnStart.style.opacity = '0.6';
            progressBox.style.display = 'block';

            const useZip = (typeof JSZip !== 'undefined') && cardsToProcess.length > 1;
            const zip = useZip ? new JSZip() : null;

            for (let i = 0; i < cardsToProcess.length; i++) {
                const cardEl = cardsToProcess[i];
                const currentNum = i + 1;
                const total = cardsToProcess.length;
                const pct = Math.round((currentNum / total) * 100);
                
                progressBar.style.width = pct + '%';
                progressText.innerText = `Memproses ${currentNum} dari ${total} kartu...`;

                const name = (cardEl.dataset.name || 'kartu').replace(/[^a-zA-Z0-9_-]/g, '_');
                const niy = (cardEl.dataset.nis || '').replace(/[^a-zA-Z0-9_-]/g, '_');
                const sideName = cardEl.dataset.side || (cardEl.classList.contains('card-front') ? 'depan' : 'belakang');
                const ext = format === 'jpg' ? 'jpg' : 'png';
                const filename = `Kartu_Guru_${niy ? niy + '_' : ''}${name}_${sideName}.${ext}`;

                const originalTransform = cardEl.style.transform;
                const isBackVertical = cardEl.classList.contains('card-back') && cardEl.style.transform.includes('180deg');
                if (isBackVertical) {
                    cardEl.style.transform = 'none';
                }
                const dlElements = cardEl.querySelectorAll('.no-print');
                dlElements.forEach(el => el.style.visibility = 'hidden');

                try {
                    const canvas = await html2canvas(cardEl, {
                        scale: 3,
                        useCORS: true,
                        allowTaint: true,
                        backgroundColor: cardEl.classList.contains('card-front') ? '#00008E' : '#ffffff',
                        logging: false
                    });

                    const mimeType = ext === 'jpg' ? 'image/jpeg' : 'image/png';
                    const dataUrl = canvas.toDataURL(mimeType, 0.95);

                    if (zip) {
                        const base64Data = dataUrl.split(',')[1];
                        zip.file(filename, base64Data, { base64: true });
                    } else {
                        const a = document.createElement('a');
                        a.href = dataUrl;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        await new Promise(r => setTimeout(r, 350));
                    }
                } catch (err) {
                    console.error('Error generating card image:', err);
                } finally {
                    if (isBackVertical) {
                        cardEl.style.transform = originalTransform;
                    }
                    dlElements.forEach(el => el.style.visibility = '');
                }
            }

            if (zip) {
                progressText.innerText = 'Mengompres file ZIP...';
                const zipBlob = await zip.generateAsync({ type: 'blob' });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(zipBlob);
                a.download = `Kartu_Identitas_Guru_${format.toUpperCase()}.zip`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(a.href);
            }

            progressText.innerText = '✅ Selesai diunduh!';
            setTimeout(() => {
                btnStart.disabled = false;
                btnStart.style.opacity = '1';
                progressBox.style.display = 'none';
                closeDownloadModal();
            }, 1200);
        }
    </script>
</body>
</html>
