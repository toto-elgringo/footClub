<?php

namespace Model\Classes;

use DateTime;

class FootballMatch
{
    private ?int $id;
    private DateTime $date;
    private string $city;
    private int $teamScore;
    private int $opponentScore;
    private int $teamId;
    private int $opposingClubId;

    public function __construct($id, $date, $city, $teamScore, $opponentScore, $teamId, int $opposingClubId)
    {
        $this->id = $id;
        $this->date = new DateTime($date);
        $this->city = $city;
        $this->teamScore = $teamScore;
        $this->opponentScore = $opponentScore;
        $this->teamId = $teamId;
        $this->opposingClubId = $opposingClubId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
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
    public function getTeamId(): int
    {
        return $this->teamId;
    }
    public function getOpposingClubId(): ?int
    {
        return $this->opposingClubId;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
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
    public function setTeamId(int $teamId): void
    {
        $this->teamId = $teamId;
    }
    public function setOpposingClubId(?int $opposingClubId): void
    {
        $this->opposingClubId = $opposingClubId;
    }
}
