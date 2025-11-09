<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\Classes\Team;
use App\Helper\FormValidator;
use App\Helper\Redirect;
use App\Helper\TwigRenderer;
use App\Model\Manager\TeamManager;

$teamManager = new TeamManager();
$oldName = $_GET['name'] ?? '';
$team = $teamManager->findByName($oldName);

$validator = new FormValidator();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_team'])) {
    $nom = trim($_POST['nom'] ?? '');
    $validator->required(['nom'], $_POST);
    if (empty($validator->getErrors())) {
        $updated = new Team($nom);

        if ($teamManager->update($updated, $oldName)) {
            Redirect::to("equipes.php");
        }
    }
}

TwigRenderer::display('pages/equipesUpdate.twig', [
    'team' => $team,
    'validator' => $validator
]);
