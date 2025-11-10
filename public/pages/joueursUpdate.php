<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\Classes\Player;
use App\Model\Classes\PlayerTeam;
use App\Model\Enum\PlayerRole;
use App\Helper\UploadPicture;
use App\Helper\FormValidator;
use App\Helper\Redirect;
use App\Helper\TwigRenderer;
use App\Model\Manager\PlayerManager;
use App\Model\Manager\TeamManager;
use App\Model\Manager\PlayerTeamManager;

$playerManager = new PlayerManager();
$teamManager = new TeamManager();
$playerTeamManager = new PlayerTeamManager();

$player = isset($_GET['firstname'], $_GET['lastname'])
    ? $playerManager->findByName($_GET['firstname'], $_GET['lastname'])
    : null;
$validator = new FormValidator();

// récupération des équipes du joueur
$playerTeams = [];
if ($player) {
    $allPlayerTeams = $playerTeamManager->findAll();
    foreach ($allPlayerTeams as $item) {
        $relation = $item['playerTeam'];
        if ($relation->getPlayer()->getFirstname() === $player->getFirstname() &&
            $relation->getPlayer()->getLastname() === $player->getLastname()) {
            $playerTeams[] = $relation;
        }
    }
}

// récupération des équipes disponibles
$allTeams = $teamManager->findAll();
$availableTeams = [];
foreach ($allTeams as $team) {
    $isAssigned = false;
    foreach ($playerTeams as $relation) {
        if ($relation->getTeam()->getName() === $team->getName()) {
            $isAssigned = true;
            break;
        }
    }
    if (!$isAssigned) {
        $availableTeams[] = $team;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_from_team') {
    $team_name = trim($_POST['team_name'] ?? '');

    if ($player && $team_name) {
        $team = $teamManager->findByName($team_name);
        if ($team) {
            // trouve la relation PlayerTeam existante pour obtenir le rôle
            $allPlayerTeams = $playerTeamManager->findAll();
            $playerTeamToDelete = null;
            foreach ($allPlayerTeams as $item) {
                $relation = $item['playerTeam'];
                if ($relation->getPlayer()->getFirstname() === $player->getFirstname() &&
                    $relation->getPlayer()->getLastname() === $player->getLastname() &&
                    $relation->getTeam()->getName() === $team_name) {
                    $playerTeamToDelete = $relation;
                    break;
                }
            }

            if ($playerTeamToDelete && $playerTeamManager->delete($playerTeamToDelete)) {
                Redirect::to("joueursUpdate.php?firstname=" . urlencode($player->getFirstname()) . "&lastname=" . urlencode($player->getLastname()));
            } else {
                $validator->addError("Erreur lors de la suppression de l'équipe.");
            }
        } else {
            $validator->addError("Équipe introuvable.");
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_team') {
    $team_name = trim($_POST['team_name'] ?? '');
    $roleStr = trim($_POST['role'] ?? '');

    if ($player && $team_name && $roleStr) {
        $team = $teamManager->findByName($team_name);
        $role = PlayerRole::from($roleStr);

        if ($team && !$playerTeamManager->exists($player->getFirstname(), $player->getLastname(), $team_name)) {
            $playerTeam = new PlayerTeam($player, $team, $role);
            if ($playerTeamManager->insert($playerTeam)) {
                Redirect::to("joueursUpdate.php?firstname=" . urlencode($player->getFirstname()) . "&lastname=" . urlencode($player->getLastname()));
            } else {
                $validator->addError("Erreur lors de l'ajout à l'équipe.");
            }
        } else {
            $validator->addError("Le joueur appartient déjà à cette équipe.");
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_player'])) {
    $old_firstname = trim($_POST['old_firstname'] ?? '');
    $old_lastname = trim($_POST['old_lastname'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');

    if ($prenom === '' || $nom === '' || $birthdate === '') {
        $validator->addError("Champs requis manquants.");
    }

    $newPicture = $player->getPicture();

    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        UploadPicture::delete($player->getPicture());
        $uploadResult = UploadPicture::upload($_FILES['picture'], 'player_');
        if ($uploadResult['success']) {
            $newPicture = $uploadResult['filename'];
        } else {
            $validator->addError($uploadResult['error']);
        }
    }

    if (!$validator->hasErrors()) {
        $updated = new Player($prenom, $nom, new DateTime($birthdate), $newPicture);
        if ($playerManager->update($updated, $old_firstname, $old_lastname)) {
            Redirect::to("joueurs.php");
        } else {
            $validator->addError("Erreur lors de la mise à jour du joueur.");
        }
    }
}

TwigRenderer::display('pages/joueursUpdate.twig', [
    'player' => $player,
    'playerTeams' => $playerTeams,
    'availableTeams' => $availableTeams,
    'validator' => $validator
]);
