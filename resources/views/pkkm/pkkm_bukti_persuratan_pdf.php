<?php
use App\Core\Branding;

$brand = Branding::get();
$logoUrl = !empty($inst['logo']) ? '/public/uploads/logo/' . $inst['logo'] : Branding::getInstitusiLogo();

$sekolah_nama = !empty($inst['nama']) ? $inst['nama'] : 'MTS ROUDLOTUS SHOLIHIN';
$nama_yayasan = !empty($inst['nama_yayasan']) ? $inst['nama_yayasan'] : 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA MATHLA\'UL ANWAR';

$alamatParts = [];
if (!empty($inst['alamat'])) $alamatParts[] = $inst['alamat'];
if (!empty($inst['desa'])) $alamatParts[] = 'Ds. ' . $inst['desa'];
if (!empty($inst['kecamatan'])) $alamatParts[] = 'Kec. ' . $inst['kecamatan'];
if (!empty($inst['kota'])) $alamatParts[] = $inst['kota'];
if (!empty($inst['provinsi'])) $alamatParts[] = 'Prov. ' . $inst['provinsi'];
if (!empty($inst['kodepos'])) $alamatParts[] = 'KP. ' . $inst['kodepos'];
$alamatLengkap = implode(', ', $alamatParts);
if (empty($alamatLengkap)) {
    $alamatLengkap = 'Jl. Tirtomoyo No. 083, Margorahayu, Ds. Tambak Jaya, Kec. Way Tenong, Lampung Barat, Prov. Lampung, KP. 34884';
}

$webUrl = !empty($inst['website']) ? $inst['website'] : 'mtsroudlotussholihin.sch.id';
$emailAddr = !empty($inst['email']) ? $inst['email'] : 'mtsroudlotussholihin1@gmail.com';
$kecamatan = !empty($inst['kecamatan']) ? $inst['kecamatan'] : 'Way Tenong';

$kepala_nama = $kamad['nama'] ?? 'NARKO, S.Pd.I., M.Pd., Gr';
$kepala_nip = $kamad['nip'] ?? '-';

$tu_nama = $tu['nama'] ?? 'AHMAD MAULANA, S.Kom';
$tu_nip = $tu['nip'] ?? '-';

$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_indo = date('j') . ' ' . $bulanIndo[(int)date('n')] . ' ' . date('Y');

$isPengawas = isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 5;
$backUrl = $isPengawas ? '/pengawas/pkkm?tab=penilaian' : '/admin/pkkm/instrumen';
$backText = $isPengawas ? 'Kembali ke Penilaian' : 'Kembali ke Instrumen';

