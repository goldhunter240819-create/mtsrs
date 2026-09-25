<?php
$_s_uri = $_SERVER['REQUEST_URI'];
?>
<div class="z-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <main class="z-main">
        <div class="z-header">
            <div>
                <h1 class="z-title">Absen V2 <span style="font-weight:400;color:#94a3b8;font-size:1rem;margin:0 10px;">&gt;</span> Konfigurasi Jam Khusus Guru</h1>
                <div class="z-subtitle">Atur jam masuk khusus untuk masing-masing guru.</div>
            </div>
        </div>

        <div class="z-content">
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
            <div style="background:#ecfdf5; border-left:4px solid #10b981; color:#065f46; padding:15px; border-radius:8px; margin-bottom:20px; font-weight:700; display:flex; align-items:center; gap:10px;">
                <i data-lucide="check-circle" style="width:20px;"></i> Konfigurasi jam khusus guru berhasil disimpan!
            </div>
            <?php endif; ?>

            <div class="min-card">
                <form action="/absen/pengaturan-guru/save" method="POST">
                    <div style="overflow-x:auto;">
                        <table class="z-table">
                            <thead>
                                <tr>
                                    <th style="width:50px;">No</th>
                                    <th>Nama Guru</th>
                                    <th style="width:250px;">Custom Jam Masuk Normal</th>
                                    <th style="width:250px;">Custom Batas Terlambat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($gurus)): ?>
                                <tr>
                                    <td colspan="4" style="text-align:center; padding:30px; color:#64748b;">Belum ada data guru.</td>
                                </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach($gurus as $g): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td style="font-weight:700; color:#1e293b;"><?= htmlspecialchars($g['nama']) ?></td>
                                        <td>
                                            <input type="time" name="gurus[<?= $g['id'] ?>][jam_masuk]" value="<?= $g['custom_jam_masuk'] ?>" class="min-input" style="padding: 6px; width: 100%;">
                                        </td>
                                        <td>
                                            <input type="time" name="gurus[<?= $g['id'] ?>][batas_terlambat]" value="<?= $g['custom_batas_terlambat'] ?>" class="min-input" style="padding: 6px; width: 100%;">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div style="margin-top:20px; display:flex; justify-content:flex-end;">
                        <button type="submit" class="z-btn-primary" style="background:#6366f1; border:none; padding:12px 25px; border-radius:12px; color:#fff; font-weight:800; cursor:pointer; display:flex; align-items:center; gap:8px;">
                            <i data-lucide="save" style="width:18px;"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<style>
.min-card { background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; }
.min-input { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; font-family: inherit; font-size: 0.9rem; transition: 0.2s; }
.min-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
</style>
