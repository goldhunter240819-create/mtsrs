<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="layers"></i> Tagihan Siswa</h1>
        <p class="mph-subtitle">Rekap tagihan per siswa berdasarkan Jenis Tagihan Wajib</p>
    </div>
</div>
<div class="z-content-pad">



<?php
// Hitung statistik
$totalSiswa = count($grouped);
$totalMengangsur = 0; $totalBelum = 0;
foreach ($grouped as $s) {
    foreach ($s['tagihan'] as $t) {
        if ($t['status'] === 'Mengangsur') $totalMengangsur++;
        elseif ($t['status'] === 'Belum Bayar') $totalBelum++;
    }
}
$totalTagihan = $totalMengangsur + $totalBelum;
?>
<div class="z-stats" style="grid-template-columns:repeat(3,1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Siswa Bertagihan</div><div class="z-stat-value"><?php echo $totalSiswa; ?></div></div><div class="z-stat-icon zi-blue"><i data-lucide="users"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Sedang Mengangsur</div><div class="z-stat-value" style="color:#2563eb;"><?php echo $totalMengangsur; ?></div></div><div class="z-stat-icon zi-blue"><i data-lucide="loader"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Belum Bayar</div><div class="z-stat-value" style="color:var(--z-danger);"><?php echo $totalBelum; ?></div></div><div class="z-stat-icon zi-red"><i data-lucide="alert-circle"></i></div></div>
</div>

<div class="z-panel">
<div style="padding: 14px 16px; border-bottom: 1px solid var(--z-border);">
    <div class="z-panel-title" style="margin:0 0 12px 0;"><i data-lucide="file-text"></i>Daftar Tagihan Per Siswa</div>
    <form id="filterForm" action="" method="GET">
        <!-- Baris 1: Semua kontrol dalam satu baris -->
        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:12px;">
            <!-- Tampilkan -->
            <div style="display:flex; align-items:center; gap:5px; font-size:0.82rem; color:#64748b; font-weight:600;white-space:nowrap;">
                Tampilkan
                <select name="per_page" class="z-field" style="width:68px; padding:4px 8px; margin:0; font-weight:700;" onchange="performAjaxFilter()">
                    <option value="10" <?php echo (($_GET['per_page'] ?? 10) == 10) ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?php echo (($_GET['per_page'] ?? 10) == 25) ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo (($_GET['per_page'] ?? 10) == 50) ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?php echo (($_GET['per_page'] ?? 10) == 100) ? 'selected' : ''; ?>>100</option>
                    <option value="999" <?php echo (($_GET['per_page'] ?? 10) == 999) ? 'selected' : ''; ?>>Semua</option>
                </select>
            </div>
            <!-- Filter Kelas -->
            <select name="kelas" class="z-field" style="min-width:130px; padding:4px 8px; margin:0; flex:1;" onchange="performAjaxFilter()">
                <option value="">- Semua Kelas -</option>
                <?php foreach($kelasList as $k): ?>
                <option value="<?php echo $k['nama_kelas']; ?>" <?php echo $filter_kelas==$k['nama_kelas']?'selected':''; ?>><?php echo $k['nama_kelas']; ?></option>
                <?php endforeach; ?>
            </select>
            <!-- Filter Status -->
            <select name="status" class="z-field" style="min-width:125px; padding:4px 8px; margin:0; flex:1;" onchange="performAjaxFilter()">
                <option value="">- Semua Status -</option>
                <option value="Mengangsur" <?php echo $filter_status=='Mengangsur'?'selected':''; ?>>Mengangsur</option>
                <option value="Belum Bayar" <?php echo $filter_status=='Belum Bayar'?'selected':''; ?>>Belum Bayar</option>
            </select>
            <input type="hidden" name="page" value="1">
            <?php if (!empty($_GET['cari'])): ?>
            <input type="hidden" name="cari" value="<?php echo htmlspecialchars($_GET['cari']); ?>">
            <?php endif; ?>
        </div>
        <!-- Baris 2: Search Bar -->
        <div style="position:relative;">
            <i data-lucide="search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;pointer-events:none;"></i>
            <input
                type="text"
                id="searchSiswa"
                name="cari"
                placeholder="Cari nama siswa atau NIS... (cari di semua halaman)"
                value="<?php echo htmlspecialchars($_GET['cari'] ?? ''); ?>"
                class="z-field"
                style="width:100%;padding:7px 32px 7px 32px;margin:0;font-size:0.85rem;box-sizing:border-box;"
                oninput="scheduleSearch()"
                onkeydown="if(event.key==='Enter'){event.preventDefault();performAjaxFilter();}"
                autocomplete="off"
            >
            <span id="searchClearBtn" onclick="clearSearch()" style="display:<?php echo !empty($_GET['cari']) ? 'block' : 'none'; ?>;position:absolute;right:10px;top:50%;transform:translateY(-50%);cursor:pointer;color:#94a3b8;font-size:1.2rem;line-height:1;" title="Hapus">&times;</span>
        </div>
    </form>
