<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\Classes\StaffMember;
use App\Helper\UploadPicture;
use App\Model\Enum\StaffRole;
use App\Helper\FormValidator;
use App\Helper\Redirect;
use App\Helper\TwigRenderer;
use App\Model\Manager\StaffMemberManager;

$staffMemberManager = new StaffMemberManager();
$staffMember = isset($_GET['firstname'], $_GET['lastname'])
    ? $staffMemberManager->findByName($_GET['firstname'], $_GET['lastname'])
    : null;
$validator = new FormValidator();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_player'])) {
    $old_firstname = trim($_POST['old_firstname'] ?? '');
    $old_lastname = trim($_POST['old_lastname'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $role = trim($_POST['role'] ?? '');

    if ($prenom === '' || $nom === '' || $role === '') {
        $validator->addError("Champs requis manquants.");
    }

    $newPicture = $staffMember->getPicture();

    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        UploadPicture::delete($staffMember->getPicture());
        $upload = UploadPicture::upload($_FILES['picture'], 'staff_');
        if (!($upload['success'] ?? false)) {
            $validator->addError($upload['error'] ?? 'Échec de l\'upload de l\'image');
        } else {
            $newPicture = $upload['filename'];
        }
    }

    if (empty($validator->getErrors())) {
        $roleEnum = StaffRole::from($role);
        $updated = new StaffMember($prenom, $nom, $roleEnum, $newPicture);
        if ($staffMemberManager->update($updated, $old_firstname, $old_lastname)) {
            Redirect::to("staff.php");
        } else {
            $validator->addError("Erreur lors de la mise à jour du membre du staff.");
        }
    }
}

TwigRenderer::display('pages/staffUpdate.twig', [
    'staffMember' => $staffMember,
    'validator' => $validator
]);
