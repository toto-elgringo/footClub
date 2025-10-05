<?php

include "includes/navbar.php";

use src\Model\Player;
use src\Model\PlayerTeam;

$players = $playerManager->findAll();
$teams = $teamManager->findAll();
$playerTeam = $playerTeamManager->findAll();

$error = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    $playerToDelete = $playerManager->findById($id);

    if ($playerToDelete instanceof src\Model\Player) { // on vérifie si la variable $playerToDelete est une instance de la classe Player, instanceof est un test de type en PHP orienté objet.
        if ($playerManager->delete($playerToDelete)) {
            header("Location: joueurs.php");
            exit;
        } else {
            $error[] = "La suppression a échoué.";
        }
    } else {
        $error[] = "Joueur introuvable.";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['player_id'], $_POST['team_id'], $_POST['role'])) {
    $player_id = trim($_POST['player_id']);
    $team_id = trim($_POST['team_id']);
    $role = trim($_POST['role']);

    if ($playerTeamManager->exists($player_id, $team_id)) {
        $error[] = "Le joueur appartient déjà à l'équipe";
    } else {
        $playerTeam = new PlayerTeam($player_id, $team_id, $role);

        if ($playerTeamManager->insert($playerTeam)) {
            header("Location: joueurs.php");
            exit;
        } else {
            $error[] = "Une erreur est survenue lors de l'ajout du joueur à l'équipe";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nom'], $_POST['prenom'], $_POST['birthdate'], $_FILES['picture'])) {
    $nom = trim($_POST["nom"] ?? '');
    $prenom = trim($_POST["prenom"] ?? '');
    $birthdate = trim($_POST["birthdate"] ?? '');
    $picture = $_FILES["picture"] ?? null;

    if (empty($nom) || empty($prenom) || empty($birthdate) || !$picture || $picture['error'] !== UPLOAD_ERR_OK) {
        $error[] = "Tous les champs, y compris l'image, doivent être remplis";
    }

    if (empty($error)) {
        $uploadResult = UploadPicture::upload($picture, 'player_');

        if ($uploadResult['success']) {
            try {
                $player = new Player(null, $prenom, $nom, $birthdate, $uploadResult['filename']);

                if ($playerManager->insert($player)) {
                    header("Location: joueurs.php");
                    exit;
                } else {
                    $error[] = "Échec de l'insertion du joueur dans la base de données";
                }
            } catch (PDOException $e) {
                $error[] = "Erreur lors de l'ajout du joueur : " . $e->getMessage();
            }
        } else {
            $error[] = $uploadResult['error'] ?? "Erreur lors du téléchargement de l'image";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Joueurs</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/joueurs.css">
</head>

<body>
    <main>
        <div class="container">

            <div class="header">
                <div class="page-title">
                    <div>
                        <h1>Joueurs</h1>
                        <p>Gerez les joueurs de votre club</p>
                    </div>
                    <div>
                        <button class="submit-button"> + Ajouter un joueur </button>
                    </div>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="error">
                        <?php foreach ($error as $msg): ?>
                            <p><?php echo htmlspecialchars($msg); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="header-toggle-add">
                    <form action="joueurs.php" method="post" enctype="multipart/form-data">
                        <label for="nom">Nom</label>
                        <input type="text" name="nom" id="nom">
                        <label for="prenom">Prenom</label>
                        <input type="text" name="prenom" id="prenom">
                        <label for="birthdate">Date de naissance</label>
                        <input type="date" name="birthdate" id="birthdate">
                        <label for="picture">Photo</label>
                        <input type="file" name="picture" id="picture">
                        <button type="submit">Ajouter</button>
                    </form>
                </div>
            </div>

            <div class="search">
                <form id="search-form" onsubmit="return false;"> <!-- onsubmit="return false;" empêche le rechargement de la page -->
                    <input type="text" name="search" id="search" placeholder="Rechercher un joueur" oninput="filterPlayers()">
                    <button type="button" class="filter-button" id="filter-button">Filtrer</button>
                </form>
            </div>

            <div class="dashboard">

                <?php foreach ($players as $player) { ?>
                    <div class="player-card card" data-type="player" data-id="<?php echo $player->getId(); ?>">
                        <a href="update/joueursUpdate.php?id=<?php echo $player->getId(); ?>" class="player-card-link">
                            <div class="card-header">
                                <img src="uploads/<?php echo htmlspecialchars($player->getPicture()); ?>" alt="Photo de <?php echo htmlspecialchars($player->getFirstname() . ' ' . $player->getLastname()); ?>" class="player-image">
                                <div class="card-header-title">
                                    <h2 id="player-name"><?php echo $player->getFirstname() . " " . $player->getLastname(); ?></h2>
                                    <?php $age = $playerManager->getAge($player); ?>
                                    <p><?php echo $age; ?> ans</p>
                                </div>
                            </div>
                        </a>
                        <span class="delete">✕</span>
                        <form method="post" action="joueurs.php" class="delete-player-form delete-form" style="display:none;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $player->getId(); ?>">
                        </form>
                    <?php
                    // filtre les équipes qui appartiennent au joueur actuel
                    $player_teams = [];
                    foreach ($playerTeam as $item) {
                        $teamRelation = $item['playerTeam'];
                        if ($teamRelation->getPlayerId() == $player->getId()) {
                            $team = $teamManager->findById($teamRelation->getTeamId());
                            if ($team) {
                                $player_teams[] = (object)[
                                    'team_name' => $team->getName(),
                                    'role' => $teamRelation->getRole()
                                ];
                            }
                        }
                    }

                    // affiche les équipes seulement s'il y en a
                    if (!empty($player_teams)): ?>
                        <div class="appartient-equipe">
                            <?php foreach ($player_teams as $equipe): ?>
                                <div class="equipe-bubble">
                                    <?php echo htmlspecialchars($equipe->team_name . ' - ' . $equipe->role); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    </a>
                    <div class="ajouter-equipe">
                        <form action="joueurs.php" method="post">
                            <input type="hidden" name="player_id" value="<?php echo $player->getId(); ?>">
                            <select name="team_id" id="team_<?php echo $player->getId(); ?>" required>
                                <option value="">Sélectionner une équipe</option>
                                <?php foreach ($teams as $team) { // évite d'afficher les équipes auxquelles le joueur appartient déjà, ce qui empêche de l'ajouter plusieurs fois à la même équipe
                                    $isInTeam = false;
                                    foreach ($playerTeam as $item) {
                                        $data = $item['playerTeam'];
                                        if ($data->getPlayerId() == $player->getId() && $data->getTeamId() == $team->getId()) {
                                            $isInTeam = true;
                                            break;
                                        }
                                    }
                                    if (!$isInTeam) { ?>
                                        <option value="<?php echo $team->getId(); ?>"><?php echo htmlspecialchars($team->getName()); ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <select name="role" id="role_<?php echo $player->getId(); ?>" required>
                                <option value="">Sélectionner un rôle</option>
                                <option value="Gardien">Gardien</option>
                                <option value="Défenseur">Défenseur</option>
                                <option value="Milieu">Milieu</option>
                                <option value="Attaquant">Attaquant</option>
                            </select>
                            <button type="submit" class="submit-button">Ajouter</button>
                        </form>
                    </div>
            </div>
        <?php
                }
        ?>
        </div>
        <?php include "includes/footer.php"; ?>
        </div>
    </main>
    <script src="js/script.js"></script>
    <script src="js/joueurs.js"></script>
</body>

</html>