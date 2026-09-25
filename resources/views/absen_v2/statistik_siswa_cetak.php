<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Statistik Kehadiran Siswa</title>
    <?php $logoUrl = !empty($inst['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $inst['logo']) : \App\Core\Helper::url('/assets/images/logo.png'); ?>
    <link rel="icon" type="image/png" href="<?php echo $logoUrl; ?>">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 20px;
            color: #0f172a;
        }
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .kop-logo {
            width: 80px;
            margin-right: 20px;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-text h2 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .kop-text h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .kop-text p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #334155;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 20px;
            text-decoration: underline;
        }
        .info {
            margin-bottom: 10px;
            font-size: 13px;
        }
        .summary-boxes {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        .s-box {
            flex: 1;
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }
        .s-box .num {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .s-box .lbl {
            font-size: 10px;
            text-transform: uppercase;
        }
        .insight-box {
            border: 1px solid #000;
            padding: 10px 15px;
            background: #f8fafc;
            margin-bottom: 15px;
            font-size: 12px;
            line-height: 1.5;
        }
        .ttd-box {
            width: 100%;
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            font-size: 13px;
        }
        .ttd {
            text-align: center;
            width: 250px;
        }
        .ttd .jabatan {
            margin-bottom: 60px;
        }
        .ttd .nama {
            font-weight: bold;
            text-decoration: underline;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            .insight-box { background: #f8fafc !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page { margin: 10mm; }
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop-surat" style="border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px;">
        <?php $logoUrl = !empty($inst['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $inst['logo']) : \App\Core\Helper::url('/assets/images/logo.png'); ?>
        <img src="<?php echo $logoUrl; ?>" class="kop-logo" alt="Logo" style="width: 80px; height: 80px; object-fit: contain;">
        <div class="kop-text" style="flex: 1; text-align: center; padding-right: 90px;">
            <h1 style="margin: 0; font-size: <?php echo intval($inst['kop_font_yayasan'] ?? 14); ?>px; text-transform: uppercase; font-weight: bold; font-family: 'Times New Roman', Times, serif; color: #000;">
                <?php echo htmlspecialchars($inst['yayasan'] ?? 'YAYASAN PENDIDIKAN ISLAM ROUDLOTUS SHOLIHIN GUNUNG TERANG'); ?>
            </h1>
            <h2 style="margin: 3px 0; font-size: <?php echo intval($inst['kop_font_nama'] ?? 18); ?>px; font-weight: bold; font-family: 'Times New Roman', Times, serif; color: #000;">
                <?php echo htmlspecialchars($inst['nama'] ?? 'MTs ROUDLOTUS SHOLIHIN'); ?>
            </h2>
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
            <p style="margin: 3px 0; font-size: 11px; font-family: 'Times New Roman', Times, serif; color: #000;">
                <?php echo htmlspecialchars($fullAddress); ?>
            </p>
            <p style="margin: 3px 0; font-size: 11px; font-family: 'Times New Roman', Times, serif; color: #000;">
                Website: <?php echo htmlspecialchars($inst['website'] ?? ''); ?> | Email: <?php echo htmlspecialchars($inst['email'] ?? ''); ?>
            </p>
        </div>
    </div>

    <!-- TITLE -->
    <div class="title">STATISTIK KEHADIRAN SISWA</div>

    <!-- INFO -->
    <div class="info">
        <strong>Periode Tanggal:</strong> <?php echo date('d M Y', strtotime($start_date)); ?> s.d <?php echo date('d M Y', strtotime($end_date)); ?><br>
        <strong>Dicetak Tanggal:</strong> <?php echo date('d M Y H:i'); ?><br>
    </div>

    <!-- SUMMARY BOXES -->
    <div class="summary-boxes">
        <div class="s-box">
            <div class="num"><?php echo number_format($totalHadir); ?></div>
            <div class="lbl">Total Hadir</div>
        </div>
        <div class="s-box">
            <div class="num"><?php echo number_format($totalSakit); ?></div>
            <div class="lbl">Total Sakit</div>
        </div>
        <div class="s-box">
            <div class="num"><?php echo number_format($totalIzin); ?></div>
            <div class="lbl">Total Izin</div>
        </div>
        <div class="s-box">
            <div class="num"><?php echo number_format($totalAlpa); ?></div>
            <div class="lbl">Total Alpa</div>
        </div>
        <div class="s-box">
            <div class="num"><?php echo number_format($totalBolos); ?></div>
            <div class="lbl">Total Bolos</div>
        </div>
    </div>

    <!-- INSIGHT BOX -->
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
    <?php if ($totalRecords > 0): ?>
    <div class="insight-box">
        <strong>Kesimpulan:</strong><br>
        Dari total <strong><?php echo number_format($totalRecords); ?></strong> catatan kehadiran, tingkat kehadiran siswa mencapai <strong><?php echo $persentaseHadir; ?>%</strong>. 
        Siswa tidak hadir mencapai <strong><?php echo $persentaseTidakHadir; ?>%</strong> 
        <?php if ($persentaseTidakHadir > 0): ?>
        dengan alasan terbanyak didominasi oleh kategori <strong><?php echo $alasanTerbanyak; ?></strong> (<?php echo number_format($jumlahTerbanyak); ?> catatan).
        <?php else: ?>
        yang berarti rekor kehadiran sangat sempurna.
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- CHARTS -->
    <div style="display:flex; gap:20px; margin-bottom:30px; height: 350px;">
        <!-- LINE CHART (TREND) -->
        <div style="flex:2.5; background:#fff; border:1px solid #000; padding:15px; position:relative;">
            <div style="font-size:14px; font-weight:bold; margin-bottom:10px; text-align:center;">Trend Kehadiran Harian</div>
            <div style="position:relative; width:100%; height:280px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- DOUGHNUT CHART (PROPORTION) -->
        <div style="flex:1; background:#fff; border:1px solid #000; padding:15px; display:flex; flex-direction:column; align-items:center;">
            <div style="font-size:14px; font-weight:bold; margin-bottom:10px; text-align:center;">Proporsi Status</div>
            <div style="position:relative; width:180px; height:180px;">
                <canvas id="proportionChart"></canvas>
            </div>
            <div style="margin-top:20px; width:100%; font-size:12px; font-weight:bold; display:flex; flex-direction:column; gap:8px;">
                <div style="display:flex; justify-content:space-between;"><span>Hadir</span> <span><?php echo number_format($totalHadir); ?></span></div>
                <div style="display:flex; justify-content:space-between;"><span>Sakit</span> <span><?php echo number_format($totalSakit); ?></span></div>
                <div style="display:flex; justify-content:space-between;"><span>Izin</span> <span><?php echo number_format($totalIzin); ?></span></div>
                <div style="display:flex; justify-content:space-between;"><span>Alpa</span> <span><?php echo number_format($totalAlpa); ?></span></div>
                <div style="display:flex; justify-content:space-between;"><span>Bolos</span> <span><?php echo number_format($totalBolos); ?></span></div>
            </div>
        </div>
    </div>

    <!-- TTD -->
    <div class="ttd-box">
        <div class="ttd">
            <div style="margin-bottom:5px;">Gunung Terang, <?php echo date('d F Y'); ?></div>
            <div class="jabatan">Kepala Madrasah</div>
            <div class="nama"><?php echo htmlspecialchars($inst['nama_kepala'] ?? '__________________________'); ?></div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php
$trendDates = [];
$trendHadirArr = [];
$trendTidakHadirArr = [];
foreach ($trendData as $row) {
    $trendDates[] = date('d M', strtotime($row['tanggal']));
    $trendHadirArr[] = $row['hadir'];
    $trendTidakHadirArr[] = $row['tidak_hadir'];
}
?>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.font.family = 'Inter';
    Chart.defaults.color = '#000';
    
    // 1. Line Chart
    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($trendDates); ?>,
            datasets: [
                {
                    label: 'Hadir',
                    data: <?php echo json_encode($trendHadirArr); ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#10b981',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Tidak Hadir',
                    data: <?php echo json_encode($trendTidakHadirArr); ?>,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#ef4444',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false, // Penting untuk print
            plugins: {
                legend: { position: 'top', labels: { color: '#000', font: { weight: 'bold' } } }
            },
            scales: {
                x: { ticks: { color: '#000' } },
                y: { ticks: { color: '#000', stepSize: 1 } }
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
                borderWidth: 1,
                borderColor: '#fff'
            }]
        },
        options: {
            cutout: '70%',
            responsive: true,
            maintainAspectRatio: false,
            animation: false, // Penting untuk print
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Trigger print setelah chart digambar
    setTimeout(function() {
        window.print();
    }, 500);
});
</script>
</body>
</html>
