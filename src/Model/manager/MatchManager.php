<?php

namespace Model\manager;

use database\Database;
use PDO;
use Model\Classes\FootballMatch;

class MatchManager {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findAll(): array {
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
                $data['date'],
                $data['city'],
                $data['team_score'],
                $data['opponent_score'],
                $data['team_id'],
                $data['opposing_club_id']
            );
        }

        return $matches;
    }

    public function findById(int $id): ?FootballMatch {
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
                $data['date'],
                $data['city'],
                $data['team_score'],
                $data['opponent_score'],
                $data['team_id'],
                $data['opposing_club_id']
            );
        }

        return null;
    }

    public function insert(FootballMatch $match): bool {
        $stmt = $this->db->prepare("INSERT INTO `match` (date, city, team_score, opponent_score, team_id, opposing_club_id) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $match->getDate()->format("Y-m-d H:i:s"),
            $match->getCity(),
            $match->getTeamScore(),
            $match->getOpponentScore(),
            $match->getTeamId(),
            $match->getOpposingClubId()
        ]);
    }

    public function delete(FootballMatch $match): bool {
        $stmt = $this->db->prepare("DELETE FROM `match` WHERE id = :id");
        return $stmt->execute(['id' => $match->getId()]);
    }

    public function update(FootballMatch $match): bool {
        $stmt = $this->db->prepare("UPDATE `match` SET date = ?, city = ?, team_score = ?, opponent_score = ?, team_id = ?, opposing_club_id = ? WHERE id = ?");
        return $stmt->execute([
            $match->getDate()->format("Y-m-d H:i:s"),
            $match->getCity(),
            $match->getTeamScore(),
            $match->getOpponentScore(),
            $match->getTeamId(),
            $match->getOpposingClubId(),
            $match->getId()
        ]);
    }
}