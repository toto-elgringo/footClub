<?php

namespace src\Model\manager;

use database\Database;
use PDO;
use src\Model\Team;

class TeamManager {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM team");
        $teams = [];
        while ($data = $stmt->fetch()) {
            $teams[] = new Team($data['id'], $data['name']);
        }
        return $teams;
    }

    public function findAllTeams(): array {
        $query = "SELECT * FROM team ORDER BY name";
        $stmt = $this->db->query($query);
    
        $teams = [];
        while ($data = $stmt->fetch()) {
            $teams[] = new Team($data['id'], $data['name']);
        }
        return $teams;
    }
    
    
    public function findById(int $id): ?Team {
        $stmt = $this->db->prepare("SELECT * FROM team WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        
        if (!$data) {
            return null;
        }
        
        return new Team($data['id'], $data['name']);
    }

    public function findAllWithPlayerCount(): array {
        $query = "
            SELECT t.*, COUNT(DISTINCT pt.player_id) as player_count
            FROM team t
            LEFT JOIN player_has_team pt ON t.id = pt.team_id
            GROUP BY t.id
            ORDER BY t.name
        ";
        $stmt = $this->db->query($query);
        
        $playerCount = [];
        while ($data = $stmt->fetch()) {
            $team = new Team($data['id'], $data['name']);
            $playerCount[] = [
                'team' => $team,
                'player_count' => (int)$data['player_count']
            ];
        }
        
        return $playerCount;
    }

    public function insert(Team $team): bool {
        $stmt = $this->db->prepare("INSERT INTO team (name) VALUES (:name)");
        $result = $stmt->execute([
            "name" => $team->getName()
        ]);
        
        if ($result) {
            $team->setId((int)$this->db->lastInsertId());
            return true;
        }
        
        return false;
    }
}
