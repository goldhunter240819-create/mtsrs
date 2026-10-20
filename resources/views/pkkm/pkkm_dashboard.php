<div class="modern-page-header" style="background: linear-gradient(135deg, #059669, #047857); margin-bottom: 25px;">
    <div>
        <h1 class="mph-title" style="color: white;">
            <i data-lucide="award" style="color: rgba(255,255,255,0.85);"></i> Dashboard PKKM
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">Ringkasan kelengkapan instrumen, master berkas bukti fisik, dan penilaian simulasi PKKM.</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="/admin/pkkm/instrumen" class="btn btn-outline" style="background: rgba(255,255,255,0.15); color: #fff; border-color: rgba(255,255,255,0.3); font-weight: 600;">
            <i data-lucide="list-checks"></i> Instrumen 42 Indikator
        </a>
        <a href="/admin/pkkm/master-berkas" class="btn btn-outline" style="background: rgba(255,255,255,0.15); color: #fff; border-color: rgba(255,255,255,0.3); font-weight: 600;">
            <i data-lucide="folder-kanban"></i> Master Berkas
        </a>
        <a href="/pengawas/pkkm" target="_blank" class="btn btn-outline" style="background: rgba(255,255,255,0.15); color: #fff; border-color: rgba(255,255,255,0.3); font-weight: 600;">
            <i data-lucide="external-link"></i> Portal Penilai
        </a>
    </div>
</div>

<div class="stat-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="stat-box" style="padding: 20px; border-radius: 16px; display: flex; align-items: center; gap: 16px; background: linear-gradient(135deg, #ecfdf5, #f0fdf4); border: 1px solid #a7f3d0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <div style="width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.85); box-shadow: 0 2px 6px rgba(0,0,0,0.04); flex-shrink: 0;">
            <i data-lucide="layers" style="width:24px;height:24px; color:#059669;"></i>
        </div>
        <div>
            <div style="font-size: 1.85rem; font-weight: 800; line-height: 1.1; margin-bottom: 4px; color:#064e3b;"><?php echo $totalKomponen; ?></div>
            <div style="font-size: 0.78rem; font-weight: 700; color:#047857; text-transform: uppercase; letter-spacing: 0.5px;">Total Komponen</div>
        </div>
    </div>

    <div class="stat-box" style="padding: 20px; border-radius: 16px; display: flex; align-items: center; gap: 16px; background: linear-gradient(135deg, #eff6ff, #f8fafc); border: 1px solid #bfdbfe; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <div style="width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.85); box-shadow: 0 2px 6px rgba(0,0,0,0.04); flex-shrink: 0;">
            <i data-lucide="list-checks" style="width:24px;height:24px; color:#2563eb;"></i>
        </div>
        <div>
            <div style="font-size: 1.85rem; font-weight: 800; line-height: 1.1; margin-bottom: 4px; color:#1e3a8a;"><?php echo $totalIndikator; ?></div>
            <div style="font-size: 0.78rem; font-weight: 700; color:#1d4ed8; text-transform: uppercase; letter-spacing: 0.5px;">Total Indikator</div>
        </div>
    </div>

    <div class="stat-box" style="padding: 20px; border-radius: 16px; display: flex; align-items: center; gap: 16px; background: linear-gradient(135deg, #f0fdf4, #f8fafc); border: 1px solid #bbf7d0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <div style="width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.85); box-shadow: 0 2px 6px rgba(0,0,0,0.04); flex-shrink: 0;">
            <i data-lucide="folder-check" style="width:24px;height:24px; color:#16a34a;"></i>
        </div>
        <div>
            <div style="font-size: 1.85rem; font-weight: 800; line-height: 1.1; margin-bottom: 4px; color:#14532d;"><?php echo $totalBerkas; ?></div>
            <div style="font-size: 0.78rem; font-weight: 700; color:#15803d; text-transform: uppercase; letter-spacing: 0.5px;">Berkas Terlampir</div>
        </div>
    </div>

    <div class="stat-box" style="padding: 20px; border-radius: 16px; display: flex; align-items: center; gap: 16px; background: linear-gradient(135deg, #fefce8, #f8fafc); border: 1px solid #fef08a; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <div style="width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.85); box-shadow: 0 2px 6px rgba(0,0,0,0.04); flex-shrink: 0;">
            <i data-lucide="award" style="width:24px;height:24px; color:#d97706;"></i>
        </div>
        <div>
            <div style="font-size: 1.85rem; font-weight: 800; line-height: 1.1; margin-bottom: 4px; color:#713f12;"><?php echo $skorTotal; ?> <span style="font-size:1rem; opacity:0.6; font-weight:600;">/ <?php echo $skorMax; ?></span></div>
            <div style="font-size: 0.78rem; font-weight: 700; color:#b45309; text-transform: uppercase; letter-spacing: 0.5px;">Estimasi Skor (<?php echo $kategori; ?>)</div>
        </div>
    </div>
