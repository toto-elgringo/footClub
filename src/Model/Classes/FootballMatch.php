<?php

namespace App\Model\Classes;

use DateTime;

class FootballMatch
{
    public function __construct(
        private DateTime $date,
        private string $city,
        private int $teamScore,
        private int $opponentScore,
        private ?Team $team,
        private OpposingClub $opposingClub
    ) {}

    public function getDate(): DateTime
    {
        return $this->date;
    }
    public function getCity(): string
    {
        return $this->city;
    }
    public function getTeamScore(): int
    {
        return $this->teamScore;
    }
    public function getOpponentScore(): int
    {
        return $this->opponentScore;
    }
    public function getTeam(): ?Team
    {
        return $this->team;
    }
    public function getOpposingClub(): OpposingClub
    {
        return $this->opposingClub;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }
    public function setCity(string $city): void
    {
        $this->city = $city;
    }
    public function setTeamScore(int $teamScore): void
    {
        $this->teamScore = $teamScore;
    }
    public function setOpponentScore(int $opponentScore): void
    {
        $this->opponentScore = $opponentScore;
    }
    public function setTeam(?Team $team): void
    {
        $this->team = $team;
    }
    public function setOpposingClub(OpposingClub $opposingClub): void
    {
        $this->opposingClub = $opposingClub;
    }
}
