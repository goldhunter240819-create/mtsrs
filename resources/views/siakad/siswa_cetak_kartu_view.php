<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Identitas Siswa | SIAKAD</title>
    <link rel="icon" href="<?php echo \App\Core\Helper::url('/public/uploads/logo/' . (isset($inst['logo']) ? $inst['logo'] : '')); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="<?php echo \App\Core\Helper::url('/public/assets/js/lucide.min.js'); ?>"></script>
    <style>
        :root {
            --emerald-900: #064e3b;
            --emerald-800: #065f46;
            --emerald-700: #047857;
            --emerald-600: #059669;
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
            background-color: #00008E;
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
        .card { width: 55mm; height: 87mm; background-color: #00008E; }
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

    </style>
</head>
<body>
    <!-- TOOLBAR -->
    <div class="no-print" style="max-width: <?php echo $mode === 'vertical' ? '750px' : '900px'; ?>; margin: 20px auto 30px auto; background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #cbd5e1; padding: 15px 20px; display: flex; flex-direction: column; gap: 15px;">
        
        <!-- Kolom Kiri: Judul & Settings -->
        <div style="display: flex; flex-direction: column; gap: 10px; flex: 1; min-width: 300px;">
            <div style="font-weight: 800; color: var(--emerald-900); font-size: 14px; display: flex; align-items: center; gap: 6px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Panel Cetak Kartu
            </div>
            
            <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1; min-width: 120px;">
                    <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 5px;">Filter Kelas:</label>
                    <select onchange="window.location.href = '?mode=<?php echo $mode; ?>&kelas_id=' + this.value" style="width: 100%; padding: 7px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-family: inherit; font-size: 11px; font-weight: 600; outline: none; color: #0f172a; cursor: pointer;">
                        <option value="">Semua Kelas</option>
                        <?php 
                        $selectedKelas = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;
                        if (isset($kelasList) && is_array($kelasList)) {
                            foreach($kelasList as $k) {
                                $sel = $selectedKelas === (int)$k['id'] ? 'selected' : '';
                                echo '<option value="' . $k['id'] . '" ' . $sel . '>' . htmlspecialchars($k['nama_kelas']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                
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
                        Tampilkan Info Siswa di Belakang
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
            // Mode Vertikal: Kelompokkan per 4 siswa, cetak depan (baris atas) lalu belakang (baris bawah)
            $chunks = array_chunk($siswas, 5);
            foreach($chunks as $group):
                // === BARIS KARTU DEPAN ===
                foreach($group as $s):
                    $avatarBg = '00008E';
                    $foto = $s['foto'] ? \App\Core\Helper::url('/public/uploads/siswa/' . $s['foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($s['nama']) . '&background=' . $avatarBg . '&color=fff';
                    $instName = $inst ? $inst['nama'] : 'GH-SSS ACADEMY';
        ?>
        <!-- SISI DEPAN VERTIKAL -->
        <div class="card card-front">
                <!-- VERTIKAL HEADER -->
                <div style="height: 19mm; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5mm; color: white; position: relative; z-index: 2;">
                    <?php if($inst && $inst['logo']): ?>
                        <img src="<?php echo \App\Core\Helper::url('/public/uploads/logo'); ?>/<?php echo $inst['logo']; ?>" style="width: 9mm; height: 9mm; object-fit: contain; margin-bottom: 0.8mm; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">
                    <?php else: ?>
                        <div style="width: 9mm; height: 9mm; display: flex; align-items: center; justify-content: center; margin-bottom: 0.8mm;"><i data-lucide="book-open" style="width: 7mm; color: var(--gold-400);"></i></div>
                    <?php endif; ?>
                    <div style="text-align: center; line-height: 1.15;">
                        <div style="font-weight: 800; font-size: 10px; letter-spacing: 1px; color: var(--gold-400);">KARTU IDENTITAS SISWA</div>
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
                    
                    <div style="text-align: center; width: 90%; position: relative; z-index: 5; margin-bottom: 1mm; background: white; padding: 1.5mm 3mm; border-radius: 4mm; border: 1px solid #e2e8f0; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                        <?php 
                            $nama_siswa = htmlspecialchars($s['nama']);
                            $len = mb_strlen($nama_siswa);
                            $fs = $len > 28 ? '7px' : ($len > 22 ? '8px' : ($len > 16 ? '9.5px' : '11px'));
                        ?>
                        <div style="font-weight: 900; color: #00008E; font-size: <?php echo $fs; ?>; line-height: 1.15; text-transform: uppercase; margin-bottom: 1.5mm; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo $nama_siswa; ?></div>
                        <div style="font-size: 8px; color: #000000; font-weight: 700; letter-spacing: 0.5px; line-height: 1.5;">
                            NIS. <?php echo !empty($s['nis']) ? htmlspecialchars($s['nis']) : '-'; ?><br>
                            NISN. <?php echo !empty($s['nisn']) ? htmlspecialchars($s['nisn']) : '-'; ?>
                        </div>
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
            foreach($group as $s):
                $instName = $inst ? $inst['nama'] : 'GH-SSS ACADEMY';
                $qrDataSiswa = $s['qr_token'] ?? 'NO-TOKEN';
        ?>
        <!-- SISI BELAKANG VERTIKAL -->
        <div class="card card-back" style="display: flex; flex-direction: column; align-items: center; justify-content: flex-start; background: white; border: 1px solid #00008E; position: relative; overflow: hidden; padding: 0; transform: rotate(180deg);">
            
            <!-- Elemen Dekoratif Melayang (Soft Shapes) -->
            <div style="position: absolute; top: -10mm; right: -10mm; width: 30mm; height: 30mm; background: radial-gradient(circle, rgba(0, 0, 142, 0.08) 0%, transparent 70%); border-radius: 50%; z-index: 1;"></div>
            <div style="position: absolute; bottom: 10mm; left: -15mm; width: 40mm; height: 40mm; background: radial-gradient(circle, rgba(245, 158, 11, 0.06) 0%, transparent 70%); border-radius: 50%; z-index: 1;"></div>

            <!-- Header Melengkung (Curved) dengan List Emas -->
            <div style="width: 100%; height: 26mm; background: #00008E; position: relative; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; padding-top: 4mm; flex-shrink: 0; z-index: 2;">
                
                <!-- Curved Bottom SVG -->
                <svg viewBox="0 0 1440 320" style="position: absolute; bottom: -1px; left: 0; width: 100%; height: auto; z-index: 3;" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="goldGradientCurve" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#f59e0b" />
                            <stop offset="50%" stop-color="#fcd34d" />
                            <stop offset="100%" stop-color="#f59e0b" />
                        </linearGradient>
                    </defs>
                    <path fill="white" d="M0,160L80,176C160,192,320,224,480,213.3C640,203,800,149,960,138.7C1120,128,1280,160,1360,176L1440,192L1440,320L0,320Z"></path>
                    <path fill="none" stroke="url(#goldGradientCurve)" stroke-width="25" d="M0,160L80,176C160,192,320,224,480,213.3C640,203,800,149,960,138.7C1120,128,1280,160,1360,176L1440,192"></path>
                </svg>

                <div style="font-weight: 800; font-size: 12px; letter-spacing: 1.5px; color: var(--gold-400); position: relative; z-index: 4;">AKSES DIGITAL</div>
                <div style="font-size: 6.5px; color: #e2e8f0; margin-top: 1mm; font-weight: 500; letter-spacing: 0.5px; position: relative; z-index: 4;">Pindai QR Code untuk verifikasi identitas</div>
            </div>

            <!-- Konten Tengah -->
            <div style="flex: 1; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0 4mm; position: relative; z-index: 3; margin-top: -3mm;">
                
                <?php if($inst && $inst['logo']): ?>
                <!-- Watermark -->
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 65%; height: 65%; background-image: url('<?php echo \App\Core\Helper::url('/public/uploads/logo/' . $inst['logo']); ?>'); background-size: contain; background-repeat: no-repeat; background-position: center; opacity: 0.04; pointer-events: none; z-index: 1;"></div>
                <?php endif; ?>

                <div style="background: white; padding: 2.5mm; border-radius: 3mm; border: 1.5px solid var(--gold-400); box-shadow: 0 8px 20px rgba(0,0,0,0.08); margin-bottom: 2.5mm; z-index: 3; position: relative;">
                    <img src="https://quickchart.io/qr?size=150&margin=0&text=<?php echo urlencode($qrDataSiswa); ?>" style="width: 25mm; height: 25mm; object-fit: contain; display: block;">
                </div>

                <div class="back-student-info" style="display: none; text-align: center; background: white; padding: 1.5mm 3mm; border-radius: 4mm; border: 1px solid #e2e8f0; box-shadow: 0 2px 5px rgba(0,0,0,0.02); width: 90%; z-index: 3; position: relative;">
                    <div style="font-weight: 900; font-size: 8.5px; text-transform: uppercase; color: #00008E;"><?php echo htmlspecialchars($s['nama']); ?></div>
                    <div style="font-size: 5.5px; color: #000000; margin-top: 0.5mm; font-weight: 700;">NIS: <?php echo htmlspecialchars($s['nis'] ?: '-'); ?> <span style="color: var(--gold-500); margin: 0 2px;">&bull;</span> NISN: <?php echo htmlspecialchars($s['nisn'] ?: '-'); ?></div>
                </div>
            </div>
            
            <!-- Footer dengan Aksen Garis -->
            <div style="width: 100%; padding: 2.5mm 4mm; text-align: center; color: #64748b; font-size: 5px; line-height: 1.4; position: relative; z-index: 3; flex-shrink: 0; margin-bottom: 1mm;">
                <div style="width: 15mm; height: 1.5px; background: #cbd5e1; margin: 0 auto 1.5mm auto; border-radius: 1px;"></div>
                Diterbitkan oleh <strong><?php echo $instName; ?></strong><br>
                Bila menemukan kartu ini, harap kembalikan ke Madrasah.
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
        <?php // Mode Horizontal: tetap depan-belakang per siswa
            foreach($siswas as $s):
                $avatarBg = '00008E';
                $foto = $s['foto'] ? \App\Core\Helper::url('/public/uploads/siswa/' . $s['foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($s['nama']) . '&background=' . $avatarBg . '&color=fff';
                $tglCetak = date('d M Y');
                $kotaCetak = isset($inst['kota']) && $inst['kota'] ? $inst['kota'] : 'Jakarta';
                $namaKepala = isset($inst['nama_kepala']) && $inst['nama_kepala'] ? $inst['nama_kepala'] : 'Kepala Sekolah';
                $instName = $inst ? $inst['nama'] : 'GH-SSS ACADEMY';
                
                $qrDataSiswa = $s['qr_token'] ?? 'NO-TOKEN';
                $qrDataSiswa = $s['qr_token'] ?? 'NO-TOKEN';

                $tgl_lahir_indo = '-';
                if (!empty($s['tanggal_lahir'])) {
                    $p = explode('-', $s['tanggal_lahir']);
                    if (count($p) == 3) {
                        $tgl_lahir_indo = (int)$p[2] . ' ' . $bulanIndo[(int)$p[1]] . ' ' . $p[0];
                    }
                }
        ?>
        <!-- SISI DEPAN HORIZONTAL -->
        <div class="card card-front">
                <!-- HORIZONTAL HEADER -->
                <div style="height: 14mm; display: flex; align-items: center; padding: 0 4mm; color: white; position: relative; z-index: 2;">
                    <?php if($inst && $inst['logo']): ?>
                        <img src="<?php echo \App\Core\Helper::url('/public/uploads/logo'); ?>/<?php echo $inst['logo']; ?>" style="width: 10mm; height: 10mm; object-fit: contain; margin-right: 3mm; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">
                    <?php else: ?>
                        <div style="width: 10mm; height: 10mm; margin-right: 3mm; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.1); border-radius: 50%; border: 1px solid rgba(255,255,255,0.2);"><i data-lucide="book-open" style="width: 6mm; color: var(--gold-400);"></i></div>
                    <?php endif; ?>
                    <div style="flex: 1; line-height: 1.15;">
                        <div style="font-weight: 800; font-size: 10.5px; letter-spacing: 1px; color: var(--gold-400);">KARTU IDENTITAS SISWA</div>
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
                                    $nama_siswa = htmlspecialchars($s['nama']);
                                    $len = strlen($nama_siswa);
                                    $fs_horiz = $len > 25 ? '7.5px' : ($len > 18 ? '8.5px' : '9.5px');
                                ?>
                                <td style="font-weight: 800; font-size: <?php echo $fs_horiz; ?>; vertical-align: top; text-transform: uppercase; color: #0f172a; position: relative; z-index: 5;"><?php echo $nama_siswa; ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700; color: #475569; vertical-align: top;">NIS/NISN</td>
                                <td style="vertical-align: top;">:</td>
                                <td style="font-weight: 700; vertical-align: top; position: relative; z-index: 5;"><?php echo htmlspecialchars($s['nis'] ?: '-'); ?> / <?php echo htmlspecialchars($s['nisn'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700; color: #475569; vertical-align: top;">Tempat Lahir</td>
                                <td style="vertical-align: top;">:</td>
                                <td style="font-weight: 700; color: #00008E; vertical-align: top; position: relative; z-index: 5;"><?php echo htmlspecialchars($s['tempat_lahir'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700; color: #475569; vertical-align: top;">Tanggal Lahir</td>
                                <td style="vertical-align: top;">:</td>
                                <td style="font-weight: 700; color: #00008E; vertical-align: top; position: relative; z-index: 5;"><?php echo $tgl_lahir_indo; ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700; color: #475569; vertical-align: top;">Alamat</td>
                                <td style="vertical-align: top;">:</td>
                                <td style="font-weight: 700; vertical-align: top; line-height: 1.1; max-width: 35mm; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; position: relative; z-index: 5;"><?php echo isset($s['alamat']) && $s['alamat'] ? $s['alamat'] : '-'; ?></td>
                            </tr>
                        </table>
                        
                        <?php 
                            $qrDataTTD = "DOKUMEN SAH\nDitandatangani oleh:\nNama: {$namaKepala}\nJabatan: Kepala Madrasah\nLokasi: {$kotaCetak}\nTanggal: {$tglCetak}";
                            $qrDataTTD = "DOKUMEN SAH\nDitandatangani oleh:\nNama: {$namaKepala}\nJabatan: Kepala Madrasah\nLokasi: {$kotaCetak}\nTanggal: {$tglCetak}";
                        ?>
                        <!-- QR TTD + Pengesahan -->
                        <div style="display: flex; align-items: flex-end; gap: 2mm; margin-top: auto;">
                            <div style="padding: 1mm; background: white; border-radius: 1mm; border: 1px solid var(--gold-400);">
                                <img src="https://quickchart.io/qr?size=150&margin=0&text=<?php echo urlencode($qrDataTTD); ?>" style="width: 11mm; height: 11mm; object-fit: contain; display: block;">
                            </div>
                            <div style="text-align: left;">
                                <div class="ttd-text-dynamic" style="font-size: 5px; color: #475569; line-height: 1.35;"><?php echo htmlspecialchars($kotaCetak); ?>, <?php echo htmlspecialchars($tglCetak); ?><br>Kepala Madrasah</div>
                                <div style="font-size: 6px; font-weight: 800; color: #0f172a; margin-top: 2.5mm; border-bottom: 0.5px solid #0f172a; display: inline-block; padding-bottom: 0.5px;"><?php echo $namaKepala; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        <!-- SISI BELAKANG HORIZONTAL (SPLIT LAYOUT) -->
        <div class="card card-back" style="display: flex; flex-direction: row; background: white; border: 1px solid #00008E; position: relative; overflow: hidden; padding: 0;">
            <!-- Kolom Kiri: Header Biru -->
            <div style="width: 35%; height: 100%; background: #00008E; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4mm; position: relative; z-index: 2;">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(circle at top left, rgba(251, 191, 36, 0.2), transparent 70%);"></div>
                
                <div style="font-weight: 900; font-size: 11px; letter-spacing: 1px; color: var(--gold-400); text-align: center; position: relative; z-index: 3; line-height: 1.2;">AKSES<br>DIGITAL</div>
                
                <div style="width: 10mm; height: 1.5px; background: var(--gold-400); margin: 3.5mm 0; position: relative; z-index: 3; border-radius: 1px;"></div>
                
                <div style="font-size: 5px; color: #e2e8f0; text-align: center; font-weight: 500; line-height: 1.4; position: relative; z-index: 3;">
                    Pindai QR Code untuk verifikasi identitas
                </div>
            </div>
            
            <!-- List Emas Vertikal -->
            <div style="width: 1.5mm; height: 100%; background: linear-gradient(180deg, var(--gold-500), var(--gold-300), var(--gold-500)); position: relative; z-index: 3; flex-shrink: 0;"></div>

            <!-- Kolom Kanan: Area Putih QR -->
            <div style="flex: 1; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4mm; position: relative; z-index: 2;">
                <?php if($inst && $inst['logo']): ?>
                <!-- Watermark -->
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 60%; height: 60%; background-image: url('<?php echo \App\Core\Helper::url('/public/uploads/logo/' . $inst['logo']); ?>'); background-size: contain; background-repeat: no-repeat; background-position: center; opacity: 0.05; pointer-events: none; z-index: 1;"></div>
                <?php endif; ?>

                <div style="background: white; padding: 2.5mm; border-radius: 2mm; border: 1.5px solid #00008E; box-shadow: 0 5px 15px rgba(0,0,0,0.06); z-index: 3; margin-bottom: 3.5mm;">
                    <img src="https://quickchart.io/qr?size=150&margin=0&text=<?php echo urlencode($qrDataSiswa); ?>" style="width: 25mm; height: 25mm; object-fit: contain; display: block;">
                </div>

                <!-- Footer Text -->
                <div style="text-align: center; color: #64748b; font-size: 5px; line-height: 1.4; position: relative; z-index: 3;">
                    Diterbitkan oleh <strong><?php echo $instName; ?></strong><br>
                    Bila menemukan kartu ini, harap kembalikan ke Madrasah.
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Fixed widgets removed to prevent overlap, moved to top toolbar -->

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

            // Toggle Back Info
            const showInfo = document.getElementById('showBackInfo');
            if (showInfo) {
                const infoEls = document.querySelectorAll('.back-student-info');
                infoEls.forEach(el => {
                    el.style.display = showInfo.checked ? 'block' : 'none';
                });
            }
        }
    </script>
</body>
</html>
