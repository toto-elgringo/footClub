<?php
require_once __DIR__ . '/../includes/navbar.php';

use Model\Classes\StaffMember;
use Helper\UploadPicture;
use Model\Enum\StaffRole;

$staffMember = $staffMemberManager->findById($_GET['id']);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_player'])) {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $role = trim($_POST['role'] ?? '');

    if ($prenom === '' || $nom === '' || $role === '') {
        $errors[] = "Champs requis manquants.";
    }

    $newPicture = $staffMember->getPicture();

    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $upload = UploadPicture::upload($_FILES['picture'], 'staff_');
        if (!($upload['success'] ?? false)) {
            $errors[] = $upload['error'] ?? 'Échec upload';
        } else {
            $newPicture = $upload['filename'];
        }
    }

    if (empty($errors)) {
        // Convert the role string to a StaffRole enum value
        $roleEnum = StaffRole::from($role);

        $updated = new StaffMember($staffMember->getId(), $prenom, $nom, $roleEnum, $newPicture);

        if ($staffMemberManager->update($updated)) {
            header('Location: staff.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update membre du staff</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/staff.css">
</head>

<body>
    <div class="container update-container">
        <div class="header">
            <div class="page-title">
                <div>
                    <h1>Update membre du staff <?php echo htmlspecialchars($staffMember->getFirstname() . ' ' . $staffMember->getLastname()); ?></h1>
                    <p>Mettez à jour les informations du membre du staff</p>
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
            <input type="hidden" name="id" value="<?php echo (int)$staffMember->getId(); ?>">

            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($staffMember->getFirstname()); ?>" required>

            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($staffMember->getLastname()); ?>" required>

            <label for="role">Rôle</label>
            <select id="role" name="role" required>
                <option value="" disabled>Sélectionner un rôle</option>
                <option value="Entraineur" <?php echo ($staffMember->getRole()?->value === 'Entraineur') ? 'selected' : ''; ?>>Entraîneur</option>
                <option value="Préparateur" <?php echo ($staffMember->getRole()?->value === 'Préparateur') ? 'selected' : ''; ?>>Préparateur</option>
                <option value="Analyste" <?php echo ($staffMember->getRole()?->value === 'Analyste') ? 'selected' : ''; ?>>Analyste</option>
            </select>
            <!-- on rajotue a chaque fois ->value pour l'enumeration -->

            <label for="picture">Photo (laisser vide pour conserver l'actuelle)</label>
            <input type="file" id="picture" name="picture" accept="image/*">

            <div class="current-photo">
                <img src="<?php echo '../uploads/' . htmlspecialchars($staffMember->getPicture()); ?>" alt="Photo actuelle">
            </div>

            <button type="submit" name="update_player" value="1">Enregistrer</button>
            <a href="staff.php" class="cancel">Annuler</a>
        </form>
        <?php include "../includes/footer.php"; ?>
    </div>
</body>

</html>