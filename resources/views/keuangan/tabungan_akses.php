<div class="z-content-pad">

<div class="z-container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <div>
            <h2 style="margin:0; font-size:1.5rem; color:#1e293b;">Manajemen Hak Akses Admin Tabungan</h2>
            <p style="margin:5px 0 0; color:#64748b;">Atur guru/staf yang ditugaskan sebagai admin tabungan beserta wilayah kelasnya.</p>
        </div>
        <div>
            <button onclick="document.getElementById('modalTambah').style.display='flex'" style="background:#10b981; color:white; border:none; padding:10px 20px; border-radius:8px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:8px;">
                <i data-lucide="plus" style="width:18px;height:18px;"></i> Tambah Hak Akses
            </button>
        </div>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <?php if($_GET['msg']=='success'): ?>
            <div class="z-alert z-alert-success mb-4"><i data-lucide="check-circle"></i> Data berhasil disimpan.</div>
        <?php elseif($_GET['msg']=='error'): ?>
            <div class="z-alert z-alert-danger mb-4"><i data-lucide="x-circle"></i> Terjadi kesalahan saat menyimpan data.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div style="background:white; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05); overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; text-align:left;">
                <thead style="background:#f8fafc; border-bottom:1px solid #e2e8f0; color:#475569; font-size:0.85rem; text-transform:uppercase;">
                    <tr>
                        <th style="padding:15px;">No</th>
                        <th style="padding:15px;">Nama Guru / Staf</th>
                        <th style="padding:15px;">Kelas yang Dikelola</th>
                        <th style="padding:15px;">Akses Hapus</th>
                        <th style="padding:15px; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($admins)): ?>
                    <tr><td colspan="4" class="text-center text-muted">Belum ada admin tabungan yang ditambahkan.</td></tr>
                    <?php else: ?>
                    <?php $no=1; foreach($admins as $a): ?>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:15px; color:#64748b;"><?= $no++ ?></td>
                        <td style="padding:15px;"><strong><?= htmlspecialchars($a['nama']) ?></strong></td>
                        <td style="padding:15px;">
                            <?php if(empty($a['kelas_dipegang'])): ?>
                                <span style="background:#fff1f2; color:#e11d48; padding:5px 10px; border-radius:50px; font-size:0.75rem; font-weight:700;">Belum ada kelas</span>
                            <?php else: ?>
                                <span style="background:#ecfdf5; color:#059669; padding:5px 10px; border-radius:50px; font-size:0.75rem; font-weight:700;"><?= htmlspecialchars($a['kelas_dipegang']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:15px;">
                            <form action="/admin/tabungan/akses/toggle-hapus" method="POST" style="margin:0;">
                                <input type="hidden" name="user_id" value="<?= $a['id'] ?>">
                                <?php if(isset($a['can_delete']) && $a['can_delete'] == 1): ?>
                                    <button type="submit" style="background:#ecfdf5; color:#059669; border:none; padding:5px 10px; border-radius:50px; font-size:0.75rem; font-weight:700; cursor:pointer;" title="Klik untuk menonaktifkan"><i data-lucide="check" style="width:12px;height:12px;display:inline-block;"></i> Aktif</button>
                                <?php else: ?>
                                    <button type="submit" style="background:#fff1f2; color:#e11d48; border:none; padding:5px 10px; border-radius:50px; font-size:0.75rem; font-weight:700; cursor:pointer;" title="Klik untuk mengaktifkan"><i data-lucide="x" style="width:12px;height:12px;display:inline-block;"></i> Nonaktif</button>
                                <?php endif; ?>
                            </form>
                        </td>
                        <td style="padding:15px; text-align:center;">
                            <form action="/admin/tabungan/akses/hapus" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mencabut seluruh hak akses tabungan dari <?= htmlspecialchars($a['nama']) ?>?');" style="display:inline-block;">
                                <input type="hidden" name="user_id" value="<?= $a['id'] ?>">
                                <button type="submit" style="background:#fee2e2; color:#ef4444; border:none; width:32px; height:32px; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:center;" title="Cabut Akses"><i data-lucide="trash-2" style="width:16px;height:16px;"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Akses -->
<div id="modalTambah" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; width:90%; max-width:500px; border-radius:12px; overflow:hidden;">
        <div style="background:var(--z-primary); color:white; padding:15px; font-weight:bold; font-size:1.1rem; display:flex; justify-content:space-between;">
            <span>Tambah Hak Akses Admin</span>
            <i data-lucide="x" onclick="document.getElementById('modalTambah').style.display='none'" style="cursor:pointer;"></i>
        </div>
        <form action="/admin/tabungan/akses/simpan" method="POST" style="padding:20px;">
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Pilih Guru / Staf <span style="color:#ef4444;">*</span></label>
                <select name="user_id" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;" required>
                    <option value="">-- Pilih Guru --</option>
                    <?php foreach($gurus as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569;">Pilih Kelas yang Boleh Dikelola <span style="color:#ef4444;">*</span></label>
                <div style="max-height:200px; overflow-y:auto; border:1px solid #e2e8f0; border-radius:8px; padding:10px; display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <?php foreach($kelasList as $k): ?>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" name="kelas_ids[]" value="<?= $k['id'] ?>"> 
                        <span>Kelas <?= htmlspecialchars($k['nama_kelas']) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <small style="color:#64748b; font-size:0.8rem; margin-top:8px; display:block;">Anda bisa mencentang lebih dari satu kelas.</small>
            </div>
            <div style="margin-bottom:20px; padding:15px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:600; color:#475569;">
                    <input type="checkbox" name="can_delete" value="1" style="width:18px; height:18px;"> 
                    <span>Beri Akses Hapus Transaksi</span>
                </label>
                <small style="color:#64748b; font-size:0.8rem; margin-top:5px; display:block; margin-left:28px;">Centang ini jika petugas diizinkan untuk menghapus transaksi mutasi di Kasir Tabungan. Saldo siswa otomatis menyesuaikan jika transaksi dihapus.</small>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="document.getElementById('modalTambah').style.display='none'" style="background:#f1f5f9; color:#475569; border:none; padding:10px 15px; border-radius:8px; font-weight:600; cursor:pointer;">Batal</button>
                <button type="submit" style="background:#10b981; color:white; border:none; padding:10px 15px; border-radius:8px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:5px;"><i data-lucide="save" style="width:18px;height:18px;"></i> Simpan Akses</button>
            </div>
        </form>
    </div>
</div>
</div> <!-- /z-scroll -->

</div>