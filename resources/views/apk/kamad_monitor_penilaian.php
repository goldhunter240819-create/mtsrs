<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <img src="<?= htmlspecialchars($faviconSrc) ?>" alt="Logo">
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Admin Delegasi</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Supervisi Penilaian</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding-bottom: 30px;">
    
    <!-- Filter Guru -->
    <form method="GET" action="/apk/kamad/monitor-penilaian" style="margin-bottom:20px;">
        <label style="font-size:0.7rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Pilih Guru</label>
        <div style="display:flex; gap:10px;">
            <select name="guru_id" style="flex:1; padding:12px; border-radius:12px; border:1px solid #e2e8f0; font-weight:700; color:#1e293b; background:#f8fafc;">
                <option value="">-- Pilih Guru --</option>
                <?php foreach($list_guru as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= $g['id'] == $selected_guru_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" style="background:linear-gradient(135deg, #16a34a, #15803d); color:white; padding:12px 20px; border:none; border-radius:12px; font-weight:800; display:flex; align-items:center; gap:8px;">
                <i data-lucide="search" style="width:18px;"></i> Cek
            </button>
        </div>
    </form>

    <?php if($selected_guru_id): ?>
        <?php if(empty($data_mengajar)): ?>
            <div style="text-align:center; padding:30px; background:#f8fafc; border-radius:15px; color:#94a3b8; margin-top:20px;">
                <i data-lucide="calendar-x" style="width:40px; height:40px; margin-bottom:10px;"></i><br>
                Guru ini belum memiliki jadwal pelajaran atau belum menginput nilai sama sekali.
            </div>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:15px;">
                <?php foreach($data_mengajar as $idx => $m): ?>
                    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:15px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.02);">
                        <!-- Card Header (Mapel + Kelas) -->
                        <div style="padding:15px; border-bottom:1px solid #f1f5f9; background:linear-gradient(to right, #f8fafc, #ffffff);">
                            <div style="font-size:0.75rem; color:#64748b; font-weight:800; text-transform:uppercase; margin-bottom:3px;">
                                Kelas <?= htmlspecialchars($m['nama_kelas']) ?>
                            </div>
                            <div style="font-size:1.1rem; color:#1e293b; font-weight:800;">
                                <?= htmlspecialchars($m['nama_mapel']) ?>
                            </div>
                        </div>
                        
                        <!-- Card Body (Evaluasi) -->
                        <div style="padding:15px; display:flex; flex-direction:column; gap:10px;">
                            <?php if(empty($m['evaluasi'])): ?>
                                <div style="text-align:center; padding:15px; color:#ef4444; font-size:0.85rem; font-weight:700; background:#fef2f2; border-radius:10px;">
                                    Belum ada nilai yang diinput.
                                </div>
                            <?php else: ?>
                                <?php foreach($m['evaluasi'] as $e_idx => $e): ?>
                                    <div class="eval-accordion" style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">
                                        <!-- Accordion Header -->
                                        <div class="eval-header" onclick="toggleAccordion('acc_<?= $idx ?>_<?= $e_idx ?>')" style="padding:12px 15px; background:#f8fafc; display:flex; justify-content:space-between; align-items:center; cursor:pointer;">
                                            <div>
                                                <div style="font-size:0.9rem; font-weight:800; color:#334155;">
                                                    <?= htmlspecialchars($e['nama_evaluasi']) ?>
                                                </div>
                                                <div style="font-size:0.7rem; font-weight:600; color:#64748b; margin-top:2px; font-style:italic;">
                                                    <?= !empty($e['materi']) ? htmlspecialchars($e['materi']) : '-' ?>
                                                </div>
                                                <?php
                                                    $tuntas = 0;
                                                    $remed = 0;
                                                    foreach($e['details'] as $dt) {
                                                        if ($dt['nilai'] !== null) {
                                                            if ((float)$dt['nilai'] >= 75) {
                                                                $tuntas++;
                                                            } else {
                                                                $remed++;
                                                            }
                                                        }
                                                    }
                                                ?>
                                                <div style="font-size:0.7rem; font-weight:700; color:#64748b; margin-top:2px;">
                                                    Terisi: <?= $e['terisi'] ?> / <?= $e['total'] ?> Siswa
                                                </div>
                                                <div style="font-size:0.7rem; font-weight:700; margin-top:2px; display:flex; gap:8px;">
                                                    <span style="color:#10b981;"><i data-lucide="check-circle-2" style="width:10px; height:10px;"></i> Tuntas: <?= $tuntas ?></span>
                                                    <span style="color:#ef4444;"><i data-lucide="x-circle" style="width:10px; height:10px;"></i> Remedial:<?= $remed ?></span>
                                                </div>
                                            </div>
                                            <div style="display:flex; align-items:center; gap:10px;">
                                                <!-- Progress Badge -->
                                                <?php 
                                                    $pct = $e['persen'];
                                                    if($pct >= 100) { $bg = '#dcfce7'; $color = '#16a34a'; }
                                                    else if($pct >= 50) { $bg = '#fef3c7'; $color = '#d97706'; }
                                                    else { $bg = '#fee2e2'; $color = '#dc2626'; }
                                                ?>
                                                <div style="background:<?= $bg ?>; color:<?= $color ?>; padding:4px 8px; border-radius:20px; font-size:0.7rem; font-weight:800;">
                                                    <?= $pct ?>%
                                                </div>
                                                <a href="/apk/kamad/supervisi-penilaian/evaluasi?guru_id=<?= $selected_guru_id ?>&kelas_id=<?= $m['kelas_id'] ?>&mapel_id=<?= $m['mapel_id'] ?>&jenis_evaluasi=<?= urlencode($e['nama_evaluasi']) ?>" onclick="event.stopPropagation();" style="background:<?= !empty($e['supervisi']) ? '#dcfce7' : '#fef3c7' ?>; color:<?= !empty($e['supervisi']) ? '#16a34a' : '#d97706' ?>; border:1px solid <?= !empty($e['supervisi']) ? '#bbf7d0' : '#fde68a' ?>; padding:4px 10px; border-radius:20px; font-size:0.7rem; font-weight:800; text-decoration:none; display:flex; align-items:center; gap:4px;">
                                                    <i data-lucide="<?= !empty($e['supervisi']) ? 'check-circle' : 'file-edit' ?>" style="width:12px; height:12px;"></i> <?= !empty($e['supervisi']) ? 'Ubah' : 'Evaluasi' ?>
                                                </a>
                                                <i data-lucide="chevron-down" class="acc-icon" id="icon_acc_<?= $idx ?>_<?= $e_idx ?>" style="width:16px; color:#94a3b8; transition:transform 0.3s;"></i>
                                            </div>
                                        </div>

                                        <!-- Accordion Body (Daftar Siswa) -->
                                        <div id="acc_<?= $idx ?>_<?= $e_idx ?>" class="eval-body" style="display:none; padding:15px; border-top:1px solid #e2e8f0; background:#fff;">
                                            <!-- Header List -->
                                            <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:2px solid #e2e8f0; margin-bottom:5px;">
                                                <div style="font-weight:800; color:#64748b; font-size:0.75rem; text-transform:uppercase;">NAMA SISWA</div>
                                                <div style="font-weight:800; color:#64748b; font-size:0.75rem; text-transform:uppercase; width:60px; text-align:center;">NILAI</div>
                                            </div>
                                            <!-- List Body -->
                                            <div style="display:flex; flex-direction:column;">
                                                <?php foreach($e['details'] as $dt): ?>
                                                <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid #f1f5f9;">
                                                    <div style="font-weight:700; color:#334155; font-size:0.8rem; flex:1; word-break:break-word;">
                                                        <?= htmlspecialchars($dt['nama']) ?>
                                                    </div>
                                                    <div style="width:60px; text-align:center; font-weight:800; color:<?= $dt['nilai'] !== null ? ($dt['nilai'] < 75 ? '#ef4444' : '#10b981') : '#94a3b8' ?>;">
                                                        <?= $dt['nilai'] !== null ? (float)$dt['nilai'] : '<span style="font-size:0.7rem; font-weight:600; font-style:italic;">Kosong</span>' ?>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

</div>

<script>
    setTimeout(function() {
        lucide.createIcons();
    }, 100);

    function toggleAccordion(id) {
        // Ambil element body dan icon yang di-klik
        var targetBody = document.getElementById(id);
        var targetIcon = document.getElementById('icon_' + id);
        
        var isCurrentlyOpen = targetBody.style.display === 'block';

        // Tutup semua accordion terlebih dahulu (ATURAN 1 BUKA, LAIN TUTUP)
        var allBodies = document.querySelectorAll('.eval-body');
        var allIcons = document.querySelectorAll('.acc-icon');
        
        allBodies.forEach(function(body) {
            body.style.display = 'none';
        });
        allIcons.forEach(function(icon) {
            icon.style.transform = 'rotate(0deg)';
        });

        // Jika yang di-klik tadinya belum terbuka, maka buka
        if (!isCurrentlyOpen) {
            targetBody.style.display = 'block';
            targetIcon.style.transform = 'rotate(180deg)';
        }
    }
</script>
