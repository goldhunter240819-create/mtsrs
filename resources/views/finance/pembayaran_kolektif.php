<?php
$dbCore = \App\Core\Database::connect('core');
$inst = $dbCore->query("SELECT disable_web_trx FROM institusi LIMIT 1")->fetch();
$disableWebTrx = !empty($inst['disable_web_trx']);
?>
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="layers" style="color: #bfdbfe;"></i> Pembayaran Kolektif (Auto-Split)
        </h1>
        <p class="mph-subtitle">Bayar uang gelondongan, sistem akan otomatis memecah ke tagihan-tagihan siswa.</p>
    </div>
</div>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div style="padding: 15px; margin-bottom: 20px; border-radius: 8px; font-weight: 600; <?php echo $_SESSION['flash_type'] == 'success' ? 'background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;' : 'background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;'; ?>">
        <?php echo $_SESSION['flash_message']; ?>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; align-items: stretch;">
    
    <!-- Form Pilihan Siswa -->
    <div class="z-panel" style="height: 100%; display: flex; flex-direction: column;">
        <div class="z-panel-body" style="flex: 1;">
            <h3 style="margin-top:0; font-size: 1.1rem; color: var(--z-text); border-bottom: 1px solid var(--z-border); padding-bottom: 10px; margin-bottom: 15px;">Pilih Siswa</h3>
            <form action="/keuangan/kolektif" method="GET">
                <div class="z-form-group" style="margin-bottom:15px;">
                    <label class="z-label">Filter Kelas</label>
                    <select name="kelas_id" id="kelas_filter" class="z-field">
                        <option value="">Semua Kelas</option>
                        <?php if(!empty($kelas_list)): foreach($kelas_list as $k): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo (isset($filter_kelas_id) && $filter_kelas_id == $k['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($k['nama_kelas']); ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="z-form-group">
                    <label class="z-label">Cari Siswa</label>
                    <select name="siswa_id" id="siswa_select" class="z-field">
                        <option value="">-- Ketik Nama atau NIS --</option>
                        <?php foreach($siswa as $s): ?>
                            <option value="<?php echo $s['id']; ?>" <?php echo (isset($siswa_id) && $siswa_id == $s['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <?php if($selected_siswa): ?>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--z-border);">
                <h4 style="margin-top:0; margin-bottom: 10px; color: var(--z-text); font-weight: 700;">Detail Siswa</h4>
                <div style="margin-bottom: 8px; color: var(--z-muted); font-size: 0.9rem;">Nama: <span style="font-weight:700; color:var(--z-text);"><?php echo htmlspecialchars($selected_siswa['nama']); ?></span></div>
                <div style="margin-bottom: 8px; color: var(--z-muted); font-size: 0.9rem;">Total Tagihan Belum Dibayar: <span style="font-weight:800; color:var(--z-danger);">Rp <?php echo number_format($sisa_tagihan, 0, ',', '.'); ?></span></div>
                <div style="color: var(--z-muted); font-size: 0.9rem;">Jumlah Tagihan Belum Lunas: <span style="font-weight:700; color:var(--z-text);"><?php echo isset($belum_lunas_count) ? $belum_lunas_count : count($tagihan_list); ?> item</span></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Form Input Nominal -->
    <div class="z-panel" style="height: 100%; display: flex; flex-direction: column;">
        <div class="z-panel-body" style="flex: 1; display: flex; flex-direction: column;">
            <?php if($selected_siswa): ?>
                <?php if((isset($belum_lunas_count) ? $belum_lunas_count : count($tagihan_list)) > 0): ?>
                <h3 style="margin-top:0; font-size: 1.1rem; color: var(--z-text); border-bottom: 1px solid var(--z-border); padding-bottom: 10px; margin-bottom: 20px;">Input Pembayaran</h3>
                <form action="/keuangan/kolektif/save" method="POST">
                    <input type="hidden" name="siswa_id" value="<?php echo $siswa_id; ?>">
                    
                    <div class="z-form-group" style="margin-bottom: 20px;">
                        <label class="z-label">Nominal Uang Titipan (Rp)</label>
                        <input type="text" id="nominal_input_web" name="nominal" class="z-field" style="font-size: 1.5rem; font-weight: 800; padding: 15px; transition:0.2s;" placeholder="Contoh: 1.000.000" autocomplete="off" onkeyup="formatRupiah(this); checkLimitWeb();" data-max="<?php echo $sisa_tagihan; ?>" required>
                        <p id="limit_warning_web" style="font-size: 0.8rem; color: var(--z-muted); margin-top: 8px;">Maksimal pembayaran adalah Rp <?php echo number_format($sisa_tagihan, 0, ',', '.'); ?>.</p>
                    </div>

                    <input type="hidden" name="mode" value="Persentase">
                    
                    <div style="text-align: right; border-top: 1px solid var(--z-border); padding-top: 20px;">
                        <?php if(!$disableWebTrx): ?>
                        <button type="submit" id="btn_submit_web" class="btn btn-primary" style="padding: 12px 25px; font-size: 1rem;">
                            <i data-lucide="check-circle"></i> Proses Auto-Split
                        </button>
                        <?php else: ?>
                        <div style="text-align:left; background:#fee2e2; color:#dc2626; padding:10px 15px; border-radius:8px; font-size:0.9rem; font-weight:600;"><i data-lucide="shield-alert" style="width:16px;height:16px;margin-right:5px;vertical-align:-3px;"></i>Pencatatan Web Dinonaktifkan. Gunakan APK Kasir.</div>
                        <?php endif; ?>
                    </div>
                </form>
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

                    function checkLimitWeb() {
                        let input = document.getElementById('nominal_input_web');
                        let warning = document.getElementById('limit_warning_web');
                        let btn = document.getElementById('btn_submit_web');
                        
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
                            warning.innerHTML = "Maksimal pembayaran adalah Rp " + maxVal.toLocaleString('id-ID') + ".";
                            warning.style.color = "var(--z-muted)";
                            input.style.borderColor = "var(--z-border)";
                            input.style.background = "var(--z-bg)";
                            btn.disabled = false;
                            btn.style.opacity = "1";
                            btn.style.cursor = "pointer";
                        }
                    }
                </script>

                <div style="margin-top: 40px;">
                    <h4 style="margin-top:0; font-size: 1.05rem; color: var(--z-text); margin-bottom: 15px;">Preview Daftar Tagihan</h4>
                    <div class="z-table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama Tagihan</th>
                                    <th>Total Tagihan</th>
                                    <th>Sisa Hutang</th>
                                    <th>Aturan Auto-Split</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                    $info_config = '<span style="color:var(--z-muted); font-size:0.8rem; font-style:italic;">Belum diatur</span>';
                                    if ($config) {
                                        $info_config = '<span class="pill pill-amber" style="background:#fef3c7; color:#d97706; padding:4px 8px; border-radius:4px; font-weight:600; font-size:0.8rem;">Persentase '.floatval($config['nilai']).'%</span>';
                                    }
                                    if ($is_lunas) {
                                        $info_config = '<span style="color:var(--z-muted); font-size:0.8rem; font-style:italic;">- (Abaikan)</span>';
                                    }
                                ?>
                                <tr style="<?php echo $is_lunas ? 'opacity: 0.6; background-color: #f8fafc;' : ''; ?>">
                                    <td style="font-weight:600; color:var(--z-text);">
                                        <?php echo htmlspecialchars($t['nama_tagihan']); ?>
                                        <?php if($is_lunas): ?>
                                            <span style="display:inline-block; margin-left:8px; padding:2px 6px; background:#dcfce7; color:#16a34a; font-size:0.7rem; border-radius:4px; font-weight:700;">LUNAS</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>Rp <?php echo number_format($t['jumlah_tagihan'], 0, ',', '.'); ?></td>
                                    <td style="font-weight:800; color:<?php echo $is_lunas ? '#16a34a' : 'var(--z-danger)'; ?>;">Rp <?php echo number_format($hutang, 0, ',', '.'); ?></td>
                                    <td><?php echo $info_config; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; margin: auto;">
                    <div style="display:inline-flex; align-items:center; justify-content:center; width:80px; height:80px; background:#dcfce7; color:#16a34a; border-radius:50%; margin-bottom:20px;">
                        <i data-lucide="check" style="width:40px; height:40px;"></i>
                    </div>
                    <h3 style="font-size:1.5rem; color:var(--z-text); margin-bottom:10px; margin-top:0;">Lunas Semua!</h3>
                    <p style="color:var(--z-muted);">Siswa ini tidak memiliki tagihan yang belum lunas. Tidak dapat melakukan pembayaran kolektif.</p>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 80px 20px; color: var(--z-muted); margin: auto;">
                    <i data-lucide="search" style="width:64px; height:64px; margin-bottom: 20px; opacity: 0.5;"></i>
                    <p>Silakan cari dan pilih siswa terlebih dahulu di panel sebelah kiri.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Responsive layout for the grid */
@media (max-width: 768px) {
    div[style*="grid-template-columns: 1fr 2fr"] {
        grid-template-columns: 1fr !important;
    }
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
/* Tweaks for Select2 to match z-field */
.select2-container .select2-selection--single {
    height: 42px !important;
    border: 1px solid var(--z-border) !important;
    border-radius: 8px !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 40px !important;
    color: var(--z-text) !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px !important;
}
</style>

<!-- Select2 for better student searching -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#siswa_select').select2({
            placeholder: '-- Ketik Nama atau NIS --',
            allowClear: true,
            width: '100%'
        }).on('select2:select', function (e) {
            $(this).closest('form').submit();
        });

        $('#kelas_filter').on('change', function() {
            var kelasId = $(this).val();
            $('#siswa_select').prop('disabled', true);
            
            $.ajax({
                url: '/keuangan/kolektif/ajax-siswa',
                type: 'GET',
                data: { kelas_id: kelasId },
                success: function(res) {
                    var $select = $('#siswa_select');
                    $select.empty();
                    $select.append('<option value="">-- Ketik Nama atau NIS --</option>');
                    if(res && res.length > 0) {
                        $.each(res, function(i, siswa) {
                            $select.append('<option value="'+siswa.id+'">'+siswa.nis+' - '+siswa.nama+'</option>');
                        });
                    }
                    $select.prop('disabled', false);
                    $select.trigger('change.select2');
                },
                error: function() {
                    $('#siswa_select').prop('disabled', false);
                    alert('Gagal mengambil data siswa.');
                }
            });
        });
    });
</script>
