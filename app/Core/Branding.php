<?php

namespace App\Core;

use PDO;

class Branding {
    private static $institusiCache = null;

    public static function getInstitusi() {
        if (self::$institusiCache !== null) {
            return self::$institusiCache;
        }

        try {
            $db = Database::connect();
            $stmt = $db->query("SELECT * FROM institusi LIMIT 1");
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                self::$institusiCache = $data;
                return $data;
            }
        } catch (\Exception $e) {}

        self::$institusiCache = [
            'nama' => 'MTs ROUDLOTUS SHOLIHIN',
            'singkatan' => 'MTs RS',
            'alamat' => 'Jl. Raya Gunung Terang No. 09',
            'desa' => 'Gunung Terang',
            'kecamatan' => 'Air Hitam',
            'kota' => 'Lampung Barat',
            'kabupaten' => 'Lampung Barat',
            'provinsi' => 'Lampung',
            'kodepos' => '34886',
            'telepon' => '+62 857-0945-2565',
            'email' => 'mtsroudlotussholihin1@gmail.com',
            'website' => 'https://mtsroudlotussholihin.sch.id',
            'nama_kepala' => 'NARKO, S.Pd.I., M.Pd., Gr',
            'nip_kepala' => '-',
            'logo' => 'logo_1790363838.png',
            'yayasan' => 'YAYASAN PENDIDIKAN ISLAM ROUDLOTUS SHOLIHIN GUNUNG TERANG'
        ];

        return self::$institusiCache;
    }

    public static function get() {
        $inst = self::getInstitusi();
        return [
            'nama' => $inst['nama'] ?? 'MTs ROUDLOTUS SHOLIHIN',
            'singkatan' => $inst['singkatan'] ?? 'MTs RS',
            'logo' => self::getInstitusiLogo(),
            'favicon' => self::getInstitusiFavicon(),
            'institusi' => $inst
        ];
    }

    public static function getInstitusiLogo() {
        $inst = self::getInstitusi();
        if (!empty($inst['logo'])) {
            return '/public/uploads/logo/' . $inst['logo'];
        }
        return '/public/assets/images/logo.png';
    }

    public static function getInstitusiFavicon() {
        return self::getInstitusiLogo();
    }
}
