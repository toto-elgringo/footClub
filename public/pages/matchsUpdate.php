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

$oldDate = $_GET['date'] ?? '';
$oldCity = $_GET['city'] ?? '';
$match = $matchManager->findByDateAndCity($oldDate, $oldCity);
$teams = $teamManager->findAll();
$opposing_clubs = $opposingClubManager->findAll();

$validator = new FormValidator();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_match'])) {
    $team_name = trim($_POST['team_name'] ?? '');
    $opposing_club_city = trim($_POST['opposing_club_city'] ?? '');
    $match_date = trim($_POST['match_date'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $team_score = trim($_POST['team_score'] ?? '');
    $opponent_score = trim($_POST['opponent_score'] ?? '');

    if ($match_date === '' || $city === '' || $team_score === '' || $opponent_score === '') {
        $validator->addError("Champs requis manquants.");
    }

    if (empty($validator->getErrors())) {
        $team = !empty($team_name) ? $teamManager->findByName($team_name) : null;
        $opposingClub = $opposingClubManager->findByCity($opposing_club_city);

        if (!$opposingClub) {
            $validator->addError("Club adverse introuvable");
        } else {
            $updated = new FootballMatch(new DateTime($match_date), $city, $team_score, $opponent_score, $team, $opposingClub);

            if ($matchManager->update($updated, $oldDate, $oldCity)) {
                Redirect::to("matchs.php");
            }
        }
    }
}

TwigRenderer::display('pages/matchsUpdate.twig', [
    'match' => $match,
    'teams' => $teams,
    'opposing_clubs' => $opposing_clubs,
    'validator' => $validator
]);
