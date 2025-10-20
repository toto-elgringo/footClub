<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\Classes\Team;
use App\Helper\FormValidator;
use App\Helper\Redirect;
use App\Helper\TwigRenderer;
use App\Model\Manager\TeamManager;

$teamManager = new TeamManager();
$teamsWithCount = $teamManager->findAllWithPlayerCount();

$validator = new FormValidator();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int) $_POST['id'];

    $teamToDelete = $teamManager->findById($id);
    if ($teamToDelete instanceof Team) {
        if ($teamManager->delete($teamToDelete)) {
            Redirect::to("equipes.php");
        } else {
            $validator->addError("La suppression a échoué.");
        }
    } else {
        $validator->addError("Équipe introuvable.");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
    $validator->required(['nom'], $_POST);

    $nom = trim($_POST["nom"] ?? '');
    if (!$validator->hasErrors()) {
        $team = new Team(null, $nom);

        if ($teamManager->insert($team)) {
            Redirect::to("equipes.php");
        } else {
            $validator->addError("Une erreur est survenue lors de l'ajout de l'équipe.");
        }
    }
}

TwigRenderer::display('pages/equipes.twig', [
    'teamsWithCount' => $teamsWithCount,
    'validator' => $validator
]);
