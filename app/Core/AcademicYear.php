<?php

namespace App\Core;

class AcademicYear {
    public static function current() {
        try {
            $db = Database::connect();
            $stmt = $db->query("SELECT * FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
            return $stmt->fetch() ?: [
                'id' => 1,
                'name' => '2025/2026',
                'semester' => 'Ganjil',
                'is_active' => 1
            ];
        } catch (\Exception $e) {
            return [
                'id' => 1,
                'name' => '2025/2026',
                'semester' => 'Ganjil'
            ];
        }
    }

    public static function getAll() {
        try {
            $db = Database::connect();
            return $db->query("SELECT * FROM tahun_ajaran ORDER BY name DESC")->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }
}
