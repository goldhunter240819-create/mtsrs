<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Cover Rekap Matriks Tabungan</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800;900&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f1f5f9;
        }
        .print-container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        @media print {
            @page { size: landscape; margin: 0; } 
            body { -webkit-print-color-adjust: exact; margin: 0; background: white; }
            .no-print { display: none !important; }
            html, body { width: 100%; height: 100%; overflow: hidden !important; }
            .print-container { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; }
        }
        
        .modern-cover {
            position: relative;
            background: #ffffff;
            overflow: hidden;
            box-sizing: border-box;
            color: #1e293b;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            margin: 0 auto;
            width: 330mm; /* F4 Landscape */
            height: 215mm;
            padding: 30px 40px;
        }
        
        @media print {
            .modern-cover {
                border: none;
                box-shadow: none;
                border-radius: 0;
                width: 100vw;
                height: 100vh;
                padding: 0;
                margin: 0;
            }
        }

        .modern-cover::before {
            content: '';
            position: absolute;
            top: -150px;
            left: -150px;
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #0ea5e9, #2563eb);
            border-radius: 50%;
            z-index: 0;
            opacity: 0.08;
        }
        .modern-cover::after {
            content: '';
            position: absolute;
            bottom: -200px;
            right: -100px;
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, #f59e0b, #ea580c);
            border-radius: 50%;
            z-index: 0;
            opacity: 0.08;
        }

        .cover-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .cover-header {
            text-align: center;
            margin-bottom: 30px;
            width: 100%;
        }
        .cover-header img {
            width: 110px;
            margin-bottom: 15px;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.15));
        }
        .cover-header .inst-name {
            font-size: 24px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 1.5px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .cover-header .inst-sub {
            font-size: 14px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 600;
        }

        .cover-title-area {
            text-align: center;
            margin: 20px 0;
            width: 100%;
            padding: 40px 0;
            background: linear-gradient(90deg, transparent, rgba(37,99,235,0.06), transparent);
            border-top: 3px solid #3b82f6;
            border-bottom: 3px solid #f59e0b;
        }

        div.dynamic-title {
            display: block;
            width: 100%;
            font-size: 52px;
            font-weight: 900;
            color: #1e3a8a;
            margin: 0 0 15px 0;
            letter-spacing: 4px;
            text-transform: uppercase;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.05);
            border: none;
            background: transparent;
            outline: none;
            text-align: center;
        }
        
        div.dynamic-subtitle {
            display: block;
            width: 100%;
            font-size: 26px;
            font-weight: 800;
            color: #f59e0b;
            margin: 0;
            letter-spacing: 3px;
            text-transform: uppercase;
            border: none;
            background: transparent;
            outline: none;
            text-align: center;
        }

        .cover-details {
            width: 60%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.9);
            padding: 25px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            border-left: 6px solid #3b82f6;
            backdrop-filter: blur(10px);
            margin-top: 20px;
        }
        
        .cover-details table {
            width: 100%;
            font-size: 18px;
            border-collapse: separate;
            border-spacing: 0 12px;
        }
        .cover-details td { vertical-align: middle; }
        .cover-details td.label {
            width: 45%;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1.5px;
        }
        .cover-details td.colon {
            width: 5%;
            color: #cbd5e1;
            text-align: center;
            font-weight: bold;
        }
        .cover-details td.value {
            font-weight: 800;
            color: #0f172a;
            font-size: 18px;
            border-bottom: 2px dashed #e2e8f0;
            padding-bottom: 4px;
        }
        
        div.dynamic-field {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            color: inherit;
            font-family: inherit;
            font-weight: inherit;
            font-size: inherit;
            display: inline-block;
        }

        @media screen {
            div.dynamic-title, div.dynamic-subtitle, div.dynamic-field {
                border-bottom: 1px dashed rgba(0,0,0,0.2);
            }
            div.dynamic-title:focus, div.dynamic-subtitle:focus, div.dynamic-field:focus {
                background: rgba(0,0,0,0.03);
            }
        }

        .cover-footer {
            margin-top: 40px;
            text-align: center;
            font-size: 14px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .filter-box { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #cbd5e1; display: flex; gap: 10px; align-items: center; justify-content: center; }
    </style>
</head>
<body>
    <div class="print-container">
        
        <div class="no-print filter-box">
            <span style="font-family: sans-serif; font-size: 14px; font-weight: bold;">Pastikan opsi "Background Graphics" aktif saat mencetak.</span>
            
            <form method="GET" action="" style="display: flex; gap: 10px; align-items: center; margin-left: auto;">
                <input type="hidden" name="mulai" value="<?php echo htmlspecialchars($tanggal_mulai); ?>">
                <input type="hidden" name="akhir" value="<?php echo htmlspecialchars($tanggal_akhir); ?>">
                <?php if (in_array($_SESSION['role_id'], [1, 99])): ?>
                <select name="petugas_id" style="padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: sans-serif; font-size: 14px; min-width: 150px;" onchange="this.form.submit()">
                    <option value="0">Semua Petugas</option>
                    <?php foreach($petugasList as $p): ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo $petugas_id == $p['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['nama']); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
            </form>

            <button onclick="window.print()" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: bold; font-family: sans-serif; margin-left: 10px;">Cetak Cover</button>
            <button onclick="window.close()" style="padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: bold; font-family: sans-serif; margin-left: 10px;">Tutup</button>
        </div>

        <div class="modern-cover">
            <div class="cover-content">
                <div class="cover-header">
                    <?php if(!empty($inst['logo'])): ?>
                        <img src="/public/uploads/logo/<?php echo htmlspecialchars($inst['logo']); ?>" alt="Logo">
                    <?php else: ?>
                        <div style="width: 80px; height: 80px; background: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #000; font-weight: bold;">LOGO</div>
                    <?php endif; ?>
                    <div class="inst-name"><?= isset($inst['nama_institusi']) ? htmlspecialchars($inst['nama_institusi']) : 'MADRASAH IBTIDAIYAH MIFTAHUL HUDA' ?></div>
                    <div class="inst-sub">Miftahul Huda Mathla'ul Anwar</div>
                </div>
                
                <div class="cover-title-area">
                    <div class="dynamic-title" contenteditable="true">REKAPITULASI TABUNGAN</div>
                    <div class="dynamic-subtitle" contenteditable="true">MATRIKS MUTASI SISWA</div>
                </div>
                
                <div class="cover-details">
                    <table>
                        <tr>
                            <td class="label">Tahun Pelajaran</td>
                            <td class="colon">:</td>
                            <td class="value"><div class="dynamic-field" contenteditable="true"><?php echo htmlspecialchars($active_year_name); ?></div></td>
                        </tr>
                        <tr>
                            <td class="label">Petugas</td>
                            <td class="colon">:</td>
                            <td class="value"><div class="dynamic-field" contenteditable="true"><?php echo htmlspecialchars($label_petugas); ?></div></td>
                        </tr>
                    </table>
                </div>
                
                <div class="cover-footer">
                    Mis Mifhda &copy; <?= date('Y') ?>
                </div>
            </div>
        </div>
        
    </div>
</body>
</html>
