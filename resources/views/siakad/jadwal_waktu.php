<?php use App\Core\Helper; ?>
<div class="modern-page-header">
    <div>
        <a href="<?php echo Helper::url('/siakad/jadwal'); ?>" style="text-decoration:none; color:var(--text-muted); margin-bottom: 5px; display:inline-block;"><i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Kembali</a>
        <h1 class="mph-title">Pengaturan Jam Pelajaran</h1>
        <p class="mph-subtitle">Atur slot waktu utama (Master Time Slots) yang akan digunakan untuk seluruh kelas.</p>
    </div>
</div>

<div class="floating-card">
    <form action="<?php echo Helper::url('/siakad/jadwal/waktu'); ?>" method="POST">
        <div class="table-responsive">
            <table class="glass-table" id="waktuTable">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align:center;">Jam Ke</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th style="width: 150px; text-align:center;">Waktu Istirahat?</th>
                        <th style="width: 80px; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="waktuBody">
                    <?php 
                    $rows = $waktuList;
                    // If empty (shouldn't be, but just in case), provide 5 empty rows
                    if(empty($rows)) {
                        for($i=1; $i<=5; $i++) {
                            $rows[] = ['jam_mulai' => '', 'jam_selesai' => '', 'is_istirahat' => 0];
                        }
                    }
                    ?>
                    
                    <?php foreach($rows as $i => $w): ?>
                    <tr>
                        <td style="text-align:center; font-weight:bold;" class="jam-ke-label">
                            <?php echo $i+1; ?>
                        </td>
                        <td>
                            <input type="time" name="jam_mulai[]" value="<?php echo empty($w['jam_mulai']) ? '' : substr($w['jam_mulai'], 0, 5); ?>" class="form-input" required>
                        </td>
                        <td>
                            <input type="time" name="jam_selesai[]" value="<?php echo empty($w['jam_selesai']) ? '' : substr($w['jam_selesai'], 0, 5); ?>" class="form-input" required>
                        </td>
                        <td style="text-align:center;">
                            <input type="hidden" name="is_istirahat[]" value="<?php echo $w['is_istirahat'] ? '1' : '0'; ?>">
                            <input type="checkbox" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'" <?php echo $w['is_istirahat'] ? 'checked' : ''; ?> style="width:18px;height:18px;cursor:pointer;">
                        </td>
                        <td style="text-align:center;">
                            <button type="button" class="btn-del-row" style="background:none;border:none;color:#ef4444;cursor:pointer;padding:4px;" title="Hapus Baris ini"><i data-lucide="trash-2" style="width:18px;height:18px;"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 15px; display:flex; justify-content:space-between; align-items:center;">
            <button type="button" class="btn btn-secondary" id="btnAddRow" style="background:#e0e7ff; color:#4f46e5; border:none;">
                <i data-lucide="plus"></i> Tambah Kolom/Baris Waktu
            </button>
            <button type="submit" class="btn btn-primary">Simpan Pengaturan Waktu</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const waktuBody = document.getElementById('waktuBody');
    const btnAddRow = document.getElementById('btnAddRow');

    function updateJamKe() {
        const labels = waktuBody.querySelectorAll('.jam-ke-label');
        labels.forEach((label, index) => {
            label.textContent = index + 1;
        });
    }

    waktuBody.addEventListener('click', function(e) {
        if(e.target.closest('.btn-del-row')) {
            if(waktuBody.children.length > 1) {
                e.target.closest('tr').remove();
                updateJamKe();
            } else {
                alert('Minimal harus ada 1 baris waktu.');
            }
        }
    });

    btnAddRow.addEventListener('click', function() {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td style="text-align:center; font-weight:bold;" class="jam-ke-label"></td>
            <td><input type="time" name="jam_mulai[]" value="" class="form-input" required></td>
            <td><input type="time" name="jam_selesai[]" value="" class="form-input" required></td>
            <td style="text-align:center;">
                <input type="hidden" name="is_istirahat[]" value="0">
                <input type="checkbox" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'" style="width:18px;height:18px;cursor:pointer;">
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn-del-row" style="background:none;border:none;color:#ef4444;cursor:pointer;padding:4px;"><i data-lucide="trash-2" style="width:18px;height:18px;"></i></button>
            </td>
        `;
        waktuBody.appendChild(newRow);
        updateJamKe();
        if(typeof lucide !== 'undefined') lucide.createIcons();
    });
});
</script>
