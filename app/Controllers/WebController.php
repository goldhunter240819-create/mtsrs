<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Helper;
use App\Core\Branding;

class WebController {

    public function index() {
        $db = Database::connect();
        $institusi = Branding::getInstitusi();
        
        $title = $institusi['nama'] . " - Selamat Datang";
        $current_page = 'home';
        
        ob_start();
        include __DIR__ . '/../../resources/views/web/home.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/web/layout.php';
    }

    public function profil() {
        $institusi = Branding::getInstitusi();
        $title = "Profil - " . $institusi['nama'];
        $current_page = 'profil';
        
        ob_start();
        include __DIR__ . '/../../resources/views/web/profil.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/web/layout.php';
    }

    public function berita() {
        $institusi = Branding::getInstitusi();
        $title = "Berita - " . $institusi['nama'];
        $current_page = 'berita';
        
        ob_start();
        include __DIR__ . '/../../resources/views/web/berita.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/web/layout.php';
    }

    public function ppdb() {
        $institusi = Branding::getInstitusi();
        $title = "PPDB - " . $institusi['nama'];
        $current_page = 'ppdb';
        
        ob_start();
        include __DIR__ . '/../../resources/views/web/ppdb.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/web/layout.php';
    }

    public function kontak() {
        $institusi = Branding::getInstitusi();
        $title = "Kontak - " . $institusi['nama'];
        $current_page = 'kontak';
        
        ob_start();
        include __DIR__ . '/../../resources/views/web/kontak.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/web/layout.php';
    }
}
