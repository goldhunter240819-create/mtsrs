<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berhasil Mencoblos! - MTs Roudlotus Sholihin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 20px; text-align: center; }
        .success-card { background: white; color: #0f172a; padding: 50px 40px; border-radius: 30px; box-shadow: 0 20px 50px rgba(0,0,0,0.2); max-width: 500px; position: relative; }
        .icon-box { width: 100px; height: 100px; background: #d1fae5; color: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; box-shadow: 0 10px 20px rgba(5,150,105,0.2); }
        h1 { font-size: 28px; font-weight: 900; margin: 0 0 15px 0; color: #064e3b; letter-spacing: -0.5px; }
        p { color: #475569; font-size: 16px; margin: 0 0 30px 0; line-height: 1.6; }
        
        .btn-home { display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 15px 30px; background: #f1f5f9; color: #0f172a; border-radius: 12px; font-weight: 700; text-decoration: none; transition: 0.3s; }
        .btn-home:hover { background: #e2e8f0; transform: translateY(-2px); }

        /* Confetti Animation Base (CSS Only fallback, but we can just use simple dots or keep it clean) */
        @keyframes pop {
            0% { transform: scale(0.5); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }
        .icon-box { animation: pop 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="icon-box">
            <i data-lucide="check" style="width: 50px; height: 50px; stroke-width: 3px;"></i>
        </div>
        <h1>Terima Kasih!</h1>
        <p>Suara Anda berhasil disimpan ke dalam kotak suara digital. Pilihan Anda sangat berarti untuk kemajuan bersama.</p>
        
        <a href="<?php echo \App\Core\Helper::url('/evoting'); ?>" class="btn-home">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Kembali ke Awal
        </a>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
