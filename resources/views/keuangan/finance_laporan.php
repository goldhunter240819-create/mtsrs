<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="layers"></i> Laporan Keuangan</h1>
        <p class="mph-subtitle">Rekapitulasi pemasukan dan pengeluaran sekolah</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        <button type="button" class="btn btn-outline" onclick="window.print()"><i data-lucide="printer" style="width:14px;height:14px;"></i>Cetak</button>
    </div>
</div>
<div class="z-content-pad">
<div class="z-stats" style="grid-template-columns: repeat(3, 1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Pemasukan</div><div class="z-stat-value">Rp <?php echo number_format($totalPemasukan,0,',','.'); ?></div></div><div class="z-stat-icon zi-green"><i data-lucide="trending-up"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Pengeluaran</div><div class="z-stat-value">Rp <?php echo number_format($totalPengeluaran,0,',','.'); ?></div></div><div class="z-stat-icon zi-red"><i data-lucide="trending-down"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Saldo Bersih</div><div class="z-stat-value" style="color:var(--z-primary);">Rp <?php echo number_format($saldo,0,',','.'); ?></div></div><div class="z-stat-icon zi-teal"><i data-lucide="wallet"></i></div></div>
</div>
<?php
$max = 1;
foreach($per_bulan as $b){ if($b['pemasukan']>$max) $max=$b['pemasukan']; }
?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="bar-chart-2"></i>Pemasukan per Bulan</div></div>
    <div class="z-panel-body">
        <?php foreach($per_bulan as $b): ?>
        <div class="bar-row">
            <div class="bar-lbl"><?php echo $b['bulan']; ?></div>
            <div class="bar-track"><div class="bar-fill" style="width:<?php echo round($b['pemasukan']/$max*100); ?>%;"></div></div>
            <div class="bar-val">Rp <?php echo number_format($b['pemasukan']/1000000,1); ?>jt</div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="list"></i>Rekap Bulanan</div></div>
    <div class="z-table-wrap"><table>
        <thead><tr><th>Bulan</th><th>Pemasukan</th><th>Pengeluaran</th><th>Saldo</th></tr></thead>
        <tbody>
            <?php foreach($per_bulan as $b): $s=$b['pemasukan']-$b['pengeluaran']; ?>
            <tr>
                <td style="font-weight:700;"><?php echo $b['bulan']; ?></td>
                <td class="z-amount">Rp <?php echo number_format($b['pemasukan'],0,',','.'); ?></td>
                <td class="z-amount-red">Rp <?php echo number_format($b['pengeluaran'],0,',','.'); ?></td>
                <td style="font-weight:800;color:<?php echo $s>=0?'var(--z-primary)':'var(--z-danger)'; ?>">Rp <?php echo number_format($s,0,',','.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table></div>
</div>
</div>
</div>
</div>