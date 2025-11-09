<?php

namespace App\Model\Manager;

use App\Model\Classes\Team;
use App\Model\Manager\ManagerInterface;
use App\Model\Trait\PdoTrait;
use App\Model\Trait\InstanceOfTrait;


class TeamManager implements ManagerInterface
{
    use PdoTrait, InstanceOfTrait;

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM team");
        $teams = [];
        while ($data = $stmt->fetch()) {
            $teams[] = new Team($data['name']);
        }
        return $teams;
    }

    public function findAllTeams(): array
    {
        $query = "SELECT * FROM team ORDER BY name";
        $stmt = $this->db->query($query);

        $teams = [];
        while ($data = $stmt->fetch()) {
            $teams[] = new Team($data['name']);
        }
        return $teams;
    }

    public function findByName(string $name): ?Team
    {
        $stmt = $this->db->prepare("SELECT * FROM team WHERE name = ?");
        $stmt->execute([$name]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return new Team($data['name']);
    }

    public function findAllWithPlayerCount(): array
    {
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
            $team = new Team($data['name']);
            $playerCount[] = [
                'team' => $team,
                'player_count' => (int)$data['player_count']
            ];
        }

        return $playerCount;
    }

    public function insert(object $object): bool
    {
        $this->checkInstanceOf($object, Team::class);

        $stmt = $this->db->prepare("INSERT INTO team (name) VALUES (:name)");
        return $stmt->execute(["name" => $object->getName()]);
    }

    public function delete(object $object): bool
    {
        $this->checkInstanceOf($object, Team::class);

        $stmt = $this->db->prepare("DELETE FROM team WHERE name = :name");
        return $stmt->execute(["name" => $object->getName()]);
    }

    public function update(object $object, string $oldName): bool
    {
        $this->checkInstanceOf($object, Team::class);

        $stmt = $this->db->prepare("UPDATE team SET name = ? WHERE name = ?");
        return $stmt->execute([
            $object->getName(),
            $oldName
        ]);
    }
}
