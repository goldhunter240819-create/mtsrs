<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="layers"></i> Rekap Tagihan Siswa</h1>
        <p class="mph-subtitle">Monitoring status pembayaran siswa per jenis tagihan dan kelas</p>
    </div>
</div>
<div class="z-content-pad">


<?php
$total_tagihan = 0;
$total_terbayar = 0;
$total_sisa = 0;
$lunas_count = 0;
$belum_count = 0;

foreach($rekap as $r){ 
    $total_tagihan += $r['jumlah_tagihan'];
    $total_terbayar += $r['terbayar'];
    $total_sisa += $r['sisa'];
    if($r['status'] === 'Lunas') $lunas_count++;
    if($r['status'] === 'Belum Lunas') $belum_count++;
}
?>
<?php if(!empty($rekap)): ?>
<div class="z-stats" style="grid-template-columns:repeat(3,1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Lunas</div><div class="z-stat-value" style="color:#16a34a;"><?php echo $lunas_count; ?> Siswa</div></div><div class="z-stat-icon zi-green"><i data-lucide="user-check"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Belum Lunas</div><div class="z-stat-value" style="color:#dc2626;"><?php echo $belum_count; ?> Siswa</div></div><div class="z-stat-icon zi-rose"><i data-lucide="user-x"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Sisa Tagihan</div><div class="z-stat-value" style="color:#d97706;">Rp <?php echo number_format($total_sisa,0,',','.'); ?></div></div><div class="z-stat-icon zi-amber"><i data-lucide="wallet"></i></div></div>
</div>
<?php endif; ?>

<div class="z-panel">
    <div class="z-panel-head" style="justify-content: space-between; flex-wrap: wrap; gap: 10px; align-items: center;">
        <div class="z-panel-title" style="font-size: 0.95rem; display: flex; align-items: center; gap: 6px; white-space: nowrap;"><i data-lucide="list" style="width:16px;height:16px;"></i>Daftar Tagihan Siswa</div>
        <form action="" method="GET" style="display:flex; align-items:flex-end; gap:15px; flex-wrap:wrap;">
            <div style="display:flex; flex-direction:column; align-items:flex-start; gap:4px;">
                <label class="z-label" style="margin:0; font-size:10px; font-weight:700; letter-spacing:0.5px; color:#64748b;"><i data-lucide="filter" style="width:10px; height:10px; display:inline-block; vertical-align:middle; margin-right:2px;"></i> TAMPILKAN</label>
                <select id="entryLimit" class="z-field" style="width:auto; padding:4px 10px; font-size:0.85rem; margin:0;" onchange="changePage(1)">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; align-items:flex-start; gap:4px;">
                <label class="z-label" style="margin:0; font-size:10px; font-weight:700; letter-spacing:0.5px; color:#64748b;">KELAS</label>
                <select name="kelas_id" class="z-field" style="width:auto; min-width:120px; padding:4px 10px; font-size:0.85rem; margin:0;" required onchange="this.form.submit()">
                    <option value="">- Pilih Kelas -</option>
                    <?php foreach($kelasList as $k): ?>
                    <option value="<?php echo $k['id']; ?>" <?php echo $kelas_id==$k['id']?'selected':''; ?>><?php echo $k['nama_kelas']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
                <button type="submit" class="btn btn-primary" style="padding:4px 12px; font-size:0.85rem; height:auto;">Filter</button>
                <?php if(!empty($rekap)): ?>
                <a href="/keuangan/komite/rekap/cetak?kelas_id=<?php echo $kelas_id; ?>" target="_blank" class="btn btn-outline" style="padding:4px 12px; font-size:0.85rem; height:auto; display:flex; align-items:center; gap:5px;"><i data-lucide="printer" style="width:14px; height:14px;"></i> Cetak</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <div class="z-table-wrap"><table id="tbl">
        <thead><tr><th>Siswa</th><th>Tagihan</th><th>Terbayar</th><th>Sisa</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach($rekap as $p): ?>
            <tr>
                <td><div class="z-uc"><div class="z-uc-av"><?php echo strtoupper(substr($p['nama'],0,2)); ?></div><div><div class="z-uc-name"><?php echo $p['nama']; ?></div><div class="z-uc-sub">NIS: <?php echo $p['nis']; ?></div></div></div></td>
                <td style="font-weight:600; font-size:.82rem;">Rp <?php echo number_format($p['jumlah_tagihan'],0,',','.'); ?></td>
                <td style="font-weight:600; font-size:.82rem; color:#16a34a;">Rp <?php echo number_format($p['terbayar'],0,',','.'); ?></td>
                <td style="font-weight:600; font-size:.82rem; color:#dc2626;">Rp <?php echo number_format($p['sisa'],0,',','.'); ?></td>
                <td><span class="pill <?php echo $p['status']==='Lunas' || $p['status']==='Tidak Ada Tagihan' ? 'pill-green' : ($p['status']==='Mengangsur' ? 'pill-blue' : 'pill-amber'); ?>"><?php echo $p['status']; ?></span></td>
                <td>
                    <button type="button" class="btn btn-outline btn-sm" onclick="showUnpaid(this)" data-name="<?php echo htmlspecialchars($p['nama'], ENT_QUOTES); ?>" data-unpaid='<?php echo htmlspecialchars(json_encode($p['unpaid_details'] ?? []), ENT_QUOTES); ?>' style="padding:4px 8px; font-size:0.75rem;" title="Lihat Tagihan Belum Lunas"><i data-lucide="eye" style="width:13px;height:13px;"></i> Lihat</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($rekap)): ?>
            <tr><td colspan="6" style="text-align:center;color:var(--z-muted);padding:20px;">
                <?php echo ($kelas_id==0) ? 'Silakan pilih Kelas terlebih dahulu.' : 'Tidak ada data siswa ditemukan.'; ?>
            </td></tr>
            <?php endif; ?>
        </tbody>
    </table></div>
    <div class="z-panel-foot" style="display:flex; justify-content:space-between; align-items:center; padding: 10px 20px;">
        <div id="pageInfo" style="font-size: 0.85rem; color: var(--z-muted);">Menampilkan 1-10 dari <?php echo count($rekap); ?> data</div>
        <div style="display:flex; gap:5px;">
            <button class="btn btn-ghost btn-sm" onclick="changePage(currentPage-1)"><i data-lucide="chevron-left" style="width:14px;"></i></button>
            <div id="pageList" style="display:flex; gap:5px;"></div>
            <button class="btn btn-ghost btn-sm" onclick="changePage(currentPage+1)"><i data-lucide="chevron-right" style="width:14px;"></i></button>
        </div>
    </div>
