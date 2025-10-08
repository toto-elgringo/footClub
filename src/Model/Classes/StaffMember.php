<?php

namespace Model\Classes;

class StaffMember
{
    private ?int $id = null;
    private string $firstname;
    private string $lastname;
    private string $role;
    private string $picture;

    public function __construct(?int $id, string $firstname, string $lastname, string $role, string $picture)
    {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->role = $role;
        $this->picture = $picture;
    }

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
    public function getRole(): string
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
    public function setRole(string $role): void
    {
        $this->role = $role;
    }
    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }
}
