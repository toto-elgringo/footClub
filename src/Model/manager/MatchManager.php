<?php

namespace Model\Manager;

use DateTime;
use Model\Classes\FootballMatch;
use Model\Trait\PdoTrait;
use Model\Trait\InstanceOfTrait;

class MatchManager implements ManagerInterface
{
    use PdoTrait, InstanceOfTrait;

    public function findAll(): array
    {
        $query = "
            SELECT m.*
            FROM `match` m
            ORDER BY m.date DESC
        ";

        $stmt = $this->db->query($query);

        $matches = [];

        while ($data = $stmt->fetch()) {
            $matches[] = new FootballMatch(
                $data['id'],
                new DateTime($data['date']),
                $data['city'],
                $data['team_score'],
                $data['opponent_score'],
                $data['team_id'],
                $data['opposing_club_id']
            );
        }

        return $matches;
    }

    public function findById(int $id): ?FootballMatch
    {
        $query = "
            SELECT m.*
            FROM `match` m
            WHERE m.id = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);

        $data = $stmt->fetch();

        if ($data) {
            return new FootballMatch(
                $data['id'],
                new DateTime($data['date']),
                $data['city'],
                $data['team_score'],
                $data['opponent_score'],
                $data['team_id'],
                $data['opposing_club_id']
            );
        }

        return null;
    }

    // passage de public function insert(FootballMatch $match): bool
    // a
    public function insert(object $object): bool
    {
        // avec le trait, passage de:
        // if(!$object instanceof FootballMatch) {
        //     return false;
        // }
        // à
        $this->checkInstanceOf($object, FootballMatch::class); // rajout d'une vérification pour voir si on passe le bon objet en paramètre
        
        $stmt = $this->db->prepare("INSERT INTO `match` (date, city, team_score, opponent_score, team_id, opposing_club_id) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $object->getDate()->format("Y-m-d H:i:s"),
            $object->getCity(),
            $object->getTeamScore(),
            $object->getOpponentScore(),
            $object->getTeamId(),
            $object->getOpposingClubId()
        ]);
    }

    public function delete(object $object): bool
    {
        $this->checkInstanceOf($object, FootballMatch::class);

        $stmt = $this->db->prepare("DELETE FROM `match` WHERE id = :id");
        return $stmt->execute(['id' => $object->getId()]);
    }

    public function update(object $object): bool
    {
        $this->checkInstanceOf($object, FootballMatch::class); 
        
        $stmt = $this->db->prepare("UPDATE `match` SET date = ?, city = ?, team_score = ?, opponent_score = ?, team_id = ?, opposing_club_id = ? WHERE id = ?");
        return $stmt->execute([
            $object->getDate()->format("Y-m-d H:i:s"),
            $object->getCity(),
            $object->getTeamScore(),
            $object->getOpponentScore(),
            $object->getTeamId(),
            $object->getOpposingClubId(),
            $object->getId()
        ]);
    }
}