</div>
<?php
$filter_cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';
if ($filter_cari !== '') {
    $grouped = array_values(array_filter($grouped, function($s) use ($filter_cari) {
        return stripos($s['nama'], $filter_cari) !== false || stripos($s['nis'], $filter_cari) !== false;
    }));
}
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
if ($per_page <= 0) $per_page = 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$total_grouped = count($grouped);
$total_pages = ($per_page >= 999) ? 1 : max(1, ceil($total_grouped / $per_page));
if ($current_page > $total_pages) $current_page = $total_pages;
$offset = ($per_page >= 999) ? 0 : ($current_page - 1) * $per_page;
$grouped_page = ($per_page >= 999) ? $grouped : array_slice($grouped, $offset, $per_page);
?>
    <div id="tagihan-list-container" style="padding: 15px; transition: opacity 0.2s;">
        <?php if(empty($grouped)): ?>
            <p style="text-align:center; color:var(--z-muted); padding:30px;">Tidak ada data tagihan.</p>
        <?php else: ?>
        <?php foreach($grouped_page as $idx => $s):
            $realIdx = $offset + $idx;
            $sLunas = 0; $sMengangsur = 0; $sBelum = 0; $sTotalSisa = 0;
            foreach($s['tagihan'] as $t) {
                if ($t['status'] === 'Lunas') $sLunas++;
                elseif ($t['status'] === 'Mengangsur') { $sMengangsur++; $sTotalSisa += $t['sisa']; }
                else { $sBelum++; $sTotalSisa += $t['sisa']; }
            }
        ?>
        <div class="student-card">
            <!-- Header Siswa -->
            <div class="student-head" onclick="toggleCard(<?php echo $realIdx; ?>)">
                <div class="z-uc-av" style="width:38px;height:38px;border-radius:50%;background:var(--z-primary);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;flex-shrink:0;">
                    <?php echo strtoupper(substr($s['nama'],0,2)); ?>
                </div>
                <div>
                    <div style="font-weight:700; font-size:.9rem;"><?php echo $s['nama']; ?></div>
                    <div style="font-size:.78rem; color:var(--z-muted);">NIS: <?php echo $s['nis']; ?> &bull; <?php echo $s['kelas']; ?></div>
                </div>
                <div class="s-summary">
                    <?php if($sLunas > 0): ?><span class="s-badge s-badge-green"><?php echo $sLunas; ?> Lunas</span><?php endif; ?>
                    <?php if($sMengangsur > 0): ?><span class="s-badge s-badge-blue"><?php echo $sMengangsur; ?> Mengangsur</span><?php endif; ?>
                    <?php if($sBelum > 0): ?><span class="s-badge s-badge-red"><?php echo $sBelum; ?> Belum Bayar</span><?php endif; ?>
                    <?php if($sTotalSisa > 0): ?>
                    <span style="font-size:.82rem; color:var(--z-danger); font-weight:700;">Sisa: Rp <?php echo number_format($sTotalSisa,0,',','.'); ?></span>
                    <?php endif; ?>
                    <a href="/keuangan/tagihan/cetak-siswa/<?php echo $s['siswa_id']; ?>" target="_blank" onclick="event.stopPropagation()" class="btn btn-outline btn-sm" style="margin-left:8px;">
                        <i data-lucide="printer" style="width:13px;height:13px;"></i> Cetak
                    </a>
                    <i data-lucide="chevron-down" id="chevron-<?php echo $realIdx; ?>" style="width:16px;height:16px;color:var(--z-muted);transition:transform .2s;"></i>
                </div>
            </div>

            <!-- Daftar Tagihan Siswa -->
            <div class="student-bills" id="bills-<?php echo $realIdx; ?>">
                <div class="bill-header" style="grid-template-columns: 2fr 1fr 1fr 1fr 100px 50px;">
                    <div>Jenis Tagihan</div>
                    <div>Nominal</div>
                    <div>Sudah Dibayar</div>
                    <div>Sisa</div>
                    <div>Status</div>
                    <div>Aksi</div>
                </div>
                <?php foreach($s['tagihan'] as $t): ?>
                <div class="bill-row" style="grid-template-columns: 2fr 1fr 1fr 1fr 100px 50px; align-items:center;">
                    <div style="font-weight:600;"><?php echo $t['jenis']; ?>
                        <div style="font-size:.75rem; color:var(--z-muted);">JT: <?php echo date('d/m/Y', strtotime($t['jatuh_tempo'])); ?></div>
                    </div>
                    <div style="color:var(--z-muted);">Rp <?php echo number_format($t['jumlah'],0,',','.'); ?></div>
                    <div style="color:#16a34a;"><?php echo $t['terbayar'] > 0 ? 'Rp '.number_format($t['terbayar'],0,',','.') : '-'; ?></div>
                    <div>
                        <?php if($t['status'] === 'Lunas'): ?>
                            <span style="color:#16a34a; font-weight:700; font-size:.85rem;">LUNAS</span>
                        <?php else: ?>
                            <strong style="color:var(--z-danger);">Rp <?php echo number_format($t['sisa'],0,',','.'); ?></strong>
                        <?php endif; ?>
                    </div>
                    <div><span class="pill <?php echo $t['status']==='Lunas'?'pill-green':($t['status']==='Mengangsur'?'pill-blue':'pill-amber'); ?>"><?php echo $t['status']; ?></span></div>
                    <div style="display:flex; gap:5px;">
                        <?php if($t['status'] !== 'Lunas'): ?>
                        <button class="btn btn-primary btn-sm" onclick="openBayarModal(<?php echo $t['id']; ?>, '<?php echo htmlspecialchars($t['jenis'], ENT_QUOTES); ?>', <?php echo $t['sisa']; ?>)" style="padding:4px 8px;" title="Bayar Tagihan"><i data-lucide="dollar-sign" style="width:14px;height:14px;"></i></button>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])): ?>
                        <button class="btn btn-outline btn-sm" onclick="openEditModal(<?php echo $t['id']; ?>, '<?php echo htmlspecialchars($t['jenis'], ENT_QUOTES); ?>', <?php echo $t['jumlah']; ?>, '<?php echo $t['jatuh_tempo']; ?>', <?php echo $t['terbayar'] ? $t['terbayar'] : 0; ?>)" style="padding:4px 8px;" title="Edit Tagihan"><i data-lucide="edit-3" style="width:14px;height:14px;"></i></button>
                        <button class="btn btn-outline btn-sm" onclick="deleteTagihan(<?php echo $t['id']; ?>)" style="padding:4px 8px; color: var(--z-danger); border-color: #fca5a5; background: #fef2f2;" title="Hapus Tagihan"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- PAGINATION BAR -->
        <?php if($total_pages > 1 || $per_page < 999): ?>
        <div style="display:flex; align-items:center; justify-content:space-between; margin-top:16px; padding-top:12px; border-top:1px solid #f1f5f9; flex-wrap:wrap; gap:10px;">
            <div style="font-size:0.82rem; color:#64748b; font-weight:600;">
                Menampilkan <?php echo min($offset+1, $total_grouped); ?>–<?php echo min($offset+$per_page, $total_grouped); ?> dari <b><?php echo $total_grouped; ?></b> siswa
            </div>
            <div style="display:flex; gap:6px; align-items:center;">
                <?php
                $base_params = http_build_query(array_filter([
                    'kelas'    => $filter_kelas,
                    'status'   => $filter_status,
                    'per_page' => ($per_page < 999) ? $per_page : 999,
                ]));
                ?>
                <?php if($current_page > 1): ?>
                <a href="?<?php echo $base_params; ?>&page=1" class="btn btn-outline btn-sm"><i data-lucide="chevrons-left" style="width:13px;height:13px;"></i></a>
                <a href="?<?php echo $base_params; ?>&page=<?php echo $current_page - 1; ?>" class="btn btn-outline btn-sm"><i data-lucide="chevron-left" style="width:13px;height:13px;"></i></a>
                <?php endif; ?>

                <?php
                $window = 2;
                $start = max(1, $current_page - $window);
                $end   = min($total_pages, $current_page + $window);
                if ($start > 1) echo '<span style="color:#94a3b8;padding:0 4px;">...</span>';
                for ($p = $start; $p <= $end; $p++):
                ?>
                <a href="?<?php echo $base_params; ?>&page=<?php echo $p; ?>" class="btn btn-sm <?php echo $p == $current_page ? 'btn-primary' : 'btn-outline'; ?>"><?php echo $p; ?></a>
                <?php endfor;
                if ($end < $total_pages) echo '<span style="color:#94a3b8;padding:0 4px;">...</span>';
                ?>

                <?php if($current_page < $total_pages): ?>
                <a href="?<?php echo $base_params; ?>&page=<?php echo $current_page + 1; ?>" class="btn btn-outline btn-sm"><i data-lucide="chevron-right" style="width:13px;height:13px;"></i></a>
                <a href="?<?php echo $base_params; ?>&page=<?php echo $total_pages; ?>" class="btn btn-outline btn-sm"><i data-lucide="chevrons-right" style="width:13px;height:13px;"></i></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<!-- Modal Edit Tagihan -->
