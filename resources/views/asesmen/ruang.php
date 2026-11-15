<?php
$title = $title ?? 'Pengaturan Ruang Ujian | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_ruang';

ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="building" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Ruang & Denah Ujian
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Pengaturan nomor ruang, daya tampung peserta, dan penataan nomor meja ujian madrasah.
        </p>
    </div>
    <div>
        <button type="button" onclick="document.getElementById('modalAddRuang').style.display='flex'" class="btn" style="background: white; color: #4338ca; font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Tambah Ruang Ujian
        </button>
    </div>
</div>

<div class="z-card" style="padding: 1.75rem; border-radius: 16px;">
    <div style="overflow-x: auto;">
        <table class="z-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Ruang</th>
                    <th>Lokasi Ruangan</th>
                    <th>Kapasitas Meja</th>
                    <th>Event Terkait</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ruangList)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--z-text-muted);">
                        Belum ada ruang ujian yang didaftarkan. Klik tombol <strong>Tambah Ruang Ujian</strong> di atas.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($ruangList as $idx => $r): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td><strong style="color: #1e293b; font-size: 0.95rem;"><?php echo htmlspecialchars($r['nama_ruang']); ?></strong></td>
                        <td><?php echo htmlspecialchars($r['lokasi'] ?: '-'); ?></td>
                        <td>
                            <span class="pill pill-green" style="font-weight: 700; font-size: 0.8rem;">
                                <?php echo $r['kapasitas']; ?> Peserta
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($r['nama_event'] ?: 'Semua Event'); ?></td>
                        <td><?php echo htmlspecialchars($r['keterangan'] ?: '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Ruang -->
<div id="modalAddRuang" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="z-card" style="width: 100%; max-width: 500px; padding: 2rem; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--z-text); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="plus-circle" style="color: #4f46e5;"></i> Tambah Ruang Ujian
            </h3>
            <button type="button" onclick="document.getElementById('modalAddRuang').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--z-text-muted);">&times;</button>
        </div>

        <form action="<?php echo \App\Core\Helper::url('/asesmen/ruang/save'); ?>" method="POST">
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Nama Ruang <span style="color: red;">*</span></label>
                <input type="text" name="nama_ruang" required placeholder="Contoh: Ruang 01 (Kelas 7A)" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Kapasitas Kursi/Meja</label>
                    <input type="number" name="kapasitas" value="20" min="1" max="100" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Lokasi / Gedung</label>
                    <input type="text" name="lokasi" placeholder="Contoh: Lantai 2 Gedung Timur" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
            </div>

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Event Ujian Terkait</label>
                <select name="event_id" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                    <option value="">Semua Event Ujian</option>
                    <?php foreach($events as $ev): ?>
                        <option value="<?php echo $ev['id']; ?>"><?php echo htmlspecialchars($ev['nama']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Keterangan</label>
                <input type="text" name="keterangan" placeholder="Contoh: Dilengkapi stopkontak & Wi-Fi" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="document.getElementById('modalAddRuang').style.display='none'" class="btn btn-outline" style="padding: 10px 18px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 10px 22px; background: #4f46e5; border-color: #4f46e5; font-weight: 700;">Simpan Ruangan</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
