<?php

namespace Model\manager;

use database\Database;
use PDO;
use Model\Classes\PlayerTeam;

class PlayerTeamManager
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $sql = "SELECT pht.*, t.name as team_name 
                FROM player_has_team pht 
                JOIN team t ON pht.team_id = t.id";

        $stmt = $this->db->query($sql);

        $playerTeams = [];

        while ($data = $stmt->fetch()) {
            $playerTeams[] = [
                "playerTeam" => new PlayerTeam(
                    $data['player_id'],
                    $data['team_id'],
                    $data['role']
                ),
                "team_name" => $data['team_name']
            ];
        }

        return $playerTeams;
    }

    public function exists(int $playerId, int $teamId): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM player_has_team WHERE player_id = ? AND team_id = ?");
        $stmt->execute([$playerId, $teamId]);
        return $stmt->fetchColumn() > 0;
    }

    public function insert(PlayerTeam $link): bool
    {
        $stmt = $this->db->prepare("INSERT INTO player_has_team (player_id, team_id, role) VALUES (?, ?, ?)");
        return $stmt->execute([
            $link->getPlayerId(),
            $link->getTeamId(),
            $link->getRole()
        ]);
    }
}
