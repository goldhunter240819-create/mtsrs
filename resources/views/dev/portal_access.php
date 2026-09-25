<?php
$title = $title ?? 'Akses Portal | Dev Panel';
$activeMenu = $activeMenu ?? 'dev_portal_access';

ob_start();
?>
<style>
    /* Modern Toggle Switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    .switch input { 
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 24px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    input:checked + .slider {
        background-color: #10b981; /* Green when active */
    }
    input:checked + .slider:before {
        transform: translateX(20px);
    }
    
    .module-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .module-item:last-child {
        border-bottom: none;
    }
    .module-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .module-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        background: #f8fafc;
        display: flex; align-items: center; justify-content: center;
        color: #64748b;
    }
</style>

<div class="modern-page-header">
    <div>
        <h1 class="mph-title"><i data-lucide="toggle-right"></i> Akses Portal <span class="z-badge" style="background: rgba(255,255,255,0.2); color: white; border-color: rgba(255,255,255,0.4); margin-left: 10px;">BETA</span></h1>
        <p class="mph-subtitle">Atur modul portal yang dapat dilihat oleh setiap admin</p>
    </div>
</div>

<div class="z-tabs" style="margin-bottom: 2rem;">
    <a href="<?php echo \App\Core\Helper::url('/dev'); ?>" class="z-tab <?php echo ($activeMenu == 'dev') ? 'active' : ''; ?>">Dashboard</a>
    <a href="<?php echo \App\Core\Helper::url('/dev/admins'); ?>" class="z-tab <?php echo ($activeMenu == 'dev_admins') ? 'active' : ''; ?>">Akun Admin</a>
    <a href="<?php echo \App\Core\Helper::url('/dev/portal-access'); ?>" class="z-tab <?php echo ($activeMenu == 'dev_portal_access') ? 'active' : ''; ?>">Akses Portal</a>
    <a href="<?php echo \App\Core\Helper::url('/dev/settings'); ?>" class="z-tab <?php echo ($activeMenu == 'dev_settings') ? 'active' : ''; ?>">API & Integrasi</a>
</div>

<div class="z-grid" style="grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem;">
    <?php foreach($admins as $adm): 
        // Default to all if null, meaning if null, we assume all are checked initially
        $accessStr = $adm['portal_access'];
        
        $modules = \App\Core\Helper::getPortalModules();
        $allKeys = array_keys($modules);
        
        $accessList = is_null($accessStr) ? $allKeys : array_filter(array_map('trim', explode(',', $accessStr)));
    ?>
    <div class="z-panel" style="margin-bottom: 0;">
        <div class="z-panel-head" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
            <div class="z-panel-title" style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; font-weight:700; color:#475569;">
                    <?php echo strtoupper(substr($adm['username'], 0, 1)); ?>
                </div>
                <div>
                    <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;"><?php echo htmlspecialchars($adm['username']); ?></div>
                    <div style="font-size: 0.75rem; color: #64748b;">Role Admin (1)</div>
                </div>
            </div>
        </div>
        <div class="z-panel-body" style="padding: 0;">
            <?php foreach($modules as $key => $mod): ?>
                <?php $isChecked = in_array($key, $accessList); ?>
                <div class="module-item">
                    <div class="module-info">
                        <div class="module-icon" style="color: <?php echo $mod['color']; ?>; background: <?php echo $mod['color']; ?>15;">
                            <i data-lucide="<?php echo $mod['icon']; ?>" style="width:18px;"></i>
                        </div>
                        <span style="font-weight: 600; color: #334155;"><?php echo $mod['title']; ?></span>
                    </div>
                    <label class="switch">
                        <input type="checkbox" onchange="toggleAccess(this, <?php echo $adm['id']; ?>, '<?php echo $key; ?>')" <?php echo $isChecked ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Toast Notification Container -->
<div id="toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;"></div>

<script>
    lucide.createIcons();

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? '#10b981' : '#ef4444';
        const icon = type === 'success' ? 'check-circle' : 'alert-circle';
        
        toast.style.cssText = `
            background: ${bgColor}; color: white; padding: 12px 20px; border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 10px;
            font-weight: 600; font-size: 0.9rem; transform: translateX(100%); opacity: 0;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        `;
        toast.innerHTML = `<i data-lucide="${icon}" style="width:18px;"></i> ${message}`;
        container.appendChild(toast);
        lucide.createIcons();
        
        // Animate in
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
            toast.style.opacity = '1';
        }, 10);
        
        // Animate out
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function toggleAccess(checkbox, adminId, moduleKey) {
        const isActive = checkbox.checked ? 1 : 0;
        
        fetch('<?php echo \App\Core\Helper::url('/dev/portal-access/toggle'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `admin_id=${adminId}&module_key=${moduleKey}&is_active=${isActive}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message, 'success');
            } else {
                checkbox.checked = !checkbox.checked; // Revert
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            checkbox.checked = !checkbox.checked; // Revert
            showToast('Terjadi kesalahan jaringan', 'error');
        });
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
