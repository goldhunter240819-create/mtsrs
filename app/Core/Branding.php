<?php

namespace App\Core;

class Branding {
    public static function getInstitusi() {
        try {
            $db = Database::connect();
            $stmt = $db->query("SELECT * FROM institusi LIMIT 1");
            return $stmt->fetch() ?: [
                'nama' => 'MTs Roudlotus Sholihin',
                'singkatan' => 'MTs RS',
                'alamat' => 'Jl. Raden Said No. 12',
                'telepon' => '081234567890',
                'email' => 'info@mtsrs.sch.id',
                'nama_kepala' => 'Ahmad Rifa\'i, M.Pd.'
            ];
        } catch (\Exception $e) {
            return [
                'nama' => 'MTs Roudlotus Sholihin',
                'singkatan' => 'MTs RS'
            ];
        }
    }
}
