<?php
class OpposingClub {
    private ?int $id;
    private string $name;
    private string $city;

    public function __construct(?int $id, string $name, string $city) {
        $this->id = $id;
        $this->name = $name;
        $this->city = $city;
    }

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getCity(): string { return $this->city; }

    public function setId(int $id): void { $this->id = $id; }
    public function setName(string $name): void { $this->name = $name; }
    public function setCity(string $city): void { $this->city = $city; }
}