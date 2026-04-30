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
    echo "<script>window.location.href='index.php';</script>";
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
                <form class="form" action="inscription.php" method="post" id="form_inscription">
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
                    <div class="form-field">
                         <label class="card-text" for="confirm_mdp">Confirmer le Mot de Passe:</label>
                         <input class="form-control form-input" type="password" id="confirm_mdp" name="confirm_mdp" placeholder="Confirmer" required>
                         <p id="erreur" style="color: red; margin-bottom: 0;"></p>
                    </div>
                    <button class="button" type="submit">S'inscrire</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        const form = document.getElementById('form_inscription');
        const mdp = document.getElementById('mdp');
        const confirmMdp = document.getElementById('confirm_mdp');
        const erreurMsg = document.getElementById('erreur');

        form.addEventListener('submit', function(event) {
            if (mdp.value !== confirmMdp.value) {
                event.preventDefault();
                erreurMsg.textContent = "Les mots de passe ne sont pas identiques !";
            } else {
                erreurMsg.textContent = "";
            }
        });

    </script>
</body>
</html>