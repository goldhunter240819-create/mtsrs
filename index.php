<?php
// GHTech Analytics Tracking
$_ghtech_key = "96d8e171f6e319449cb43ea5d2d4825e";
$_ghtech_ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"]);
if ($_ghtech_ip == "127.0.0.1" || $_ghtech_ip == "::1") {
    if (!isset($_COOKIE["ghtech_vid"])) { $vid = uniqid("loc_"); setcookie("ghtech_vid", $vid, time() + 86400, "/"); $_ghtech_ip = $vid; } else { $_ghtech_ip = $_COOKIE["ghtech_vid"]; }
}
// Track IP langsung
@file_get_contents("http://localhost/ghtech/api/track.php?key=" . $_ghtech_key . "&ip=" . urlencode($_ghtech_ip));
// Update username setelah session aktif
register_shutdown_function(function() {
    global $_ghtech_key, $_ghtech_ip;
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_u = "";
        if (isset($_SESSION["guru_nama"])) { $_u = $_SESSION["guru_nama"]; }
        elseif (isset($_SESSION["siswa_nama"])) { $_u = $_SESSION["siswa_nama"]; }
        elseif (isset($_SESSION["nama"])) { $_u = $_SESSION["nama"]; }
        if (!empty($_u)) {
            @file_get_contents("http://localhost/ghtech/api/track.php?key=" . $_ghtech_key . "&ip=" . urlencode($_ghtech_ip) . "&user=" . urlencode($_u));
        }
    }
});
?>
<?php
/**
 * MTs Roudlotus Sholihin System - Root Entry Point
 * Routing & Session Initialization
 */

date_default_timezone_set('Asia/Jakarta');

session_name("MTS_RS_SESSION");
if (session_status() === PHP_SESSION_NONE) {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    
    // Set maxlifetime server ke 1 tahun untuk SEMUA rute 
    ini_set('session.gc_maxlifetime', 31536000);
    
    // Terapkan fitur Anti-Logout untuk SEMUA rute di MTs RS (1 Tahun)
    ini_set('session.cookie_lifetime', 31536000);
    session_set_cookie_params(31536000, "/");
    
    // Gunakan folder khusus agar session tidak terhapus oleh garbage collector dari app PHP lain
    session_save_path(__DIR__ . '/storage/sessions');
    
    session_start();
}

// Simple PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Router;
use App\Middleware\AuthMiddleware;

// Handle authentication & direct entry routing (/ -> /login or /portal)
AuthMiddleware::handle();

$router = new Router();

// --- Public Website Route ---
$router->add('GET', '/', [\App\Controllers\WebController::class, 'index']);
$router->add('GET', '/profil', [\App\Controllers\WebController::class, 'profil']);
$router->add('GET', '/berita', [\App\Controllers\WebController::class, 'berita']);
$router->add('GET', '/ppdb', [\App\Controllers\WebController::class, 'ppdb']);
$router->add('GET', '/kontak', [\App\Controllers\WebController::class, 'kontak']);

// --- Auth Routes ---
$router->add('GET', '/login', [\App\Controllers\AuthController::class, 'loginForm']);
$router->add('POST', '/login', [\App\Controllers\AuthController::class, 'loginProcess']);
$router->add('GET', '/logout', [\App\Controllers\AuthController::class, 'logout']);

// --- Portal Route ---
$router->add('GET', '/portal', [\App\Controllers\PortalController::class, 'index']);

// --- SIAKAD Routes ---
$router->add('GET', '/siakad', [\App\Controllers\SiakadController::class, 'dashboard']);
$router->add('GET', '/siakad/institusi', [\App\Controllers\SiakadController::class, 'institusi']);
$router->add('POST', '/siakad/institusi/save', [\App\Controllers\SiakadController::class, 'institusiSave']);
$router->add('POST', '/siakad/tahun-ajaran/add', [\App\Controllers\SiakadController::class, 'tahunAjaranAdd']);
$router->add('GET', '/siakad/tahun-ajaran/activate/{id}', [\App\Controllers\SiakadController::class, 'tahunAjaranActivate']);
$router->add('GET', '/siakad/mapel', [\App\Controllers\SiakadController::class, 'mapel']);
$router->add('POST', '/siakad/mapel/save', [\App\Controllers\SiakadController::class, 'mapelSave']);
$router->add('POST', '/siakad/mapel/delete', [\App\Controllers\SiakadController::class, 'mapelDelete']);
$router->add('GET', '/siakad/kelas', [\App\Controllers\SiakadController::class, 'kelas']);
$router->add('POST', '/siakad/kelas/save', [\App\Controllers\SiakadController::class, 'kelasSave']);
$router->add('POST', '/siakad/kelas/update', [\App\Controllers\SiakadController::class, 'kelasUpdate']);
$router->add('GET', '/siakad/kelas/delete/{id}', [\App\Controllers\SiakadController::class, 'kelasDelete']);
$router->add('POST', '/siakad/kelas/assign-wali', [\App\Controllers\SiakadController::class, 'kelasAssignWali']);
$router->add('GET', '/siakad/ekstra', [\App\Controllers\SiakadController::class, 'ekstra']);
$router->add('POST', '/siakad/ekstra/save', [\App\Controllers\SiakadController::class, 'ekstraSave']);
$router->add('POST', '/siakad/ekstra/delete', [\App\Controllers\SiakadController::class, 'ekstraDelete']);
$router->add('GET', '/siakad/kop-surat', [\App\Controllers\SiakadController::class, 'kopSurat']);
$router->add('POST', '/siakad/kop-surat', [\App\Controllers\SiakadController::class, 'kopSuratSave']);
$router->add('GET', '/siakad/guru', [\App\Controllers\SiakadController::class, 'guru']);
$router->add('GET', '/siakad/guru/edit/{id}', [\App\Controllers\SiakadController::class, 'guruEdit']);
$router->add('POST', '/siakad/guru/save', [\App\Controllers\SiakadController::class, 'guruSave']);
$router->add('POST', '/siakad/guru/update-foto', [\App\Controllers\SiakadController::class, 'guruUpdateFoto']);
$router->add('GET', '/siakad/guru/delete/{id}', [\App\Controllers\SiakadController::class, 'guruDelete']);
$router->add('POST', '/siakad/guru/delete-bulk', [\App\Controllers\SiakadController::class, 'guruDeleteBulk']);
$router->add('GET', '/siakad/guru/cetak-kartu-massal', [\App\Controllers\SiakadController::class, 'guruCetakKartuMassal']);
$router->add('POST', '/siakad/guru/cetak-kartu-bulk', [\App\Controllers\SiakadController::class, 'guruCetakKartuBulk']);
$router->add('POST', '/siakad/guru/import', [\App\Controllers\SiakadController::class, 'guruImport']);
$router->add('GET', '/siakad/guru/template-import', [\App\Controllers\SiakadController::class, 'guruTemplateImport']);
$router->add('GET', '/siakad/guru/export', [\App\Controllers\SiakadController::class, 'guruExport']);
$router->add('GET', '/siakad/guru/tugas', [\App\Controllers\SiakadController::class, 'guruTugas']);
$router->add('POST', '/siakad/guru/tugas/save', [\App\Controllers\SiakadController::class, 'guruTugasSave']);
$router->add('GET', '/siakad/guru/mengajar', [\App\Controllers\SiakadController::class, 'guruMengajar']);
$router->add('POST', '/siakad/guru/mengajar/save', [\App\Controllers\SiakadController::class, 'guruMengajarSave']);
$router->add('GET', '/siakad/guru/cetak-kartu', [\App\Controllers\SiakadController::class, 'guruCetakKartu']);
$router->add('GET', '/siakad/jurnal-global', [\App\Controllers\SiakadController::class, 'jurnalGlobal']);
$router->add('GET', '/siakad/jurnal-global/cetak', [\App\Controllers\SiakadController::class, 'jurnalGlobalCetak']);
$router->add('POST', '/siakad/jurnal-global/settings', [\App\Controllers\SiakadController::class, 'updateJurnalSettings']);

