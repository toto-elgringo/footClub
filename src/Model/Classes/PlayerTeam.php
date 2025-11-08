<?php

namespace App\Model\Classes;

use App\Model\Enum\PlayerRole;

class PlayerTeam
{
    public function __construct(
        private int $playerId,
        private int $teamId,
        private PlayerRole $role
    ) {}

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