<div id="modal-edit" class="z-modal-ov" style="z-index:9999;">
    <div class="z-modal" style="width:100%;max-width:400px;margin:auto;">
        <div class="z-modal-head">
            <h2 style="margin:0;font-size:1.2rem;display:flex;align-items:center;gap:10px;"><i data-lucide="edit-3" style="color:#10b981;"></i> Edit Tagihan</h2>
            <button type="button" class="z-modal-close" onclick="document.getElementById('modal-edit').style.display='none'"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <form id="form-edit" action="/admin/finance/tagihan/update" method="POST" onsubmit="handleAjaxSubmit(event)">
            <input type="hidden" name="tagihan_id" id="edit_tagihan_id">
            <div class="z-form-group">
                <label>Nama/Jenis Tagihan</label>
                <input type="text" name="nama_tagihan" id="edit_nama_tagihan" class="z-input" required>
            </div>
            <div class="z-form-group">
                <label>Nominal Tagihan (Rp)</label>
                <input type="number" name="jumlah_tagihan" id="edit_jumlah_tagihan" class="z-input" required>
            </div>
            <div style="background: #fffbeb; border: 1px solid #fde68a; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                <label style="color: #b45309; font-weight: 600; display: block; margin-bottom: 8px;"><i data-lucide="alert-circle" style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:4px;"></i> Koreksi Nominal Sudah Dibayar (Rp)</label>
                <input type="number" name="nominal_pembayaran" id="edit_nominal_pembayaran" class="z-input" value="0" style="border-color: #fcd34d; background: #fff;">
                <small style="color: #92400e; font-size: 11.5px; margin-top: 6px; display: block; line-height: 1.4;">
                    <strong>Info:</strong> Mengubah nilai ini akan <u>menggantikan (reset)</u> histori kwitansi tagihan ini menjadi 1 transaksi baru dengan nominal yang Anda inputkan.
                </small>
            </div>
            <div class="z-form-group">
                <label>Jatuh Tempo (Opsional)</label>
                <input type="date" name="jatuh_tempo" id="edit_jatuh_tempo" class="z-input">
            </div>
            </form>
        </div>
        <div class="z-modal-foot">
            <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-edit').style.display='none'">Batal</button>
            <button type="submit" form="form-edit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </div>
