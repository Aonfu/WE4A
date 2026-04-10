<?php
include "header.php";
include "footer.php";
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
<html lang="fr">
<head>
    <title>Mon Espace</title>
</head>
<body>
<h1>Mon Espace</h1>
<h2>Statistiques:</h2>
<div class="redirection-vente">
    <a href="vente.php">Mettre aux enchères un nouveau produit</a>
</div>
</body>
</html>