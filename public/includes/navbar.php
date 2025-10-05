<?php
require_once __DIR__ . '/../autoloader.php';
require_once __DIR__ . '/../../src/function/UploadPicture.php';

$playerManager      = new src\Model\manager\PlayerManager();
$teamManager        = new src\Model\manager\TeamManager();
$playerTeamManager  = new src\Model\manager\PlayerTeamManager();
$opposingClubManager= new src\Model\manager\OpposingClubManager();
$staffMemberManager = new src\Model\manager\StaffMemberManager();
$matchManager       = new src\Model\manager\MatchManager();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" href="../css/navbar.css">
</head>

<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

nav {
    width: 20vw;
    min-width: 250px;
    height: 100vh;
    box-shadow: 10px 0px 15px rgba(0, 0, 0, 0.1);
    padding: 25px;
}

.nav-title {
    display: flex;
    align-items: center;
    margin-bottom: 50px;
}

.nav-title svg {
    background-color: #37c60f;
    border-radius: 100%;
    padding: 10px;
    fill: white;
    width: 50px;
    height: 50px;
}

.nav-title-text {
    margin-left: 10px;
}

nav ul {
    list-style: none;
}

nav ul li {
    margin-bottom: 10px;
}

nav ul li a {
    text-decoration: none;
    color: black;
}
</style>

<body>
    <nav>
        <div class="nav-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256">
                <path d="M232,64H208V48a8,8,0,0,0-8-8H56a8,8,0,0,0-8,8V64H24A16,16,0,0,0,8,80V96a40,40,0,0,0,40,40h3.65A80.13,80.13,0,0,0,120,191.61V216H96a8,8,0,0,0,0,16h64a8,8,0,0,0,0-16H136V191.58c31.94-3.23,58.44-25.64,68.08-55.58H208a40,40,0,0,0,40-40V80A16,16,0,0,0,232,64ZM48,120A24,24,0,0,1,24,96V80H48v32q0,4,.39,8Zm144-8.9c0,35.52-29,64.64-64,64.9a64,64,0,0,1-64-64V56H192ZM232,96a24,24,0,0,1-24,24h-.5a81.81,81.81,0,0,0,.5-8.9V80h24Z"></path>
            </svg>
            <div class="nav-title-text">
                <h2>Foot Club</h2>
                <p>Gestion Club</p>
            </div>
        </div>

        <ul>
            <li><a href="index.php">👤 Tableau de bord</a></li>
            <br>
            <li><a href="joueurs.php">🛡️ Joueurs</a></li>
            <br>
            <li><a href="equipes.php">🧑‍💼 Equipes</a></li>
            <br>
            <li><a href="matchs.php">📅 Matchs</a></li>
            <br>
            <li><a href="staff.php">👥 Staff</a></li>
        </ul>
    </nav>

</body>

</html>