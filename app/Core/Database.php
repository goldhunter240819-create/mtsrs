<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;

    public static function connect($name = 'core') {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';
            $dbConfig = $config['core'];

            try {
                $dsn = "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['db_name'] . ";charset=" . ($dbConfig['charset'] ?? 'utf8mb4');
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                self::$instance = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $options);
            } catch (PDOException $e) {
                die("Connection failed to db_mts_rs: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
