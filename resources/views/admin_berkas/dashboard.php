<?php
$title = $title ?? 'Dashboard Manajemen Berkas | MTs RS';
$activeMenu = $activeMenu ?? 'manajemen_berkas_dashboard';
?>

<div class="modern-page-header">
    <div>
        <h1 class="mph-title"><i data-lucide="layout-dashboard"></i> Dashboard Manajemen Berkas</h1>
        <p class="mph-subtitle">Ringkasan arsip berkas pribadi dan perangkat pembelajaran guru.</p>
    </div>
</div>

<div class="z-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Stat 1 -->
    <div class="z-panel" style="margin-bottom: 0; background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); border-color: #c7d2fe;">
        <div class="z-panel-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 60px; height: 60px; background: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #4f46e5; flex-shrink: 0; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.1);">
                <i data-lucide="user" style="width: 28px; height: 28px;"></i>
            </div>
            <div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #4338ca; text-transform: uppercase; letter-spacing: 0.5px;">Total Berkas Pribadi</div>
                <div style="font-size: 2rem; font-weight: 800; color: #312e81; line-height: 1; margin-top: 5px;"><?= number_format($countPribadi) ?></div>
            </div>
        </div>
    </div>
    
    <!-- Stat 2 -->
    <div class="z-panel" style="margin-bottom: 0; background: linear-gradient(135deg, #fdf4ff 0%, #fae8ff 100%); border-color: #f5d0fe;">
        <div class="z-panel-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 60px; height: 60px; background: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #c026d3; flex-shrink: 0; box-shadow: 0 4px 10px rgba(192, 38, 211, 0.1);">
                <i data-lucide="book-open" style="width: 28px; height: 28px;"></i>
            </div>
            <div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #a21caf; text-transform: uppercase; letter-spacing: 0.5px;">Total Perangkat Pembelajaran</div>
                <div style="font-size: 2rem; font-weight: 800; color: #701a75; line-height: 1; margin-top: 5px;"><?= number_format($countPerangkat) ?></div>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
