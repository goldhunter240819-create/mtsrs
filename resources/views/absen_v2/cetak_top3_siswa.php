<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Juara Kehadiran</title>
    <?php $faviconUrl = !empty($inst['logo']) ? '/public/uploads/logo/'.$inst['logo'] : '/public/assets/images/logo.png'; ?>
    <link rel="icon" type="image/png" href="<?php echo $faviconUrl; ?>">
    <script src="<?php echo \App\Core\Helper::url('/public/assets/js/lucide.min.js'); ?>"></script>
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #000; margin: 0; padding: 0; background: #f8fafc; }
        .page { background: white; width: 100%; padding: 5mm; margin: 0 auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); box-sizing: border-box; }
        
        .kop-surat { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 5px; margin-bottom: 15px; }
        .kop-surat img { width: 80px; height: 80px; object-fit: contain; }
        .kop-text { flex: 1; text-align: center; padding: 0 10px; }
        .kop-text h1 { margin: 0; text-transform: uppercase; font-weight: bold; }
        .kop-text h2 { margin: 3px 0; font-weight: bold; text-transform: uppercase; }
        .kop-text p { margin: 3px 0; font-size: 11px; }
        
        .judul { text-align: center; margin: 10px 0 20px; }
        .judul h4 { margin: 0; font-size: 13pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; }
        .judul p { margin: 4px 0 0 0; font-size: 10pt; }

        .kelas-section { margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px; background: #f8fafc; page-break-inside: avoid; }
        .kelas-header { font-size: 11pt; font-weight: bold; text-align: center; border-bottom: 2px dashed #cbd5e1; padding-bottom: 8px; margin-bottom: 10px; color: #0f172a; }
        
        .top-list { display: flex; flex-direction: column; gap: 8px; }
        .top-item { display: flex; align-items: center; gap: 10px; background: white; padding: 8px; border-radius: 8px; border: 1px solid #f1f5f9; }
        
        .rank-badge { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12pt; font-family: sans-serif; }
        .rank-1 { background: #fef08a; color: #854d0e; }
        .rank-2 { background: #e2e8f0; color: #475569; }
        .rank-3 { background: #fef3c7; color: #92400e; }
        
        .student-info { flex: 1; }
        .student-name { font-size: 9.5pt; font-weight: bold; color: #0f172a; margin-bottom: 2px; }
        .student-stats { font-size: 8pt; color: #475569; line-height: 1.2; }
        
        .grid-container { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .page { margin: 0; box-shadow: none; padding: 5mm; width: 100%; min-height: auto; page-break-after: auto; }
            .no-print { display: none; }
            @page { size: portrait; margin: 5mm; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; padding: 20px; background: #1e293b; color: white;">
        <button onclick="window.print()" style="background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 16px; cursor: pointer; font-weight: bold;">
            🖨️ Cetak Dokumen (Portrait)
        </button>
        <p style="margin-top: 10px; font-size: 14px; color: #cbd5e1;">Pilih pengaturan kertas A4 pada mode Portrait.</p>
    </div>

    <div class="page">
        <!-- KOP SURAT -->
        <div class="kop-surat">
            <img src="<?php echo $faviconUrl; ?>" alt="Logo">
            <div class="kop-text">
                <h1 style="font-size: <?php echo intval($inst['kop_font_yayasan'] ?? 14); ?>px;"><?php echo htmlspecialchars($inst['yayasan'] ?? ''); ?></h1>
                <h2 style="font-size: <?php echo intval($inst['kop_font_nama'] ?? 18); ?>px;"><?php echo htmlspecialchars($inst['nama'] ?? ''); ?></h2>
                <?php 
                    $alamat = $inst['alamat'] ?? '';
                    $desa = !empty($inst['desa']) ? 'Ds. ' . $inst['desa'] : '';
                    $kecamatan = !empty($inst['kecamatan']) ? 'Kec. ' . $inst['kecamatan'] : '';
                    $kota = !empty($inst['kota']) ? 'Kab. ' . $inst['kota'] : '';
                    $provinsi = !empty($inst['provinsi']) ? 'Prov. ' . $inst['provinsi'] : '';
                    $kodepos = !empty($inst['kodepos']) ? 'KP. ' . $inst['kodepos'] : '';
                    $fullAddress = implode(', ', array_filter([$alamat, $desa, $kecamatan, $kota, $provinsi, $kodepos]));
                ?>
                <p><?php echo htmlspecialchars($fullAddress); ?></p>
                <p>Website: <?php echo htmlspecialchars($inst['website'] ?? ''); ?> | Email: <?php echo htmlspecialchars($inst['email'] ?? ''); ?></p>
            </div>
            <!-- Spacer to balance logo width for perfect centering -->
            <div style="width: 80px;"></div>
        </div>

        <div class="judul">
            <h4>PENGHARGAAN JUARA KEHADIRAN SISWA</h4>
            <p>Bulan: <strong><?php echo $bulan_name . ' ' . $tahun; ?></strong></p>
        </div>

        <?php if(!empty($top3Instansi)): ?>
        <div style="margin-bottom: 20px; border: 2px solid #eab308; border-radius: 12px; padding: 15px; background: #fefce8; page-break-inside: avoid;">
            <div style="font-size: 12pt; font-weight: bold; text-align: center; color: #854d0e; margin-bottom: 15px;">
                🌟 TOP 3 PALING RAJIN SE-INSTANSI 🌟
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                <?php $rk = 1; foreach($top3Instansi as $top): ?>
                    <?php 
                    $active_days = $class_active_days_count[$top['kelas_id']] ?? 1;
                    $persen = min(100, round(($top['total_hadir'] / max(1, $active_days)) * 100));
                    $avgTime = $top['avg_jam_masuk'] ? substr($top['avg_jam_masuk'], 0, 5) : '-';
                    $alpa_sebenarnya = $top['total_alpa'];
                    if ($top['total_hari_tercatat'] < $active_days) {
                        $alpa_sebenarnya += ($active_days - $top['total_hari_tercatat']);
                    }
                    ?>
                    <div style="display: flex; align-items: center; gap: 10px; background: white; padding: 10px; border-radius: 8px; border: 1px solid #fde047; box-shadow: 0 2px 4px -1px rgba(0,0,0,0.05);">
                        <div class="rank-badge rank-<?php echo $rk; ?>" style="width: 35px; height: 35px; font-size: 14pt; min-width: 35px;"><?php echo $rk; ?></div>
                        <div class="student-info">
                            <div class="student-name" style="font-size: 9.5pt; line-height:1.1; margin-bottom:3px;"><?php echo htmlspecialchars($top['nama']); ?></div>
                            <div class="student-stats" style="font-weight: bold; color: #1e293b; font-size:8pt;">Kelas <?php echo htmlspecialchars($top['nama_kelas']); ?></div>
                            <div class="student-stats" style="margin-top: 2px; font-size:7.5pt;">Hadir: <?php echo $top['total_hadir']; ?> &bull; Alpa: <?php echo $alpa_sebenarnya; ?></div>
                            <div class="student-stats" style="margin-top: 1px; font-size:7.5pt;">Persentase: <?php echo $persen; ?>% &bull; Rata: <?php echo $avgTime; ?></div>
                        </div>
                    </div>
                <?php $rk++; endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="grid-container">
            <?php foreach($kelasList as $kls): ?>
                <?php if(!empty($top3PerKelas[$kls['id']])): ?>
                    <div class="kelas-section">
                        <div class="kelas-header">Kelas <?php echo htmlspecialchars($kls['nama_kelas']); ?></div>
                        <div class="top-list">
                            <?php $rk = 1; foreach($top3PerKelas[$kls['id']] as $tk): ?>
                                <?php 
                                $active_daysK = $class_active_days_count[$tk['kelas_id']] ?? 1;
                                $persenK = min(100, round(($tk['total_hadir'] / max(1, $active_daysK)) * 100));
                                $avgTimeK = $tk['avg_jam_masuk'] ? substr($tk['avg_jam_masuk'], 0, 5) : '-';
                                $tk_alpa_sebenarnya = $tk['total_alpa'];
                                if ($tk['total_hari_tercatat'] < $active_daysK) {
                                    $tk_alpa_sebenarnya += ($active_daysK - $tk['total_hari_tercatat']);
                                }
                                ?>
                                <div class="top-item">
                                    <div class="rank-badge rank-<?php echo $rk; ?>"><?php echo $rk; ?></div>
                                    <div class="student-info">
                                        <div class="student-name"><?php echo htmlspecialchars($tk['nama']); ?></div>
                                        <div class="student-stats">Hadir: <?php echo $tk['total_hadir']; ?> &bull; Alpa: <?php echo $tk_alpa_sebenarnya; ?></div>
                                        <div class="student-stats" style="margin-top: 2px;">Persentase: <?php echo $persenK; ?>% &bull; Rata-rata jam: <?php echo $avgTimeK; ?></div>
                                    </div>
                                </div>
                            <?php $rk++; endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
