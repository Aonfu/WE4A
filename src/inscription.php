<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $mdp = $_POST["mdp"];

    $conn = new mysqli(
        $_ENV['MYSQL_HOST'],
        $_ENV['MYSQL_USER'],
        $_ENV['MYSQL_PASSWORD'],
        $_ENV['MYSQL_DATABASE']
    );
    $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, email, mdp) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nom, $prenom, $email, $mdp);
    $stmt->execute();

    #il faudra aussi changer le header, il est temporaire
    header("Location: index.php");
    exit();
}
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <link rel="stylesheet" type="text/css" href="ressources/style/style.css">
</head>
<body>
<nav class="navbar navbar-expand-md navbar-light bg-warning-subtle flex-nowrap">
    <div class="container-fluid d-flex align-items-center">
        <a class="navbar-brand me-3 pawnstar-font" href="index.php">PawnStar?</a>
        <div class="collapse navbar-collapse order-2 order-md-1" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-0 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active pawnstar-font" aria-current="page" href="index.php">Hub</a>
                </li>
                <?php
                if (isset($_SESSION["id"])) {
                    echo
                    '<li class="nav-item">
                            <a class="nav-link pawnstar-font" href="mon_espace.php">Mon Espace</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pawnstar-font" href="vente.php">Vente</a>
                        </li>';
                } else {
                    echo
                    '<li class="nav-item">
                            <a class="nav-link pawnstar-font" href="connexion.php">Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pawnstar-font" href="inscription.php">Inscription</a>
                        </li>';
                }?>
            </ul>
        </div>
        <form class="d-flex order-1 order-md-2" role="search">
            <input class="form-control me-2 search-bar" type="search" placeholder="Search" aria-label="Search"/>
            <button class="btn search-button" type="submit"></button>
        </form>
        <button class="navbar-toggler order-3 ms-auto navbar-burger" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>
    <h1>Inscription</h1>
    <form action="inscription.php" method="post">

        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom" placeholder="Nom" required>

        <br>

        <label for="prenom">Prénom:</label>
        <input type="text" id="prenom" name="prenom" placeholder="Prénom" required>

        <br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="mail.exemple@ex.com" required>

        <br>

        <label for="mdp">Mot de Passe:</label>
        <input type="password" id="mdp" name="mdp" placeholder="Mot de Passe" required>

        <br>

        <!-- Il faudra faire la verification du mdp avec javascript (voir sujet) -->

        <button type="submit">S'inscrire</button>

    </form>
</body>
</html>