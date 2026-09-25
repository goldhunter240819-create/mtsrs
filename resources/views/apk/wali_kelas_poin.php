<div class="apk-header" style="background: transparent; padding-top: 30px; position: absolute; width: 100%; top: 0; z-index: 100;">
    <a href="/apk/aplikasi-saya" style="color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 700; background: rgba(0,0,0,0.2); padding: 8px 15px; border-radius: 20px; backdrop-filter: blur(5px);">
        <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Kembali
    </a>
</div>

<div class="apk-vector-header" style="padding-bottom: 25px; padding-top: 90px; background-color: #ef4444;">
    <div class="apk-top-logo">
        <div style="width: 45px; height: 45px; border-radius: 12px; background: #fff; color: #ef4444; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <i data-lucide="alert-circle"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Wali Kelas</div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Log Kedisiplinan</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; min-height: 70vh; padding: 20px;">
    <!-- Tabs -->
    <div style="display: flex; gap: 10px; margin-bottom: 20px; background: #f1f5f9; padding: 5px; border-radius: 12px;">
        <button id="btnTabLog" onclick="switchTab('log')" style="flex: 1; padding: 10px; border: none; border-radius: 8px; font-weight: 800; font-size: 0.9rem; background: #fff; color: #ef4444; box-shadow: 0 2px 5px rgba(0,0,0,0.05); transition: 0.2s;">Riwayat Log</button>
        <button id="btnTabTop" onclick="switchTab('top')" style="flex: 1; padding: 10px; border: none; border-radius: 8px; font-weight: 800; font-size: 0.9rem; background: transparent; color: #64748b; transition: 0.2s;">Top Anak Wali</button>
    </div>

    <!-- Tab Log -->
    <div id="tabLog">
        <?php if (empty($poinList)): ?>
            <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 15px; color: #cbd5e1;"></i>
                <h4 style="margin: 0 0 10px 0;">Belum Ada Data</h4>
                <p style="margin: 0; font-size: 0.9rem;">Belum ada catatan kedisiplinan untuk kelas Anda.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <?php foreach ($poinList as $p): ?>
                    <div style="background: #fff; padding: 15px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                            <div>
                                <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 3px;">
                                    <?= date('d M Y', strtotime($p['tanggal'])) ?>
                                </div>
                                <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: #1e293b;">
                                    <?= htmlspecialchars($p['nama_siswa'] ?? 'Siswa Tidak Diketahui') ?>
                                </h4>
                            </div>
                            <div style="background: #fef2f2; padding: 5px 10px; border-radius: 8px; font-weight: 800; font-size: 0.9rem; color: #ef4444; border: 1px solid rgba(239,68,68,0.2); display: flex; align-items: center; gap: 5px;">
                                <i data-lucide="alert-triangle" style="width: 14px; height: 14px;"></i> <?= htmlspecialchars($p['poin']) ?>
                            </div>
                        </div>
                        <div style="font-size: 0.85rem; color: #475569; margin-bottom: 5px;">
                            <strong><?= htmlspecialchars($p['nama_kategori'] ?? 'Kategori') ?></strong> <?= !empty($p['keterangan']) ? ' - '.htmlspecialchars($p['keterangan']) : '' ?>
                        </div>
                        <div style="display: flex; align-items: center; gap: 5px; font-size: 0.75rem; color: #94a3b8; font-weight: 600;">
                            <i data-lucide="user" style="width: 12px; height: 12px;"></i> Pelapor: <?= htmlspecialchars($p['nama_guru'] ?? '-') ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tab Top Siswa -->
    <div id="tabTop" style="display: none;">
        <?php if (empty($topSiswa)): ?>
            <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                <i data-lucide="award" style="width: 48px; height: 48px; margin-bottom: 15px; color: #cbd5e1;"></i>
                <h4 style="margin: 0 0 10px 0;">Anak Wali Disiplin</h4>
                <p style="margin: 0; font-size: 0.9rem;">Belum ada akumulasi poin tercatat di kelas ini.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <?php $rank = 1; foreach ($topSiswa as $ts): ?>
                    <?php 
                        $total = intval($ts['total_poin']);
                        if ($total > 50) {
                            $bgColor = '#fef2f2'; $textColor = '#ef4444'; $borderColor = 'rgba(239,68,68,0.2)'; $badgeName = 'Bahaya';
                        } elseif ($total >= 30) {
                            $bgColor = '#fff7ed'; $textColor = '#f97316'; $borderColor = 'rgba(249,115,22,0.2)'; $badgeName = 'Peringatan';
                        } else {
                            $bgColor = '#fefce8'; $textColor = '#eab308'; $borderColor = 'rgba(234,179,8,0.2)'; $badgeName = 'Waspada';
                        }
                    ?>
                    <div style="background: <?= $bgColor ?>; padding: 15px; border-radius: 16px; border: 1px solid <?= $borderColor ?>; display: flex; align-items: center; gap: 15px;">
                        <div style="width: 35px; height: 35px; border-radius: 50%; background: <?= $textColor ?>; color: #fff; font-weight: 800; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                            <?= $rank++ ?>
                        </div>
                        <div style="flex: 1;">
                            <h4 style="margin: 0 0 3px 0; font-size: 1.05rem; font-weight: 800; color: #1e293b;"><?= htmlspecialchars($ts['nama_siswa']) ?></h4>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 1.5rem; font-weight: 900; color: <?= $textColor ?>; line-height: 1;"><?= $total ?></div>
                            <div style="font-size: 0.65rem; font-weight: 700; color: <?= $textColor ?>; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 3px;"><?= $badgeName ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function switchTab(tab) {
    document.getElementById('tabLog').style.display = tab === 'log' ? 'block' : 'none';
    document.getElementById('tabTop').style.display = tab === 'top' ? 'block' : 'none';
    
    document.getElementById('btnTabLog').style.background = tab === 'log' ? '#fff' : 'transparent';
    document.getElementById('btnTabLog').style.color = tab === 'log' ? '#ef4444' : '#64748b';
    document.getElementById('btnTabLog').style.boxShadow = tab === 'log' ? '0 2px 5px rgba(0,0,0,0.05)' : 'none';
    
    document.getElementById('btnTabTop').style.background = tab === 'top' ? '#fff' : 'transparent';
    document.getElementById('btnTabTop').style.color = tab === 'top' ? '#ef4444' : '#64748b';
    document.getElementById('btnTabTop').style.boxShadow = tab === 'top' ? '0 2px 5px rgba(0,0,0,0.05)' : 'none';
}
</script>
