<?php use App\Core\Helper; ?>
<div class="modern-page-header" style="margin-bottom: 20px;">
    <div>
        <h1 class="mph-title">
            <i data-lucide="file-text" style="color: #bfdbfe;"></i> Pengaturan Kop Surat
        </h1>
        <p class="mph-subtitle">Sesuaikan tampilan Kop Surat untuk cetak laporan sekolah.</p>
    </div>
</div>
<div class="z-container" style="padding-top: 0;">

    <?php if(isset($_SESSION['flash_message'])): ?>
        <div class="z-alert z-alert-success" style="margin-bottom: 20px;">
            <?php echo $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
        </div>
    <?php endif; ?>

    <div style="display: flex; flex-direction: column; gap: 30px;">
        <!-- FORM PENGATURAN -->
        <div class="z-card" style="padding: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--z-border); padding-bottom: 10px; margin-bottom: 20px;">
                <h3 style="margin: 0;">Pengaturan Data Kop Surat</h3>
                <button type="button" class="btn btn-outline" onclick="toggleForm()" id="btnToggleForm">
                    <i data-lucide="edit"></i> Edit Form
                </button>
            </div>
            
            <div id="formContainer" style="display: none;">
                <form action="/siakad/kop-surat" method="POST">
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
                    <div class="z-form-group">
                        <label class="z-label">Nama Yayasan</label>
                        <input type="text" name="yayasan" id="inputYayasan" class="z-field" value="<?php echo htmlspecialchars($institusi['yayasan'] ?? ''); ?>" required placeholder="Contoh: YAYASAN PENDIDIKAN ISLAM ROUDLOTUS SHOLIHIN" onkeyup="updatePreview()">
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Ukuran Font Yayasan</label>
                        <input type="number" name="kop_font_yayasan" id="inputFontYayasan" class="z-field" value="<?php echo intval($institusi['kop_font_yayasan'] ?? 14); ?>" onkeyup="updatePreview()" onchange="updatePreview()">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
                    <div class="z-form-group">
                        <label class="z-label">Nama Madrasah / Institusi</label>
                        <input type="text" name="nama" id="inputNama" class="z-field" value="<?php echo htmlspecialchars($institusi['nama'] ?? ''); ?>" required placeholder="Contoh: MTs ROUDLOTUS SHOLIHIN" onkeyup="updatePreview()">
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Ukuran Font Madrasah</label>
                        <input type="number" name="kop_font_nama" id="inputFontNama" class="z-field" value="<?php echo intval($institusi['kop_font_nama'] ?? 18); ?>" onkeyup="updatePreview()" onchange="updatePreview()">
                    </div>
                </div>
                
                <div class="z-form-group">
                    <label class="z-label">Alamat Jalan</label>
                    <textarea name="alamat" id="inputAlamat" class="z-field" rows="2" required placeholder="Contoh: Jl. Raya Gunung Terang No. 09" onkeyup="updatePreview()"><?php echo htmlspecialchars($institusi['alamat'] ?? ''); ?></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="z-form-group">
                        <label class="z-label">Desa / Kelurahan</label>
                        <input type="text" name="desa" id="inputDesa" class="z-field" value="<?php echo htmlspecialchars($institusi['desa'] ?? ''); ?>" placeholder="Contoh: Gunung Terang" onkeyup="updatePreview()">
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Kecamatan</label>
                        <input type="text" name="kecamatan" id="inputKecamatan" class="z-field" value="<?php echo htmlspecialchars($institusi['kecamatan'] ?? ''); ?>" placeholder="Contoh: Air Hitam" onkeyup="updatePreview()">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                    <div class="z-form-group">
                        <label class="z-label">Kabupaten / Kota</label>
                        <input type="text" name="kota" id="inputKota" class="z-field" value="<?php echo htmlspecialchars($institusi['kota'] ?? ''); ?>" placeholder="Contoh: Lampung Barat" onkeyup="updatePreview()">
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Provinsi</label>
                        <input type="text" name="provinsi" id="inputProvinsi" class="z-field" value="<?php echo htmlspecialchars($institusi['provinsi'] ?? ''); ?>" placeholder="Contoh: Lampung" onkeyup="updatePreview()">
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Kode Pos</label>
                        <input type="text" name="kodepos" id="inputKodepos" class="z-field" value="<?php echo htmlspecialchars($institusi['kodepos'] ?? ''); ?>" placeholder="Contoh: 34886" onkeyup="updatePreview()">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="z-form-group">
                        <label class="z-label">Website</label>
                        <input type="text" name="website" id="inputWebsite" class="z-field" value="<?php echo htmlspecialchars($institusi['website'] ?? ''); ?>" placeholder="https://mtsrs.sch.id" onkeyup="updatePreview()">
                    </div>
                    
                    <div class="z-form-group">
                        <label class="z-label">Email</label>
                        <input type="email" name="email" id="inputEmail" class="z-field" value="<?php echo htmlspecialchars($institusi['email'] ?? ''); ?>" placeholder="email@contoh.com" onkeyup="updatePreview()">
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;"><i data-lucide="save"></i> Simpan Kop Surat</button>
                </div>
                </form>
            </div>
        </div>

        <!-- LIVE PREVIEW -->
        <div>
            <div class="z-card" style="background: #f8fafc; border: 2px dashed #cbd5e1; padding: 25px;">
                <h3 style="margin-top:0; border-bottom: 1px solid var(--z-border); padding-bottom: 10px; margin-bottom: 20px; color: var(--z-text-muted);">
                    <i data-lucide="eye"></i> Live Preview Kop Surat (Mode Cetak)
                </h3>
                
                <div style="background: white; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 4px;">
                    <!-- BENTUK KOP SURAT -->
                    <div style="display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 10px;">
                        
                        <?php $logoUrl = !empty($institusi['logo']) ? '/public/uploads/logo/'.$institusi['logo'] : '/public/assets/images/logo.png'; ?>
                        <img src="<?php echo $logoUrl; ?>" alt="Logo" style="width: 80px; height: 80px; object-fit: contain;">
                        
                        <div style="text-align: center; flex: 1; padding: 0 10px;">
                            <h1 id="previewYayasan" style="margin: 0; font-size: <?php echo intval($institusi['kop_font_yayasan'] ?? 14); ?>px; text-transform: uppercase; font-weight: bold; font-family: 'Times New Roman', Times, serif; color: #000;">
                                <?php echo htmlspecialchars($institusi['yayasan'] ?? ''); ?>
                            </h1>
                            <h2 id="previewNama" style="margin: 3px 0; font-size: <?php echo intval($institusi['kop_font_nama'] ?? 18); ?>px; font-weight: bold; font-family: 'Times New Roman', Times, serif; color: #000;">
                                <?php echo htmlspecialchars($institusi['nama'] ?? ''); ?>
                            </h2>
                            <?php 
                                $alamat = $institusi['alamat'] ?? '';
                                $desa = !empty($institusi['desa']) ? 'Ds. ' . $institusi['desa'] : '';
                                $kecamatan = !empty($institusi['kecamatan']) ? 'Kec. ' . $institusi['kecamatan'] : '';
                                $kota = !empty($institusi['kota']) ? 'Kab. ' . $institusi['kota'] : '';
                                $provinsi = !empty($institusi['provinsi']) ? 'Prov. ' . $institusi['provinsi'] : '';
                                $kodepos = !empty($institusi['kodepos']) ? 'KP. ' . $institusi['kodepos'] : '';
                                
                                $fullAddressArray = array_filter([$alamat, $desa, $kecamatan, $kota, $provinsi, $kodepos]);
                                $fullAddress = implode(', ', $fullAddressArray);
                            ?>
                            <p id="previewAlamat" style="margin: 3px 0; font-size: 11px; font-family: 'Times New Roman', Times, serif; color: #000;">
                                <?php echo htmlspecialchars($fullAddress); ?>
                            </p>
                            <p style="margin: 3px 0; font-size: 11px; font-family: 'Times New Roman', Times, serif; color: #000;">
                                Website: <span id="previewWebsite"><?php echo htmlspecialchars($institusi['website'] ?? ''); ?></span> | Email: <span id="previewEmail"><?php echo htmlspecialchars($institusi['email'] ?? ''); ?></span>
                            </p>
                        </div>
                        
                        <!-- Empty right space to balance the logo -->
                        <div style="width: 80px;"></div>
                    </div>
                </div>
                
                <div style="margin-top: 15px; font-size: 12px; color: var(--z-text-muted); text-align: center;">
                    *Preview ini adalah perkiraan tampilan yang akan dicetak di kertas.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updatePreview() {
        document.getElementById('previewYayasan').innerText = document.getElementById('inputYayasan').value;
        document.getElementById('previewYayasan').style.fontSize = document.getElementById('inputFontYayasan').value + 'px';
        
        document.getElementById('previewNama').innerText = document.getElementById('inputNama').value;
        document.getElementById('previewNama').style.fontSize = document.getElementById('inputFontNama').value + 'px';
        
        let arr = [];
        let jalan = document.getElementById('inputAlamat').value;
        let desa = document.getElementById('inputDesa').value;
        let kec = document.getElementById('inputKecamatan').value;
        let kota = document.getElementById('inputKota').value;
        let prov = document.getElementById('inputProvinsi').value;
        let pos = document.getElementById('inputKodepos').value;
        
        if (jalan) arr.push(jalan);
        if (desa) arr.push('Ds. ' + desa);
        if (kec) arr.push('Kec. ' + kec);
        if (kota) arr.push('Kab. ' + kota);
        if (prov) arr.push('Prov. ' + prov);
        if (pos) arr.push('KP. ' + pos);
        
        document.getElementById('previewAlamat').innerText = arr.join(', ');
        
        document.getElementById('previewWebsite').innerText = document.getElementById('inputWebsite').value;
        document.getElementById('previewEmail').innerText = document.getElementById('inputEmail').value;
    }
    
    function toggleForm() {
        const formContainer = document.getElementById('formContainer');
        const btn = document.getElementById('btnToggleForm');
        if (formContainer.style.display === 'none') {
            formContainer.style.display = 'block';
            btn.innerHTML = '<i data-lucide="eye-off"></i> Sembunyikan Form';
            lucide.createIcons();
        } else {
            formContainer.style.display = 'none';
            btn.innerHTML = '<i data-lucide="edit"></i> Edit Form';
            lucide.createIcons();
        }
    }
</script>
