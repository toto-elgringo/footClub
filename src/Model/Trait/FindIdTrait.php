<?php

namespace App\Model\Trait;

use App\Model\Classes\Player;
use App\Model\Classes\Team;
use App\Model\Classes\OpposingClub;

trait FindIdTrait
{
    protected function findPlayerId(Player $player): int|false
    {
        $stmtPlayer = $this->db->prepare("SELECT id FROM player WHERE firstname = ? AND lastname = ?");
        $stmtPlayer->execute([
            $player->getFirstname(),
            $player->getLastname()
        ]);
        return $stmtPlayer->fetchColumn();
    }

    protected function findTeamId(Team $team): int|false
    {
        $stmtTeam = $this->db->prepare("SELECT id FROM team WHERE name = ?");
        $stmtTeam->execute([$team->getName()]);
        return $stmtTeam->fetchColumn();
    }

    protected function findOpposingClubId(OpposingClub $club): int|false
    {
        $stmtClub = $this->db->prepare("SELECT id FROM opposing_club WHERE address = ? AND city = ?");
        $stmtClub->execute([
            $club->getName(),
            $club->getCity()
        ]);
        return $stmtClub->fetchColumn();
    }
}
