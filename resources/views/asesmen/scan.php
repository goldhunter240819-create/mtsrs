<?php
use App\Core\Helper;

$title = $title ?? 'Scan Presensi Ujian Siswa | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_scan';

ob_start();
?>

<!-- Include html5-qrcode library -->
<script src="<?php echo Helper::url('/assets/js/html5-qrcode.min.js'); ?>"></script>

<style>
    /* Styling khusus scanner asesmen */
    .scan-hero {
        background: linear-gradient(135deg, #059669 0%, #0d9488 50%, #0f766e 100%);
        border-radius: 20px;
        padding: 1.75rem 2rem;
        color: white;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.25rem;
        box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.25);
    }
    .hero-badge-live {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        padding: 6px 14px;
        border-radius: 9999px;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .hero-badge-live .pulse-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #4ade80;
        box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7);
        animation: pulseGreen 1.8s infinite;
    }
    @keyframes pulseGreen {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(74, 222, 128, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74, 222, 128, 0); }
    }

    /* Grid scanner & list */
    .scan-grid {
        display: grid;
        grid-template-columns: 460px 1fr;
        gap: 1.5rem;
        align-items: start;
    }
    @media (max-width: 1080px) {
        .scan-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Camera Reader Wrapper */
    .camera-box {
        position: relative;
        background: #0f172a;
        border-radius: 18px;
        overflow: hidden;
        border: 2px solid #1e293b;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15);
        min-height: 320px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    #reader {
        width: 100% !important;
        border: none !important;
    }
    #reader video {
        width: 100% !important;
        height: auto !important;
        object-fit: cover !important;
        border-radius: 16px;
    }

    /* Laser Scanning Animation */
    .scanner-overlay {
        position: absolute;
        inset: 0;
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 5;
    }
    .scanner-target-box {
        width: 240px;
        height: 240px;
        border: 2px solid rgba(16, 185, 129, 0.6);
        border-radius: 18px;
        position: relative;
        box-shadow: 0 0 0 4000px rgba(15, 23, 42, 0.65);
    }
    .scanner-laser {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #10b981;
        box-shadow: 0 0 12px #10b981, 0 0 4px #34d399;
        animation: scanLaser 2.2s infinite ease-in-out;
    }
    @keyframes scanLaser {
        0%, 100% { top: 6px; opacity: 0.2; }
        50% { top: 228px; opacity: 1; }
    }

    /* Stat Cards */
    .stat-counter-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 768px) {
        .stat-counter-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .stat-counter-card {
        background: var(--z-bg);
        border: 1px solid var(--z-border);
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Result Popup Card */
    .scan-result-card {
        border-radius: 16px;
        padding: 1.25rem;
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .scan-result-card.success {
        background: #ecfdf5;
        border: 2px solid #10b981;
        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.25);
    }
    .scan-result-card.duplicate {
        background: #fffbeb;
        border: 2px solid #f59e0b;
        box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.25);
    }
    .scan-result-card.error {
        background: #fef2f2;
        border: 2px solid #ef4444;
        box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.25);
    }

    /* Siswa row item */
    .siswa-card-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid var(--z-border);
        background: var(--z-bg);
        margin-bottom: 8px;
        transition: 0.15s;
    }
    .siswa-card-row:hover {
        background: rgba(241, 245, 249, 0.6);
        border-color: #cbd5e1;
    }
    .siswa-card-row.is-hadir {
        border-left: 4px solid #10b981;
    }
    .siswa-card-row.is-belum {
        border-left: 4px solid #cbd5e1;
    }
</style>

