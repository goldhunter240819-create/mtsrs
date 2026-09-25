<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <a href="/apk/aplikasi-saya" style="color:white; text-decoration:none; display:flex; align-items:center; gap:8px;">
            <i data-lucide="arrow-left" style="width:20px;"></i>
        </a>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Kasir Bendahara</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Pembayaran Manual</div>
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
        .kasir-input-sm { width: 100%; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #fff; color: #1e293b; font-size: 1rem; font-weight: 700; outline: none; transition: border 0.2s; box-sizing: border-box; text-align: center; }
        .kasir-input-sm:focus { border-color: #0ea5e9; }
        .kasir-btn-sm { padding: 10px 16px; border-radius: 10px; border: none; font-size: 0.85rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s; }
        .kasir-btn-pay { background: linear-gradient(135deg, #10b981, #059669); color: #fff; box-shadow: 0 3px 10px rgba(16,185,129,0.3); }
        .kasir-btn-pay:active { transform: scale(0.97); }
        .kasir-btn-pay:disabled { opacity: 0.4; cursor: not-allowed; transform: none; box-shadow: none; }
        .tagihan-card { background: #fff; border-radius: 14px; padding: 16px; margin-bottom: 12px; border: 1px solid #e2e8f0; transition: all 0.2s; }
        .tagihan-card.lunas { opacity: 0.5; background: #f0fdf4; border-color: #bbf7d0; }
        .tagihan-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
        .tagihan-nama { font-size: 0.9rem; font-weight: 800; color: #1e293b; }
        .tagihan-meta { font-size: 0.78rem; color: #64748b; margin-top: 2px; }
        .tagihan-badge-lunas { background: #dcfce7; color: #166534; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }
        .tagihan-badge-angsur { background: #dbeafe; color: #1d4ed8; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }
        .tagihan-amounts { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 12px; }
        .tagihan-amount-box { background: #f8fafc; border-radius: 10px; padding: 8px 10px; text-align: center; }
        .tagihan-amount-label { font-size: 0.7rem; color: #94a3b8; font-weight: 600; }
        .tagihan-amount-value { font-size: 0.9rem; font-weight: 800; color: #1e293b; margin-top: 2px; }
        .tagihan-pay-row { display: flex; gap: 8px; align-items: center; }
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

        <form action="/apk/kasir/manual" method="GET">
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
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:44px; height:44px; border-radius:14px; background:linear-gradient(135deg,#10b981,#059669); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:1.1rem;">
                <?= strtoupper(substr($selected_siswa['nama'], 0, 1)) ?>
            </div>
            <div>
                <div style="font-size:1rem; font-weight:800; color:#1e293b;"><?= htmlspecialchars($selected_siswa['nama']) ?></div>
                <div style="font-size:0.8rem; color:#64748b;">NIS: <?= htmlspecialchars($selected_siswa['nis']) ?></div>
            </div>
        </div>
    </div>

    <!-- Daftar Tagihan -->
    <div style="font-size:0.9rem; font-weight:800; color:#1e293b; margin-bottom:12px; padding-left:4px;">
        <i data-lucide="receipt" style="width:16px; display:inline; vertical-align:middle;"></i> Daftar Tagihan
    </div>

    <?php foreach($tagihan_list as $t): 
        $is_lunas = ($t['status'] === 'Lunas');
        $hutang = $t['jumlah_tagihan'] - $t['jumlah_terbayar'];
    ?>
    <div class="tagihan-card <?= $is_lunas ? 'lunas' : '' ?>">
        <div class="tagihan-header">
            <div>
                <div class="tagihan-nama"><?= htmlspecialchars($t['nama_tagihan']) ?></div>
                <div class="tagihan-meta">
                    <?php if ($is_lunas): ?>
                        <span class="tagihan-badge-lunas">✓ Lunas</span>
                    <?php elseif ($t['status'] === 'Mengangsur'): ?>
                        <span class="tagihan-badge-angsur">Mengangsur</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="tagihan-amounts">
            <div class="tagihan-amount-box">
                <div class="tagihan-amount-label">Total Tagihan</div>
                <div class="tagihan-amount-value">Rp <?= number_format($t['jumlah_tagihan'], 0, ',', '.') ?></div>
            </div>
            <div class="tagihan-amount-box" style="<?= $is_lunas ? '' : 'background:#fef2f2;' ?>">
                <div class="tagihan-amount-label">Sisa Hutang</div>
                <div class="tagihan-amount-value" style="color:<?= $is_lunas ? '#22c55e' : '#ef4444' ?>;">
                    <?= $is_lunas ? 'Rp 0' : 'Rp ' . number_format($hutang, 0, ',', '.') ?>
                </div>
            </div>
        </div>

        <?php if (!$is_lunas): ?>
        <div class="tagihan-pay-row">
            <input type="text" id="nom_<?= $t['id'] ?>" class="kasir-input-sm" placeholder="Nominal" autocomplete="off"
                   onkeyup="formatRupiah(this); checkLimit(<?= $t['id'] ?>);" data-max="<?= $hutang ?>" style="flex:1; transition:0.2s;">
            <button type="button" id="btn_<?= $t['id'] ?>" class="kasir-btn-sm kasir-btn-pay" 
                    onclick="bayarManual(<?= $siswa_id ?>, '<?= htmlspecialchars($t['nama_tagihan'], ENT_QUOTES) ?>', <?= $t['id'] ?>)">
                <i data-lucide="banknote" style="width:16px;"></i> Bayar
            </button>
        </div>
        <div id="warn_<?= $t['id'] ?>" style="font-size: 0.75rem; color: #ef4444; margin-top: 5px; display: none;"></div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <?php if(empty($tagihan_list)): ?>
    <div class="kasir-card" style="text-align:center; padding:30px;">
        <i data-lucide="inbox" style="width:48px; height:48px; color:#cbd5e1; margin-bottom:10px;"></i>
        <p style="color:#94a3b8; font-size:0.85rem; font-weight:600; margin:0;">Pilih siswa untuk melihat tagihan.</p>
    </div>
    <?php endif; ?>
    <?php endif; ?>

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

function checkLimit(id) {
    let input = document.getElementById('nom_' + id);
    let btn = document.getElementById('btn_' + id);
    let warn = document.getElementById('warn_' + id);
    
    let maxVal = parseInt(input.getAttribute('data-max'));
    let currentVal = parseInt(input.value.replace(/[^0-9]/g, '')) || 0;

    if(currentVal > maxVal) {
        warn.innerHTML = "Nominal melebihi sisa tagihan! (Maks. Rp " + maxVal.toLocaleString('id-ID') + ")";
        warn.style.display = "block";
        input.style.borderColor = "#ef4444";
        input.style.background = "#fef2f2";
        btn.disabled = true;
        btn.style.opacity = "0.5";
        btn.style.cursor = "not-allowed";
    } else {
        warn.style.display = "none";
        input.style.borderColor = "#e2e8f0";
        input.style.background = "#fff";
        btn.disabled = false;
        btn.style.opacity = "1";
        btn.style.cursor = "pointer";
    }
}

function bayarManual(siswaId, namaTagihan, tagihanId) {
    var inputEl = document.getElementById('nom_' + tagihanId);
    var nominalRaw = inputEl ? inputEl.value : '';
    var nominal = nominalRaw.replace(/[^0-9]/g, '');
    
    if (!nominal || parseInt(nominal) <= 0) {
        Swal.fire({ icon: 'warning', title: 'Oops', text: 'Masukkan nominal yang valid!' });
        return;
    }
    
    var nominalFormatted = parseInt(nominal).toLocaleString('id-ID');
    
    Swal.fire({
        title: 'Konfirmasi Pembayaran',
        html: 'Bayar <b>' + namaTagihan + '</b> sebesar <b>Rp ' + nominalFormatted + '</b>?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Bayar!',
        cancelButtonText: 'Batal'
    }).then(function(result) {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({ title: 'Memproses...', text: 'Sedang menyimpan pembayaran', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
            
            var formData = new FormData();
            formData.append('siswa_id', siswaId);
            formData.append('nama_tagihan', namaTagihan);
            formData.append('nominal', nominal);
            
            fetch('/apk/kasir/manual/save', {
                method: 'POST',
                body: formData
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        // Reload halaman dengan filter siswa tetap
                        window.location.href = '/apk/kasir/manual?siswa_id=' + siswaId;
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
                }
            })
            .catch(function(err) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan koneksi.' });
            });
        }
    });
}
</script>
