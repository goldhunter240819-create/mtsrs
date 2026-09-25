<?php
use App\Core\Helper;
?>
<div class="apk-vector-header" style="padding-bottom: 30px;">
    <div class="apk-top-logo">
        <img src="<?= htmlspecialchars($faviconSrc) ?>" alt="Logo">
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Info Keuangan</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Data Pembayaran</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -25px; border-radius: 25px 25px 0 0;">
    
    <div style="background: linear-gradient(135deg, #fecaca, #f87171); padding: 20px; border-radius: 20px; color: #fff; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(248,113,113,0.3); display: flex; align-items: center; justify-content: space-between;">
        <div>
            <div style="font-size: 0.85rem; font-weight: 600; opacity: 0.9; margin-bottom: 4px;">Total Tunggakan</div>
            <div style="font-size: 1.5rem; font-weight: 800;">Rp <?= number_format($total_tunggakan, 0, ',', '.') ?></div>
        </div>
        <div style="background: rgba(255,255,255,0.2); width: 45px; height: 45px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="wallet" style="width: 24px; height: 24px;"></i>
        </div>
    </div>

    <?php if (empty($tagihan_per_tp)): ?>
        <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1; margin-bottom: 25px;">
            <i data-lucide="check-circle" style="width: 48px; height: 48px; color: #10b981; margin-bottom: 15px;"></i>
            <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 600;">Hebat! Belum ada data tagihan.</p>
        </div>
    <?php else: ?>
        <?php foreach ($tagihan_per_tp as $tp_name => $tagihans): ?>
            <h3 style="font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 12px; margin-top: 0; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="calendar" style="width: 16px;"></i> TP: <?= htmlspecialchars($tp_name) ?>
            </h3>
            <div style="background: #fff; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 25px; overflow: hidden;">
                <?php foreach ($tagihans as $idx => $t): 
                    $is_lunas = $t['status'] == 'Lunas';
                    $sisa = $t['nominal'] - $t['terbayar'];
                    $border = ($idx < count($tagihans) - 1) ? 'border-bottom: 1px dashed #e2e8f0;' : '';
                ?>
                    <div style="padding: 15px; <?= $border ?> display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem; margin-bottom: 4px;"><?= htmlspecialchars($t['nama_tagihan']) ?></div>
                            <div style="font-size: 1.05rem; font-weight: 800; color: <?= $is_lunas ? '#10b981' : '#f59e0b' ?>; margin-bottom: 4px;">
                                Rp <?= number_format($is_lunas ? $t['nominal'] : $sisa, 0, ',', '.') ?>
                            </div>
                            <div style="color: #94a3b8; font-size: 0.75rem;">Bulan: <?= htmlspecialchars($t['bulan']) ?></div>
                        </div>
                        <div style="text-align: right;">
                            <?php if($is_lunas): ?>
                                <span style="background:#dcfce7; color:#166534; font-size:0.75rem; padding:6px 12px; border-radius:8px; font-weight:700; display: inline-block;">Lunas</span>
                            <?php elseif($t['status'] == 'Sebagian' || ($t['terbayar'] > 0 && $t['terbayar'] < $t['nominal'])): ?>
                                <span style="background:#fef3c7; color:#d97706; font-size:0.75rem; padding:6px 12px; border-radius:8px; font-weight:700; display: inline-block;">Mengangsur</span>
                            <?php else: ?>
                                <span style="background:#fee2e2; color:#b91c1c; font-size:0.75rem; padding:6px 12px; border-radius:8px; font-weight:700; display: inline-block;">Belum Lunas</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <h3 style="font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="history" style="width: 16px;"></i> Riwayat Pembayaran (10 Terakhir)
    </h3>
    
    <div style="background: #fff; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px; overflow: hidden;">
        <?php if (count($riwayat_pembayaran) > 0): ?>
            <?php foreach ($riwayat_pembayaran as $idx => $r): 
                $border = ($idx < count($riwayat_pembayaran) - 1) ? 'border-bottom: 1px dashed #e2e8f0;' : '';
            ?>
                <div style="padding: 15px; <?= $border ?> display: flex; gap: 12px; align-items: flex-start;">
                    <div style="background: #ecfdf5; color: #10b981; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="receipt" style="width: 20px;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem; margin-bottom: 4px;"><?= htmlspecialchars($r['nama_komponen'] ?: 'Pembayaran') ?></div>
                        <div style="font-size: 1.05rem; font-weight: 800; color: #10b981; margin-bottom: 4px;">
                            Rp <?= number_format($r['nominal'], 0, ',', '.') ?>
                        </div>
                        <div style="color: #94a3b8; font-size: 0.75rem;">Tanggal: <?= htmlspecialchars(date('d M Y, H:i', strtotime($r['tanggal_bayar']))) ?></div>
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                        <span style="background:#dcfce7; color:#166534; font-size:0.7rem; padding:4px 8px; border-radius:6px; font-weight:700;">Berhasil</span>
                        <a href="<?= Helper::url('/keuangan/kwitansi/' . $r['id']) ?>" style="font-size: 0.75rem; color: #3b82f6; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; background: #eff6ff; padding: 4px 8px; border-radius: 6px;">
                            <i data-lucide="file-text" style="width: 14px;"></i> Struk
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; color: #94a3b8; font-size: 0.85rem; padding: 25px; font-weight: 600;">Belum ada riwayat pembayaran.</div>
        <?php endif; ?>
    </div>
</div>
