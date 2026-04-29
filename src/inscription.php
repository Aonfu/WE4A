<?php

include "header.php";
include "footer.php";

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
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Inscription</title>
</head>
<body class="background">
    <div class="form-grid">
        <div class="card">
            <div class="card-body">
                <form class="form" action="inscription.php" method="post">
                    <h1 class="pawnstar-font">Inscription</h1>
                    <div class="form-row">
                        <div class="form-field">
                            <label class="card-text" for="nom">Nom:</label>
                            <input class="form-control form-input" type="text" id="nom" name="nom" placeholder="Nom" required>
                        </div>
                        <div class="form-field">
                            <label class="card-text" for="prenom">Prénom:</label>
                            <input class="form-control form-input" type="text" id="prenom" name="prenom" placeholder="Prénom" required>
                        </div>
                    </div>
                    <div class="form-field">
                        <label class="card-text" for="email">Email:</label>
                        <input class="form-control form-input" type="email" id="email" name="email" placeholder="mail.exemple@ex.com" required>
                    </div>
                    <div class="form-field">
                        <label class="card-text" for="mdp">Mot de Passe:</label>
                        <input class="form-control form-input" type="password" id="mdp" name="mdp" placeholder="Mot de Passe" required>
                    </div>
                    <button class="button" type="submit">S'inscrire</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>