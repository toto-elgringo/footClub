<?php

namespace Model\Classes;
use Model\Enum\PlayerRole;

class PlayerTeam
{
    private int $playerId;
    private int $teamId;
    private PlayerRole $role;

    public function __construct($playerId, $teamId, $role)
    {
        $this->playerId = $playerId;
        $this->teamId = $teamId;
        $this->role = $role;
    }

    public function getPlayerId(): int
    {
        return $this->playerId;
    }
    public function getTeamId(): int
    {
        return $this->teamId;
    }
    public function getRole(): PlayerRole
    {
        return $this->role;
    }

    public function setPlayerId(int $playerId): void
    {
        $this->playerId = $playerId;
    }
    public function setTeamId(int $teamId): void
    {
        $this->teamId = $teamId;
    }
    public function setRole(PlayerRole $role): void
    {
        $this->role = $role;
    }
}
