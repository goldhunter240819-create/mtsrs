<?php 
$totalPersen = 0;
foreach($configs as $c) {
    $totalPersen += floatval($c['nilai']);
}
$sisaKuota = 100 - $totalPersen;
?>
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="settings" style="color: #bfdbfe;"></i> Pengaturan Auto-Split
        </h1>
        <p class="mph-subtitle">Atur daftar prioritas atau persentase pemecahan pembayaran kolektif.</p>
    </div>
    <div class="mph-actions">
        <a href="/keuangan/kolektif" class="btn btn-outline">
            <i data-lucide="arrow-left"></i> Kembali ke Pembayaran
        </a>
    </div>
</div>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div style="padding: 15px; margin-bottom: 20px; border-radius: 8px; font-weight: 600; <?php echo $_SESSION['flash_type'] == 'success' ? 'background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;' : 'background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;'; ?>">
        <?php echo $_SESSION['flash_message']; ?>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    </div>
<?php endif; ?>

<!-- Daftar Aturan -->
<div class="z-panel">
    <div class="z-panel-body">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--z-border); padding-bottom: 10px; margin-bottom: 15px;">
            <h3 style="margin:0; font-size: 1.1rem; color: var(--z-text);">Daftar Aturan Aktif</h3>
            <button class="btn btn-primary" onclick="bukaModal()">
                <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Tambah Aturan
            </button>
        </div>
        
        <?php if(count($configs) > 0): ?>
        <div class="z-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama Tagihan</th>
                        <th style="text-align: center;">Persentase Potongan (%)</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($configs as $c): ?>
                    <tr>
                        <td style="font-weight:600; color:var(--z-text);"><?php echo htmlspecialchars($c['nama_tagihan']); ?></td>
                        <td style="text-align: center; font-weight: 800; font-size: 1.1rem; color: var(--z-primary);">
                            <?php echo floatval($c['nilai']); ?>%
                        </td>
                        <td style="text-align: right;">
                            <button type="button" class="btn btn-ghost btn-sm" onclick="editAturan('<?php echo htmlspecialchars($c['nama_tagihan'], ENT_QUOTES); ?>', <?php echo floatval($c['nilai']); ?>)"><i data-lucide="edit-3" style="width:14px;height:14px;"></i></button>
                            <button type="button" class="btn btn-ghost btn-sm" style="color:var(--z-danger);" onclick="hapusAturan(<?php echo $c['id']; ?>, '<?php echo htmlspecialchars($c['nama_tagihan'], ENT_QUOTES); ?>')"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top: 15px; padding: 15px; border-radius: 8px; background: #f0fdf4; border: 1px solid #bbf7d0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-weight: 600; color: #166534;">Total Kuota Terpakai</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #15803d;"><?php echo $totalPersen; ?>%</div>
            </div>
            <div style="text-align: right;">
                <div style="font-weight: 600; color: <?php echo $sisaKuota < 0 ? '#991b1b' : '#166534'; ?>;">Sisa Kuota Tersedia</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: <?php echo $sisaKuota < 0 ? '#dc2626' : '#15803d'; ?>;"><?php echo $sisaKuota; ?>%</div>
            </div>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 40px 20px; color: var(--z-muted);">
            <p>Belum ada aturan yang dibuat. Silakan tambahkan aturan baru dengan menekan tombol di atas.</p>
        </div>
        <div style="margin-top: 15px; padding: 15px; border-radius: 8px; background: #f0fdf4; border: 1px solid #bbf7d0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-weight: 600; color: #166534;">Total Kuota Terpakai</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #15803d;">0%</div>
            </div>
            <div style="text-align: right;">
                <div style="font-weight: 600; color: #166534;">Sisa Kuota Tersedia</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #15803d;">100%</div>
            </div>
        </div>
        <?php endif; ?>
        
        <div style="margin-top: 25px; background: #fffbeb; border: 1px solid #fde68a; padding: 15px; border-radius: 8px;">
            <h4 style="margin-top: 0; color: #92400e; font-size: 0.9rem; margin-bottom: 5px;"><i data-lucide="info" style="width: 14px; height: 14px; display: inline-block;"></i> Info Penting</h4>
            <p style="margin: 0; font-size: 0.8rem; color: #b45309; line-height: 1.4;">
                Jika ada tagihan siswa yang tidak terdaftar dalam aturan ini (atau tagihan tersebut sudah lunas), maka uang akan otomatis masuk sebagai <b>"Sisa"</b> dan sistem akan menggunakannya untuk melunasi tagihan lainnya secara otomatis.<br>
                Total aturan maksimal 100%. Jika ada sisa kuota yang tidak diatur (misal total hanya 70%), maka sisa 30% akan dialokasikan ke tagihan lain yang belum lunas.
            </p>
        </div>
    </div>