</div>
</div>
</div>

<!-- Modal Lihat Tagihan -->
<div id="modal-unpaid" class="z-modal-ov" style="z-index:9999; display:none;">
    <div class="z-modal" style="width:100%;max-width:700px;margin:auto;">
        <div class="z-modal-head">
            <h2 style="margin:0;font-size:1.2rem;display:flex;align-items:center;gap:10px;"><i data-lucide="eye" style="color:#2563eb;"></i> Tagihan Belum Lunas</h2>
            <button type="button" class="z-modal-close" onclick="document.getElementById('modal-unpaid').style.display='none'"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <div style="font-weight:600; margin-bottom:12px; color:#1e293b;" id="unpaid-student-name"></div>
            <div id="unpaid-list" style="max-height: 300px; overflow-y: auto;">
                <!-- content generated by JS -->
            </div>
        </div>
        <div class="z-modal-foot">
            <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-unpaid').style.display='none'">Tutup</button>
        </div>
    </div>
</div>

<script>
function showUnpaid(btn) {
    const rawData = btn.getAttribute('data-unpaid');
    const name = btn.getAttribute('data-name');
    const data = JSON.parse(rawData);
    const container = document.getElementById('unpaid-list');
    document.getElementById('unpaid-student-name').innerText = 'Siswa: ' + name;
    
    container.innerHTML = '';
    
    if (data.length === 0) {
        container.innerHTML = '<div style="padding:15px; text-align:center; color:#64748b; background:#f8fafc; border-radius:6px;">Tidak ada tagihan yang tertunggak. Semua Lunas! 🎉</div>';
    } else {
        let html = '<div class="z-table-wrap" style="box-shadow:none; border:1px solid var(--z-border);"><table class="z-table" style="margin:0;"><thead><tr><th style="background:#f8fafc;">Nama Tagihan</th><th style="background:#f8fafc; text-align:right;">Sisa (Rp)</th></tr></thead><tbody>';
        data.forEach(item => {
            html += `<tr>
                <td style="font-weight:500;">${item.nama_tagihan}</td>
                <td style="text-align:right; color:#dc2626; font-weight:600;">${Number(item.sisa).toLocaleString('id-ID')}</td>
            </tr>`;
        });
        html += '</tbody></table></div>';
        container.innerHTML = html;
    }
    
    document.getElementById('modal-unpaid').style.display = 'flex';
}

let currentPage = 1;
function changePage(page) {
    const limit = parseInt(document.getElementById('entryLimit').value);
    const tbody = document.querySelector('#tbl tbody');
    const rows = Array.from(tbody.querySelectorAll('tr:not(.no-data)'));
    
    // Check if empty
    if(rows.length === 0 || tbody.innerHTML.includes('Tidak ada data')) return;

    const totalPages = Math.ceil(rows.length / limit);
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    currentPage = page;

    const start = (currentPage - 1) * limit;
    const end = start + limit;

    rows.forEach((row, index) => {
        if (index >= start && index < end) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    const info = document.getElementById('pageInfo');
    if (info) {
        const startDisp = rows.length === 0 ? 0 : start + 1;
        const endDisp = Math.min(end, rows.length);
        info.innerText = `Menampilkan ${startDisp}-${endDisp} dari ${rows.length} data`;
    }

    renderPageList(totalPages);
}

function renderPageList(totalPages) {
    const list = document.getElementById('pageList');
    if (!list) return;
    list.innerHTML = '';
    
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    
    if (endPage - startPage < 4) {
        startPage = Math.max(1, endPage - 4);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const btn = document.createElement('button');
        btn.className = `btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-outline'}`;
        btn.innerText = i;
        btn.onclick = (e) => { e.preventDefault(); changePage(i); };
        list.appendChild(btn);
    }
}

// Initialize on load
document.addEventListener("DOMContentLoaded", () => {
    changePage(1);
});
</script>