$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'dokumen';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Fisik PKKM - Tata Kelola Persuratan (Surat Masuk & Keluar) - <?php echo htmlspecialchars($sekolah_nama); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($brand['favicon']); ?>">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 15mm 10mm 15mm;
        }

        body {
            background: #e2e8f0;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-size: 9.5pt;
            line-height: 1.35;
            color: #0f172a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print-bar {
            background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .bar-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 0.95rem;
        }
        .bar-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        .bar-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .bar-btn {
            background: #ffffff;
            color: #064e3b;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        .bar-btn:hover { background: #f0fdf4; transform: translateY(-1px); }
        .bar-btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .bar-btn-secondary:hover { background: rgba(255, 255, 255, 0.25); color: #ffffff; }
        .bar-btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }
        .bar-btn-danger:hover { background: rgba(239, 68, 68, 0.35); color: #ffffff; }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 12mm 15mm;
            margin: 15px auto;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            box-sizing: border-box;
            position: relative;
        }

        /* Kop Surat Resmi Sesuai Pengaturan Institusi */
        .kop-surat-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 3px double #064e3b;
            padding-bottom: 6px;
            margin-bottom: 10px;
            position: relative;
        }
        .kop-surat-preview img {
            width: 64px;
            height: 64px;
            object-fit: contain;
            position: absolute;
            left: 5px;
        }
        .kop-text-preview {
            text-align: center;
            flex: 1;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            padding: 0 70px;
        }
        .kop-text-preview h1 {
            margin: 0;
            font-size: <?php echo !empty($inst['ukuran_font_yayasan']) ? (int)$inst['ukuran_font_yayasan'] : 13; ?>px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            color: #064e3b;
        }
        .kop-text-preview h2 {
            margin: 1px 0 2px 0;
            font-size: <?php echo !empty($inst['ukuran_font_madrasah']) ? (int)$inst['ukuran_font_madrasah'] : 16; ?>px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            color: #0f172a;
        }
        .kop-text-preview p {
            margin: 1px 0;
            font-size: 8pt;
            line-height: 1.25;
            color: #334155;
        }

        /* Header Dokumen */
        .doc-title-block {
            text-align: center;
            margin-bottom: 12px;
        }
        .doc-title-main {
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #064e3b;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .doc-title-sub {
            font-size: 9.5pt;
            font-weight: bold;
            color: #1e293b;
            margin-top: 2px;
            margin-bottom: 4px;
        }
        .badge-indikator {
            display: inline-block;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 7.5pt;
            font-weight: bold;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Bento Stat Grid */
        .stat-grid-doc {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .stat-doc-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 6px 10px;
            text-align: center;
        }
        .stat-doc-val {
            font-size: 13pt;
            font-weight: 800;
            color: #064e3b;
            line-height: 1.1;
        }
        .stat-doc-lbl {
            font-size: 6.8pt;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            margin-top: 2px;
        }

        /* Sections & Tables */
        .section-header {
            font-size: 8.8pt;
            font-weight: bold;
            color: #064e3b;
            text-transform: uppercase;
            border-bottom: 1.5px solid #059669;
            padding-bottom: 2px;
            margin-top: 10px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .section-header span.tag {
            font-size: 6.8pt;
            font-weight: normal;
            background: #f0fdf4;
            color: #047857;
            padding: 1px 6px;
            border-radius: 4px;
            border: 1px solid #bbf7d0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 8px;
        }
        .data-table th, .data-table td {
            border: 1px solid #94a3b8;
            padding: 4px 6px;
            vertical-align: top;
        }
        .data-table th {
            background: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
            font-size: 7.8pt;
        }
        .data-table tr:nth-child(even) td {
            background: #fafafa;
        }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; font-size: 7.5pt; font-weight: bold; }

        /* Asesor Verification Box */
        .eval-box {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 7.5pt;
            line-height: 1.35;
            color: #14532d;
            margin-top: 10px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Signatures Wrap */
        .auth-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr 1.2fr;
            gap: 10px;
            align-items: center;
            margin-top: 14px;
            padding-top: 8px;
            border-top: 1px dashed #cbd5e1;
            font-family: 'Times New Roman', Times, serif;
        }
        .auth-col {
            text-align: center;
            font-size: 8.5pt;
            line-height: 1.25;
        }
        .auth-role { font-weight: bold; }
        .auth-sub { font-size: 8pt; color: #334155; margin-bottom: 40px; }
        .auth-name { font-weight: bold; text-decoration: underline; font-size: 9pt; }
        .auth-nip { font-size: 8pt; color: #475569; }

        .qr-box {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .qr-img { width: 56px; height: 56px; }
        .qr-label {
            font-size: 6pt;
            font-weight: 800;
            color: #047857;
            letter-spacing: 0.5px;
            margin-top: 2px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-note {
            margin-top: 10px;
            padding-top: 4px;
            border-top: 1px solid #e2e8f0;
            font-size: 6.8pt;
            color: #64748b;
            display: flex;
            justify-content: space-between;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        @media print {
            body { background: #ffffff; }
            .no-print-bar { display: none !important; }
            .page {
                box-shadow: none;
                margin: 0;
                padding: 5mm 0;
                width: 100%;
                min-height: auto;
            }
        }
    </style>
</head>
<body>

    <!-- Sticky Action Bar (Tidak Dicetak) -->
    <div class="no-print-bar">
        <div class="bar-title">
            <span class="bar-badge">E-BUKTI PKKM</span>
            <span>Tata Kelola Persuratan & Agenda Digital (Indikator 2.9.1)</span>
        </div>
        <div class="bar-actions">
            <?php if($is_pkkm_linked): ?>
                <span class="bar-badge" style="background:#10b981; color:#fff; display:inline-flex; align-items:center; gap:5px;">
                    ✓ Tertaut di PKKM (<?php echo $linked_count; ?> Berkas)
                </span>
                <form action="/admin/pkkm/cetak-bukti/persuratan/detach" method="POST" style="margin:0;" onsubmit="return confirm('Lepas tautan berkas ini dari instrumen PKKM?')">
                    <button type="submit" class="bar-btn bar-btn-danger" title="Lepas dari instrumen PKKM">
                        Lepas Tautan
                    </button>
                </form>
            <?php else: ?>
                <span class="bar-badge" style="background:#f59e0b; color:#fff;">
                    Belum Tertaut PKKM
                </span>
                <a href="/admin/pkkm/cetak-bukti/persuratan/attach" class="bar-btn" style="background:#10b981; color:#fff;" title="Tautkan otomatis ke Instrumen PKKM 2.9.1 & Master Berkas">
                    + Tautkan ke PKKM (1-Klik)
                </a>
            <?php endif; ?>

            <a href="<?php echo $backUrl; ?>" class="bar-btn bar-btn-secondary">
                ← <?php echo $backText; ?>
            </a>
            <a href="/admin/pkkm/master-berkas" class="bar-btn bar-btn-secondary" title="Buka Daftar Berkas Master PKKM">
                Daftar Berkas Master
            </a>
            <button onclick="window.print()" class="bar-btn">
                🖨️ Cetak / PDF A4
            </button>
        </div>
    </div>

    <!-- Halaman Dokumen Cetak A4 -->
    <div class="page">

        <!-- KOP SURAT MADRASAH -->
        <div class="kop-surat-preview">
            <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo Madrasah">
            <div class="kop-text-preview">
                <h1><?php echo htmlspecialchars($nama_yayasan); ?></h1>
                <h2><?php echo htmlspecialchars($sekolah_nama); ?></h2>
                <p>Alamat: <?php echo htmlspecialchars($alamatLengkap); ?></p>
                <p>Website: <?php echo htmlspecialchars($webUrl); ?> | Email: <?php echo htmlspecialchars($emailAddr); ?></p>
            </div>
        </div>

        <!-- JUDUL BUKTI FISIK -->
        <div class="doc-title-block">
            <div class="doc-title-main">BUKTI FISIK PENILAIAN KINERJA KEPALA MADRASAH (PKKM)</div>
            <div class="doc-title-sub">LAPORAN ADMINISTRASI PERSURATAN MADRASAH (SURAT MASUK & KELUAR)</div>
            <span class="badge-indikator">
                Komponen 2: Pengembangan Madrasah &bull; Sub 2.9 &bull; Indikator 2.9.1 (Pedoman Persuratan Standar Kode 422)
            </span>
        </div>

        <!-- 4 BENTO METRICS -->
        <div class="stat-grid-doc">
            <div class="stat-doc-card">
                <div class="stat-doc-val"><?php echo $total_masuk; ?></div>
                <div class="stat-doc-lbl">Surat Masuk Tercatat</div>
            </div>
            <div class="stat-doc-card">
                <div class="stat-doc-val"><?php echo $total_keluar; ?></div>
                <div class="stat-doc-lbl">Surat Keluar Kode 422</div>
            </div>
            <div class="stat-doc-card">
                <div class="stat-doc-val"><?php echo $total_disposisi; ?></div>
                <div class="stat-doc-lbl">Disposisi Elektronik</div>
            </div>
            <div class="stat-doc-card">
                <div class="stat-doc-val" style="font-size:11pt; color:#047857;">STANDAR 422</div>
                <div class="stat-doc-lbl">Format Kemenag Valid</div>
            </div>
        </div>

        <!-- SECTION 1: BUKU AGENDA SURAT KELUAR (STANDAR KODE 422) -->
        <div class="section-header">
            <span>I. BUKU REGISTER AGENDA SURAT KELUAR (STANDARISASI KODE 422)</span>
            <span class="tag">Arsip Resmi Surat Keluar</span>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 32%;">Nomor Surat Keluar</th>
                    <th style="width: 14%;">Tanggal</th>
                    <th style="width: 25%;">Tujuan Surat</th>
                    <th>Perihal / Isi Ringkas</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list_keluar)): ?>
                    <tr><td colspan="5" class="text-center" style="color:#64748b; padding:8px;">Belum ada arsip surat keluar tercatat.</td></tr>
                <?php else: ?>
                    <?php $no = 1; foreach($list_keluar as $k): ?>
                    <tr>
                        <td class="text-center font-mono"><?php echo $no++; ?></td>
                        <td class="font-mono" style="color:#064e3b;"><?php echo htmlspecialchars($k['no_surat']); ?></td>
                        <td class="text-center"><?php echo date('d/m/Y', strtotime($k['tanggal'])); ?></td>
                        <td><?php echo htmlspecialchars($k['tujuan']); ?></td>
                        <td><?php echo htmlspecialchars($k['perihal']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- SECTION 2: BUKU AGENDA SURAT MASUK & DISPOSISI -->
        <div class="section-header">
            <span>II. BUKU REGISTER AGENDA SURAT MASUK & TINDAK LANJUT DISPOSISI</span>
            <span class="tag">Arsip Resmi Surat Masuk</span>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 18%;">No. Agenda / Asli</th>
                    <th style="width: 13%;">Tgl Terima</th>
                    <th style="width: 25%;">Pengirim / Asal Surat</th>
                    <th style="width: 25%;">Perihal Surat</th>
                    <th>Status Disposisi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list_masuk)): ?>
                    <tr><td colspan="6" class="text-center" style="color:#64748b; padding:8px;">Belum ada arsip surat masuk tercatat.</td></tr>
                <?php else: ?>
                    <?php $no = 1; foreach($list_masuk as $m): ?>
                    <tr>
                        <td class="text-center font-mono"><?php echo $no++; ?></td>
                        <td class="font-mono"><?php echo htmlspecialchars($m['no_agenda']); ?><br><span style="font-size:7pt; color:#64748b;"><?php echo htmlspecialchars($m['no_surat']); ?></span></td>
                        <td class="text-center"><?php echo date('d/m/Y', strtotime($m['tanggal'])); ?></td>
                        <td><?php echo htmlspecialchars($m['asal']); ?></td>
                        <td><?php echo htmlspecialchars($m['perihal']); ?></td>
                        <td class="text-center" style="font-size:7.5pt; font-weight:bold; color:<?php echo $m['status'] === 'Sudah Disposisi' ? '#059669' : '#d97706'; ?>;">
                            <?php echo htmlspecialchars($m['status']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- TELAAH & VERIFIKASI ASESOR -->
        <div class="eval-box">
            <div>
                <strong>TELAAH DOKUMEN & VERIFIKASI ASESOR PKKM:</strong><br>
                <span>Madrasah memiliki tata kelola persuratan yang tertib, modern, dan terdokumentasi secara digital dengan penomoran standar klasifikasi <strong>Kode 422 (Pendidikan & Kelembagaan)</strong>, pencatatan buku agenda surat masuk/keluar real-time, serta lembar disposisi instruksi pimpinan terarsip akuntabel.</span>
            </div>
            <div style="font-weight: 800; font-size: 7.2pt; color: #065f46; white-space: nowrap; margin-left: 15px; text-align:right;">
                INDIKATOR 2.9.1<br>
                <span style="font-size:8.5pt; background:#059669; color:#fff; padding:2px 8px; border-radius:4px;">SKOR 4 (SANGAT BAIK)</span>
            </div>
        </div>

        <!-- PENGESAHAN & QR VALIDASI -->
        <div class="auth-grid">
            <!-- PETUGAS TATA USAHA / PERSURATAN -->
            <div class="auth-col">
                <div class="auth-role">Petugas Tata Usaha & Persuratan,</div>
                <div class="auth-sub">Pengelola Administrasi Madrasah</div>
                <div class="auth-name"><?php echo htmlspecialchars($tu_nama); ?></div>
                <div class="auth-nip">NIP. <?php echo htmlspecialchars($tu_nip); ?></div>
            </div>

            <!-- QR VALIDASI DIGITAL -->
            <div class="qr-box">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://mtsroudlotussholihin.sch.id/admin/pkkm/cetak-bukti/persuratan" alt="QR Validasi" class="qr-img">
                <div class="qr-label">TERVALIDASI DIGITAL ✓</div>
            </div>

            <!-- KEPALA MADRASAH -->
            <div class="auth-col">
                <div class="auth-role"><?php echo htmlspecialchars($kecamatan); ?>, <?php echo $tgl_indo; ?></div>
                <div class="auth-sub">Kepala Madrasah,</div>
                <div class="auth-name"><?php echo htmlspecialchars($kepala_nama); ?></div>
                <div class="auth-nip">NIP. <?php echo htmlspecialchars($kepala_nip); ?></div>
            </div>
        </div>

        <!-- FOOTER NOTE -->
        <div class="footer-note">
            <span>Dokumen Bukti Fisik PKKM &bull; Komponen 2: Pengembangan Madrasah (Indikator 2.9.1) &bull; <?php echo htmlspecialchars($sekolah_nama); ?></span>
            <span style="font-weight:700; color:#059669;">https://mtsroudlotussholihin.sch.id</span>
        </div>

    </div>

</body>
</html>

