<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\Classes\Player;
use App\Model\Classes\PlayerTeam;
use App\Helper\UploadPicture;
use App\Model\Enum\PlayerRole;
use App\Helper\FormValidator;
use App\Helper\Redirect;
use App\Helper\TwigRenderer;
use App\Model\Manager\PlayerManager;
use App\Model\Manager\TeamManager;
use App\Model\Manager\PlayerTeamManager;

$playerManager = new PlayerManager();
$teamManager = new TeamManager();
$playerTeamManager = new PlayerTeamManager();

$players = $playerManager->findAll();
$teams = $teamManager->findAll();
$playerTeam = $playerTeamManager->findAll();

$validator = new FormValidator();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    $playerToDelete = $playerManager->findById($id);

    if ($playerToDelete instanceof Player) {
        UploadPicture::delete($playerToDelete->getPicture());

        if ($playerManager->delete($playerToDelete)) {
            Redirect::to("joueurs.php");
        } else {
            $validator->addError("La suppression a échoué.");
        }
    } else {
        $validator->addError("Joueur introuvable.");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['player_id'], $_POST['team_id'], $_POST['role'])) {
    $player_id = trim($_POST['player_id']);
    $team_id = trim($_POST['team_id']);
    $roleStr = trim($_POST['role']);

    $role = PlayerRole::from($roleStr);

    if ($playerTeamManager->exists($player_id, $team_id)) {
        $validator->addError("Le joueur appartient déjà à l'équipe");
    } else {
        $playerTeam = new PlayerTeam($player_id, $team_id, $role);

        if ($playerTeamManager->insert($playerTeam)) {
            Redirect::to("joueurs.php");
        } else {
            $validator->addError("Une erreur est survenue lors de l'ajout du joueur à l'équipe");
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nom'], $_POST['prenom'], $_POST['birthdate'], $_FILES['picture'])) {
    $nom = trim($_POST["nom"] ?? '');
    $prenom = trim($_POST["prenom"] ?? '');
    $birthdate = trim($_POST["birthdate"] ?? '');
    $picture = $_FILES["picture"] ?? null;

    if (empty($nom) || empty($prenom) || empty($birthdate) || !$picture || $picture['error'] !== UPLOAD_ERR_OK) {
        $validator->addError("Tous les champs, y compris l'image, doivent être remplis");
    }

    if (!$validator->hasErrors()) {
        $uploadResult = UploadPicture::upload($picture, 'player_');

        if ($uploadResult['success']) {
            try {
                $player = new Player(null, $prenom, $nom, new DateTime($birthdate), $uploadResult['filename']);

                if ($playerManager->insert($player)) {
                    Redirect::to("joueurs.php");
                } else {
                    $validator->addError("Échec de l'insertion du joueur dans la base de données");
                }
            } catch (PDOException $e) {
                $validator->addError("Erreur lors de l'ajout du joueur : " . $e->getMessage());
            }
        } else {
            $validator->addError($uploadResult['error'] ?? "Erreur lors du téléchargement de l'image");
        }
    }
}

TwigRenderer::display('pages/joueurs.twig', [
    'players' => $players,
    'teams' => $teams,
    'playerTeam' => $playerTeam,
    'playerManager' => $playerManager,
    'validator' => $validator
]);
