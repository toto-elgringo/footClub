<?php

namespace App\Model\Classes;

use App\Model\Enum\StaffRole;

class StaffMember extends Person
{
    public function __construct(
        string $firstname,
        string $lastname,
        private StaffRole $role,
        string $picture
    ) {
        parent::__construct($firstname, $lastname, $picture);
    }

    public function getRole(): StaffRole
    {
        return $this->role;
    }

    public function setRole(StaffRole $role): void
    {
        $this->role = $role;
    }
}
