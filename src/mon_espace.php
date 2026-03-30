<?php
$conn = new mysqli(
    $_ENV['MYSQL_HOST'],
    $_ENV['MYSQL_USER'],
    $_ENV['MYSQL_PASSWORD'],
    $_ENV['MYSQL_DATABASE']
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mon Espace</title>
    <link rel="stylesheet" href="/ressources/style/style.css">
</head>
<body>
<div class="bandeau">
    <a href="connexion.php">Connexion</a>
    <a href="connexion.php">Inscription</a>
    <a href="mon_espace.php">Mon Espace</a>
    <a href="index.php">Hub</a>
</div>
<h1>Mon Espace</h1>
<h2>Statistiques:</h2>
<div class="redirection-vente">
    <a href="vente.php">Mettre au enchères un nouveau produits</a>
</div>
</body>
</html>