</div>

<!-- Modal Aturan -->
<div id="mOv" style="position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; display:none; align-items:center; justify-content:center; padding: 20px;">
    <div style="background:var(--z-bg); width: 100%; max-width: 500px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow:hidden;">
        <div style="padding: 15px 20px; border-bottom: 1px solid var(--z-border); display: flex; justify-content: space-between; align-items: center;">
            <h3 id="mTitle" style="margin:0; font-size: 1.1rem; color: var(--z-text); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="settings"></i> Form Aturan
            </h3>
            <button type="button" onclick="tutupModal()" style="background:none; border:none; cursor:pointer; color:var(--z-muted);"><i data-lucide="x"></i></button>
        </div>
        <form action="/keuangan/kolektif/setting/save" method="POST" id="formAturan">
            <div style="padding: 20px; max-height: 70vh; overflow-y: auto;">
                <div class="z-form-group">
                    <label class="z-label">Nama Tagihan</label>
                    <select name="nama_tagihan" id="inputNama" class="z-field" required>
                        <option value="">-- Pilih Jenis Tagihan --</option>
                        <?php foreach($kategori as $k): ?>
                            <option value="<?php echo htmlspecialchars($k['nama_kategori']); ?>"><?php echo htmlspecialchars($k['nama_kategori']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="hidden" name="tipe" value="Persentase">
                <div class="z-form-group" style="margin-bottom: 20px;">
                    <label class="z-label">Persentase Potongan (%)</label>
                    <input type="number" step="0.01" name="nilai" id="inputNilai" class="z-field" placeholder="Contoh: 50" required>
                    <div style="font-size: 0.8rem; color: var(--z-muted); margin-top: 8px; line-height: 1.4;">
                        - Isi angka persen (misal: <b>50</b> berarti tagihan ini akan dipotong sebesar 50% dari total uang titipan).<br>
                        - <b style="color:var(--z-primary);">Total Kuota Terpakai: <?php echo $totalPersen; ?>% | Sisa Kuota Saat Ini: <?php echo $sisaKuota; ?>%</b>
                    </div>
                </div>
            </div>
            <div style="padding: 15px 20px; border-top: 1px solid var(--z-border); background: var(--z-bg); display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-ghost" onclick="tutupModal()">Batal</button>
                <button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:14px;height:14px;"></i> Simpan Aturan</button>
            </div>
        </form>
    </div>
</div>

<script>
function bukaModal() {
    var m = document.getElementById('mOv');
    if(m && m.parentElement !== document.body) document.body.appendChild(m);
    document.getElementById('formAturan').reset();
    document.getElementById('mTitle').innerHTML = '<i data-lucide="settings"></i> Tambah Aturan';
    if(typeof lucide !== 'undefined') lucide.createIcons();
    m.style.display = 'flex';
}

function tutupModal() {
    document.getElementById('mOv').style.display = 'none';
}

function editAturan(nama, nilai) {
    bukaModal();
    document.getElementById('mTitle').innerHTML = '<i data-lucide="edit-3"></i> Edit Aturan';
    document.getElementById('inputNama').value = nama;
    document.getElementById('inputNilai').value = nilai;
    if(typeof lucide !== 'undefined') lucide.createIcons();
}

function hapusAturan(id, nama) {
    if(typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Aturan?',
            text: "Aturan kolektif untuk tagihan '" + nama + "' akan dihapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/keuangan/kolektif/setting/delete/' + id;
            }
        });
    } else {
        if(confirm("Hapus aturan kolektif untuk tagihan '" + nama + "'?")) {
            window.location.href = '/keuangan/kolektif/setting/delete/' + id;
        }
    }
}
</script>
