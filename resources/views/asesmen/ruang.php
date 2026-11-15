<?php
$title = $title ?? 'Pengaturan Ruang Ujian | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_ruang';

ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="building" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Ruang & Formasi Meja Ujian
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Atur tata letak meja (1x5x6 atau 2x3x6 gandeng), kapasitas ruangan, dan denah ujian madrasah.
        </p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?php echo \App\Core\Helper::url('/asesmen/denah'); ?>" class="btn" style="background: #f59e0b; color: white; font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);">
            <i data-lucide="layout-grid" style="width: 18px; height: 18px;"></i> Buka Denah & Atur Siswa
        </a>
        <button type="button" onclick="bukaModalRuang('add')" class="btn" style="background: white; color: #4338ca; font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Tambah Ruang Ujian
        </button>
    </div>
</div>

<!-- Info Alert / Panduan Formasi -->
<div class="z-card" style="padding: 1.25rem 1.5rem; border-radius: 14px; margin-bottom: 1.5rem; background: #eef2ff; border: 1px solid #c7d2fe; display: flex; align-items: center; gap: 14px;">
    <div style="width: 38px; height: 38px; border-radius: 10px; background: #e0e7ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        <i data-lucide="info" style="width: 22px; height: 22px;"></i>
    </div>
    <div style="font-size: 0.88rem; color: #3730a3; line-height: 1.5;">
        <strong>Fleksibilitas Formasi Meja:</strong> Anda dapat mengatur model meja <strong>1 Siswa Mandiri</strong> (contoh: <code>1 x 5 x 6 = 30 Peserta</code>) atau model <strong>2 Meja Gandeng</strong> (contoh: <code>2 x 3 x 6 = 36 Peserta</code>). Setelah formasi ruangan tersimpan, Anda dapat mengklik tombol <strong>Denah & Atur Siswa</strong> untuk mengacak nama peserta secara otomatis.
    </div>
</div>

<div class="z-card" style="padding: 1.75rem; border-radius: 16px;">
    <div style="overflow-x: auto;">
        <table class="z-table">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Nama Ruang</th>
                    <th>Formasi Meja</th>
                    <th>Kapasitas / Terisi</th>
                    <th>Pola Penomoran</th>
                    <th>Lokasi & Keterangan</th>
                    <th style="width: 220px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ruangList)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem; color: var(--z-text-muted);">
                        Belum ada ruang ujian yang didaftarkan. Klik tombol <strong>Tambah Ruang Ujian</strong> di atas.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($ruangList as $idx => $r): 
                        $t = (int)($r['tipe_meja'] ?: 1);
                        $k = (int)($r['jumlah_kolom'] ?: 5);
                        $b = (int)($r['jumlah_baris'] ?: 6);
                        $terisi = (int)($r['terisi'] ?? 0);
                        $kapasitas = (int)$r['kapasitas'];
                    ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td>
                            <strong style="color: #1e293b; font-size: 0.95rem; display: block;">
                                <?php echo htmlspecialchars($r['nama_ruang']); ?>
                            </strong>
                            <small style="color: var(--z-text-muted);"><?php echo htmlspecialchars($r['lokasi'] ?: 'MTs Roudlotus Sholihin'); ?></small>
                        </td>
                        <td>
                            <?php if ($t == 1): ?>
                                <span class="pill pill-blue" style="font-weight: 700; font-size: 0.82rem;">
                                    1 &times; <?php echo $k; ?> &times; <?php echo $b; ?> (Mandiri)
                                </span>
                            <?php else: ?>
                                <span class="pill pill-purple" style="font-weight: 700; font-size: 0.82rem;">
                                    2 &times; <?php echo $k; ?> &times; <?php echo $b; ?> (Gandeng)
                                </span>
                            <?php endif; ?>
                            <div style="font-size: 0.75rem; color: var(--z-text-muted); margin-top: 3px;">
                                <?php echo $k; ?> deret samping &times; <?php echo $b; ?> baris belakang
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <strong style="font-size: 0.95rem; color: <?php echo $terisi > 0 ? '#16a34a' : '#64748b'; ?>;">
                                    <?php echo $terisi; ?> / <?php echo $kapasitas; ?>
                                </strong>
                                <span style="font-size: 0.75rem; color: var(--z-text-muted);">Siswa</span>
                            </div>
                            <div style="width: 100%; max-width: 120px; height: 6px; background: #e2e8f0; border-radius: 99px; overflow: hidden; margin-top: 4px;">
                                <?php $pct = $kapasitas > 0 ? min(100, round(($terisi / $kapasitas) * 100)) : 0; ?>
                                <div style="height: 100%; width: <?php echo $pct; ?>%; background: #4f46e5; border-radius: 99px;"></div>
                            </div>
                        </td>
                        <td>
                            <span style="font-family: monospace; font-size: 0.82rem; font-weight: 700; color: #475569; background: #f1f5f9; padding: 3px 8px; border-radius: 6px; text-transform: capitalize;">
                                <?php echo htmlspecialchars($r['pola_penomoran'] ?: 'Kolom'); ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; color: var(--z-text);"><?php echo htmlspecialchars($r['keterangan'] ?: '-'); ?></div>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 6px;">
                                <a href="<?php echo \App\Core\Helper::url('/asesmen/denah?ruang_id=' . $r['id']); ?>" class="btn btn-sm btn-primary" title="Buka Denah & Atur Siswa" style="display: flex; align-items: center; gap: 4px; padding: 6px 10px; font-size: 0.8rem;">
                                    <i data-lucide="layout-grid" style="width: 14px; height: 14px;"></i> Denah
                                </a>
                                <button type="button" onclick="editRuang(<?php echo htmlspecialchars(json_encode($r)); ?>)" class="btn btn-sm btn-outline" title="Edit Ruang & Formasi" style="padding: 6px 10px; font-size: 0.8rem;">
                                    <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                </button>
                                <form action="<?php echo \App\Core\Helper::url('/asesmen/ruang/delete'); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus <?php echo addslashes($r['nama_ruang']); ?>?');">
                                    <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus Ruangan" style="padding: 6px 10px; font-size: 0.8rem;">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah / Edit Ruang Ujian -->
