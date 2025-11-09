<?php

namespace App\Model\Manager;

use DateTime;
use App\Model\Classes\FootballMatch;
use App\Model\Classes\Team;
use App\Model\Classes\OpposingClub;
use App\Model\Trait\PdoTrait;
use App\Model\Trait\InstanceOfTrait;
use App\Model\Trait\FindIdTrait;

class MatchManager implements ManagerInterface
{
    use PdoTrait, InstanceOfTrait, FindIdTrait;

    public function findAll(): array
    {
        $query = "
            SELECT m.*, t.name as team_name, oc.address as club_name, oc.city as club_city
            FROM `match` m
            LEFT JOIN team t ON m.team_id = t.id
            JOIN opposing_club oc ON m.opposing_club_id = oc.id
            ORDER BY m.date DESC
        ";

        $stmt = $this->db->query($query);

        $matches = [];

        while ($data = $stmt->fetch()) {
            $team = $data['team_name'] ? new Team($data['team_name']) : null;
            $opposingClub = new OpposingClub($data['club_name'], $data['club_city']);

            $matches[] = new FootballMatch(
                new DateTime($data['date']),
                $data['city'],
                $data['team_score'],
                $data['opponent_score'],
                $team,
                $opposingClub
            );
        }

        return $matches;
    }

    public function findByDateAndCity(string $date, string $city): ?FootballMatch
    {
        $query = "
            SELECT m.*, t.name as team_name, oc.address as club_name, oc.city as club_city
            FROM `match` m
            LEFT JOIN team t ON m.team_id = t.id
            JOIN opposing_club oc ON m.opposing_club_id = oc.id
            WHERE m.date = :date AND m.city = :city
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['date' => $date, 'city' => $city]);

        $data = $stmt->fetch();

        if ($data) {
            $team = $data['team_name'] ? new Team($data['team_name']) : null;
            $opposingClub = new OpposingClub($data['club_name'], $data['club_city']);

            return new FootballMatch(
                new DateTime($data['date']),
                $data['city'],
                $data['team_score'],
                $data['opponent_score'],
                $team,
                $opposingClub
            );
        }

        return null;
    }

    public function insert(object $object): bool
    {
        $this->checkInstanceOf($object, FootballMatch::class);

        $teamId = $object->getTeam() !== null ? $this->findTeamId($object->getTeam()) : null;
        $opposingClubId = $this->findOpposingClubId($object->getOpposingClub());

        $stmt = $this->db->prepare("INSERT INTO `match` (date, city, team_score, opponent_score, team_id, opposing_club_id) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $object->getDate()->format("Y-m-d H:i:s"),
            $object->getCity(),
            $object->getTeamScore(),
            $object->getOpponentScore(),
            $teamId,
            $opposingClubId
        ]);
    }

    public function delete(object $object): bool
    {
        $this->checkInstanceOf($object, FootballMatch::class);

        $stmt = $this->db->prepare("DELETE FROM `match` WHERE date = ? AND city = ?");
        return $stmt->execute([
            $object->getDate()->format("Y-m-d H:i:s"),
            $object->getCity()
        ]);
    }

    public function update(object $object, string $oldDate, string $oldCity): bool
    {
        $this->checkInstanceOf($object, FootballMatch::class);

        $teamId = $object->getTeam() !== null ? $this->findTeamId($object->getTeam()) : null;
        $opposingClubId = $this->findOpposingClubId($object->getOpposingClub());

        $stmt = $this->db->prepare("UPDATE `match` SET date = ?, city = ?, team_score = ?, opponent_score = ?, team_id = ?, opposing_club_id = ? WHERE date = ? AND city = ?");
        return $stmt->execute([
            $object->getDate()->format("Y-m-d H:i:s"),
            $object->getCity(),
            $object->getTeamScore(),
            $object->getOpponentScore(),
            $teamId,
            $opposingClubId,
            $oldDate,
            $oldCity
        ]);
    }
}
