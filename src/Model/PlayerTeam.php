<?php

namespace src\Model;

class PlayerTeam {
    private int $playerId;
    private int $teamId;
    private string $role;

    public function __construct($playerId, $teamId, $role) {
        $this->playerId = $playerId;
        $this->teamId = $teamId;
        $this->role = $role;
    }

    public function getPlayerId(): int { return $this->playerId; }
    public function getTeamId(): int { return $this->teamId; }
    public function getRole(): string { return $this->role; }

    public function setPlayerId(int $playerId): void { $this->playerId = $playerId; }
    public function setTeamId(int $teamId): void { $this->teamId = $teamId; }
    public function setRole(string $role): void { $this->role = $role; }
}
