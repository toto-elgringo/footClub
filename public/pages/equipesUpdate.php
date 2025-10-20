<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\Classes\Team;
use App\Helper\FormValidator;
use App\Helper\Redirect;
use App\Helper\TwigRenderer;
use App\Model\Manager\TeamManager;

$teamManager = new TeamManager();
$team = $teamManager->findById($_GET['id']);

$validator = new FormValidator();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_team'])) {
    $nom = trim($_POST['nom'] ?? '');
    $validator->required(['nom'], $_POST);
    if (empty($validator->getErrors())) {
        $updated = new Team($team->getId(), $nom);

        if ($teamManager->update($updated)) {
            Redirect::to("equipes.php");
        }
    }
}

TwigRenderer::display('pages/equipesUpdate.twig', [
    'team' => $team,
    'validator' => $validator
]);