$router->add('GET', '/siakad/monitoring-nilai', [\App\Controllers\SiakadController::class, 'monitoringNilai']);
$router->add('GET', '/siakad/monitoring-nilai/detail', [\App\Controllers\SiakadController::class, 'monitoringNilaiDetail']);
$router->add('GET', '/siakad/monitoring-nilai/cetak', [\App\Controllers\SiakadController::class, 'monitoringNilaiCetak']);

$router->add('GET', '/siakad/siswa', [\App\Controllers\SiakadController::class, 'siswa']);
$router->add('POST', '/siakad/siswa/save', [\App\Controllers\SiakadController::class, 'siswaSave']);
$router->add('GET', '/siakad/siswa/edit/{id}', [\App\Controllers\SiakadController::class, 'siswaEdit']);
$router->add('POST', '/siakad/siswa/edit/{id}', [\App\Controllers\SiakadController::class, 'siswaUpdate']);
$router->add('POST', '/siakad/siswa/update-foto', [\App\Controllers\SiakadController::class, 'siswaUpdateFoto']);
$router->add('GET', '/siakad/siswa/delete/{id}', [\App\Controllers\SiakadController::class, 'siswaDelete']);
$router->add('GET', '/siakad/siswa/cetak-kartu', [\App\Controllers\SiakadController::class, 'siswaCetakKartu']);
$router->add('GET', '/siakad/siswa/cetak-kartu-massal', [\App\Controllers\SiakadController::class, 'siswaCetakKartuMassal']);
$router->add('POST', '/siakad/siswa/cetak-kartu-bulk', [\App\Controllers\SiakadController::class, 'siswaCetakKartuBulk']);
$router->add('POST', '/siakad/siswa/delete-bulk', [\App\Controllers\SiakadController::class, 'siswaDeleteBulk']);
$router->add('POST', '/siakad/siswa/reset-password-bulk', [\App\Controllers\SiakadController::class, 'siswaResetPasswordBulk']);
$router->add('POST', '/siakad/siswa/import', [\App\Controllers\SiakadController::class, 'siswaImport']);
$router->add('GET', '/siakad/siswa/template-import', [\App\Controllers\SiakadController::class, 'siswaTemplateImport']);
$router->add('GET', '/siakad/siswa/naik-kelas', [\App\Controllers\SiakadController::class, 'siswaNaikKelas']);
$router->add('POST', '/siakad/siswa/naik-kelas/process', [\App\Controllers\SiakadController::class, 'siswaNaikKelasProcess']);
$router->add('GET', '/siakad/siswa/alumni', [\App\Controllers\SiakadController::class, 'siswaAlumni']);
$router->add('POST', '/siakad/siswa/upload-foto-masal', [\App\Controllers\SiakadController::class, 'siswaUploadFotoMasal']);
$router->add('POST', '/siakad/siswa/upload-foto-masal-ajax', [\App\Controllers\SiakadController::class, 'siswaUploadFotoMasalAjax']);
$router->add('GET', '/siakad/mutasi', [\App\Controllers\SiakadController::class, 'mutasi']);
$router->add('POST', '/siakad/mutasi/save', [\App\Controllers\SiakadController::class, 'mutasiSave']);
$router->add('GET', '/siakad/mutasi/delete/{id}', [\App\Controllers\SiakadController::class, 'mutasiDelete']);

$router->add('GET', '/siakad/kalender', [\App\Controllers\SiakadController::class, 'kalender']);
$router->add('POST', '/siakad/kalender/save', [\App\Controllers\SiakadController::class, 'kalenderSave']);
$router->add('POST', '/siakad/kalender/update', [\App\Controllers\SiakadController::class, 'kalenderUpdate']);
$router->add('GET', '/siakad/kalender/delete', [\App\Controllers\SiakadController::class, 'kalenderDelete']);
$router->add('GET', '/siakad/nilai', [\App\Controllers\SiakadController::class, 'nilai']);
$router->add('POST', '/siakad/nilai/save', [\App\Controllers\SiakadController::class, 'nilaiSave']);
$router->add('GET', '/siakad/nilai/input', [\App\Controllers\SiakadController::class, 'nilaiInput']);
$router->add('GET', '/siakad/nilai/template', [\App\Controllers\SiakadController::class, 'nilaiTemplate']);
$router->add('GET', '/siakad/jadwal', [\App\Controllers\SiakadController::class, 'jadwal']);
$router->add('POST', '/siakad/jadwal/save', [\App\Controllers\SiakadController::class, 'jadwalSave']);
$router->add('GET', '/siakad/jadwal/edit/{id}', [\App\Controllers\SiakadController::class, 'jadwalEdit']);
$router->add('POST', '/siakad/jadwal/save_inline/{id}', [\App\Controllers\SiakadController::class, 'jadwalSaveInline']);
$router->add('POST', '/siakad/jadwal/edit_jam', [\App\Controllers\SiakadController::class, 'jadwalEditJam']);
$router->add('GET', '/siakad/jadwal/waktu', [\App\Controllers\SiakadController::class, 'jadwalWaktu']);
$router->add('POST', '/siakad/jadwal/waktu', [\App\Controllers\SiakadController::class, 'jadwalWaktuSave']);
$router->add('GET', '/siakad/raport', [\App\Controllers\SiakadController::class, 'raport']);
$router->add('GET', '/siakad/raport/cetak/{id}', [\App\Controllers\SiakadController::class, 'raportCetak']);

