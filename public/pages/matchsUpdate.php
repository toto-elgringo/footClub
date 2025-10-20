<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\Classes\FootballMatch;
use App\Helper\FormValidator;
use App\Helper\Redirect;
use App\Helper\TwigRenderer;
use App\Model\Manager\MatchManager;
use App\Model\Manager\TeamManager;
use App\Model\Manager\OpposingClubManager;

$matchManager = new MatchManager();
$teamManager = new TeamManager();
$opposingClubManager = new OpposingClubManager();

$match = $matchManager->findById($_GET['id']);
$teams = $teamManager->findAll();
$opposing_clubs = $opposingClubManager->findAll();

$validator = new FormValidator();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_match'])) {
    $team_id = trim($_POST['team_id'] ?? '');
    $opposing_club_id = trim($_POST['opposing_club_id'] ?? '');
    $match_date = trim($_POST['match_date'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $team_score = trim($_POST['team_score'] ?? '');
    $opponent_score = trim($_POST['opponent_score'] ?? '');

    if ($team_id === '' || $opposing_club_id === '' || $match_date === '' || $city === '' || $team_score === '' || $opponent_score === '') {
        $validator->addError("Champs requis manquants.");
    }

    if (empty($validator->getErrors())) {
        $updated = new FootballMatch($match->getId(), new DateTime($match_date), $city, $team_score, $opponent_score, $team_id, $opposing_club_id);

        if ($matchManager->update($updated)) {
            Redirect::to("matchs.php");
        }
    }
}

TwigRenderer::display('pages/matchsUpdate.twig', [
    'match' => $match,
    'teams' => $teams,
    'opposing_clubs' => $opposing_clubs,
    'validator' => $validator
]);
