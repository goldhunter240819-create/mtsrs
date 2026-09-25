<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="layers"></i> Dashboard Keuangan</h1>
        <p class="mph-subtitle">Manajemen Keuangan Terpadu</p>
    </div>
</div>
<div class="z-content-pad">



<div class="z-stats">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Pemasukan</div><div class="z-stat-value">Rp <?php echo number_format($totalPemasukan,0,',','.'); ?></div><div class="z-stat-trend z-trend-up"><i data-lucide="trending-up" style="width:12px;height:12px;"></i> Tahun ini</div></div><div class="z-stat-icon zi-green"><i data-lucide="trending-up"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Pengeluaran</div><div class="z-stat-value">Rp <?php echo number_format($totalPengeluaran,0,',','.'); ?></div><div class="z-stat-trend z-trend-dn">Operasional</div></div><div class="z-stat-icon zi-red"><i data-lucide="trending-down"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Saldo Bersih</div><div class="z-stat-value">Rp <?php echo number_format($saldo,0,',','.'); ?></div><div class="z-stat-trend z-trend-up">Surplus</div></div><div class="z-stat-icon zi-teal"><i data-lucide="wallet"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Tagihan Belum Bayar</div><div class="z-stat-value" style="color:var(--z-warning);"><?php echo $tagihanBelumBayar; ?></div><div class="z-stat-trend" style="color:var(--z-warning);">Siswa menunggak</div></div><div class="z-stat-icon zi-amber"><i data-lucide="alert-circle"></i></div></div>
</div>

<div style="display:grid;grid-template-columns:3fr 2fr;gap:1.25rem;flex-wrap:wrap;">
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="clock"></i>Transaksi Terbaru</div><a href="/admin/finance/laporan" class="btn btn-outline btn-sm">Laporan</a></div>
    <div class="z-table-wrap"><table>
        <thead><tr><th>Tanggal</th><th>Keterangan</th><th>Jenis</th><th>Jumlah</th></tr></thead>
        <tbody>
            <?php foreach(array_slice($transaksi,0,6) as $t): ?>
            <tr>
                <td style="font-size:.8rem;color:var(--z-muted);"><?php echo date('d/m/Y H:i',strtotime($t['waktu'])); ?></td>
                <td style="font-weight:600;"><?php echo $t['keterangan']; ?></td>
                <td><span class="pill <?php echo $t['jenis']==='Pemasukan'?'pill-green':'pill-red'; ?>"><?php echo $t['jenis']; ?></span></td>
                <td class="<?php echo $t['jenis']==='Pemasukan'?'z-amount':'z-amount-red'; ?>">Rp <?php echo number_format($t['jumlah'],0,',','.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table></div>
</div>
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="zap"></i>Aksi Cepat</div></div>
    <div class="z-panel-body" style="display:flex;flex-direction:column;gap:4px;">
        <style>
            .quick-action-item {
                display: flex; align-items: center; gap: 12px; padding: 10px 12px; text-decoration: none; border-radius: 12px; transition: all 0.2s;
            }
            .quick-action-item:hover { background: rgba(0,0,0,0.02); transform: translateX(4px); }
            .quick-action-icon {
                width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            }
            .quick-action-text { flex: 1; font-size: 0.82rem; font-weight: 700; color: var(--z-text); }
            .quick-action-arrow { width: 16px; height: 16px; color: #cbd5e1; transition: 0.2s; }
            .quick-action-item:hover .quick-action-arrow { color: var(--z-primary); transform: translateX(2px); }
        </style>
        
        <a href="/admin/finance/tagihan" class="quick-action-item">
            <div class="quick-action-icon" style="background:rgba(16,185,129,0.1); color:#10b981;"><i data-lucide="file-text" style="width:18px;height:18px;"></i></div>
            <div class="quick-action-text">Kelola Tagihan</div>
            <i data-lucide="chevron-right" class="quick-action-arrow"></i>
        </a>
        <a href="/admin/finance/cetak-buku-manual" class="quick-action-item">
            <div class="quick-action-icon" style="background:rgba(5,150,105,0.1); color:#059669;"><i data-lucide="book-open" style="width:18px;height:18px;"></i></div>
            <div class="quick-action-text">Cetak Buku Pembayaran</div>
            <i data-lucide="chevron-right" class="quick-action-arrow"></i>
        </a>
        <a href="/admin/finance/cetak-buku-kas" class="quick-action-item">
            <div class="quick-action-icon" style="background:rgba(79,70,229,0.1); color:#4f46e5;"><i data-lucide="wallet" style="width:18px;height:18px;"></i></div>
            <div class="quick-action-text">Cetak Buku Kas Umum</div>
            <i data-lucide="chevron-right" class="quick-action-arrow"></i>
        </a>
        <a href="/admin/finance/cetak-cover" class="quick-action-item">
            <div class="quick-action-icon" style="background:rgba(139,92,246,0.1); color:#8b5cf6;"><i data-lucide="book" style="width:18px;height:18px;"></i></div>
            <div class="quick-action-text">Cetak Cover Buku</div>
            <i data-lucide="chevron-right" class="quick-action-arrow"></i>
        </a>
        <div style="height:1px; background:rgba(0,0,0,0.05); margin:4px 0;"></div>
        <a href="/admin/finance/gaji" class="quick-action-item">
            <div class="quick-action-icon" style="background:rgba(99,102,241,0.1); color:#6366f1;"><i data-lucide="users" style="width:18px;height:18px;"></i></div>
            <div class="quick-action-text">Data Gaji</div>
            <i data-lucide="chevron-right" class="quick-action-arrow"></i>
        </a>
        <a href="/admin/finance/biaya" class="quick-action-item">
            <div class="quick-action-icon" style="background:rgba(245,158,11,0.1); color:#f59e0b;"><i data-lucide="receipt" style="width:18px;height:18px;"></i></div>
            <div class="quick-action-text">Biaya Sekolah</div>
            <i data-lucide="chevron-right" class="quick-action-arrow"></i>
        </a>
        <a href="/admin/finance/laporan" class="quick-action-item">
            <div class="quick-action-icon" style="background:rgba(100,116,139,0.1); color:#64748b;"><i data-lucide="bar-chart-2" style="width:18px;height:18px;"></i></div>
            <div class="quick-action-text">Laporan Keuangan</div>
            <i data-lucide="chevron-right" class="quick-action-arrow"></i>
        </a>
    </div>
</div>
</div>
</div>

</div>