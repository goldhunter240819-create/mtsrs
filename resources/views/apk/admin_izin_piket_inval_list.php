        <?php if (empty($tugasInvalGrouped)): ?>
            <div style="text-align: center; padding: 40px 20px; background: white; border-radius: 16px; border: 1px solid #f1f5f9;">
                <div style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                    <i data-lucide="book-check" style="width: 28px; height: 28px; color: #94a3b8;"></i>
                </div>
                <div style="font-weight: 700; color: #475569; font-size: 1rem;">Tidak ada kelas kosong</div>
                <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 5px;">Semua kelas dilaporkan aman hari ini.</div>
            </div>
        <?php else: ?>
            <div style="padding-bottom: 20px;">
                <?php foreach ($tugasInvalGrouped as $guruId => $guruGroup): ?>
                    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 10px; overflow: hidden;">
                        <!-- Accordion Header -->
                        <div onclick="toggleInvalGuru(this)" style="padding: 12px 15px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; background: <?php echo ($guruGroup['sumber'] === 'Alpa') ? '#fee2e2' : '#dbeafe'; ?>; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i data-lucide="user-x" style="width: 18px; height: 18px; color: <?php echo ($guruGroup['sumber'] === 'Alpa') ? '#ef4444' : '#2563eb'; ?>;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a; line-height: 1.2;"><?php echo htmlspecialchars($guruGroup['nama_guru']); ?></div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                                        <span style="font-weight:600; color: <?php echo ($guruGroup['sumber'] === 'Alpa') ? '#ef4444' : '#2563eb'; ?>;"><?php echo $guruGroup['sumber']; ?></span> &bull; <?php echo count($guruGroup['tugas']); ?> Kelas Inval
                                    </div>
                                </div>
                            </div>
                            <i data-lucide="chevron-down" style="width: 18px; height: 18px; color: #64748b; transition: transform 0.3s;" class="inval-chevron"></i>
                        </div>

                        <!-- Accordion Body -->
                        <div class="inval-body" style="display: none; padding: 12px 15px; background: white;">
                            <?php foreach ($guruGroup['tugas'] as $ti): ?>
                                <div style="border-left: 3px solid <?php echo ($guruGroup['sumber'] === 'Alpa') ? '#ef4444' : '#2563eb'; ?>; padding-left: 12px; margin-bottom: 15px;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                        <div>
                                            <div style="font-weight: 800; font-size: 0.9rem; color: #0f172a;"><?php echo htmlspecialchars($ti['nama_kelas']); ?> &bull; <?php echo $ti['jam_mulai'] . ' - ' . $ti['jam_selesai']; ?></div>
                                            <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;"><?php echo htmlspecialchars($ti['nama_mapel']); ?></div>
                                        </div>
                                        <?php if (empty($ti['sudah_inval'])): ?>
                                            <?php if (isset($tanggal_inval) && $tanggal_inval < date('Y-m-d')): ?>
                                                <span style="font-size: 0.7rem; background: #f1f5f9; color: #64748b; padding: 4px 8px; border-radius: 6px; font-weight: 700; border: 1px solid #e2e8f0;"><i data-lucide="history" style="width:12px;height:12px;display:inline-block;vertical-align:middle;margin-right:3px;"></i> Riwayat (Terlewat)</span>
                                            <?php else: ?>
                                                <a href="<?php echo \App\Core\Helper::url('/apk/eksekusi-inval?id=' . $ti['pengajuan_id'] . '&kelas_id=' . $ti['kelas_id'] . '&mapel_id=' . $ti['mapel_id'] . '&tanggal=' . $ti['tanggal']); ?>" class="btn btn-primary btn-sm" style="display: flex; align-items: center; gap: 5px; padding: 4px 8px; font-size: 0.75rem; white-space: nowrap;">
                                                    <i data-lucide="play" style="width:12px;height:12px;"></i> Ambil
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="font-size: 0.7rem; background: #dcfce7; color: #16a34a; padding: 2px 6px; border-radius: 4px; font-weight: 700;">✓ Selesai <?php echo !empty($ti['petugas_inval']) ? '('.htmlspecialchars($ti['petugas_inval']).')' : ''; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($ti['materi'])): ?>
                                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 10px; font-size: 0.75rem; color: #475569; margin-top: 5px;">
                                            <strong style="color: #0f172a;">Tugas:</strong> <?php echo htmlspecialchars($ti['materi']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
