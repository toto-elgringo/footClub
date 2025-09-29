<?php
include "classes/database/Database.php";
$pdo = Database::getConnection();

$teams = $pdo->query("SELECT * FROM team")->fetchAll(PDO::FETCH_OBJ);
$opposing_clubs = $pdo->query("SELECT * FROM opposing_club")->fetchAll(PDO::FETCH_OBJ);

$matchs = $pdo->query("
    SELECT m.*, t.name as team_name, oc.city as club_name, oc.address as club_address
    FROM `match` m
    LEFT JOIN team t ON m.team_id = t.id
    LEFT JOIN opposing_club oc ON m.opposing_club_id = oc.id
    ORDER BY m.date DESC
")->fetchAll(PDO::FETCH_OBJ);

$error = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // verifie si c'est une création de club adverse
    if (isset($_POST['new_club_city']) && !empty($_POST['new_club_city'])) {
        $club_city = trim($_POST['new_club_city']);
        $club_address = trim($_POST['new_club_address'] ?? '');

        if (empty($club_city)) {
            $error[] = "La ville du club est obligatoire";
        } else {
            try {
                // verifie si le club existe déjà
                $stmt = $pdo->prepare("SELECT id FROM opposing_club WHERE city = ?");
                $stmt->execute([$club_city]);

                if ($stmt->rowCount() > 0) {
                    $error[] = "Un club existe déjà pour cette ville";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO opposing_club (city, address) VALUES (?, ?)");
                    $stmt->execute([$club_city, $club_address]);
                    $opposing_club_id = $pdo->lastInsertId();
                    // recharge la liste des clubs
                    $opposing_clubs = $pdo->query("SELECT * FROM opposing_club")->fetchAll(PDO::FETCH_OBJ);
                }
            } catch (PDOException $e) {
                $error[] = "Erreur lors de l'ajout du club : " . $e->getMessage();
            }
        }
    }
    // sinon, c'est un ajout de match
    else {
        $team_id = (int)($_POST['team_id'] ?? 0);
        $team_score = (int)($_POST['team_score'] ?? 0);
        $opponent_score = (int)($_POST['opponent_score'] ?? 0);
        $date = $_POST['match_date'] ?? '';
        $city = trim($_POST['city'] ?? '');
        $opposing_club_id = (int)($_POST['opposing_club_id'] ?? 0);

        // validation des données
        if (empty($team_id) || empty($date) || empty($city) || empty($opposing_club_id)) {
            $error[] = "Tous les champs doivent être remplis";
        } elseif ($team_score < 0 || $opponent_score < 0) {
            $error[] = "Les scores doivent être positifs";
        } elseif ($team_score > 20 || $opponent_score > 20) {
            $error[] = "Les scores ne peuvent pas dépasser 20";
        } elseif (!strtotime($date)) {
            $error[] = "Date invalide";
        }

        if (empty($error)) {
            try {
                $sql = "INSERT INTO `match` (team_id, team_score, opponent_score, date, city, opposing_club_id) 
                        VALUES (:team_id, :team_score, :opponent_score, :date, :city, :opposing_club_id)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ":team_id" => $team_id,
                    ":team_score" => $team_score,
                    ":opponent_score" => $opponent_score,
                    ":date" => $date,
                    ":city" => $city,
                    ":opposing_club_id" => $opposing_club_id
                ]);
                
                // recharge les données pour afficher le nouveau match
                $matchs = $pdo->query("
                    SELECT m.*, t.name as team_name, oc.city as club_name, oc.address as club_address
                    FROM `match` m
                    LEFT JOIN team t ON m.team_id = t.id
                    LEFT JOIN opposing_club oc ON m.opposing_club_id = oc.id
                    ORDER BY m.date DESC
                ")->fetchAll(PDO::FETCH_OBJ);
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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/matchs.css">
</head>

<body>
    <main>
        <?php include "includes/navbar.php"; ?>

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

                <div class="header-toggle-add">
                    <?php if (!empty($error)): ?>
                        <div class="error">
                            <?php foreach ($error as $msg): ?>
                                <p><?php echo htmlspecialchars($msg); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <h3>Ajouter un nouveau club adverse</h3>
                    <form action="matchs.php" method="post" class="form-grid">
                        <div class="form-group">
                            <label for="new_club_city">Ville du club</label>
                            <input type="text" name="new_club_city" id="new_club_city" required>
                        </div>

                        <div class="form-group">
                            <label for="new_club_address">Adresse (optionnel)</label>
                            <input type="text" name="new_club_address" id="new_club_address">
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
                                <option value="">Sélectionner une équipe</option>
                                <?php foreach ($teams as $team): ?>
                                    <option value="<?php echo $team->id; ?>"><?php echo htmlspecialchars($team->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="opposing_club_id">Club adverse</label>
                            <select name="opposing_club_id" id="opposing_club_id" required>
                                <option value="">Sélectionner un club</option>
                                <?php foreach ($opposing_clubs as $club): ?>
                                    <option value="<?php echo $club->id; ?>">
                                        <?php echo htmlspecialchars($club->city); ?>
                                        <?php if (!empty($club->address)) echo ' - ' . htmlspecialchars($club->address); ?>
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
                            $match_date = new DateTime($match->date);
                            $is_past = $match_date < new DateTime();
                            $is_win = $match->team_score > $match->opponent_score;
                            $is_draw = $match->team_score == $match->opponent_score;
                            $opponent_name = $match->club_name ?? 'Club inconnu';
                        ?>
                            <div class="match-card card <?php echo $is_past ? ($is_win ? 'win' : ($is_draw ? 'draw' : 'lose')) : ''; ?>">
                                <div class="match-date">
                                    <?php echo $match_date->format('d/m/Y'); ?>
                                </div>
                                <div class="match-content">
                                    <div class="match-teams">
                                        <!-- equipe a domicile -->
                                        <div class="team-row">
                                            <div class="team">
                                                <div class="team-logo">
                                                    <?php echo strtoupper(substr($match->team_name, 0, 1)); ?>
                                                </div>
                                                <span class="team-name"><?php echo htmlspecialchars($match->team_name); ?></span>
                                            </div>
                                            <?php if ($is_past): ?>
                                                <span class="score"><?php echo $match->team_score; ?></span>
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
                                                <div class="team-logo">
                                                    <?php echo strtoupper(substr($opponent_name, 0, 1)); ?>
                                                </div>
                                                <span class="team-name"><?php echo htmlspecialchars($opponent_name); ?></span>
                                            </div>
                                            <?php if ($is_past): ?>
                                                <span class="score"><?php echo $match->opponent_score; ?></span>
                                            <?php else: ?>
                                                <span class="score">-</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- ligne de statut colorée -->
                                    <div class="status-line"></div>
                                </div>

                                <!-- pied de carte -->
                                <div class="match-footer">
                                    <div class="match-location">
                                        <span>📍</span>
                                        <span><?php echo htmlspecialchars($match->city); ?></span>
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
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>


        </div>
    </main>

    <script src="js/script.js"></script>
</body>

</html>