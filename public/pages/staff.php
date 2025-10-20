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
$staffs = $staffMemberManager->findAll();
$validator = new FormValidator();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $staffMemberToDelete = $staffMemberManager->findById($id);

    if ($staffMemberToDelete instanceof StaffMember) {
        UploadPicture::delete($staffMemberToDelete->getPicture());

        if ($staffMemberManager->delete($staffMemberToDelete)) {
            Redirect::to("staff.php");
        } else {
            $validator->addError("La suppression a échoué.");
        }
    } else {
        $validator->addError("Membre du staff introuvable.");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
    $first_name = trim($_POST['first-name']);
    $last_name = trim($_POST['last-name']);
    $picture = $_FILES['picture'];
    $role = trim($_POST['role']);

    if (empty($first_name) || empty($last_name) || empty($picture['name']) || empty($role)) {
        $validator->addError("Tous les champs doivent être remplis");
    } else {
        $uploadResult = UploadPicture::upload($picture, 'staff_');

        if ($uploadResult['success']) {
            try {
                $staffMember = new StaffMember(null, $first_name, $last_name, StaffRole::from($role), $uploadResult['filename']);

                if ($staffMemberManager->insert($staffMember)) {
                    Redirect::to("staff.php");
                } else {
                    $validator->addError("Erreur lors de l'ajout du membre dans la base de données");
                }
            } catch (PDOException $e) {
                $validator->addError($e->getMessage());
            }
        } else {
            $validator->addError($uploadResult['error'] ?? "Erreur lors du téléchargement du fichier");
        }
    }
}

TwigRenderer::display('pages/staff.twig', [
    'staffs' => $staffs,
    'validator' => $validator
]);