<!-- Hero / Header Section -->
<div class="scan-hero">
    <div>
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
            <span class="hero-badge-live">
                <span class="pulse-dot"></span>
                <span>SCANNER OTOMATIS AKTIF</span>
            </span>
            <span id="liveClock" style="font-family: monospace; font-size: 0.95rem; font-weight: 700; opacity: 0.95; background: rgba(0,0,0,0.15); padding: 4px 10px; border-radius: 6px;">
                <?php echo date('H:i:s'); ?> WIB
            </span>
        </div>
        <h1 style="font-size: 1.55rem; font-weight: 800; margin: 0 0 6px 0; color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="qr-code" style="width: 28px; height: 28px;"></i>
            Presensi Ujian Real-Time
        </h1>
        <p style="margin: 0; font-size: 0.9rem; opacity: 0.92; line-height: 1.4;">
            Pindai Barcode / QR Code Kartu Pelajar siswa untuk verifikasi kehadiran dan nomor meja otomatis per sesi.
        </p>
    </div>

    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <?php if ($activeJadwal && $selectedRuangId > 0): ?>
            <a href="<?php echo Helper::url('/asesmen/daftar-hadir/cetak?event_id=' . $activeJadwal['event_id'] . '&ruang_id=' . $selectedRuangId . '&mapel_id=' . $activeJadwal['mapel_id'] . '&tanggal=' . $activeJadwal['tanggal']); ?>" 
               target="_blank" 
               class="btn" 
               style="background: white; color: #065f46; font-weight: 700; border-radius: 10px; padding: 10px 18px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <i data-lucide="printer" style="width: 18px; height: 18px; color: #059669;"></i>
                Cetak Lembar Presensi
            </a>
        <?php endif; ?>
        <a href="<?php echo Helper::url('/asesmen/berita-acara'); ?>" 
           class="btn" 
           style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.35); font-weight: 700; border-radius: 10px; padding: 10px 18px; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="file-text" style="width: 18px; height: 18px;"></i>
            Berita Acara
        </a>
    </div>
</div>

