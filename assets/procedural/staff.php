<?php
include "classes/database/Database.php";
$pdo = Database::getConnection();

$staffs = $pdo->query("SELECT * FROM staff_member")->fetchAll(PDO::FETCH_OBJ);

$error = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first-name']);
    $last_name = trim($_POST['last-name']);
    $picture = $_FILES['picture'];
    $role = trim($_POST['role']);

    if (empty($first_name) || empty($last_name) || empty($picture['name']) || empty($role)) {
        $error[] = "Tous les champs doivent être remplis";
    } else {
        $uploadDir = __DIR__ . '/uploads/staff/';

        // Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                $error[] = "Impossible de créer le dossier de destination";
            }
        }

        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
        $fileType = $picture['type'];

        if (!array_key_exists($fileType, $allowedTypes)) {
            $error[] = "Type de fichier non autorisé. Seuls les JPG, PNG et GIF sont acceptés.";
        } elseif ($picture['size'] > 5000000) { // 5MB max
            $error[] = "Le fichier est trop volumineux. Taille maximale autorisée : 5MB.";
        } else {
            $fileExtension = $allowedTypes[$fileType];
            $newFileName = uniqid('staff_', true) . '.' . $fileExtension;
            $uploadFile = $uploadDir . $newFileName;
            $relativePath = 'uploads/staff/' . $newFileName;

            if (move_uploaded_file($picture['tmp_name'], $uploadFile)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO staff_member (firstname, lastname, picture, role) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$first_name, $last_name, $relativePath, $role]);
                    $error[] = "Membre ajouté avec succès";

                    header("Location: staff.php");
                    exit;
                } catch (PDOException $e) {
                    if (file_exists($uploadFile)) {
                        unlink($uploadFile);
                    }
                    $error[] = "Erreur lors de l'ajout du membre : " . $e->getMessage();
                }
            } else {
                $error[] = "Erreur lors du téléchargement du fichier";
            }
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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/staff.css">
</head>

<body>
    <main>

        <?php include "includes/navbar.php"; ?>

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

                <div class="header-toggle-add">
                    <?php if (!empty($error)): ?>
                        <div class="error">
                            <?php foreach ($error as $msg): ?>
                                <p><?php echo htmlspecialchars($msg); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

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
                                <input type="file" name="picture" id="picture" accept="image/*" required>
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
                        <?php if ($staff): ?>
                            <div class="staff-card card">
                                <div class="staff-card-picture">
                                    <?php if (!empty($staff->picture) && file_exists(__DIR__ . '/' . $staff->picture)): ?>
                                        <img src="<?php echo htmlspecialchars($staff->picture); ?>" alt="<?php echo htmlspecialchars(($staff->firstname ?? '') . ' ' . ($staff->lastname ?? '')); ?>">
                                    <?php else: ?>
                                        <div class="no-picture">
                                            <span class="material-icons">person</span>
                                            <span>Pas de photo</span>
                                        </div>
                                    <?php endif; ?>
                                </div>


                                <div class="staff-card-body">
                                    <h3 class="staff-card-title"><?php echo htmlspecialchars(($staff->firstname ?? '') . ' ' . ($staff->lastname ?? '')); ?></h3>
                                    <div class="staff-card-role">
                                        <?php echo htmlspecialchars($staff->role ?? 'Membre du staff'); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun membre du staff n'a été trouvé.</p>
                <?php endif; ?>
            </div>

        </div>
    </main>

    <script src="js/script.js"></script>
</body>

</html>