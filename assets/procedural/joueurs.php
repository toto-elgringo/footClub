<?php
include "classe2/Database.php";
$pdo = Database::getConnection();

$players = $pdo->query("SELECT * FROM player")->fetchAll(PDO::FETCH_OBJ);
$teams = $pdo->query("SELECT * FROM team")->fetchAll(PDO::FETCH_OBJ);
$player_has_team = $pdo->query("SELECT pht.*, t.name as team_name FROM player_has_team pht JOIN team t ON pht.team_id = t.id")->fetchAll(PDO::FETCH_OBJ);

$error = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['player_id'], $_POST['team_id'], $_POST['role'])) {
    $player_id = trim($_POST['player_id']);
    $team_id = trim($_POST['team_id']);
    $role = trim($_POST['role']);

    // verifie si la combinaison joueur/équipe existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM player_has_team WHERE player_id = ? AND team_id = ?");
    $stmt->execute([$player_id, $team_id]);
    $exists = $stmt->fetchColumn();

    if ($exists > 0) {
        $error[] = "Le joueur appartient déjà à l'équipe";
    } else {
        $sql = "INSERT INTO player_has_team (player_id, team_id, role) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$player_id, $team_id, $role]);

        if ($success) {
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
        $uploadDir = 'uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExtension = pathinfo($picture['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('player_', true) . '.' . $fileExtension;
        $uploadFile = $uploadDir . $newFileName;

        // si le fichier est bien uploadé
        if (move_uploaded_file($picture['tmp_name'], $uploadFile)) {
            $sql = "INSERT INTO player (firstname, lastname, birthdate, picture) VALUES (:firstname, :lastname, :birthdate, :picture)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                "firstname" => $nom,
                "lastname" => $prenom,
                "birthdate" => $birthdate,
                "picture" => $newFileName
            ]);

            header("Location: joueurs.php");
            exit;
        } else {
            $error[] = "Erreur lors du téléchargement de l'image.";
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
        <?php include "includes/navbar.php"; ?>

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


                <div class="header-toggle-add">
                    <?php if (!empty($error)): ?>
                        <div class="error">
                            <?php foreach ($error as $msg): ?>
                                <p><?php echo htmlspecialchars($msg); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

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

                    <div class="player-card card">
                        <div class="card-header">
                            <img src="uploads/<?php echo htmlspecialchars($player->picture); ?>" alt="Photo de <?php echo htmlspecialchars($player->firstname . ' ' . $player->lastname); ?>" class="player-image">
                            <div class="card-header-title">
                                <h2 id="player-name"><?php echo $player->firstname . " " . $player->lastname; ?></h2>
                                <?php
                                $birthDate = new DateTime($player->birthdate);
                                $today = new DateTime();
                                $age = $birthDate->diff($today)->y; // "y" sert a recuperer l'age en année
                                ?>
                                <p><?php echo $age; ?> ans</p>
                            </div>
                        </div>

                        <?php
                        // filtre les équipes qui appartiennent au joueur actuel
                        $player_teams = [];
                        foreach ($player_has_team as $equipe) {
                            if ($equipe->player_id == $player->id) {
                                $player_teams[] = $equipe;
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

                        <div class="ajouter-equipe">
                            <form action="joueurs.php" method="post">
                                <input type="hidden" name="player_id" value="<?php echo $player->id; ?>">
                                <select name="team_id" id="team_<?php echo $player->id; ?>" required>
                                    <option value="">Sélectionner une équipe</option>
                                    <?php foreach ($teams as $team) {
                                        $isInTeam = false;
                                        foreach ($player_has_team as $pht) {
                                            if ($pht->player_id == $player->id && $pht->team_id == $team->id) {
                                                $isInTeam = true;
                                                break;
                                            }
                                        }
                                        if (!$isInTeam) { ?>
                                            <option value="<?php echo $team->id; ?>"><?php echo htmlspecialchars($team->name); ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <select name="role" id="role_<?php echo $player->id; ?>" required>
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
        </div>
    </main>

    <script src="js/script.js"></script>
    <script src="js/joueurs.js"></script>
</body>

</html>