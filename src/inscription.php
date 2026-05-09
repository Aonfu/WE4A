<?php

session_start();

$page_title = "Inscription";
// Initialisation de la variable d'erreur pour l'affichage
$erreur = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $mdp = $_POST["mdp"];
    // Hashage du mot de passe
    $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);

    $conn = new mysqli(
        $_ENV['MYSQL_HOST'],
        $_ENV['MYSQL_USER'],
        $_ENV['MYSQL_PASSWORD'],
        $_ENV['MYSQL_DATABASE']
    );
    // Vérifie si l'email est déjà utilisé par un autre compte
    $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $erreur = 'Cet email est déjà utilisé !';
    }
    else {
        $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, email, mdp) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nom, $prenom, $email, $mdp_hash);
        $stmt->execute();
        echo "<script>window.location.href='catalogue.php';</script>";
        exit();
    }
}

include "header.php";

?>
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
                         <p id="erreur" style="color: red; margin-bottom: 0;"><?php echo $erreur ?></p>
                    </div>
                    <button class="button" type="submit">S'inscrire</button>
                </form>
            </div>
        </div>
    </div>
    <?php include "footer.php"; ?>
    <script>
        // Validation JavaScript : vérifie que les deux mots de passe correspondent avant l'envoi
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