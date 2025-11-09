<?php

namespace App\Model\Classes;

use App\Model\Enum\PlayerRole;

class PlayerTeam
{
    public function __construct(
        private Player $player,
        private Team $team,
        private PlayerRole $role
    ) {}

    public function getPlayer(): Player
    {
        return $this->player;
    }
    public function getTeam(): Team
    {
        return $this->team;
    }
    public function getRole(): PlayerRole
    {
        return $this->role;
    }

    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }
    public function setTeam(Team $team): void
    {
        $this->team = $team;
    }
    public function setRole(PlayerRole $role): void
    {
        $this->role = $role;
    }
}
