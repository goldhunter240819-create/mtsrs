<?php
$title = $title ?? 'Denah Ruangan & Atur Meja Siswa | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_denah';

ob_start();

$t = (int)($selectedRuang['tipe_meja'] ?? 1);
$kolom = (int)($selectedRuang['jumlah_kolom'] ?? 5);
$baris = (int)($selectedRuang['jumlah_baris'] ?? 6);
$kapasitas = (int)($selectedRuang['kapasitas'] ?? ($t * $kolom * $baris));
$pola = $selectedRuang['pola_penomoran'] ?? 'kolom';

$totalTerisi = count($pesertaMeja);
$totalKosong = max(0, $kapasitas - $totalTerisi);

// Generate desk numbers mapped by row and col
// Desk slot structure: $gridMap[baris][kolom][bangku] = mejaNo
$gridMap = [];
if ($t == 1) {
    if ($pola === 'kolom') {
        $no = 1;
        for ($k = 1; $k <= $kolom; $k++) {
            for ($b = 1; $b <= $baris; $b++) {
                if ($no <= $kapasitas) {
                    $gridMap[$b][$k][1] = $no++;
                }
            }
        }
    } elseif ($pola === 'baris') {
        $no = 1;
        for ($b = 1; $b <= $baris; $b++) {
            for ($k = 1; $k <= $kolom; $k++) {
                if ($no <= $kapasitas) {
                    $gridMap[$b][$k][1] = $no++;
                }
            }
        }
    } else { // zigzag
        $no = 1;
        for ($b = 1; $b <= $baris; $b++) {
            $kRange = ($b % 2 == 1) ? range(1, $kolom) : range($kolom, 1);
            foreach ($kRange as $k) {
                if ($no <= $kapasitas) {
                    $gridMap[$b][$k][1] = $no++;
                }
            }
        }
    }
} else {
    // Double desk: 2 benches per desk
    $no = 1;
    for ($b = 1; $b <= $baris; $b++) {
        for ($k = 1; $k <= $kolom; $k++) {
            if ($no <= $kapasitas) {
                $gridMap[$b][$k][1] = $no++;
            }
            if ($no <= $kapasitas) {
                $gridMap[$b][$k][2] = $no++;
            }
        }
    }
}
?>

