<?php
require_once __DIR__ . '/../includes/navbar.php';

use Model\Classes\Team;

$team = $teamManager->findById($_GET['id']);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_team'])) {
    $nom = trim($_POST['nom'] ?? '');

    if ($nom === '') {
        $errors[] = "Champs requis manquants.";
    }

    if (empty($errors)) {
        $updated = new Team($team->getId(), $nom);

        if ($teamManager->update($updated)) {
            header('Location: equipes.php');
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
    <title>Update équipe</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/equipes.css">
</head>

<body>
    <div class="container update-container">
        <div class="header">
            <div class="page-title">
                <div>
                    <h1>Update équipe <?php echo htmlspecialchars($team->getName()); ?></h1>
                    <p>Mettez à jour les informations de l'équipe</p>
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
            <input type="hidden" name="id" value="<?php echo (int)$team->getId(); ?>">

            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($team->getName()); ?>" required>

            <button type="submit" name="update_team" value="1">Enregistrer</button>
            <a href="equipes.php" class="cancel">Annuler</a>
        </form>
        <?php include "../includes/footer.php"; ?>
    </div>
</body>

</html>