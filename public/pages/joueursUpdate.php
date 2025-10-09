<?php
require_once __DIR__ . '/../includes/navbar.php';

use Model\Classes\Player;
use Helper\UploadPicture;
use Helper\FormValidator;
use Helper\Redirect;

$player = $playerManager->findById($_GET['id']);

$validator = new FormValidator();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_player'])) {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');
    
    if ($prenom === '' || $nom === '' || $birthdate === '') {
        $validator->addError("Champs requis manquants.");
    }

    $newPicture = $player->getPicture();

    // Si un nouveau fichier est téléchargé
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        // Supprimer l'ancienne image si elle existe
        UploadPicture::delete($player->getPicture());
        // Télécharger la nouvelle image
        $uploadResult = UploadPicture::upload($_FILES['picture'], 'player_');
        if ($uploadResult['success']) {
            $newPicture = $uploadResult['filename'];
        } else {
            $validator->addError($uploadResult['error']);
        }
    }

    if (!$validator->hasErrors()) {
        $updated = new Player($player->getId(), $prenom, $nom, new DateTime($birthdate), $newPicture);

        if ($playerManager->update($updated)) {
            Redirect::to("joueurs.php");
        } else {
            $validator->addError("Erreur lors de la mise à jour du joueur.");
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
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/joueurs.css">
</head>

<body>
    <div class="container update-container">
        <div class="header">
            <div class="page-title">
                <div>
                    <h1>Update joueur <?php echo htmlspecialchars($player->getFirstname() . ' ' . $player->getLastname()); ?></h1>
                    <p>Mettez à jour les informations du joueur</p>
                </div>
            </div>
        </div>

        <?php FormValidator::displayErrors($validator); ?>

        <form method="post" enctype="multipart/form-data" class="update-form-container">
            <input type="hidden" name="id" value="<?php echo (int)$player->getId(); ?>">

            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($player->getFirstname()); ?>" required>

            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($player->getLastname()); ?>" required>

            <label for="birthdate">Date de naissance</label>
            <input type="date" id="birthdate" name="birthdate" value="<?php echo $player->getBirthdate()->format('Y-m-d'); ?>" required>

            <label for="picture">Photo (laisser vide pour conserver l'actuelle)</label>
            <input type="file" id="picture" name="picture" accept="image/*">

            <div class="current-photo">
                <img src="<?php echo '../uploads/' . htmlspecialchars($player->getPicture()); ?>" alt="Photo actuelle">
            </div>

            <button type="submit" name="update_player" value="1">Enregistrer</button>
            <a href="joueurs.php" class="cancel">Annuler</a>
        </form>
        <?php include "../includes/footer.php"; ?>
    </div>
</body>

</html>