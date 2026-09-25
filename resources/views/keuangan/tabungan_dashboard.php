<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="layers"></i> </h1>
        <p class="mph-subtitle">Manajemen Keuangan Terpadu</p>
    </div>
</div>
<div class="z-content-pad">


                    <div class="z-stat-icon zi-purple"><i data-lucide="users"></i></div>
                </div>

                <div class="z-stat">
                    <div class="z-stat-left">
                        <div class="z-stat-label">Total Saldo Seluruh Siswa</div>
                        <div class="z-stat-value">Rp <?php echo number_format($totalSaldo, 0, ',', '.'); ?></div>
                        <div class="z-stat-trend" style="color:var(--z-success);">
                            <?php echo ($filter_petugas > 0) ? 'Saldo Dipegang Petugas' : 'Bank Mini Madrasah'; ?>
                        </div>
                    </div>
                    <div class="z-stat-icon zi-teal"><i data-lucide="wallet"></i></div>
                </div>
                
                <div class="z-stat">
                    <div class="z-stat-left">
                        <div class="z-stat-label">Setoran Hari Ini</div>
                        <div class="z-stat-value">Rp <?php echo number_format($setorHariIni, 0, ',', '.'); ?></div>
                        <div class="z-stat-trend" style="color:var(--z-primary);">Pemasukan Kas</div>
                    </div>
                    <div class="z-stat-icon zi-blue"><i data-lucide="arrow-down-to-line"></i></div>
                </div>

                <div class="z-stat">
                    <div class="z-stat-left">
                        <div class="z-stat-label">Penarikan Hari Ini</div>
                        <div class="z-stat-value">Rp <?php echo number_format($tarikHariIni, 0, ',', '.'); ?></div>
                        <div class="z-stat-trend" style="color:var(--z-danger);">Pengeluaran Kas</div>
                    </div>
                    <div class="z-stat-icon zi-rose"><i data-lucide="arrow-up-from-line"></i></div>
                </div>
            </div>

            <!-- Riwayat Transaksi -->
            <div class="z-panel">
                <div class="z-panel-head">
                    <div class="z-panel-title"><i data-lucide="history"></i> 5 Transaksi Terakhir</div>
                    <a href="/admin/tabungan/mutasi" class="btn btn-outline btn-sm">Lihat Semua</a>
                </div>
                <div class="z-panel-body" style="padding: 0;">
                    <?php if (empty($riwayat)): ?>
                        <div style="padding: 3rem; text-align: center; color: var(--z-muted);">
                            <i data-lucide="inbox" style="width: 48px; height: 48px; opacity: 0.3; margin-bottom: 10px; display: block; margin-left: auto; margin-right: auto;"></i>
                            <div style="font-weight: 600;">Belum ada transaksi hari ini</div>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x:auto;">
                            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--z-muted);">
                                    <tr>
                                        <th style="padding: 12px 15px;">Waktu</th>
                                        <th style="padding: 12px 15px;">Siswa</th>
                                        <th style="padding: 12px 15px;">Jenis Mutasi</th>
                                        <th style="padding: 12px 15px;">Jumlah</th>
                                        <th style="padding: 12px 15px;">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($riwayat as $r): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 12px 15px; font-size: 0.85rem; color: #475569;"><?php echo date('d M Y, H:i', strtotime($r['tanggal'])); ?></td>
                                        <td style="padding: 12px 15px;">
                                            <div style="font-weight: 700; color: #0f172a; font-size: 0.9rem;"><?php echo htmlspecialchars($r['nama_siswa']); ?></div>
                                            <div style="font-size: 0.75rem; color: var(--z-muted);">NIS: <?php echo htmlspecialchars($r['nis']); ?></div>
                                        </td>
                                        <td style="padding: 12px 15px;">
                                            <?php if ($r['jenis_mutasi'] == 'Setor'): ?>
                                                <span style="display:inline-flex; align-items:center; gap:4px; padding:4px 8px; background:#ecfdf5; color:#10b981; border-radius:6px; font-size:0.75rem; font-weight:700;"><i data-lucide="arrow-down-to-line" style="width:12px; height:12px;"></i> Setor Tunai</span>
                                            <?php else: ?>
                                                <span style="display:inline-flex; align-items:center; gap:4px; padding:4px 8px; background:#fff1f2; color:#f43f5e; border-radius:6px; font-size:0.75rem; font-weight:700;"><i data-lucide="arrow-up-from-line" style="width:12px; height:12px;"></i> Tarik Tunai</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding: 12px 15px;">
                                            <div style="font-weight: 800; color: <?php echo $r['jenis_mutasi'] == 'Setor' ? '#10b981' : '#f43f5e'; ?>; font-size: 0.95rem;">
                                                <?php echo $r['jenis_mutasi'] == 'Setor' ? '+' : '-'; ?> Rp <?php echo number_format($r['jumlah'], 0, ',', '.'); ?>
                                            </div>
                                        </td>
                                        <td style="padding: 12px 15px; font-size: 0.85rem; color: var(--z-muted);"><?php echo htmlspecialchars($r['keterangan']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

</div> <!-- /z-scroll -->

</div>