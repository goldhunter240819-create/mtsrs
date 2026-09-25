<?php
$title = $title ?? 'Berkas Pribadi Guru | MTs RS';
$activeMenu = $activeMenu ?? 'manajemen_berkas_pribadi';
?>

<div class="modern-page-header">
    <div>
        <h1 class="mph-title"><i data-lucide="user"></i> Berkas Pribadi Guru</h1>
        <p class="mph-subtitle">Kelola dan unduh arsip berkas pribadi guru.</p>
    </div>
    <div class="mph-actions">
        <form action="" method="GET" style="display:flex; gap:10px;">
            <input type="text" name="q" placeholder="Cari nama atau judul..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" style="width: 100%; max-width: 250px; padding: 10px 15px; border-radius: 8px; border: 1px solid #cbd5e1; background: white; color: #1e293b; outline: none; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <button type="submit" class="btn btn-primary" style="padding: 10px 20px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);"><i data-lucide="search" style="width:16px; height:16px;"></i> Cari</button>
        </form>
    </div>
</div>

<div class="z-panel">
    <div class="z-panel-head">
        <h3 class="z-panel-title">Daftar Berkas Pribadi</h3>
    </div>
    <div class="z-panel-body" style="padding: 0; overflow-x: auto;">
        <table class="z-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Jenis Berkas</th>
                    <th>Judul Berkas</th>
                    <th>Tanggal Diunggah</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grouped = [];
                if(!empty($pribadi)){
                    foreach($pribadi as $p){
                        $guruId = $p['guru_id'];
                        if(!isset($grouped[$guruId])){
                            $grouped[$guruId] = [
                                'nama_guru' => $p['nama_guru'],
                                'nip' => $p['nip'],
                                'berkas' => []
                            ];
                        }
                        $grouped[$guruId]['berkas'][] = $p;
                    }
                }
                ?>
                <?php if(empty($grouped)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: #64748b;">
                        Belum ada berkas pribadi yang diunggah.
                    </td>
                </tr>
                <?php else: foreach($grouped as $guruId => $group): ?>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; cursor: pointer;" onclick="toggleBerkas(<?= $guruId ?>)">
                    <td colspan="5" style="padding: 12px 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-weight: 800; color: #1e293b; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
                                <div style="width:32px; height:32px; background:#e0e7ff; color:#4f46e5; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                                    <i data-lucide="user" style="width:18px; height:18px;"></i>
                                </div>
                                <div>
                                    <?= htmlspecialchars($group['nama_guru']) ?>
                                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 500; margin-top:2px;">NIP: <?= htmlspecialchars($group['nip'] ?? '-') ?></div>
                                </div>
                            </div>
                            <i data-lucide="chevron-down" id="icon-<?= $guruId ?>" style="color: #64748b; transition: transform 0.3s ease;"></i>
                        </div>
                    </td>
                </tr>
                <?php $no = 1; foreach($group['berkas'] as $d): ?>
                <tr class="berkas-row-<?= $guruId ?>" style="display: none;">
                    <td style="text-align: center; font-weight: 600; color: #94a3b8;"><?= $no++ ?></td>
                    <td>
                        <span class="z-badge" style="background: #eef2ff; color: #4f46e5; border-color: #c7d2fe;">
                            <?= htmlspecialchars($d['jenis_berkas']) ?>
                        </span>
                    </td>
                    <td style="font-weight: 600; color: #334155;">
                        <?= htmlspecialchars($d['judul_berkas']) ?>
                    </td>
                    <td style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                        <?= date('d M Y, H:i', strtotime($d['tanggal_upload'])) ?>
                    </td>
                    <td style="text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="<?= \App\Core\Helper::url('/manajemen-berkas/view-pdf/' . $d['file_nama']) ?>" class="btn btn-primary btn-sm" title="Lihat Berkas" style="display: inline-flex; align-items: center; gap: 6px; border-radius: 6px; font-weight: 600; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);" target="_blank">
                                <i data-lucide="eye" style="width:14px; height:14px;"></i> Lihat
                            </a>
                            <a href="<?= \App\Core\Helper::url('/manajemen-berkas/download/pribadi/' . $d['file_nama']) ?>" class="btn btn-sm" title="Download Berkas" style="display: inline-flex; align-items: center; gap: 6px; border-radius: 6px; font-weight: 600; color: #10b981; background: #ecfdf5; border: 1px solid #a7f3d0; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.1);">
                                <i data-lucide="download" style="width:14px; height:14px;"></i> Unduh
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    lucide.createIcons();
    
    function toggleBerkas(id) {
        const rows = document.querySelectorAll('.berkas-row-' + id);
        const icon = document.getElementById('icon-' + id);
        
        if(rows.length === 0) return;
        
        let isHidden = rows[0].style.display === 'none';
        
        rows.forEach(row => {
            row.style.display = isHidden ? 'table-row' : 'none';
        });
        
        if (icon) {
            icon.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
        }
    }
</script>
