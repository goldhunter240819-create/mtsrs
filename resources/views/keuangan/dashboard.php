<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="pie-chart" style="color: #bfdbfe;"></i> Dashboard Manajemen Keuangan
        </h1>
        <p class="mph-subtitle">Pantau arus pemasukan kas, tagihan SPP, sisa tunggakan, serta mutasi tabungan siswa madrasah.</p>
    </div>
</div>

<div class="z-stats" style="margin-bottom: 2rem;">
    <div class="z-stat">
        <div class="z-stat-left">
            <div class="z-stat-label">Total Pemasukan</div>
            <div class="z-stat-value" style="color:var(--z-accent);">Rp <?php echo number_format($statPemasukan, 0, ',', '.'); ?></div>
            <div style="font-size: 0.75rem; color: var(--z-muted); margin-top: 4px;">Pencatatan real-time kas</div>
        </div>
        <div class="z-stat-icon zi-teal"><i data-lucide="trending-up"></i></div>
    </div>

    <div class="z-stat">
        <div class="z-stat-left">
            <div class="z-stat-label">Total Tagihan</div>
            <div class="z-stat-value" style="color:var(--z-primary);">Rp <?php echo number_format($statTagihanTotal, 0, ',', '.'); ?></div>
            <div style="font-size: 0.75rem; color: var(--z-muted); margin-top: 4px;">Seluruh komponen tagihan</div>
        </div>
        <div class="z-stat-icon zi-blue"><i data-lucide="receipt"></i></div>
    </div>

    <div class="z-stat">
        <div class="z-stat-left">
            <div class="z-stat-label">Sisa Tunggakan</div>
            <div class="z-stat-value" style="color:#f43f5e;">Rp <?php echo number_format($statTunggakan, 0, ',', '.'); ?></div>
            <div style="font-size: 0.75rem; color: var(--z-muted); margin-top: 4px;">Belum terbayar penuh</div>
        </div>
        <div class="z-stat-icon zi-amber"><i data-lucide="alert-circle"></i></div>
    </div>

    <div class="z-stat">
        <div class="z-stat-left">
            <div class="z-stat-label">Total Saldo Tabungan</div>
            <div class="z-stat-value" style="color:var(--z-purple);">Rp <?php echo number_format($statTabunganTotal, 0, ',', '.'); ?></div>
            <div style="font-size: 0.75rem; color: var(--z-muted); margin-top: 4px;">Total tabungan siswa</div>
        </div>
        <div class="z-stat-icon zi-purple"><i data-lucide="wallet"></i></div>
    </div>
</div>

<div class="z-panel">
    <div class="z-panel-head">
        <div class="z-panel-title"><i data-lucide="clock"></i> Transaksi Pembayaran Terbaru</div>
        <a href="/keuangan/pembayaran" class="btn btn-primary btn-sm">Input Pembayaran</a>
    </div>

    <div class="z-table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 150px;">No Kwitansi</th>
                    <th>Nama Siswa</th>
                    <th>Komponen Tagihan</th>
                    <th>Nominal Bayar</th>
                    <th>Tanggal Transaksi</th>
                    <th style="text-align:center; width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentTrx)): ?>
                <tr>
                    <td colspan="6" style="text-align:center; color: var(--z-muted); padding: 2rem;">Belum ada transaksi pembayaran.</td>
                </tr>
                <?php endif; ?>
                <?php foreach($recentTrx as $t): ?>
                <tr>
                    <td style="font-weight:800; color: var(--z-purple); font-family:monospace;"><?php echo htmlspecialchars($t['nomor_kwitansi']); ?></td>
                    <td style="font-weight:700; color:var(--z-text);"><?php echo htmlspecialchars($t['nama_siswa']); ?></td>
                    <td><span class="pill pill-blue"><?php echo htmlspecialchars($t['nama_komponen']); ?></span></td>
                    <td style="font-weight:800; color: var(--z-accent);">Rp <?php echo number_format($t['nominal'], 0, ',', '.'); ?></td>
                    <td style="font-size: 0.85rem; color: var(--z-muted);"><?php echo date('d/m/Y H:i', strtotime($t['tanggal_bayar'])); ?></td>
                    <td>
                        <div class="z-action-btns">
                            <a href="/keuangan/kwitansi/<?php echo $t['id']; ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 4px 8px;" title="Cetak">
                                <i data-lucide="printer"></i> Cetak
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
