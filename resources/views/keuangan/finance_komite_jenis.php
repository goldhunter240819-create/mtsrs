<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="receipt"></i> Master Jenis Tagihan</h1>
        <p class="mph-subtitle">Kelola daftar tarif baku untuk SPP atau tagihan komite</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        <button type="button" class="btn btn-danger" id="btnBulkDelete" style="display:none;" onclick="submitBulkDelete()"><i data-lucide="trash-2" style="width:14px;height:14px;"></i>Hapus Terpilih</button>
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById('mOv').classList.add('open')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Jenis Tagihan</button>
    </div>
</div>
<div class="z-content-pad">


<div class="z-panel">
    <div class="z-panel-head" style="flex-wrap: wrap; gap: 10px; justify-content: space-between;">
        <div class="z-panel-title"><i data-lucide="tags"></i>Daftar Jenis Tagihan</div>
        <div style="display:flex; gap:10px; align-items:center;">
            <select id="filter_tahun" class="z-field" style="padding: 6px 12px; width: auto; font-size: 0.85rem;" onchange="filterTable()">
                <option value="">-- Semua Tahun --</option>
                <?php foreach($tahunAjaranList as $ta): ?>
                    <option value="<?php echo htmlspecialchars($ta['name']); ?>"><?php echo htmlspecialchars($ta['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filter_kategori" class="z-field" style="padding: 6px 12px; width: auto; font-size: 0.85rem;" onchange="filterTable()">
                <option value="">-- Semua Kategori --</option>
                <?php foreach($kategoriList as $k): ?>
                    <option value="<?php echo htmlspecialchars($k['kategori']); ?>"><?php echo htmlspecialchars($k['kategori']); ?></option>
                <?php endforeach; ?>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>
    </div>
    <div class="z-table-wrap">
        <form id="bulkForm" action="/keuangan/komite/jenis/bulk-delete" method="POST">
        
        <div id="cards_container">
            <?php $current_group = ''; ?>
            <?php $is_first = true; ?>
            <?php foreach($jenisList as $j): ?>
                <?php 
                $kategori_nama = isset($j['kategori']) && !empty($j['kategori']) ? htmlspecialchars($j['kategori']) : 'Lainnya';
                $group_name = ($j['nama_tahun'] ?: '-') . ' - ' . $kategori_nama; 
                if ($current_group !== $group_name): 
                    if (!$is_first): 
                        echo '</tbody></table></div></div>'; // Close previous card
                    endif;
                    $current_group = $group_name;
                    $is_first = false;
                ?>
                <div class="tagihan-card" data-tahun="<?php echo htmlspecialchars(strtolower($j['nama_tahun'] ?: '-')); ?>" data-kategori="<?php echo htmlspecialchars(strtolower($kategori_nama)); ?>" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden;">
                    <div style="background: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center;">
                        <i data-lucide="folder" style="width: 18px; height: 18px; margin-right: 10px; color: #64748b;"></i>
                        <div style="font-weight: 700; color: #334155; font-size: 1rem;">
                            Tahun Ajaran: <span style="color:#0ea5e9"><?php echo $j['nama_tahun'] ?: '-'; ?></span> 
                            <span style="margin: 0 10px; color: #cbd5e1;">|</span> 
                            Kategori: <span style="color:#f59e0b"><?php echo $kategori_nama; ?></span>
                        </div>
                    </div>
                    <div style="padding: 0; overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                            <thead>
                                <tr style="background: #fff; border-bottom: 1px solid #f1f5f9; text-align: left;">
                                    <th style="width:40px; text-align:center; padding: 12px 15px; border-bottom: 2px solid #e2e8f0; color: #64748b;"><input type="checkbox" onclick="checkAll(this)"></th>
                                    <th style="padding: 12px 15px; border-bottom: 2px solid #e2e8f0; font-size: 0.85rem; color: #64748b; text-transform: uppercase;">Nama Tagihan</th>
                                    <th style="padding: 12px 15px; border-bottom: 2px solid #e2e8f0; font-size: 0.85rem; color: #64748b; text-transform: uppercase;">Periode</th>
                                    <th style="padding: 12px 15px; border-bottom: 2px solid #e2e8f0; font-size: 0.85rem; color: #64748b; text-transform: uppercase;">Sifat</th>
                                    <th style="padding: 12px 15px; border-bottom: 2px solid #e2e8f0; font-size: 0.85rem; color: #64748b; text-transform: uppercase;">Target</th>
                                    <th style="padding: 12px 15px; border-bottom: 2px solid #e2e8f0; font-size: 0.85rem; color: #64748b; text-transform: uppercase;">Nominal (Rp)</th>
                                    <th style="padding: 12px 15px; border-bottom: 2px solid #e2e8f0; font-size: 0.85rem; color: #64748b; text-transform: uppercase; text-align: right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                <?php endif; ?>
                <tr style="border-bottom: 1px solid #f1f5f9; transition: 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="text-align:center; padding: 12px 15px;"><input type="checkbox" name="ids[]" value="<?php echo $j['id']; ?>" class="chk-item" onchange="toggleBulkBtn()"></td>
                    <td style="font-weight:600; padding: 12px 15px; color: #1e293b;"><?php echo $j['nama_tagihan']; ?></td>
                    <td style="padding: 12px 15px;"><span class="pill pill-purple" style="font-size: 0.8rem; padding: 4px 10px; border-radius: 6px;"><?php echo $j['jenis_periode']; ?></span></td>
                    <td style="padding: 12px 15px;"><span class="pill <?php echo $j['sifat']=='Wajib'?'pill-blue':'pill-amber'; ?>" style="font-size: 0.8rem; padding: 4px 10px; border-radius: 6px;"><?php echo $j['sifat']; ?></span></td>
                    <td style="padding: 12px 15px; color: #475569; font-size: 0.9rem;">
                        <?php 
                        if($j['target_tipe']=='Semua'){ echo '<span style="color:#10b981; font-weight:600;"><i data-lucide="users" style="width:14px;height:14px;display:inline-block;vertical-align:text-bottom;margin-right:4px;"></i>Semua Siswa</span>'; }
                        else if($j['target_tipe']=='Kelas'){ echo 'Kelas: '.$j['nama_kelas']; }
                        else if($j['target_tipe']=='Siswa'){ echo 'Siswa: '.$j['nama_siswa']; }
                        else if($j['target_tipe']=='Individu'){ echo 'Per Individu'; }
                        ?>
                    </td>
                    <td class="z-amount" style="padding: 12px 15px; font-weight: 700; color: #1e293b; font-size: 0.95rem;">Rp <?php echo number_format($j['nominal'],0,',','.'); ?></td>
                    <td style="padding: 12px 15px; text-align: right;">
                        <?php if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])): ?>
                            <div style="display:flex; gap:5px; justify-content: flex-end;">
                                <button type="button" class="btn btn-primary btn-sm" style="padding: 6px 12px; font-size: 0.8rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(37,99,235,0.15);" onclick="openEditModalJenis('<?php echo $j['id']; ?>', '<?php echo htmlspecialchars($j['nama_tagihan'], ENT_QUOTES); ?>', '<?php echo $j['jenis_periode']; ?>', '<?php echo $j['sifat']; ?>', '<?php echo $j['nominal']; ?>', '<?php echo $j['target_tipe']; ?>', '<?php echo $j['target_kelas_id']; ?>', '<?php echo $j['target_siswa_id']; ?>', '<?php echo htmlspecialchars($kategori_nama, ENT_QUOTES); ?>', '<?php echo $j['tahun_ajaran_id']; ?>')">Edit</button>
                                <button type="button" class="btn btn-danger btn-sm" style="padding: 6px 12px; font-size: 0.8rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(220,38,38,0.15);" onclick="openDeleteModal('<?php echo $j['id']; ?>', '<?php echo htmlspecialchars($j['nama_tagihan'], ENT_QUOTES); ?>')">Hapus</button>
                            </div>
                        <?php else: ?>
                            <span class="pill pill-amber" style="font-size:10px;">Akses Terbatas</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(!empty($jenisList)): ?>
                </tbody></table></div></div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #94a3b8; background: #fff; border-radius: 12px; border: 1px dashed #cbd5e1;">
                    <i data-lucide="inbox" style="width: 48px; height: 48px; opacity: 0.5; margin-bottom: 10px; display: block; margin: 0 auto;"></i>
                    <p style="margin-top: 10px;">Belum ada jenis tagihan yang ditambahkan</p>
                </div>
            <?php endif; ?>
        </div>
        </form>
    </div>