$router->add('GET', '/siakad/lain-lain/rapat', [\App\Controllers\SiakadController::class, 'rapat']);
$router->add('POST', '/siakad/lain-lain/rapat/save', [\App\Controllers\SiakadController::class, 'rapatSave']);
$router->add('GET', '/siakad/lain-lain/broadcast', [\App\Controllers\SiakadController::class, 'broadcast']);
$router->add('POST', '/siakad/lain-lain/broadcast/save', [\App\Controllers\SiakadController::class, 'broadcastSave']);
$router->add('GET', '/siakad/lain-lain/broadcast/delete', [\App\Controllers\SiakadController::class, 'broadcastDelete']);

// -- Master Tugas Tambahan
$router->add('GET', '/siakad/master-tugas', [\App\Controllers\SiakadController::class, 'masterTugas']);
$router->add('POST', '/siakad/master-tugas/save', [\App\Controllers\SiakadController::class, 'masterTugasSave']);
$router->add('POST', '/siakad/master-tugas/delete', [\App\Controllers\SiakadController::class, 'masterTugasDelete']);

// --- Keuangan Routes (Finance) ---
$router->add('GET', '/keuangan', [\App\Controllers\AdminFinanceController::class, 'overview']);
$router->add('GET', '/keuangan/kolektif', [\App\Controllers\AdminFinanceController::class, 'pembayaranKolektif']);
$router->add('GET', '/keuangan/kolektif/ajax-siswa', [\App\Controllers\AdminFinanceController::class, 'ajaxSiswaByKelas']);
$router->add('POST', '/keuangan/kolektif/save', [\App\Controllers\AdminFinanceController::class, 'savePembayaranKolektif']);
$router->add('GET', '/keuangan/kolektif/setting', [\App\Controllers\AdminFinanceController::class, 'settingKolektif']);
$router->add('POST', '/keuangan/kolektif/setting/save', [\App\Controllers\AdminFinanceController::class, 'saveSettingKolektif']);
$router->add('GET', '/keuangan/kolektif/setting/delete/{id}', [\App\Controllers\AdminFinanceController::class, 'deleteSettingKolektif']);
$router->add('GET', '/keuangan/tagihan', [\App\Controllers\AdminFinanceController::class, 'tagihan']);
$router->add('GET', '/keuangan/tagihan/cetak/{id}', [\App\Controllers\AdminFinanceController::class, 'cetakSuratTagihan']);
$router->add('GET', '/keuangan/tagihan/cetak-siswa/{id}', [\App\Controllers\AdminFinanceController::class, 'cetakSuratTagihanSiswa']);
$router->add('POST', '/keuangan/tagihan/update', [\App\Controllers\AdminFinanceController::class, 'updateTagihan']);
$router->add('POST', '/keuangan/tagihan/delete', [\App\Controllers\AdminFinanceController::class, 'deleteTagihan']);
$router->add('GET', '/keuangan/komite/pemasukan', [\App\Controllers\AdminFinanceController::class, 'komitePemasukan']);
$router->add('GET', '/keuangan/komite/pengeluaran', [\App\Controllers\AdminFinanceController::class, 'komitePengeluaran']);
$router->add('GET', '/keuangan/komite/kategori', [\App\Controllers\AdminFinanceController::class, 'komiteKategori']);
$router->add('POST', '/keuangan/komite/kategori/save', [\App\Controllers\AdminFinanceController::class, 'saveKomiteKategori']);
$router->add('GET', '/keuangan/komite/kategori/delete/{id}', [\App\Controllers\AdminFinanceController::class, 'deleteKomiteKategori']);
$router->add('GET', '/keuangan/komite/jenis', [\App\Controllers\AdminFinanceController::class, 'komiteJenisTagihan']);
$router->add('POST', '/keuangan/komite/jenis/save', [\App\Controllers\AdminFinanceController::class, 'saveKomiteJenisTagihan']);
$router->add('POST', '/keuangan/komite/jenis/bulk-delete', [\App\Controllers\AdminFinanceController::class, 'bulkDeleteKomiteJenisTagihan']);
$router->add('GET', '/keuangan/komite/jenis/delete/{id}', [\App\Controllers\AdminFinanceController::class, 'deleteKomiteJenisTagihan']);
$router->add('GET', '/keuangan/komite/pembayaran/{id}', [\App\Controllers\AdminFinanceController::class, 'komitePembayaranSiswa']);
$router->add('POST', '/keuangan/komite/pembayaran/save', [\App\Controllers\AdminFinanceController::class, 'saveKomitePembayaran']);
$router->add('GET', '/keuangan/komite/rekap', [\App\Controllers\AdminFinanceController::class, 'komiteRekapTagihan']);
$router->add('GET', '/keuangan/komite/rekap/cetak', [\App\Controllers\AdminFinanceController::class, 'komiteRekapTagihanCetak']);
$router->add('GET', '/keuangan/komite/laporan', [\App\Controllers\AdminFinanceController::class, 'komiteLaporan']);
$router->add('GET', '/keuangan/komite/laporan/cetak', [\App\Controllers\AdminFinanceController::class, 'cetakLaporan']);
$router->add('POST', '/keuangan/komite/transaksi/save', [\App\Controllers\AdminFinanceController::class, 'saveKomiteTransaksi']);
$router->add('GET', '/keuangan/komite/transaksi/delete/{id}', [\App\Controllers\AdminFinanceController::class, 'deleteKomiteTransaksi']);
$router->add('GET', '/keuangan/kwitansi/{id}', [\App\Controllers\AdminFinanceController::class, 'cetakKwitansi']);
$router->add('GET', '/kwitansi/verifikasi/{id}', [\App\Controllers\AdminFinanceController::class, 'verifikasiKwitansiPublic']);
$router->add('GET', '/verify-invoice', [\App\Controllers\AdminFinanceController::class, 'verifikasiTagihanPublic']);
$router->add('GET', '/bos/pemasukan', [\App\Controllers\AdminBosController::class, 'bosPemasukan']);
$router->add('GET', '/bos/pengeluaran', [\App\Controllers\AdminBosController::class, 'bosPengeluaran']);
$router->add('GET', '/bos/laporan', [\App\Controllers\AdminBosController::class, 'bosLaporan']);
$router->add('POST', '/bos/transaksi/save', [\App\Controllers\AdminBosController::class, 'saveBosTransaksi']);
$router->add('GET', '/bos/transaksi/delete/{id}', [\App\Controllers\AdminBosController::class, 'deleteBosTransaksi']);
$router->add('GET', '/keuangan/gaji', [\App\Controllers\AdminFinanceController::class, 'gaji']);
$router->add('POST', '/keuangan/gaji/bayar', [\App\Controllers\AdminFinanceController::class, 'bayarGaji']);
$router->add('GET', '/keuangan/akses', [\App\Controllers\AdminFinanceController::class, 'aksesBendahara']);
$router->add('POST', '/keuangan/akses/save', [\App\Controllers\AdminFinanceController::class, 'saveAksesBendahara']);
$router->add('POST', '/keuangan/akses/bendahara-umum', [\App\Controllers\AdminFinanceController::class, 'saveBendaharaUmum']);
$router->add('GET', '/keuangan/akses/delete/{id}', [\App\Controllers\AdminFinanceController::class, 'deleteAksesBendahara']);
$router->add('GET', '/keuangan/biaya', [\App\Controllers\AdminFinanceController::class, 'biaya']);
$router->add('POST', '/keuangan/biaya/save', [\App\Controllers\AdminFinanceController::class, 'saveBiaya']);
$router->add('GET', '/keuangan/biaya/delete/{id}', [\App\Controllers\AdminFinanceController::class, 'deleteBiaya']);
$router->add('GET', '/keuangan/laporan', [\App\Controllers\AdminFinanceController::class, 'laporan']);
$router->add('GET', '/keuangan/cetak/buku-kas', [\App\Controllers\AdminFinanceController::class, 'cetakBukuKas']);
$router->add('GET', '/keuangan/cetak/buku-manual', [\App\Controllers\AdminFinanceController::class, 'cetakBukuManual']);
$router->add('GET', '/keuangan/cetak/cover', [\App\Controllers\AdminFinanceController::class, 'cetakCover']);

