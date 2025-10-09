<?php

namespace Model\Classes;

use DateTime;

class Player extends Person
{
    // private DateTime $birthdate;

    // public function __construct(?int $id, string $firstname, string $lastname, string $birthdate, string $picture)
    // {
    //     $this->id = $id;
    //     $this->firstname = $firstname;
    //     $this->lastname = $lastname;
    //     $this->birthdate = new DateTime($birthdate);
    //     $this->picture = $picture;
    // }

    public function __construct(
        private ?int $id, 
        private string $firstname, 
        private string $lastname,
        private DateTime $birthdate,
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
    public function getBirthdate(): DateTime
    {
        return $this->birthdate;
    }
    public function getPicture(): string
    {
        return $this->picture;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }
    public function setBirthdate(string $birthdate): void
    {
        $this->birthdate = new DateTime($birthdate);
    }
    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }
}
