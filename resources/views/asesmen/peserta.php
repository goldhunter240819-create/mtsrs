<?php
$title = $title ?? 'Distribusi Peserta Ujian | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_peserta';

ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="users" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Distribusi Peserta Ujian
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Penetapan ruangan, penomoran meja, dan generate kode peserta ujian madrasah secara otomatis.
        </p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?php echo \App\Core\Helper::url('/asesmen/denah?event_id=' . $eventId); ?>" class="btn" style="background: #10b981; color: white; font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
            <i data-lucide="layout-grid" style="width: 18px; height: 18px;"></i> Buka Denah Ruangan
        </a>
        <button type="button" onclick="document.getElementById('modalGeneratePeserta').style.display='flex'" class="btn" style="background: white; color: #4338ca; font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <i data-lucide="shuffle" style="width: 18px; height: 18px; color: #f59e0b;"></i> Atur & Acak Peserta Otomatis
        </button>
    </div>
</div>

<!-- Filter Bar -->
<div class="z-card" style="padding: 1.25rem 1.75rem; border-radius: 16px; margin-bottom: 1.5rem;">
    <form method="GET" action="<?php echo \App\Core\Helper::url('/asesmen/peserta'); ?>" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        <div style="flex: 1; min-width: 220px;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--z-text); margin-bottom: 6px;">Pilih Event Ujian:</label>
            <select name="event_id" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <?php foreach($events as $ev): ?>
                    <option value="<?php echo $ev['id']; ?>" <?php echo $eventId == $ev['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($ev['nama']); ?> (<?php echo htmlspecialchars($ev['kode']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1; min-width: 180px;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--z-text); margin-bottom: 6px;">Pilih Ruangan:</label>
            <select name="ruang_id" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <option value="0">Semua Ruangan</option>
                <?php foreach($ruangs as $r): ?>
                    <option value="<?php echo $r['id']; ?>" <?php echo $ruangId == $r['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($r['nama_ruang']); ?> (Kapasitas: <?php echo $r['kapasitas']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="padding: 10px 20px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Tampilkan Peserta
            </button>
        </div>

        <?php if (!empty($pesertaList)): ?>
        <div style="margin-left: auto;">
            <a href="<?php echo \App\Core\Helper::url('/asesmen/kartu-peserta?event_id=' . $eventId . ($ruangId ? '&ruang_id=' . $ruangId : '')); ?>" class="btn btn-outline" style="padding: 10px 18px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="printer" style="width: 16px; height: 16px;"></i> Cetak Kartu / Meja
            </a>
        </div>
        <?php endif; ?>
    </form>
</div>

<!-- Table Peserta -->
<div class="z-card" style="padding: 1.75rem; border-radius: 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--z-text);">
            Daftar Peserta Terdaftar (Total: <?php echo count($pesertaList); ?> Siswa)
        </h3>
    </div>

    <div style="overflow-x: auto;">
        <table class="z-table">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Nomor Peserta</th>
                    <th>Nomor Meja</th>
                    <th>Ruangan</th>
                    <th>NIS / NISN</th>
                    <th>Nama Peserta Didik</th>
                    <th>Kelas</th>
                    <th>L/P</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pesertaList)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 3rem; color: var(--z-text-muted);">
                        Belum ada peserta yang didistribusikan untuk event ini.<br>
                        Klik tombol <strong>Distribusi Otomatis (Generate)</strong> di atas untuk menempatkan seluruh siswa aktif ke dalam ruangan dan nomor meja secara otomatis.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($pesertaList as $idx => $p): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td>
                            <span style="font-family: monospace; font-weight: 800; color: #4338ca; background: #eef2ff; padding: 3px 8px; border-radius: 6px; font-size: 0.85rem;">
                                <?php echo htmlspecialchars($p['nomor_peserta'] ?: '-'); ?>
                            </span>
                        </td>
                        <td>
                            <span class="pill pill-green" style="font-weight: 800; font-size: 0.82rem; min-width: 30px; text-align: center;">
                                Meja <?php echo $p['nomor_meja'] ?: '-'; ?>
                            </span>
                        </td>
                        <td><strong style="color: #0f172a;"><?php echo htmlspecialchars($p['nama_ruang'] ?: '-'); ?></strong></td>
                        <td><?php echo htmlspecialchars($p['nis'] ?: '-'); ?> / <?php echo htmlspecialchars($p['nisn'] ?: '-'); ?></td>
                        <td><strong style="color: #1e293b;"><?php echo htmlspecialchars($p['nama_siswa']); ?></strong></td>
                        <td><span class="pill pill-blue" style="font-size: 0.75rem;"><?php echo htmlspecialchars($p['nama_kelas'] ?: '-'); ?></span></td>
                        <td><?php echo htmlspecialchars($p['jenis_kelamin'] ?: '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Generate Peserta -->
<div id="modalGeneratePeserta" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.65); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="z-card" style="width: 100%; max-width: 540px; padding: 2rem; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--z-text); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="shuffle" style="color: #10b981;"></i> Atur & Acak Peserta ke Meja Ujian
            </h3>
            <button type="button" onclick="document.getElementById('modalGeneratePeserta').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--z-text-muted);">&times;</button>
        </div>

        <form action="<?php echo \App\Core\Helper::url('/asesmen/peserta/acak'); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengacak/menata peserta? Meja ruangan pada event ini akan diisi secara otomatis.');">
            <input type="hidden" name="redirect_to" value="<?php echo \App\Core\Helper::url('/asesmen/peserta?event_id=' . $eventId); ?>">

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
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Sasaran Ruangan:</label>
                    <select name="ruang_id" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <option value="0">Semua Ruangan</option>
                        <?php foreach($ruangs as $r): ?>
                            <option value="<?php echo $r['id']; ?>" <?php echo $ruangId == $r['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($r['nama_ruang']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Tingkat Kelas:</label>
                    <select name="tingkat" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <option value="Semua">Semua (7, 8, 9)</option>
                        <option value="7">Kelas 7 Saja</option>
                        <option value="8">Kelas 8 Saja</option>
                        <option value="9">Kelas 9 Saja (AM)</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 0.88rem;">Metode Pengacakan Meja:</label>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: 1px solid #bbf7d0; background: #f0fdf4; border-radius: 10px; cursor: pointer;">
                        <input type="radio" name="metode" value="silang" checked>
                        <div>
                            <strong style="color: #166534; font-size: 0.85rem;">🔀 Acak Silang Antar-Kelas (Anti-Contek)</strong>
                            <div style="font-size: 0.72rem; color: #15803d;">Diselang-seling kelas 7, 8, 9 agar bersebelahan beda kelas.</div>
                        </div>
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: 1px solid var(--z-border); background: var(--z-bg); border-radius: 10px; cursor: pointer;">
                        <input type="radio" name="metode" value="acak">
                        <div>
                            <strong style="font-size: 0.85rem;">🎲 Acak Murni (Full Shuffle Random)</strong>
                            <div style="font-size: 0.72rem; color: var(--z-text-muted);">Seluruh siswa dikocok acak bebas.</div>
                        </div>
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: 1px solid var(--z-border); background: var(--z-bg); border-radius: 10px; cursor: pointer;">
                        <input type="radio" name="metode" value="urut">
                        <div>
                            <strong style="font-size: 0.85rem;">📋 Urut Sesuai Kelas & Abjad</strong>
                            <div style="font-size: 0.72rem; color: var(--z-text-muted);">Sesuai urutan kelas & nama absen siswa.</div>
                        </div>
                    </label>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="document.getElementById('modalGeneratePeserta').style.display='none'" class="btn btn-outline" style="padding: 10px 18px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 10px 22px; background: #10b981; border-color: #10b981; font-weight: 700;">
                    Mulai Isi Meja Otomatis
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