<style>
/* Custom styling for Seating Chart Grid */
.desk-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 105px;
    position: relative;
}
.desk-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    border-color: #cbd5e1;
}
.desk-card.empty {
    background: #f8fafc;
    border: 2px dashed #cbd5e1;
    opacity: 0.85;
}
.desk-double-container {
    background: #f1f5f9;
    border: 1.5px solid #cbd5e1;
    border-radius: 14px;
    padding: 8px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    position: relative;
}
.desk-double-badge {
    position: absolute;
    top: -9px;
    left: 50%;
    transform: translateX(-50%);
    background: #475569;
    color: white;
    font-size: 0.65rem;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 99px;
    letter-spacing: 0.5px;
    z-index: 2;
}
.badge-grade-7 { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.badge-grade-8 { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
.badge-grade-9 { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
</style>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="layout-grid" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Denah Ruangan & Atur Meja Siswa
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Visualisasi susunan meja ruang ujian, pengaturan formasi, dan tombol pengacakan (random) siswa otomatis.
        </p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <button type="button" onclick="bukaModalAcakSiswa()" class="btn" style="background: #10b981; color: white; font-weight: 800; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);">
            <i data-lucide="shuffle" style="width: 18px; height: 18px;"></i> Atur Siswa (Random Meja)
        </button>
        <?php if ($selectedRuang): ?>
        <a href="<?php echo \App\Core\Helper::url('/asesmen/denah/cetak?event_id=' . $eventId . '&ruang_id=' . $selectedRuang['id']); ?>" target="_blank" class="btn" style="background: white; color: #4338ca; font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <i data-lucide="printer" style="width: 18px; height: 18px; color: #2563eb;"></i> Cetak Denah (A4)
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Selector & Stats Bar -->
<div class="z-card" style="padding: 1.5rem; border-radius: 16px; margin-bottom: 1.5rem;">
    <form method="GET" action="<?php echo \App\Core\Helper::url('/asesmen/denah'); ?>" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; margin-bottom: 1.25rem;">
        <div style="flex: 1; min-width: 220px;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--z-text); margin-bottom: 6px;">Pilih Event Ujian:</label>
            <select name="event_id" onchange="this.form.submit()" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <?php foreach($events as $ev): ?>
                    <option value="<?php echo $ev['id']; ?>" <?php echo $eventId == $ev['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($ev['nama']); ?> (<?php echo htmlspecialchars($ev['kode']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1; min-width: 220px;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--z-text); margin-bottom: 6px;">Pilih Ruangan:</label>
            <select name="ruang_id" onchange="this.form.submit()" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <?php foreach($ruangList as $r): ?>
                    <option value="<?php echo $r['id']; ?>" <?php echo $ruangId == $r['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($r['nama_ruang']); ?> (Formasi: <?php echo $r['tipe_meja'] ?: 1; ?>&times;<?php echo $r['jumlah_kolom'] ?: 5; ?>&times;<?php echo $r['jumlah_baris'] ?: 6; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="padding: 10px 18px; border-radius: 8px; font-weight: 600;">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Muat Denah
            </button>
        </div>
    </form>

    <!-- Room Summary Chips -->
    <?php if ($selectedRuang): ?>
    <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; padding-top: 1rem; border-top: 1px dashed var(--z-border);">
        <div style="background: #f1f5f9; padding: 6px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 6px;">
            <i data-lucide="building" style="width: 16px; height: 16px; color: #64748b;"></i>
            <strong><?php echo htmlspecialchars($selectedRuang['nama_ruang']); ?></strong>
            <span style="color: #64748b;">(<?php echo htmlspecialchars($selectedRuang['lokasi'] ?: 'Lantai 1'); ?>)</span>
        </div>

        <div style="background: #eef2ff; color: #4338ca; padding: 6px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; gap: 6px;">
            <i data-lucide="grid" style="width: 16px; height: 16px;"></i>
            Formasi: <?php echo $t; ?> &times; <?php echo $kolom; ?> &times; <?php echo $baris; ?> 
            (<?php echo $t == 1 ? 'Mandiri' : 'Gandeng'; ?>)
        </div>

        <div style="background: #f0fdf4; color: #166534; padding: 6px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; gap: 6px;">
            <i data-lucide="check-circle" style="width: 16px; height: 16px; color: #16a34a;"></i>
            Terisi: <?php echo $totalTerisi; ?> / <?php echo $kapasitas; ?> Siswa
        </div>

        <?php if ($totalKosong > 0): ?>
        <div style="background: #fef2f2; color: #991b1b; padding: 6px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; gap: 6px;">
            <i data-lucide="alert-circle" style="width: 16px; height: 16px; color: #ef4444;"></i>
            Kosong: <?php echo $totalKosong; ?> Meja
        </div>
        <?php endif; ?>

        <div style="margin-left: auto;">
            <a href="<?php echo \App\Core\Helper::url('/asesmen/ruang'); ?>" class="btn btn-sm btn-outline" style="font-size: 0.8rem; padding: 6px 12px;">
                <i data-lucide="edit" style="width: 14px; height: 14px;"></i> Ubah Formasi Ruang
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Visual Room Canvas (Seating Chart) -->
<div class="z-card" style="padding: 2rem; border-radius: 20px; background: #fafafa; border: 1.5px solid #e2e8f0; margin-bottom: 2rem;">
    <!-- Bagian Depan Kelas: Papan Tulis & Meja Pengawas -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; gap: 1rem;">
        <!-- Pintu Masuk Kiri -->
        <div style="display: flex; align-items: center; gap: 8px; background: #e2e8f0; padding: 8px 16px; border-radius: 10px; font-weight: 700; font-size: 0.82rem; color: #475569; border: 1px dashed #94a3b8;">
            <i data-lucide="log-in" style="width: 18px; height: 18px; color: #0284c7;"></i> PINTU MASUK
        </div>

        <!-- Papan Tulis Tengah -->
        <div style="flex: 1; max-width: 600px; background: linear-gradient(135deg, #1e293b, #0f172a); color: white; padding: 14px 20px; border-radius: 14px; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.12); border: 2px solid #334155;">
            <div style="font-size: 0.72rem; letter-spacing: 1.5px; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Bagian Depan Kelas</div>
            <div style="font-size: 1.1rem; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 2px;">
                <i data-lucide="presentation" style="width: 20px; height: 20px; color: #38bdf8;"></i> PAPAN TULIS & MEJA PENGAWAS
            </div>
        </div>

        <!-- Meja Pengawas Kanan -->
        <div style="background: #fef3c7; border: 1px solid #fde68a; padding: 8px 16px; border-radius: 10px; font-weight: 700; font-size: 0.82rem; color: #92400e; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="user-check" style="width: 18px; height: 18px; color: #d97706;"></i> MEJA PENGAWAS
        </div>
    </div>

    <!-- Grid Meja Ujian -->
    <?php if ($t == 1): ?>
        <!-- FORMATION: 1 SISWA PER MEJA (SINGLE) -->
        <div style="display: grid; grid-template-columns: repeat(<?php echo $kolom; ?>, minmax(140px, 1fr)); gap: 1rem; overflow-x: auto; padding: 10px 0;">
            <?php for ($b = 1; $b <= $baris; $b++): ?>
                <?php for ($k = 1; $k <= $kolom; $k++): 
                    $noMeja = $gridMap[$b][$k][1] ?? null;
                    $siswa = ($noMeja && isset($pesertaMeja[$noMeja])) ? $pesertaMeja[$noMeja] : null;
                ?>
                    <?php if ($noMeja): ?>
                        <div class="desk-card <?php echo $siswa ? '' : 'empty'; ?>">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <span class="pill pill-green" style="font-weight: 800; font-size: 0.75rem; padding: 2px 8px;">
                                    Meja <?php echo sprintf("%02d", $noMeja); ?>
                                </span>
                                <?php if ($siswa): ?>
                                    <?php 
                                    $tk = $siswa['tingkat'] ?? '7';
                                    $badgeClass = 'badge-grade-' . $tk;
                                    ?>
                                    <span class="<?php echo $badgeClass; ?>" style="font-weight: 800; font-size: 0.72rem; padding: 2px 6px; border-radius: 6px;">
                                        Kelas <?php echo htmlspecialchars($siswa['nama_kelas']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if ($siswa): ?>
                                <div style="margin: 4px 0;">
                                    <div style="font-weight: 800; font-size: 0.85rem; color: #0f172a; line-height: 1.25; margin-bottom: 3px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="<?php echo htmlspecialchars($siswa['nama_siswa']); ?>">
                                        <?php echo htmlspecialchars($siswa['nama_siswa']); ?>
                                    </div>
                                    <div style="font-size: 0.72rem; color: #64748b; font-family: monospace;">
                                        NIS: <?php echo htmlspecialchars($siswa['nis']); ?>
                                    </div>
                                </div>
                                <div style="font-size: 0.7rem; font-family: monospace; font-weight: 700; color: #4338ca; background: #eef2ff; padding: 2px 6px; border-radius: 4px; text-align: center;">
                                    <?php echo htmlspecialchars($siswa['nomor_peserta'] ?: '-'); ?>
                                </div>
                            <?php else: ?>
                                <div style="text-align: center; margin: auto 0; color: #94a3b8; font-size: 0.8rem; font-weight: 600;">
                                    <i data-lucide="armchair" style="width: 20px; height: 20px; margin: 0 auto 4px; display: block; opacity: 0.5;"></i>
                                    Meja Kosong
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div></div>
                    <?php endif; ?>
                <?php endfor; ?>
            <?php endfor; ?>
        </div>

    <?php else: ?>
        <!-- FORMATION: 2 SISWA PER MEJA GANDENG (DOUBLE) -->
        <div style="display: grid; grid-template-columns: repeat(<?php echo $kolom; ?>, minmax(280px, 1fr)); gap: 1.25rem; overflow-x: auto; padding: 10px 0;">
            <?php for ($b = 1; $b <= $baris; $b++): ?>
                <?php for ($k = 1; $k <= $kolom; $k++): 
                    $noMeja1 = $gridMap[$b][$k][1] ?? null;
                    $noMeja2 = $gridMap[$b][$k][2] ?? null;
                    $siswa1 = ($noMeja1 && isset($pesertaMeja[$noMeja1])) ? $pesertaMeja[$noMeja1] : null;
                    $siswa2 = ($noMeja2 && isset($pesertaMeja[$noMeja2])) ? $pesertaMeja[$noMeja2] : null;
                ?>
                    <div class="desk-double-container">
                        <div class="desk-double-badge">MEJA GANDENG <?php echo $k; ?>-<?php echo $b; ?></div>

                        <!-- Bangku Kiri (Meja 1) -->
                        <div class="desk-card <?php echo $siswa1 ? '' : 'empty'; ?>" style="border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <span class="pill pill-green" style="font-weight: 800; font-size: 0.72rem; padding: 1px 6px;">
                                    Meja <?php echo $noMeja1 ? sprintf("%02d", $noMeja1) : '-'; ?> (L)
                                </span>
                                <?php if ($siswa1): ?>
                                    <span class="badge-grade-<?php echo $siswa1['tingkat'] ?? '7'; ?>" style="font-weight: 800; font-size: 0.7rem; padding: 1px 5px; border-radius: 4px;">
                                        <?php echo htmlspecialchars($siswa1['nama_kelas']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if ($siswa1): ?>
                                <div style="margin: 3px 0;">
                                    <div style="font-weight: 800; font-size: 0.8rem; color: #0f172a; line-height: 1.2; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?php echo htmlspecialchars($siswa1['nama_siswa']); ?>
                                    </div>
                                    <div style="font-size: 0.7rem; color: #64748b; font-family: monospace;">NIS: <?php echo htmlspecialchars($siswa1['nis']); ?></div>
                                </div>
                                <div style="font-size: 0.68rem; font-family: monospace; font-weight: 700; color: #4338ca; background: #eef2ff; padding: 1px 4px; border-radius: 4px; text-align: center;">
                                    <?php echo htmlspecialchars($siswa1['nomor_peserta'] ?: '-'); ?>
                                </div>
                            <?php else: ?>
                                <div style="text-align: center; margin: auto 0; color: #94a3b8; font-size: 0.75rem;">
                                    Kosong
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Bangku Kanan (Meja 2) -->
                        <div class="desk-card <?php echo $siswa2 ? '' : 'empty'; ?>" style="border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <span class="pill pill-green" style="font-weight: 800; font-size: 0.72rem; padding: 1px 6px;">
                                    Meja <?php echo $noMeja2 ? sprintf("%02d", $noMeja2) : '-'; ?> (R)
                                </span>
                                <?php if ($siswa2): ?>
                                    <span class="badge-grade-<?php echo $siswa2['tingkat'] ?? '7'; ?>" style="font-weight: 800; font-size: 0.7rem; padding: 1px 5px; border-radius: 4px;">
                                        <?php echo htmlspecialchars($siswa2['nama_kelas']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if ($siswa2): ?>
                                <div style="margin: 3px 0;">
                                    <div style="font-weight: 800; font-size: 0.8rem; color: #0f172a; line-height: 1.2; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?php echo htmlspecialchars($siswa2['nama_siswa']); ?>
                                    </div>
                                    <div style="font-size: 0.7rem; color: #64748b; font-family: monospace;">NIS: <?php echo htmlspecialchars($siswa2['nis']); ?></div>
                                </div>
                                <div style="font-size: 0.68rem; font-family: monospace; font-weight: 700; color: #4338ca; background: #eef2ff; padding: 1px 4px; border-radius: 4px; text-align: center;">
                                    <?php echo htmlspecialchars($siswa2['nomor_peserta'] ?: '-'); ?>
                                </div>
                            <?php else: ?>
                                <div style="text-align: center; margin: auto 0; color: #94a3b8; font-size: 0.75rem;">
                                    Kosong
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endfor; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

    <!-- Bagian Belakang Kelas -->
    <div style="margin-top: 2rem; padding: 12px; background: #e2e8f0; border-radius: 12px; text-align: center; font-size: 0.8rem; font-weight: 700; color: #64748b; letter-spacing: 1px;">
        <i data-lucide="arrow-down" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i> BAGIAN BELAKANG RUANGAN
    </div>
</div>

<!-- Modal Atur & Acak Meja Siswa (Randomize) -->
<div id="modalAcakSiswa" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.65); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="z-card" style="width: 100%; max-width: 560px; padding: 2.25rem; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--z-text); display: flex; align-items: center; gap: 10px; margin: 0;">
                <span style="width: 40px; height: 40px; border-radius: 12px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="shuffle" style="width: 22px; height: 22px;"></i>
                </span>
                Atur & Acak Meja Siswa
            </h3>
            <button type="button" onclick="document.getElementById('modalAcakSiswa').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--z-text-muted);">&times;</button>
        </div>

        <form id="formAcakSiswa" action="<?php echo \App\Core\Helper::url('/asesmen/denah/acak-siswa'); ?>" method="POST" onsubmit="handleAcakSubmit(event)">
            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 0.88rem;">Pilih Event Ujian Target <span style="color: red;">*</span></label>
                <select name="event_id" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                    <?php foreach($events as $ev): ?>
                        <option value="<?php echo $ev['id']; ?>" <?php echo $eventId == $ev['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($ev['nama']); ?> (<?php echo htmlspecialchars($ev['kode']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 0.88rem;">Pilihan Ruangan:</label>
                    <select name="ruang_id" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <option value="0">Semua Ruangan Sekaligus</option>
                        <?php foreach($ruangList as $r): ?>
                            <option value="<?php echo $r['id']; ?>" <?php echo $ruangId == $r['id'] ? 'selected' : ''; ?>>
                                Khusus <?php echo htmlspecialchars($r['nama_ruang']); ?> Saja
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 0.88rem;">Sasaran Tingkat Siswa:</label>
                    <select name="tingkat" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <option value="Semua">Semua (Kelas 7, 8, dan 9)</option>
                        <option value="7">Kelas 7 Saja</option>
                        <option value="8">Kelas 8 Saja</option>
                        <option value="9">Kelas 9 Saja (Khusus AM)</option>
                    </select>
                </div>
            </div>

            <!-- Metode Pengacakan Meja (Radio Options) -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 800; margin-bottom: 8px; font-size: 0.9rem; color: #1e293b;">Metode Penataan / Pengacakan Meja:</label>
                
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <label style="display: flex; align-items: flex-start; gap: 12px; padding: 12px 14px; border: 1.5px solid #bbf7d0; background: #f0fdf4; border-radius: 12px; cursor: pointer;">
                        <input type="radio" name="metode" value="silang" checked style="margin-top: 3px;">
                        <div>
                            <strong style="color: #166534; font-size: 0.9rem; display: block;">🔀 Acak Silang Antar-Kelas (Rekomendasi Anti-Contek)</strong>
                            <span style="font-size: 0.78rem; color: #15803d; line-height: 1.4; display: block; margin-top: 2px;">
                                Siswa kelas 7, 8, dan 9 diacak bergantian silang. Siswa di meja sebelah dipastikan bukan dari tingkatan kelas yang sama untuk mencegah saling contek.
                            </span>
                        </div>
                    </label>

                    <label style="display: flex; align-items: flex-start; gap: 12px; padding: 12px 14px; border: 1px solid var(--z-border); background: var(--z-bg); border-radius: 12px; cursor: pointer;">
                        <input type="radio" name="metode" value="acak" style="margin-top: 3px;">
                        <div>
                            <strong style="color: var(--z-text); font-size: 0.9rem; display: block;">🎲 Acak Murni (Full Shuffle Random)</strong>
                            <span style="font-size: 0.78rem; color: var(--z-text-muted); line-height: 1.4; display: block; margin-top: 2px;">
                                Seluruh urutan siswa dikocok secara acak bebas tanpa aturan pola tertentu.
                            </span>
                        </div>
                    </label>

                    <label style="display: flex; align-items: flex-start; gap: 12px; padding: 12px 14px; border: 1px solid var(--z-border); background: var(--z-bg); border-radius: 12px; cursor: pointer;">
                        <input type="radio" name="metode" value="urut" style="margin-top: 3px;">
                        <div>
                            <strong style="color: var(--z-text); font-size: 0.9rem; display: block;">📋 Urut Sesuai Kelas & Abjad</strong>
                            <span style="font-size: 0.78rem; color: var(--z-text-muted); line-height: 1.4; display: block; margin-top: 2px;">
                                Mengisi meja secara rapi berurutan mulai Kelas 7A, 7B, 8A dst. sesuai urutan nama siswa.
                            </span>
                        </div>
                    </label>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="document.getElementById('modalAcakSiswa').style.display='none'" class="btn btn-outline" style="padding: 10px 18px;">Batal</button>
                <button type="submit" id="btnSubmitAcak" class="btn btn-primary" style="padding: 10px 24px; background: #10b981; border-color: #10b981; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="sparkles" style="width: 18px; height: 18px;"></i> Mulai Isi Meja Otomatis
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function bukaModalAcakSiswa() {
    document.getElementById('modalAcakSiswa').style.display = 'flex';
    if (window.lucide) { lucide.createIcons(); }
}

function handleAcakSubmit(e) {
    e.preventDefault();
    var form = document.getElementById('formAcakSiswa');
    var btn = document.getElementById('btnSubmitAcak');

    Swal.fire({
        title: 'Mulai Pengacakan Meja?',
        text: 'Sistem akan menempatkan siswa ke meja ujian sesuai metode yang dipilih. Data meja sebelumnya pada event/ruangan ini akan diperbarui.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Acak Sekarang!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" class="animate-spin" style="width: 18px; height: 18px;"></i> Mengacak Meja...';
            if (window.lucide) { lucide.createIcons(); }

            var formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="sparkles" style="width: 18px; height: 18px;"></i> Mulai Isi Meja Otomatis';
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Diacak!',
                        text: data.message,
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan saat memproses data.'
                    });
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="sparkles" style="width: 18px; height: 18px;"></i> Mulai Isi Meja Otomatis';
                form.submit();
            });
        }
    });
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
