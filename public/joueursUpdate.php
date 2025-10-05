<?php
require_once __DIR__ . '/includes/navbar.php';

use src\Model\Player;
use src\Model\manager\PlayerManager;
use src\function\UploadPicture;

// Chargement joueur (déjà vu précédemment)
$playerManager = $playerManager ?? new PlayerManager(/* ... si besoin ... */);
$playerId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if (!$playerId) { http_response_code(400); exit('ID invalide.'); }
$player = $playerManager->findById($playerId);
if (!$player) { http_response_code(404); exit('Joueur introuvable.'); }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_player'])) {
    $prenom    = trim($_POST['prenom'] ?? '');
    $nom       = trim($_POST['nom'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');

    if ($prenom === '' || $nom === '' || $birthdate === '') {
        $errors[] = "Champs requis manquants.";
    }

    // 1) Upload AVANT toute écriture SQL
    $newPicture = $player->getPicture();
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $upload = UploadPicture::upload($_FILES['picture'], 'player_');
        if (!($upload['success'] ?? false)) {
            $errors[] = $upload['error'] ?? 'Échec upload';
        } else {
            $newPicture = $upload['filename'];
        }
    }

    if (empty($errors)) {
        $updated = new Player($player->getId(), $prenom, $nom, $birthdate, $newPicture);

        // 2) UPDATE court + retry doux sur 1205/1213
        $maxTries = 2; $ok = false;
        for ($i = 0; $i < $maxTries; $i++) {
            try {
                // (optionnel) abaisser l’isolation pour réduire les verrous fantômes
                // $playerManager->pdo()->exec("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");

                $ok = $playerManager->update($updated);
                break;
            } catch (\PDOException $e) {
                $code = $e->errorInfo[1] ?? null; // MySQL code
                if ($code === 1205 || $code === 1213) { // lock wait / deadlock
                    usleep(200000); // 200 ms
                    continue;       // on retente une fois
                }
                $errors[] = 'Erreur SQL: ' . $e->getMessage();
                break;
            }
        }

        if ($ok) {
            header('Location: joueurs.php', true, 303);
            exit;
        }
        if (empty($errors)) {
            $errors[] = "Mise à jour non enregistrée.";
        }
    }
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update joueur</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/joueurs.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="page-title">
                <div>
                    <h1>Update joueur <?php echo htmlspecialchars($player->getFirstname() . ' ' . $player->getLastname()); ?></h1>
                    <p>Mettez à jour les informations du joueur</p>
                </div>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $err): ?>
                    <div><?php echo htmlspecialchars($err); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="update-form-container">
            <input type="hidden" name="id" value="<?php echo (int)$player->getId(); ?>">
            <div class="update-form-fields">
                <div>
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($player->getFirstname()); ?>" required>
                </div>
                <div>
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($player->getLastname()); ?>" required>
                </div>
            </div>

            <div class="update-form-fields">
                <div>
                    <label for="birthdate">Date de naissance</label>
                    <input type="date" id="birthdate" name="birthdate" value="<?php echo $player->getBirthdate()->format('Y-m-d'); ?>" required>
                </div>
            </div>

            <div class="update-form-fields">
                <div>
                    <label for="picture">Photo (laisser vide pour conserver l'actuelle)</label>
                    <input type="file" id="picture" name="picture" accept="image/*">
                    <div class="current-photo">
                        <img src="<?php echo 'uploads/' . htmlspecialchars($player->getPicture()); ?>" alt="Photo actuelle">
                    </div>
                </div>
            </div>

            <div class="update-form-actions">
                <button type="submit" name="update_player" value="1">Enregistrer</button>
                <a href="joueurs.php">Annuler</a>
            </div>
        </form>
    </div>
</body>
</html>
