<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\Classes\Player;
use App\Helper\UploadPicture;
use App\Helper\FormValidator;
use App\Helper\Redirect;
use App\Helper\TwigRenderer;
use App\Model\Manager\PlayerManager;

$playerManager = new PlayerManager();
$player = isset($_GET['firstname'], $_GET['lastname'])
    ? $playerManager->findByName($_GET['firstname'], $_GET['lastname'])
    : null;
$validator = new FormValidator();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_player'])) {
    $old_firstname = trim($_POST['old_firstname'] ?? '');
    $old_lastname = trim($_POST['old_lastname'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');

    if ($prenom === '' || $nom === '' || $birthdate === '') {
        $validator->addError("Champs requis manquants.");
    }

    $newPicture = $player->getPicture();

    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        UploadPicture::delete($player->getPicture());
        $uploadResult = UploadPicture::upload($_FILES['picture'], 'player_');
        if ($uploadResult['success']) {
            $newPicture = $uploadResult['filename'];
        } else {
            $validator->addError($uploadResult['error']);
        }
    }

    if (!$validator->hasErrors()) {
        $updated = new Player($prenom, $nom, new DateTime($birthdate), $newPicture);
        if ($playerManager->update($updated, $old_firstname, $old_lastname)) {
            Redirect::to("joueurs.php");
        } else {
            $validator->addError("Erreur lors de la mise à jour du joueur.");
        }
    }
}

TwigRenderer::display('pages/joueursUpdate.twig', [
    'player' => $player,
    'validator' => $validator
]);
