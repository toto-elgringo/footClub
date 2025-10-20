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
$staffMember = $staffMemberManager->findById($_GET['id']);
$validator = new FormValidator();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_player'])) {
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
        $updated = new StaffMember($staffMember->getId(), $prenom, $nom, $roleEnum, $newPicture);

        if ($staffMemberManager->update($updated)) {
            Redirect::to("staff.php");
        }
    }
}

TwigRenderer::display('pages/staffUpdate.twig', [
    'staffMember' => $staffMember,
    'validator' => $validator
]);
