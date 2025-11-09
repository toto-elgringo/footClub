<?php

namespace App\Model\Classes;

abstract class Person
{
    public function __construct(
        private string $firstname,
        private string $lastname,
        private string $picture
    ) {}

    public function getFirstname(): string
    {
        return $this->firstname;
    }
    public function getLastname(): string
    {
        return $this->lastname;
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
    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }
}
