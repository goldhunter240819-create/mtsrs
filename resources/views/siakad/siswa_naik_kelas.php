<?php use App\Core\Helper; ?>
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="arrow-up-right" style="color: #bfdbfe;"></i> Proses Kenaikan Kelas Siswa
        </h1>
        <p class="mph-subtitle">Pindahkan rombel siswa secara massal untuk pergantian tahun ajaran baru.</p>
    </div>
</div>

<div class="z-panel">
    <div class="z-panel-head">
        <form method="GET" action="<?php echo Helper::url('/siakad/siswa/naik-kelas'); ?>" style="display:flex; align-items:center; gap:12px;">
            <div class="z-form-group" style="margin:0;">
                <select name="kelas_asal" class="z-input" style="min-width: 200px;" onchange="this.form.submit()">
                    <option value="">Pilih Kelas Asal...</option>
                    <?php foreach($kelasList as $k): ?>
                        <option value="<?php echo $k['id']; ?>" <?php echo $kelasAsalId == $k['id'] ? 'selected' : ''; ?>>
                            Kelas Asal: <?php echo htmlspecialchars($k['nama_kelas']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <div class="z-panel-body" style="padding:0;">
        <form method="POST" action="<?php echo Helper::url('/siakad/siswa/naik-kelas/process'); ?>">
            <div style="display:flex; align-items:center; gap:12px; padding: 1.25rem; background: var(--z-primary-light); border-bottom: 1px solid var(--z-border);">
                <div style="font-weight:800; color: var(--z-primary);">Target Kelas Tujuan:</div>
                <select name="kelas_tujuan" class="z-input" style="max-width: 250px;" required>
                    <option value="">-- Pilih Kelas Tujuan --</option>
                    <?php foreach($kelasList as $k): ?>
                        <option value="<?php echo $k['id']; ?>">Pindah ke Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary" onclick="return confirm('Yakin ingin memindahkan siswa yang dipilih?')">
                    <i data-lucide="arrow-right-left"></i> Proses Naik Kelas Massal
                </button>
            </div>

            <div class="z-table-wrap" style="border:none; border-radius:0;">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;"><input type="checkbox" onclick="toggleSelectAll(this)" style="cursor:pointer; width:16px; height:16px;"></th>
                            <th style="width: 150px;">NIS</th>
                            <th>Nama Siswa</th>
                            <th style="width: 150px;">Jenis Kelamin</th>
                            <th style="width: 150px;">Kelas Asal Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($siswaList)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; color: var(--z-muted); padding: 2rem;">Tidak ada siswa aktif di kelas asal ini atau belum memilih kelas asal.</td>
                        </tr>
                        <?php endif; ?>
                        <?php foreach($siswaList as $s): ?>
                        <tr>
                            <td><input type="checkbox" name="siswa_ids[]" value="<?php echo $s['id']; ?>" class="cb-siswa" style="cursor:pointer; width:16px; height:16px;"></td>
                            <td style="font-weight:800; color: var(--z-primary);"><?php echo htmlspecialchars($s['nis']); ?></td>
                            <td style="font-weight:700; color:var(--z-text);"><?php echo htmlspecialchars($s['nama']); ?></td>
                            <td><?php echo $s['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                            <td><span class="pill pill-blue">Kelas <?php echo htmlspecialchars($s['nama_kelas']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleSelectAll(master) {
        var cbs = document.querySelectorAll('.cb-siswa');
        cbs.forEach(function(cb) { cb.checked = master.checked; });
    }
</script>
