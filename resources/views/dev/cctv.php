<?php 
ob_start(); 
?>

<style>
    /* CCTV Dashboard Theme */
    .cctv-container {
        font-family: 'JetBrains Mono', 'Courier New', Courier, monospace;
        background-color: #000;
        color: #0f0;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 0 20px rgba(0,255,0,0.1);
        min-height: 80vh;
        position: relative;
        overflow: hidden;
    }
    
    .cctv-container::before {
        content: " ";
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
        z-index: 2;
        background-size: 100% 2px, 3px 100%;
        pointer-events: none;
    }

    .cctv-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #0f0;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .cctv-header h1 {
        margin: 0;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #0f0;
        text-shadow: 0 0 5px #0f0;
    }

    .live-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
        color: red;
        font-weight: bold;
        animation: blink 1s infinite;
    }

    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0.3; }
        100% { opacity: 1; }
    }

    .cctv-table-wrap {
        overflow-x: auto;
    }

    .cctv-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .cctv-table th, .cctv-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid rgba(0, 255, 0, 0.2);
    }

    .cctv-table th {
        color: #fff;
        background: rgba(0, 255, 0, 0.1);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .cctv-table tr:hover {
        background: rgba(0, 255, 0, 0.05);
    }

    .badge-action {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: bold;
        background: rgba(0, 255, 0, 0.2);
        color: #0f0;
        border: 1px solid #0f0;
    }

    .badge-action.danger {
        color: #ff3333;
        border-color: #ff3333;
        background: rgba(255, 0, 0, 0.1);
    }

    .badge-action.warning {
        color: #ffcc00;
        border-color: #ffcc00;
        background: rgba(255, 204, 0, 0.1);
    }
    
    .badge-action.info {
        color: #00ccff;
        border-color: #00ccff;
        background: rgba(0, 204, 255, 0.1);
    }

    .cctv-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        font-size: 0.8rem;
        color: rgba(0, 255, 0, 0.6);
    }
    
    .cctv-btn {
        background: transparent;
        color: #0f0;
        border: 1px solid #0f0;
        padding: 5px 15px;
        cursor: pointer;
        font-family: inherit;
        transition: 0.2s;
    }
    .cctv-btn:hover {
        background: #0f0;
        color: #000;
    }
</style>

<div class="cctv-container">
    <div class="cctv-header">
        <h1><i data-lucide="cctv"></i> SYSTEM MONITORING (CAM-01)</h1>
        <div class="live-indicator">
            <div style="width:10px; height:10px; background:red; border-radius:50%;"></div>
            REC
        </div>
    </div>

    <div class="cctv-table-wrap">
        <table class="cctv-table">
            <thead>
                <tr>
                    <th style="width: 150px;">TIMESTAMP</th>
                    <th style="width: 150px;">USER/ACTOR</th>
                    <th style="width: 120px;">MODULE</th>
                    <th style="width: 100px;">ACTION</th>
                    <th>DESCRIPTION</th>
                    <th style="width: 120px;">IP ADDRESS</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($logs)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: rgba(0,255,0,0.5);">NO ACTIVITY LOGS FOUND IN DATABASE.</td>
                </tr>
                <?php else: ?>
                    <?php foreach($logs as $log): 
                        $actionClass = 'badge-action';
                        $act = strtoupper($log['action']);
                        if($act == 'DELETE') $actionClass .= ' danger';
                        else if($act == 'UPDATE' || $act == 'EDIT') $actionClass .= ' warning';
                        else if($act == 'LOGIN' || $act == 'LOGOUT') $actionClass .= ' info';
                    ?>
                    <tr>
                        <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($log['username'] ?? 'Unknown'); ?></td>
                        <td><?php echo htmlspecialchars($log['module']); ?></td>
                        <td><span class="<?php echo $actionClass; ?>"><?php echo htmlspecialchars($log['action']); ?></span></td>
                        <td><?php echo htmlspecialchars($log['description']); ?></td>
                        <td style="font-size: 0.75rem; opacity: 0.7;"><?php echo htmlspecialchars($log['ip_address'] ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="cctv-footer">
        <div>SERVER: MTS-RS-MAIN-NODE</div>
        <div>
            <button class="cctv-btn" onclick="location.reload()">REFRESH LOG</button>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
    // Auto refresh every 30 seconds
    setTimeout(() => {
        location.reload();
    }, 30000);
</script>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layout.php';
?>
