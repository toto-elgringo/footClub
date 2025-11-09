<?php

namespace App\Model\Classes;

class OpposingClub
{
    public function __construct(
        private string $name,
        private string $city
    ) {}

    public function getName(): string
    {
        return $this->name;
    }
    public function getCity(): string
    {
        return $this->city;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function setCity(string $city): void
    {
        $this->city = $city;
    }
}
