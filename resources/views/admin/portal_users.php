<?php
$title = $title ?? 'Akses Portal Guru | Admin Panel';
$activeMenu = $activeMenu ?? 'admin_portal_users';

ob_start();
?>
<style>
    /* Modern Toggle Switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
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
        background-color: #10b981;
    }
    input:checked + .slider:before {
        transform: translateX(20px);
    }
    
    .guru-card {
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(226,232,240,0.6);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 1rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        transition: 0.3s;
    }
    .guru-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
    }
    .guru-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        gap: 12px;
    }
    .guru-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 0;
    }
    .guru-avatar {
        width: 42px; height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: #475569; font-size: 1rem;
        flex-shrink: 0;
    }
    .guru-avatar.active {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    .guru-name {
        font-size: 0.95rem; font-weight: 700; color: #1e293b;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .guru-username {
        font-size: 0.75rem; color: #64748b; font-weight: 500;
    }
    .guru-status-badge {
        font-size: 0.65rem; font-weight: 700; padding: 3px 10px;
        border-radius: 20px; white-space: nowrap;
    }
    .guru-status-badge.active {
        background: rgba(16,185,129,0.1); color: #059669;
    }
    .guru-status-badge.inactive {
        background: rgba(148,163,184,0.15); color: #64748b;
    }

    .module-dropdown {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease, padding 0.3s ease;
        border-top: 0px solid transparent;
    }
    .module-dropdown.open {
        max-height: 600px;
        border-top: 1px solid #f1f5f9;
    }
    
    .module-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.65rem 1.25rem;
        border-bottom: 1px solid #f8fafc;
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
        width: 30px; height: 30px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        color: #64748b;
    }

    .search-box {
        width: 100%; padding: 10px 16px 10px 40px;
        border-radius: 12px; border: 1px solid var(--z-border);
        background: var(--z-bg); color: var(--z-text);
        font-size: 0.9rem; outline: none;
        transition: 0.2s;
    }
    .search-box:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14,165,233,0.1);
    }
</style>

<div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
    <div>
        <h1 class="mph-title"><i data-lucide="shield"></i> Akses Portal Guru</h1>
        <p class="mph-subtitle">Atur akses Portal Hub dan modul yang dapat dilihat oleh setiap Guru</p>
    </div>
</div>

<!-- Search Bar -->
<div style="position: relative; margin-bottom: 1.5rem;">
    <i data-lucide="search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 18px; color: #94a3b8;"></i>
    <input type="text" class="search-box" id="searchGuru" placeholder="Cari nama guru..." oninput="filterGuru(this.value)">
</div>

<!-- Stats -->
<div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
    <div style="background: rgba(16,185,129,0.08); padding: 10px 20px; border-radius: 12px; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="check-circle" style="width:16px; color:#10b981;"></i>
        <span style="font-weight: 700; color: #059669;" id="countActive"><?php echo count(array_filter($users, fn($u) => $u['portal_enabled'])); ?></span>
        <span style="font-size: 0.85rem; color: #64748b;">Guru Aktif Portal</span>
    </div>
    <div style="background: rgba(148,163,184,0.08); padding: 10px 20px; border-radius: 12px; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="x-circle" style="width:16px; color:#94a3b8;"></i>
        <span style="font-weight: 700; color: #64748b;" id="countInactive"><?php echo count(array_filter($users, fn($u) => !$u['portal_enabled'])); ?></span>
        <span style="font-size: 0.85rem; color: #64748b;">Guru Nonaktif Portal</span>
    </div>
</div>

<!-- Guru List -->
<div id="guruList">
    <?php foreach($users as $usr): 
        $accessStr = $usr['portal_access'];
        $modules = \App\Core\Helper::getPortalModules();
        $allKeys = array_keys($modules);
        
        if (is_null($accessStr)) {
            $accessList = $allKeys;
        } else if ($accessStr === '') {
            $accessList = [];
        } else {
            $accessList = array_filter(array_map('trim', explode(',', $accessStr)));
        }

        $displayName = !empty($usr['nama']) ? $usr['nama'] : $usr['username'];
        $isEnabled = (int)$usr['portal_enabled'];
    ?>
    <div class="guru-card" data-name="<?php echo htmlspecialchars(strtolower($displayName)); ?>">
        <div class="guru-card-head">
            <div class="guru-info">
                <div class="guru-avatar <?php echo $isEnabled ? 'active' : ''; ?>" id="avatar-<?php echo $usr['id']; ?>">
                    <?php echo strtoupper(substr($displayName, 0, 1)); ?>
                </div>
                <div style="min-width: 0;">
                    <div class="guru-name"><?php echo htmlspecialchars($displayName); ?></div>
                    <div class="guru-username">Username: <?php echo htmlspecialchars($usr['username']); ?></div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <span class="guru-status-badge <?php echo $isEnabled ? 'active' : 'inactive'; ?>" id="badge-<?php echo $usr['id']; ?>">
                    <?php echo $isEnabled ? 'Portal Aktif' : 'Portal Nonaktif'; ?>
                </span>
                <label class="switch">
                    <input type="checkbox" onchange="toggleMaster(this, <?php echo $usr['id']; ?>)" <?php echo $isEnabled ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
            </div>
        </div>
        <div class="module-dropdown <?php echo $isEnabled ? 'open' : ''; ?>" id="modules-<?php echo $usr['id']; ?>">
            <div style="padding: 0.5rem 1.25rem 0.25rem; font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">
                <i data-lucide="layout-grid" style="width:12px; display:inline; vertical-align:middle;"></i> Modul yang Diizinkan
            </div>
            <?php foreach($modules as $key => $mod): ?>
                <?php $isChecked = in_array($key, $accessList); ?>
                <div class="module-item">
                    <div class="module-info">
                        <div class="module-icon" style="color: <?php echo $mod['color']; ?>; background: <?php echo $mod['color']; ?>15;">
                            <i data-lucide="<?php echo $mod['icon']; ?>" style="width:16px;"></i>
                        </div>
                        <span style="font-weight: 600; color: #334155; font-size: 0.88rem;"><?php echo $mod['title']; ?></span>
                    </div>
                    <label class="switch">
                        <input type="checkbox" onchange="toggleAccess(this, <?php echo $usr['id']; ?>, '<?php echo $key; ?>')" <?php echo $isChecked ? 'checked' : ''; ?>>
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
        
        toast.style.cssText = `
            background: ${bgColor}; color: white; padding: 12px 20px; border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 10px;
            font-weight: 600; font-size: 0.9rem; transform: translateX(100%); opacity: 0;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        `;
        toast.innerHTML = `<i data-lucide="${type === 'success' ? 'check-circle' : 'alert-circle'}" style="width:18px;"></i> ${message}`;
        container.appendChild(toast);
        lucide.createIcons();
        
        setTimeout(() => { toast.style.transform = 'translateX(0)'; toast.style.opacity = '1'; }, 10);
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)'; toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function toggleMaster(checkbox, userId) {
        const isEnabled = checkbox.checked ? 1 : 0;
        const dropdown = document.getElementById('modules-' + userId);
        const avatar = document.getElementById('avatar-' + userId);
        const badge = document.getElementById('badge-' + userId);
        
        fetch('<?php echo \App\Core\Helper::url('/admin/portal-users/toggle-master'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `user_id=${userId}&is_enabled=${isEnabled}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                if (isEnabled) {
                    dropdown.classList.add('open');
                    avatar.classList.add('active');
                    badge.className = 'guru-status-badge active';
                    badge.textContent = 'Portal Aktif';
                } else {
                    dropdown.classList.remove('open');
                    avatar.classList.remove('active');
                    badge.className = 'guru-status-badge inactive';
                    badge.textContent = 'Portal Nonaktif';
                }
                showToast(isEnabled ? 'Portal diaktifkan!' : 'Portal dinonaktifkan!', 'success');
                updateCounts();
            } else {
                checkbox.checked = !checkbox.checked;
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            checkbox.checked = !checkbox.checked;
            showToast('Terjadi kesalahan jaringan', 'error');
        });
    }

    function toggleAccess(checkbox, userId, moduleKey) {
        const isActive = checkbox.checked ? 1 : 0;
        
        fetch('<?php echo \App\Core\Helper::url('/admin/portal-users/toggle'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `user_id=${userId}&module_key=${moduleKey}&is_active=${isActive}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showToast('Modul berhasil diperbarui!', 'success');
            } else {
                checkbox.checked = !checkbox.checked;
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            checkbox.checked = !checkbox.checked;
            showToast('Terjadi kesalahan jaringan', 'error');
        });
    }

    function filterGuru(query) {
        const cards = document.querySelectorAll('.guru-card');
        const q = query.toLowerCase();
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            card.style.display = name.includes(q) ? '' : 'none';
        });
    }

    function updateCounts() {
        const cards = document.querySelectorAll('.guru-card');
        let active = 0, inactive = 0;
        cards.forEach(card => {
            const badge = card.querySelector('.guru-status-badge');
            if (badge && badge.classList.contains('active')) active++;
            else inactive++;
        });
        document.getElementById('countActive').textContent = active;
        document.getElementById('countInactive').textContent = inactive;
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
