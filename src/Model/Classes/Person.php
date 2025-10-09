<?php

namespace Model\Classes;

abstract class Person
{
    public function __construct(
        private ?int $id, 
        private string $firstname, 
        private string $lastname,
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
    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }
}