// --- Keuangan Routes (Tabungan) ---
$router->add('GET', '/tabungan', [\App\Controllers\AdminTabunganController::class, 'overview']);
$router->add('GET', '/tabungan/siswa', [\App\Controllers\AdminTabunganController::class, 'siswa']);
$router->add('GET', '/tabungan/kasir', [\App\Controllers\AdminTabunganController::class, 'kasir']);
$router->add('POST', '/tabungan/transaksi', [\App\Controllers\AdminTabunganController::class, 'transaksi']);
$router->add('POST', '/tabungan/mutasi', [\App\Controllers\AdminTabunganController::class, 'mutasi']);
$router->add('GET', '/tabungan/rekap', [\App\Controllers\AdminTabunganController::class, 'rekap']);
$router->add('GET', '/tabungan/rekap/cetak', [\App\Controllers\AdminTabunganController::class, 'rekapCetak']);
$router->add('GET', '/tabungan/rekap/cover', [\App\Controllers\AdminTabunganController::class, 'rekapCetakCover']);
$router->add('GET', '/tabungan/rekap/matriks', [\App\Controllers\AdminTabunganController::class, 'rekapCetakMatriks']);
$router->add('GET', '/tabungan/cetak/{id}', [\App\Controllers\AdminTabunganController::class, 'cetak']);
$router->add('POST', '/tabungan/buka-rekening', [\App\Controllers\AdminTabunganController::class, 'bukaRekening']);
$router->add('GET', '/tabungan/api-siswa', [\App\Controllers\AdminTabunganController::class, 'apiSiswa']);
$router->add('GET', '/tabungan/akses', [\App\Controllers\AdminTabunganController::class, 'akses']);
$router->add('POST', '/tabungan/akses/simpan', [\App\Controllers\AdminTabunganController::class, 'simpanAkses']);
$router->add('GET', '/tabungan/akses/hapus/{id}', [\App\Controllers\AdminTabunganController::class, 'hapusAkses']);
$router->add('GET', '/tabungan/akses/toggle-hapus/{id}', [\App\Controllers\AdminTabunganController::class, 'toggleAksesHapus']);

// --- Absensi QR (V2) Routes ---
$router->add('GET', '/absen-v2', [\App\Controllers\AbsenV2Controller::class, 'dashboard']);
$router->add('GET', '/absen-v2/device', [\App\Controllers\AbsenV2Controller::class, 'deviceConfig']);
$router->add('POST', '/absen-v2/device/save', [\App\Controllers\AbsenV2Controller::class, 'deviceConfigSave']);
$router->add('GET', '/absen-v2/rekap', [\App\Controllers\AbsenV2Controller::class, 'rekap']);
$router->add('GET', '/absen-v2/rekap/detail', [\App\Controllers\AbsenV2Controller::class, 'rekapDetail']);

// --- Pengaturan APK Routes ---
$router->add('GET', '/pengaturan-apk', [\App\Controllers\PengaturanApkController::class, 'dashboard']);
$router->add('GET', '/pengaturan-apk/navbar', [\App\Controllers\PengaturanApkController::class, 'navbar']);
$router->add('GET', '/pengaturan-apk/aplikasi', [\App\Controllers\PengaturanApkController::class, 'aplikasi']);
$router->add('POST', '/pengaturan-apk/aplikasi/save-akses', [\App\Controllers\PengaturanApkController::class, 'saveAkses']);

