<?php
use App\Core\Helper;

$instName = $institusi['nama'] ?? 'MTs ROUDLOTUS SHOLIHIN';
$yayasan = $institusi['yayasan'] ?? 'YAYASAN PENDIDIKAN ISLAM ROUDLOTUS SHOLIHIN';
$alamat = $institusi['alamat'] ?? '';
$desa = $institusi['desa'] ?? '';
$kec = $institusi['kecamatan'] ?? '';
$kota = $institusi['kota'] ?? 'Lampung';
$namaKepala = $institusi['nama_kepala'] ?? 'Kepala Madrasah';
$nipKepala = $institusi['nip_kepala'] ?? '-';
$logo = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
$faviconUrl = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');

$isPeserta = ($tipe !== 'pengawas');
$judulDoc = $isPeserta ? 'TATA TERTIB PESERTA UJIAN MADRASAH' : 'TATA TERTIB PENGAWAS RUANG UJIAN MADRASAH';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $judulDoc; ?> | <?php echo htmlspecialchars($instName); ?></title>
    <link rel="icon" type="image/png" href="<?php echo $faviconUrl; ?>">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #e2e8f0;
            margin: 0;
            padding: 20px;
        }

        .print-btn-bar {
            max-width: 210mm;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            font-family: sans-serif;
        }

        .print-btn {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .sheet {
            width: 210mm;
            min-height: 297mm;
            padding: 12mm 18mm;
            margin: 0 auto 20px auto;
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 16px;
        }

        .kop-surat img {
            width: 75px;
            height: 75px;
            object-fit: contain;
            margin-right: 15px;
        }

        .kop-text {
            flex: 1;
            text-align: center;
            line-height: 1.2;
        }

        .kop-text h2 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-text h1 {
            margin: 2px 0;
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-text p {
            margin: 2px 0;
            font-size: 8.5pt;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .doc-title h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .rules-list {
            font-size: 11pt;
            line-height: 1.75;
            text-align: justify;
            padding-left: 25px;
            margin-bottom: 35px;
        }

        .rules-list li {
            margin-bottom: 8px;
        }

        .ttd-box {
            display: flex;
            justify-content: flex-end;
            font-size: 11pt;
            page-break-inside: avoid;
        }

        .ttd-column {
            text-align: center;
            width: 65mm;
        }

        @media print {
            body { background: white; padding: 0; }
            .print-btn-bar { display: none; }
            .sheet {
                box-shadow: none;
                margin: 0;
                width: 100%;
                min-height: auto;
                padding: 10mm 15mm;
            }
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <div>
            <strong><?php echo $judulDoc; ?></strong>
        </div>
        <button class="print-btn" onclick="window.print()">
            &#128424; Cetak Dokumen (A4)
        </button>
    </div>

    <div class="sheet">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <img src="<?php echo $logo; ?>" alt="Logo">
            <div class="kop-text">
                <h2><?php echo htmlspecialchars($yayasan); ?></h2>
                <h1><?php echo htmlspecialchars($instName); ?></h1>
                <p><?php echo htmlspecialchars($alamat . ($desa ? ', ' . $desa : '') . ($kec ? ', Kec. ' . $kec : '') . ($kota ? ', ' . $kota : '')); ?></p>
            </div>
        </div>

        <div class="doc-title">
            <h3><?php echo $judulDoc; ?></h3>
            <div style="font-size: 10.5pt; font-weight: bold; margin-top: 3px;">TAHUN PELAJARAN 2026/2027</div>
        </div>

        <?php if ($isPeserta): ?>
        <ol class="rules-list">
            <li>Peserta ujian memasuki ruangan setelah tanda masuk dibunyikan, yakni <strong>15 (lima belas) menit</strong> sebelum ujian dimulai.</li>
            <li>Peserta yang terlambat hadir hanya diperkenankan mengikuti ujian setelah mendapat izin dari Ketua Panitia Ujian, tanpa penambahan waktu pengerjaan.</li>
            <li>Peserta ujian wajib membawa dan mengenakan <strong>Kartu Pelajar / Kartu Peserta Ujian</strong> yang sah.</li>
            <li>Peserta dilarang membawa alat komunikasi elektronik (HP, Smartwatch, Tablet) berkamera atau berinternet ke dalam ruang ujian, kecuali ditentukan lain oleh panitia pada ujian berbasis CBT/Online.</li>
            <li>Peserta duduk di tempat yang telah ditentukan sesuai dengan <strong>Nomor Meja</strong> masing-masing dan meletakkan Kartu Pelajar di atas meja sebelah kanan.</li>
            <li>Peserta mengisi dan menandatangani lembar <strong>Daftar Hadir Peserta</strong> dengan menggunakan pulpen bertinta hitam atau biru.</li>
            <li>Selama ujian berlangsung, peserta dilarang menanyakan jawaban soal kepada siapa pun, bekerja sama, menyontek, atau saling meminjam alat tulis.</li>
            <li>Peserta yang memerlukan penjelasan mengenai teks soal yang kurang jelas hanya boleh bertanya kepada Pengawas Ruang dengan mengangkat tangan terlebih dahulu.</li>
            <li>Peserta yang telah selesai mengerjakan soal sebelum waktu habis diperkenankan meninggalkan ruangan setelah menyerahkan lembar pengerjaan kepada pengawas dengan tertib.</li>
            <li>Peserta yang melanggar tata tertib ujian akan diberi peringatan/teguran oleh pengawas, dan jika mengulanginya dapat dinyatakan tidak lulus pada mata pelajaran terkait.</li>
        </ol>
        <?php else: ?>
        <ol class="rules-list">
            <li>Pengawas ruang telah hadir di ruang sekretariat panitia ujian selambat-lambatnya <strong>20 (dua puluh) menit</strong> sebelum ujian dimulai.</li>
            <li>Pengawas ruang menerima berkas naskah soal, lembar jawaban, daftar hadir, dan lembar Berita Acara Pelaksanaan Ujian dari panitia.</li>
            <li>Pengawas masuk ruang ujian 15 menit sebelum waktu ujian dimulai, memeriksa kerapian ruangan dan memandu doa bersama peserta.</li>
            <li>Pengawas memeriksa kesesuaian Kartu Pelajar peserta dengan daftar hadir resmi dan nomor meja.</li>
            <li>Pengawas membacakan tata tertib ujian kepada peserta dan membuka amplop soal di depan peserta ujian.</li>
            <li>Pengawas mengedarkan daftar hadir peserta dan memverifikasi tanda tangan seluruh peserta yang hadir.</li>
            <li>Pengawas mengisi dan menandatangani <strong>Berita Acara Pelaksanaan Ujian</strong> secara cermat, mencatat jumlah peserta hadir, absen, dan catatan kejadian penting selama ujian.</li>
            <li>Selama ujian berlangsung, pengawas dilarang merokok, memainkan gawai/HP, membaca buku, mengobrol, atau melakukan kegiatan yang mengganggu konsentrasi peserta.</li>
            <li>Setelah waktu ujian selesai, pengawas mengumpulkan dan menghitung seluruh lembar pengerjaan peserta agar sesuai dengan jumlah peserta yang hadir.</li>
            <li>Pengawas menyerahkan seluruh berkas ujian (naskah, daftar hadir, dan berita acara) kembali ke sekretariat Panitia Ujian Madrasah.</li>
        </ol>
        <?php endif; ?>

        <div class="ttd-box">
            <div class="ttd-column">
                <?php echo htmlspecialchars($kota); ?>, <?php echo date('d F Y'); ?><br>
                Kepala Madrasah,<br><br><br><br>
                <strong><u><?php echo htmlspecialchars($namaKepala); ?></u></strong><br>
                NIP. <?php echo htmlspecialchars($nipKepala); ?>
            </div>
        </div>
    </div>

</body>
</html>