</div>

<div class="z-card" style="margin-bottom: 24px; padding: 24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <h3 style="margin:0; color:#1e293b; font-size:1.15rem; font-weight: 700; display:flex; align-items:center; gap:8px;">
            <i data-lucide="trending-up" style="width:18px; color:#059669;"></i> Progress Skor PKKM (Simulasi Penilaian)
        </h3>
        <span class="pill pill-green" style="font-size:0.8rem; font-weight:700;"><?php echo $persentase; ?>% Tercapai</span>
    </div>
    <p style="color:#64748b; font-size:0.88rem; margin-bottom:15px;">Persentase kelengkapan berdasarkan simulasi penilaian (Skor 1-4) yang diinputkan tim.</p>
    <div style="width: 100%; height: 26px; background: #e2e8f0; border-radius: 100px; overflow: hidden; position:relative;">
        <div style="height: 100%; background: linear-gradient(90deg, #059669, #10b981); display: flex; align-items: center; justify-content: flex-end; padding-right: 12px; color: white; font-weight: 800; font-size: 0.8rem; width: <?php echo max(5, $persentase); ?>%; transition: width 0.8s ease;">
            <?php echo $persentase; ?>%
        </div>
    </div>
</div>

<div class="z-card" style="padding: 24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
        <h3 style="margin:0; color:#1e293b; font-size:1.15rem; font-weight: 700; display:flex; align-items:center; gap:8px;">
            <i data-lucide="check-square" style="width:18px; color:#059669;"></i> Status Kelengkapan per Komponen
        </h3>
        <a href="/admin/pkkm/instrumen" class="btn btn-outline btn-sm">Lihat Selengkapnya &rarr;</a>
    </div>
    <div class="table-responsive">
        <table class="z-table">
            <thead>
                <tr>
                    <th width="6%" style="text-align:center;">No</th>
                    <th>Nama Komponen Penilaian</th>
                    <th width="15%" style="text-align:center;">Jumlah Indikator</th>
                    <th width="15%" style="text-align:center;">Berkas Terlampir</th>
                    <th width="15%" style="text-align:center;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($komponenStats as $idx => $k): ?>
                <tr>
                    <td style="text-align:center; color:#64748b; font-weight:600;"><?php echo $idx + 1; ?></td>
                    <td style="color:#1e293b; font-weight:700;">
                        <a href="/admin/pkkm/instrumen#komponen_<?php echo $k['id']; ?>" style="color:#0f172a; text-decoration:none;">
                            <?php echo htmlspecialchars($k['nama_komponen']); ?>
                        </a>
                    </td>
                    <td style="text-align:center; color:#475569; font-weight:600;"><?php echo $k['jml_indikator']; ?> Item</td>
                    <td style="text-align:center; color:#475569; font-weight:600;"><?php echo $k['jml_berkas']; ?> File</td>
                    <td style="text-align:center;">
                        <?php if ($k['jml_indikator'] > 0 && $k['jml_berkas'] >= $k['jml_indikator']): ?>
                            <span class="pill pill-green" style="font-weight:700;">Lengkap</span>
                        <?php else: ?>
                            <span class="pill pill-yellow" style="font-weight:700;">Proses</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

