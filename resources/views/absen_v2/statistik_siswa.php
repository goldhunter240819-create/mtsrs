<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" onclick="zToggleSidebar()" style="background:none;border:none;cursor:pointer;color:#64748b;">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <span style="font-weight:800;letter-spacing:1px;font-size:0.8rem;color:#10b981;">ABSEN V2</span>
                    <i data-lucide="chevron-right" style="width:14px;color:#cbd5e1;"></i>
                    <span style="font-weight:700;color:#1e293b;">Statistik Kehadiran</span>
                </div>
            </div>
            <div class="z-header-right" style="display:flex;align-items:center;gap:12px;">
                <div style="width:36px;height:36px;background:#10b981;color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;"><?php echo $initial ?? 'M'; ?></div>
                <div>
                    <div style="font-weight:800;font-size:0.9rem;color:#0f172a;"><?php echo $nama ?? 'Admin'; ?></div>
                </div>
            </div>
        </header>

        <div class="z-scroll">
            <div style="padding: 2rem 2rem 1.5rem;">
                <div style="background:white; border-radius:16px; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02); overflow:hidden;">
                    <div class="dashboard-header" style="padding: 1.5rem 2rem 0;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                            <div>
                                <h1 style="font-size:1.8rem; font-weight:800; color:#0f172a; margin:0 0 0.25rem;">Statistik Kehadiran Siswa</h1>
                                <p style="color:#64748b; margin:0; font-size:0.95rem;">Laporan grafik kehadiran siswa berdasarkan rentang tanggal</p>
                            </div>
                            <div>
                                <a href="/absen/statistik-siswa-cetak?start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>" target="_blank" style="display:flex; align-items:center; gap:8px; padding:0.6rem 1.2rem; background:white; border:1px solid #cbd5e1; border-radius:10px; color:#475569; font-weight:700; cursor:pointer; font-size:0.85rem; box-shadow:0 2px 4px rgba(0,0,0,0.02); transition:all 0.2s; text-decoration:none;">
                                    <i data-lucide="printer" style="width:18px;height:18px;"></i> Cetak Laporan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- FILTER -->
                    <div style="padding: 1.5rem 2rem;">
                        <form method="GET" action="/absen/statistik-siswa" style="display:flex; gap:1rem; align-items:flex-end;">
                            <div style="flex:1;">
                                <label style="display:block; font-size:0.75rem; font-weight:700; color:#64748b; margin-bottom:0.5rem;">Tanggal Mulai</label>
                                <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" class="form-control" style="width:100%; padding:0.6rem 1rem; border-radius:10px; border:1px solid #e2e8f0; background:#f8fafc;" required>
                            </div>
                            <div style="flex:1;">
                                <label style="display:block; font-size:0.75rem; font-weight:700; color:#64748b; margin-bottom:0.5rem;">Tanggal Akhir</label>
                                <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" class="form-control" style="width:100%; padding:0.6rem 1rem; border-radius:10px; border:1px solid #e2e8f0; background:#f8fafc;" required>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary" style="padding:0.6rem 1.5rem; height:42px; display:flex; align-items:center; gap:0.5rem; background:#10b981; border:none; border-radius:10px; color:white; font-weight:600; cursor:pointer; transition:all 0.2s;">
                                    <i data-lucide="filter" style="width:18px;height:18px;"></i> Terapkan Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

