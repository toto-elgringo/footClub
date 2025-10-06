<?php
require_once __DIR__ . '/includes/navbar.php';

use src\Model\FootballMatch;

$match = $matchManager->findById($_GET['id']);

$teams = $teamManager->findAll();
$opposing_clubs = $opposingClubManager->findAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_match'])) {
    $team_id = trim($_POST['team_id'] ?? '');
    $opposing_club_id = trim($_POST['opposing_club_id'] ?? '');
    $match_date = trim($_POST['match_date'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $team_score = trim($_POST['team_score'] ?? '');
    $opponent_score = trim($_POST['opponent_score'] ?? '');

    if ($team_id === '' || $opposing_club_id === '' || $match_date === '' || $city === '' || $team_score === '' || $opponent_score === '') {
        $errors[] = "Champs requis manquants.";
    }

    if (empty($errors)) {
        $updated = new FootballMatch($match->getId(), $match_date, $city, $team_score, $opponent_score, $team_id, $opposing_club_id);

        if ($matchManager->update($updated)) {
            header('Location: matchs.php');
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
    <title>Update joueur</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/joueurs.css">
</head>

<body>
    <div class="container update-container">
        <div class="header">
            <div class="page-title">
                <div>
                    <h1>Update match</h1>
                    <p>Mettez à jour les informations du match</p>
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

        <form action="matchsUpdate.php?id=<?php echo $match->getId(); ?>" method="post" class="form-grid">
            <input type="hidden" name="update_match" value="1">
            <div class="form-group">
                <label for="team_id">Équipe</label>
                <select name="team_id" id="team_id" required>
                    <option value="">Sélectionner une équipe</option>
                    <?php if (!empty($teams)): ?>
                        <?php foreach ($teams as $team): ?>
                            <option value="<?php echo $team->getId(); ?>" <?php echo ($match->getTeamId() == $team->getId()) ? 'selected' : ''; ?>> <!-- permet de selectionner l'equipe -->
                                <?php echo htmlspecialchars($team->getName()); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="opposing_club_id">Club adverse</label>
                <select name="opposing_club_id" id="opposing_club_id" required>
                    <option value="">Sélectionner un club</option>
                        <?php foreach ($opposing_clubs as $club): ?>
                            <option value="<?php echo $club->getId(); ?>" <?php echo ($match->getOpposingClubId() == $club->getId()) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($club->getCity()) . ' - ' . htmlspecialchars($club->getName()); ?>
                            </option>
                        <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="match_date">Date du match</label>
                <input type="datetime-local" name="match_date" id="match_date"
                    value="<?php echo $match->getDate()->format('Y-m-d\TH:i'); ?>" required> <!-- format 'Y-m-d\TH:i' car sinon ca fonctionne pas -->
            </div>

            <div class="form-group">
                <label for="city">Lieu du match</label>
                <input type="text" name="city" id="city" value="<?php echo htmlspecialchars($match->getCity()); ?>" required>
            </div>

            <div class="form-group">
                <label for="team_score">Score de l'équipe</label>
                <input type="number" name="team_score" id="team_score" min="0" max="20"
                    value="<?php echo htmlspecialchars($match->getTeamScore()); ?>">
            </div>

            <div class="form-group">
                <label for="opponent_score">Score de l'adversaire</label>
                <input type="number" name="opponent_score" id="opponent_score" min="0" max="20"
                    value="<?php echo htmlspecialchars($match->getOpponentScore()); ?>">
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Mettre à jour le match</button>
            </div>
        </form>
        <?php include "includes/footer.php"; ?>
    </div>
</body>

</html>