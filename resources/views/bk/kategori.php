<?php
$title = $title ?? 'Kategori Poin BK | MTs RS';
$activeMenu = $activeMenu ?? 'bk_kategori';

ob_start();
?>

<div class="welcome-hero" style="background: linear-gradient(135deg, #3b82f6 0%, #4f46e5 100%); margin-bottom: 1.5rem; border-radius: 16px; padding: 2rem 2.5rem; position: relative; overflow: hidden; color: white; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);">
    <!-- Wave pattern from screenshot -->
    <svg style="position: absolute; bottom: -2px; left: 0; width: 100%; height: auto; opacity: 0.15; transform: scaleY(1.2);" viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg"><path fill="#ffffff" fill-opacity="1" d="M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,250.7C672,235,768,181,864,181.3C960,181,1056,235,1152,234.7C1248,235,1344,181,1392,154.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>
    <svg style="position: absolute; bottom: -2px; left: 0; width: 100%; height: auto; opacity: 0.1;" viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg"><path fill="#ffffff" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,165.3C960,192,1056,224,1152,213.3C1248,203,1344,149,1392,122.7L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>
    <svg style="position: absolute; bottom: -2px; left: 0; width: 100%; height: auto; opacity: 0.05;" viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg"><path fill="#ffffff" fill-opacity="1" d="M0,256L48,245.3C96,235,192,213,288,218.7C384,224,480,256,576,277.3C672,299,768,309,864,288C960,267,1056,213,1152,192C1248,171,1344,181,1392,186.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>

    <div style="position:relative; z-index:2; flex:1;">
        <h1 style="font-size: 1.7rem; font-weight: 800; margin: 0 0 0.5rem; display: flex; align-items: center; gap: 12px; letter-spacing: -0.5px;">
            <i data-lucide="tags" style="width:28px;height:28px;opacity:0.9;"></i> Kategori Poin BK
        </h1>
        <p style="margin: 0; opacity: 0.85; font-size: 0.95rem; line-height: 1.5;">Kelola master data pelanggaran dan prestasi beserta poinnya.</p>
    </div>
    <div style="position:relative; z-index:2;">
        <button onclick="showAddKategori()" style="background: white; color: #4f46e5; border: none; padding: 0.75rem 1.25rem; border-radius: 12px; font-weight: 700; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: 0.2s;">
            <i data-lucide="plus" style="width:18px;height:18px;"></i> Tambah Kategori
        </button>
    </div>
</div>

<?php \App\Core\Helper::showFlash(); ?>

<div class="z-panel">
    <table class="z-table">
        <thead>
            <tr>
                <th>Tipe</th>
                <th>Nama Kategori</th>
                <th>Poin</th>
                <th style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $currentSection = '';
            foreach($kategoriList as $k): 
                $sectionName = '';
                $bgColor = '#f8fafc';
                $textColor = '#334155';
                $iconColor = '#64748b';
                
                if ($k['tipe'] == 'prestasi') {
                    $sectionName = 'PRESTASI';
                    $bgColor = '#d1fae5';
                    $textColor = '#065f46';
                    $iconColor = '#10b981';
                } else {
                    $tingkatLabel = $k['tingkat'] ? ucfirst($k['tingkat']) : 'Tidak Diketahui';
                    $sectionName = 'PELANGGARAN ' . strtoupper($tingkatLabel);
                    
                    if ($k['tingkat'] == 'berat') {
                        $bgColor = '#fee2e2';
                        $textColor = '#991b1b';
                        $iconColor = '#ef4444';
                    } elseif ($k['tingkat'] == 'sedang') {
                        $bgColor = '#ffedd5';
                        $textColor = '#9a3412';
                        $iconColor = '#f97316';
                    } elseif ($k['tingkat'] == 'ringan') {
                        $bgColor = '#fef9c3';
                        $textColor = '#854d0e';
                        $iconColor = '#eab308';
                    }
                }

                if ($sectionName !== $currentSection): 
                    $currentSection = $sectionName;
            ?>
            <tr style="background: <?php echo $bgColor; ?>;">
                <td colspan="4" style="font-weight: 800; color: <?php echo $textColor; ?>; padding: 1.25rem 1rem 0.75rem; border-bottom: 2px solid #cbd5e1; font-size: 0.95rem; letter-spacing: 0.5px;">
                    <i data-lucide="<?php echo $k['tipe'] == 'prestasi' ? 'star' : 'alert-triangle'; ?>" style="width: 16px; height: 16px; margin-right: 6px; color: <?php echo $iconColor; ?>; vertical-align: middle;"></i>
                    <span style="vertical-align: middle;"><?php echo $sectionName; ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <td>
                    <?php if ($k['tipe'] == 'pelanggaran'): ?>
                        <?php if (isset($k['tingkat']) && $k['tingkat'] == 'berat'): ?>
                            <span class="pill" style="background:#fee2e2; color:#b91c1c;">Pel. Berat</span>
                        <?php elseif (isset($k['tingkat']) && $k['tingkat'] == 'sedang'): ?>
                            <span class="pill" style="background:#ffedd5; color:#c2410c;">Pel. Sedang</span>
                        <?php elseif (isset($k['tingkat']) && $k['tingkat'] == 'ringan'): ?>
                            <span class="pill" style="background:#fef9c3; color:#a16207;">Pel. Ringan</span>
                        <?php else: ?>
                            <span class="pill pill-red">Pelanggaran</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="pill pill-green">Prestasi</span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($k['nama_kategori']); ?></td>
                <td style="font-weight: bold;"><?php echo $k['poin']; ?></td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        <button style="padding: 6px 12px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px; border: 1px solid #e2e8f0; background: white; color: #475569; border-radius: 6px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'" onclick='editKategori(<?php echo htmlspecialchars(json_encode($k)); ?>)'>
                            <i data-lucide="edit-3" style="width:14px; height:14px;"></i> Edit
                        </button>
                        <a href="<?php echo \App\Core\Helper::url('/bk/kategori/delete/' . $k['id']); ?>" style="padding: 6px 12px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px; background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; border-radius: 6px; text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'" onclick="return confirm('Hapus kategori ini?')">
                            <i data-lucide="trash-2" style="width:14px; height:14px;"></i> Hapus
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($kategoriList)): ?>
                <tr><td colspan="4" style="text-align:center; padding: 2rem; color: #64748b;">Belum ada kategori data poin yang ditambahkan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Form -->
