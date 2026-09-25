<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat Tagihan Resmi - <?php echo htmlspecialchars($siswa['nama']); ?></title>
    <?php if(!empty($institusi['logo'])): ?>
    <link rel="icon" type="image/png" href="/public/uploads/logo/<?php echo htmlspecialchars($institusi['logo']); ?>">
    <?php endif; ?>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            background: radial-gradient(circle at 50% 0%, #f0fdf4 0%, #f1f5f9 60%, #e2e8f0 100%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        .verify-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 24px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 25px 50px -12px rgba(6, 95, 70, 0.12);
            padding: 35px 30px;
            box-sizing: border-box;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .verify-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #10b981 0%, #059669 50%, #047857 100%);
        }

        /* Badge Status */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ecfdf5;
            border: 1.5px solid #a7f3d0;
            color: #047857;
            padding: 6px 14px;
            border-radius: 100px;
            font-size: clamp(0.55rem, 3vw, 0.75rem);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 25px;
            white-space: nowrap;
            animation: pulse-badge 2s infinite;
        }
        
        @keyframes pulse-badge {
            0%, 100% { box-shadow: 0 0 0 0px rgba(16, 185, 129, 0.2); }
            50% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0.05); }
        }

        .status-badge i {
            color: #10b981;
        }

        .school-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(0.9rem, 4.5vw, 1.6rem);
            font-weight: 700;
            color: #064e3b;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .school-subtitle {
            font-size: clamp(0.6rem, 3vw, 0.8rem);
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            margin-bottom: 25px;
            white-space: nowrap;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: #e2e8f0;
            margin: 20px 0;
        }



        /* Detail List */
        .detail-list {
            text-align: left;
            margin-bottom: 25px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px dashed #cbd5e1;
            gap: 6px;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-val {
            font-size: clamp(0.75rem, 4vw, 1.05rem);
            color: #0f172a;
            font-weight: 800;
            text-align: left;
            white-space: nowrap;
        }

        /* Tagihan Cards (Mobile Friendly) */
        .tagihan-cards {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin: 15px 0;
            text-align: left;
        }

        .tagihan-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .tagihan-card.total-card {
            background: #f1f5f9;
            border: 2px dashed #cbd5e1;
        }

        .tc-header {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
            width: 100%;
        }

        .tc-title {
            font-weight: 700;
            color: #1e293b;
            font-size: clamp(0.7rem, 3.5vw, 0.95rem);
            line-height: 1.4;
        }

        .tc-status {
            font-size: 0.7rem;
            font-weight: 800;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .tc-status.lunas { background: #dcfce7; color: #166534; }
        .tc-status.belum { background: #fee2e2; color: #991b1b; }

        .tc-body {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .tc-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .tc-item.tc-sisa {
            margin-top: 5px;
            padding-top: 8px;
            border-top: 1px dashed #cbd5e1;
        }

        .tc-label {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 600;
        }

        .tc-val {
            font-size: 0.95rem;
            font-weight: 700;
            color: #334155;
        }
        .tc-val.terbayar { color: #16a34a; }
        .tc-val.sisa-merah { color: #dc2626; font-size: 1.05rem; font-weight: 800; }
        .tc-val.sisa-hijau { color: #16a34a; font-size: 1.05rem; font-weight: 800; }

        /* Seal section */
        .seal-box {
            margin-top: 25px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            padding: 12px;
            border-radius: 12px;
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 600;
            line-height: 1.5;
            text-align: center;
        }

        .close-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
            transition: all 0.2s ease;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
        }

        .close-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
            filter: brightness(0.95);
        }

        .footer-credit {
            margin-top: 25px;
            font-size: 0.7rem;
            color: #94a3b8;
            font-weight: 500;
            letter-spacing: 0.3px;
        }
    </style>
</head>
<body>

<div class="verify-card">
    <!-- Status Badge -->
    <div class="status-badge">
        <i data-lucide="shield-check"></i>
        Dokumen Tagihan Resmi & Sah
    </div>

    <!-- Header info -->
    <h1 class="school-title"><?php echo htmlspecialchars($institusi['nama'] ?? 'MTs Roudlotus Sholihin'); ?></h1>
    <div class="school-subtitle">Status Terverifikasi Sistem</div>
    
    <div class="divider"></div>

    <!-- Details -->
    <div class="detail-list">
        <div class="detail-item">
            <span class="detail-label">Nama Siswa</span>
            <span class="detail-val"><?php echo htmlspecialchars($siswa['nama']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">NIS Siswa</span>
            <span class="detail-val"><?php echo htmlspecialchars($siswa['nis']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Kelas</span>
            <span class="detail-val"><?php echo htmlspecialchars($siswa['kelas']); ?></span>
        </div>
    </div>

    <div class="divider"></div>

    <!-- Table Details -->
    <h4 style="text-align: left; margin: 10px 0 5px 0; color: #334155; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Rincian Status Tagihan</h4>
    
    <div class="tagihan-cards">
        <?php foreach($tagihanList as $t): ?>
        <div class="tagihan-card">
            <div class="tc-header">
                <span class="tc-title"><?php echo htmlspecialchars($t['nama_tagihan']); ?></span>
                <span class="tc-status <?php echo $t['sisa'] > 0 ? 'belum' : 'lunas'; ?>">
                    <?php echo $t['sisa'] > 0 ? 'Belum Lunas' : 'Lunas'; ?>
                </span>
            </div>
            <div class="tc-body">
                <div class="tc-item">
                    <span class="tc-label">Tagihan</span>
                    <span class="tc-val">Rp <?php echo number_format($t['jumlah_tagihan'], 0, ',', '.'); ?></span>
                </div>
                <div class="tc-item">
                    <span class="tc-label">Terbayar</span>
                    <span class="tc-val terbayar">Rp <?php echo number_format($t['terbayar'], 0, ',', '.'); ?></span>
                </div>
                <div class="tc-item tc-sisa">
                    <span class="tc-label">Sisa</span>
                    <span class="tc-val <?php echo $t['sisa'] > 0 ? 'sisa-merah' : 'sisa-hijau'; ?>">
                        Rp <?php echo number_format($t['sisa'], 0, ',', '.'); ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <div class="tagihan-card total-card">
            <div class="tc-body">
                <div class="tc-item">
                    <span class="tc-label">Total Tagihan</span>
                    <span class="tc-val">Rp <?php echo number_format($totalTagihan, 0, ',', '.'); ?></span>
                </div>
                <div class="tc-item">
                    <span class="tc-label">Total Terbayar</span>
                    <span class="tc-val terbayar">Rp <?php echo number_format($totalTerbayar, 0, ',', '.'); ?></span>
                </div>
                <div class="tc-item tc-sisa">
                    <span class="tc-label">Total Sisa</span>
                    <span class="tc-val <?php echo $totalSisa > 0 ? 'sisa-merah' : 'sisa-hijau'; ?>">
                        Rp <?php echo number_format($totalSisa, 0, ',', '.'); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>



    <!-- Official Seal Box -->
    <div class="seal-box">
        <span>Lembar verifikasi digital ini diterbitkan secara otomatis oleh sistem administrasi keuangan resmi <?php echo htmlspecialchars($institusi['nama'] ?? 'MTs Roudlotus Sholihin'); ?>.</span>
    </div>

    <button onclick="window.close()" class="close-btn">Tutup Halaman</button>

    <div class="footer-credit">
        Powered by GH Smart School Platform &copy; <?php echo date('Y'); ?>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>
