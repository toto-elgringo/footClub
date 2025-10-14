<?php

namespace App\Model\Manager;

use App\Model\Classes\OpposingClub;
use App\Model\Manager\ManagerInterface;
use App\Model\Trait\PdoTrait;
use App\Model\Trait\InstanceOfTrait;

class OpposingClubManager implements ManagerInterface
{
    use PdoTrait, InstanceOfTrait;

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
        $this->checkInstanceOf($object, OpposingClub::class);

        $stmt = $this->db->prepare("INSERT INTO opposing_club (city, address) VALUES (?, ?)");
        $result = $stmt->execute([
            $object->getCity(),
            $object->getName()
        ]);

        if ($result) {
            $object->setId($this->db->lastInsertId());
            return true;
        }

        return false;
    }

    public function delete(object $object): bool
    {
        $this->checkInstanceOf($object, OpposingClub::class);

        $stmt = $this->db->prepare("DELETE FROM opposing_club WHERE id = ?");
        return $stmt->execute([$object->getId()]);
    }

    public function update(object $object): bool
    {
        $this->checkInstanceOf($object, OpposingClub::class);

        $stmt = $this->db->prepare("UPDATE opposing_club SET city = ?, address = ? WHERE id = ?");
        return $stmt->execute([
            $object->getCity(),
            $object->getName(),
            $object->getId()
        ]);
    }
}
