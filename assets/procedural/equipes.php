<?php
include "classes/database/Database.php";
$pdo = Database::getConnection();

$teams = $pdo->query("SELECT t.*, COUNT(pht.player_id) as nb_joueurs 
                     FROM team t 
                     LEFT JOIN player_has_team pht ON t.id = pht.team_id 
                     GROUP BY t.id")->fetchAll(PDO::FETCH_OBJ);

$error = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"] ?? '');

    if (empty($nom)) {
        $error[] = "Le nom de l'équipe est obligatoire";
    }

    if (empty($error)) {
        $sql = "INSERT INTO team (name) VALUES (:nom)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            "nom" => $nom
        ]);

        header("Location: equipes.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipes</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/equipes.css">
</head>

<body>

    <main>
        <?php include "includes/navbar.php"; ?>

        <div class="container">

        <div class="header">
                <div class="page-title">
                    <div>
                        <h1>Equipes</h1>
                        <p>Gerez les equipes de votre club</p>
                    </div>
                    <div>
                        <button class="submit-button"> + Ajouter une equipe </button>
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

                    <form action="equipes.php" method="post">
                        <label for="nom">Nom</label>
                        <input type="text" name="nom" id="nom">
                        <button type="submit">Ajouter</button>
                    </form>
                </div>
            </div>

            <div class="dashboard">

                <?php foreach ($teams as $team) {
                ?>

                    <div class="equipe-card card">
                        <div class="card-header">
                            <div class="card-header-title">
                                <h2><?php echo $team->name; ?></h2>
                            </div>
                        </div>

                        <div class="card-stat">
                            <div class="nombre-joueurs">
                                <p><?php echo $team->nb_joueurs; ?></p>
                                <p>Joueur<?php echo $team->nb_joueurs > 1 ? 's' : ''; ?></p>
                            </div>
                            <!-- <div class="nombre-points">
                            <p>40</p>
                            <p>Nombre de points</p>
                        </div> -->
                        </div>

                        <!-- <div class="card-data">
                        <div class="nombre-victoires">
                            <p>10</p>
                            <p>Nombre de victoires</p>
                        </div>
                        <div class="nombre-nuls">
                            <p>2</p>
                            <p>Nombre de nuls</p>
                        </div>
                        <div class="nombre-defaites">
                            <p>2</p>
                            <p>Nombre de défaites</p>
                        </div>
                    </div> -->

                    </div>

                <?php }
                ?>

            </div>

        </div>
    </main>
    <script src="js/script.js"></script>
</body>

</html>