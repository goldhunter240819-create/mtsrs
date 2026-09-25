<div class="z-content-pad">


                    <?php else: ?>
                        <table id="tableMutasi" style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--z-muted);">
                                <tr>
                                    <th style="padding: 12px 15px;">Waktu</th>
                                    <th style="padding: 12px 15px;">Siswa</th>
                                    <th style="padding: 12px 15px;">Kelas</th>
                                    <th style="padding: 12px 15px;">Jenis Transaksi</th>
                                    <th style="padding: 12px 15px; text-align: right;">Jumlah (Rp)</th>
                                    <th style="padding: 12px 15px; text-align: right;">Saldo Setelahnya</th>
                                    <th style="padding: 12px 15px;">Keterangan</th>
                                    <th style="padding: 12px 15px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($riwayat as $r): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px 15px; white-space: nowrap;">
                                        <div style="font-weight: 700; color: #0f172a; font-size: 0.85rem;"><?php echo date('d M Y', strtotime($r['tanggal'])); ?></div>
                                        <div style="font-size: 0.75rem; color: var(--z-muted);"><?php echo date('H:i', strtotime($r['tanggal'])); ?></div>
                                    </td>
                                    <td style="padding: 12px 15px;">
                                        <div style="font-weight: 700; color: #0f172a; font-size: 0.9rem;"><?php echo htmlspecialchars($r['nama_siswa']); ?></div>
                                        <div style="font-size: 0.75rem; color: var(--z-muted);">NIS: <?php echo htmlspecialchars($r['nis']); ?></div>
                                    </td>
                                    <td style="padding: 12px 15px;"><span style="display:inline-block; padding: 4px 10px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;"><?php echo htmlspecialchars($r['nama_kelas'] ? $r['nama_kelas'] : '-'); ?></span></td>
                                    <td style="padding: 12px 15px;">
                                        <?php if ($r['jenis_mutasi'] == 'Setor'): ?>
                                            <span style="display:inline-flex; align-items:center; gap:4px; padding:4px 8px; background:#ecfdf5; color:#10b981; border-radius:6px; font-size:0.75rem; font-weight:700;"><i data-lucide="arrow-down-to-line" style="width:12px; height:12px;"></i> Setor Tunai</span>
                                        <?php else: ?>
                                            <span style="display:inline-flex; align-items:center; gap:4px; padding:4px 8px; background:#fff1f2; color:#f43f5e; border-radius:6px; font-size:0.75rem; font-weight:700;"><i data-lucide="arrow-up-from-line" style="width:12px; height:12px;"></i> Tarik Tunai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px 15px; text-align: right; font-weight: 800; font-size: 1.05rem; color: <?php echo $r['jenis_mutasi'] == 'Setor' ? '#10b981' : '#f43f5e'; ?>;">
                                        <?php echo $r['jenis_mutasi'] == 'Setor' ? '+' : '-'; ?> <?php echo number_format($r['jumlah'], 0, ',', '.'); ?>
                                    </td>
                                    <td style="padding: 12px 15px; text-align: right; font-weight: 800; color: #0f172a; font-size: 1rem;">
                                        <?php echo number_format($r['saldo_sesudah'], 0, ',', '.'); ?>
                                    </td>
                                    <td style="padding: 12px 15px; color: var(--z-muted); font-size: 0.85rem;">
                                        <?php echo htmlspecialchars($r['keterangan'] ? $r['keterangan'] : '-'); ?>
                                    </td>
                                    <td style="padding: 12px 15px; text-align: center;">
                                        <a href="/admin/tabungan/cetak?id=<?php echo $r['id']; ?>" target="_blank" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.75rem; padding: 4px 8px;">
                                            <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Cetak
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

</div> <!-- /z-scroll -->

</div>