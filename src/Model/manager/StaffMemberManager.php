<?php

namespace src\Model\manager;

use database\Database;
use PDO;
use src\Model\StaffMember;

class StaffMemberManager {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM staff_member");

        $staffMembers = [];

        while ($data = $stmt->fetch()) {
            $staffMembers[] = new StaffMember(
                $data['id'] ?? null,
                $data['firstname'],
                $data['lastname'],
                $data['role'],
                $data['picture'] ?? ''
            );
        }

        return $staffMembers;
    }

    public function insert(StaffMember $staffMember): bool {
        $stmt = $this->db->prepare("INSERT INTO staff_member (firstname, lastname, role, picture) VALUES (:firstname, :lastname, :role, :picture)");
        return $stmt->execute([
            "firstname" => $staffMember->getFirstname(),
            "lastname" => $staffMember->getLastname(),
            "role" => $staffMember->getRole(),
            "picture" => $staffMember->getPicture()
        ]);
    }
}