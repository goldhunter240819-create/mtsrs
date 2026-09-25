<?php
$dbCore = \App\Core\Database::connect('core');
$inst = $dbCore->query("SELECT disable_web_trx FROM institusi LIMIT 1")->fetch();
$disableWebTrx = !empty($inst['disable_web_trx']);
?>
<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="layers"></i> Pembayaran Siswa</h1>
        <p class="mph-subtitle">Pencatatan uang SPP / tagihan dari siswa</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        <?php if(!$disableWebTrx): ?>
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById('mOv').classList.add('open')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Pembayaran</button>
        <?php else: ?>
        <div style="background:#fee2e2; color:#dc2626; padding:8px 15px; border-radius:8px; font-size:0.85rem; font-weight:600;"><i data-lucide="shield-alert" style="width:14px;height:14px;margin-right:5px;vertical-align:-2px;"></i>Pencatatan Web Dinonaktifkan</div>
        <?php endif; ?>
    </div>
</div>
<div class="z-content-pad">

<?php
$total_dana = 0;
foreach($pembayaran as $p) {
    $total_dana += $p['jumlah'];
}
?>
<div class="z-stats" style="grid-template-columns: repeat(2, 1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Siswa Telah Bayar</div><div class="z-stat-value"><?php echo count($pembayaran); ?></div></div><div class="z-stat-icon zi-blue"><i data-lucide="users"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Dana Terkumpul</div><div class="z-stat-value" style="color:#16a34a;">Rp <?php echo number_format($total_dana,0,',','.'); ?></div></div><div class="z-stat-icon zi-green"><i data-lucide="wallet"></i></div></div>
</div>
<div class="z-panel">
    <div class="z-panel-head" style="justify-content: space-between;">
        <div class="z-panel-title"><i data-lucide="list"></i>Daftar Pembayaran Siswa</div>
        <form action="" method="GET" style="display:flex; align-items:center; gap:10px;">
            <label class="z-label" style="margin:0; font-size:0.85rem;"><i data-lucide="filter" style="width:14px; height:14px;"></i> Tampilkan:</label>
            <select id="entryLimit" class="z-field" style="min-width:80px; padding:4px 8px; margin:0;" onchange="changePage(1)">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <label class="z-label" style="margin:0; font-size:0.85rem;">Kelas:</label>
            <select name="kelas_id" class="z-field" style="min-width:150px; padding:4px 8px; margin:0;" onchange="this.form.submit()">
                <option value="0">- Semua Kelas -</option>
                <?php foreach($kelasList as $k): ?>
                <option value="<?php echo $k['id']; ?>" <?php echo $kelas_id==$k['id']?'selected':''; ?>><?php echo $k['nama_kelas']; ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div class="z-table-wrap"><table id="tbl">
        <thead><tr><th>Siswa</th><th>Kelas</th><th>Tanggal Bayar</th><th>Periode</th><th>Status</th><th>Jumlah</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach($pembayaran as $p): ?>
            <tr>
                <td><div class="z-uc"><div class="z-uc-av"><?php echo strtoupper(substr($p['nama'],0,2)); ?></div><div><div class="z-uc-name"><?php echo $p['nama']; ?></div><div class="z-uc-sub">NIS: <?php echo $p['nis']; ?></div></div></div></td>
                <td style="font-size:.82rem;"><?php echo $p['kelas']; ?></td>
                <td style="font-size:.82rem; color:var(--z-muted);"><?php echo date('d/m/Y',strtotime($p['tanggal'])); ?></td>
                <td style="font-size:.82rem;"><span style="font-weight:600;"><?php echo $p['jenis']; ?></span> <br><span style="color:var(--z-muted);"><?php echo $p['periode']; ?></span></td>
                <td><span class="pill <?php echo $p['status']==='Lunas' ? 'pill-green' : ($p['status']==='Mengangsur' ? 'pill-blue' : 'pill-amber'); ?>"><?php echo $p['status']; ?></span></td>
                <td class="z-amount">Rp <?php echo number_format($p['jumlah'],0,',','.'); ?></td>
                <td>
                    <a href="/keuangan/kwitansi/<?php echo $p['id']; ?>" target="_blank" class="btn btn-ghost btn-sm">Cetak Kwitansi</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($pembayaran)): ?>
            <tr><td colspan="7" style="text-align:center;color:var(--z-muted);padding:20px;">
                <?php echo $kelas_id==0 ? 'Silakan pilih kelas terlebih dahulu untuk melihat daftar siswa.' : 'Belum ada data siswa di kelas ini.'; ?>
            </td></tr>
            <?php endif; ?>
        </tbody>
    </table></div>
    <div class="z-panel-foot" style="display:flex; justify-content:space-between; align-items:center; padding: 10px 20px;">
        <div id="pageInfo" style="font-size: 0.85rem; color: var(--z-muted);">Menampilkan 1-10 dari <?php echo count($pembayaran); ?> data</div>
        <div style="display:flex; gap:5px;">
            <button class="btn btn-ghost btn-sm" onclick="changePage(currentPage-1)"><i data-lucide="chevron-left" style="width:14px;"></i></button>
            <div id="pageList" style="display:flex; gap:5px;"></div>
            <button class="btn btn-ghost btn-sm" onclick="changePage(currentPage+1)"><i data-lucide="chevron-right" style="width:14px;"></i></button>
        </div>
    </div>
