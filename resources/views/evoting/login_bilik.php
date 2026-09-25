<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Bilik Suara - MTs Roudlotus Sholihin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 20px; }
        .login-card { background: white; color: #0f172a; padding: 40px; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); width: 100%; max-width: 450px; text-align: center; position: relative; overflow: hidden; }
        .login-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(90deg, #e11d48, #f43f5e); }
        .icon-box { width: 70px; height: 70px; background: #ffe4e6; color: #e11d48; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        h1 { font-size: 24px; font-weight: 800; margin: 0 0 10px 0; color: #0f172a; }
        p { color: #64748b; font-size: 14px; margin: 0 0 30px 0; line-height: 1.5; }
        
        .input-group { text-align: left; margin-bottom: 25px; }
        .input-group label { display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }
        .input-group input { width: 100%; padding: 15px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 20px; font-weight: 700; text-align: center; letter-spacing: 5px; text-transform: uppercase; color: #0f172a; transition: all 0.3s; outline: none; }
        .input-group input:focus { border-color: #e11d48; box-shadow: 0 0 0 4px rgba(225,29,72,0.1); }
        
        .btn-submit { width: 100%; padding: 16px; background: #e11d48; color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-submit:hover { background: #be123c; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(225,29,72,0.3); }
        
        .alert { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 15px; border-radius: 10px; margin-bottom: 25px; font-size: 14px; display: flex; align-items: center; gap: 10px; text-align: left; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="icon-box">
            <i data-lucide="box-select" style="width: 32px; height: 32px;"></i>
        </div>
        <h1>Bilik Suara E-Voting</h1>
        <?php if($activeEvent): ?>
            <p>Sesi Pemilihan: <strong><?= htmlspecialchars($activeEvent['nama_event']) ?></strong><br>Silakan masukkan PIN (Token) Rahasia Anda untuk mulai mencoblos.</p>
            
            <?php if(isset($error)): ?>
                <div class="alert">
                    <i data-lucide="alert-circle" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo \App\Core\Helper::url('/evoting/verify'); ?>" method="POST">
                <div class="input-group">
                    <label>PIN (Token) Pemilih</label>
                    <input type="text" name="token" placeholder="XXXXXX" maxlength="6" autocomplete="off" required autofocus>
                </div>
                <button type="submit" class="btn-submit">
                    Lanjut ke Bilik Suara <i data-lucide="arrow-right"></i>
                </button>
            </form>
        <?php else: ?>
            <div class="alert" style="background: #fffbeb; border-color: #fde68a; color: #92400e; justify-content: center; text-align: center;">
                Saat ini belum ada event pemilihan yang sedang aktif.
            </div>
            <a href="<?php echo \App\Core\Helper::url('/'); ?>" style="color: #64748b; text-decoration: none; font-size: 14px; display: inline-block; margin-top: 10px;">Kembali ke Beranda</a>
        <?php endif; ?>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
