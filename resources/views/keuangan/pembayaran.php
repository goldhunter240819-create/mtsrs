<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="credit-card" style="color: #bfdbfe;"></i> Form Pembayaran Tagihan
        </h1>
        <p class="mph-subtitle">Proses pencatatan transaksi pembayaran SPP / Administrasi siswa dan cetak kwitansi.</p>
    </div>
</div>

<div class="z-panel" style="max-width: 600px; margin: 0 auto;">
    <div class="z-panel-head">
        <div class="z-panel-title"><i data-lucide="wallet"></i> Entry Pembayaran</div>
    </div>
    <div class="z-panel-body">
        <form action="/keuangan/pembayaran/process" method="POST">
            <div class="z-form-group">
                <label class="z-label">Pilih Tagihan Siswa</label>
                <select name="tagihan_id" id="select-tagihan" class="z-field" onchange="autoFillNominal(this)">
                    <option value="">-- Pilih Tagihan yang Akan Dibayar --</option>
                    <?php foreach($pendingTagihan as $pt): ?>
                        <?php $sisa = floatval($pt['nominal']) - floatval($pt['terbayar']); ?>
                        <option value="<?php echo $pt['id']; ?>" data-sisa="<?php echo $sisa; ?>" <?php echo ($tagihanDetail && $tagihanDetail['id'] == $pt['id']) ? 'selected' : ''; ?>>
                            [<?php echo htmlspecialchars($pt['nis']); ?>] <?php echo htmlspecialchars($pt['nama_siswa']); ?> — <?php echo htmlspecialchars($pt['nama_komponen']); ?> (Sisa: Rp <?php echo number_format($sisa,0,',','.'); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="z-form-group">
                <label class="z-label">Nominal Yang Dibayarkan (Rp)</label>
                <input type="number" name="nominal" id="input-nominal" class="z-field" placeholder="Masukkan jumlah uang bayar" required value="<?php echo $tagihanDetail ? (floatval($tagihanDetail['nominal']) - floatval($tagihanDetail['terbayar'])) : ''; ?>">
            </div>

            <div class="z-form-group">
                <label class="z-label">Metode Pembayaran</label>
                <select name="metode_pembayaran" class="z-field">
                    <option value="Tunai">Tunai / Cash</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="QRIS">QRIS / E-Wallet</option>
                    <option value="Tabungan Siswa">Potong Tabungan Siswa</option>
                </select>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding: 0.9rem;">
                    <i data-lucide="check-circle-2"></i> Proses Transaksi & Cetak Kwitansi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function autoFillNominal(selectEl) {
        var selectedOption = selectEl.options[selectEl.selectedIndex];
        var sisa = selectedOption.getAttribute('data-sisa');
        if (sisa) {
            document.getElementById('input-nominal').value = sisa;
        }
    }
</script>
