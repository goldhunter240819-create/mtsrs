<?php use App\Core\Helper; ?>
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="book-open" style="color: #bfdbfe;"></i> Penugasan Mengajar
        </h1>
        <p class="mph-subtitle">Atur guru pengampu mata pelajaran untuk setiap kelas.</p>
    </div>
</div>

<!-- Filter Kelas -->
<div style="background: #fff; padding: 0.75rem 1.25rem; border-radius: 14px; border: 1px solid #e2e8f0; margin-bottom: 1.25rem;">
    <form action="" method="GET" style="display: flex; align-items: center; justify-content: space-between; gap: 15px;">
        <div style="flex: 1; display: flex; align-items: center; gap: 12px;">
            <label style="color: #64748b; font-size: 0.82rem; font-weight: 700; white-space: nowrap;">Pilih Kelas:</label>
            <select name="kelas_id" onchange="this.form.submit()" style="background: #fff; border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 6px 12px; font-weight: 700; color: var(--z-primary); font-size: 0.85rem; outline: none; width: 240px; cursor: pointer;">
                <option value="">-- Pilih Kelas --</option>
                <?php foreach($kelasList as $k): ?>
                    <option value="<?php echo $k['id']; ?>" <?php echo $selected_kelas == $k['id'] ? 'selected' : ''; ?>>
                        KELAS <?php echo htmlspecialchars($k['nama_kelas']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if($selected_kelas): ?>
            <div>
                <span style="background: #ecfdf5; color: #059669; border: 1px solid #bbf7d0; padding: 4px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                    <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Mode Edit Aktif
                </span>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php if($selected_kelas): ?>
<form action="<?php echo Helper::url('/siakad/guru/mengajar/save'); ?>" method="POST">
    <input type="hidden" name="kelas_id" value="<?php echo $selected_kelas; ?>">
    
    <?php 
        // Group mapels by kategori
        $grouped_mapels = [];
        foreach($mapelList as $m) {
            $kat = $m['kelompok'] ?: 'Umum'; // using 'kelompok' from mtsrs mapel schema
            $grouped_mapels[$kat][] = $m;
        }
    ?>
    
    <?php foreach($grouped_mapels as $kategori => $maps): ?>
    <div style="background: #fff; padding: 0; overflow: hidden; border-radius: 14px; border: 1px solid #e2e8f0; margin-bottom: 1.25rem;">
        <div style="background: #f8fafc; padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px;">
            <div style="width: 32px; height: 32px; background: #e0f2fe; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #0284c7;">
                <i data-lucide="folder-open" style="width: 18px; height: 18px;"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo htmlspecialchars($kategori); ?></h3>
                <p style="margin: 0; font-size: 0.75rem; color: #64748b;">Kelompok Mata Pelajaran</p>
            </div>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #fff; text-align: left; border-bottom: 2px solid #f1f5f9;">
                    <th style="padding: 0.65rem 1rem; width: 150px; font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Kode Mapel</th>
                    <th style="padding: 0.65rem 1rem; font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Nama Mata Pelajaran</th>
                    <th style="padding: 0.65rem 1rem; width: 350px; font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Guru Pengampu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($maps as $m): ?>
                <tr style="border-bottom: 1px solid #f1f5f9; transition: 0.2s;">
                    <td style="padding: 0.65rem 1rem;">
                        <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; font-weight: 700; border: 1px solid #e2e8f0;">
                            <?php echo htmlspecialchars($m['kode_mapel'] ?? '-'); ?>
                        </span>
                    </td>
                    <td style="padding: 0.65rem 1rem; font-weight: 700; color: #1e293b; font-size: 0.88rem;">
                        <?php echo htmlspecialchars($m['nama_mapel']); ?>
                    </td>
                    <td style="padding: 0.65rem 1rem;">
                        <select name="guru[<?php echo $m['id']; ?>]" style="width: 100%; padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 0.85rem; font-weight: 500; background: #fff; outline: none; transition: 0.2s;">
                            <option value="">-- Pilih Guru --</option>
                            <?php foreach($guruList as $g): ?>
                                <option value="<?php echo $g['id']; ?>" <?php echo (isset($assignments[$m['id']]) && $assignments[$m['id']] == $g['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($g['nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endforeach; ?>
    
    <div style="padding: 1.25rem; background: #fff; text-align: right; border-radius: 14px; border: 1px solid #e2e8f0; margin-bottom: 2rem;">
        <button type="submit" style="padding: 12px 24px; font-size: 0.95rem; font-weight: 600; border-radius: 10px; border: none; cursor: pointer; color: white; background: #10b981; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3); transition: all 0.2s ease;">
            <i data-lucide="save" style="width: 18px; height: 18px; margin-right: 6px; vertical-align: -3px;"></i> SIMPAN PENUGASAN
        </button>
    </div>
</form>
<?php else: ?>
    <div style="text-align: center; padding: 3.5rem 2rem; background: #fff; border-radius: 14px; border: 1px solid #e2e8f0;">
        <i data-lucide="mouse-pointer-2" style="width: 36px; height: 36px; color: #94a3b8; margin-bottom: 0.75rem;"></i>
        <h3 style="color: #64748b; font-size: 1.05rem; font-weight: 700; margin: 0 0 4px 0;">Silakan pilih kelas terlebih dahulu</h3>
        <p style="color: #94a3b8; font-size: 0.8rem; margin: 0;">Data mata pelajaran akan muncul setelah kelas dipilih.</p>
    </div>
<?php endif; ?>