<!-- SUMMARY CARDS -->
<div style="display:grid; grid-template-columns:repeat(5, 1fr); gap:1rem; padding:0 2rem 1.5rem;">
    <div style="background:white; padding:1.5rem; border-radius:16px; border:1px solid #f1f5f9; text-align:center; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);">
        <div style="font-size:2rem; font-weight:900; color:#10b981; line-height:1;"><?php echo number_format($totalHadir); ?></div>
        <div style="font-size:0.7rem; font-weight:700; color:#64748b; text-transform:uppercase; margin-top:8px; letter-spacing:0.5px;">Total Hadir</div>
    </div>
    <div style="background:white; padding:1.5rem; border-radius:16px; border:1px solid #f1f5f9; text-align:center; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);">
        <div style="font-size:2rem; font-weight:900; color:#3b82f6; line-height:1;"><?php echo number_format($totalSakit); ?></div>
        <div style="font-size:0.7rem; font-weight:700; color:#64748b; text-transform:uppercase; margin-top:8px; letter-spacing:0.5px;">Total Sakit</div>
    </div>
    <div style="background:white; padding:1.5rem; border-radius:16px; border:1px solid #f1f5f9; text-align:center; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);">
        <div style="font-size:2rem; font-weight:900; color:#a855f7; line-height:1;"><?php echo number_format($totalIzin); ?></div>
        <div style="font-size:0.7rem; font-weight:700; color:#64748b; text-transform:uppercase; margin-top:8px; letter-spacing:0.5px;">Total Izin</div>
    </div>
    <div style="background:white; padding:1.5rem; border-radius:16px; border:1px solid #f1f5f9; text-align:center; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);">
        <div style="font-size:2rem; font-weight:900; color:#ef4444; line-height:1;"><?php echo number_format($totalAlpa); ?></div>
        <div style="font-size:0.7rem; font-weight:700; color:#64748b; text-transform:uppercase; margin-top:8px; letter-spacing:0.5px;">Total Alpa</div>
    </div>
    <div style="background:white; padding:1.5rem; border-radius:16px; border:1px solid #f1f5f9; text-align:center; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);">
        <div style="font-size:2rem; font-weight:900; color:#475569; line-height:1;"><?php echo number_format($totalBolos); ?></div>
        <div style="font-size:0.7rem; font-weight:700; color:#64748b; text-transform:uppercase; margin-top:8px; letter-spacing:0.5px;">Total Bolos</div>
    </div>
</div>

<?php
$totalRecords = $totalHadir + $totalSakit + $totalIzin + $totalAlpa + $totalBolos;
$persentaseHadir = $totalRecords > 0 ? round(($totalHadir / $totalRecords) * 100, 1) : 0;
$persentaseTidakHadir = $totalRecords > 0 ? round((($totalSakit + $totalIzin + $totalAlpa + $totalBolos) / $totalRecords) * 100, 1) : 0;

