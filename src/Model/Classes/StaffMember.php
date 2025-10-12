<?php

namespace Model\Classes;

use Model\Enum\StaffRole;
// utilisation de l'énumération StaffRole

class StaffMember extends Person
{
    public function __construct(
        private ?int $id, 
        private string $firstname, 
        private string $lastname,
        private StaffRole $role, // utilisation de variable $role de type StaffRole
        private string $picture
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getFirstname(): string
    {
        return $this->firstname;
    }
    public function getLastname(): string
    {
        return $this->lastname;
    }
    public function getRole(): StaffRole // renvoi en type StaffRole
    {
        return $this->role;
    }
    public function getPicture(): string
    {
        return $this->picture;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }
    public function setRole(StaffRole $role): void // paramètre $role de type StaffRole
    {
        $this->role = $role;
    }
    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }
}
