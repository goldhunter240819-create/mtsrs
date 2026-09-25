<div class="apk-vector-header" style="padding-bottom: 50px; background: linear-gradient(135deg, #1e40af, #3b82f6); flex-direction: column; justify-content: flex-start; gap: 20px;">
    <div class="apk-top-logo" style="width: 100%;">
        <a href="/apk/aplikasi-saya" style="color:white; text-decoration:none; display:flex; align-items:center; gap:8px;">
            <i data-lucide="arrow-left" style="width:20px;"></i>
        </a>
        <div style="margin-top: 5px;">
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Keuangan</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">BKU Bendahara</div>
        </div>
    </div>

    <!-- Saldo Box -->
    <div style="margin: 0; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); border-radius: 16px; padding: 15px 20px; text-align: center; backdrop-filter: blur(10px); width: 100%; box-sizing: border-box;">
        <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Saldo Kas Umum</div>
        <div style="font-size: 1.6rem; font-weight: 900; color: #fff; letter-spacing: -0.5px;">Rp <?= number_format($saldo, 0, ',', '.') ?></div>
        
        <button onclick="openDetailModal()" style="margin-top: 15px; padding: 6px 16px; border-radius: 20px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4); color: #fff; font-size: 0.7rem; font-weight: 700; cursor: pointer; transition: 0.2s;">
            Lihat Detail
        </button>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding-bottom: 100px;">

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div style="margin: 20px 20px 0 20px; padding: 14px; border-radius: 12px; font-weight: 600; font-size: 0.85rem;
            <?php echo ($_SESSION['flash_type'] ?? '') == 'success' 
                ? 'background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;' 
                : 'background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;'; ?>">
            <?php echo $_SESSION['flash_message']; ?>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        </div>
    <?php endif; ?>

    <style>
        .bku-card { background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 15px; border: 1px solid #f1f5f9; display: block; }
        .bku-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
        .bku-label { display: block; font-size: 0.7rem; font-weight: 700; color: #64748b; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
        .bku-input { width: 100%; padding: 10px 12px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: 0.9rem; font-family: inherit; color: #1e293b; transition: 0.2s; background: #fff; }
        .bku-input:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .bku-submit { width: 100%; padding: 12px; border-radius: 10px; border: none; font-size: 0.95rem; font-weight: 800; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s; }
        .bku-submit:hover { transform: translateY(-2px); }
        
        .radio-group { display: flex; gap: 10px; margin-bottom: 15px; }
        .radio-btn { flex: 1; text-align: center; padding: 12px; border-radius: 12px; border: 1.5px solid #e2e8f0; font-weight: 700; color: #64748b; cursor: pointer; transition: 0.2s; background: #f8fafc; }
        
        input[type="radio"][value="Pemasukan"]:checked + .radio-btn {
            background: #dcfce7; border-color: #22c55e; color: #166534;
        }
        input[type="radio"][value="Pengeluaran"]:checked + .radio-btn {
            background: #fee2e2; border-color: #ef4444; color: #991b1b;
        }
        
        .riwayat-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #e2e8f0; }
        .riwayat-item:last-child { border-bottom: none; }
    </style>

    <style>
        .bku-modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15,23,42,0.6); z-index: 999;
            opacity: 0; pointer-events: none; transition: 0.3s;
            backdrop-filter: blur(2px);
        }
        .bku-modal {
            position: fixed; top: 48%; left: 50%; transform: translate(-50%, -50%) scale(0.95);
            background: #fff; border-radius: 20px;
            padding: 20px; z-index: 1000; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-height: 85vh; overflow-y: auto;
            width: 90%; max-width: 400px;
            opacity: 0; pointer-events: none;
        }
        .bku-modal.open { transform: translate(-50%, -50%) scale(1); opacity: 1; pointer-events: auto; }
        .bku-modal-overlay.open { opacity: 1; pointer-events: auto; }
        .modal-close { position: absolute; top: 15px; right: 15px; width: 30px; height: 30px; border-radius: 50%; background: #f1f5f9; color: #64748b; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; transition: 0.2s; }
        .modal-close:hover { background: #e2e8f0; color: #1e293b; }
    </style>

    <!-- Tombol Buka Form (Minimalis Sleek) -->
    <div style="display: flex; gap: 12px; padding: 25px 20px 5px 20px;">
        <div onclick="openBkuModal('Pemasukan')" style="flex: 1; background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 14px; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: 0.2s; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
            <i data-lucide="arrow-down-left" style="width: 20px; height: 20px; color: #10b981;"></i>
            <span style="font-weight: 800; color: #334155; font-size: 0.9rem;">Pemasukan</span>
        </div>
        
        <div onclick="openBkuModal('Pengeluaran')" style="flex: 1; background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 14px; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: 0.2s; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
            <i data-lucide="arrow-up-right" style="width: 20px; height: 20px; color: #f43f5e;"></i>
            <span style="font-weight: 800; color: #334155; font-size: 0.9rem;">Pengeluaran</span>
        </div>
    </div>

    <!-- Centered Modal Form -->
    <div class="bku-modal-overlay" id="bkuOverlay" onclick="closeBkuModal()"></div>
    <div class="bku-modal" id="bkuSheet" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
        <button class="modal-close" onclick="closeBkuModal()"><i data-lucide="x" style="width:16px;"></i></button>
        <div id="modal_title" style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 15px; text-align: center; margin-top: 2px;">
            Input Transaksi BKU
        </div>
        
        <form action="/apk/bku/save" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="jenis" id="modal_jenis">

            <div style="margin-bottom: 10px;">
                <label class="bku-label">Kategori</label>
                <select name="kategori" id="modal_kategori" class="bku-input" required style="padding: 8px 12px;">
                    <option value="">- Pilih Kategori -</option>
                </select>
            </div>

            <div style="margin-bottom: 10px;">
                <label class="bku-label">Nominal (Rp)</label>
                <input type="text" name="jumlah" class="bku-input" placeholder="Contoh: 150000" onkeyup="formatRupiah(this)" required autocomplete="off" style="padding: 8px 12px;">
            </div>
            
            <div style="margin-bottom: 10px;">
                <label class="bku-label">Keterangan / Uraian</label>
                <input type="text" name="keterangan" class="bku-input" placeholder="Contoh: Beli alat kebersihan" required autocomplete="off" style="padding: 8px 12px;">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                <div>
                    <label class="bku-label">Tanggal</label>
                    <input type="date" name="tanggal" class="bku-input" value="<?= date('Y-m-d') ?>" required style="padding: 8px 10px;">
                </div>
                <div>
                    <label class="bku-label">Upload Bukti</label>
                    <input type="file" name="bukti" class="bku-input" accept="image/*" style="padding: 5px; font-size: 0.75rem;">
                </div>
            </div>

            <button type="submit" class="bku-submit" id="modal_btn">
                <i data-lucide="save" style="width:18px;"></i> Simpan Transaksi
            </button>
        </form>
    </div>

    <!-- Modal Detail Kategori -->
    <div class="bku-modal-overlay" id="detailOverlay" onclick="closeDetailModal()"></div>
    <div class="bku-modal" id="detailSheet" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background: #f0fdf4; border: 2px solid #bbf7d0;">
        <button class="modal-close" onclick="closeDetailModal()" style="background: #dcfce7; color: #166534;"><i data-lucide="x" style="width:16px;"></i></button>
        <div style="font-size: 1rem; font-weight: 800; color: #166534; margin-bottom: 15px; text-align: center; margin-top: 2px;">
            Rincian Saldo per Kategori
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <?php if(!empty($rincianKategori)): ?>
                <?php foreach($rincianKategori as $rk): ?>
                <div style="background: #16a34a; border-radius: 10px; padding: 12px 15px; display: flex; justify-content: space-between; align-items: center; color: #fff; box-shadow: 0 4px 6px rgba(22,163,74,0.2);">
                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;"><?= htmlspecialchars($rk['kategori']) ?></div>
                    <div style="font-size: 0.85rem; font-weight: 800;">Rp <?= number_format($rk['saldo'], 0, ',', '.') ?></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; color: #166534; font-size: 0.8rem; font-weight: 600; padding: 10px;">
                    Belum ada rincian data.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const dbCategories = `
            <?php foreach ($kategoriList ?? [] as $kat): ?>
                <option value="<?= htmlspecialchars($kat) ?>"><?= htmlspecialchars($kat) ?></option>
            <?php endforeach; ?>
        `;

        function openBkuModal(jenis) {
            let modal = document.getElementById('bkuSheet');
            let title = document.getElementById('modal_title');
            let inputJenis = document.getElementById('modal_jenis');
            let btn = document.getElementById('modal_btn');
            let catSelect = document.getElementById('modal_kategori');
            
            inputJenis.value = jenis;
            catSelect.innerHTML = '<option value="">- Pilih Kategori -</option>' + dbCategories;
            
            if (jenis === 'Pemasukan') {
                title.innerHTML = 'Input Pemasukan';
                title.style.color = '#166534';
                modal.style.background = '#f0fdf4'; // Light green background
                modal.style.border = '2px solid #bbf7d0';
                btn.style.background = 'linear-gradient(135deg, #22c55e, #16a34a)';
                btn.style.boxShadow = '0 4px 12px rgba(22,163,74,0.3)';
                btn.innerHTML = '<i data-lucide="save" style="width:18px;"></i> Simpan Pemasukan';
            } else {
                title.innerHTML = 'Input Pengeluaran';
                title.style.color = '#991b1b';
                modal.style.background = '#fef2f2'; // Light red background
                modal.style.border = '2px solid #fecaca';
                btn.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
                btn.style.boxShadow = '0 4px 12px rgba(220,38,38,0.3)';
                btn.innerHTML = '<i data-lucide="save" style="width:18px;"></i> Simpan Pengeluaran';
            }
            if(typeof lucide !== 'undefined') lucide.createIcons();

            document.getElementById('bkuOverlay').classList.add('open');
            document.getElementById('bkuSheet').classList.add('open');
        }
        function closeBkuModal() {
            document.getElementById('bkuOverlay').classList.remove('open');
            document.getElementById('bkuSheet').classList.remove('open');
        }

        function openDetailModal() {
            document.getElementById('detailOverlay').classList.add('open');
            document.getElementById('detailSheet').classList.add('open');
        }
        function closeDetailModal() {
            document.getElementById('detailOverlay').classList.remove('open');
            document.getElementById('detailSheet').classList.remove('open');
        }
    </script>

    <!-- Riwayat Transaksi -->
    <div class="bku-card">
        <div style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <div style="width:32px; height:32px; border-radius:8px; background:#f1f5f9; color:#64748b; display:flex; align-items:center; justify-content:center;">
                <i data-lucide="history" style="width:18px;"></i>
            </div>
            Riwayat Transaksi
        </div>

        <?php if(empty($riwayat)): ?>
            <div style="text-align:center; padding:20px; color:#94a3b8; font-size:0.9rem; font-weight:600;">
                Belum ada transaksi.
            </div>
        <?php else: ?>
            <?php 
            $grouped_riwayat = [];
            foreach($riwayat as $r) {
                $ket = $r['keterangan'];
                $nama_siswa = '';
                $nama_petugas = '';
                $is_auto = false;
                
                if (preg_match('/(Pembayaran Kas|Auto-Split)/i', $ket)) {
                    $is_auto = true;
                    if (strpos($ket, ': APK Kasir') !== false) {
                        preg_match('/\((.*?)\)/', $ket, $matches);
                        if (!empty($matches[1])) $ket = $matches[1];
                    } else {
                        $parts = explode(' - ', $ket);
                        if (count($parts) > 1) $ket = trim($parts[1]);
                    }

                    // Cari nama siswa dan petugas
                    if (!empty($r['siswa_id'])) {
                        // Jika siswa_id sudah ada di transaksi
                        $sName = $db->query("SELECT nama FROM siswa WHERE id = " . intval($r['siswa_id']))->fetchColumn();
                        $nama_siswa = $sName ?: '';
                        
                        // Cari petugas dari komite pembayaran jika ada
                        $stmtFind = $db->prepare("
                            SELECT g.nama as pnama 
                            FROM keuangan_komite_pembayaran p 
                            LEFT JOIN guru g ON p.petugas_id = g.user_id
                            WHERE p.tanggal_bayar = ? AND p.jumlah = ? AND p.siswa_id = ? AND ? LIKE CONCAT('%', p.jenis_pembayaran, '%') 
                            LIMIT 1
                        ");
                        $stmtFind->execute([$r['tanggal_bayar'], $r['jumlah'], $r['siswa_id'], $r['keterangan']]);
                        $match = $stmtFind->fetch(\PDO::FETCH_ASSOC);
                        if ($match && $match['pnama']) {
                            $nama_petugas = $match['pnama'];
                        } else {
                            $nama_petugas = 'Sistem';
                        }
                    } else {
                        // Jika siswa_id kosong (fallback murni)
                        $stmtFind = $db->prepare("
                            SELECT s.nama as snama, g.nama as pnama 
                            FROM keuangan_komite_pembayaran p 
                            JOIN siswa s ON p.siswa_id = s.id 
                            LEFT JOIN guru g ON p.petugas_id = g.user_id
                            WHERE p.tanggal_bayar = ? AND p.jumlah = ? AND ? LIKE CONCAT('%', p.jenis_pembayaran, '%') 
                            LIMIT 1
                        ");
                        $stmtFind->execute([$r['tanggal_bayar'], $r['jumlah'], $r['keterangan']]);
                        $match = $stmtFind->fetch(\PDO::FETCH_ASSOC);
                        if ($match) {
                            $nama_siswa = $match['snama'];
                            $nama_petugas = $match['pnama'] ?: 'Sistem';
                        }
                    }
                }

                if ($is_auto && !empty($nama_siswa)) {
                    // Grouping key: pisahkan auto-split dan manual
                    $isAutoSplit = (strpos($r['keterangan'], 'Auto-Split') !== false);
                    if ($isAutoSplit) {
                        $gkey = date('Y-m-d', strtotime($r['tanggal_bayar'])) . "_" . md5($nama_siswa) . "_" . md5($nama_petugas) . "_autosplit";
                    } else {
                        // Transaksi manual / lainnya dibiarkan berdiri sendiri menggunakan ID transaksi
                        $gkey = "manual_" . $r['id'];
                    }
                    if (!isset($grouped_riwayat[$gkey])) {
                        $r['keterangan'] = $ket; // simpan nama pertama
                        $r['nama_siswa'] = $nama_siswa;
                        $r['nama_petugas'] = $nama_petugas;
                        $r['is_grouped'] = false;
                        $grouped_riwayat[$gkey] = $r;
                    } else {
                        $grouped_riwayat[$gkey]['jumlah'] += $r['jumlah'];
                        $grouped_riwayat[$gkey]['keterangan'] = 'Pembayaran Multi-Tagihan (Auto-Split)';
                        $grouped_riwayat[$gkey]['is_grouped'] = true;
                    }
                } else {
                    $r['keterangan'] = $ket;
                    $r['nama_siswa'] = $nama_siswa;
                    $r['nama_petugas'] = $nama_petugas;
                    $grouped_riwayat[] = $r;
                }
            }
            ?>
            <?php foreach($grouped_riwayat as $r): ?>
                <div class="riwayat-item">
                    <div style="flex:1; padding-right:10px;">
                        <div style="font-weight: 700; color: #1e293b; font-size: 0.75rem; line-height: 1.3; text-transform: uppercase;">
                            <?= htmlspecialchars($r['keterangan']) ?>
                            <?php if(!empty($r['is_grouped'])): ?>
                                <span style="background: #e0e7ff; color: #4338ca; padding: 2px 6px; border-radius: 4px; font-size: 0.6rem; margin-left: 4px;">Paket</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($r['nama_siswa']): ?>
                            <div style="font-size: 0.75rem; font-weight: 600; color: #3b82f6; margin-top: 3px;"><?= htmlspecialchars($r['nama_siswa']) ?></div>
                            <div style="font-size: 0.65rem; color: #64748b; margin-top: 1px;">Diinput oleh: <?= htmlspecialchars($r['nama_petugas']) ?></div>
                        <?php endif; ?>
                        <div style="font-size: 0.65rem; color: #94a3b8; margin-top: 3px;">
                            <?= date('d M Y', strtotime($r['tanggal_bayar'])) ?> &bull; 
                            <span style="font-weight:600; color: <?= $r['jenis'] == 'Pemasukan' ? '#16a34a' : '#ef4444' ?>;">
                                <?= $r['jenis'] ?>
                            </span>
                        </div>
                    </div>
                    <div style="font-weight: 800; font-size: 0.9rem; color: <?= $r['jenis'] == 'Pemasukan' ? '#16a34a' : '#ef4444' ?>; flex-shrink: 0;">
                        <?= $r['jenis'] == 'Pemasukan' ? '+' : '-' ?>Rp<?= number_format($r['jumlah'], 0, ',', '.') ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

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
</script>
