<?php
$title = $title ?? 'API & Integrasi | Dev Panel';
$activeMenu = $activeMenu ?? 'dev_settings';

ob_start();
?>

<div class="modern-page-header">
    <div>
        <h1 class="mph-title"><i data-lucide="blocks"></i> API & Integrasi <span class="z-badge" style="background: rgba(255,255,255,0.2); color: white; border-color: rgba(255,255,255,0.4); margin-left: 10px;">BETA</span></h1>
        <p class="mph-subtitle">Kelola integrasi pihak ketiga (Push Notifications dll)</p>
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

<div class="z-panel" style="max-width: 800px; margin: 0 auto;">
    <div class="z-panel-head">
        <div class="z-panel-title">
            <svg style="width: 20px; height: 20px; margin-right: 8px; color: #ea4c89;" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2C6.486 2 2 6.486 2 12c0 2.296.792 4.409 2.109 6.079l-1.378 1.378a1 1 0 0 0 1.414 1.414l1.378-1.378A9.957 9.957 0 0 0 12 22c5.514 0 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm4-9H8a1 1 0 0 0 0 2h8a1 1 0 0 0 0-2z"/></svg> 
            Konfigurasi OneSignal
        </div>
    </div>
    <div class="z-panel-body">
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            <strong>Informasi:</strong><br>
            API ini digunakan untuk mengirim Push Notification secara langsung ke aplikasi APK (Android/iOS) guru maupun siswa. Dapatkan kredensial ini di dashboard <a href="https://onesignal.com" target="_blank" style="font-weight: 700; color: #1d4ed8; text-decoration: underline;">OneSignal</a>.
        </div>

        <form action="<?php echo \App\Core\Helper::url('/dev/settings/save'); ?>" method="POST">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display:block; margin-bottom: 0.5rem; font-weight:600; color:#475569;">OneSignal App ID</label>
                <input type="text" name="onesignal_app_id" class="form-control" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px;" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" value="<?php echo htmlspecialchars($settings['onesignal_app_id'] ?? ''); ?>">
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display:block; margin-bottom: 0.5rem; font-weight:600; color:#475569;">REST API Key</label>
                <input type="text" name="onesignal_rest_api_key" class="form-control" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px;" placeholder="os_v2_app_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" value="<?php echo htmlspecialchars($settings['onesignal_rest_api_key'] ?? ''); ?>">
            </div>

            <div style="text-align: right; border-top: 1px solid #f1f5f9; padding-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                    <i data-lucide="save"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