</div>
</div>

<!-- Modal Tambah/Edit Jenis Tagihan -->
<div id="mOv" class="z-modal-ov">
    <div class="z-modal" style="max-width: 600px; max-height: 90vh; overflow-y: auto;">
        <div class="z-modal-head">
            <div class="z-modal-title" id="mTitle"><i data-lucide="tag"></i> Tambah Jenis Tagihan</div>
            <div class="z-modal-close" onclick="document.getElementById('mOv').classList.remove('open')"><i data-lucide="x"></i></div>
        </div>
        <form action="/keuangan/komite/jenis/save" method="POST">
            <div class="z-modal-body">
                <input type="hidden" name="jenis_id" id="j_id" value="">
                
                <div class="z-form-group">
                    <label class="z-label">Tahun Pelajaran</label>
                    <select name="tahun_ajaran_id" id="j_tahun" class="z-field" required onchange="generateNamaTagihan()">
                        <?php foreach($tahunAjaranList as $ta): ?>
                            <option value="<?php echo $ta['id']; ?>"><?php echo htmlspecialchars($ta['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="z-form-group">
                    <label class="z-label">Kategori Laporan</label>
                    <select name="kategori" id="j_kategori" class="z-field" required onchange="generateNamaTagihan()">
                        <option value="Lainnya">Lainnya</option>
                        <?php foreach($kategoriList as $k): ?>
                            <option value="<?php echo htmlspecialchars($k['kategori']); ?>"><?php echo htmlspecialchars($k['kategori']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="z-form-group">
                    <label class="z-label">Nama Tagihan</label>
                    <input type="text" name="nama_tagihan" id="j_nama" class="z-field" placeholder="Misal: SPP Juli 2026 / LKS Ganjil" required>
                </div>

                <div style="display:flex; gap:15px; margin-bottom:15px;">
                    <div style="flex:1;">
                        <label class="z-label">Periode</label>
                        <select name="jenis_periode" id="j_periode" class="z-field" required>
                            <option value="Bulanan">Bulanan</option>
                            <option value="Tahunan">Tahunan</option>
                            <option value="Sekali">Sekali (Non-Rutin)</option>
                        </select>
                    </div>
                    <div style="flex:1;">
                        <label class="z-label">Sifat</label>
                        <select name="sifat" id="j_sifat" class="z-field" required>
                            <option value="Wajib">Wajib</option>
                            <option value="Sukarela">Sukarela</option>
                        </select>
                    </div>
                </div>
                
                <div class="z-form-group">
                    <label class="z-label">Nominal Tagihan (Rp)</label>
                    <input type="text" name="nominal" id="j_nominal" class="z-field" required onkeyup="formatRupiah(this)" autocomplete="off">
                </div>
                
                <div class="z-form-group">
                    <label class="z-label">Target Penerima Tagihan</label>
                    <select name="target_tipe" id="j_target" class="z-field" required onchange="toggleTarget()">
                        <option value="Semua">Semua Siswa</option>
                        <option value="Kelas">Kelas Tertentu</option>
                        <option value="Siswa">Siswa Tertentu / Individu</option>
                    </select>
                </div>
                
                <div class="z-form-group" id="target_kelas_div" style="display:none;">
                    <label class="z-label">Pilih Kelas</label>
                    <select name="target_kelas_ids[]" id="j_target_kelas" class="z-field" multiple style="height: 120px;">
                        <?php foreach($kelasList as $k): ?>
                            <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small>Tahan CTRL / CMD untuk memilih lebih dari satu</small>
                </div>
                
                <div class="z-form-group" id="target_siswa_div" style="display:none;">
                    <label class="z-label">Pilih Siswa</label>
                    <div style="margin-bottom: 8px;">
                        <select id="filter_kelas_siswa" class="z-field" style="width: 100%; padding:8px; border:1px solid var(--z-border); border-radius:8px;" onchange="filterSiswaByKelas()">
                            <option value="">-- Filter: Semua Kelas --</option>
                            <?php foreach($kelasList as $k): ?>
                                <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <select name="target_siswa_ids[]" id="j_target_siswa" class="z-field" multiple style="height: 150px;">
                        <?php foreach($allSiswasRaw as $s): ?>
                            <option value="<?php echo $s['id']; ?>" data-kelas="<?php echo $s['kelas_id']; ?>"><?php echo htmlspecialchars($s['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small>Tahan CTRL / CMD untuk memilih lebih dari satu</small>
                </div>
                
            </div>
            <div class="z-modal-foot">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('mOv').classList.remove('open')">Batal</button>
                <button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:14px;height:14px;"></i> Simpan Jenis Tagihan</button>
            </div>
        </form>
    </div>
</div>

<script>
if (typeof window.formatRupiah !== 'function') {
    window.formatRupiah = function(angka, prefix) {
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
    };
}

function generateNamaTagihan() {
    var tahunSel = document.getElementById('j_tahun');
    var kategoriSel = document.getElementById('j_kategori');
    var namaInput = document.getElementById('j_nama');
    
    var tahunText = tahunSel.options[tahunSel.selectedIndex] ? tahunSel.options[tahunSel.selectedIndex].text : '';
    var kategoriText = kategoriSel.options[kategoriSel.selectedIndex] ? kategoriSel.options[kategoriSel.selectedIndex].text : '';
    
    if (kategoriText && tahunText && kategoriText !== 'Lainnya') {
        namaInput.value = kategoriText + ' ' + tahunText;
    }
}

function moveModal() {
    var m = document.getElementById('mOv');
    if(m && m.parentElement !== document.body) {
        document.body.appendChild(m);
    }
}
document.addEventListener('DOMContentLoaded', moveModal);

function filterTable() {
    var fTahun = document.getElementById('filter_tahun').value.toLowerCase();
    var fKat = document.getElementById('filter_kategori').value.toLowerCase();
    var cards = document.querySelectorAll('.tagihan-card');
    
    cards.forEach(function(card) {
        var thn = card.getAttribute('data-tahun');
        var kat = card.getAttribute('data-kategori');
        var matchThn = fTahun === "" || thn === fTahun;
        var matchKat = fKat === "" || kat === fKat;
        
        if(matchThn && matchKat) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function filterSiswaByKelas() {
    var kelasId = document.getElementById('filter_kelas_siswa').value;
    var sel = document.getElementById('j_target_siswa');
    for (var i = 0; i < sel.options.length; i++) {
        if (!kelasId || sel.options[i].getAttribute('data-kelas') === kelasId) {
            sel.options[i].style.display = 'block';
        } else {
            sel.options[i].style.display = 'none';
        }
    }
}

function toggleTarget() {
    var v = document.getElementById('j_target').value;
    document.getElementById('target_kelas_div').style.display = (v === 'Kelas') ? 'block' : 'none';
    document.getElementById('target_siswa_div').style.display = (v === 'Siswa' || v === 'Individu') ? 'block' : 'none';
}

function resetModal() {
    moveModal();
    document.getElementById('mTitle').innerHTML = '<i data-lucide="plus"></i> Tambah Jenis Tagihan';
    document.getElementById('j_id').value = '';
    document.getElementById('j_nama').value = '';
    document.getElementById('j_nominal').value = '';
    document.getElementById('j_target').value = 'Semua';
    document.getElementById('filter_kelas_siswa').value = '';
    filterSiswaByKelas();
    toggleTarget();
    if(typeof lucide !== 'undefined') lucide.createIcons();
}

function openEditModalJenis(id, nama, periode, sifat, nominal, target_tipe, target_kelas_id, target_siswa_id, kategori, tahun_id) {
    moveModal();
    document.getElementById('mTitle').innerHTML = '<i data-lucide="edit-3"></i> Edit Jenis Tagihan';
    document.getElementById('j_id').value = id;
    document.getElementById('j_nama').value = nama;
    document.getElementById('j_periode').value = periode;
    document.getElementById('j_sifat').value = sifat;
    document.getElementById('j_target').value = target_tipe;
    document.getElementById('j_kategori').value = kategori;
    
    var nomInput = document.getElementById('j_nominal');
    nomInput.value = nominal ? Math.round(parseFloat(nominal)) : '';
    formatRupiah(nomInput);
    if(tahun_id) document.getElementById('j_tahun').value = tahun_id;
    
    document.getElementById('filter_kelas_siswa').value = '';
    filterSiswaByKelas();
    toggleTarget();
    
    // Set multiselect for kelas
    var k_sel = document.getElementById('j_target_kelas');
    for(var i=0; i<k_sel.options.length; i++) k_sel.options[i].selected = false;
    if(target_tipe === 'Kelas' && target_kelas_id) {
        var k_arr = target_kelas_id.split(',');
        for(var i=0; i<k_sel.options.length; i++) {
            if(k_arr.includes(k_sel.options[i].value)) k_sel.options[i].selected = true;
        }
    }
    
    // Set multiselect for siswa
    var s_sel = document.getElementById('j_target_siswa');
    for(var i=0; i<s_sel.options.length; i++) s_sel.options[i].selected = false;
    if((target_tipe === 'Siswa' || target_tipe === 'Individu') && target_siswa_id) {
        var s_arr = target_siswa_id.split(',');
        for(var i=0; i<s_sel.options.length; i++) {
            if(s_arr.includes(s_sel.options[i].value)) s_sel.options[i].selected = true;
        }
    }
    
    document.getElementById('mOv').classList.add('open');
    if(typeof lucide !== 'undefined') lucide.createIcons();
}

function openDeleteModal(id, nama) {
    Swal.fire({
        title: 'Hapus Jenis Tagihan?',
        text: "Menghapus jenis tagihan '"+nama+"' akan menghapus seluruh tagihan yang belum dibayar. Tindakan ini tidak bisa dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/keuangan/komite/jenis/delete/' + id;
        }
    });
}

function checkAll(source) {
    var card = source.closest('.tagihan-card');
    var checkboxes = card ? card.querySelectorAll('.chk-item') : document.querySelectorAll('.chk-item');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = source.checked;
    }
    toggleBulkBtn();
}

function toggleBulkBtn() {
    var checked = document.querySelectorAll('.chk-item:checked').length;
    document.getElementById('btnBulkDelete').style.display = checked > 0 ? 'inline-block' : 'none';
}

function submitBulkDelete() {
    Swal.fire({
        title: 'Hapus Tagihan Terpilih?',
        text: "Jenis tagihan yang dipilih beserta seluruh tagihan siswa terkait (yang belum dibayar) akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus Semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('bulkForm').submit();
        }
    });
}
</script>

</div>