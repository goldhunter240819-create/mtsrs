<?php
$title = $title ?? 'Tata Tertib Ujian Madrasah | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_tata_tertib';

ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="shield-alert" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Tata Tertib Ujian Madrasah
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Regulasi dan tata tertib resmi bagi Peserta Didik dan Pengawas Ruang Ujian Madrasah.
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="<?php echo \App\Core\Helper::url('/asesmen/tata-tertib/cetak?tipe=peserta'); ?>" target="_blank" class="btn" style="background: white; color: #4338ca; font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <i data-lucide="printer" style="width: 18px; height: 18px;"></i> Cetak Tata Tertib Peserta
        </a>
        <a href="<?php echo \App\Core\Helper::url('/asesmen/tata-tertib/cetak?tipe=pengawas'); ?>" target="_blank" class="btn btn-outline" style="background: rgba(255,255,255,0.15); color: white; border-color: rgba(255,255,255,0.4); font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="printer" style="width: 18px; height: 18px;"></i> Cetak Tata Tertib Pengawas
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Tata Tertib Peserta -->
    <div class="z-card" style="padding: 1.75rem; border-radius: 16px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1.25rem; border-bottom: 1px solid var(--z-border); padding-bottom: 12px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="user-check" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--z-text);">Tata Tertib Peserta Ujian</h3>
                <small style="color: var(--z-text-muted);">Wajib ditempel di pintu setiap ruang ujian</small>
            </div>
        </div>

        <ol style="font-size: 0.88rem; color: var(--z-text); line-height: 1.7; padding-left: 20px; margin: 0;">
            <li>Peserta ujian memasuki ruangan setelah tanda masuk dibunyikan, yakni <strong>15 menit</strong> sebelum ujian dimulai.</li>
            <li>Peserta yang terlambat hadir hanya diperkenankan mengikuti ujian setelah mendapat izin dari Ketua Panitia, tanpa penambahan waktu.</li>
            <li>Peserta ujian wajib membawa dan mengenakan <strong>Kartu Pelajar / Kartu Peserta Ujian</strong> yang sah.</li>
            <li>Peserta dilarang membawa alat komunikasi elektronik (HP/Gadget) berkamera/berinternet ke dalam ruang ujian, kecuali ditentukan lain oleh panitia (ujian berbasis CBT).</li>
            <li>Peserta duduk di tempat yang telah ditentukan sesuai dengan <strong>Nomor Meja</strong> masing-masing.</li>
            <li>Peserta mengisi daftar hadir dengan menggunakan pulpen/tanda tangan yang disediakan pengawas.</li>
            <li>Selama ujian berlangsung, peserta dilarang menanyakan jawaban soal kepada siapa pun, bekerja sama, atau saling meminjam alat tulis.</li>
            <li>Peserta yang telah selesai mengerjakan soal sebelum waktu habis dapat meninggalkan ruangan setelah menyerahkan lembar ujian kepada pengawas.</li>
        </ol>
    </div>

    <!-- Tata Tertib Pengawas -->
    <div class="z-card" style="padding: 1.75rem; border-radius: 16px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1.25rem; border-bottom: 1px solid var(--z-border); padding-bottom: 12px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="shield" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--z-text);">Tata Tertib Pengawas Ruang</h3>
                <small style="color: var(--z-text-muted);">Panduan tugas pengawas selama sesi ujian</small>
            </div>
        </div>

        <ol style="font-size: 0.88rem; color: var(--z-text); line-height: 1.7; padding-left: 20px; margin: 0;">
            <li>Pengawas ruang telah hadir di ruang sekretariat panitia selambat-lambatnya <strong>20 menit</strong> sebelum ujian dimulai.</li>
            <li>Pengawas ruang menerima berkas naskah soal, lembar jawaban, daftar hadir, dan berita acara dari panitia.</li>
            <li>Pengawas masuk ruang ujian 15 menit sebelum waktu mulai, memeriksa ketertiban peserta, dan memandu doa.</li>
            <li>Pengawas memeriksa kesesuaian Kartu Pelajar peserta dengan daftar hadir resmi dan nomor meja.</li>
            <li>Pengawas mengedarkan daftar hadir peserta dan memverifikasi tanda tangan siswa.</li>
            <li>Pengawas mengisi dan menandatangani <strong>Berita Acara Pelaksanaan Ujian</strong> secara cermat dan teliti.</li>
            <li>Selama ujian berlangsung, pengawas dilarang merokok, memainkan gawai/HP, membaca koran/buku, atau melakukan kegiatan yang mengganggu konsentrasi peserta.</li>
            <li>Setelah waktu selesai, pengawas mengumpulkan berkas ujian dan menyerahkannya ke sekretariat panitia.</li>
        </ol>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
