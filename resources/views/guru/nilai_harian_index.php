<div class="modern-page-header" style="background: linear-gradient(135deg, #2563eb, #3b82f6); padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 15px -3px rgba(37,99,235,0.2);">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; color: white;">Monitoring Nilai Harian & Rekap</h2>
        <p style="color: rgba(255,255,255,0.85); font-size: 0.95rem; margin: 0;">Pantau progres nilai harian siswa secara detail sesuai dengan mata pelajaran dan kelas yang Anda ampu.</p>
    </div>
</div>

<?php if(isset($_SESSION['guru_msg'])): ?>
    <div style="background: <?= $_SESSION['guru_msg_type'] == 'success' ? '#ecfdf5' : '#fef2f2' ?>; border-left: 4px solid <?= $_SESSION['guru_msg_type'] == 'success' ? '#10b981' : '#ef4444' ?>; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <p style="margin: 0; color: <?= $_SESSION['guru_msg_type'] == 'success' ? '#047857' : '#b91c1c' ?>; font-weight: 500;">
            <?= htmlspecialchars($_SESSION['guru_msg']) ?>
        </p>
    </div>
    <?php unset($_SESSION['guru_msg']); unset($_SESSION['guru_msg_type']); ?>
<?php endif; ?>

<div class="z-card" style="padding: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--z-text); margin: 0; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="history" style="color: var(--z-primary);"></i> Daftar Nilai Siswa
        </h3>
        
        <form method="GET" style="margin: 0; width: 100%; max-width: 600px; display: flex; gap: 10px;">
            <select name="kelas_id" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none; width: 100%; font-size: 0.9rem;" onchange="this.form.mapel_id ? this.form.mapel_id.value='' : null; this.form.submit()">
                <option value="">-- Pilih Kelas --</option>
                <?php foreach ($kelas_mengajar as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= ($filter_kelas_id == $k['id']) ? 'selected' : '' ?>>
                        Kelas <?= htmlspecialchars($k['nama_kelas']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="mapel_id" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none; width: 100%; font-size: 0.9rem;" onchange="this.form.submit()" <?= empty($mapel_mengajar) ? 'disabled' : '' ?>>
                <option value="">-- Pilih Mata Pelajaran --</option>
                <?php foreach ($mapel_mengajar as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= ($filter_mapel_id == $m['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['nama_mapel']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    
    <?php if ($filter_kelas_id > 0 && $filter_mapel_id > 0): ?>
        <div style="overflow-x: auto; margin-top: 20px;">
            <table class="z-table" style="font-size: 0.9rem; width: 100%;">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 50px; text-align: center; vertical-align: middle;">No</th>
                        <th rowspan="2" style="width: 100px; text-align: center; vertical-align: middle;">NIS</th>
                        <th rowspan="2" style="vertical-align: middle;">Nama Lengkap Siswa</th>
                        <th colspan="<?= count($ph_list) ?>" style="text-align: center;">Penilaian Harian (PH)</th>
                        <th colspan="2" style="text-align: center;">Evaluasi Sumatif</th>
                        <th rowspan="2" style="width: 100px; text-align: center; vertical-align: middle;">Nilai Rapor</th>
                    </tr>
                    <tr>
                        <?php foreach($ph_list as $ph): ?>
                            <th style="width: 70px; text-align: center;"><?= htmlspecialchars(str_replace('PH ', 'PH', $ph)) ?></th>
                        <?php endforeach; ?>
                        <th style="width: 70px; text-align: center;">PTS</th>
                        <th style="width: 70px; text-align: center;">PAS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($siswa_list)): ?>
                        <tr>
                            <td colspan="<?= 4 + count($ph_list) + 2 ?>" style="text-align: center; padding: 20px;">Belum ada data siswa di kelas ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no=1; foreach($siswa_list as $s): ?>
                        <tr>
                            <td style="text-align: center;"><?= $no++ ?></td>
                            <td style="text-align: center;"><?= htmlspecialchars($s['nis'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($s['nama']) ?></td>
                            
                            <?php foreach ($ph_list as $ph): ?>
                                <?php 
                                    $val = isset($nilai_grouped[$s['id']][$ph]) ? $nilai_grouped[$s['id']][$ph] : '';
                                    $is_red = ($val !== '' && (float)$val < 70) ? 'color: red;' : '';
                                ?>
                                <td style="text-align: center; <?= $val !== '' ? 'font-weight:bold; ' . $is_red : 'color:#cbd5e1;' ?>"><?= $val !== '' ? $val : '-' ?></td>
                            <?php endforeach; ?>
                            
                            <?php 
                                $pts = isset($nilai_grouped[$s['id']]['PTS']) ? $nilai_grouped[$s['id']]['PTS'] : '';
                                $pas = isset($nilai_grouped[$s['id']]['PAS']) ? $nilai_grouped[$s['id']]['PAS'] : '';
                                $rapor = isset($nilai_grouped[$s['id']]['RAPOR']) ? $nilai_grouped[$s['id']]['RAPOR'] : '';

                                // Hitung Nilai Rapor
                                if ($rapor === '') {
                                    $institusi = \App\Core\Branding::getInstitusi();
                                    
                                    $sum_ph = 0;
                                    $count_komponen = count($ph_list) + 1; // PH + PTS
                                    
                                    // Jumlahkan PH
                                    foreach ($ph_list as $ph) {
                                        $val = $nilai_grouped[$s['id']][$ph] ?? '';
                                        if ($val !== '') {
                                            $sum_ph += (float)$val;
                                        }
                                    }
                                    
                                    // Tambahkan PTS
                                    if ($pts !== '') {
                                        $sum_ph += (float)$pts;
                                    }
                                    
                                    $bobotHarian = (float)($institusi['bobot_harian'] ?? 50) / 100;
                                    $bobotPas = (float)($institusi['bobot_pas'] ?? 50) / 100;

                                    $rata_rata = $sum_ph / $count_komponen;
                                    $pas_val = $pas !== '' ? (float)$pas : 0;
                                    $rapor = round(($rata_rata * $bobotHarian) + ($pas_val * $bobotPas));
                                }
                            ?>
                            <td style="text-align: center; <?= ($pts !== '' && (float)$pts < 70) ? 'font-weight:bold; color:red;' : ($pts !== '' ? 'font-weight:bold;' : 'color:#cbd5e1;') ?>"><?= $pts !== '' ? $pts : '-' ?></td>
                            <td style="text-align: center; <?= ($pas !== '' && (float)$pas < 70) ? 'font-weight:bold; color:red;' : ($pas !== '' ? 'font-weight:bold;' : 'color:#cbd5e1;') ?>"><?= $pas !== '' ? $pas : '-' ?></td>
                            <td style="text-align: center; font-weight: 800; <?= ($rapor !== '' && (float)$rapor < 70) ? 'color: red;' : ($rapor !== '' ? 'color: var(--z-primary);' : 'color:#cbd5e1;') ?>"><?= $rapor !== '' ? $rapor : '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
            <i data-lucide="filter" style="width: 40px; height: 40px; color: #94a3b8; margin-bottom: 10px;"></i>
            <p style="margin: 0; color: #64748b; font-weight: 500;">Silakan pilih Kelas & Mata Pelajaran untuk melihat daftar nilai siswa.</p>
        </div>
    <?php endif; ?>
</div>
