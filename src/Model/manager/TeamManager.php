<?php

namespace Model\Manager;

use Model\Classes\Team;
use Model\Manager\ManagerInterface;
use Model\Trait\PdoTrait;
use Model\Trait\InstanceOfTrait;


class TeamManager implements ManagerInterface
{
    use PdoTrait, InstanceOfTrait;

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM team");
        $teams = [];
        while ($data = $stmt->fetch()) {
            $teams[] = new Team($data['id'], $data['name']);
        }
        return $teams;
    }

    public function findAllTeams(): array
    {
        $query = "SELECT * FROM team ORDER BY name";
        $stmt = $this->db->query($query);

        $teams = [];
        while ($data = $stmt->fetch()) {
            $teams[] = new Team($data['id'], $data['name']);
        }
        return $teams;
    }


    public function findById(int $id): ?Team
    {
        $stmt = $this->db->prepare("SELECT * FROM team WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return new Team($data['id'], $data['name']);
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
            $team = new Team($data['id'], $data['name']);
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

        $stmt = $this->db->prepare("DELETE FROM team WHERE id = :id");
        return $stmt->execute(["id" => $object->getId()]);
    }

    public function update(object $object): bool
    {
        $this->checkInstanceOf($object, Team::class);

        $stmt = $this->db->prepare("UPDATE team SET name = ? WHERE id = ?");
        return $stmt->execute([
            $object->getName(),
            $object->getId()
        ]);
    }
}
