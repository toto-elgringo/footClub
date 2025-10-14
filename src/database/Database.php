<?php

namespace App\Database;

use PDO;
use PDOException;

class Database {
    private static ?PDO $pdo = null;

    private static string $host = "localhost";
    private static string $user = "root";
    private static string $pass = "";
    private static string $dbname = "foot_club";

    private function __construct() {}

    public static function getConnection(): PDO {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8",
                    self::$user,
                    self::$pass
                );
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}