</div>

<!-- Modal Bayar Tagihan -->
<div id="modal-bayar" class="z-modal-ov" style="z-index:9999;">
    <div class="z-modal" style="width:100%;max-width:400px;margin:auto;">
        <div class="z-modal-head">
            <h2 style="margin:0;font-size:1.2rem;display:flex;align-items:center;gap:10px;"><i data-lucide="dollar-sign" style="color:#10b981;"></i> Bayar Tagihan</h2>
            <button type="button" class="z-modal-close" onclick="document.getElementById('modal-bayar').style.display='none'"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <form id="form-bayar" action="/admin/finance/tagihan/bayar" method="POST" onsubmit="handleAjaxSubmit(event)">
            <input type="hidden" name="tagihan_id" id="bayar_tagihan_id">
            <div class="z-form-group">
                <label>Nama/Jenis Tagihan</label>
                <input type="text" id="bayar_nama_tagihan" class="z-input" readonly style="background:#f1f5f9;">
            </div>
            <div class="z-form-group">
                <label>Nominal Pembayaran (Rp)</label>
                <input type="number" name="jumlah_bayar" id="bayar_jumlah_bayar" class="z-input" required>
                <div style="font-size:0.8rem; color:var(--z-muted); margin-top:5px;">Sisa Tagihan: <strong id="bayar_sisa_text" style="color:var(--z-danger);"></strong></div>
            </div>
            </form>
        </div>
        <div class="z-modal-foot">
            <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-bayar').style.display='none'">Batal</button>
            <button type="submit" form="form-bayar" class="btn btn-primary">Bayar Sekarang</button>
        </div>
    </div>