// --- Absensi QR V2 Routes ---
$router->add('GET', '/absen', [\App\Controllers\AbsenV2Controller::class, 'dashboard']);
$router->add('GET', '/absen/scanner', [\App\Controllers\AbsenV2Controller::class, 'scanner']);
$router->add('GET', '/absen/scanner-guru', [\App\Controllers\AbsenV2Controller::class, 'scannerGuru']);
$router->add('POST', '/absen/process-scan', [\App\Controllers\AbsenV2Controller::class, 'processScan']);
$router->add('GET', '/absen/rekap-harian', [\App\Controllers\AbsenV2Controller::class, 'rekapHarian']);
$router->add('GET', '/absen/rekap-detail', [\App\Controllers\AbsenV2Controller::class, 'rekapDetail']);
$router->add('GET', '/absen/rekap-detail/cetak', [\App\Controllers\AbsenV2Controller::class, 'rekapDetailCetak']);
$router->add('POST', '/absen/update-status-siswa', [\App\Controllers\AbsenV2Controller::class, 'updateStatusSiswa']);
$router->add('GET', '/absen/rekap-bulanan', [\App\Controllers\AbsenV2Controller::class, 'rekapBulanan']);
$router->add('GET', '/absen/cetak-manual', [\App\Controllers\AbsenV2Controller::class, 'cetakManual']);
$router->add('GET', '/absen/rekap-harian-guru', [\App\Controllers\AbsenV2Controller::class, 'rekapHarianGuru']);
$router->add('GET', '/absen/rekap-bulanan-guru', [\App\Controllers\AbsenV2Controller::class, 'rekapBulananGuru']);
$router->add('GET', '/absen/input-absen-guru', [\App\Controllers\AbsenV2Controller::class, 'inputAbsenGuru']);
$router->add('POST', '/absen/input-absen-guru/save', [\App\Controllers\AbsenV2Controller::class, 'inputAbsenGuruSave']);
$router->add('GET', '/absen/pengaturan', [\App\Controllers\AbsenV2Controller::class, 'pengaturan']);
$router->add('GET', '/absen/pengaturan-guru', [\App\Controllers\AbsenV2Controller::class, 'pengaturanGuruCustom']);
$router->add('POST', '/absen/pengaturan-guru/save', [\App\Controllers\AbsenV2Controller::class, 'savePengaturanGuruCustom']);
$router->add('POST', '/absen/pengaturan/save', [\App\Controllers\AbsenV2Controller::class, 'savePengaturan']);
$router->add('GET', '/absen/cetak-bulanan-guru-detail', [\App\Controllers\AbsenV2Controller::class, 'cetakBulananGuruDetail']);
$router->add('GET', '/absen/cetak-bulanan-siswa-detail', [\App\Controllers\AbsenV2Controller::class, 'cetakBulananSiswaDetail']);
$router->add('GET', '/absen/cetak-top3-siswa', [\App\Controllers\AbsenV2Controller::class, 'cetakTop3Siswa']);
$router->add('GET', '/absen/ketidakhadiran', [\App\Controllers\AbsenV2Controller::class, 'ketidakhadiran']);
$router->add('GET', '/absen/statistik-siswa', [\App\Controllers\AbsenV2Controller::class, 'statistikSiswa']);
$router->add('GET', '/absen/statistik-siswa-cetak', [\App\Controllers\AbsenV2Controller::class, 'statistikSiswaCetak']);
$router->add('POST', '/absen/ketidakhadiran/save', [\App\Controllers\AbsenV2Controller::class, 'ketidakhadiranSave']);
$router->add('POST', '/absen/ketidakhadiran/delete', [\App\Controllers\AbsenV2Controller::class, 'ketidakhadiranDelete']);
$router->add('GET', '/absen/admin-izin-piket', [\App\Controllers\AbsenV2Controller::class, 'adminIzinPiket']);
$router->add('POST', '/absen/approve-izin', [\App\Controllers\AbsenV2Controller::class, 'approveIzinGuru']);
$router->add('POST', '/absen/lapor-alpa', [\App\Controllers\AbsenV2Controller::class, 'laporAlpa']);
$router->add('GET', '/absen/eksekusi-inval', [\App\Controllers\AbsenV2Controller::class, 'eksekusiInval']);
$router->add('POST', '/absen/eksekusi-inval', [\App\Controllers\AbsenV2Controller::class, 'eksekusiInval']);
// --- Kurikulum (Waka) Routes ---
$router->add('GET', '/kurikulum', [\App\Controllers\KurikulumController::class, 'dashboard']);
$router->add('GET', '/kurikulum/supervisi-administrasi', [\App\Controllers\KurikulumController::class, 'supervisiAdministrasi']);
$router->add('GET', '/kurikulum/supervisi-administrasi/evaluasi', [\App\Controllers\KurikulumController::class, 'supervisiAdministrasiEvaluasi']);
$router->add('POST', '/kurikulum/supervisi-administrasi/save', [\App\Controllers\KurikulumController::class, 'supervisiAdministrasiSave']);
$router->add('GET', '/kurikulum/supervisi-kelas', [\App\Controllers\KurikulumController::class, 'supervisiKelas']);
$router->add('GET', '/kurikulum/supervisi-kelas/evaluasi', [\App\Controllers\KurikulumController::class, 'supervisiKelasEvaluasi']);
$router->add('POST', '/kurikulum/supervisi-kelas/save', [\App\Controllers\KurikulumController::class, 'supervisiKelasSave']);
$router->add('GET', '/kurikulum/monitoring-jurnal', [\App\Controllers\KurikulumController::class, 'monitoringJurnal']);
$router->add('GET', '/kurikulum/monitoring-nilai', [\App\Controllers\KurikulumController::class, 'monitoringNilai']);
$router->add('POST', '/kurikulum/monitoring-nilai/simpan-bobot', [\App\Controllers\KurikulumController::class, 'simpanBobotNilai']);
$router->add('GET', '/kurikulum/monitoring-nilai/detail', [\App\Controllers\KurikulumController::class, 'monitoringNilaiDetail']);
$router->add('GET', '/kurikulum/monitoring-nilai/global', [\App\Controllers\KurikulumController::class, 'monitoringNilaiGlobal']);
$router->add('GET', '/kurikulum/monitoring-nilai/cetak', [\App\Controllers\KurikulumController::class, 'monitoringNilaiCetak']);
$router->add('GET', '/kurikulum/supervisi-penilaian', [\App\Controllers\KurikulumController::class, 'supervisiPenilaian']);
$router->add('POST', '/kurikulum/supervisi-penilaian/save', [\App\Controllers\KurikulumController::class, 'supervisiPenilaianSave']);
$router->add('GET', '/kurikulum/jadwal-pelajaran', [\App\Controllers\KurikulumController::class, 'jadwalPelajaran']);
$router->add('GET', '/kurikulum/monitoring-kkm', [\App\Controllers\KurikulumController::class, 'monitoringKkm']);
$router->add('POST', '/kurikulum/monitoring-kkm/save', [\App\Controllers\KurikulumController::class, 'monitoringKkmSave']);
$router->add('GET', '/kurikulum/rekap-ketuntasan', [\App\Controllers\KurikulumController::class, 'rekapKetuntasan']);
$router->add('GET', '/kurikulum/api/guru-mengajar', [\App\Controllers\KurikulumController::class, 'apiGuruMengajar']);
$router->add('GET', '/kurikulum/api/jenis-evaluasi', [\App\Controllers\KurikulumController::class, 'apiJenisEvaluasi']);

