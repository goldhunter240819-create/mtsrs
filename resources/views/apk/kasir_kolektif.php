<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <a href="/apk/aplikasi-saya" style="color:white; text-decoration:none; display:flex; align-items:center; gap:8px;">
            <i data-lucide="arrow-left" style="width:20px;"></i>
        </a>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Kasir Bendahara</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Pembayaran Kolektif</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding-bottom: 100px;">

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div style="padding: 14px; margin-bottom: 16px; border-radius: 12px; font-weight: 600; font-size: 0.85rem;
            <?php echo ($_SESSION['flash_type'] ?? '') == 'success' 
                ? 'background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;' 
                : 'background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;'; ?>">
            <?php echo $_SESSION['flash_message']; ?>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        </div>
    <?php endif; ?>

    <style>
        .kasir-card { background: #fff; border-radius: 16px; padding: 18px; margin-bottom: 16px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .kasir-label { font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block; }
        .kasir-select { width: 100%; padding: 12px 14px; border-radius: 12px; border: 1.5px solid #e2e8f0; background: #f8fafc; color: #1e293b; font-size: 0.95rem; font-weight: 600; outline: none; transition: border 0.2s; -webkit-appearance: none; }
        .kasir-select:focus { border-color: #0ea5e9; }
        .kasir-input { width: 100%; padding: 16px; border-radius: 14px; border: 2px solid #e2e8f0; background: #fff; color: #1e293b; font-size: 1.8rem; font-weight: 800; text-align: center; outline: none; transition: border 0.2s; box-sizing: border-box; }
        .kasir-input:focus { border-color: #0ea5e9; }
        .kasir-input::placeholder { font-size: 1rem; font-weight: 600; color: #94a3b8; }
        .kasir-btn { width: 100%; padding: 16px; border-radius: 14px; border: none; font-size: 1.05rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s; }
        .kasir-btn-primary { background: linear-gradient(135deg, #0ea5e9, #0284c7); color: #fff; box-shadow: 0 4px 14px rgba(14,165,233,0.35); }
        .kasir-btn-primary:active { transform: scale(0.97); }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-size: 0.82rem; color: #64748b; font-weight: 600; }
        .info-value { font-size: 0.9rem; color: #1e293b; font-weight: 700; }
        .tagihan-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
        .tagihan-item:last-child { border-bottom: none; }
        .tagihan-nama { font-size: 0.85rem; font-weight: 700; color: #1e293b; }
        .tagihan-detail { font-size: 0.78rem; color: #64748b; margin-top: 2px; }
        .tagihan-sisa { font-size: 0.9rem; font-weight: 800; color: #ef4444; text-align: right; }
        .pill-lunas { background: #dcfce7; color: #166534; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }
        .pill-persen { background: #fef3c7; color: #d97706; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }
    </style>

    <!-- Cari Siswa -->
    <div class="kasir-card">
        <label class="kasir-label"><i data-lucide="search" style="width:14px; display:inline; vertical-align:middle;"></i> Cari Siswa</label>
        
        <div style="margin-bottom: 10px;">
            <select id="filter_kelas" class="kasir-select" onchange="filterSiswaByKelas()">
                <option value="">-- Semua Kelas --</option>
                <?php foreach($kelas as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <form action="/apk/kasir/kolektif" method="GET">
            <select id="select_siswa" name="siswa_id" class="kasir-select select2-siswa">
                <option value="">-- Ketik Nama / NIS --</option>
                <?php foreach($siswa as $s): ?>
                    <option value="<?= $s['id'] ?>" data-kelas="<?= $s['kelas_id'] ?>" <?= (isset($siswa_id) && $siswa_id == $s['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama'] . ' (' . ($s['nama_kelas'] ?? 'Tanpa Kelas') . ')') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .select2-container .select2-selection--single {
            height: 44px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            padding: 6px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px;
            right: 10px;
        }
    </style>
    <script>
        let originalSiswaOptions = [];

        $(document).ready(function() {
            originalSiswaOptions = $('#select_siswa option').clone();

            $('.select2-siswa').select2({
                placeholder: "-- Ketik Nama / NIS --",
                width: '100%',
                language: {
                    noResults: function() { return "Siswa tidak ditemukan"; }
                }
            });

            $('.select2-siswa').on('select2:select', function (e) {
                if(this.value) this.form.submit();
            });

            let selectedOption = $('#select_siswa option:selected');
            if (selectedOption.val() !== "") {
                let kId = selectedOption.attr('data-kelas');
                if (kId) {
                    $('#filter_kelas').val(kId);
                }
            }
            
            filterSiswaByKelas(true);
        });

        function filterSiswaByKelas(isInitial = false) {
            let kelasId = $('#filter_kelas').val();
            let selectSiswa = $('#select_siswa');
            let currentVal = selectSiswa.val();
            
            selectSiswa.empty();
            
            originalSiswaOptions.each(function() {
                if ($(this).val() === "") {
                    selectSiswa.append($(this).clone());
                } else {
                    if (kelasId === "" || $(this).attr('data-kelas') == kelasId) {
                        selectSiswa.append($(this).clone());
                    }
                }
            });
            
            if (currentVal && selectSiswa.find(`option[value="${currentVal}"]`).length > 0) {
                selectSiswa.val(currentVal);
            } else {
                selectSiswa.val('');
            }

            selectSiswa.trigger('change.select2');
        }
    </script>

    <?php if($selected_siswa): ?>
    <!-- Info Siswa -->
    <div class="kasir-card">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div style="width:44px; height:44px; border-radius:14px; background:linear-gradient(135deg,#0ea5e9,#0284c7); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:1.1rem;">
                <?= strtoupper(substr($selected_siswa['nama'], 0, 1)) ?>
            </div>
            <div>
                <div style="font-size:1rem; font-weight:800; color:#1e293b;"><?= htmlspecialchars($selected_siswa['nama']) ?></div>
                <div style="font-size:0.8rem; color:#64748b;">NIS: <?= htmlspecialchars($selected_siswa['nis']) ?></div>
            </div>
        </div>
        <div class="info-row">
            <span class="info-label">Total Sisa Tagihan</span>
            <span class="info-value" style="color:#ef4444; font-size:1.05rem;">Rp <?= number_format($sisa_tagihan, 0, ',', '.') ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Tagihan Belum Lunas</span>
            <span class="info-value"><?= isset($belum_lunas_count) ? $belum_lunas_count : count($tagihan_list) ?> item</span>
        </div>
    </div>

    <?php if((isset($belum_lunas_count) ? $belum_lunas_count : count($tagihan_list)) > 0): ?>
    <!-- Form Bayar -->
    <div class="kasir-card" style="border: 2px solid #bae6fd; background: #f0f9ff;">
        <form action="/apk/kasir/kolektif/save" method="POST">
            <input type="hidden" name="siswa_id" value="<?= $siswa_id ?>">
            <input type="hidden" name="mode" value="Persentase">
            <input type="hidden" name="is_apk" value="1">

            <label class="kasir-label" style="text-align:center; margin-bottom:12px;">Nominal Uang Titipan (Rp)</label>
            <input type="text" id="nominal_input" name="nominal" class="kasir-input" placeholder="Masukkan Nominal" required autocomplete="off"
                   onkeyup="formatRupiah(this); checkLimit();" data-max="<?= $sisa_tagihan ?>" style="transition:0.2s;">

            <p id="limit_warning" style="text-align:center; font-size:0.78rem; color:#0369a1; margin:10px 0 18px; font-weight:600;">
                Maks. Rp <?= number_format($sisa_tagihan, 0, ',', '.') ?>
            </p>

            <button type="submit" id="btn_submit" class="kasir-btn kasir-btn-primary">
                <i data-lucide="zap" style="width:20px;"></i> Proses Auto-Split
            </button>
        </form>
    </div>
    <script>
        function formatRupiah(angka) {
            var number_string = angka.value.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            angka.value = rupiah;
        }

        function checkLimit() {
            let input = document.getElementById('nominal_input');
            let warning = document.getElementById('limit_warning');
            let btn = document.getElementById('btn_submit');
            
            let maxVal = parseInt(input.getAttribute('data-max'));
            let currentVal = parseInt(input.value.replace(/[^0-9]/g, '')) || 0;

            if(currentVal > maxVal) {
                warning.innerHTML = "Nominal berlebih! (Maks. Rp " + maxVal.toLocaleString('id-ID') + ")";
                warning.style.color = "#ef4444";
                input.style.borderColor = "#ef4444";
                input.style.background = "#fef2f2";
                btn.disabled = true;
                btn.style.opacity = "0.5";
                btn.style.cursor = "not-allowed";
            } else {
                warning.innerHTML = "Maks. Rp " + maxVal.toLocaleString('id-ID');
                warning.style.color = "#0369a1";
                input.style.borderColor = "#bae6fd";
                input.style.background = "#fff";
                btn.disabled = false;
                btn.style.opacity = "1";
                btn.style.cursor = "pointer";
            }
        }
    </script>
    <?php else: ?>
    <div class="kasir-card" style="text-align:center; padding: 30px;">
        <i data-lucide="check-circle-2" style="width:48px; height:48px; color:#22c55e; margin-bottom:10px;"></i>
        <p style="font-weight:800; color:#166534; font-size:1rem; margin:0;">Semua Tagihan Lunas! 🎉</p>
    </div>
    <?php endif; ?>

    <!-- Preview Tagihan -->
    <div class="kasir-card">
        <div style="font-size:0.9rem; font-weight:800; color:#1e293b; margin-bottom:12px;">
            <i data-lucide="list" style="width:16px; display:inline; vertical-align:middle;"></i> Preview Tagihan
        </div>
        <?php foreach($tagihan_list as $t): 
            $is_lunas = ($t['status'] === 'Lunas');
            $hutang = $t['jumlah_tagihan'] - $t['jumlah_terbayar'];
            $config = null;
            foreach ($config_map as $c_name => $c) {
                if (stripos($t['nama_tagihan'], $c_name) !== false) {
                    $config = $c;
                    break;
                }
            }
        ?>
        <div class="tagihan-item" style="<?= $is_lunas ? 'opacity:0.5;' : '' ?>">
            <div>
                <div class="tagihan-nama"><?= htmlspecialchars($t['nama_tagihan']) ?></div>
                <div class="tagihan-detail">
                    Total: Rp <?= number_format($t['jumlah_tagihan'], 0, ',', '.') ?>
                    <?php if ($is_lunas): ?>
                        <span class="pill-lunas">Lunas ✓</span>
                    <?php elseif ($config): ?>
                        <span class="pill-persen"><?= floatval($config['nilai']) ?>%</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tagihan-sisa">
                <?php if ($is_lunas): ?>
                    <span style="color:#22c55e; font-size:0.85rem;">Lunas</span>
                <?php else: ?>
                    Rp <?= number_format($hutang, 0, ',', '.') ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if(empty($tagihan_list)): ?>
        <div style="text-align:center; padding:20px; color:#94a3b8; font-size:0.85rem;">
            Pilih siswa terlebih dahulu.
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>
