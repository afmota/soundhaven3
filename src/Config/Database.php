<?php
namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $envFile = __DIR__ . '/../../.env';
            $env = [];
            if (file_exists($envFile)) {
                $parsed = parse_ini_file($envFile);
                if ($parsed !== false) {
                    $env = $parsed;
                }
            }

            $host = getenv('DB_HOST') ?: ($env['DB_HOST'] ?? 'db');
            $dbName = getenv('DB_NAME') ?: ($env['DB_NAME'] ?? 'soundhaven3');
            $user = getenv('DB_USER') ?: ($env['DB_USER'] ?? 'sh_user');
            $pass = getenv('DB_PASS') ?: ($env['DB_PASS'] ?? 'W3azxc*9');
            $charset = getenv('DB_CHARSET') ?: ($env['DB_CHARSET'] ?? 'utf8mb4');

            try {
                $dsn = "mysql:host={$host};dbname={$dbName};charset={$charset}";
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die("Erro de conexao com o banco de dados: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}