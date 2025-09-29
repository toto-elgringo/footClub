<?php
require_once __DIR__ . "/../database/Database.php";
require_once __DIR__ . "/../Player.php";

class PlayerManager {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findAll(): array {

        $stmt = $this->db->query("SELECT * FROM player");

        $players = [];

        while ($row = $stmt->fetch()) {
            $players[] = new Player(
                $row['id'],
                $row['firstname'],
                $row['lastname'],
                $row['birthdate'],
                $row['picture']
            );
        }

        return $players;
    }

    public function getAge(Player $player): int {
        $now = new DateTime();
        return $now->diff($player->getBirthdate())->y;
    }

    public function insert(Player $player): bool {
        $stmt = $this->db->prepare("INSERT INTO player (firstname, lastname, birthdate, picture) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $player->getFirstname(),
            $player->getLastname(),
            $player->getBirthdate()->format("Y-m-d"),
            $player->getPicture()
        ]);
    }
}
