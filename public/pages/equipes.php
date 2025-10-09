<?php
include "../includes/navbar.php";

use Model\Classes\Team;
use Helper\FormValidator;
use Helper\Redirect;

$teams = $teamManager->findAll();
$teamsWithCount = $teamManager->findAllWithPlayerCount();

$validator = new FormValidator();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int) $_POST['id'];

    $teamToDelete = $teamManager->findById($id);

    if ($teamToDelete instanceof Team) {
        if ($teamManager->delete($teamToDelete)) {
            Redirect::to("equipes.php"); // redirige vers equipe.php, economise le exit; et et plus simple
        } else {
            $validator->addError("La suppression a échoué."); // ajoute une erreur au tableau error
        }
    } else {
        $validator->addError("Équipe introuvable.");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validator->required(['nom'], $_POST);

    $nom = trim($_POST["nom"] ?? '');

    if (!$validator->hasErrors()) { // pareil que: if(empty($error)) {}
        $team = new Team(null, $nom);

        if ($teamManager->insert($team)) {
            Redirect::to("equipes.php");
        } else {
            $validator->addError("Une erreur est survenue lors de l'ajout de l'équipe.");
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipes</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/equipes.css">
</head>

<body>

    <main>
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

                <!-- -----passage de ca:----- -->
                <!-- <?php //if ($validator->hasErrors()): ?>
                    <div class="error">
                        <?php //foreach ($validator->getErrors() as $err): ?>
                            <div><?php //echo htmlspecialchars($err); ?></div>
                        <?php //endforeach; ?>
                    </div>
                <?php //endif; ?> -->
                <!-- -----à:----- -->
                <?php FormValidator::displayErrors($validator); ?>


                <div class="header-toggle-add">
                    <form action="equipes.php" method="post">
                        <label for="nom">Nom</label>
                        <input type="text" name="nom" id="nom">
                        <button type="submit">Ajouter</button>
                    </form>
                </div>
            </div>

            <div class="dashboard">
                <?php foreach ($teamsWithCount as $teamData) {
                    $team = $teamData['team']->getName();
                    $playerCount = $teamData['player_count']; ?>

                    <div class="equipe-card card" data-type="team" data-id="<?php echo $teamData['team']->getId(); ?>">
                        <span class="delete">✕</span>
                        <form method="post" action="equipes.php" class="delete-player-form delete-form" style="display:none;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $teamData['team']->getId(); ?>">
                        </form>
                        <a href="equipesUpdate.php?id=<?php echo $teamData['team']->getId(); ?>" class="player-card-link">
                            <div class="card-header">
                                <div class="card-header-title">
                                    <h2><?php echo $team; ?></h2>
                                </div>
                            </div>
                        </a>
                        <div class="card-stat">
                            <div class="nombre-joueurs">
                                <p><?php echo $playerCount; ?></p>
                                <p>joueurs</p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php include "../includes/footer.php"; ?>
        </div>
    </main>
    <script src="../js/script.js"></script>
</body>

</html>