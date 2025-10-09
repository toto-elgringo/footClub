<?php

namespace Model\Classes;

class Team
{
    // private ?int $id;
    // private string $name;

    // public function __construct(?int $id, string $name)
    // {
    //     $this->id = $id;
    //     $this->name = $name;
    // }

    private function __construct(private ?int $id, private string $name)
    { // fonction promue
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