// --- APK Web View Routes ---
$router->add('GET', '/apk', [\App\Controllers\ApkController::class, 'index']);
$router->add('GET', '/apk/login', [\App\Controllers\ApkController::class, 'login']);
$router->add('POST', '/apk/login', [\App\Controllers\ApkController::class, 'loginProcess']);
$router->add('GET', '/apk/siswa/dashboard', [\App\Controllers\ApkController::class, 'dashboardSiswa']);
$router->add('GET', '/apk/guru/dashboard', [\App\Controllers\ApkController::class, 'dashboardGuru']);
$router->add('GET', '/apk/logout', [\App\Controllers\ApkController::class, 'logout']);
$router->add('GET', '/apk/guru/jurnal', [\App\Controllers\ApkController::class, 'jurnal']);
$router->add('POST', '/apk/guru/jurnal', [\App\Controllers\ApkController::class, 'jurnal']);
$router->add('GET', '/apk/absen', [\App\Controllers\ApkController::class, 'scanner']);
$router->add('GET', '/apk/aplikasi-saya', [\App\Controllers\ApkController::class, 'aplikasiSaya']);
$router->add('GET', '/apk/guru/rekap-jurnal', [\App\Controllers\ApkController::class, 'rekapJurnal']);
$router->add('GET', '/apk/guru/rekap-absensi', [\App\Controllers\ApkController::class, 'rekapAbsensi']);
$router->add('GET', '/apk/kasir/kolektif', [\App\Controllers\ApkController::class, 'kasirKolektif']);
$router->add('GET', '/apk/kasir/manual', [\App\Controllers\ApkController::class, 'kasirManual']);
$router->add('POST', '/apk/kasir/manual/save', [\App\Controllers\ApkController::class, 'kasirManualSave']);
$router->add('POST', '/apk/kasir/kolektif/save', [\App\Controllers\ApkController::class, 'kasirKolektifSave']);
$router->add('GET', '/apk/bku', [\App\Controllers\ApkController::class, 'bkuBendahara']);
$router->add('POST', '/apk/bku/save', [\App\Controllers\ApkController::class, 'bkuBendaharaSave']);
$router->add('GET', '/apk/nilai-harian', [\App\Controllers\ApkController::class, 'nilaiHarianIndex']);
$router->add('GET', '/apk/nilai-harian/input', [\App\Controllers\ApkController::class, 'nilaiHarianInput']);
$router->add('POST', '/apk/nilai-harian/save', [\App\Controllers\ApkController::class, 'nilaiHarianSave']);
$router->add('POST', '/apk/nilai-harian/delete', [\App\Controllers\ApkController::class, 'nilaiHarianDelete']);
$router->add('POST', '/apk/guru/rekap-jurnal', [\App\Controllers\ApkController::class, 'rekapJurnal']);
$router->add('GET', '/apk/guru/rekap-jurnal/delete', [\App\Controllers\ApkController::class, 'deleteJurnal']);
$router->add('GET', '/apk/aplikasi-siswa', [\App\Controllers\ApkController::class, 'aplikasiSiswa']);
$router->add('GET', '/apk/aplikasi-guru', [\App\Controllers\ApkController::class, 'aplikasiSaya']);
$router->add('GET', '/apk/supervisi-administrasi', [\App\Controllers\ApkController::class, 'supervisiAdministrasi']);
$router->add('GET', '/apk/supervisi-administrasi/evaluasi', [\App\Controllers\ApkController::class, 'supervisiAdministrasiEvaluasi']);
$router->add('POST', '/apk/supervisi-administrasi/save', [\App\Controllers\ApkController::class, 'supervisiAdministrasiSave']);
$router->add('GET', '/apk/supervisi-kelas', [\App\Controllers\ApkController::class, 'supervisiKelas']);
$router->add('GET', '/apk/supervisi-kelas/evaluasi', [\App\Controllers\ApkController::class, 'supervisiKelasEvaluasi']);
$router->add('POST', '/apk/supervisi-kelas/save', [\App\Controllers\ApkController::class, 'supervisiKelasSave']);
$router->add('GET', '/apk/monitor-absen', [\App\Controllers\ApkController::class, 'monitorAbsen']);
$router->add('GET', '/apk/kamad/monitoring-jurnal', [\App\Controllers\ApkController::class, 'kamadMonitoringJurnal']);
$router->add('GET', '/apk/kamad/rekap-absen', [\App\Controllers\ApkController::class, 'kamadRekapAbsen']);
$router->add('GET', '/apk/kamad/monitor-penilaian', [\App\Controllers\ApkController::class, 'kamadMonitorPenilaian']);
$router->add('GET', '/apk/kamad/supervisi-penilaian/evaluasi', [\App\Controllers\ApkController::class, 'kamadSupervisiPenilaianEvaluasi']);
$router->add('POST', '/apk/kamad/supervisi-penilaian/save', [\App\Controllers\ApkController::class, 'kamadSupervisiPenilaianSave']);
$router->add('GET', '/apk/kamad/monitor-qr-siswa', [\App\Controllers\ApkController::class, 'kamadMonitorQRSiswa']);
$router->add('GET', '/apk/kamad/rekap-qr-siswa', [\App\Controllers\ApkController::class, 'kamadRekapQRSiswa']);
$router->add('GET', '/apk/kamad/realtime-guru', [\App\Controllers\ApkController::class, 'wakaKurikulumPresensi']);
$router->add('GET', '/apk/kamad/rekap-absen-guru', [\App\Controllers\ApkController::class, 'kamadRekapAbsenGuru']);
$router->add('GET', '/apk/monitor-keuangan', [\App\Controllers\ApkController::class, 'monitorKeuangan']);
$router->add('GET', '/apk/monitor-bos', [\App\Controllers\ApkController::class, 'monitorBos']);
$router->add('GET', '/apk/monitor-realtime', [\App\Controllers\ApkController::class, 'monitorRealtime']);
$router->add('GET', '/apk/monitor-realtime/rekap', [\App\Controllers\ApkController::class, 'monitorRealtimeRekap']);
$router->add('GET', '/apk/monitor-permapel', [\App\Controllers\ApkController::class, 'monitorPermapel']);
$router->add('GET', '/apk/monitor-permapel/rekap', [\App\Controllers\ApkController::class, 'monitorPermapelRekap']);
$router->add('GET', '/apk/wali-kelas/siswa', [\App\Controllers\ApkController::class, 'waliKelasSiswa']);
$router->add('GET', '/apk/wali-kelas/siswa/profil/{id}', [\App\Controllers\ApkController::class, 'waliKelasSiswaProfil']);
$router->add('GET', '/apk/wali-kelas/jurnal', [\App\Controllers\ApkController::class, 'waliKelasJurnal']);
$router->add('GET', '/apk/wali-kelas/monitor-absen', [\App\Controllers\ApkController::class, 'waliKelasMonitorAbsen']);
$router->add('GET', '/apk/wali-kelas/absen', [\App\Controllers\ApkController::class, 'waliKelasAbsen']);
$router->add('GET', '/apk/wali-kelas/catatan', [\App\Controllers\ApkController::class, 'waliKelasCatatan']);
$router->add('POST', '/apk/wali-kelas/catatan/save', [\App\Controllers\ApkController::class, 'waliKelasCatatanSave']);
$router->add('GET', '/apk/wali-kelas/buku-kerja', [\App\Controllers\ApkController::class, 'waliKelasBukuKerja']);
$router->add('POST', '/apk/wali-kelas/buku-kerja', [\App\Controllers\ApkController::class, 'waliKelasBukuKerja']);
$router->add('GET', '/apk/wali-kelas/poin', [\App\Controllers\ApkController::class, 'waliKelasPoin']);
$router->add('GET', '/apk/tagihan', [\App\Controllers\ApkController::class, 'tagihan']);
$router->add('GET', '/apk/viewer', [\App\Controllers\ApkController::class, 'viewer']);
$router->add('GET', '/apk/profile', [\App\Controllers\ApkController::class, 'profile']);
$router->add('GET', '/apk/rekap-absensi', [\App\Controllers\ApkController::class, 'rekapKehadiranSaya']);
$router->add('GET', '/apk/izin-siswa', [\App\Controllers\ApkController::class, 'izinSiswa']);
$router->add('POST', '/apk/izin-siswa/save', [\App\Controllers\ApkController::class, 'izinSiswaSave']);
$router->add('GET', '/apk/izin-guru', [\App\Controllers\ApkController::class, 'izinGuru']);
$router->add('POST', '/apk/izin-guru', [\App\Controllers\ApkController::class, 'submitIzinGuru']);
$router->add('GET', '/apk/admin-izin-piket', [\App\Controllers\ApkController::class, 'adminIzinPiket']);
$router->add('POST', '/apk/approve-izin', [\App\Controllers\ApkController::class, 'approveIzinGuru']);
$router->add('GET', '/apk/eksekusi-inval', [\App\Controllers\ApkController::class, 'eksekusiInval']);
$router->add('POST', '/apk/eksekusi-inval', [\App\Controllers\ApkController::class, 'eksekusiInval']);
$router->add('POST', '/apk/lapor-alpa', [\App\Controllers\ApkController::class, 'laporAlpa']);
$router->add('GET', '/apk/api/jadwal-harian', [\App\Controllers\ApkController::class, 'getJadwalHarian']);
$router->add('GET', '/apk/berkas', [\App\Controllers\ApkController::class, 'berkas']);
$router->add('POST', '/apk/berkas', [\App\Controllers\ApkController::class, 'berkas']);
$router->add('GET', '/apk/berkas/hapus/{id}', [\App\Controllers\ApkController::class, 'hapusBerkas']);
$router->add('GET', '/apk/berkas-pribadi/hapus/{id}', [\App\Controllers\ApkController::class, 'hapusBerkasPribadi']);
$router->add('GET', '/apk/edit-profile', [\App\Controllers\ApkController::class, 'editProfile']);
$router->add('POST', '/apk/edit-profile', [\App\Controllers\ApkController::class, 'editProfile']);
$router->add('GET', '/apk/logout', [\App\Controllers\ApkController::class, 'logout']);
$router->add('GET', '/apk/api/siswa-kelas', [\App\Controllers\ApkController::class, 'apiGetSiswaKelas']);
$router->add('POST', '/apk/api/scan', [\App\Controllers\ApkController::class, 'apiScan']);

