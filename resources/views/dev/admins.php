<?php
$title = $title ?? 'Manajemen Admin | Dev Panel';
$activeMenu = $activeMenu ?? 'dev_admins';

ob_start();
?>

<div class="modern-page-header">
    <div>
        <h1 class="mph-title"><i data-lucide="users"></i> Manajemen Admin <span class="z-badge" style="background: rgba(255,255,255,0.2); color: white; border-color: rgba(255,255,255,0.4); margin-left: 10px;">BETA</span></h1>
        <p class="mph-subtitle">Atur akun admin dan hak akses modul portal</p>
    </div>
</div>

<div class="z-tabs" style="margin-bottom: 2rem;">
    <a href="<?php echo \App\Core\Helper::url('/dev'); ?>" class="z-tab <?php echo ($activeMenu == 'dev') ? 'active' : ''; ?>">Dashboard</a>
    <a href="<?php echo \App\Core\Helper::url('/dev/admins'); ?>" class="z-tab <?php echo ($activeMenu == 'dev_admins') ? 'active' : ''; ?>">Akun Admin</a>
    <a href="<?php echo \App\Core\Helper::url('/dev/portal-access'); ?>" class="z-tab <?php echo ($activeMenu == 'dev_portal_access') ? 'active' : ''; ?>">Akses Portal</a>
    <a href="<?php echo \App\Core\Helper::url('/dev/settings'); ?>" class="z-tab <?php echo ($activeMenu == 'dev_settings') ? 'active' : ''; ?>">API & Integrasi</a>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div style="background: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <div style="font-weight: 700;"><i data-lucide="check-circle" style="width:18px; vertical-align:middle; margin-right:8px; color:#10b981;"></i> <?php echo $_SESSION['flash_success']; ?></div>
        <button onclick="this.parentElement.style.display='none'" style="background:none; border:none; color:#065f46; cursor:pointer;"><i data-lucide="x"></i></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div style="background: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start;">
        <div style="font-weight: 700;"><i data-lucide="alert-circle" style="width:18px; vertical-align:middle; margin-right:8px; color:#ef4444;"></i> <?php echo $_SESSION['flash_error']; ?></div>
        <button onclick="this.parentElement.style.display='none'" style="background:none; border:none; color:#991b1b; cursor:pointer;"><i data-lucide="x"></i></button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="z-panel">
    <div class="z-panel-head">
        <div class="z-panel-title"><i data-lucide="user-cog"></i> Daftar Admin (Role 1)</div>
        <div class="z-panel-actions">
            <button type="button" class="btn btn-primary" onclick="openAdminModal()">
                <i data-lucide="plus"></i> Tambah Admin
            </button>
        </div>
    </div>
    <div class="z-panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table table-hover" style="margin: 0;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th style="padding: 1rem 1.5rem;">Username</th>
                        <th style="padding: 1rem 1.5rem;">Status</th>
                        <th style="padding: 1rem 1.5rem; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($admins)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem; color: #64748b;">Belum ada data admin.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($admins as $adm): 
                            $accessList = array_filter(array_map('trim', explode(',', $adm['portal_access'])));
                            $badges = [];
                            foreach ($accessList as $ac) {
                                $badges[] = '<span class="pill pill-blue" style="margin-right:4px;">' . htmlspecialchars($ac) . '</span>';
                            }
                        ?>
                        <tr>
                            <td style="padding: 1rem 1.5rem; font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($adm['username']); ?></td>
                            <td style="padding: 1rem 1.5rem;">
                                <?php if ($adm['status'] == 'active'): ?>
                                    <span class="pill pill-green">Active</span>
                                <?php else: ?>
                                    <span class="pill pill-red">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem 1.5rem; text-align: right;">
                                <button type="button" class="btn btn-sm btn-primary-white" onclick='editAdmin(<?php echo json_encode($adm); ?>)' style="margin-right: 5px;">
                                    <i data-lucide="edit"></i> Edit
                                </button>
                                <a href="<?php echo \App\Core\Helper::url('/dev/admins/delete/'.$adm['id']); ?>" class="btn btn-sm" style="background: #fef2f2; color: #ef4444; border: 1px solid #fca5a5;" onclick="return confirm('Yakin hapus admin ini?');">
                                    <i data-lucide="trash-2"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Admin -->
<div id="modalAdmin" class="z-modal" style="display: none;">
    <div class="z-modal-content" style="max-width: 500px;">
        <div class="z-modal-header">
            <h3 class="z-modal-title" id="modalAdminTitle">Tambah Admin</h3>
            <button class="z-modal-close" onclick="closeAdminModal()"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <form action="<?php echo \App\Core\Helper::url('/dev/admins/save'); ?>" method="POST" id="formAdmin">
                <input type="hidden" name="id" id="admin_id">
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display:block; margin-bottom: 0.5rem; font-weight:600; color:#475569;">Username</label>
                    <input type="text" name="username" id="admin_username" class="form-control" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px;" required>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display:block; margin-bottom: 0.5rem; font-weight:600; color:#475569;">Password <small style="font-weight:400; color:#94a3b8;">(Kosongkan jika tidak ingin mengubah password saat edit)</small></label>
                    <input type="password" name="password" id="admin_password" class="form-control" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px;">
                </div>

                <div style="text-align: right;">
                    <button type="button" class="btn btn-primary-white" onclick="closeAdminModal()" style="margin-right: 10px;">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Basic Modal Styles */
    .z-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 9999; display: flex; align-items: center; justify-content: center; }
    .z-modal-content { background: #fff; border-radius: 16px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; }
    .z-modal-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .z-modal-title { margin: 0; font-size: 1.1rem; font-weight: 700; color: #1e293b; }
    .z-modal-close { background: none; border: none; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 4px; border-radius: 6px; transition: 0.2s; }
    .z-modal-close:hover { background: #f1f5f9; color: #1e293b; }
    .z-modal-body { padding: 1.5rem; }
</style>

<script>
    lucide.createIcons();

    function openAdminModal() {
        document.getElementById('modalAdminTitle').innerText = 'Tambah Admin';
        document.getElementById('formAdmin').reset();
        document.getElementById('admin_id').value = '';
        document.getElementById('admin_password').required = true;
        document.getElementById('modalAdmin').style.display = 'flex';
    }

    function editAdmin(admin) {
        document.getElementById('modalAdminTitle').innerText = 'Edit Admin';
        document.getElementById('formAdmin').reset();
        document.getElementById('admin_id').value = admin.id;
        document.getElementById('admin_username').value = admin.username;
        document.getElementById('admin_password').required = false;

        document.getElementById('modalAdmin').style.display = 'flex';
    }

    function closeAdminModal() {
        document.getElementById('modalAdmin').style.display = 'none';
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
