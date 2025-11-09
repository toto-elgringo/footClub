<?php

namespace App\Model\Manager;

use App\Model\Classes\PlayerTeam;
use App\Model\Classes\Player;
use App\Model\Classes\Team;
use App\Model\Manager\ManagerInterface;
use App\Model\Trait\PdoTrait;
use PDO;
use App\Model\Trait\InstanceOfTrait;
use App\Model\Trait\FindIdTrait;
use App\Model\Enum\PlayerRole;
use DateTime;

class PlayerTeamManager implements ManagerInterface
{
    use PdoTrait, InstanceOfTrait, FindIdTrait;

    public function findAll(): array
    {
        $sql = "SELECT p.firstname, p.lastname, p.birthdate, p.picture,
                       t.name as team_name, pht.role
                FROM player_has_team pht
                JOIN player p ON pht.player_id = p.id
                JOIN team t ON pht.team_id = t.id";

        $stmt = $this->db->query($sql);

        $playerTeams = [];

        while ($data = $stmt->fetch()) {
            $player = new Player(
                $data['firstname'],
                $data['lastname'],
                new DateTime($data['birthdate']),
                $data['picture']
            );
            $team = new Team($data['team_name']);

            $playerTeams[] = [
                "playerTeam" => new PlayerTeam(
                    $player,
                    $team,
                    PlayerRole::from($data['role'])
                ),
                "team_name" => $data['team_name']
            ];
        }

        return $playerTeams;
    }

    public function exists(string $playerFirstname, string $playerLastname, string $teamName): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM player_has_team pht
            JOIN player p ON pht.player_id = p.id
            JOIN team t ON pht.team_id = t.id
            WHERE p.firstname = ? AND p.lastname = ? AND t.name = ?
        ");
        $stmt->execute([$playerFirstname, $playerLastname, $teamName]);
        return $stmt->fetchColumn() > 0;
    }

    public function findByPlayerName(string $firstname, string $lastname): ?object
    {
        $sql = "SELECT p.firstname, p.lastname, p.birthdate, p.picture,
                       t.name as team_name, pht.role
                FROM player_has_team pht
                JOIN player p ON pht.player_id = p.id
                JOIN team t ON pht.team_id = t.id
                WHERE p.firstname = :firstname AND p.lastname = :lastname";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['firstname' => $firstname, 'lastname' => $lastname]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $player = new Player(
            $data['firstname'],
            $data['lastname'],
            new DateTime($data['birthdate']),
            $data['picture']
        );
        $team = new Team($data['team_name']);

        return new PlayerTeam(
            $player,
            $team,
            PlayerRole::from($data['role'])
        );
    }

    public function insert(object $object): bool
    {
        $this->checkInstanceOf($object, PlayerTeam::class);

        $playerId = $this->findPlayerId($object->getPlayer());
        $teamId = $this->findTeamId($object->getTeam());

        $stmt = $this->db->prepare("INSERT INTO player_has_team (player_id, team_id, role) VALUES (?, ?, ?)");
        return $stmt->execute([
            $playerId,
            $teamId,
            $object->getRole()->value
        ]);
    }

    public function delete(object $object): bool
    {
        $this->checkInstanceOf($object, PlayerTeam::class);

        $playerId = $this->findPlayerId($object->getPlayer());
        $teamId = $this->findTeamId($object->getTeam());

        $stmt = $this->db->prepare("DELETE FROM player_has_team WHERE player_id = ? AND team_id = ?");
        return $stmt->execute([$playerId, $teamId]);
    }

    public function update(object $object): bool
    {
        $this->checkInstanceOf($object, PlayerTeam::class);

        $playerId = $this->findPlayerId($object->getPlayer());
        $teamId = $this->findTeamId($object->getTeam());

        $stmt = $this->db->prepare("UPDATE player_has_team SET role = ? WHERE player_id = ? AND team_id = ?");
        return $stmt->execute([
            $object->getRole()->value,
            $playerId,
            $teamId
        ]);
    }
}
