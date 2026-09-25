<div class="z-content-pad">


                <div class="summary-card" style="border-left: 4px solid #10b981;">
                    <div class="label"><i data-lucide="arrow-down-to-line" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;"></i> Total Setor</div>
                    <div class="value" style="color: #10b981;">Rp <?php echo number_format($g_setor, 0, ',', '.'); ?></div>
                </div>
                <div class="summary-card" style="border-left: 4px solid #f43f5e;">
                    <div class="label"><i data-lucide="arrow-up-from-line" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;"></i> Total Tarik</div>
                    <div class="value" style="color: #f43f5e;">Rp <?php echo number_format($g_tarik, 0, ',', '.'); ?></div>
                </div>
                <div class="summary-card" style="border-left: 4px solid #8b5cf6;">
                    <div class="label"><i data-lucide="wallet" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;"></i> Saldo Bersih</div>
                    <div class="value" style="color: #8b5cf6;">Rp <?php echo number_format($g_setor - $g_tarik, 0, ',', '.'); ?></div>
                </div>
            </div>

            <div class="z-panel">
                <div class="z-panel-head" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div class="z-panel-title"><i data-lucide="calendar-days"></i> Data Rekap Harian</div>
                    
                    <form method="GET" action="/admin/tabungan/rekap" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap; justify-content: flex-end;">
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <?php if (in_array($_SESSION['role_id'], [1, 99])): ?>
                            <select name="petugas_id" class="form-control" style="font-size: 0.85rem; padding: 6px 12px; border-radius: 6px; border: 1px solid #e2e8f0; min-width: 150px;">
                                <option value="0">Semua Petugas</option>
                                <?php foreach($petugasList as $p): ?>
                                    <option value="<?php echo $p['id']; ?>" <?php echo $petugas_id == $p['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['nama']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php endif; ?>
                            <select name="kelas_id" class="form-control" style="font-size: 0.85rem; padding: 6px 12px; border-radius: 6px; border: 1px solid #e2e8f0; width: 140px;">
                                <option value="0">Semua Kelas</option>
                                <?php foreach($kelasList as $k): ?>
                                    <option value="<?php echo $k['id']; ?>" <?php echo $kelas_id == $k['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="tampil_semua" class="form-control" style="font-size: 0.85rem; padding: 6px 12px; border-radius: 6px; border: 1px solid #e2e8f0; width: 150px;">
                                <option value="0" <?php echo (isset($_GET['tampil_semua']) && $_GET['tampil_semua'] == '0') ? 'selected' : ''; ?>>Yang Mutasi Saja</option>
                                <option value="1" <?php echo (isset($_GET['tampil_semua']) && $_GET['tampil_semua'] == '1') ? 'selected' : ''; ?>>Semua Siswa</option>
                            </select>
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="date" name="mulai" class="form-control" style="font-size: 0.85rem; padding: 6px 12px; border-radius: 6px; border: 1px solid #e2e8f0; width: 130px;" value="<?php echo htmlspecialchars($tanggal_mulai); ?>">
                            <span style="color: var(--z-muted); font-size: 0.85rem; font-weight: 600;">s/d</span>
                            <input type="date" name="akhir" class="form-control" style="font-size: 0.85rem; padding: 6px 12px; border-radius: 6px; border: 1px solid #e2e8f0; width: 130px;" value="<?php echo htmlspecialchars($tanggal_akhir); ?>">
                        </div>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <button type="submit" class="btn btn-primary btn-sm" style="padding: 6px 16px;"><i data-lucide="filter" style="width: 14px; height: 14px;"></i> Filter</button>
                            <a href="/admin/tabungan/rekap/cetak?<?php echo http_build_query($_GET); ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 6px 16px;"><i data-lucide="printer" style="width: 14px; height: 14px;"></i> Cetak Baris</a>
                            <a href="/admin/tabungan/rekap/cetak-matriks?<?php echo http_build_query($_GET); ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 6px 16px;"><i data-lucide="table" style="width: 14px; height: 14px;"></i> Cetak Matriks</a>
                            <a href="/admin/tabungan/rekap/cetak-cover?<?php echo http_build_query($_GET); ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 6px 16px;"><i data-lucide="layout-template" style="width: 14px; height: 14px;"></i> Cetak Cover</a>
                        </div>
                    </form>
                </div>

                <div class="z-panel-body" style="padding: 0; overflow-x: auto;">
                    <?php if (empty($rekap)): ?>
                        <div style="padding: 4rem 2rem; text-align: center; color: var(--z-muted);">
                            <i data-lucide="search-x" style="width: 48px; height: 48px; opacity: 0.3; margin-bottom: 15px; display: block; margin-left: auto; margin-right: auto;"></i>
                            <div style="font-weight: 700; font-size: 1.1rem; color: #334155;">Tidak ada rekap data</div>
                            <div style="font-size: 0.9rem; margin-top: 4px;">Tidak ditemukan mutasi pada rentang tanggal dan filter tersebut.</div>
                        </div>
                    <?php else: ?>
                        <table id="tableRekap" style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--z-muted);">
                                <tr>
                                    <th style="padding: 12px 15px;">Tanggal</th>
                                    <th style="padding: 12px 15px;">Petugas</th>
                                    <th style="padding: 12px 15px;">Siswa</th>
                                    <th style="padding: 12px 15px;">Kelas</th>
                                    <th style="padding: 12px 15px; text-align: right;">Setor (Rp)</th>
                                    <th style="padding: 12px 15px; text-align: right;">Tarik (Rp)</th>
                                    <th style="padding: 12px 15px; text-align: right;">Saldo Bersih (Rp)</th>
                                    <th style="padding: 12px 15px; text-align: center;">Jml Trx</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($rekap as $r): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px 15px; white-space: nowrap;">
                                        <div style="font-weight: 700; color: #0f172a; font-size: 0.9rem;"><?php echo date('d M Y', strtotime($r['tgl'])); ?></div>
                                    </td>
                                    <td style="padding: 12px 15px;">
                                        <div style="font-weight: 700; color: #0f172a; font-size: 0.9rem;"><?php echo htmlspecialchars($r['nama_petugas']); ?></div>
                                    </td>
                                    <td style="padding: 12px 15px;">
                                        <div style="font-weight: 700; color: #334155; font-size: 0.9rem;"><?php echo htmlspecialchars($r['nama_siswa']); ?></div>
                                    </td>
                                    <td style="padding: 12px 15px;">
                                        <div style="font-size: 0.85rem; color: #64748b;"><?php echo htmlspecialchars($r['nama_kelas']); ?></div>
                                    </td>
                                    <td style="padding: 12px 15px; text-align: right; font-weight: 700; color: #10b981; font-size: 0.95rem;">
                                        <?php echo number_format($r['total_setor'], 0, ',', '.'); ?>
                                    </td>
                                    <td style="padding: 12px 15px; text-align: right; font-weight: 700; color: #f43f5e; font-size: 0.95rem;">
                                        <?php echo number_format($r['total_tarik'], 0, ',', '.'); ?>
                                    </td>
                                    <td style="padding: 12px 15px; text-align: right; font-weight: 800; color: #0f172a; font-size: 0.95rem;">
                                        <?php echo number_format($r['total_setor'] - $r['total_tarik'], 0, ',', '.'); ?>
                                    </td>
                                    <td style="padding: 12px 15px; text-align: center; font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                        <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 100px;"><?php echo $r['jumlah_transaksi']; ?></span>
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