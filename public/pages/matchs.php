<?php
include "../includes/navbar.php";

use Model\Classes\OpposingClub;
use Model\Classes\FootballMatch;

$matchs = $matchManager->findAll();
$opposing_clubs = $opposingClubManager->findAll();

$teams = $teamManager->findAllTeams();

$error = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    $matchToDelete = $matchManager->findById($id);

    if ($matchToDelete instanceof FootballMatch) {
        if ($matchManager->delete($matchToDelete)) {
            header("Location: matchs.php");
            exit;
        } else {
            $error[] = "La suppression a échoué.";
        }
    } else {
        $error[] = "Match introuvable.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['new_club_city']) || isset($_POST['new_club_address'])) {
        $club_city = trim($_POST['new_club_city']);
        $club_address = trim($_POST['new_club_address']);

        if (empty($club_city) || empty($club_address)) {
            $error[] = "La ville et l'adresse du club sont obligatoires";
        } else {
            try {
                $existingClub = $opposingClubManager->findByCity($club_city);

                if ($existingClub) {
                    $error[] = "Un club existe déjà pour cette ville";
                } else {
                    $newClub = new OpposingClub(null, $club_city, $club_address);
                    $opposingClubManager->insert($newClub);
                }
            } catch (PDOException $e) {
                $error[] = "Erreur lors de l'ajout du club : " . $e->getMessage();
            }
        }
    }

    if (isset($_POST['team_id'], $_POST['match_date'], $_POST['opposing_club_id'])) {
        $team_id = (int)$_POST['team_id'];
        $team_score = (int)$_POST['team_score'];
        $opponent_score = (int)$_POST['opponent_score'];
        $date = $_POST['match_date'];
        $city = trim($_POST['city']);
        $opposing_club_id = (int)$_POST['opposing_club_id'];

        if (empty($team_id) || empty($date) || empty($city) || empty($opposing_club_id)) {
            $error[] = "Tous les champs doivent être remplis";
        } elseif ($team_score < 0 || $opponent_score < 0) {
            $error[] = "Les scores doivent être positifs";
        } elseif (!strtotime($date)) {
            $error[] = "Date invalide";
        }

        if (empty($error)) {
            try {
                $match = new FootballMatch(null, $date, $city, $team_score, $opponent_score, $team_id, $opposing_club_id);
                $matchManager->insert($match);
            } catch (PDOException $e) {
                $error[] = "Erreur lors de l'ajout du match : " . $e->getMessage();
            }
        }
    }

    if (empty($error)) {
        header("Location: matchs.php");
        exit;
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matchs</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/matchs.css">
</head>

<body>
    <main>
        <div class="container">

            <div class="header">
                <div class="page-title">
                    <div>
                        <h1>Matchs</h1>
                        <p>Gerez les matchs de votre club</p>
                    </div>
                    <div>
                        <button class="submit-button"> + Ajouter un match </button>
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
                    <h3>Ajouter un nouveau club adverse</h3>
                    <form action="matchs.php" method="post" class="form-grid">
                        <div class="form-group">
                            <label for="new_club_city">Ville du club</label>
                            <input type="text" name="new_club_city" id="new_club_city" required>
                        </div>

                        <div class="form-group">
                            <label for="new_club_address">Adresse</label>
                            <input type="text" name="new_club_address" id="new_club_address" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Ajouter le club</button>
                        </div>
                    </form>

                    <h3>Ajouter un match</h3>
                    <form action="matchs.php" method="post" class="form-grid">
                        <div class="form-group">
                            <label for="team_id">Équipe</label>
                            <select name="team_id" id="team_id" required>
                                <option>Sélectionner une équipe</option>
                                <?php if (!empty($teams)): ?>
                                    <?php foreach ($teams as $team): ?>
                                        <option value="<?php echo $team->getId(); ?>">
                                            <?php echo htmlspecialchars($team->getName()); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option disabled>Aucune équipe disponible</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="opposing_club_id">Club adverse</label>
                            <select name="opposing_club_id" id="opposing_club_id" required>
                                <option>Sélectionner un club</option>
                                <?php foreach ($opposing_clubs as $club): ?>
                                    <option value="<?php echo $club->getId(); ?>">
                                        <?php echo htmlspecialchars($club->getCity()) . ' - ' . htmlspecialchars($club->getName()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="match_date">Date du match</label>
                            <input type="datetime-local" name="match_date" id="match_date" required>
                        </div>

                        <div class="form-group">
                            <label for="city">Lieu du match</label>
                            <input type="text" name="city" id="city" required>
                        </div>

                        <div class="form-group">
                            <label for="team_score">Score de l'équipe</label>
                            <input type="number" name="team_score" id="team_score" min="0" max="20" value="0">
                        </div>

                        <div class="form-group">
                            <label for="opponent_score">Score de l'adversaire</label>
                            <input type="number" name="opponent_score" id="opponent_score" min="0" max="20" value="0">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Enregistrer le match</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="dashboard">
                <h2>Liste des matchs</h2>

                <div class="match-card-container">
                    <?php if (empty($matchs)): ?>
                        <p>Aucun match programmé pour le moment.</p>
                    <?php else: ?>
                        <?php foreach ($matchs as $match):
                            $match_date = $match->getDate();
                            $is_past = $match_date < new DateTime();
                            $is_win = $match->getTeamScore() > $match->getOpponentScore();
                            $is_draw = $match->getTeamScore() == $match->getOpponentScore();

                            $opposingClub = $opposingClubManager->findById($match->getOpposingClubId());
                            $opponent_name = $opposingClub ? $opposingClub->getCity() : 'Club inconnu';
                        ?>
                            <div class="match-card card <?php echo $is_past ? ($is_win ? 'win' : ($is_draw ? 'draw' : 'lose')) : ''; ?>" data-type="match" data-id="<?php echo $match->getId(); ?>"> <!-- pour la statu line -->
                                <span class="delete">✕</span>
                                <form method="post" action="matchs.php" class="delete-player-form delete-form" style="display:none;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $match->getId(); ?>">
                                </form>
                                <a href="matchsUpdate.php?id=<?php echo $match->getId(); ?>" class="match-card-link">
                                    <div class="match-date">
                                        <?php echo $match_date->format('d/m/Y'); ?>
                                    </div>
                                </a>
                                <div class="match-content">
                                        <div class="match-teams">
                                            <!-- equipe a domicile -->
                                            <div class="team-row">
                                                <div class="team">
                                                    <?php
                                                    $team = $teamManager->findById($match->getTeamId());
                                                    $teamName = $team ? htmlspecialchars($team->getName()) : 'Équipe inconnue';
                                                    ?>
                                                    <span class="team-name"><?php echo $teamName; ?></span>
                                                </div>
                                                <?php if ($is_past): ?>
                                                    <span class="score"><?php echo $match->getTeamScore(); ?></span>
                                                <?php else: ?>
                                                    <span class="score">-</span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="match-separator">
                                                <span>VS</span>
                                            </div>

                                            <!-- equipe a adverse -->
                                            <div class="team-row">
                                                <div class="team">
                                                    <span class="team-name"><?php echo htmlspecialchars($opponent_name); ?></span>
                                                </div>
                                                <?php if ($is_past): ?>
                                                    <span class="score"><?php echo $match->getOpponentScore(); ?></span>
                                                <?php else: ?>
                                                    <span class="score">-</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="status-line"></div>
                                    </div>

                                    <!-- pied de carte -->
                                    <div class="match-footer">
                                        <div class="match-location">
                                            <span>📍</span>
                                            <span><?php echo htmlspecialchars($match->getCity()); ?></span>
                                        </div>
                                        <div class="match-status">
                                            <?php if ($is_past): ?>
                                                <?php
                                                if ($is_draw) {
                                                    echo 'Match nul';
                                                } else {
                                                    echo $is_win ? 'Victoire' : 'Défaite';
                                                }
                                                ?>
                                            <?php else: ?>
                                                À venir
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php include "../includes/footer.php"; ?>
        </div>
    </main>
    <script src="../js/script.js"></script>
</body>

</html>