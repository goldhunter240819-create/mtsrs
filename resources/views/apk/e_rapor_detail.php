<style>
    .er-card {
        background: #fff;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 15px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .er-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
        border-bottom: 1px dashed #e2e8f0;
        padding-bottom: 12px;
    }
    .er-student-name {
        font-size: 0.85rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 2px;
        line-height: 1.3;
    }
    .er-student-nis {
        font-size: 0.7rem;
        color: #64748b;
        font-weight: 600;
    }
    .er-rapor-box {
        background: #fee2e2;
        color: #ef4444;
        border-radius: 8px;
        padding: 6px 12px;
        text-align: center;
        min-width: 60px;
    }
    .er-rapor-box.good {
        background: #ecfdf5;
        color: #10b981;
    }
    .er-rapor-title {
        font-size: 0.6rem;
        font-weight: 800;
        margin-bottom: 2px;
    }
    .er-rapor-score {
        font-size: 1.1rem;
        font-weight: 800;
    }
    .er-grades-row {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 5px;
    }
    .er-grades-row::-webkit-scrollbar {
        height: 4px;
    }
    .er-grades-row::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .er-grade-box {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 8px;
        text-align: center;
        min-width: 45px;
        background: #f8fafc;
    }
    .er-grade-title {
        font-size: 0.6rem;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .er-grade-score {
        font-size: 0.85rem;
        font-weight: 800;
        color: #1e293b;
    }
    .er-grade-empty {
        background: #fef2f2;
        border-color: #fecaca;
        color: #ef4444;
    }
    .er-grade-box.pts {
        background: #fefce8;
        border-color: #fef08a;
    }
    .er-grade-box.pas {
        background: #fdf4ff;
        border-color: #f5d0fe;
    }
</style>

<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <a href="/apk/e-rapor" style="color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 12px; margin-right: 15px;">
            <i data-lucide="arrow-left"></i>
        </a>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Monitor E-Rapor</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;"><?= htmlspecialchars($nama_mapel) ?></div>
            <div style="font-size: 0.75rem; color: rgba(255,255,255,0.9); margin-top: 2px;">
                <?= htmlspecialchars($nama_kelas) ?> &bull; SMT <?= (date('m') >= 7 && date('m') <= 12) ? '1' : '2' ?>
            </div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding-bottom: 40px;">
    
    <?php if (empty($rekapSiswa)): ?>
        <div style="text-align: center; padding: 40px 20px;">
            <div style="width: 60px; height: 60px; border-radius: 20px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: #94a3b8;">
                <i data-lucide="users" style="width: 30px; height: 30px;"></i>
            </div>
            <h4 style="margin: 0 0 5px 0; font-size: 0.95rem; color: #1e293b; font-weight: 700;">Belum Ada Siswa</h4>
            <p style="margin: 0; font-size: 0.8rem; color: #64748b;">Data siswa untuk kelas ini belum tersedia.</p>
        </div>
    <?php else: ?>
        <?php $no = 1; foreach ($rekapSiswa as $rs): ?>
            <?php 
                $s = $rs['siswa'];
                $rapor = $rs['RAPOR'];
                $rapor_class = $rapor >= 75 ? 'good' : ''; 
            ?>
            <div class="er-card">
                <div class="er-header">
                    <div style="flex: 1; padding-right: 10px;">
                        <div class="er-student-name"><?= $no++ ?>. <?= htmlspecialchars($s['nama']) ?></div>
                        <div class="er-student-nis">NIS: <?= htmlspecialchars($s['nis'] ?? '-') ?></div>
                    </div>
                    <div class="er-rapor-box <?= $rapor_class ?>">
                        <div class="er-rapor-title">RAPOR</div>
                        <div class="er-rapor-score"><?= $rapor > 0 ? $rapor : '-' ?></div>
                    </div>
                </div>
                
                <div class="er-grades-row">
                    <?php 
                        // Tampilkan sejumlah max_ph_class yang ada di database untuk kelas ini
                        $max_ph = $max_ph_class > 0 ? $max_ph_class : 0; 
                        for ($i = 1; $i <= $max_ph; $i++): 
                            $val = $rs['PH'][$i] ?? null;
                            $empty_class = $val === null ? 'er-grade-empty' : '';
                    ?>
                        <div class="er-grade-box <?= $empty_class ?>">
                            <div class="er-grade-title">PH <?= $i ?></div>
                            <div class="er-grade-score"><?= $val !== null ? $val : '-' ?></div>
                        </div>
                    <?php endfor; ?>

                    <!-- PTS -->
                    <?php 
                        $pts_val = $rs['PTS']; 
                        $pts_empty = $pts_val === null ? 'er-grade-empty' : '';
                    ?>
                    <div class="er-grade-box pts <?= $pts_empty ?>">
                        <div class="er-grade-title">PTS</div>
                        <div class="er-grade-score"><?= $pts_val !== null ? $pts_val : '-' ?></div>
                    </div>

                    <!-- PAS -->
                    <?php 
                        $pas_val = $rs['PAS']; 
                        $pas_empty = $pas_val === null ? 'er-grade-empty' : '';
                    ?>
                    <div class="er-grade-box pas <?= $pas_empty ?>">
                        <div class="er-grade-title">PAS</div>
                        <div class="er-grade-score"><?= $pas_val !== null ? $pas_val : '-' ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>
