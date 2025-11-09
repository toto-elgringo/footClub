<?php

namespace App\Model\Classes;

use DateTime;

class Player extends Person
{
    public function __construct(
        string $firstname,
        string $lastname,
        private DateTime $birthdate,
        string $picture
    ) {
        parent::__construct($firstname, $lastname, $picture);
    }

    public function getBirthdate(): DateTime
    {
        return $this->birthdate;
    }

    public function setBirthdate(string $birthdate): void
    {
        $this->birthdate = new DateTime($birthdate);
    }
}
