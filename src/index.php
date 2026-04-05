<?php
session_start(); // j'ai mis un session start car je pense qu'il sera utile pour empecher un utilisateur non connecté de participer a une enchère
$conn = new mysqli(
    $_ENV['MYSQL_HOST'],
    $_ENV['MYSQL_USER'],
    $_ENV['MYSQL_PASSWORD'],
    $_ENV['MYSQL_DATABASE']
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT * FROM produit");
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hub</title>
    <link rel="stylesheet" href="/ressources/style/style.css">
</head>
<body>
<div class="navbar">
    <a href="connexion.php">Connexion</a>
    <a href="inscription.php">Inscription</a>
    <a href="mon_espace.php">Mon Espace</a>
    <a href="index.php">Hub</a>
</div>
<h1>Catalogue</h1>
<div class="catalog">
    <?php while ($row=$result->fetch_assoc()) {
        echo '<div class="catalog-element">'.$row['nom'].'<a href="enchere.php?id='.$row['id_produit'].'">Participer à l\'enchère</a></div>'; // faudra aussi echo l'image quand on aura gérer ça
    } ?>
</div>
</body>
</html>