// Waka & BK APK Routes
$router->add('GET', '/apk/waka-kurikulum', [\App\Controllers\ApkController::class, 'wakaKurikulum']);
$router->add('GET', '/apk/waka-kurikulum/jadwal-kbm', [\App\Controllers\ApkController::class, 'wakaKurikulumJadwal']);
$router->add('GET', '/apk/waka-kurikulum/rekap-presensi-guru', [\App\Controllers\ApkController::class, 'wakaKurikulumPresensi']);
$router->add('GET', '/apk/waka-kesiswaan', [\App\Controllers\ApkController::class, 'wakaKesiswaan']);
$router->add('GET', '/apk/waka-kesiswaan/poin', [\App\Controllers\ApkController::class, 'wakaKesiswaanPoin']);
$router->add('GET', '/apk/waka-kesiswaan/mutasi', [\App\Controllers\ApkController::class, 'wakaKesiswaanMutasi']);
$router->add('GET', '/apk/waka-kesiswaan/statistik-kehadiran', [\App\Controllers\ApkController::class, 'wakaKesiswaanAbsensi']);
// --- Bimbingan Konseling APK ---
$router->add('GET', '/apk/bk', [\App\Controllers\ApkController::class, 'bk']);
$router->add('GET', '/apk/bk/jurnal', [\App\Controllers\ApkController::class, 'bkJurnal']);
$router->add('POST', '/apk/bk/jurnal/save', [\App\Controllers\ApkController::class, 'bkJurnalSave']);
$router->add('GET', '/apk/bk/poin', [\App\Controllers\ApkController::class, 'bkPoin']);
$router->add('POST', '/apk/bk/poin/save', [\App\Controllers\ApkController::class, 'bkPoinSave']);

// --- Admin Panel Routes (For Admin to manage Guru portal access) ---
$router->add('GET', '/admin/portal-users', [\App\Controllers\AdminController::class, 'portalUsers']);
$router->add('POST', '/admin/portal-users/toggle', [\App\Controllers\AdminController::class, 'portalUsersToggle']);
$router->add('POST', '/admin/portal-users/toggle-master', [\App\Controllers\AdminController::class, 'portalUsersToggleMaster']);
$router->add('GET', '/admin/profil', [\App\Controllers\AdminController::class, 'profil']);
$router->add('POST', '/admin/profil/save', [\App\Controllers\AdminController::class, 'profilSave']);

// --- Dev Panel Routes ---
$router->add('GET', '/dev', [\App\Controllers\DevController::class, 'index']);
$router->add('POST', '/dev/cache-clear', [\App\Controllers\DevController::class, 'clearCache']);
$router->add('POST', '/dev/backup-db', [\App\Controllers\DevController::class, 'backupDb']);
$router->add('POST', '/dev/query', [\App\Controllers\DevController::class, 'runQuery']);
$router->add('GET', '/dev/admins', [\App\Controllers\DevController::class, 'admins']);
$router->add('POST', '/dev/admins/save', [\App\Controllers\DevController::class, 'adminSave']);
$router->add('GET', '/dev/admins/delete/{id}', [\App\Controllers\DevController::class, 'adminDelete']);
$router->add('GET', '/dev/portal-access', [\App\Controllers\DevController::class, 'portalAccess']);
$router->add('POST', '/dev/portal-access/toggle', [\App\Controllers\DevController::class, 'portalAccessToggle']);
$router->add('GET', '/dev/settings', [\App\Controllers\DevController::class, 'settings']);
$router->add('POST', '/dev/settings/save', [\App\Controllers\DevController::class, 'settingsSave']);
$router->add('GET', '/dev/cctv', [\App\Controllers\DevController::class, 'cctv']);

