<?php

namespace Model\Manager;

use Model\Classes\Player;
use DateTime;
use Model\Trait\PdoTrait;
use Model\Trait\InstanceOfTrait;

class PlayerManager implements ManagerInterface
{
    use PdoTrait, InstanceOfTrait;

    public function findAll(): array
    {

        $stmt = $this->db->query("SELECT * FROM player");

        $players = [];

        while ($row = $stmt->fetch()) {
            $players[] = new Player(
                $row['id'],
                $row['firstname'],
                $row['lastname'],
                new DateTime($row['birthdate']),
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
                new DateTime($row['birthdate']),
                $row['picture']
            );
        }
        return null;
    }

    public function insert(object $object): bool
    {
        $this->checkInstanceOf($object, Player::class);

        $stmt = $this->db->prepare("INSERT INTO player (firstname, lastname, birthdate, picture) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $object->getFirstname(),
            $object->getLastname(),
            $object->getBirthdate()->format("Y-m-d"),
            $object->getPicture()
        ]);
    }

    public function delete(object $object): bool
    {
        $this->checkInstanceOf($object, Player::class);

        $stmt = $this->db->prepare("DELETE FROM player WHERE id = ?");
        return $stmt->execute([$object->getId()]);
    }

    public function update(object $object): bool
    {
        $this->checkInstanceOf($object, Player::class);
        
        $stmt = $this->db->prepare("UPDATE player SET firstname = ?, lastname = ?, birthdate = ?, picture = ? WHERE id = ?");
        return $stmt->execute([
            $object->getFirstname(),
            $object->getLastname(),
            $object->getBirthdate()->format("Y-m-d"),
            $object->getPicture(),
            $object->getId()
        ]);
    }
}
