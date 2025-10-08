<?php

namespace Model\manager;

use database\Database;
use PDO;
use Model\Classes\PlayerTeam;
use Model\Manager\ManagerInterface;

class PlayerTeamManager implements ManagerInterface
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

    public function findById(int $id): ?object
    {
        $sql = "SELECT pht.*, t.name as team_name 
                FROM player_has_team pht 
                JOIN team t ON pht.team_id = t.id 
                WHERE pht.player_id = :id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            return null;
        }
        
        return new PlayerTeam(
            $data['player_id'],
            $data['team_id'],
            $data['role']
        );
    }

    public function insert(object $object): bool
    {
        if (!$object instanceof PlayerTeam) {
            return false;
        }

        $link = $object;
        $stmt = $this->db->prepare("INSERT INTO player_has_team (player_id, team_id, role) VALUES (?, ?, ?)");
        return $stmt->execute([
            $link->getPlayerId(),
            $link->getTeamId(),
            $link->getRole()
        ]);
    }

    public function delete(object $object): bool
    {
        if (!$object instanceof PlayerTeam) {
            return false;
        }

        $link = $object;
        $stmt = $this->db->prepare("DELETE FROM player_has_team WHERE player_id = ? AND team_id = ?");
        return $stmt->execute([$link->getPlayerId(), $link->getTeamId()]);
    }

    public function update(object $object): bool
    {
        if (!$object instanceof PlayerTeam) {
            return false;
        }

        $link = $object;
        $stmt = $this->db->prepare("UPDATE player_has_team SET player_id = ?, team_id = ?, role = ? WHERE player_id = ? AND team_id = ?");
        return $stmt->execute([
            $link->getPlayerId(),
            $link->getTeamId(),
            $link->getRole(),
            $link->getPlayerId(),
            $link->getTeamId()
        ]);
    }
}