// --- BK Routes ---
$router->add('GET', '/bk', [\App\Controllers\BkController::class, 'index']);
$router->add('GET', '/bk/pelanggaran', [\App\Controllers\BkController::class, 'pelanggaran']);
$router->add('POST', '/bk/pelanggaran/save', [\App\Controllers\BkController::class, 'savePelanggaran']);
$router->add('GET', '/bk/kategori', [\App\Controllers\BkController::class, 'kategori']);
$router->add('POST', '/bk/kategori/save', [\App\Controllers\BkController::class, 'saveKategori']);
$router->add('GET', '/bk/kategori/delete/{id}', [\App\Controllers\BkController::class, 'deleteKategori']);
$router->add('GET', '/bk/pelanggaran/delete/{id}', [\App\Controllers\BkController::class, 'deletePelanggaran']);
$router->add('GET', '/bk/laporan', [\App\Controllers\BkController::class, 'laporan']);
$router->add('GET', '/bk/laporan/cetak-kelas', [\App\Controllers\BkController::class, 'cetakKelas']);
$router->add('GET', '/bk/laporan/cetak-siswa', [\App\Controllers\BkController::class, 'cetakSiswa']);

// --- Manajemen Berkas Routes ---
$router->add('GET', '/manajemen-berkas', [\App\Controllers\AdminBerkasController::class, 'dashboard']);
$router->add('GET', '/manajemen-berkas/pribadi', [\App\Controllers\AdminBerkasController::class, 'pribadi']);
$router->add('GET', '/manajemen-berkas/perangkat', [\App\Controllers\AdminBerkasController::class, 'perangkat']);
$router->add('GET', '/manajemen-berkas/download/{type}/{filename}', [\App\Controllers\AdminBerkasController::class, 'download']);
$router->add('GET', '/manajemen-berkas/view-pdf/{filename}', [\App\Controllers\AdminBerkasController::class, 'viewPdf']);

// ROUTE: Web Guru Dashboard
$router->add('GET', '/guru', [\App\Controllers\WebGuruController::class, 'dashboard']);
$router->add('GET', '/guru/jadwal', [\App\Controllers\WebGuruController::class, 'jadwal']);
$router->add('GET', '/guru/jurnal', [\App\Controllers\WebGuruController::class, 'jurnal']);
$router->add('POST', '/guru/jurnal/save', [\App\Controllers\WebGuruController::class, 'jurnalSave']);

// Nilai Harian Guru (Web)
$router->add('GET', '/guru/nilai-harian', [\App\Controllers\WebGuruController::class, 'nilaiHarian']);
$router->add('GET', '/guru/nilai-harian/input', [\App\Controllers\WebGuruController::class, 'nilaiHarianInput']);
$router->add('POST', '/guru/nilai-harian/save', [\App\Controllers\WebGuruController::class, 'nilaiHarianSave']);
$router->add('POST', '/guru/nilai-harian/delete', [\App\Controllers\WebGuruController::class, 'nilaiHarianDelete']);
$router->add('GET', '/guru/berkas', [\App\Controllers\WebGuruController::class, 'berkas']);
$router->add('POST', '/guru/berkas', [\App\Controllers\WebGuruController::class, 'berkas']);
$router->add('GET', '/guru/berkas/hapus/{id}', [\App\Controllers\WebGuruController::class, 'hapusBerkas']);
$router->add('GET', '/guru/berkas-pribadi/hapus/{id}', [\App\Controllers\WebGuruController::class, 'hapusBerkasPribadi']);

// ROUTE: Apk Guru Izin
$router->add('GET', '/apk/izin-guru', [\App\Controllers\ApkController::class, 'izinGuru']);
$router->add('POST', '/apk/izin-guru', [\App\Controllers\ApkController::class, 'submitIzinGuru']);
$router->add('GET', '/apk/api/jadwal-harian', [\App\Controllers\ApkController::class, 'getJadwalHarian']);

// ROUTE: E-Rapor APK
$router->add('GET', '/apk/e-rapor', [\App\Controllers\ApkController::class, 'eRapor']);
$router->add('GET', '/apk/e-rapor/detail', [\App\Controllers\ApkController::class, 'eRaporDetail']);

// ROUTE: Web Siswa Dashboard
$router->add('GET', '/siswa', [\App\Controllers\WebSiswaController::class, 'dashboard']);

// --- E-Voting Routes ---
$router->add('GET', '/evoting/admin', [\App\Controllers\EVotingAdminController::class, 'index']);
$router->add('POST', '/evoting/admin/store-event', [\App\Controllers\EVotingAdminController::class, 'storeEvent']);
$router->add('GET', '/evoting/admin/candidates', [\App\Controllers\EVotingAdminController::class, 'manageCandidates']);
$router->add('POST', '/evoting/admin/store-candidate', [\App\Controllers\EVotingAdminController::class, 'storeCandidate']);
$router->add('GET', '/evoting/admin/delete-candidate', [\App\Controllers\EVotingAdminController::class, 'deleteCandidate']);
$router->add('GET', '/evoting/admin/voters', [\App\Controllers\EVotingAdminController::class, 'manageVoters']);
$router->add('POST', '/evoting/admin/generate-tokens', [\App\Controllers\EVotingAdminController::class, 'generateTokens']);
$router->add('GET', '/evoting/admin/print-tokens', [\App\Controllers\EVotingAdminController::class, 'printTokens']);
$router->add('GET', '/evoting/admin/live', [\App\Controllers\EVotingAdminController::class, 'liveCount']);

// --- E-Voting Frontend (Bilik Suara) ---
$router->add('GET', '/evoting', [\App\Controllers\EVotingController::class, 'loginBilik']);
$router->add('POST', '/evoting/verify', [\App\Controllers\EVotingController::class, 'verifyToken']);
$router->add('GET', '/evoting/bilik', [\App\Controllers\EVotingController::class, 'bilikSuara']);
$router->add('POST', '/evoting/coblos', [\App\Controllers\EVotingController::class, 'coblos']);
$router->add('GET', '/evoting/success', [\App\Controllers\EVotingController::class, 'success']);

// Default fallback 404
$router->run();
