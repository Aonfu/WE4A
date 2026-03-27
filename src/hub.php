<?php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>hub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="navbar">
    <div class="container navbar-content">

        <div class="nav-actions" style="margin-left: auto;">

            <a href="#" class="btn btn-primary">+ Ajouter un objet</a>
            <a href="connexion.php" class="btn btn-text">Connexion</a>
            <a href="inscription.php" class="btn btn-secondary">Inscription</a>

            <div class="dropdown" style="position: relative;">
                <button class="btn btn-secondary dropdown-toggle">Mon Espace ▼</button>

                <div class="dropdown-menu">
                    <a href="#">Mon Porte-monnaie (16€)</a>
                    <a href="#">Déconnexion</a>
                </div>
            </div>

        </div>
    </div>
</header>
    <main class="container page-content">
        <h1>Enchères à la une</h1>
        <p>Découvrez les objets les plus populaires.</p>

        <div class="empty-state">
            <h2>📦 Aucune enchère en cours</h2>
            <p>Il n'y a pas encore d'objets disponibles.</p>
        </div>
    </main>




</body>
</html>