<div id="modalRuang" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="z-card" style="width: 100%; max-width: 540px; padding: 2rem; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 id="modalRuangTitle" style="font-size: 1.25rem; font-weight: 800; color: var(--z-text); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="building" style="color: #4f46e5;"></i> Tambah Ruang & Formasi Meja
            </h3>
            <button type="button" onclick="document.getElementById('modalRuang').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--z-text-muted);">&times;</button>
        </div>

        <form action="<?php echo \App\Core\Helper::url('/asesmen/ruang/save'); ?>" method="POST">
            <input type="hidden" name="id" id="ruang_id" value="">

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Nama Ruang <span style="color: red;">*</span></label>
                <input type="text" name="nama_ruang" id="ruang_nama" required placeholder="Contoh: Ruang 01, Ruang 02, Lab Komputer" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
            </div>

            <!-- Tipe Formasi Meja -->
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 0.88rem; color: #4338ca;">Model / Susunan Meja:</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px 12px; border: 1px solid var(--z-border); border-radius: 10px; cursor: pointer; background: var(--z-bg);">
                        <input type="radio" name="tipe_meja" value="1" id="tipe_1" checked onchange="hitungKapasitas()">
                        <div>
                            <strong style="display: block; font-size: 0.85rem;">1 Siswa / Meja</strong>
                            <small style="color: var(--z-text-muted); font-size: 0.75rem;">Meja Mandiri (Single)</small>
                        </div>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px 12px; border: 1px solid var(--z-border); border-radius: 10px; cursor: pointer; background: var(--z-bg);">
                        <input type="radio" name="tipe_meja" value="2" id="tipe_2" onchange="hitungKapasitas()">
                        <div>
                            <strong style="display: block; font-size: 0.85rem;">2 Siswa / Meja</strong>
                            <small style="color: var(--z-text-muted); font-size: 0.75rem;">Meja Gandeng (Double)</small>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Grid Kolom dan Baris -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Deret Meja ke Samping (Kolom)</label>
                    <input type="number" name="jumlah_kolom" id="ruang_kolom" value="5" min="1" max="15" oninput="hitungKapasitas()" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                    <small style="color: var(--z-text-muted); font-size: 0.72rem;">Jumlah baris bangku menyamping</small>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Baris Meja ke Belakang (Baris)</label>
                    <input type="number" name="jumlah_baris" id="ruang_baris" value="6" min="1" max="20" oninput="hitungKapasitas()" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                    <small style="color: var(--z-text-muted); font-size: 0.72rem;">Jumlah baris bangku ke belakang</small>
                </div>
            </div>

            <!-- Live Calculation Info Box -->
            <div id="boxLiveCalc" style="padding: 12px 16px; border-radius: 10px; background: #f0fdf4; border: 1px solid #bbf7d0; margin-bottom: 1.2rem;">
                <div style="font-size: 0.78rem; font-weight: 700; color: #166534; text-transform: uppercase;">Perhitungan Kapasitas Meja:</div>
                <div id="liveCalcText" style="font-size: 1.05rem; font-weight: 800; color: #15803d; margin-top: 2px;">
                    1 &times; 5 Kolom &times; 6 Baris = 30 Kursi Peserta
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Kapasitas Total Kursi</label>
                    <input type="number" name="kapasitas" id="ruang_kapasitas" value="30" min="1" max="150" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Pola Penomoran Meja</label>
                    <select name="pola_penomoran" id="ruang_pola" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <option value="kolom">Urut Kolom (Depan-Belakang)</option>
                        <option value="baris">Urut Baris (Kiri-Kanan)</option>
                        <option value="zigzag">Zig-Zag / Pola Ular</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Lokasi Ruangan / Gedung</label>
                <input type="text" name="lokasi" id="ruang_lokasi" placeholder="Contoh: Gedung A - Lantai 1" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Keterangan Tambahan</label>
                <input type="text" name="keterangan" id="ruang_keterangan" placeholder="Contoh: Meja single formasi 1x5x6, stopkontak aman" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="document.getElementById('modalRuang').style.display='none'" class="btn btn-outline" style="padding: 10px 18px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 10px 22px; background: #4f46e5; border-color: #4f46e5; font-weight: 700;">Simpan Ruang & Formasi</button>
            </div>
        </form>
    </div>
