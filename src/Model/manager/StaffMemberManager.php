<?php

namespace Model\Manager;

use Model\Classes\StaffMember;
use Model\Enum\StaffRole; // pour StaffRole::from()
use Model\Trait\PdoTrait;
use Model\Trait\InstanceOfTrait;

class StaffMemberManager implements ManagerInterface
{
    use PdoTrait, InstanceOfTrait;

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM staff_member");

        $staffMembers = [];

        while ($data = $stmt->fetch()) {
            $staffMembers[] = new StaffMember(
                $data['id'] ?? null,
                $data['firstname'],
                $data['lastname'],
                // passage de:
                // $data['role'],
                // à
                StaffRole::from($data['role']),
                $data['picture'] ?? ''
            );
        }

        return $staffMembers;
    }

    public function findById(int $id): ?StaffMember
    {
        $stmt = $this->db->prepare("SELECT * FROM staff_member WHERE id = :id");
        $stmt->execute(["id" => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new StaffMember(
                $data['id'] ?? null,
                $data['firstname'],
                $data['lastname'],
                StaffRole::from($data['role']),
                $data['picture'] ?? ''
            );
        }

        return null;
    }

    public function insert(object $object): bool
    {
        $this->checkInstanceOf($object, StaffMember::class);

        $stmt = $this->db->prepare("INSERT INTO staff_member (firstname, lastname, role, picture) VALUES (:firstname, :lastname, :role, :picture)");
        return $stmt->execute([
            "firstname" => $object->getFirstname(),
            "lastname" => $object->getLastname(),
            "role" => $object->getRole()->value, // ajoute de -> pour accéder à la valeur de l'énumération
            "picture" => $object->getPicture()
        ]);
    }

    public function delete(object $object): bool
    {
        $this->checkInstanceOf($object, StaffMember::class);

        $stmt = $this->db->prepare("DELETE FROM staff_member WHERE id = :id");
        return $stmt->execute(["id" => $object->getId()]);
    }

    public function update(object $object): bool
    {
        $this->checkInstanceOf($object, StaffMember::class);

        $stmt = $this->db->prepare("UPDATE staff_member SET firstname = ?, lastname = ?, role = ?, picture = ? WHERE id = ?");
        return $stmt->execute([
            $object->getFirstname(),
            $object->getLastname(),
            $object->getRole()->value,
            $object->getPicture(),
            $object->getId()
        ]);
    }
}
