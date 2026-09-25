<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilik Suara - MTs Roudlotus Sholihin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { margin: 0; background: #f1f5f9; color: #0f172a; padding-bottom: 50px; }
        
        .header { background: linear-gradient(135deg, #1e293b, #0f172a); color: white; padding: 20px 0; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.1); position: relative; z-index: 10; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { margin: 5px 0 0; color: #94a3b8; font-size: 14px; }
        .voter-badge { display: inline-block; background: rgba(255,255,255,0.1); padding: 5px 15px; border-radius: 20px; font-size: 13px; font-weight: 600; margin-top: 10px; border: 1px solid rgba(255,255,255,0.2); }
        
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        
        .card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: all 0.3s; position: relative; display: flex; flex-direction: column; border: 2px solid transparent; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); border-color: rgba(225,29,72,0.3); }
        
        .card-img-wrap { height: 250px; background: #e2e8f0; position: relative; }
        .card-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .card-img-wrap .no-img { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8; }
        .no-urut { position: absolute; top: 15px; right: 15px; width: 45px; height: 45px; background: #e11d48; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 900; box-shadow: 0 4px 10px rgba(225,29,72,0.4); border: 3px solid white; }
        
        .card-body { padding: 25px; text-align: center; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .card-title { font-size: 20px; font-weight: 800; margin: 0 0 15px 0; color: #1e293b; line-height: 1.3; }
        
        .btn-visi { width: 100%; padding: 12px; background: #f1f5f9; color: #475569; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 15px; }
        .btn-visi:hover { background: #e2e8f0; color: #0f172a; }
        
        .btn-coblos { width: 100%; padding: 16px; background: #e11d48; color: white; border: none; border-radius: 12px; font-weight: 800; font-size: 16px; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; text-transform: uppercase; letter-spacing: 1px; }
        .btn-coblos:hover { background: #be123c; transform: scale(1.02); box-shadow: 0 10px 20px rgba(225,29,72,0.3); }

        /* Modal Styles */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15,23,42,0.8); backdrop-filter: blur(5px); z-index: 100; display: none; align-items: center; justify-content: center; padding: 20px; opacity: 0; transition: opacity 0.3s; }
        .modal-overlay.show { opacity: 1; }
        .modal-content { background: white; border-radius: 24px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; transform: translateY(20px); transition: transform 0.3s; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        .modal-overlay.show .modal-content { transform: translateY(0); }
        .modal-header { padding: 25px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10; }
        .modal-title { margin: 0; font-size: 18px; font-weight: 800; color: #0f172a; }
        .modal-close { background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b; transition: 0.2s; }
        .modal-close:hover { background: #e2e8f0; color: #0f172a; }
        .modal-body { padding: 25px; }
        
        .vm-section { margin-bottom: 25px; }
        .vm-title { font-size: 14px; font-weight: 800; color: #e11d48; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; display: flex; align-items: center; gap: 8px; }
        .vm-text { font-size: 15px; color: #475569; line-height: 1.6; background: #f8fafc; padding: 15px; border-radius: 12px; border-left: 4px solid #e11d48; }

    </style>
</head>
<body>

    <div class="header">
        <h1>Bilik Suara E-Voting</h1>
        <p><?= htmlspecialchars($eventData['nama_event']) ?></p>
        <div class="voter-badge">
            <i data-lucide="user" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle; margin-right: 5px;"></i>
            Halo, <?= htmlspecialchars($voterName) ?>
        </div>
    </div>

    <div class="container">
        <?php if(isset($_SESSION['flash_error'])): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 15px; border-radius: 12px; margin-bottom: 30px; display: flex; align-items: center; gap: 10px;">
                <i data-lucide="alert-circle"></i> <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-size: 28px; font-weight: 800; margin: 0 0 10px 0; color: #0f172a;">Tentukan Pilihan Anda!</h2>
            <p style="color: #64748b; font-size: 16px; margin: 0;">Pilihlah kandidat yang menurut Anda terbaik. Pilihan Anda bersifat rahasia.</p>
        </div>

        <div class="grid">
            <?php foreach($candidatesData as $c): ?>
            <div class="card">
                <div class="card-img-wrap">
                    <div class="no-urut"><?= $c['no_urut'] ?></div>
                    <?php if(!empty($c['foto'])): ?>
                        <img src="<?= \App\Core\Helper::url('/public/uploads/evoting/'.$c['foto']) ?>" alt="Kandidat <?= $c['no_urut'] ?>">
                    <?php else: ?>
                        <div class="no-img"><i data-lucide="user" style="width: 64px; height: 64px;"></i></div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div>
                        <h3 class="card-title"><?= htmlspecialchars($c['nama_kandidat']) ?></h3>
                        <button class="btn-visi" onclick="openModal('modal-<?= $c['id'] ?>')">
                            <i data-lucide="file-text" style="width: 16px; height: 16px;"></i> Lihat Visi & Misi
                        </button>
                    </div>
                    
                    <form action="<?php echo \App\Core\Helper::url('/evoting/coblos'); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mencoblos KANDIDAT NO. <?= $c['no_urut'] ?> ? Pilihan tidak dapat diubah setelah dicoblos.')">
                        <input type="hidden" name="candidate_id" value="<?= $c['id'] ?>">
                        <button type="submit" class="btn-coblos">
                            <i data-lucide="check-square" style="width: 20px; height: 20px;"></i> Coblos
                        </button>
                    </form>
                </div>
            </div>

            <!-- Modal Visi Misi -->
            <div id="modal-<?= $c['id'] ?>" class="modal-overlay" onclick="if(event.target === this) closeModal('modal-<?= $c['id'] ?>')">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title">Kandidat No. <?= $c['no_urut'] ?></div>
                        <button class="modal-close" onclick="closeModal('modal-<?= $c['id'] ?>')"><i data-lucide="x" style="width: 18px; height: 18px;"></i></button>
                    </div>
                    <div class="modal-body">
                        <div style="text-align: center; margin-bottom: 25px;">
                            <h3 style="font-size: 22px; font-weight: 800; margin: 0; color: #0f172a;"><?= htmlspecialchars($c['nama_kandidat']) ?></h3>
                        </div>
                        <div class="vm-section">
                            <div class="vm-title"><i data-lucide="eye" style="width: 16px; height: 16px;"></i> Visi</div>
                            <div class="vm-text"><?= nl2br(htmlspecialchars($c['visi'] ?: 'Tidak ada visi.')) ?></div>
                        </div>
                        <div class="vm-section">
                            <div class="vm-title"><i data-lucide="target" style="width: 16px; height: 16px;"></i> Misi</div>
                            <div class="vm-text"><?= nl2br(htmlspecialchars($c['misi'] ?: 'Tidak ada misi.')) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        lucide.createIcons();
        
        function openModal(id) {
            const modal = document.getElementById(id);
            modal.style.display = 'flex';
            setTimeout(() => { modal.classList.add('show'); }, 10);
        }
        
        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('show');
            setTimeout(() => { modal.style.display = 'none'; }, 300);
        }
    </script>
</body>
</html>
