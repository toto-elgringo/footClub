<?php

class StaffMember
{
    public string $firstname;
    public string $lastname;
    public string $picture;
    public string $role;
    
    public function __construct(string $firstname, string $lastname, string $picture, string $role)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->picture = $picture;
        $this->role = $role;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function getPicture(): string {
        return $this->picture;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function setFirstname(string $firstname): void {
        $this->firstname = $firstname;
    }

    public function setLastname(string $lastname): void {
        $this->lastname = $lastname;
    }

    public function setPicture(string $picture): void {
        $this->picture = $picture;
    }

    public function setRole(string $role): void {
        $this->role = $role;
    }
}
