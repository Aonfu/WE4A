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
<h1>Mise aux enchères</h1>
<form action="inscription.php" method="post">

    <label for="nom">Nom:</label>
    <input type="text" id="nom" name="nom" placeholder="Nom" required>

    <br>

    <label for="categorie">Catégorie:</label>
    <input type="" id="categorie" name="categorie" required>

    <br>

    <label for="decription">Décription:</label>
    <textarea id="decription" placeholder="décription"></textarea>

    <br>

    <label for="photo">Photo:</label>
    <input type="file" id="photo" name="photo" accept="image/*" required>

    <br>

    <label for="prix">Prix de départ:</label>
    <input type="number" id="prix" name="prix" required min="1" step="10">

    <br>

    <button type="submit">Mettre aux enchères</button>

</form>
</body>
</html>