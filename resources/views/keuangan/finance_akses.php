<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="settings"></i> Pengaturan & Akses</h1>
        <p class="mph-subtitle">
        Kelola hak akses menu dan pengaturan bendahara umum.
    </p>
    </div>
</div>
<div class="z-content-pad">




<?php if(isset($_GET['msg'])): ?>
    <div style="background: <?php echo $_GET['msg']=='deleted' ? '#fee2e2' : '#dcfce7'; ?>; color: <?php echo $_GET['msg']=='deleted' ? '#b91c1c' : '#15803d'; ?>; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 0.85rem; border: 1px solid <?php echo $_GET['msg']=='deleted' ? '#fca5a5' : '#bbf7d0'; ?>;">
        <?php if($_GET['msg'] == 'saved'): ?>Pengaturan akses berhasil disimpan!
        <?php elseif($_GET['msg'] == 'deleted'): ?>Pengaturan akses berhasil dihapus!
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="z-panel" style="margin-bottom: 25px;">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="user-check"></i> Bendahara Umum (Tanda Tangan Surat Tagihan)</div></div>
    <div class="z-panel-body" style="padding: 20px;">
        <form action="/keuangan/akses/bendahara-umum" method="POST" style="display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 250px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem; color: #475569;">Nama Bendahara Umum (Bebas / Resmi)</label>
                <input type="text" name="bendahara_nama" class="z-input" placeholder="Contoh: Admin " value="<?= htmlspecialchars($institusi['bendahara_nama'] ?? '') ?>" style="width: 100%;" required>
            </div>
            <div style="flex: 1; min-width: 250px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem; color: #475569;">No WhatsApp Bendahara Umum</label>
                <input type="text" name="bendahara_wa" class="z-input" placeholder="Contoh: 08123456789" value="<?= htmlspecialchars($institusi['bendahara_wa'] ?? '') ?>" style="width: 100%;">
            </div>
            <div style="width: 100%; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; margin-top: 5px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 10px 15px; background: #fff; border: 1px solid var(--z-border); border-radius: 8px;">
                    <input type="checkbox" name="disable_web_trx" value="1" <?= (!empty($institusi['disable_web_trx'])) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: #dc2626;">
                    <span style="font-weight: 600; font-size: 0.9rem; color: #dc2626;">Larang Pencatatan Transaksi via Web (Hanya Kasir via APK yang bisa mencatat)</span>
                </label>
                <button type="submit" class="btn btn-primary" style="padding: 11px 20px;"><i data-lucide="save"></i> Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>

<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="shield-check"></i> Daftar Hak Akses</div></div>
    <div class="z-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Petugas / Bendahara</th>
                    <th>Kategori Tanggung Jawab</th>
                    <th>Akses Menu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($aksesList) > 0): ?>
                    <?php foreach ($aksesList as $a): ?>
                        <tr>
                            <td>
                                <strong style="color: #0f172a;"><?= htmlspecialchars($a['nama']) ?></strong><br>
                                <span style="font-size: 0.75rem; color: #64748b;">NIP: <?= htmlspecialchars($a['nip']) ?></span>
                            </td>
                            <td>
                                <?php if(empty($a['kategori_tanggung_jawab'])): ?>
                                    <span class="pill pill-gray">Tidak Ada Kategori</span>
                                <?php else: ?>
                                    <?php 
                                    $cats = explode(', ', $a['kategori_tanggung_jawab']);
                                    foreach($cats as $cat): 
                                    ?>
                                        <span class="pill pill-green" style="margin:2px; display:inline-block;"><?= htmlspecialchars($cat) ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(empty($a['akses_menu_arr'])): ?>
                                    <span class="pill pill-gray">Tidak Ada Akses Menu</span>
                                <?php else: ?>
                                    <?php foreach($a['akses_menu_arr'] as $m): ?>
                                        <span class="pill pill-blue" style="margin:2px; display:inline-block;"><?= htmlspecialchars($availableMenus[$m] ?? $m) ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display:flex;gap:5px;">
                                    <button class="btn btn-outline btn-sm btn-edit-akses" 
                                            data-id="<?= $a['id'] ?>" 
                                            data-guru-id="<?= $a['guru_id'] ?>" 
                                            data-nama="<?= htmlspecialchars($a['nama'], ENT_QUOTES) ?>" 
                                            data-menu="<?= htmlspecialchars($a['akses_menu'], ENT_QUOTES) ?>" 
                                            title="Edit Akses Menu">
                                        <i data-lucide="edit" style="width:12px;height:12px;"></i> Edit Menu
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px; color: #64748b;">Belum ada petugas yang memiliki hak akses. Hubungkan Guru ke Kategori di Master Kategori terlebih dahulu.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>

<!-- Modal Edit Akses Menu -->
<div id="mOvAkses" class="z-modal-ov">
    <div class="z-modal" style="max-width: 500px;">
        <div class="z-modal-head">
            <div class="z-modal-title" id="mAksesTitle"><i data-lucide="shield"></i> Edit Akses Menu</div>
            <div class="z-modal-close" onclick="document.getElementById('mOvAkses').classList.remove('open')"><i data-lucide="x"></i></div>
        </div>
        <form action="/keuangan/akses/save" method="POST">
            <div class="z-modal-body">
                <input type="hidden" name="guru_id" id="a_guru_id" value="">
                
                <div class="z-form-group">
                    <label class="z-label">Petugas / Bendahara</label>
                    <input type="text" id="a_nama" class="z-field" readonly style="background: #f1f5f9; cursor: not-allowed;">
                </div>
                
                <div class="z-form-group">
                    <label class="z-label">Pilih Menu yang Bisa Diakses:</label>
                    <div style="display:flex; flex-direction:column; gap:8px; margin-top:10px; max-height: 250px; overflow-y:auto; padding: 10px; border: 1px solid var(--z-border); border-radius: 8px; background: #fff;">
                        <?php foreach($availableMenus as $k => $v): ?>
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="akses_menu[]" value="<?= htmlspecialchars($k) ?>" class="a-menu-cb">
                                <span><?= htmlspecialchars($v) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="z-modal-foot">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('mOvAkses').classList.remove('open')">Batal</button>
                <button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:14px;height:14px;"></i> Simpan Hak Akses</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var m = document.getElementById('mOvAkses');
    if(m && m.parentElement !== document.body) {
        document.body.appendChild(m);
    }

    var btns = document.querySelectorAll('.btn-edit-akses');
    btns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var guruId = this.getAttribute('data-guru-id');
            var nama = this.getAttribute('data-nama');
            var menusJson = this.getAttribute('data-menu');
            
            document.getElementById('a_guru_id').value = guruId;
            document.getElementById('a_nama').value = nama;
            
            var cbs = document.querySelectorAll('.a-menu-cb');
            cbs.forEach(cb => cb.checked = false);
            
            try {
                var menus = JSON.parse(menusJson);
                if (Array.isArray(menus)) {
                    cbs.forEach(cb => {
                        if (menus.includes(cb.value)) {
                            cb.checked = true;
                        }
                    });
                }
            } catch(e) {}
            
            document.getElementById('mOvAkses').classList.add('open');
            if(typeof lucide !== 'undefined') lucide.createIcons();
        });
    });
});
</script>

</div>