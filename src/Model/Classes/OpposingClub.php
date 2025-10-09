<?php

namespace Model\Classes;

class OpposingClub
{    
    public function __construct(
        private ?int $id, 
        private string $name, 
        private string $city
    ) {}
    
    public function getId(): int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getCity(): string
    {
        return $this->city;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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
