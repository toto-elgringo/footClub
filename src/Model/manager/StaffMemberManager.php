<?php

namespace App\Model\Manager;

use App\Model\Classes\StaffMember;
use App\Model\Enum\StaffRole; // pour StaffRole::from()
use App\Model\Trait\PdoTrait;
use App\Model\Trait\InstanceOfTrait;

class StaffMemberManager implements ManagerInterface
{
    use PdoTrait, InstanceOfTrait;

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM staff_member");

        $staffMembers = [];

        while ($data = $stmt->fetch()) {
            $staffMembers[] = new StaffMember(
                $data['firstname'],
                $data['lastname'],
                StaffRole::from($data['role']),
                $data['picture'] ?? ''
            );
        }

        return $staffMembers;
    }

    public function findByName(string $firstname, string $lastname): ?StaffMember
    {
        $stmt = $this->db->prepare("SELECT * FROM staff_member WHERE firstname = :firstname AND lastname = :lastname");
        $stmt->execute(["firstname" => $firstname, "lastname" => $lastname]);
        $data = $stmt->fetch();

        if ($data) {
            return new StaffMember(
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

        $stmt = $this->db->prepare("DELETE FROM staff_member WHERE firstname = :firstname AND lastname = :lastname");
        return $stmt->execute([
            "firstname" => $object->getFirstname(),
            "lastname" => $object->getLastname()
        ]);
    }

    public function update(object $object, string $oldFirstname, string $oldLastname): bool
    {
        $this->checkInstanceOf($object, StaffMember::class);

        $stmt = $this->db->prepare("UPDATE staff_member SET firstname = ?, lastname = ?, role = ?, picture = ? WHERE firstname = ? AND lastname = ?");
        return $stmt->execute([
            $object->getFirstname(),
            $object->getLastname(),
            $object->getRole()->value,
            $object->getPicture(),
            $oldFirstname,
            $oldLastname
        ]);
    }
}
