<?php

class Player
{
    public string $firstname;
    public string $lastname;
    public DateTime $datetime;
    public int $picture;

    public function __construct(string $firstname, string $lastname, DateTime $datetime, int $picture)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->datetime = $datetime;
        $this->picture = $picture;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getDatetime(): DateTime
    {
        return $this->datetime;
    }

    public function getPicture(): int
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

    public function setDatetime(DateTime $datetime): void
    {
        $this->datetime = $datetime;
    }

    public function setPicture(int $picture): void
    {
        $this->picture = $picture;
    }
}
