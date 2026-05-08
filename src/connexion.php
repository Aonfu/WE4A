<?php

session_start();

$page_title = "Connexion";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $mdp = $_POST["mdp"];

    $conn = new mysqli(
        $_ENV['MYSQL_HOST'],
        $_ENV['MYSQL_USER'],
        $_ENV['MYSQL_PASSWORD'],
        $_ENV['MYSQL_DATABASE']
    );
    $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE email = ? and mdp = ?");
    $stmt->bind_param("ss", $email,$mdp);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result){
        $_SESSION["id"] = $result["utilisateur_id"];
        echo "<script>window.location.href='catalogue.php';</script>";
        exit();
    }
    else{
        $error = '<p style="color: red; margin-bottom: 0;">Mot de passe ou Email invalide</p>';
    }
}

include "header.php";

?>
    <div class="form-grid">
        <div class="card">
            <div class="card-body">
                <form class="form" action="connexion.php" method="post">
                    <h1 class="pawnstar-font">Connexion</h1>
                    <div class="form-field">
                        <label class="card-text left" for="email">Email:</label>
                        <input class="form-control form-input" type="email" id="email" name="email" placeholder="mail.exemple@ex.com" required>
                    </div>
                    <div class="form-field">
                        <label class="card-text left" for="mdp">Mot de Passe:</label>
                        <input class="form-control form-input" type="password" id="mdp" name="mdp" placeholder="Mot de Passe" required>
                        <?php if(isset($error)){echo $error;} ?>
                    </div>
                    <button class="button" type="submit">Se Connecter</button>
                </form>
            </div>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>