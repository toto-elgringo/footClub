<?php

namespace Model\manager;

use database\Database;
use PDO;
use Model\Classes\OpposingClub;
use Model\Manager\ManagerInterface;

class OpposingClubManager implements ManagerInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM opposing_club ORDER BY city");
        $clubs = [];

        while ($data = $stmt->fetch()) {
            $clubs[] = new OpposingClub(
                $data['id'],
                $data['address'],
                $data['city']
            );
        }

        return $clubs;
    }

    public function findById(?int $id): ?OpposingClub
    {
        if ($id === null) {
            return null;
        }
        
        $stmt = $this->db->prepare("SELECT * FROM opposing_club WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return new OpposingClub(
            $data['id'],
            $data['address'], // name
            $data['city'] // city
        );
    }

    public function findByCity(string $city): ?OpposingClub
    {
        $stmt = $this->db->prepare("SELECT * FROM opposing_club WHERE city = ?");
        $stmt->execute([$city]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return new OpposingClub(
            $data['id'],
            $data['address'],
            $data['city']
        );
    }

    public function insert(object $object): bool
    {
        if (!$object instanceof OpposingClub) {
            return false;
        }

        $club = $object;
        $stmt = $this->db->prepare("INSERT INTO opposing_club (city, address) VALUES (?, ?)");
        $result = $stmt->execute([
            $club->getCity(),
            $club->getName()
        ]);

        if ($result) {
            $club->setId($this->db->lastInsertId());
            return true;
        }

        return false;
    }

    public function delete(object $object): bool
    {
        if (!$object instanceof OpposingClub) {
            return false;
        }

        $club = $object;
        $stmt = $this->db->prepare("DELETE FROM opposing_club WHERE id = ?");
        return $stmt->execute([$club->getId()]);
    }

    public function update(object $object): bool
    {
        if (!$object instanceof OpposingClub) {
            return false;
        }

        $club = $object;
        $stmt = $this->db->prepare("UPDATE opposing_club SET city = ?, address = ? WHERE id = ?");
        return $stmt->execute([
            $club->getCity(),
            $club->getName(),
            $club->getId()
        ]);
    }
}
