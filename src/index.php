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

<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hub</title>
    <link rel="stylesheet" href="/ressources/style/style.css">
</head>
<body>
<div class="bandeau">
    <a href="connexion.php">Connexion</a>
    <a href="connexion.php">Inscription</a>
</div>
<h1>Catalogue</h1>

</body>
</html>



