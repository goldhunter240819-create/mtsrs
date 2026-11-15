<?php
$title = $title ?? 'Jadwal Pelaksanaan Ujian | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_jadwal';

ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="clock" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Jadwal Pelaksanaan Ujian
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Waktu pelaksanaan, durasi pengerjaan, dan mata pelajaran per sesi ujian madrasah.
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" onclick="document.getElementById('modalAddJadwal').style.display='flex'" class="btn" style="background: white; color: #4338ca; font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Tambah Jadwal
        </button>
        <a href="<?php echo \App\Core\Helper::url('/asesmen/jadwal/cetak'); ?>" target="_blank" class="btn btn-outline" style="background: rgba(255,255,255,0.15); color: white; border-color: rgba(255,255,255,0.4); font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="printer" style="width: 18px; height: 18px;"></i> Cetak Jadwal
        </a>
    </div>
</div>

<div class="z-card" style="padding: 1.75rem; border-radius: 16px;">
    <div style="overflow-x: auto;">
        <table class="z-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Hari & Tanggal</th>
                    <th>Jam / Waktu</th>
                    <th>Durasi</th>
                    <th>Event Ujian</th>
                    <th>Mata Pelajaran</th>
                    <th>Tingkat</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($jadwalList)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--z-text-muted);">
                        Belum ada jadwal pelaksanaan ujian yang diatur. Klik tombol <strong>Tambah Jadwal</strong> di atas.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($jadwalList as $idx => $j): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td><strong><?php echo date('d/m/Y', strtotime($j['tanggal'])); ?></strong></td>
                        <td><?php echo substr($j['jam_mulai'], 0, 5); ?> - <?php echo substr($j['jam_selesai'], 0, 5); ?> WIB</td>
                        <td><span class="pill pill-purple" style="font-size: 0.75rem;"><?php echo $j['durasi_menit']; ?> Menit</span></td>
                        <td><?php echo htmlspecialchars($j['nama_event'] ?: '-'); ?></td>
                        <td><strong style="color: #1e293b;"><?php echo htmlspecialchars($j['nama_mapel'] ?: '-'); ?></strong></td>
                        <td><span class="pill pill-blue" style="font-size: 0.75rem;"><?php echo htmlspecialchars($j['tingkat'] ?: 'Semua'); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Jadwal -->
<div id="modalAddJadwal" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="z-card" style="width: 100%; max-width: 520px; padding: 2rem; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--z-text); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="plus-circle" style="color: #4f46e5;"></i> Tambah Jadwal Ujian
            </h3>
            <button type="button" onclick="document.getElementById('modalAddJadwal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--z-text-muted);">&times;</button>
        </div>

        <form action="<?php echo \App\Core\Helper::url('/asesmen/jadwal/save'); ?>" method="POST">
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Pilih Event Ujian <span style="color:red;">*</span></label>
                <select name="event_id" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                    <?php foreach($events as $ev): ?>
                        <option value="<?php echo $ev['id']; ?>"><?php echo htmlspecialchars($ev['nama']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Mata Pelajaran <span style="color:red;">*</span></label>
                    <select name="mapel_id" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <?php foreach($mapels as $m): ?>
                            <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['nama_mapel']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Tingkat Kelas</label>
                    <select name="tingkat" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <option value="Semua">Semua (7, 8, 9)</option>
                        <option value="7">Kelas 7</option>
                        <option value="8">Kelas 8</option>
                        <option value="9">Kelas 9</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Tanggal Ujian</label>
                    <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Durasi (Menit)</label>
                    <input type="number" name="durasi_menit" value="90" min="15" max="300" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Jam Mulai</label>
                    <input type="time" name="jam_mulai" value="07:30" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Jam Selesai</label>
                    <input type="time" name="jam_selesai" value="09:00" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="document.getElementById('modalAddJadwal').style.display='none'" class="btn btn-outline" style="padding: 10px 18px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 10px 22px; background: #4f46e5; border-color: #4f46e5; font-weight: 700;">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
