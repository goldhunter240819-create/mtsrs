<?php use App\Core\Helper; ?>
<div class="modern-page-header">
    <div>
        <a href="<?php echo Helper::url('/siakad/jadwal'); ?>" style="text-decoration:none; color:var(--text-muted); margin-bottom: 5px; display:inline-block;"><i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Kembali</a>
        <h1 class="mph-title">Atur Jadwal Kelas <?php echo htmlspecialchars($kelas['nama_kelas']); ?></h1>
        <p class="mph-subtitle">Tahun Ajaran <?php echo htmlspecialchars($activeYear['name']); ?></p>
    </div>
</div>

<div class="floating-card">
    <p style="color:var(--text-muted); margin-bottom: 20px;">Pilih mata pelajaran pada slot jam yang tersedia. Nama guru akan otomatis menyesuaikan dengan tabel jadwal yang telah Anda simpan sebelumnya. Klik tombol check-mark atau ubah pilihan untuk menyimpan otomatis.</p>

    <?php 
    $hari_list = ['Sabtu', 'Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis'];

    foreach($hari_list as $hari): 
    ?>
        <h3 style="margin-top:0; color:var(--primary-blue); border-bottom: 2px solid #eee; padding-bottom:5px; margin-bottom:15px; display:flex; align-items:center; gap:8px;">
            <i data-lucide="calendar" style="width:18px;height:18px;"></i> <?php echo $hari; ?>
        </h3>
        
        <div class="table-responsive" style="margin-bottom: 40px;">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th style="width: 70px; text-align:center;">Jam Ke</th>
                        <th style="width: 130px;">Waktu</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengampu</th>
                        <th style="width: 100px; text-align:center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach($jam_waktu as $i => $jam_info): 
                        $jam_mulai_txt = $jam_info[0];
                        $jam_selesai_txt = $jam_info[1];
                        
                        $existing = $jadwal[$hari][$jam_mulai_txt] ?? null;
                        
                        // Jam istirahat disable
                        $is_break = $jam_info[2];
                    ?>
                    <tr id="row-<?php echo $hari; ?>-<?php echo $i; ?>" style="<?php echo $is_break ? 'background-color:#f8fafc;' : ''; ?>">
                        <td style="text-align:center; font-weight:600; <?php echo $is_break ? 'color:#94a3b8;' : ''; ?>">
                            <?php echo $i; ?>
                        </td>
                        <td style="font-size:0.9rem; font-weight:600; <?php echo $is_break ? 'color:#94a3b8;' : 'color:var(--primary-purple);'; ?>">
                            <?php echo $jam_mulai_txt; ?> - <?php echo $jam_selesai_txt; ?>
                        </td>
                        <?php if($is_break): ?>
                            <td colspan="2" style="color:#94a3b8; font-style:italic; font-weight: 600;">
                                ISTIRAHAT
                            </td>
                            <td></td>
                        <?php else: ?>
                            <td>
                                <select class="form-select select-mapel" data-hari="<?php echo $hari; ?>" data-jam-ke="<?php echo $i; ?>" style="padding: 6px 12px; font-size: 0.9rem; height: auto;">
                                    <option value="">-- Kosong --</option>
                                    <?php foreach($mapel as $m): ?>
                                    <option value="<?php echo $m['id']; ?>" <?php echo ($existing && $existing['mapel_id'] == $m['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($m['nama_mapel']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select class="form-select select-guru" id="guru-<?php echo $hari; ?>-<?php echo $i; ?>" data-hari="<?php echo $hari; ?>" data-jam-ke="<?php echo $i; ?>" style="padding: 6px 12px; font-size: 0.9rem; height: auto;">
                                    <option value="">-- Kosong --</option>
                                    <?php foreach($guru as $g): ?>
                                    <option value="<?php echo $g['id']; ?>" <?php echo ($existing && $existing['guru_id'] == $g['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($g['nama']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td style="text-align:center;">
                                <div id="loader-<?php echo $hari; ?>-<?php echo $i; ?>" style="display:none; text-align:center; padding: 6px;">
                                    <i data-lucide="loader" class="z-spin" style="width:16px;height:16px; color:var(--primary-blue);"></i>
                                </div>
                                <div id="success-<?php echo $hari; ?>-<?php echo $i; ?>" style="display:none; text-align:center; padding: 6px; color: #10b981;">
                                    <i data-lucide="check-circle" style="width:16px;height:16px;"></i>
                                </div>
                                <button class="save-btn" 
                                    data-hari="<?php echo $hari; ?>" 
                                    data-jam-ke="<?php echo $i; ?>" 
                                    data-jam-mulai="<?php echo $jam_mulai_txt . ':00'; ?>"
                                    data-jam-selesai="<?php echo $jam_selesai_txt . ':00'; ?>"
                                    data-jadwal-id="<?php echo $existing ? $existing['id'] : ''; ?>"
                                    id="btn-save-<?php echo $hari; ?>-<?php echo $i; ?>"
                                    style="display:none;"></button>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const saveUrl = "<?php echo Helper::url('/siakad/jadwal/save_inline/' . $kelas['id']); ?>";

    const mengajarMap = <?php 
        $map = [];
        foreach($mengajar_list as $m) {
            $map[$m['mapel_id']] = $m['guru_id'];
        }
        echo json_encode($map);
    ?>;

    $('.select-mapel').on('change', function() {
        const hari = $(this).data('hari');
        const jamKe = $(this).data('jam-ke');
        const mapelId = $(this).val();
        
        if (mapelId && mengajarMap[mapelId]) {
            $('#guru-' + hari + '-' + jamKe).val(mengajarMap[mapelId]);
        } else if (!mapelId) {
            $('#guru-' + hari + '-' + jamKe).val('');
        }
        
        $('#btn-save-' + hari + '-' + jamKe).click();
    });

    $('.select-guru').on('change', function() {
        const hari = $(this).data('hari');
        const jamKe = $(this).data('jam-ke');
        $('#btn-save-' + hari + '-' + jamKe).click();
    });

    // Handle save (Auto triggered now)
    $('.save-btn').on('click', function() {
        const btn = $(this);
        const hari = btn.data('hari');
        const jamKe = btn.data('jam-ke');
        
        const mapelId = btn.closest('tr').find('.select-mapel').val();
        const guruId = $('#guru-' + hari + '-' + jamKe).val();
        
        // Read time from button data
        const jamMulai = btn.data('jam-mulai');
        const jamSelesai = btn.data('jam-selesai');
        
        const jadwalId = btn.attr('data-jadwal-id'); // Use attr to get dynamic updates

        if (mapelId && !guruId) {
            // Highlight guru select to tell user they need to select it
            $('#guru-' + hari + '-' + jamKe).css({'border': '2px solid #ef4444', 'background-color': '#fef2f2'});
            return;
        } else {
            $('#guru-' + hari + '-' + jamKe).css({'border': '', 'background-color': ''});
        }

        const loader = $('#loader-' + hari + '-' + jamKe);
        const successIcon = $('#success-' + hari + '-' + jamKe);
        successIcon.hide();
        loader.show();

        $.ajax({
            url: saveUrl,
            type: 'POST',
            data: {
                hari: hari,
                jam_mulai: jamMulai,
                jam_selesai: jamSelesai,
                mapel_id: mapelId,
                guru_id: guruId,
                jadwal_id: jadwalId
            },
            success: function(res) {
                loader.hide();
                
                if (res.status === 'success') {
                    // Update UI to success state
                    if (res.jadwal_id) {
                        btn.attr('data-jadwal-id', res.jadwal_id);
                    } else {
                        btn.attr('data-jadwal-id', '');
                    }
                    // Show checkmark briefly
                    successIcon.show();
                    if(typeof lucide !== 'undefined') lucide.createIcons();
                    setTimeout(() => { successIcon.fadeOut(); }, 2000);
                } else {
                    alert(res.message);
                }
            },
            error: function() {
                loader.hide();
                alert('Terjadi kesalahan jaringan.');
            }
        });
    });
});
</script>

<style>
.z-spin {
    animation: z-spin 1s linear infinite;
}
@keyframes z-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
