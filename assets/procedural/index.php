<?php
include "classes/database/Database.php";
$pdo = Database::getConnection();

$players = $pdo->query("SELECT * FROM player")->fetchAll(PDO::FETCH_OBJ);
$teams = $pdo->query("SELECT * FROM team")->fetchAll(PDO::FETCH_OBJ);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/index.css">
</head>



<body>

    <main>
        <?php include "includes/navbar.php"; ?>

        <div class="container">

            <div class="header">
                <div class="page-title">
                    <div>
                        <h1>Tableau de bord</h1>
                        <p>Vue d'ensemble de votre club de Football</p>
                    </div>
                </div>
            </div>

            <div class="dashboard">
                <div class="dashboard-section joueurs">
                    <div class="dashboard-section-icon">
                        👤
                    </div>
                    <div class="number-jouers">
                        <?php
                        echo count($players);
                        ?>
                    </div>
                    <div class="dashboard-section-title">
                        Total de jouers
                    </div>
                </div>

                <div class="dashboard-section equipe">
                    <div class="dashboard-section-icon">
                        🛡️
                    </div>
                    <div class="number-equipes">
                        <?php
                        echo count($teams);
                        ?>
                    </div>
                    <div class="dashboard-section-title">
                        Nombre d'équipes
                    </div>
                </div>

                <div class="dashboard-section matchs">
                    <div class="dashboard-section-icon">
                        🧑‍💼
                    </div>
                    <div class="number-matchs">
                        <!-- mettre php -->
                    </div>
                    <div class="dashboard-section-title">
                        Match ce mois ci
                    </div>
                </div>

                <div class="dashboard-section victoires">
                    <div class="dashboard-section-icon">
                        📅
                    </div>
                    <div class="number-victoires">
                        <!-- mettre php -->
                    </div>
                    <div class="dashboard-section-title">
                        Victoires
                    </div>
                </div>



                <div class="dashboard-section match-recent">
                    <div class="dashboard-section-title">
                        Match recents
                    </div>
                    <!-- <img src="" alt="" class="dashboard-section-image"> -->
                    <div class="section-data">
                        <ul>
                        </ul>
                    </div>
                </div>

                <div class="dashboard-section match-prochain">
                    <div class="dashboard-section-title">
                        Prochains match
                    </div>
                    <!-- <img src="" alt="" class="dashboard-section-image"> -->
                    <div class="section-data">
                        <ul>
                        </ul>
                    </div>
                </div>


            </div>

        </div>
    </main>

</body>

</html>