<?php
$title = $title ?? 'Dev Panel | MTs RS';
$activeMenu = $activeMenu ?? 'dev';

ob_start();
?>
<style>
    .dev-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .dev-stat-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(226, 232, 240, 0.6);
        border-radius: 16px; padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    .dev-stat-title {
        font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;
        display: flex; align-items: center; gap: 8px; margin-bottom: 12px;
    }
    .dev-stat-val { font-size: 1.5rem; font-weight: 800; color: #1e293b; }
    
    .dev-log-viewer {
        background: #0f172a; color: #33ff00;
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.85rem; padding: 1rem; border-radius: 12px;
        height: 300px; overflow-y: auto; white-space: pre-wrap;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.5);
    }

    .query-textarea {
        width: 100%; min-height: 120px;
        background: #1e293b; color: #fff;
        border: 1px solid #334155; border-radius: 12px;
        padding: 1rem; font-family: monospace; font-size: 0.9rem;
        resize: vertical; outline: none; margin-bottom: 1rem;
    }
    .query-textarea:focus { border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,0.2); }
</style>

<div class="modern-page-header">
    <div>
        <h1 class="mph-title"><i data-lucide="terminal"></i> Dev Panel <span class="z-badge" style="background: rgba(255,255,255,0.2); color: white; border-color: rgba(255,255,255,0.4); margin-left: 10px;">BETA</span></h1>
        <p class="mph-subtitle">System Overview & Developer Tools</p>
    </div>
    <div class="mph-actions">
        <form action="<?php echo \App\Core\Helper::url('/dev/cache-clear'); ?>" method="POST" style="margin:0;">
            <button type="submit" class="btn btn-primary-white" onclick="return confirm('Bersihkan seluruh cache aplikasi?')">
                <i data-lucide="trash-2"></i> Clear Cache
            </button>
        </form>
        <form action="<?php echo \App\Core\Helper::url('/dev/backup-db'); ?>" method="POST" style="margin:0;">
            <button type="submit" class="btn btn-primary-white">
                <i data-lucide="database-backup"></i> Backup DB
            </button>
        </form>
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

<!-- System Info Grid -->
<div class="dev-grid">
    <!-- Server Info -->
    <div class="dev-stat-card">
        <div class="dev-stat-title"><i data-lucide="server"></i> Server Info</div>
        <div style="display:flex; flex-direction:column; gap:8px;">
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f5f9; padding-bottom:5px;">
                <span style="color:#64748b;">OS</span>
                <span style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($sysInfo['os']); ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f5f9; padding-bottom:5px;">
                <span style="color:#64748b;">Software</span>
                <span style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($sysInfo['server_software']); ?></span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span style="color:#64748b;">PHP Version</span>
                <span style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($sysInfo['php_version']); ?></span>
            </div>
        </div>
    </div>

    <!-- Database Info -->
    <div class="dev-stat-card">
        <div class="dev-stat-title"><i data-lucide="database"></i> Database MySQL</div>
        <div style="display:flex; flex-direction:column; gap:8px;">
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f5f9; padding-bottom:5px;">
                <span style="color:#64748b;">Version</span>
                <span style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($dbInfo['version']); ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f5f9; padding-bottom:5px;">
                <span style="color:#64748b;">Size (db_mts_rs)</span>
                <span style="font-weight:600; color:#10b981;"><?php echo htmlspecialchars($dbInfo['size']); ?></span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span style="color:#64748b;">Status</span>
                <span style="font-weight:600; color:#1e293b;"><span style="color:#10b981;">●</span> Connected</span>
            </div>
        </div>
    </div>

    <!-- PHP Settings -->
    <div class="dev-stat-card">
        <div class="dev-stat-title"><i data-lucide="settings"></i> PHP Config</div>
        <div style="display:flex; flex-direction:column; gap:8px;">
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f5f9; padding-bottom:5px;">
                <span style="color:#64748b;">Memory Limit</span>
                <span style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($sysInfo['memory_limit']); ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f5f9; padding-bottom:5px;">
                <span style="color:#64748b;">Upload Max</span>
                <span style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($sysInfo['upload_max_filesize']); ?></span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span style="color:#64748b;">Post Max Size</span>
                <span style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($sysInfo['post_max_size']); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- SQL Runner & Log Viewer -->
<div class="dev-grid" style="grid-template-columns: 1fr 1fr;">
    
    <!-- SQL Query Runner -->
    <div class="z-panel">
        <div class="z-panel-head">
            <div class="z-panel-title"><i data-lucide="terminal-square"></i> SQL Query Runner</div>
        </div>
        <div class="z-panel-body">
            <form action="<?php echo \App\Core\Helper::url('/dev/query'); ?>" method="POST">
                <textarea name="query" class="query-textarea" placeholder="SELECT * FROM users LIMIT 10;" required></textarea>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <i data-lucide="play"></i> Execute Query
                </button>
            </form>

            <?php if (isset($_SESSION['dev_query_results'])): ?>
                <div style="margin-top: 1.5rem;">
                    <div style="font-weight: 700; margin-bottom: 10px; color: #1e293b;">
                        Results (<?php echo $_SESSION['dev_query_count']; ?> rows):
                    </div>
                    <div style="overflow-x: auto; max-height: 250px; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <table class="table table-bordered table-hover" style="margin: 0; font-size: 0.8rem;">
                            <?php if (!empty($_SESSION['dev_query_results'])): ?>
                                <thead style="position: sticky; top: 0; background: #f8fafc; z-index: 10;">
                                    <tr>
                                        <?php foreach (array_keys($_SESSION['dev_query_results'][0]) as $col): ?>
                                            <th><?php echo htmlspecialchars($col); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_SESSION['dev_query_results'] as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $val): ?>
                                                <td><?php echo htmlspecialchars((string)$val); ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            <?php else: ?>
                                <tr><td>No records returned.</td></tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
                <?php 
                    unset($_SESSION['dev_query_results']); 
                    unset($_SESSION['dev_query_count']); 
                ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Error Log Viewer -->
    <div class="z-panel">
        <div class="z-panel-head">
            <div class="z-panel-title"><i data-lucide="file-warning"></i> Recent Error Logs</div>
        </div>
        <div class="z-panel-body" style="padding: 0;">
            <div class="dev-log-viewer">
<?php
if (empty($recentErrors)) {
    echo "No errors found or error log not accessible.\nPath: " . ($errorLogPath ? $errorLogPath : 'Not configured');
} else {
    foreach ($recentErrors as $line) {
        echo htmlspecialchars($line);
    }
}
?>
            </div>
        </div>
    </div>
    
</div>

<script>
    lucide.createIcons();
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
