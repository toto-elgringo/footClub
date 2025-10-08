<?php
include "../includes/navbar.php";

use Model\Classes\StaffMember;
use Model\Helper\UploadPicture;
use Model\Enum\StaffRole;

$staffs = $staffMemberManager->findAll();

$error = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    $staffMemberToDelete = $staffMemberManager->findById($id);

    if ($staffMemberToDelete instanceof StaffMember) {
        if ($staffMemberManager->delete($staffMemberToDelete)) {
            header("Location: staff.php");
            exit;
        } else {
            $error[] = "La suppression a échoué.";
        }
    } else {
        $error[] = "Membre du staff introuvable.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first-name']);
    $last_name = trim($_POST['last-name']);
    $picture = $_FILES['picture'];
    $role = trim($_POST['role']);

    if (empty($first_name) || empty($last_name) || empty($picture['name']) || empty($role)) {
        $error[] = "Tous les champs doivent être remplis";
    } else {
        $uploadResult = UploadPicture::upload($picture, 'staff_');

        if ($uploadResult['success']) {
            try {
                $staffMember = new StaffMember(null, $first_name, $last_name, StaffRole::from($role), $uploadResult['filename']);
                                                                            //$role devient StaffRole::from($role)

                if ($staffMemberManager->insert($staffMember)) {
                    header("Location: staff.php");
                    exit;
                } else {
                    $error[] = "Erreur lors de l'ajout du membre dans la base de données";
                }
            } catch (PDOException $e) {
                $error[] = $e->getMessage();
            }
        } else {
            $error[] = $uploadResult['error'] ?? "Erreur lors du téléchargement du fichier";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/staff.css">
</head>

<body>
    <main>

        <div class="container">

            <div class="header">
                <div class="page-title">
                    <div>
                        <h1>Staff</h1>
                        <p>Gerez les membres de votre club</p>
                    </div>
                    <div>
                        <button class="submit-button"> + Ajouter un membre </button>
                    </div>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="error">
                        <?php foreach ($error as $msg): ?>
                            <p><?php echo htmlspecialchars($msg); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="header-toggle-add">
                    <form action="staff.php" method="post" class="form-container" enctype="multipart/form-data">
                        <h2>Ajouter un membre du staff</h2>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="first-name">Prénom</label>
                                <input type="text" name="first-name" id="first-name" required>
                            </div>
                            <div class="form-group">
                                <label for="last-name">Nom</label>
                                <input type="text" name="last-name" id="last-name" required>
                            </div>
                            <div class="form-group">
                                <label for="role">Rôle</label>
                                <select name="role" id="role" required>
                                    <option value="">Sélectionner un rôle</option>
                                    <option value="Entraineur">Entraîneur</option>
                                    <option value="Préparateur">Préparateur</option>
                                    <option value="Analyste">Analyste</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="picture">Photo</label>
                                <input type="file" name="picture" id="picture" required>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Ajouter le membre</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="dashboard">
                <?php if (!empty($staffs)): ?>
                    <?php foreach ($staffs as $staff): ?>
                        <div class="staff-card card" data-type="staff" data-id="<?php echo $staff->getId(); ?>">
                            <span class="delete">✕</span>
                            <form method="post" action="staff.php" class="delete-staff-form delete-form" style="display:none;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $staff->getId(); ?>">
                            </form>
                            <a href="staffUpdate.php?id=<?php echo $staff->getId(); ?>" class="staff-card-link">
                                <div class="staff-card-picture">
                                    <img src="../uploads/<?php echo htmlspecialchars($staff->getPicture()); ?>" alt="<?php echo htmlspecialchars($staff->getFirstname() . ' ' . $staff->getLastname()); ?>">
                                </div>
                                
                            <div class="staff-card-body">
                                <h3 class="staff-card-title" id="staff-name"><?php echo htmlspecialchars($staff->getFirstname() . ' ' . $staff->getLastname()); ?></h3>
                                <div class="staff-card-role">
                                     <!-- echo htmlspecialchars($staff->getRole() ?: 'Membre du staff'); devient:  -->
                                    <?php echo htmlspecialchars(($role = $staff->getRole()) ? $role->value : 'Membre du staff'); ?>
                                </div>
                                <div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun membre du staff n'a été trouvé.</p>
                <?php endif; ?>
            </div>
            <?php include "../includes/footer.php"; ?>
        </div>
    </main>
    <script src="../js/script.js"></script>
</body>

</html>