<?php

namespace Model\Classes;

abstract class Person
{
    protected ?int $id = null;
    protected string $firstname;
    protected string $lastname;
    protected string $picture;

    public function __construct(?int $id, string $firstname, string $lastname, string $picture)
    {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
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
