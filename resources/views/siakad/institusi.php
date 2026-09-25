<?php use App\Core\Helper; ?>

<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="building-2" style="color: #bfdbfe;"></i> Profil Institusi Madrasah
        </h1>
        <p class="mph-subtitle">Kelola identitas resmi madrasah MTs Roudlotus Sholihin dan konfigurasi sistem aktif.</p>
    </div>
    <div class="mph-actions">
        <button type="button" class="btn" onclick="document.getElementById('modal-manage-years').style.display='flex'">
            <i data-lucide="calendar"></i> Tahun Pelajaran
        </button>
        <button type="submit" form="form-institusi" class="btn btn-primary-white">
            <i data-lucide="save"></i> Simpan Perubahan
        </button>
    </div>
</div>

<form id="form-institusi" method="POST" action="<?php echo Helper::url('/siakad/institusi/save'); ?>" enctype="multipart/form-data">
    <div style="display:grid; grid-template-columns: 300px 1fr; gap: 1.5rem;">
        
        <!-- Left Column: Logo & System Config Card -->
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            
            <!-- Logo Card -->
            <div class="z-panel" style="margin:0; text-align:center;">
                <div class="z-panel-body">
                    <div style="width:160px; height:160px; margin:0 auto 1.25rem; border-radius:24px; background:#f8fafc; border:2px dashed #cbd5e1; position:relative; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                        <?php 
                        $logoUrl = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
                        ?>
                        <img src="<?php echo $logoUrl; ?>" id="logo-preview" alt="Logo MTs RS" style="width:100%; height:100%; object-fit:contain; padding:10px;">
                    </div>
                    <label for="logo-input" class="btn btn-outline" style="width:100%; justify-content:center;">
                        <i data-lucide="camera"></i> Ganti Logo
                    </label>
                    <input type="file" name="logo" id="logo-input" hidden onchange="previewLogo(this)" accept="image/*">
                    <p style="font-size:0.75rem; color:var(--z-muted); margin-top:8px;">Format: PNG / JPG (Maks 2MB)</p>
                </div>
            </div>

            <!-- Config Card -->
            <div class="z-panel" style="margin:0; background:var(--z-primary-gradient); color:white; border:none;">
                <div class="z-panel-body">
                    <div style="font-size:0.8rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between;">
                        <span style="display:flex; align-items:center; gap:8px;"><i data-lucide="settings-2"></i> Konfigurasi Aktif</span>
                    </div>

                    <div class="z-form-group">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                            <label style="font-size:0.75rem; font-weight:700; color:rgba(255,255,255,0.85);">Tahun Pelajaran Aktif</label>
                        </div>
                        <select name="tahun_ajaran_id" style="width:100%; padding:0.75rem; border-radius:12px; border:1px solid rgba(255,255,255,0.2); background:rgba(255,255,255,0.15); color:white; font-weight:700; outline:none;">
                            <?php foreach($years as $y): ?>
                                <option value="<?php echo $y['id']; ?>" style="color:#0f172a;" <?php echo ($institusi['tahun_ajaran_id'] ?? 1) == $y['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($y['name']); ?> <?php echo $y['is_active'] ? ' - [ AKTIF ]' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="z-form-group">
                        <label style="font-size:0.75rem; font-weight:700; color:rgba(255,255,255,0.85); display:block; margin-bottom:4px;">Semester Aktif</label>
                        <select name="semester" style="width:100%; padding:0.75rem; border-radius:12px; border:1px solid rgba(255,255,255,0.2); background:rgba(255,255,255,0.15); color:white; font-weight:700; outline:none;">
                            <option value="Ganjil" style="color:#0f172a;" <?php echo ($institusi['semester'] ?? 'Ganjil') === 'Ganjil' ? 'selected' : ''; ?>>Ganjil</option>
                            <option value="Genap" style="color:#0f172a;" <?php echo ($institusi['semester'] ?? 'Ganjil') === 'Genap' ? 'selected' : ''; ?>>Genap</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Form Sections -->
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            
            <!-- Identitas Section -->
            <div class="z-panel" style="margin:0;">
                <div class="z-panel-head">
                    <div class="z-panel-title"><i data-lucide="badge-check"></i> Identitas Resmi Madrasah</div>
                </div>
                <div class="z-panel-body z-grid2">
                    <div class="z-form-group" style="grid-column: span 2;">
                        <label class="z-label">Nama Resmi Institusi</label>
                        <input type="text" name="nama" class="z-field" value="<?php echo htmlspecialchars($institusi['nama']); ?>" required>
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Singkatan / Inisial</label>
                        <input type="text" name="singkatan" class="z-field" value="<?php echo htmlspecialchars($institusi['singkatan']); ?>" required>
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">NPSN</label>
                        <input type="text" name="npsn" class="z-field" value="<?php echo htmlspecialchars($institusi['npsn'] ?? '20580001'); ?>" placeholder="Nomor Pokok Sekolah Nasional">
                    </div>
                    <div class="z-form-group" style="grid-column: span 2;">
                        <label class="z-label">NSM (Nomor Statistik Madrasah)</label>
                        <input type="text" name="nsm" class="z-field" value="<?php echo htmlspecialchars($institusi['nsm'] ?? '121235100001'); ?>">
                    </div>
                </div>
            </div>

            <!-- Lokasi Section -->
            <div class="z-panel" style="margin:0;">
                <div class="z-panel-head">
                    <div class="z-panel-title"><i data-lucide="map-pin"></i> Lokasi & Alamat Lengkap</div>
                </div>
                <div class="z-panel-body z-grid2">
                    <div class="z-form-group" style="grid-column: span 2;">
                        <label class="z-label">Alamat Jalan / Gedung</label>
                        <textarea name="alamat" class="z-field" rows="2"><?php echo htmlspecialchars($institusi['alamat'] ?? ''); ?></textarea>
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Desa / Kelurahan</label>
                        <input type="text" name="desa" class="z-field" value="<?php echo htmlspecialchars($institusi['desa'] ?? ''); ?>">
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Kecamatan</label>
                        <input type="text" name="kecamatan" class="z-field" value="<?php echo htmlspecialchars($institusi['kecamatan'] ?? ''); ?>">
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Kota / Kabupaten</label>
                        <input type="text" name="kota" class="z-field" value="<?php echo htmlspecialchars($institusi['kota'] ?? ($institusi['kabupaten'] ?? 'Banyuwangi')); ?>">
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Provinsi</label>
                        <input type="text" name="provinsi" class="z-field" value="<?php echo htmlspecialchars($institusi['provinsi'] ?? 'Jawa Timur'); ?>">
                    </div>
                    <div class="z-form-group" style="grid-column: span 2;">
                        <label class="z-label">Kode Pos</label>
                        <input type="text" name="kodepos" class="z-field" value="<?php echo htmlspecialchars($institusi['kodepos'] ?? '68461'); ?>">
                    </div>
                </div>
            </div>

            <!-- Kontak & Pimpinan Section -->
            <div class="z-panel" style="margin:0;">
                <div class="z-panel-head">
                    <div class="z-panel-title"><i data-lucide="user-cog"></i> Kontak & Kepala Madrasah</div>
                </div>
                <div class="z-panel-body z-grid2">
                    <div class="z-form-group">
                        <label class="z-label">Nomor Telepon / WA</label>
                        <input type="text" name="telepon" class="z-field" value="<?php echo htmlspecialchars($institusi['telepon'] ?? ''); ?>">
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Email Resmi</label>
                        <input type="email" name="email" class="z-field" value="<?php echo htmlspecialchars($institusi['email'] ?? ''); ?>">
                    </div>
                    <div class="z-form-group" style="grid-column: span 2;">
                        <label class="z-label">Website Resmi</label>
                        <input type="text" name="website" class="z-field" value="<?php echo htmlspecialchars($institusi['website'] ?? 'https://mtsrs.sch.id'); ?>">
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Nama Kepala Madrasah</label>
                        <input type="text" name="nama_kepala" class="z-field" value="<?php echo htmlspecialchars($institusi['nama_kepala'] ?? ''); ?>">
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">NIP / NIY Kepala</label>
                        <input type="text" name="nip_kepala" class="z-field" value="<?php echo htmlspecialchars($institusi['nip_kepala'] ?? ''); ?>">
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

<!-- Modal Manage Academic Years -->
<div id="modal-manage-years" class="z-modal-ov" style="display:none;">
    <div class="z-modal">
        <div class="z-modal-head">
            <div class="z-modal-title"><i data-lucide="calendar"></i> Kelola Tahun Pelajaran</div>
            <div class="z-modal-close" onclick="document.getElementById('modal-manage-years').style.display='none'"><i data-lucide="x"></i></div>
        </div>
        <div class="z-modal-body">
            <form action="<?php echo Helper::url('/siakad/tahun-ajaran/add'); ?>" method="POST" style="display:flex; gap:10px; margin-bottom:1.25rem;">
                <input type="text" name="name" placeholder="Contoh: 2026/2027" class="z-field" required style="flex:1;">
                <button type="submit" class="btn btn-primary" style="white-space:nowrap;"><i data-lucide="plus"></i> Tambah</button>
            </form>

            <div class="z-table-wrap" style="max-height: 250px; border:1px solid #e2e8f0; border-radius:10px; overflow-y:auto;">
                <table>
                    <tbody>
                        <?php foreach($years as $y): ?>
                        <tr>
                            <td style="font-weight:700; color:var(--z-text);"><?php echo htmlspecialchars($y['name']); ?></td>
                            <td style="text-align:right;">
                                <?php if($y['is_active']): ?>
                                    <span class="pill pill-green">[ AKTIF ]</span>
                                <?php else: ?>
                                    <a href="<?php echo Helper::url('/siakad/tahun-ajaran/activate/' . $y['id']); ?>" class="btn btn-outline btn-sm">Jadikan Aktif</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('logo-preview');
            if (preview) {
                preview.src = e.target.result;
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
