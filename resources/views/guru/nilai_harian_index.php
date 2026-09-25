<div class="z-card" style="padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">Nilai Harian & Rekap Penilaian</h2>
    <p style="color: var(--z-muted); font-size: 0.95rem; margin: 0;">Input nilai harian atau evaluasi lainnya serta pantau riwayat penilaian yang telah Anda berikan.</p>
</div>

<?php if(isset($_SESSION['guru_msg'])): ?>
    <div style="background: <?= $_SESSION['guru_msg_type'] == 'success' ? '#ecfdf5' : '#fef2f2' ?>; border-left: 4px solid <?= $_SESSION['guru_msg_type'] == 'success' ? '#10b981' : '#ef4444' ?>; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <p style="margin: 0; color: <?= $_SESSION['guru_msg_type'] == 'success' ? '#047857' : '#b91c1c' ?>; font-weight: 500;">
            <?= htmlspecialchars($_SESSION['guru_msg']) ?>
        </p>
    </div>
    <?php unset($_SESSION['guru_msg']); unset($_SESSION['guru_msg_type']); ?>
<?php endif; ?>

<div class="z-card" style="margin-bottom: 30px; padding: 1.5rem;">
    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--z-text); margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="plus-circle" style="color: var(--z-primary);"></i> Form Input Nilai Baru
    </h3>
    
    <form action="/guru/nilai-harian/input" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
        <div>
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--z-text); font-size: 0.85rem;">Pilih Kelas <span style="color:red">*</span></label>
            <select name="kelas_id" required class="z-input">
                <option value="">-- Pilih Kelas --</option>
                <?php foreach ($kelas_mengajar as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--z-text); font-size: 0.85rem;">Pilih Mata Pelajaran <span style="color:red">*</span></label>
            <select name="mapel_id" required class="z-input">
                <option value="">-- Pilih Mata Pelajaran --</option>
                <?php foreach ($mapel_mengajar as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nama_mapel']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--z-text); font-size: 0.85rem;">Jenis Evaluasi <span style="color:red">*</span></label>
            <select name="jenis_evaluasi" id="jenis_evaluasi" required class="z-input" onchange="checkJenis()">
                <option value="PH">Penilaian Harian (Otomatis)</option>
                <option value="PTS">Penilaian Tengah Semester (PTS)</option>
                <option value="PAS">Penilaian Akhir Semester (PAS)</option>
                <option value="Praktik">Nilai Praktik / Keterampilan</option>
                <option value="Lainnya">Lainnya (Custom)</option>
            </select>
            <input type="text" name="jenis_evaluasi_custom" id="jenis_evaluasi_custom" class="z-input" style="display:none; margin-top: 10px;" placeholder="Tulis nama evaluasi...">
        </div>
        
        <div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem 2rem; font-weight: 700; border-radius: 8px; background: var(--z-primary); color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;" onclick="return submitInput(event, this.form)">
                <i data-lucide="arrow-right-circle"></i> Mulai Input Nilai
            </button>
        </div>
    </form>
    <p style="font-size: 0.8rem; color: var(--z-muted); margin-top: 15px; margin-bottom: 0;">
        <i data-lucide="info" style="width: 14px; display: inline-block; vertical-align: middle;"></i> Jika Anda memilih "Penilaian Harian (Otomatis)", sistem akan mencarikan urutan PH terakhir dan membuatkan urutan selanjutnya (Misal: PH 3 jika sudah ada PH 2).
    </p>
</div>

<div class="z-card" style="padding: 1.5rem;">
    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--z-text); margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="history" style="color: var(--z-primary);"></i> Riwayat Penilaian (Rekap)
    </h3>
    
    <?php if (empty($history)): ?>
        <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
            <i data-lucide="inbox" style="width: 40px; height: 40px; color: #94a3b8; margin-bottom: 10px;"></i>
            <p style="margin: 0; color: #64748b; font-weight: 500;">Belum ada data nilai harian yang Anda input.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="z-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Jenis Evaluasi</th>
                        <th>Jml Siswa dinilai</th>
                        <th>Tgl Terakhir Update</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($history as $h): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><span style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($h['nama_kelas']) ?></span></td>
                        <td><?= htmlspecialchars($h['nama_mapel']) ?></td>
                        <td>
                            <span style="background: #e0e7ff; color: #4338ca; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                <?= htmlspecialchars($h['jenis_evaluasi']) ?>
                            </span>
                        </td>
                        <td><?= $h['jml_siswa'] ?> Siswa</td>
                        <td><?= date('d M Y H:i', strtotime($h['last_updated'])) ?></td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 5px; justify-content: center;">
                                <a href="/guru/nilai-harian/input?kelas_id=<?= $h['kelas_id'] ?>&mapel_id=<?= $h['mapel_id'] ?>&jenis_evaluasi=<?= urlencode($h['jenis_evaluasi']) ?>" class="btn btn-primary" style="padding: 6px 10px; font-size: 0.8rem; border-radius: 8px; background: var(--z-primary); color: white; border: none; text-decoration: none;" title="Edit / Lihat Detail">
                                    <i data-lucide="edit-3" style="width: 14px; height: 14px; display:inline-block; vertical-align:middle;"></i> Edit
                                </a>
                                <form action="/guru/nilai-harian/delete" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA nilai untuk <?= $h['jenis_evaluasi'] ?> kelas <?= $h['nama_kelas'] ?>? Data tidak dapat dikembalikan.');">
                                    <input type="hidden" name="kelas_id" value="<?= $h['kelas_id'] ?>">
                                    <input type="hidden" name="mapel_id" value="<?= $h['mapel_id'] ?>">
                                    <input type="hidden" name="jenis_evaluasi" value="<?= $h['jenis_evaluasi'] ?>">
                                    <button type="submit" class="btn" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; border-radius: 8px; padding: 6px 10px; font-size: 0.8rem; cursor: pointer;" title="Hapus Data">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px; display:inline-block; vertical-align:middle;"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function checkJenis() {
    var val = document.getElementById('jenis_evaluasi').value;
    if (val === 'Lainnya') {
        document.getElementById('jenis_evaluasi_custom').style.display = 'block';
        document.getElementById('jenis_evaluasi_custom').setAttribute('required', 'required');
    } else {
        document.getElementById('jenis_evaluasi_custom').style.display = 'none';
        document.getElementById('jenis_evaluasi_custom').removeAttribute('required');
    }
}

function submitInput(e, form) {
    if(!form.kelas_id.value || !form.mapel_id.value) {
        alert("Harap pilih Kelas dan Mata Pelajaran terlebih dahulu!");
        e.preventDefault();
        return false;
    }
    
    if (form.jenis_evaluasi.value === 'Lainnya') {
        var customVal = form.jenis_evaluasi_custom.value;
        if (!customVal.trim()) {
            alert("Harap isi nama evaluasi!");
            e.preventDefault();
            return false;
        }
        form.jenis_evaluasi.value = customVal;
    }
    return true;
}
</script>
