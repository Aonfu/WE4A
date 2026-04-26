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
<body>
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

        <label for="confirm_mdp">Confirmer le Mot de Passe:</label>
        <input type="password" id="confirm_mdp" name="confirm_mdp" placeholder="Confirmer" required>
        <p id="erreur" style="color: red;"></p>

        <br>

        <!-- Il faudra faire la verification du mdp avec javascript (voir sujet) -->

        <button type="submit">S'inscrire</button>

    </form>
    <script>
        const form = document.querySelector('form');
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