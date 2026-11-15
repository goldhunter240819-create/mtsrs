<?php
$title = $title ?? 'Jadwal Pelaksanaan Ujian | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_jadwal';

ob_start();
?>

<style>
.schedule-wrapper {
    width: 100%;
    overflow-x: auto;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    border: 1px solid #e2e8f0;
    margin-bottom: 2rem;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f8fafc;
}
.schedule-wrapper::-webkit-scrollbar { height: 10px; }
.schedule-wrapper::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
.schedule-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 2px solid #f8fafc; }

.master-schedule-table {
    width: max-content;
    border-collapse: separate;
    border-spacing: 0;
    min-width: 100%;
}
.master-schedule-table th, .master-schedule-table td {
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 12px;
    vertical-align: middle;
}
.master-schedule-table thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: center;
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: 0 1px 0 #e2e8f0;
}
.kelas-header {
    width: 155px;
    min-width: 155px;
    max-width: 155px;
    background: #f1f5f9 !important;
    text-align: center;
}
.kelas-name { margin-bottom: 4px; font-weight: 900; color: #1e293b; font-size: 0.82rem; }

/* Sticky columns */
.sticky-col-1, .sticky-col-2 {
    position: sticky;
    left: 0;
    background: #f8fafc;
    z-index: 20;
}
.sticky-col-2 { left: 45px; border-right: 2px solid #cbd5e1 !important; }

.master-schedule-table thead .sticky-col-1, 
.master-schedule-table thead .sticky-col-2 {
    z-index: 30;
}

.cell-empty {
    background: #fff;
    text-align: center;
    color: #cbd5e1;
    font-size: 0.85rem;
    transition: 0.2s;
}
.cell-empty:hover {
    background: #f8fafc;
    color: #4f46e5;
}
.cell-filled {
    background: #f0fdf4;
    border-left: 3.5px solid #10b981;
    transition: 0.2s;
    cursor: default;
    width: 155px;
    max-width: 155px;
    overflow: hidden;
    padding: 8px 10px;
}
.cell-filled:hover {
    background: #dcfce7;
    transform: scale(1.02);
    box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    z-index: 5;
    position: relative;
}

.mapel-name {
    display: block;
    font-weight: 800;
    color: #0f172a;
    font-size: 0.78rem;
    line-height: 1.25;
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.guru-name {
    display: block;
    font-size: 0.68rem;
    color: #64748b;
    font-weight: 600;
    line-height: 1.1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Row variations per day */
.day-card-header {
    background: #2563eb;
    padding: 12px 20px;
    border-radius: 12px 12px 0 0;
    color: white;
    font-weight: 800;
    font-size: 1.05rem;
    text-transform: uppercase;
    margin-top: 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}
</style>

<div class="modern-page-header" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="calendar" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Jadwal Pelaksanaan Ujian
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Kelola alokasi jadwal mata pelajaran ujian madrasah harian per rombel kelas.
        </p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <button type="button" onclick="bukaModalJadwal()" class="btn" style="background: white; color: #1d4ed8; font-weight: 800; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Tambah Jadwal Ujian
        </button>
        <?php if ($eventId > 0): ?>
        <a href="<?php echo \App\Core\Helper::url('/asesmen/jadwal/cetak?event_id=' . $eventId); ?>" target="_blank" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4); font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="printer" style="width: 18px; height: 18px;"></i> Cetak Jadwal (A4)
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filter Event Ujian Bar -->
<div class="z-card" style="padding: 1.25rem 1.5rem; border-radius: 16px; margin-bottom: 1.5rem; display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: center; justify-content: space-between;">
    <form method="GET" action="<?php echo \App\Core\Helper::url('/asesmen/jadwal'); ?>" style="display: flex; align-items: center; gap: 12px; flex: 1; max-width: 500px;">
        <label style="font-weight: 800; font-size: 0.88rem; color: var(--z-text); white-space: nowrap;">Event Ujian:</label>
        <select name="event_id" onchange="this.form.submit()" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none; font-weight: 600;">
            <?php foreach($events as $ev): ?>
                <option value="<?php echo $ev['id']; ?>" <?php echo $eventId == $ev['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($ev['nama']); ?> (<?php echo htmlspecialchars($ev['kode']); ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <div style="display: flex; gap: 10px; align-items: center;">
        <span class="pill pill-blue" style="font-size: 0.82rem; font-weight: 700; padding: 6px 14px;">
            <i data-lucide="calendar" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i> Total: <?php echo count($dates); ?> Hari Ujian
        </span>
        <span class="pill pill-purple" style="font-size: 0.82rem; font-weight: 700; padding: 6px 14px;">
            <i data-lucide="layers" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i> <?php echo count($rawList); ?> Sesi Terjadwal
        </span>
    </div>
</div>

<?php if (empty($dates)): ?>
<!-- Empty State -->
<div class="z-card" style="padding: 4rem 2rem; border-radius: 20px; text-align: center; margin-top: 1rem;">
    <div style="width: 70px; height: 70px; border-radius: 20px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
        <i data-lucide="calendar-plus" style="width: 36px; height: 36px;"></i>
    </div>
    <h3 style="font-size: 1.25rem; font-weight: 800; color: #1e293b; margin-bottom: 8px;">Belum Ada Jadwal Pelaksanaan Ujian</h3>
    <p style="color: #64748b; font-size: 0.9rem; max-width: 500px; margin: 0 auto 1.5rem auto;">
        Jadwal ujian belum disusun untuk event ini. Klik tombol di bawah untuk menambahkan mata pelajaran dan waktu ujian per rombel kelas.
    </p>
    <button type="button" onclick="bukaModalJadwal()" class="btn btn-primary" style="padding: 10px 22px; font-weight: 700; background: #2563eb; border-color: #2563eb;">
        <i data-lucide="plus"></i> Tambah Jadwal Ujian Sekarang
    </button>
</div>
<?php else: ?>

<!-- Loop per Date (SIAKAD Style Schedule Matrix) -->
<?php foreach($dates as $tgl => $dateInfo): 
    $slots = $timeSlotsPerDate[$tgl] ?? [];
?>
<div class="day-card-header">
    <div style="display: flex; align-items: center; gap: 10px;">
        <i data-lucide="calendar" style="width: 20px; height: 20px;"></i>
        <span><?php echo strtoupper($dateInfo['formatted']); ?></span>
    </div>
    <button type="button" onclick="tambahJadwalTanggal('<?php echo $tgl; ?>')" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4); font-size: 0.78rem; font-weight: 700; padding: 5px 12px; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
        <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Tambah Sesi
    </button>
</div>

<div class="schedule-wrapper" style="border-radius: 0 0 14px 14px; margin-bottom: 2rem;">
    <table class="master-schedule-table">
        <thead>
            <tr>
                <th class="sticky-col-1" style="width: 45px;">NO</th>
                <th class="sticky-col-2" style="width: 130px;">WAKTU</th>
                <?php foreach($kelas as $k): ?>
                    <th class="kelas-header">
                        <div class="kelas-name"><?php echo htmlspecialchars($k['nama_kelas']); ?></div>
                        <a href="javascript:void(0)" onclick="tambahJadwalKelas(<?php echo $k['id']; ?>, '<?php echo addslashes($k['nama_kelas']); ?>', '<?php echo $tgl; ?>')" class="btn-atur" title="Atur Jadwal Kelas Ini" style="display:inline-block; background:#e0e7ff; color:#2563eb; font-size:0.65rem; font-weight:700; padding:2px 8px; border-radius:10px; text-decoration:none;">[Atur]</a>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($slots)): ?>
                <tr>
                    <td colspan="<?php echo 2 + count($kelas); ?>" style="text-align: center; padding: 2.5rem; color: #94a3b8;">
                        Belum ada sesi ujian di hari ini.
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach($slots as $jam): ?>
                <tr>
                    <td class="sticky-col-1 text-center" style="font-size: 0.85rem; font-weight: 800; color: #475569; text-align: center;">
                        <?php echo $no++; ?>
                    </td>
                    <td class="sticky-col-2 text-center" style="font-size: 0.78rem; font-weight: 800; font-family: monospace; color: #2563eb; text-align: center;">
                        <?php echo htmlspecialchars($jam); ?><br>
                        <small style="color: #64748b; font-weight: 600; font-family: sans-serif; font-size: 0.7rem;">Sesi <?php echo $no - 1; ?></small>
                    </td>

                    <?php foreach($kelas as $k): ?>
                        <?php 
                        $cell = $jadwalMatrix[$tgl][$jam][$k['id']] ?? null;
                        if ($cell): 
                        ?>
                        <td class="cell-filled" title="<?php echo htmlspecialchars($cell['nama_mapel']); ?> (<?php echo $cell['durasi_menit']; ?> Menit)">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 4px;">
                                <span class="mapel-name"><?php echo htmlspecialchars($cell['nama_mapel']); ?></span>
                                <button type="button" onclick="hapusJadwal(<?php echo $cell['id']; ?>, '<?php echo addslashes($cell['nama_mapel']); ?>')" style="background: none; border: none; cursor: pointer; color: #ef4444; padding: 0; font-size: 0.85rem; line-height: 1;" title="Hapus Mapel Ini">&times;</button>
                            </div>
                            <span class="guru-name">
                                <i data-lucide="clock" style="width: 10px; height: 10px; display: inline;"></i> <?php echo $cell['durasi_menit']; ?>m
                                &bull; <strong style="color: #2563eb;"><?php echo $cell['tingkat'] === 'Semua' ? 'Serentak' : 'Tingkat ' . $cell['tingkat']; ?></strong>
                            </span>
                        </td>
                        <?php else: ?>
                        <td class="cell-empty" onclick="tambahJadwalSlot('<?php echo $tgl; ?>', '<?php echo $jam; ?>', <?php echo $k['id']; ?>, '<?php echo addslashes($k['nama_kelas']); ?>')" style="cursor: pointer;" title="Klik untuk tambah jadwal kelas ini">
                            <i data-lucide="plus" style="width: 12px; height: 12px; opacity: 0.25;"></i>
                        </td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php endforeach; ?>

<?php endif; ?>

<!-- Modal Tambah Jadwal Ujian -->
<div id="modalAddJadwal" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.65); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="z-card" style="width: 100%; max-width: 560px; padding: 2rem; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 id="modalJadwalTitle" style="font-size: 1.25rem; font-weight: 800; color: var(--z-text); display: flex; align-items: center; gap: 8px; margin: 0;">
                <i data-lucide="calendar-plus" style="color: #2563eb;"></i> Tambah Jadwal Ujian
            </h3>
            <button type="button" onclick="document.getElementById('modalAddJadwal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--z-text-muted);">&times;</button>
        </div>

        <form action="<?php echo \App\Core\Helper::url('/asesmen/jadwal/save'); ?>" method="POST">
            <input type="hidden" name="id" id="jadwal_id" value="">

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 0.88rem;">Pilih Event Ujian <span style="color:red;">*</span></label>
                <select name="event_id" id="jadwal_event_id" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                    <?php foreach($events as $ev): ?>
                        <option value="<?php echo $ev['id']; ?>" <?php echo $eventId == $ev['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($ev['nama']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 0.88rem;">Mata Pelajaran <span style="color:red;">*</span></label>
                    <select name="mapel_id" id="jadwal_mapel_id" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <?php foreach($mapels as $m): ?>
                            <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['nama_mapel']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 0.88rem;">Sasaran Kelas</label>
                    <select name="tingkat" id="jadwal_tingkat" onchange="toggleKelasSelect()" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <option value="Semua">Semua Kelas (7A-9C)</option>
                        <option value="7">Kelas 7 Saja</option>
                        <option value="8">Kelas 8 Saja</option>
                        <option value="9">Kelas 9 Saja</option>
                        <option value="khusus">Khusus 1 Rombel</option>
                    </select>
                </div>
            </div>

            <div id="boxPilihKelas" style="display: none; margin-bottom: 1.2rem;">
                <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 0.88rem;">Pilih Rombel Kelas Tertentu:</label>
                <select name="kelas_id" id="jadwal_kelas_id" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                    <?php foreach($kelas as $k): ?>
                        <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 0.88rem;">Tanggal Pelaksanaan</label>
                    <input type="date" name="tanggal" id="jadwal_tanggal" value="<?php echo date('Y-m-d'); ?>" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 0.88rem;">Durasi (Menit)</label>
                    <input type="number" name="durasi_menit" id="jadwal_durasi" value="90" min="15" max="300" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 0.88rem;">Jam Mulai</label>
                    <input type="time" name="jam_mulai" id="jadwal_jam_mulai" value="07:30" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 0.88rem;">Jam Selesai</label>
                    <input type="time" name="jam_selesai" id="jadwal_jam_selesai" value="09:00" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="document.getElementById('modalAddJadwal').style.display='none'" class="btn btn-outline" style="padding: 10px 18px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 10px 22px; background: #2563eb; border-color: #2563eb; font-weight: 700;">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<!-- Form Hapus Jadwal (Hidden) -->
<form id="formHapusJadwal" action="<?php echo \App\Core\Helper::url('/asesmen/jadwal/delete'); ?>" method="POST" style="display: none;">
    <input type="hidden" name="id" id="hapus_jadwal_id">
    <input type="hidden" name="event_id" value="<?php echo $eventId; ?>">
</form>

<script>
function toggleKelasSelect() {
    var val = document.getElementById('jadwal_tingkat').value;
    document.getElementById('boxPilihKelas').style.display = (val === 'khusus') ? 'block' : 'none';
}

function bukaModalJadwal() {
    document.getElementById('modalJadwalTitle').innerHTML = '<i data-lucide="calendar-plus" style="color: #2563eb;"></i> Tambah Jadwal Ujian';
    document.getElementById('jadwal_id').value = '';
    document.getElementById('jadwal_tingkat').value = 'Semua';
    document.getElementById('boxPilihKelas').style.display = 'none';
    document.getElementById('modalAddJadwal').style.display = 'flex';
    if (window.lucide) { lucide.createIcons(); }
}

function tambahJadwalTanggal(tgl) {
    bukaModalJadwal();
    document.getElementById('jadwal_tanggal').value = tgl;
}

function tambahJadwalKelas(kelasId, namaKelas, tgl) {
    bukaModalJadwal();
    document.getElementById('modalJadwalTitle').innerHTML = '<i data-lucide="calendar-plus" style="color: #2563eb;"></i> Atur Jadwal: ' + namaKelas;
    if (tgl) {
        document.getElementById('jadwal_tanggal').value = tgl;
    }
    document.getElementById('jadwal_tingkat').value = 'khusus';
    document.getElementById('boxPilihKelas').style.display = 'block';
    document.getElementById('jadwal_kelas_id').value = kelasId;
}

function tambahJadwalSlot(tgl, jamStr, kelasId, namaKelas) {
    bukaModalJadwal();
    document.getElementById('modalJadwalTitle').innerHTML = '<i data-lucide="calendar-plus" style="color: #2563eb;"></i> Atur Sesi: ' + namaKelas;
    document.getElementById('jadwal_tanggal').value = tgl;
    if (jamStr && jamStr.includes(' - ')) {
        var parts = jamStr.split(' - ');
        document.getElementById('jadwal_jam_mulai').value = parts[0].trim();
        document.getElementById('jadwal_jam_selesai').value = parts[1].replace(' WIB', '').trim();
    }
    document.getElementById('jadwal_tingkat').value = 'khusus';
    document.getElementById('boxPilihKelas').style.display = 'block';
    document.getElementById('jadwal_kelas_id').value = kelasId;
}

function hapusJadwal(id, mapel) {
    Swal.fire({
        title: 'Hapus Jadwal Ujian?',
        text: 'Mata pelajaran ' + (mapel || '') + ' pada sesi ini akan dihapus dari jadwal.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('hapus_jadwal_id').value = id;
            document.getElementById('formHapusJadwal').submit();
        }
    });
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