$arrTidakHadir = [
    'Sakit' => $totalSakit,
    'Izin' => $totalIzin,
    'Alpa' => $totalAlpa,
    'Bolos' => $totalBolos
];
arsort($arrTidakHadir);
$alasanTerbanyak = key($arrTidakHadir);
$jumlahTerbanyak = current($arrTidakHadir);
?>
<!-- INSIGHTS -->
<?php if ($totalRecords > 0): ?>
<div style="padding: 0 2rem 1.5rem;">
    <div style="background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-left:4px solid #3b82f6; padding:1.25rem; border-radius:12px; display:flex; gap:1rem; align-items:flex-start;">
        <div style="width:40px; height:40px; border-radius:10px; background:#eff6ff; color:#3b82f6; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <i data-lucide="lightbulb" style="width:20px; height:20px;"></i>
        </div>
        <div>
            <div style="font-size:0.85rem; font-weight:800; color:#0f172a; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.5px;">Kesimpulan Periode Ini</div>
            <div style="font-size:0.9rem; color:#475569; line-height:1.6;">
                Dari total <strong><?php echo number_format($totalRecords); ?></strong> catatan kehadiran, tingkat kehadiran siswa mencapai <strong style="color:#10b981;"><?php echo $persentaseHadir; ?>%</strong>. 
                Siswa tidak hadir mencapai <strong style="color:#ef4444;"><?php echo $persentaseTidakHadir; ?>%</strong> 
                <?php if ($persentaseTidakHadir > 0): ?>
                dengan alasan terbanyak didominasi oleh kategori <strong><?php echo $alasanTerbanyak; ?></strong> (<?php echo number_format($jumlahTerbanyak); ?> catatan).
                <?php else: ?>
                yang berarti rekor kehadiran sangat sempurna.
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- CHARTS -->
<div style="display:grid; grid-template-columns:2.5fr 1fr; gap:1.5rem; padding:0 2rem 2rem;">
    <!-- LINE CHART (TREND) -->
    <div style="background:white; border-radius:20px; padding:1.5rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);">
        <div style="font-size:1rem; font-weight:800; color:#0f172a; margin-bottom:1.5rem; display:flex; align-items:center; gap:8px;">
            <i data-lucide="trending-up" style="width:20px; color:#3b82f6;"></i> Trend Kehadiran Harian
        </div>
        <div style="position:relative; width:100%; height:300px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- DOUGHNUT CHART (PROPORTION) -->
    <div style="background:white; border-radius:20px; padding:1.5rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02); display:flex; flex-direction:column; align-items:center;">
        <div style="font-size:1rem; font-weight:800; color:#0f172a; margin-bottom:1.5rem; width:100%; text-align:left; display:flex; align-items:center; gap:8px;">
            <i data-lucide="pie-chart" style="width:20px; color:#10b981;"></i> Proporsi Status
        </div>
        
        <div style="position:relative; width:220px; height:220px;">
            <canvas id="proportionChart"></canvas>
            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); text-align:center;">
                <div style="font-size:1.8rem; font-weight:900; color:#0f172a; line-height:1;">
                    <?php echo number_format($totalHadir + $totalSakit + $totalIzin + $totalAlpa + $totalBolos); ?>
                </div>
                <div style="font-size:0.6rem; font-weight:700; color:#64748b; margin-top:4px; text-transform:uppercase; letter-spacing:1px;">Total Record</div>
            </div>
        </div>
        
        <div style="margin-top:2rem; width:100%; font-size:0.8rem; font-weight:600; color:#64748b; display:flex; flex-direction:column; gap:8px;">
            <div style="display:flex; justify-content:space-between;"><span style="color:#10b981">● Hadir</span> <span><?php echo number_format($totalHadir); ?></span></div>
            <div style="display:flex; justify-content:space-between;"><span style="color:#3b82f6">● Sakit</span> <span><?php echo number_format($totalSakit); ?></span></div>
            <div style="display:flex; justify-content:space-between;"><span style="color:#a855f7">● Izin</span> <span><?php echo number_format($totalIzin); ?></span></div>
            <div style="display:flex; justify-content:space-between;"><span style="color:#ef4444">● Alpa</span> <span><?php echo number_format($totalAlpa); ?></span></div>
            <div style="display:flex; justify-content:space-between;"><span style="color:#475569">● Bolos</span> <span><?php echo number_format($totalBolos); ?></span></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Line Chart
    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($trendDates); ?>,
            datasets: [
                {
                    label: 'Hadir',
                    data: <?php echo json_encode($trendHadir); ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Tidak Hadir',
                    data: <?php echo json_encode($trendTidakHadir); ?>,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { family: 'Inter', weight: '600' }, color: '#475569' }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { family: 'Inter', size: 13 },
                    bodyFont: { family: 'Inter', size: 12 },
                    padding: 10,
                    cornerRadius: 8
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Inter' }, color: '#94a3b8' }
                },
                y: {
                    border: { display: false },
                    grid: { color: '#f1f5f9' },
                    ticks: { font: { family: 'Inter' }, color: '#94a3b8', stepSize: 1 }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });

    // 2. Proportion Doughnut Chart
    const ctxProportion = document.getElementById('proportionChart').getContext('2d');
    new Chart(ctxProportion, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Sakit', 'Izin', 'Alpa', 'Bolos'],
            datasets: [{
                data: [
                    <?php echo $totalHadir; ?>,
                    <?php echo $totalSakit; ?>,
                    <?php echo $totalIzin; ?>,
                    <?php echo $totalAlpa; ?>,
                    <?php echo $totalBolos; ?>
                ],
                backgroundColor: ['#10b981', '#3b82f6', '#a855f7', '#ef4444', '#475569'],
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
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { family: 'Inter', size: 13 },
                    bodyFont: { family: 'Inter', size: 13 },
                    padding: 12,
                    cornerRadius: 8,
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
        </div> <!-- z-scroll -->
    </div> <!-- z-main -->
</div> <!-- siakad-container -->