</div>
</div>
</div>

<div id="mOv" class="z-modal-ov">
    <div class="z-modal">
        <div class="z-modal-head">
            <h3 class="z-modal-title"><i data-lucide="plus"></i> Tambah Pembayaran Manual</h3>
            <div class="z-modal-close" onclick="document.getElementById('mOv').classList.remove('open')"><i data-lucide="x"></i></div>
        </div>
        <div class="z-modal-body">
            <form action="/keuangan/komite/pembayaran/save" method="POST" id="formModal">
                <input type="hidden" name="kelas_id" value="<?php echo $kelas_id; ?>">
                
                <div style="margin-bottom:15px;">
                    <label class="z-label">Filter Kelas <span style="font-size:0.8rem;color:#64748b;font-weight:normal;">(Opsional)</span></label>
                    <select id="modalKelasFilter" class="z-field" onchange="filterSiswaModal()">
                        <option value="">- Semua Kelas -</option>
                        <?php foreach($kelasList as $k): ?>
                        <option value="<?php echo $k['id']; ?>" <?php echo $kelas_id==$k['id']?'selected':''; ?>><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom:15px;">
                    <label class="z-label">Siswa <span style="color:red">*</span></label>
                    <select name="siswa_id" id="modalSiswaSelect" class="z-field" required onchange="checkMaxBayar()">
                        <option value="">- Pilih Siswa -</option>
                        <?php foreach($allSiswaList as $s): ?>
                        <option value="<?php echo $s['id']; ?>" data-kelas="<?php echo $s['kelas_id']; ?>"><?php echo htmlspecialchars($s['nama']); ?> (NIS: <?php echo htmlspecialchars($s['nis']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom:15px;">
                    <label class="z-label">Jenis Pembayaran <span style="color:red">*</span></label>
                    <select name="jenis_pembayaran" class="z-field" required onchange="updatePeriodeFields(this)">
                        <option value="">- Pilih Jenis Tagihan -</option>
                        <?php foreach($jenisTagihan as $j): ?>
                        <option value="<?php echo htmlspecialchars($j['nama_tagihan']); ?>" data-periode="<?php echo htmlspecialchars($j['jenis_periode']); ?>"><?php echo htmlspecialchars($j['nama_tagihan']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <input type="hidden" name="jenis_periode_value" id="jenis_periode_value" value="">

                <div id="divTahunan" style="display:none; margin-bottom:15px;">
                    <label class="z-label">Periode (Tahun) <span style="color:red">*</span></label>
                    <select name="periode_tahunan" class="z-field" id="selTahunan">
                        <option value="">- Pilih Tahun -</option>
                        <?php 
                        $y = date('Y');
                        for($i = $y-2; $i <= $y+3; $i++) {
                            $nextY = $i + 1;
                            echo "<option value=\"$i/$nextY\">$i/$nextY</option>";
                        }
                        ?>
                    </select>
                </div>

                <div id="divBulanan" style="display:none; margin-bottom:15px;">
                    <label class="z-label">Periode (Bulan) <span style="color:red">*</span></label>
                    <input type="month" name="periode_bulanan" class="z-field" id="selBulanan">
                </div>

                <div style="margin-bottom:15px;">
                    <label class="z-label">Tanggal Bayar <span style="color:red">*</span></label>
                    <input type="date" name="tanggal_bayar" class="z-field" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div style="margin-bottom:20px;">
                    <label class="z-label">Jumlah (Rp) <span style="color:red">*</span></label>
                    <input type="text" id="jumlah_formatted" class="z-field" required placeholder="0" oninput="formatRupiah(this)">
                    <input type="hidden" name="jumlah" id="jumlah_raw">
                    <div id="jumlah_helper" style="font-size:0.8rem; color:#64748b; margin-top:5px;"></div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('mOv').classList.remove('open')">Batal</button>
                    <button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:14px;"></i> Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var currentPage = 1;
function changePage(page) {
    var limit = parseInt(document.getElementById('entryLimit').value);
    var rows = document.querySelectorAll('#tbl tbody tr');
    var totalRows = rows.length;
    // Don't count empty message row if exists
    if(totalRows === 1 && rows[0].innerText.includes('Silakan pilih kelas')) {
        return;
    }
    
    var totalPages = Math.ceil(totalRows / limit);
    if(page < 1) page = 1;
    if(page > totalPages && totalPages > 0) page = totalPages;
    currentPage = page;
    
    var start = (currentPage - 1) * limit;
    var end = start + limit;
    
    for(var i=0; i<totalRows; i++) {
        rows[i].style.display = (i >= start && i < end) ? '' : 'none';
    }
    
    var dispEnd = end > totalRows ? totalRows : end;
    var dispStart = totalRows === 0 ? 0 : start + 1;
    
    var info = document.getElementById('pageInfo');
    if(info) info.innerText = 'Menampilkan ' + dispStart + '-' + dispEnd + ' dari ' + totalRows + ' data';
    
    renderPagination(totalPages);
}

function renderPagination(totalPages) {
    var pl = document.getElementById('pageList');
    if(!pl) return;
    pl.innerHTML = '';
    
    var maxButtons = 5;
    var startBtn = Math.max(1, currentPage - Math.floor(maxButtons/2));
    var endBtn = Math.min(totalPages, startBtn + maxButtons - 1);
    
    if(endBtn - startBtn + 1 < maxButtons) {
        startBtn = Math.max(1, endBtn - maxButtons + 1);
    }
    
    for(var i=startBtn; i<=endBtn; i++) {
        var btn = document.createElement('button');
        btn.className = 'btn btn-sm ' + (i === currentPage ? 'btn-primary' : 'btn-outline');
        btn.innerText = i;
        btn.onclick = (function(p){ return function(){ changePage(p); } })(i);
        pl.appendChild(btn);
    }
}

function resetModal() {
    document.getElementById('formModal').reset();
    document.getElementById('divTahunan').style.display = 'none';
    document.getElementById('divBulanan').style.display = 'none';
    
    // Set filter kelas ke default sesuai halaman
    var defaultKelas = '<?php echo $kelas_id == 0 ? "" : $kelas_id; ?>';
    document.getElementById('modalKelasFilter').value = defaultKelas;
    filterSiswaModal();
    
    lucide.createIcons();
}

function filterSiswaModal() {
    var kId = document.getElementById('modalKelasFilter').value;
    var sel = document.getElementById('modalSiswaSelect');
    var opts = sel.options;
    sel.value = ""; // Reset
    for (var i = 1; i < opts.length; i++) {
        var optK = opts[i].getAttribute('data-kelas');
        if (kId === "" || optK === kId) {
            opts[i].style.display = '';
        } else {
            opts[i].style.display = 'none';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    changePage(1);
    filterSiswaModal(); // initial filter
});

function updatePeriodeFields(sel) {
    var option = sel.options[sel.selectedIndex];
    var tipe = option.getAttribute('data-periode');
    document.getElementById('jenis_periode_value').value = tipe;
    
    document.getElementById('divTahunan').style.display = 'none';
    document.getElementById('divBulanan').style.display = 'none';
    document.getElementById('selTahunan').required = false;
    document.getElementById('selBulanan').required = false;

    if (tipe === 'tahunan') {
        document.getElementById('divTahunan').style.display = 'block';
        document.getElementById('selTahunan').required = true;
    } else if (tipe === 'bulanan') {
        document.getElementById('divBulanan').style.display = 'block';
        document.getElementById('selBulanan').required = true;
    }
    
    checkMaxBayar();
}

var tagihanData = <?php echo json_encode($tagihanData); ?>;
var maxBayarCurrent = 0;

function checkMaxBayar() {
    var siswaId = document.getElementById('modalSiswaSelect').value;
    var jenis = document.querySelector('select[name="jenis_pembayaran"]').value;
    
    var helper = document.getElementById('jumlah_helper');
    var rawInput = document.getElementById('jumlah_raw');
    var fmtInput = document.getElementById('jumlah_formatted');
    
    maxBayarCurrent = 0;
    helper.innerHTML = '';
    
    if (siswaId && jenis) {
        var found = tagihanData.find(function(t) {
            return t.siswa_id == siswaId && t.nama_tagihan == jenis;
        });
        if (found) {
            var tagihan = parseFloat(found.jumlah_tagihan);
            var terbayar = parseFloat(found.terbayar || 0);
            maxBayarCurrent = tagihan - terbayar;
            
            if (maxBayarCurrent > 0) {
                helper.innerHTML = 'Sisa tagihan: <strong style="color:#16a34a;">Rp ' + formatRibuan(maxBayarCurrent) + '</strong>';
            } else {
                helper.innerHTML = '<strong style="color:#dc2626;">Siswa ini sudah lunas untuk tagihan ini.</strong>';
            }
            
            // Re-validate input
            formatRupiah(fmtInput);
        }
    }
}

function formatRibuan(angka) {
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function formatRupiah(el) {
    var rawValue = el.value.replace(/[^0-9]/g, '');
    var val = parseInt(rawValue, 10);
    
    if (isNaN(val)) val = 0;
    
    if (maxBayarCurrent > 0 && val > maxBayarCurrent) {
        val = maxBayarCurrent;
    }
    
    if (val === 0 && rawValue === '') {
        el.value = '';
        document.getElementById('jumlah_raw').value = '';
    } else {
        el.value = formatRibuan(val);
        document.getElementById('jumlah_raw').value = val;
    }
}

// Hook form submit to ensure raw value is > 0
document.getElementById('formModal').addEventListener('submit', function(e) {
    var raw = parseInt(document.getElementById('jumlah_raw').value, 10);
    if (isNaN(raw) || raw <= 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Jumlah pembayaran tidak valid!'
        });
    }
});
</script>