<?php
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
        #il faudra changer le header car il est temporaire
        header("Location: hub.php");
        exit();
    }
    else{
        $error = "mdp ou email invalide";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="../ressources/style/style.css">
</head>
<body>
<h1>Connexion</h1>
<?php if(isset($error)){echo $error;} ?>
<form action="connexion.php" method="post">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" placeholder="mail.exemple@ex.com" required>

    <label for="mdp">Mot de Passe</label>
    <input type="password" id="mdp" name="mdp" placeholder="Mot de Passe" required>

    <button type="submit">Se connecter</button>

</form>
</body>
</html>