<!-- Session & Room Controls Bar -->
<div class="z-card" style="padding: 1.25rem 1.5rem; border-radius: 16px; margin-bottom: 1.5rem;">
    <form id="filterForm" method="GET" action="<?php echo Helper::url('/asesmen/scan'); ?>" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        <!-- Dropdown Jadwal / Sesi -->
        <div style="flex: 2; min-width: 280px;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--z-text); margin-bottom: 6px;">
                <i data-lucide="calendar-clock" style="width: 14px; height: 14px; display: inline; vertical-align: middle; color: #059669;"></i>
                Jadwal & Mata Pelajaran Aktif:
            </label>
            <select name="jadwal_id" id="jadwalSelect" onchange="this.form.submit()" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); font-weight: 600; outline: none;">
                <?php if (empty($allJadwals)): ?>
                    <option value="">Belum ada jadwal ujian yang diatur</option>
                <?php else: ?>
                    <?php foreach($allJadwals as $j): 
                        $isSelected = ($activeJadwal && $activeJadwal['id'] == $j['id']);
                        $isToday = ($j['tanggal'] == date('Y-m-d'));
                        $labelTgl = date('d/m/Y', strtotime($j['tanggal']));
                        $labelWaktu = substr($j['jam_mulai'], 0, 5) . ' - ' . substr($j['jam_selesai'], 0, 5) . ' WIB';
                        $labelKelas = !empty($j['nama_kelas']) ? $j['nama_kelas'] : ($j['tingkat'] !== 'Semua' ? 'Tingkat ' . $j['tingkat'] : 'Semua Tingkat');
                    ?>
                        <option value="<?php echo $j['id']; ?>" <?php echo $isSelected ? 'selected' : ''; ?>>
                            <?php echo $isToday ? '[HARI INI] ' : ''; ?><?php echo $labelTgl; ?> &bull; <?php echo htmlspecialchars($j['nama_mapel']); ?> (<?php echo $labelWaktu; ?>) &bull; <?php echo $labelKelas; ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <!-- Dropdown Ruangan -->
        <div style="flex: 1.2; min-width: 200px;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--z-text); margin-bottom: 6px;">
                <i data-lucide="door-open" style="width: 14px; height: 14px; display: inline; vertical-align: middle; color: #059669;"></i>
                Pilih Ruangan Ujian:
            </label>
            <select name="ruang_id" id="ruangSelect" onchange="onRuangChanged(this.value)" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); font-weight: 600; outline: none;">
                <option value="0">-- Semua Ruangan (Global) --</option>
                <?php foreach($ruangs as $r): ?>
                    <option value="<?php echo $r['id']; ?>" <?php echo ($selectedRuangId == $r['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($r['nama_ruang']); ?> (Kapasitas: <?php echo $r['kapasitas']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="padding: 10px 18px; border-radius: 8px; font-weight: 700; display: flex; align-items: center; gap: 8px; background: #059669; border-color: #059669;">
                <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i> Refresh Data
            </button>
        </div>
    </form>
</div>

<!-- Stat Counter Bar -->
<div class="stat-counter-grid">
    <div class="stat-counter-card">
        <div class="stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
            <i data-lucide="users" style="width: 24px; height: 24px;"></i>
        </div>
        <div>
            <div style="font-size: 0.78rem; font-weight: 700; color: var(--z-text-muted); text-transform: uppercase;">Total Peserta</div>
            <div id="statTotal" style="font-size: 1.55rem; font-weight: 800; color: var(--z-text); line-height: 1.2;">
                <?php echo $totalPeserta; ?>
            </div>
        </div>
    </div>

    <div class="stat-counter-card">
        <div class="stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
            <i data-lucide="user-check" style="width: 24px; height: 24px;"></i>
        </div>
        <div>
            <div style="font-size: 0.78rem; font-weight: 700; color: #16a34a; text-transform: uppercase;">Sudah Hadir</div>
            <div id="statHadir" style="font-size: 1.55rem; font-weight: 800; color: #16a34a; line-height: 1.2;">
                <?php echo $totalHadir; ?>
            </div>
        </div>
    </div>

    <div class="stat-counter-card">
        <div class="stat-icon-wrap" style="background: #fee2e2; color: #dc2626;">
            <i data-lucide="user-x" style="width: 24px; height: 24px;"></i>
        </div>
        <div>
            <div style="font-size: 0.78rem; font-weight: 700; color: #dc2626; text-transform: uppercase;">Belum Hadir</div>
            <div id="statBelum" style="font-size: 1.55rem; font-weight: 800; color: #dc2626; line-height: 1.2;">
                <?php echo $totalBelum; ?>
            </div>
        </div>
    </div>

    <div class="stat-counter-card">
        <div class="stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
            <i data-lucide="pie-chart" style="width: 24px; height: 24px;"></i>
        </div>
        <div>
            <div style="font-size: 0.78rem; font-weight: 700; color: var(--z-text-muted); text-transform: uppercase;">Kehadiran</div>
            <div id="statPersen" style="font-size: 1.55rem; font-weight: 800; color: #d97706; line-height: 1.2;">
                <?php echo $persenHadir; ?>%
            </div>
        </div>
    </div>
</div>

<!-- Main Interactive Workspace -->
<div class="scan-grid">
    <!-- LEFT: Scanner & Input Gun -->
    <div>
        <div class="z-card" style="padding: 1.5rem; border-radius: 18px; margin-bottom: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0; color: var(--z-text); display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="camera" style="color: #059669; width: 20px; height: 20px;"></i>
                    Kamera Scanner
                </h3>
                <div style="display: flex; gap: 6px;">
                    <button type="button" id="btnToggleSound" onclick="toggleAudio()" class="btn btn-outline btn-sm" title="Mute / Unmute Suara" style="border-radius: 8px; padding: 6px 10px;">
                        <i data-lucide="volume-2" id="soundIcon" style="width: 14px; height: 14px;"></i>
                    </button>
                    <button type="button" id="btnFlipCamera" onclick="flipCamera()" class="btn btn-outline btn-sm" title="Putar Kamera" style="border-radius: 8px; padding: 6px 10px;">
                        <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i> Putar
                    </button>
                </div>
            </div>

            <!-- Video Camera Viewport -->
            <div class="camera-box" id="cameraBox">
                <div id="reader"></div>
                <div class="scanner-overlay" id="scannerOverlay">
                    <div class="scanner-target-box">
                        <div class="scanner-laser"></div>
                    </div>
                </div>
                <div id="cameraLoading" style="position: absolute; color: #94a3b8; font-size: 0.88rem; display: flex; flex-direction: column; align-items: center; gap: 10px;">
                    <i data-lucide="loader-2" class="spin" style="width: 32px; height: 32px; color: #10b981;"></i>
                    <span>Memulai Kamera Scanner...</span>
                </div>
            </div>

            <!-- Scanner Gun / Manual Input -->
            <div style="margin-top: 1.25rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--z-text); margin-bottom: 6px;">
                    <i data-lucide="barcode" style="width: 16px; height: 16px; display: inline; vertical-align: middle; color: #059669;"></i>
                    Barcode Scanner Gun / Input NIS / No. Peserta:
                </label>
                <div style="position: relative;">
                    <input type="text" 
                           id="barcodeGunInput" 
                           autocomplete="off" 
                           placeholder="Scan barcode gun / ketik NIS lalu Enter..." 
                           style="width: 100%; padding: 12px 42px 12px 14px; border-radius: 10px; border: 2px solid #cbd5e1; background: var(--z-bg); color: var(--z-text); font-weight: 600; font-size: 0.95rem; outline: none; transition: 0.2s;"
                           onfocus="this.style.borderColor='#059669'; this.style.boxShadow='0 0 0 3px rgba(5,150,105,0.15)'"
                           onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'"
                           onkeydown="if(event.key==='Enter'){ event.preventDefault(); handleManualInput(this.value); this.value=''; }">
                    <button type="button" 
                            onclick="handleManualInput(document.getElementById('barcodeGunInput').value); document.getElementById('barcodeGunInput').value='';" 
                            style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: #059669; border: none; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                        <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                    </button>
                </div>
                <div style="font-size: 0.75rem; color: var(--z-text-muted); margin-top: 5px;">
                    *Jika menggunakan scanner gun USB, cursor otomatis fokus dan mendeteksi input seketika.
                </div>
            </div>
        </div>

        <!-- Result / Feedback Card -->
        <div class="z-card" style="padding: 1.25rem; border-radius: 18px;">
            <h4 style="font-size: 0.88rem; font-weight: 700; color: var(--z-text-muted); margin: 0 0 10px 0; text-transform: uppercase;">
                Hasil Pindaian Terakhir:
            </h4>
            <div id="scanResultArea" class="scan-result-card">
                <div style="text-align: center; color: var(--z-text-muted); padding: 1.5rem 0;">
                    <i data-lucide="scan-line" style="width: 36px; height: 36px; opacity: 0.4; margin-bottom: 6px;"></i>
                    <div style="font-size: 0.88rem; font-weight: 600;">Siap memindai kartu peserta...</div>
                    <div style="font-size: 0.75rem; opacity: 0.8;">Arahkan QR Code ke kamera atau tembakkan scanner gun</div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Peserta List & Live Presensi -->
    <div>
        <div class="z-card" style="padding: 1.5rem; border-radius: 18px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0; color: var(--z-text); display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="list-checks" style="color: #059669; width: 20px; height: 20px;"></i>
                        Daftar Peserta Sesi Ini
                    </h3>
                    <div style="font-size: 0.8rem; color: var(--z-text-muted);">
                        <?php echo $selectedRuangId > 0 ? 'Filter Ruangan Terpilih' : 'Menampilkan Semua Ruangan'; ?>
                    </div>
                </div>

                <!-- Tabs Filter Status -->
                <div style="display: flex; gap: 6px; background: rgba(0,0,0,0.04); padding: 4px; border-radius: 10px;">
                    <button type="button" class="btn btn-sm btnTab active" onclick="filterTab('all', this)" style="border-radius: 8px; font-size: 0.78rem; font-weight: 700;">
                        Semua (<span id="tabCountAll"><?php echo $totalPeserta; ?></span>)
                    </button>
                    <button type="button" class="btn btn-sm btnTab" onclick="filterTab('hadir', this)" style="border-radius: 8px; font-size: 0.78rem; font-weight: 700;">
                        Hadir (<span id="tabCountHadir"><?php echo $totalHadir; ?></span>)
                    </button>
                    <button type="button" class="btn btn-sm btnTab" onclick="filterTab('belum', this)" style="border-radius: 8px; font-size: 0.78rem; font-weight: 700;">
                        Belum (<span id="tabCountBelum"><?php echo $totalBelum; ?></span>)
                    </button>
                </div>
            </div>

            <!-- Search Field -->
            <div style="margin-bottom: 1rem; position: relative;">
                <input type="text" 
                       id="searchPesertaInput" 
                       oninput="searchPeserta(this.value)" 
                       placeholder="Cari nama peserta, NIS, atau nomor meja..." 
                       style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); font-size: 0.88rem; outline: none;">
                <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--z-text-muted);"></i>
            </div>

            <!-- List of Students -->
            <div id="pesertaListContainer" style="max-height: 560px; overflow-y: auto; padding-right: 4px;">
                <?php if (empty($pesertaList)): ?>
                    <div style="text-align: center; padding: 3rem 1rem; color: var(--z-text-muted);">
                        <i data-lucide="user-x" style="width: 42px; height: 42px; opacity: 0.4; margin-bottom: 8px;"></i>
                        <p style="margin: 0; font-size: 0.95rem; font-weight: 600;">Belum ada peserta ujian yang terdaftar untuk sesi/ruang ini.</p>
                        <p style="margin: 4px 0 0 0; font-size: 0.8rem;">Pastikan jadwal ujian dan distribusi ruang telah disiapkan.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($pesertaList as $p): 
                        $isHadir = !empty($p['presensi_id']);
                        $statusClass = $isHadir ? 'is-hadir' : 'is-belum';
                        $statusText = $isHadir ? strtoupper($p['presensi_status']) : 'BELUM SCAN';
                        $jamScanText = $isHadir ? substr($p['jam_scan'], 0, 5) . ' WIB' : '';
                        $fotoSiswa = !empty($p['foto']) ? Helper::url('/uploads/siswa/' . $p['foto']) : null;
                    ?>
                        <div class="siswa-card-row <?php echo $statusClass; ?>" 
                             id="siswaRow_<?php echo $p['siswa_id']; ?>" 
                             data-status="<?php echo $isHadir ? 'hadir' : 'belum'; ?>"
                             data-nama="<?php echo strtolower(htmlspecialchars($p['nama_siswa'])); ?>"
                             data-nis="<?php echo htmlspecialchars($p['nis']); ?>"
                             data-meja="<?php echo sprintf("%02d", $p['nomor_meja']); ?>">
                            
                            <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                                <!-- Badge Meja -->
                                <div style="width: 44px; height: 44px; border-radius: 10px; background: <?php echo $isHadir ? '#dcfce7' : '#f1f5f9'; ?>; color: <?php echo $isHadir ? '#15803d' : '#475569'; ?>; display: flex; flex-direction: column; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; flex-shrink: 0; border: 1px solid <?php echo $isHadir ? '#bbf7d0' : '#e2e8f0'; ?>;">
                                    <span style="font-size: 0.6rem; font-weight: 700; opacity: 0.8; line-height: 1;">MEJA</span>
                                    <span><?php echo sprintf("%02d", $p['nomor_meja']); ?></span>
                                </div>

                                <!-- Foto or Avatar -->
                                <?php if ($fotoSiswa): ?>
                                    <img src="<?php echo $fotoSiswa; ?>" alt="Foto" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 2px solid #e2e8f0;">
                                <?php else: ?>
                                    <div style="width: 38px; height: 38px; border-radius: 50%; background: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0;">
                                        <?php echo strtoupper(substr($p['nama_siswa'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Info Siswa -->
                                <div style="min-width: 0;">
                                    <div style="font-weight: 700; font-size: 0.9rem; color: var(--z-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($p['nama_siswa']); ?>
                                    </div>
                                    <div style="font-size: 0.76rem; color: var(--z-text-muted); display: flex; align-items: center; gap: 8px;">
                                        <span>NIS: <?php echo htmlspecialchars($p['nis']); ?></span>
                                        <span>&bull;</span>
                                        <span>Kelas <?php echo htmlspecialchars($p['nama_kelas'] ?? '-'); ?></span>
                                        <span>&bull;</span>
                                        <span style="color: #059669; font-weight: 600;"><?php echo htmlspecialchars($p['nama_ruang'] ?? '-'); ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Status & Action -->
                            <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                                <div id="badgeStatus_<?php echo $p['siswa_id']; ?>">
                                    <?php if ($isHadir): ?>
                                        <span class="pill pill-green" style="font-size: 0.75rem; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px;">
                                            <i data-lucide="check" style="width: 12px; height: 12px;"></i>
                                            <?php echo $statusText; ?> (<?php echo $jamScanText; ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="pill" style="background: #f1f5f9; color: #64748b; font-size: 0.75rem; padding: 4px 10px;">
                                            Belum Hadir
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Action button -->
                                <div id="btnAction_<?php echo $p['siswa_id']; ?>">
                                    <?php if ($isHadir): ?>
                                        <button type="button" 
                                                onclick="cancelPresensi(<?php echo $p['presensi_id']; ?>, <?php echo $p['siswa_id']; ?>)" 
                                                class="btn btn-ghost btn-sm" 
                                                title="Batalkan Presensi" 
                                                style="color: #ef4444; padding: 4px 8px; border-radius: 6px;">
                                            <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" 
                                                onclick="manualPresensi(<?php echo $p['siswa_id']; ?>, '<?php echo htmlspecialchars(addslashes($p['nama_siswa'])); ?>')" 
                                                class="btn btn-outline btn-sm" 
                                                title="Tandai Hadir Manual" 
                                                style="font-size: 0.75rem; padding: 4px 8px; border-radius: 6px; color: #059669; border-color: #a7f3d0;">
                                            Hadir
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // State variables
    const currentJadwalId = <?php echo (int)($activeJadwal['id'] ?? 0); ?>;
    let selectedRuangId = <?php echo (int)$selectedRuangId; ?>;
    let isProcessing = false;
    let soundEnabled = true;
    let html5QrCode = null;
    let currentFacingMode = "environment";
    let activeFilter = 'all';

    // Digital Clock
    function updateLiveClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour12: false }) + ' WIB';
        const el = document.getElementById('liveClock');
        if (el) el.innerText = timeStr;
    }
    setInterval(updateLiveClock, 1000);

    // Persist and sync Ruang Selection via localStorage
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
        const savedRuang = localStorage.getItem('mtsrs_scan_ruang_id');
        const ruangSelect = document.getElementById('ruangSelect');
        if (selectedRuangId === 0 && savedRuang && savedRuang !== '0' && ruangSelect) {
            ruangSelect.value = savedRuang;
            // auto submit if not explicitly set in url
            const urlParams = new URLSearchParams(window.location.search);
            if (!urlParams.has('ruang_id')) {
                document.getElementById('filterForm').submit();
                return;
            }
        }
        initScanner();
        focusScannerGun();
    });

    function onRuangChanged(val) {
        localStorage.setItem('mtsrs_scan_ruang_id', val);
        document.getElementById('filterForm').submit();
    }

    function focusScannerGun() {
        const input = document.getElementById('barcodeGunInput');
        if (input) {
            input.focus();
        }
    }
    // Re-focus scanner gun input on click anywhere outside inputs
    document.addEventListener('click', (e) => {
        if (!e.target.closest('input, select, textarea, button, a')) {
            focusScannerGun();
        }
    });

    // Sound Generator (Web Audio API - 100% offline)
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    function playBeep(type = 'success') {
        if (!soundEnabled) return;
        try {
            if (audioCtx.state === 'suspended') audioCtx.resume();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);

            if (type === 'success') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, audioCtx.currentTime); // High pitch pleasant beep
                gain.gain.setValueAtTime(0.4, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.18);
                osc.start(audioCtx.currentTime);
                osc.stop(audioCtx.currentTime + 0.18);
            } else if (type === 'duplicate') {
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(440, audioCtx.currentTime);
                gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
                osc.start(audioCtx.currentTime);
                osc.stop(audioCtx.currentTime + 0.3);
            } else {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(220, audioCtx.currentTime);
                gain.gain.setValueAtTime(0.4, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);
                osc.start(audioCtx.currentTime);
                osc.stop(audioCtx.currentTime + 0.4);
            }
        } catch(e) {}
    }

    function toggleAudio() {
        soundEnabled = !soundEnabled;
        const icon = document.getElementById('soundIcon');
        const btn = document.getElementById('btnToggleSound');
        if (soundEnabled) {
            btn.style.color = '#059669';
            btn.innerHTML = '<i data-lucide="volume-2" style="width:14px;height:14px;"></i>';
        } else {
            btn.style.color = '#94a3b8';
            btn.innerHTML = '<i data-lucide="volume-x" style="width:14px;height:14px;"></i>';
        }
        lucide.createIcons();
    }

    // HTML5 QR Code Scanner Init
    function initScanner() {
        const loading = document.getElementById('cameraLoading');
        html5QrCode = new Html5Qrcode("reader");

        const config = {
            fps: 15,
            qrbox: { width: 240, height: 240 },
            aspectRatio: 1.0
        };

        html5QrCode.start(
            { facingMode: currentFacingMode },
            config,
            (decodedText) => {
                handleScanSuccess(decodedText);
            },
            (errorMessage) => {
                // scanning frame error (ignore)
            }
        ).then(() => {
            if (loading) loading.style.display = 'none';
        }).catch((err) => {
            if (loading) {
                loading.innerHTML = '<span style="color:#ef4444; font-weight:600;"><i data-lucide="video-off" style="width:24px;height:24px;display:block;margin:0 auto 4px auto;"></i> Kamera tidak aktif/diizinkan</span><span style="font-size:0.75rem; color:#94a3b8;">Gunakan Scanner Gun USB atau Input NIS di bawah.</span>';
                lucide.createIcons();
            }
        });
    }

    function flipCamera() {
        if (!html5QrCode) return;
        currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
        html5QrCode.stop().then(() => {
            initScanner();
        }).catch(() => {
            initScanner();
        });
    }

    // Unified scan processor (Camera & Barcode Gun)
    function handleScanSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;
        sendScanRequest(decodedText);
    }

    function handleManualInput(text) {
        text = text.trim();
        if (!text) return;
        if (isProcessing) return;
        isProcessing = true;
        sendScanRequest(text);
    }

    function sendScanRequest(code) {
        const resultArea = document.getElementById('scanResultArea');
        resultArea.innerHTML = `
            <div style="text-align:center; padding:1.25rem 0; color:#059669;">
                <i data-lucide="loader-2" class="spin" style="width:28px;height:28px;margin-bottom:6px;"></i>
                <div style="font-weight:700; font-size:0.9rem;">Memverifikasi Data Siswa...</div>
            </div>
        `;
        lucide.createIcons();

        fetch('<?php echo Helper::url('/asesmen/scan/process'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                qr_code: code,
                jadwal_id: currentJadwalId,
                ruang_id: selectedRuangId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                playBeep('success');
                renderResultCard(data.data, true, data.message, data.warning);
                updateSiswaRowHadir(data.data);
                refreshCounters(1);
            } else if (data.duplicate) {
                playBeep('duplicate');
                renderResultCard(data.data, false, data.message, data.warning, true);
            } else {
                playBeep('error');
                renderErrorCard(data.message);
            }
        })
        .catch(err => {
            playBeep('error');
            renderErrorCard('Terjadi kesalahan koneksi ke server.');
        })
        .finally(() => {
            setTimeout(() => {
                isProcessing = false;
                focusScannerGun();
            }, 1800);
        });
    }

    function renderResultCard(s, isSuccess, message, warning, isDuplicate = false) {
        const resultArea = document.getElementById('scanResultArea');
        const cardClass = isSuccess ? 'scan-result-card success' : (isDuplicate ? 'scan-result-card duplicate' : 'scan-result-card error');
        const badgeColor = isSuccess ? '#15803d' : '#b45309';
        const badgeBg = isSuccess ? '#dcfce7' : '#fef3c7';

        const fotoHtml = s.foto 
            ? `<img src="${s.foto}" style="width:52px; height:52px; border-radius:14px; object-fit:cover; border:2px solid ${isSuccess ? '#86efac' : '#fde68a'}; flex-shrink:0;">`
            : `<div style="width:52px; height:52px; border-radius:14px; background:${badgeBg}; color:${badgeColor}; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.1rem; flex-shrink:0;">${s.nama.charAt(0)}</div>`;

        let warningBanner = '';
        if (warning) {
            warningBanner = `
                <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:6px 10px; margin-top:8px; font-size:0.78rem; color:#b91c1c; font-weight:700; display:flex; align-items:center; gap:6px;">
                    <i data-lucide="alert-triangle" style="width:14px;height:14px;flex-shrink:0;"></i> ${warning}
                </div>
            `;
        }

        resultArea.className = cardClass;
        resultArea.innerHTML = `
            <div style="display:flex; gap:14px; align-items:center;">
                ${fotoHtml}
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:6px;">
                        <div style="font-weight:800; font-size:1.05rem; color:var(--z-text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            ${s.nama}
                        </div>
                        <span style="background:${badgeBg}; color:${badgeColor}; font-weight:800; font-size:0.75rem; padding:3px 8px; border-radius:6px; flex-shrink:0;">
                            ${s.status}
                        </span>
                    </div>
                    <div style="font-size:0.8rem; color:var(--z-text-muted); margin-top:2px;">
                        NIS: <strong>${s.nis}</strong> &bull; Kelas ${s.kelas}
                    </div>
                    <div style="display:flex; align-items:center; gap:8px; margin-top:4px; font-size:0.82rem;">
                        <span style="background:#e0f2fe; color:#0369a1; font-weight:700; padding:2px 8px; border-radius:6px;">
                            ${s.ruang}
                        </span>
                        <span style="background:#f0fdf4; color:#15803d; font-weight:800; padding:2px 8px; border-radius:6px; border:1px solid #bbf7d0;">
                            MEJA ${s.nomor_meja}
                        </span>
                        <span style="font-size:0.75rem; color:var(--z-text-muted);">
                            Pukul ${s.jam_scan}
                        </span>
                    </div>
                </div>
            </div>
            ${warningBanner}
            <div style="font-size:0.78rem; font-weight:600; color:${isSuccess ? '#16a34a' : '#d97706'}; margin-top:8px;">
                ${message}
            </div>
        `;
        lucide.createIcons();
    }

    function renderErrorCard(message) {
        const resultArea = document.getElementById('scanResultArea');
        resultArea.className = 'scan-result-card error';
        resultArea.innerHTML = `
            <div style="display:flex; gap:12px; align-items:center;">
                <div style="width:44px; height:44px; border-radius:12px; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i data-lucide="alert-circle" style="width:24px; height:24px;"></i>
                </div>
                <div>
                    <div style="font-weight:800; font-size:0.95rem; color:#b91c1c;">Gagal Memverifikasi</div>
                    <div style="font-size:0.82rem; color:#475569; margin-top:2px;">${message}</div>
                </div>
            </div>
        `;
        lucide.createIcons();
    }

    // Update DOM row after successful scan
    function updateSiswaRowHadir(s) {
        const row = document.getElementById('siswaRow_' + s.siswa_id);
        if (row) {
            row.classList.remove('is-belum');
            row.classList.add('is-hadir');
            row.setAttribute('data-status', 'hadir');

            const badge = document.getElementById('badgeStatus_' + s.siswa_id);
            if (badge) {
                badge.innerHTML = `
                    <span class="pill pill-green" style="font-size:0.75rem; padding:4px 10px; display:inline-flex; align-items:center; gap:4px;">
                        <i data-lucide="check" style="width:12px;height:12px;"></i>
                        HADIR (${s.jam_scan.substring(0, 5)} WIB)
                    </span>
                `;
            }

            const btnArea = document.getElementById('btnAction_' + s.siswa_id);
            if (btnArea && s.id) {
                btnArea.innerHTML = `
                    <button type="button" 
                            onclick="cancelPresensi(${s.id}, ${s.siswa_id})" 
                            class="btn btn-ghost btn-sm" 
                            title="Batalkan Presensi" 
                            style="color:#ef4444; padding:4px 8px; border-radius:6px;">
                        <i data-lucide="x" style="width:14px;height:14px;"></i>
                    </button>
                `;
            }
            lucide.createIcons();
        }
    }

    function refreshCounters(deltaHadir = 0) {
        const elHadir = document.getElementById('statHadir');
        const elBelum = document.getElementById('statBelum');
        const elTotal = document.getElementById('statTotal');
        const elPersen = document.getElementById('statPersen');

        let hadir = parseInt(elHadir.innerText) + deltaHadir;
        let total = parseInt(elTotal.innerText);
        let belum = Math.max(0, total - hadir);
        let persen = total > 0 ? Math.round((hadir / total) * 100) : 0;

        elHadir.innerText = hadir;
        elBelum.innerText = belum;
        elPersen.innerText = persen + '%';

        document.getElementById('tabCountHadir').innerText = hadir;
        document.getElementById('tabCountBelum').innerText = belum;
    }

    // Manual Presensi & Cancel Presensi
    function manualPresensi(siswaId, nama) {
        Swal.fire({
            title: 'Tandai Hadir Manual?',
            text: `Apakah Anda ingin menandai ${nama} hadir dalam ujian sesi ini?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Tandai Hadir',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#059669'
        }).then((res) => {
            if (res.isConfirmed) {
                fetch('<?php echo Helper::url('/asesmen/scan/manual'); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        siswa_id: siswaId,
                        jadwal_id: currentJadwalId,
                        status: 'hadir',
                        keterangan: 'Hadir Manual Pengawas'
                    })
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message, timer: 1200, showConfirmButton: false });
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: d.message });
                    }
                });
            }
        });
    }

    function cancelPresensi(presensiId, siswaId) {
        Swal.fire({
            title: 'Batalkan Presensi?',
            text: 'Presensi siswa ini akan dihapus dan status dikembalikan menjadi belum hadir.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tutup',
            confirmButtonColor: '#ef4444'
        }).then((res) => {
            if (res.isConfirmed) {
                fetch('<?php echo Helper::url('/asesmen/scan/reset'); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: presensiId })
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message, timer: 1200, showConfirmButton: false });
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: d.message });
                    }
                });
            }
        });
    }

    // Filter Tabs (Semua, Hadir, Belum)
    function filterTab(status, btn) {
        activeFilter = status;
        document.querySelectorAll('.btnTab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const rows = document.querySelectorAll('.siswa-card-row');
        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            if (status === 'all' || rowStatus === status) {
                row.style.display = 'flex';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Search Peserta in list
    function searchPeserta(keyword) {
        keyword = keyword.toLowerCase().trim();
        const rows = document.querySelectorAll('.siswa-card-row');
        rows.forEach(row => {
            const nama = row.getAttribute('data-nama') || '';
            const nis = row.getAttribute('data-nis') || '';
            const meja = row.getAttribute('data-meja') || '';
            const status = row.getAttribute('data-status') || '';

            const matchTab = (activeFilter === 'all' || status === activeFilter);
            const matchSearch = (!keyword || nama.includes(keyword) || nis.includes(keyword) || meja.includes(keyword));

            if (matchTab && matchSearch) {
                row.style.display = 'flex';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>