</div>

<style>
.student-card { border: 1px solid var(--z-border); border-radius: 8px; margin-bottom: 15px; overflow: hidden; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.student-head { display: flex; align-items: center; gap: 15px; padding: 15px; cursor: pointer; background: #f8fafc; border-bottom: 1px solid var(--z-border); transition: background 0.2s; }
.student-head:hover { background: #f1f5f9; }
.s-summary { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-left: auto; }
.s-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; white-space: nowrap; }
.s-badge-green { background: #dcfce7; color: #166534; }
.s-badge-blue { background: #dbeafe; color: #1e40af; }
.s-badge-red { background: #fee2e2; color: #991b1b; }
.student-bills { display: none; padding: 15px; background: #fff; }
.bill-header { display: grid; padding: 10px; background: #f8fafc; border-bottom: 2px solid var(--z-border); font-weight: 600; font-size: 0.85rem; color: #475569; gap: 10px; }
.bill-row { display: grid; padding: 12px 10px; border-bottom: 1px solid var(--z-border); gap: 10px; font-size: 0.9rem; align-items: center; }
.bill-row:last-child { border-bottom: none; }
</style>

<script>
function toggleCard(idx) {
    var b = document.getElementById('bills-' + idx);
    var c = document.getElementById('chevron-' + idx);
    if(b.style.display === 'block') {
        b.style.display = 'none';
        c.style.transform = 'rotate(0deg)';
    } else {
        b.style.display = 'block';
        c.style.transform = 'rotate(180deg)';
    }
}
function performAjaxFilter() {
    document.getElementById('filterForm').submit();
}
var searchTimer;
function scheduleSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(performAjaxFilter, 800);
}
function clearSearch() {
    document.getElementById('searchSiswa').value = '';
    performAjaxFilter();
}
function openEditModal(id, nama, jumlah, tgl, terbayar) {
    document.getElementById('edit_tagihan_id').value = id;
    document.getElementById('edit_nama_tagihan').value = nama;
    document.getElementById('edit_jumlah_tagihan').value = jumlah;
    document.getElementById('edit_jatuh_tempo').value = tgl;
    document.getElementById('edit_nominal_pembayaran').value = terbayar;
    document.getElementById('modal-edit').style.display = 'flex';
}
function openBayarModal(id, nama, sisa) {
    document.getElementById('bayar_tagihan_id').value = id;
    document.getElementById('bayar_nama_tagihan').value = nama;
    document.getElementById('bayar_sisa_text').innerText = 'Rp ' + Number(sisa).toLocaleString('id-ID');
    document.getElementById('bayar_jumlah_bayar').value = sisa;
    document.getElementById('bayar_jumlah_bayar').max = sisa;
    document.getElementById('modal-bayar').style.display = 'flex';
}
function handleAjaxSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    
    // Sembunyikan modal agar sweet alert tidak tertutup
    document.getElementById('modal-edit').style.display = 'none';
    document.getElementById('modal-bayar').style.display = 'none';
    
    // Show loading
    Swal.fire({
        title: 'Menyimpan...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(form.action, {
        method: form.method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        // Assume success if no throw
        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data berhasil disimpan!', timer: 1500, showConfirmButton: false })
        .then(() => {
            window.location.reload();
        });
    })
    .catch(error => {
        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat menyimpan.' });
    });
}

function deleteTagihan(id) {
    Swal.fire({
        title: 'Hapus Tagihan?',
        text: 'Apakah Anda yakin? Semua riwayat pembayaran (saldo) yang terkait dengan tagihan ini akan ikut terhapus secara permanen dari sistem.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus & Tarik Saldo',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            const formData = new FormData();
            formData.append('tagihan_id', id);
            fetch('/keuangan/tagihan/delete', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Tagihan dan saldo terkait berhasil dihapus.', timer: 1500, showConfirmButton: false })
                    .then(() => window.location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan pada server.' });
                }
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi terputus.' });
            });
        }
    });
}
</script>

</div>
</div>