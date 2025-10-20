<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Helper\TwigRenderer;
use App\Model\Manager\PlayerManager;
use App\Model\Manager\TeamManager;

$playerManager = new PlayerManager();
$teamManager = new TeamManager();

$players = $playerManager->findAll();
$teams = $teamManager->findAll();

TwigRenderer::display('pages/index.twig', [
    'players' => $players,
    'teams' => $teams
]);
