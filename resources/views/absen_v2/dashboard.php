<style>
    /* Menggunakan tema Zamrud (Green) untuk modul Absensi */
    .z-avatar-premium { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .header-breadcrumb .parent { color: #10b981; }
    
    .welcome-hero {
        background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
        border-bottom: 5px solid #10b981;
    }
    .hero-badge { background: rgba(16,185,129,0.2); color: #10b981; border-color: rgba(16,185,129,0.3); }
    .hero-btn.primary { background: #10b981; box-shadow: 0 10px 20px rgba(16,185,129,0.2); }
    .hero-bg-icon { color: #10b981; opacity: 0.05; }
    
    .teal .stat-icon { background: #f0fdf4; color: #10b981; }
    
    /* Progress Circle */
    .progress-circle {
        width: 80px; height: 80px; border-radius: 50%;
        background: conic-gradient(#10b981 calc(var(--p) * 1%), #e2e8f0 0);
        display: flex; align-items: center; justify-content: center; position: relative;
    }
    .progress-circle::after {
        content: ''; width: 68px; height: 68px; border-radius: 50%;
        background: white; position: absolute;
    }
    .progress-val { position: relative; z-index: 1; font-weight: 800; font-size: 1.2rem; color: #0f172a; }

    .stats-section-label {
        font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;
        color: #94a3b8; padding: 0 2rem; margin-bottom: 0.5rem; margin-top: 0.5rem;
    }
    .stats-section-label.blue { color: #3b82f6; }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()" style="background:none;border:none;cursor:pointer;color:#64748b;">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <span class="parent" style="font-weight:800;letter-spacing:1px;font-size:0.8rem;">ABSEN V2</span>
                    <i data-lucide="chevron-right" class="sep" style="width:14px;color:#cbd5e1;"></i>
                    <span class="current" style="font-weight:700;color:#1e293b;">Dashboard Central</span>
                </div>
            </div>
            <div class="z-header-right" style="display:flex;align-items:center;gap:2rem;">
                <div class="header-status-pill" style="display:flex;align-items:center;gap:8px;background:#f0fdf4;padding:6px 14px;border-radius:100px;border:1px solid #dcfce7;font-size:0.75rem;font-weight:700;color:#15803d;">
                    <div class="dot pulse" style="width:8px;height:8px;background:#22c55e;border-radius:50%;"></div>
                    <span>Scanner Online</span>
                </div>
                <div class="z-header-user" style="display:flex;align-items:center;gap:12px;cursor:pointer;">
                    <div class="z-avatar-premium" style="width:36px;height:36px;color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;box-shadow:0 4px 12px rgba(16,185,129,0.2);"><?php echo $initial; ?></div>
                    <div class="z-user-info">
                        <div class="name" style="font-weight:800;font-size:0.9rem;color:#0f172a;"><?php echo $nama; ?></div>
                        <div class="role" style="font-size:0.75rem;color:#64748b;">Super Administrator</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="z-scroll">
            <!-- Welcome Hero -->
            <div class="welcome-hero" style="margin: 1.5rem 2rem; border-radius: 20px; padding: 2rem 2.5rem; position: relative; overflow: hidden; color: white; display: flex; justify-content: space-between; align-items: center;">
                <div class="hero-content" style="position:relative; z-index:2; max-width:550px;">
                    <div class="hero-badge" style="padding:4px 12px; border-radius:100px; font-weight:800; font-size:0.7rem; display:inline-block; margin-bottom:.8rem; border-width:1px; border-style:solid;">ABSENSI ENGINE V2</div>
                    <h1 style="font-size:1.8rem; font-weight:800; margin:0 0 .5rem; letter-spacing:-0.5px; line-height:1.15;">Pusat Kontrol Absensi <span class="wave">🎯</span></h1>
                    <p style="font-size:.92rem; opacity:0.8; line-height:1.5; margin-bottom:1.5rem;">Pantau kehadiran <b>siswa & guru</b> secara real-time. Scan QR Code — otomatis deteksi siapa yang absen dan catat jam masuk / pulang guru.</p>
                    <div class="hero-actions" style="display:flex; gap:.75rem;">
                        <a href="/absen/rekap-harian" class="hero-btn primary" style="text-decoration:none; padding:.7rem 1.5rem; border-radius:12px; font-weight:700; display:flex; align-items:center; gap:8px; font-size:.85rem; transition:0.3s; color:white;">
                            <i data-lucide="calendar-check"></i> Lihat Rekap Harian
                        </a>
                    </div>
                </div>
                <div class="hero-bg-icon" style="position:absolute; right:-20px; bottom:-20px; font-size:140px; transform:rotate(-10deg);">
                    <i data-lucide="qr-code" style="width:180px;height:180px;"></i>
                </div>
            </div>

            <!-- === STATS BERSAMA (CHART) === -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; padding:0 2rem 1.5rem; margin-top: 1rem;">
                
                <!-- KARTU SISWA -->
                <div style="background:white; border-radius:20px; padding:1.5rem; border:1px solid #f1f5f9; display:flex; flex-direction:column; align-items:center; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);">
                    <div style="font-size:0.85rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:#10b981; margin-bottom:1.5rem; width:100%; text-align:left; display:flex; align-items:center; gap:8px;">
                        <i data-lucide="pie-chart" style="width:18px;"></i> Statistik Kehadiran Siswa
                    </div>
                    
                    <div style="position:relative; width:220px; height:220px;">
                        <canvas id="chartSiswa"></canvas>
                        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); text-align:center;">
                            <div style="font-size:1.8rem; font-weight:900; color:#0f172a; line-height:1;"><?php echo number_format($totalSiswa); ?></div>
                            <div style="font-size:0.65rem; font-weight:700; color:#64748b; margin-top:4px; text-transform:uppercase; letter-spacing:1px;">Total Siswa</div>
                        </div>
                    </div>
                    
                    <!-- Rincian bawah -->
                    <?php $belumAbsenSiswa = max(0, $totalSiswa - $hadir - $sakit - $izin - $alpa - $bolos); ?>
                    <div style="width:100%; margin-top:2rem; display:grid; grid-template-columns:repeat(3, 1fr); gap:0.75rem;">
                        <div style="background:#f8fafc; padding:0.75rem; border-radius:12px; text-align:center; border:1px solid #e2e8f0;">
                            <div style="font-size:1.1rem; font-weight:800; color:#10b981;"><?php echo number_format($hadir); ?></div>
                            <div style="font-size:0.6rem; font-weight:700; color:#64748b; text-transform:uppercase; margin-top:4px;">Hadir</div>
                        </div>
                        <div style="background:#fef3c7; padding:0.75rem; border-radius:12px; text-align:center; border:1px solid #fde68a;">
                            <div style="font-size:1.1rem; font-weight:800; color:#d97706;"><?php echo number_format($belumAbsenSiswa); ?></div>
                            <div style="font-size:0.6rem; font-weight:700; color:#b45309; text-transform:uppercase; margin-top:4px;">Belum Absen</div>
                        </div>
                        <div style="background:#eff6ff; padding:0.75rem; border-radius:12px; text-align:center; border:1px solid #bfdbfe;">
                            <div style="font-size:1.1rem; font-weight:800; color:#3b82f6;"><?php echo number_format($sakit); ?></div>
                            <div style="font-size:0.6rem; font-weight:700; color:#1d4ed8; text-transform:uppercase; margin-top:4px;">Sakit</div>
                        </div>
                        <div style="background:#f3e8ff; padding:0.75rem; border-radius:12px; text-align:center; border:1px solid #e9d5ff;">
                            <div style="font-size:1.1rem; font-weight:800; color:#a855f7;"><?php echo number_format($izin); ?></div>
                            <div style="font-size:0.6rem; font-weight:700; color:#7e22ce; text-transform:uppercase; margin-top:4px;">Izin</div>
                        </div>
                        <div style="background:#fef2f2; padding:0.75rem; border-radius:12px; text-align:center; border:1px solid #fecaca;">
                            <div style="font-size:1.1rem; font-weight:800; color:#ef4444;"><?php echo number_format($alpa); ?></div>
                            <div style="font-size:0.6rem; font-weight:700; color:#b91c1c; text-transform:uppercase; margin-top:4px;">Alpa</div>
                        </div>
                        <div style="background:#f1f5f9; padding:0.75rem; border-radius:12px; text-align:center; border:1px solid #cbd5e1;">
                            <div style="font-size:1.1rem; font-weight:800; color:#475569;"><?php echo number_format($bolos); ?></div>
                            <div style="font-size:0.6rem; font-weight:700; color:#334155; text-transform:uppercase; margin-top:4px;">Bolos</div>
                        </div>
                    </div>
                </div>

                <!-- KARTU GURU -->
                <div style="background:white; border-radius:20px; padding:1.5rem; border:1px solid #f1f5f9; display:flex; flex-direction:column; align-items:center; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);">
                    <div style="font-size:0.85rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:#3b82f6; margin-bottom:1.5rem; width:100%; text-align:left; display:flex; align-items:center; gap:8px;">
                        <i data-lucide="pie-chart" style="width:18px;"></i> Statistik Kehadiran Guru
                    </div>
                    
                    <div style="position:relative; width:220px; height:220px;">
                        <canvas id="chartGuru"></canvas>
                        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); text-align:center;">
                            <div style="font-size:1.8rem; font-weight:900; color:#0f172a; line-height:1;"><?php echo number_format($totalGuru); ?></div>
                            <div style="font-size:0.65rem; font-weight:700; color:#64748b; margin-top:4px; text-transform:uppercase; letter-spacing:1px;">Total Guru</div>
                        </div>
                    </div>
                    
                    <!-- Rincian bawah -->
                    <?php $belumAbsenGuru = max(0, $totalGuru - $hadirGuru - $tidakHadirGuru); ?>
                    <div style="width:100%; margin-top:2rem; display:grid; grid-template-columns:repeat(3, 1fr); gap:0.75rem;">
                        <div style="background:#f8fafc; padding:0.75rem; border-radius:12px; text-align:center; border:1px solid #e2e8f0;">
                            <div style="font-size:1.1rem; font-weight:800; color:#10b981;"><?php echo number_format($hadirGuru); ?></div>
                            <div style="font-size:0.6rem; font-weight:700; color:#64748b; text-transform:uppercase; margin-top:4px;">Hadir</div>
                        </div>
                        <div style="background:#fef3c7; padding:0.75rem; border-radius:12px; text-align:center; border:1px solid #fde68a;">
                            <div style="font-size:1.1rem; font-weight:800; color:#d97706;"><?php echo number_format($belumAbsenGuru); ?></div>
                            <div style="font-size:0.6rem; font-weight:700; color:#b45309; text-transform:uppercase; margin-top:4px;">Belum Absen</div>
                        </div>
                        <div style="background:#eff6ff; padding:0.75rem; border-radius:12px; text-align:center; border:1px solid #bfdbfe;">
                            <div style="font-size:1.1rem; font-weight:800; color:#3b82f6;"><?php echo number_format($sakitGuru); ?></div>
                            <div style="font-size:0.6rem; font-weight:700; color:#1d4ed8; text-transform:uppercase; margin-top:4px;">Sakit</div>
                        </div>
                        <div style="background:#f3e8ff; padding:0.75rem; border-radius:12px; text-align:center; border:1px solid #e9d5ff;">
                            <div style="font-size:1.1rem; font-weight:800; color:#a855f7;"><?php echo number_format($izinGuru); ?></div>
                            <div style="font-size:0.6rem; font-weight:700; color:#7e22ce; text-transform:uppercase; margin-top:4px;">Izin</div>
                        </div>
                        <div style="background:#fef2f2; padding:0.75rem; border-radius:12px; text-align:center; border:1px solid #fecaca;">
                            <div style="font-size:1.1rem; font-weight:800; color:#ef4444;"><?php echo number_format($alpaGuru); ?></div>
                            <div style="font-size:0.6rem; font-weight:700; color:#b91c1c; text-transform:uppercase; margin-top:4px;">Alpa</div>
                        </div>
                        <div style="background:#f1f5f9; padding:0.75rem; border-radius:12px; text-align:center; border:1px solid #cbd5e1;">
                            <div style="font-size:1.1rem; font-weight:800; color:#475569;"><?php echo number_format($bolosGuru); ?></div>
                            <div style="font-size:0.6rem; font-weight:700; color:#334155; text-transform:uppercase; margin-top:4px;">Bolos</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Dashboard Content -->
            <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:1.25rem; padding:0 2rem 2.5rem;">
                
                <!-- Quick Actions Panel -->
                <div style="background:white; border-radius:20px; padding:1.5rem; border:1px solid #f1f5f9;">
                    <div style="margin-bottom:1.5rem;">
                        <h3 style="margin:0; font-size:.95rem; font-weight:800; color:#1e293b; display:flex; align-items:center; gap:8px;"><i data-lucide="zap" style="color:#10b981;"></i> Fitur Cepat Absensi</h3>
                    </div>
                    
                    <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:1rem;">

                        
                        <a href="/absen/rekap-harian" style="text-decoration:none; display:flex; gap:15px; padding:1.25rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; transition:0.2s;" onmouseover="this.style.background='#eff6ff';this.style.borderColor='#dbeafe'" onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0'">
                            <div style="width:48px;height:48px;border-radius:12px;background:white;display:flex;align-items:center;justify-content:center;color:#3b82f6;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);flex-shrink:0;">
                                <i data-lucide="calendar-check"></i>
                            </div>
                            <div>
                                <div style="font-size:0.9rem;font-weight:800;color:#0f172a;margin-bottom:4px;">Rekap Harian Siswa</div>
                                <div style="font-size:0.75rem;color:#64748b;line-height:1.4;">Lihat detail absensi siswa hari ini.</div>
                            </div>
                        </a>
                        
                        <a href="/absen/rekap-harian-guru" style="text-decoration:none; display:flex; gap:15px; padding:1.25rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; transition:0.2s;" onmouseover="this.style.background='#eff6ff';this.style.borderColor='#bfdbfe'" onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0'">
                            <div style="width:48px;height:48px;border-radius:12px;background:white;display:flex;align-items:center;justify-content:center;color:#2563eb;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);flex-shrink:0;">
                                <i data-lucide="users"></i>
                            </div>
                            <div>
                                <div style="font-size:0.9rem;font-weight:800;color:#0f172a;margin-bottom:4px;">Rekap Harian Guru</div>
                                <div style="font-size:0.75rem;color:#64748b;line-height:1.4;">Jam masuk & pulang guru hari ini.</div>
                            </div>
                        </a>

                        <a href="/absen/input-absen-guru" style="text-decoration:none; display:flex; gap:15px; padding:1.25rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; transition:0.2s;" onmouseover="this.style.background='#fef3c7';this.style.borderColor='#fde68a'" onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0'">
                            <div style="width:48px;height:48px;border-radius:12px;background:white;display:flex;align-items:center;justify-content:center;color:#d97706;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);flex-shrink:0;">
                                <i data-lucide="clipboard-edit"></i>
                            </div>
                            <div>
                                <div style="font-size:0.9rem;font-weight:800;color:#0f172a;margin-bottom:4px;">Input Manual Guru</div>
                                <div style="font-size:0.75rem;color:#64748b;line-height:1.4;">Input absen guru tanpa QR scan.</div>
                            </div>
                        </a>

                        <a href="/siakad/siswa/cetak-kartu" style="text-decoration:none; display:flex; gap:15px; padding:1.25rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; transition:0.2s;" onmouseover="this.style.background='#fef3c7';this.style.borderColor='#fde68a'" onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0'">
                            <div style="width:48px;height:48px;border-radius:12px;background:white;display:flex;align-items:center;justify-content:center;color:#d97706;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);flex-shrink:0;">
                                <i data-lucide="printer"></i>
                            </div>
                            <div>
                                <div style="font-size:0.9rem;font-weight:800;color:#0f172a;margin-bottom:4px;">Cetak Kartu QR Siswa</div>
                                <div style="font-size:0.75rem;color:#64748b;line-height:1.4;">Generate kartu pelajar dengan QR V2.</div>
                            </div>
                        </a>

                        <a href="/absen/pengaturan" style="text-decoration:none; display:flex; gap:15px; padding:1.25rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; transition:0.2s;" onmouseover="this.style.background='#f3f4f6';this.style.borderColor='#e5e7eb'" onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0'">
                            <div style="width:48px;height:48px;border-radius:12px;background:white;display:flex;align-items:center;justify-content:center;color:#475569;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);flex-shrink:0;">
                                <i data-lucide="settings"></i>
                            </div>
                            <div>
                                <div style="font-size:0.9rem;font-weight:800;color:#0f172a;margin-bottom:4px;">Pengaturan Jam</div>
                                <div style="font-size:0.75rem;color:#64748b;line-height:1.4;">Tentukan batas terlambat & jam pulang.</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Log / Live Stream Panel -->
                <div style="background:white; border-radius:20px; padding:1.5rem; border:1px solid #f1f5f9;">
                    <div style="margin-bottom:1.5rem;">
                        <h3 style="margin:0; font-size:.95rem; font-weight:800; color:#1e293b; display:flex; align-items:center; gap:8px;"><i data-lucide="activity" style="color:#3b82f6;"></i> Log Aktivitas Terakhir</h3>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:1rem;">
                        <?php foreach($activities as $act): ?>
                        <div style="display:flex; gap:12px; align-items:flex-start;">
                            <div style="width:34px;height:34px;border-radius:50%;background:#f8fafc;color:<?php echo $act['color'] ?? '#10b981'; ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i data-lucide="<?php echo $act['icon']; ?>" style="width:16px;height:16px;"></i>
                            </div>
                            <div>
                                <div style="font-size:0.85rem;font-weight:700;color:#0f172a;"><?php echo htmlspecialchars($act['text']); ?></div>
                                <div style="font-size:0.75rem;color:#64748b;margin-top:2px;"><?php echo htmlspecialchars($act['sub']); ?> &bull; Baru saja</div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div style="margin-top:1rem; text-align:center; padding:1.25rem; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:12px;">
                            <i data-lucide="scan" style="width:28px;height:28px;color:#cbd5e1;margin-bottom:6px;"></i>
                            <div style="font-size:0.78rem;font-weight:700;color:#64748b;">Aktivitas scan real-time akan muncul di sini saat scanner dijalankan.</div>
                            <a href="/absen/scanner" target="_blank" style="display:inline-flex;align-items:center;gap:6px;margin-top:10px;font-size:0.75rem;font-weight:800;color:#10b981;text-decoration:none;padding:6px 14px;background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;">
                                <i data-lucide="scan-line" style="width:12px;height:12px;"></i> Buka Scanner
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data Siswa
    const ctxSiswa = document.getElementById('chartSiswa').getContext('2d');
    new Chart(ctxSiswa, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Belum Absen', 'Sakit', 'Izin', 'Alpa', 'Bolos'],
            datasets: [{
                data: [
                    <?php echo $hadir; ?>, 
                    <?php echo $belumAbsenSiswa; ?>, 
                    <?php echo $sakit; ?>, 
                    <?php echo $izin; ?>, 
                    <?php echo $alpa; ?>, 
                    <?php echo $bolos; ?>
                ],
                backgroundColor: ['#10b981', '#fcd34d', '#3b82f6', '#a855f7', '#ef4444', '#475569'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            cutout: '75%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ' + context.raw + ' orang';
                        }
                    }
                }
            }
        }
    });

    // Data Guru
    const ctxGuru = document.getElementById('chartGuru').getContext('2d');
    new Chart(ctxGuru, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Belum Absen', 'Sakit', 'Izin', 'Alpa', 'Bolos'],
            datasets: [{
                data: [
                    <?php echo $hadirGuru; ?>, 
                    <?php echo $belumAbsenGuru; ?>, 
                    <?php echo $sakitGuru; ?>, 
                    <?php echo $izinGuru; ?>, 
                    <?php echo $alpaGuru; ?>, 
                    <?php echo $bolosGuru; ?>
                ],
                backgroundColor: ['#10b981', '#fcd34d', '#3b82f6', '#a855f7', '#ef4444', '#475569'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            cutout: '75%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ' + context.raw + ' orang';
                        }
                    }
                }
            }
        }
    });
});
</script>
