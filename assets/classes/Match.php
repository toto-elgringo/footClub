<?php

class FootBallMatch // renomé car il beugue si juste    class Match
{
    public int $teamScore;
    public int $opponentScore;
    public string $date;
    public string $team;
    public string $city;
    public string $opposingClub;

    public function __construct(int $teamScore, int $opponentScore, string $date, string $team, string $city, string $opposingClub)
    {
        $this->teamScore = $teamScore;
        $this->opponentScore = $opponentScore;
        $this->date = $date;
        $this->team = $team;
        $this->city = $city;
        $this->opposingClub = $opposingClub;
    }

    public function getTeamScore(): int
    {
        return $this->teamScore;
    }

    public function getOpponentScore(): int
    {
        return $this->opponentScore;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getTeam(): string
    {
        return $this->team;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getOpposingClub(): string
    {
        return $this->opposingClub;
    }




    public function setTeamScore(int $teamScore): void
    {
        $this->teamScore = $teamScore;
    }

    public function setOpponentScore(int $opponentScore): void
    {
        $this->opponentScore = $opponentScore;
    }

    public function setDate (string $date): void
    {
        $this->date = $date;
    }

    public function setTeam(string $team): void
    {
        $this->team = $team;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function setOpposingClub(string $opposingClub): void
    {
        $this->opposingClub = $opposingClub;
    }

}