<div class="modern-page-header" style="background: linear-gradient(135deg, #0f172a, #334155);">
    <a href="<?php echo \App\Core\Helper::url('/evoting/admin'); ?>" style="color: rgba(255,255,255,0.8); display: inline-flex; align-items: center; gap: 5px; text-decoration: none; font-size: 13px; margin-bottom: 10px;">
        <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Kembali ke Dashboard
    </a>
    <div>
        <h1 class="mph-title" style="color: white;">
            <i data-lucide="bar-chart-2" style="color: rgba(255,255,255,0.8);"></i> Live Count Hasil Suara
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">Event: <strong><?= htmlspecialchars($eventData['nama_event']) ?></strong></p>
    </div>
</div>

<div class="z-card" style="margin-top: 2rem; padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Perolehan Suara Sementara</h2>
        <button onclick="window.location.reload()" class="btn btn-outline">
            <i data-lucide="refresh-cw" style="width: 18px; height: 18px;"></i> Refresh Data
        </button>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase;">Total Pemilih (DPT)</div>
            <div style="font-size: 32px; font-weight: 800; color: #0f172a; margin-top: 10px;"><?= $totalVoters ?></div>
        </div>
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #166534; font-weight: 600; text-transform: uppercase;">Suara Masuk</div>
            <div style="font-size: 32px; font-weight: 800; color: #15803d; margin-top: 10px;"><?= $totalVotes ?></div>
        </div>
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #991b1b; font-weight: 600; text-transform: uppercase;">Belum Memilih</div>
            <div style="font-size: 32px; font-weight: 800; color: #b91c1c; margin-top: 10px;"><?= $totalVoters - $totalVotes ?></div>
        </div>
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #1e40af; font-weight: 600; text-transform: uppercase;">Partisipasi</div>
            <div style="font-size: 32px; font-weight: 800; color: #1d4ed8; margin-top: 10px;">
                <?= $totalVoters > 0 ? round(($totalVotes / $totalVoters) * 100, 1) : 0 ?>%
            </div>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 20px;">
        <?php 
        $maxVotes = max(array_column($candidatesData, 'vote_count') ?: [0]);
        foreach($candidatesData as $c): 
            $percent = $totalVotes > 0 ? round(($c['vote_count'] / $totalVotes) * 100, 1) : 0;
            $barWidth = $maxVotes > 0 ? ($c['vote_count'] / $maxVotes) * 100 : 0;
        ?>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="width: 50px; height: 50px; border-radius: 50%; background: #e2e8f0; overflow: hidden; border: 2px solid #cbd5e1; flex-shrink: 0; position: relative;">
                <?php if(!empty($c['foto'])): ?>
                    <img src="<?= \App\Core\Helper::url('/public/uploads/evoting/'.$c['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8;"><i data-lucide="user"></i></div>
                <?php endif; ?>
                <div style="position: absolute; bottom: -2px; right: -2px; background: var(--z-primary); color: white; width: 20px; height: 20px; border-radius: 50%; font-size: 10px; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 1px solid white;">
                    <?= $c['no_urut'] ?>
                </div>
            </div>
            
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <strong style="font-size: 14px; color: #1e293b;"><?= htmlspecialchars($c['nama_kandidat']) ?></strong>
                    <div style="font-size: 14px; font-weight: 800; color: #0f172a;"><?= $c['vote_count'] ?> Suara (<?= $percent ?>%)</div>
                </div>
                <div style="height: 12px; background: #f1f5f9; border-radius: 6px; overflow: hidden; box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);">
                    <div style="height: 100%; background: linear-gradient(90deg, var(--z-primary), #60a5fa); width: <?= $barWidth ?>%; border-radius: 6px; transition: width 1s ease-in-out;"></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