</div>

<script>
function hitungKapasitas() {
    var tipe = document.querySelector('input[name="tipe_meja"]:checked') ? parseInt(document.querySelector('input[name="tipe_meja"]:checked').value) : 1;
    var kolom = parseInt(document.getElementById('ruang_kolom').value) || 1;
    var baris = parseInt(document.getElementById('ruang_baris').value) || 1;
    var total = tipe * kolom * baris;

    document.getElementById('ruang_kapasitas').value = total;
    
    var tipeLabel = (tipe === 1) ? '1 Siswa Mandiri' : '2 Siswa Meja Gandeng';
    document.getElementById('liveCalcText').innerHTML = tipe + ' &times; ' + kolom + ' Kolom &times; ' + baris + ' Baris = <strong>' + total + ' Kursi Peserta</strong> (' + tipeLabel + ')';
}

function bukaModalRuang(mode) {
    if (mode === 'add') {
        document.getElementById('modalRuangTitle').innerHTML = '<i data-lucide="plus-circle" style="color: #4f46e5;"></i> Tambah Ruang & Formasi Meja';
        document.getElementById('ruang_id').value = '';
        document.getElementById('ruang_nama').value = '';
        document.getElementById('tipe_1').checked = true;
        document.getElementById('ruang_kolom').value = '5';
        document.getElementById('ruang_baris').value = '6';
        document.getElementById('ruang_pola').value = 'kolom';
        document.getElementById('ruang_lokasi').value = 'Gedung A';
        document.getElementById('ruang_keterangan').value = 'Formasi 1 x 5 x 6 (1 Meja Mandiri, 5 Kolom, 6 Baris)';
        hitungKapasitas();
    }
    document.getElementById('modalRuang').style.display = 'flex';
    if (window.lucide) { lucide.createIcons(); }
}

function editRuang(r) {
    document.getElementById('modalRuangTitle').innerHTML = '<i data-lucide="edit-3" style="color: #4f46e5;"></i> Edit Ruang & Formasi Meja';
    document.getElementById('ruang_id').value = r.id;
    document.getElementById('ruang_nama').value = r.nama_ruang;
    
    var t = parseInt(r.tipe_meja || 1);
    if (t === 2) {
        document.getElementById('tipe_2').checked = true;
    } else {
        document.getElementById('tipe_1').checked = true;
    }

    document.getElementById('ruang_kolom').value = r.jumlah_kolom || 5;
    document.getElementById('ruang_baris').value = r.jumlah_baris || 6;
    document.getElementById('ruang_kapasitas').value = r.kapasitas || 30;
    document.getElementById('ruang_pola').value = r.pola_penomoran || 'kolom';
    document.getElementById('ruang_lokasi').value = r.lokasi || '';
    document.getElementById('ruang_keterangan').value = r.keterangan || '';

    hitungKapasitas();
    document.getElementById('modalRuang').style.display = 'flex';
    if (window.lucide) { lucide.createIcons(); }
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
