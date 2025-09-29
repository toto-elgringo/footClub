<?php
// classes/Database.php
class Database {
    private static ?PDO $pdo = null; // statci permet d'y accéder sans créer d'objet

    private static string $host = "localhost";
    private static string $user = "root";
    private static string $pass = "";
    private static string $dbname = "foot_club";

    private function __construct() {} // Empêche new Database() Pourquoi ?
    // Cette classe est un singleton : on veut qu’il n’existe qu’une seule connexion PDO pour toute l’application.
    // Personne ne doit créer plusieurs objets

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



// database include de base
// <?php
// $host = "localhost";
// $user = "root";
// $pass = "";
// $dbname = "foot_club";

// try {
//     $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
//     echo "Connection failed: " . $e->getMessage();
// }