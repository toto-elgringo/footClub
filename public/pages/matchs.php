<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\Classes\OpposingClub;
use App\Model\Classes\FootballMatch;
use App\Helper\FormValidator;
use App\Helper\Redirect;
use App\Helper\TwigRenderer;
use App\Model\Manager\MatchManager;
use App\Model\Manager\OpposingClubManager;
use App\Model\Manager\TeamManager;

$matchManager = new MatchManager();
$opposingClubManager = new OpposingClubManager();
$teamManager = new TeamManager();

$matchs = $matchManager->findAll();
$opposing_clubs = $opposingClubManager->findAll();
$teams = $teamManager->findAllTeams();

$validator = new FormValidator();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['date'], $_POST['city'])) {
    $date = $_POST['date'];
    $city = trim($_POST['city']);
    $matchToDelete = $matchManager->findByDateAndCity($date, $city);

    if ($matchToDelete instanceof FootballMatch) {
        if ($matchManager->delete($matchToDelete)) {
            Redirect::to("matchs.php");
        } else {
            $validator->addError("La suppression a échoué.");
        }
    } else {
        $validator->addError("Match introuvable.");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['new_club_city']) || isset($_POST['new_club_address'])) {
        $club_city = trim($_POST['new_club_city']);
        $club_address = trim($_POST['new_club_address']);

        if (empty($club_city) || empty($club_address)) {
            $validator->addError("La ville et l'adresse du club sont obligatoires");
        } else {
            try {
                $existingClub = $opposingClubManager->findByCity($club_city);

                if ($existingClub) {
                    $validator->addError("Un club existe déjà pour cette ville");
                } else {
                    $newClub = new OpposingClub($club_address, $club_city);
                    $opposingClubManager->insert($newClub);
                }
            } catch (PDOException $e) {
                $validator->addError("Erreur lors de l'ajout du club : " . $e->getMessage());
            }
        }
    }

    if (isset($_POST['team_name'], $_POST['match_date'], $_POST['opposing_club_city'])) {
        $team_name = trim($_POST['team_name']);
        $team_score = (int)$_POST['team_score'];
        $opponent_score = (int)$_POST['opponent_score'];
        $date = $_POST['match_date'];
        $city = trim($_POST['city']);
        $opposing_club_city = trim($_POST['opposing_club_city']);

        if (empty($date) || empty($city) || empty($opposing_club_city)) {
            $validator->addError("Tous les champs doivent être remplis");
        } elseif ($team_score < 0 || $opponent_score < 0) {
            $validator->addError("Les scores doivent être positifs");
        } elseif (!strtotime($date)) {
            $validator->addError("Date invalide");
        }

        if (empty($validator->getErrors())) {
            try {
                $dateTime = new DateTime($date);
                $team = !empty($team_name) ? $teamManager->findByName($team_name) : null;
                $opposingClub = $opposingClubManager->findByCity($opposing_club_city);

                if (!$opposingClub) {
                    $validator->addError("Club adverse introuvable");
                } else {
                    $match = new FootballMatch($dateTime, $city, $team_score, $opponent_score, $team, $opposingClub);
                    $matchManager->insert($match);
                }
            } catch (Exception $e) {
                $validator->addError("Erreur lors de la création du match : " . $e->getMessage());
            }
        }
    }

    if (empty($validator->getErrors())) {
        Redirect::to("matchs.php");
    }
}

TwigRenderer::display('pages/matchs.twig', [
    'matchs' => $matchs,
    'opposing_clubs' => $opposing_clubs,
    'teams' => $teams,
    'validator' => $validator,
    'teamManager' => $teamManager,
    'opposingClubManager' => $opposingClubManager,
    'now' => new DateTime()  // Pour comparer les dates dans le template
]);