<style>
    .modal-input { width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; margin-top: 0.4rem; box-sizing: border-box; font-family: inherit; font-size: 0.95rem; transition: 0.2s; background: #f8fafc; color: #0f172a; }
    .modal-input:focus { border-color: #3b82f6; background: #ffffff; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    .modal-label { font-size: 0.8rem; font-weight: 800; color: #64748b; display: block; margin-bottom: 0.25rem; letter-spacing: 0.5px; text-transform: uppercase; }
    .modal-form-group { margin-bottom: 1.25rem; }
    .btn-modal-cancel { background: #f1f5f9; color: #475569; padding: 0.7rem 1.25rem; border-radius: 8px; border: none; font-weight: 700; cursor: pointer; transition: 0.2s; font-size: 0.9rem; }
    .btn-modal-cancel:hover { background: #e2e8f0; }
    .btn-modal-save { background: #3b82f6; color: white; padding: 0.7rem 1.5rem; border-radius: 8px; border: none; font-weight: 700; cursor: pointer; transition: 0.2s; font-size: 0.9rem; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25); display: flex; align-items: center; gap: 8px; }
    .btn-modal-save:hover { background: #2563eb; transform: translateY(-1px); }
</style>

<div id="modalAdd" class="z-modal-ov" style="display:none;">
    <div class="z-modal" style="max-width: 450px;">
        <div class="z-modal-head">
            <div class="z-modal-title" id="modalTitle">
                <i data-lucide="tag" style="color: #3b82f6;"></i> Tambah Kategori
            </div>
            <div class="z-modal-close" onclick="document.getElementById('modalAdd').style.display='none'">
                <i data-lucide="x"></i>
            </div>
        </div>
        <div class="z-modal-body">
            <form action="<?php echo \App\Core\Helper::url('/bk/kategori/save'); ?>" method="POST" id="formKategori">
                <input type="hidden" name="id" id="kategori_id">
                
                <div class="z-form-group">
                    <label class="z-label">Tipe Kategori</label>
                    <select name="tipe" id="kategori_tipe" class="z-field" required onchange="document.getElementById('tingkat_container').style.display = this.value === 'pelanggaran' ? 'block' : 'none'">
                        <option value="pelanggaran">Pelanggaran (Poin Plus)</option>
                        <option value="prestasi">Prestasi (Poin Minus)</option>
                    </select>
                </div>

                <div class="z-form-group" id="tingkat_container">
                    <label class="z-label">Tingkat Pelanggaran</label>
                    <select name="tingkat" id="kategori_tingkat" class="z-field">
                        <option value="ringan">Ringan</option>
                        <option value="sedang">Sedang</option>
                        <option value="berat">Berat</option>
                    </select>
                </div>

                <div class="z-form-group">
                    <label class="z-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori" id="kategori_nama" class="z-field" placeholder="Misal: Datang Terlambat" required>
                </div>

                <div class="z-form-group">
                    <label class="z-label">Bobot Poin</label>
                    <input type="number" name="poin" id="kategori_poin" class="z-field" required value="0" min="0" placeholder="0">
                </div>
            </form>
        </div>
        <div class="z-modal-foot">
            <button class="btn btn-ghost" onclick="document.getElementById('modalAdd').style.display='none'">Batal</button>
            <button class="btn btn-primary" onclick="document.getElementById('formKategori').submit()">
                <i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan Data
            </button>
        </div>
    </div>
</div>

<script>
function moveModalToBody() {
    var modal = document.getElementById('modalAdd');
    if (modal && modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }
}

function showAddKategori() {
    moveModalToBody();
    document.getElementById('modalTitle').innerHTML = '<i data-lucide="tag" style="color: #3b82f6;"></i> Tambah Kategori';
    document.getElementById('kategori_id').value = '';
    document.getElementById('kategori_tipe').value = 'pelanggaran';
    document.getElementById('tingkat_container').style.display = 'block';
    document.getElementById('kategori_tingkat').value = 'ringan';
    document.getElementById('kategori_nama').value = '';
    document.getElementById('kategori_poin').value = '0';
    document.getElementById('modalAdd').style.display = 'flex';
}

function editKategori(data) {
    moveModalToBody();
    document.getElementById('modalTitle').innerHTML = '<i data-lucide="edit-3" style="color: #3b82f6;"></i> Edit Kategori';
    document.getElementById('kategori_id').value = data.id;
    document.getElementById('kategori_tipe').value = data.tipe;
    if (data.tipe === 'pelanggaran') {
        document.getElementById('tingkat_container').style.display = 'block';
        document.getElementById('kategori_tingkat').value = data.tingkat || 'ringan';
    } else {
        document.getElementById('tingkat_container').style.display = 'none';
        document.getElementById('kategori_tingkat').value = 'ringan';
    }
    document.getElementById('kategori_nama').value = data.nama_kategori;
    document.getElementById('kategori_poin').value = data.poin;
    document.getElementById('modalAdd').style.display = 'flex';
}
</script>

<?php 
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php'; 
?>
