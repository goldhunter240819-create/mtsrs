<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Tagihan Nilai Susulan Syarat PAS</title>
    <?php 
        $logoUrl = !empty($institusi['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $institusi['logo']) : \App\Core\Helper::url('/assets/images/logo.png'); 
    ?>
    <link rel="icon" type="image/png" href="<?php echo $logoUrl; ?>">
    <style>
        @page {
            size: 21.59cm 33.02cm portrait; /* F4 Folio portrait */
            margin: 1.2cm 1cm 1cm 1cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .slip-item {
            page-break-after: always;
            margin-bottom: 25px;
        }
        .slip-item:last-child {
            page-break-after: auto;
        }

        /* Kop Surat Standard */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .kop-surat .logo {
            width: 60px;
            height: auto;
            margin-right: 15px;
        }
        .kop-surat .kop-text {
            flex: 1;
            text-align: center;
            padding-right: 75px;
        }
        .kop-surat .kop-text h2 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-surat .kop-text h1 {
            margin: 2px 0 0 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-surat .kop-text p {
            margin: 2px 0 0 0;
            font-size: 10.5px;
        }

        .title {
            text-align: center;
            font-size: 13.5px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 8px 0 3px 0;
            text-decoration: underline;
        }
        .subtitle {
            text-align: center;
            font-size: 11px;
            margin-bottom: 12px;
            font-style: italic;
        }

        .alert-box {
            background: #fff5f5;
            border: 1px solid #feb2b2;
            padding: 8px 12px;
            margin-bottom: 12px;
            font-size: 11px;
            line-height: 1.4;
        }

        .info-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 11.5px;
        }
        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
        }
        .data-table th {
            text-align: center;
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .signature-section {
            width: 100%;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 11.5px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 220px;
            text-align: center;
        }
        .signature-space {
            height: 55px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="text-align: center; margin: 15px 0; background: #f8f9fa; padding: 12px; border: 1px solid #ddd;">
        <button onclick="window.print()" style="padding: 8px 20px; font-size: 14px; cursor: pointer; background: #dc2626; color: #fff; border: none; border-radius: 6px; font-weight: bold;">
            Cetak Lembar Tagihan
        </button>
        <button onclick="window.close()" style="padding: 8px 15px; font-size: 14px; cursor: pointer; background: #64748b; color: #fff; border: none; border-radius: 6px; margin-left: 8px;">
            Tutup
        </button>
        <p style="margin: 5px 0 0 0; font-size: 11px; color: #666;">
            Total <strong><?= count($tagihan_siswa) ?> siswa</strong> memiliki tagihan nilai belum lengkap.
        </p>
    </div>

    <?php if (empty($tagihan_siswa)): ?>
    <div style="text-align: center; padding: 50px 20px;">
        <h2 style="color: #059669;">Alhamdulillah! Semua Nilai Lengkap 100%</h2>
        <p>Tidak ada siswa di kelas ini yang memiliki tagihan nilai kosong untuk syarat mengikuti PAS.</p>
    </div>
    <?php else: ?>

    <?php 
        $alamat = $institusi['alamat'] ?? '';
        $desa = !empty($institusi['desa']) ? 'Ds. ' . $institusi['desa'] : '';
        $kecamatan = !empty($institusi['kecamatan']) ? 'Kec. ' . $institusi['kecamatan'] : '';
        $kota = !empty($institusi['kota']) ? 'Kab. ' . $institusi['kota'] : (!empty($institusi['kabupaten']) ? 'Kab. ' . $institusi['kabupaten'] : '');
        $provinsi = !empty($institusi['provinsi']) ? 'Prov. ' . $institusi['provinsi'] : '';
        $kodepos = !empty($institusi['kodepos']) ? 'KP. ' . $institusi['kodepos'] : '';
        
        $fullAddressArray = array_filter([$alamat, $desa, $kecamatan, $kota, $provinsi, $kodepos]);
        $fullAddress = implode(', ', $fullAddressArray);

        $kls = $pasData['kelas'];
    ?>

    <?php foreach ($tagihan_siswa as $s): ?>
    <div class="slip-item">
        <!-- KOP SURAT -->
        <div class="kop-surat">
            <img src="<?= $logoUrl ?>" alt="Logo" class="logo">
            <div class="kop-text">
                <h2><?= htmlspecialchars($institusi['yayasan'] ?? '') ?></h2>
                <h1><?= htmlspecialchars($institusi['nama'] ?? '') ?></h1>
                <p><?= htmlspecialchars($fullAddress ?: 'Jl. Raya Pendidikan No. 123') ?></p>
                <p>Website: <?= htmlspecialchars($institusi['website'] ?? '-') ?> | Email: <?= htmlspecialchars($institusi['email'] ?? '-') ?></p>
            </div>
        </div>

        <div class="title">LEMBAR KENDALI TAGIHAN NILAI SUSULAN SISWA</div>
        <div class="subtitle">Syarat Wajib Kelayakan Mengikuti Penilaian Akhir Semester (PAS)</div>

        <div class="alert-box">
            <strong>Pemberitahuan:</strong> Berdasarkan rekapitulasi data akademik kurikulum, siswa yang bersangkutan saat ini <strong>BELUM DIPERBOLEHKAN</strong> mengikuti Penilaian Akhir Semester (PAS) karena terdapat <strong><?= $s['total_kosong'] ?> komponen nilai</strong> yang belum terisi (kelengkapan baru <?= $s['persen'] ?>% dari syarat mutlak 100%). Segera hubungi guru pengampu mata pelajaran terkait untuk menyelesaikan tugas / ujian susulan.
        </div>

        <table class="info-table">
            <tr>
                <td style="width: 16%;">Nama Lengkap</td>
                <td style="width: 2%;">:</td>
                <td style="width: 36%; font-weight: bold; font-size: 13px;"><?= htmlspecialchars($s['nama']) ?></td>

                <td style="width: 16%;">Kelas / Semester</td>
                <td style="width: 2%;">:</td>
                <td style="width: 28%; font-weight: bold;"><?= htmlspecialchars($kls['nama_kelas']) ?> / <?= htmlspecialchars($semester) ?></td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>:</td>
                <td><?= htmlspecialchars($s['nis']) ?></td>

                <td>Tahun Ajaran</td>
                <td>:</td>
                <td><?= htmlspecialchars($activeYear['name'] ?? $tahun_ajaran_id) ?></td>
            </tr>
            <tr>
                <td>Wali Kelas</td>
                <td>:</td>
                <td><?= htmlspecialchars($kls['wali_kelas']) ?></td>

                <td>Status PAS</td>
                <td>:</td>
                <td style="color: #b91c1c; font-weight: bold;">TIDAK DIPERBOLEHKAN (Tertahan)</td>
            </tr>
        </table>

        <!-- Tabel Tagihan Mapel -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Mata Pelajaran</th>
                    <th style="width: 110px;">Komponen Tagihan</th>
                    <th>Guru Pengampu</th>
                    <th style="width: 90px;">Status Nilai</th>
                    <th style="width: 140px;">Paraf Guru Pengampu</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($s['tagihan_kosong'] as $tk): ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($tk['mapel']) ?></strong></td>
                    <td style="text-align: center; font-weight: bold; color: #b91c1c;"><?= htmlspecialchars($tk['jenis']) ?></td>
                    <td><?= htmlspecialchars($tk['guru']) ?></td>
                    <td style="text-align: center; color: #b91c1c; font-weight: bold; font-size: 10px;">BELUM ADA NILAI</td>
                    <td style="height: 35px; vertical-align: bottom; text-align: center; font-size: 9px; color: #888;">
                        ( .................................... )
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="font-size: 10.5px; font-style: italic; color: #444; margin-top: 5px;">
            * Lembar kendali ini wajib diserahkan kembali ke Waka Kurikulum / Panitia PAS setelah seluruh guru pengampu membubuhkan paraf pengesahan nilai susulan.
        </div>

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-box">
                Mengetahui Orang Tua / Wali Siswa,
                <div class="signature-space"></div>
                ( .................................................... )
            </div>

            <div class="signature-box">
                Siswa yang bersangkutan,
                <div class="signature-space"></div>
                <strong><?= htmlspecialchars($s['nama']) ?></strong>
            </div>

            <div class="signature-box">
                <?= htmlspecialchars($institusi['kota'] ?? 'Madrasah') ?>, <?= date('d/m/Y') ?><br>
                Wali Kelas <?= htmlspecialchars($kls['nama_kelas']) ?>
                <div class="signature-space"></div>
                <strong><?= htmlspecialchars($kls['wali_kelas']) ?></strong>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php endif; ?>

</body>
</html>
