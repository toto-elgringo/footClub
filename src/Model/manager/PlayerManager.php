<?php

namespace Model\manager;

use database\Database;
use PDO;
use Model\Classes\Player;
use DateTime;

class PlayerManager implements ManagerInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {

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

    public function getAge(Player $player): int
    {
        $now = new DateTime();
        return $now->diff($player->getBirthdate())->y;
    }

    public function findById(int $id): ?Player
    {
        $stmt = $this->db->prepare("SELECT * FROM player WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Player(
                $row['id'],
                $row['firstname'],
                $row['lastname'],
                $row['birthdate'],
                $row['picture']
            );
        }
        return null;
    }

    public function insert(object $object): bool
    {
        if (!$object instanceof Player) {
            return false;
        }

        $player = $object;
        $stmt = $this->db->prepare("INSERT INTO player (firstname, lastname, birthdate, picture) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $player->getFirstname(),
            $player->getLastname(),
            $player->getBirthdate()->format("Y-m-d"),
            $player->getPicture()
        ]);
    }

    public function delete(object $object): bool
    {
        if (!$object instanceof Player) {
            return false;
        }

        $player = $object;
        $stmt = $this->db->prepare("DELETE FROM player WHERE id = ?");
        return $stmt->execute([$player->getId()]);
    }

    public function update(object $object): bool
    {
        if (!$object instanceof Player) {
            return false;
        }

        $player = $object;
        $stmt = $this->db->prepare("UPDATE player SET firstname = ?, lastname = ?, birthdate = ?, picture = ? WHERE id = ?");
        return $stmt->execute([
            $player->getFirstname(),
            $player->getLastname(),
            $player->getBirthdate()->format("Y-m-d"),
            $player->getPicture(),
            $player->getId()
        ]);
    }
}
