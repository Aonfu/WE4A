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

$stmt = $conn->prepare("SELECT * FROM produit");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Hub</title>
</head>
<body>
<h1>Catalogue</h1>
<div class="catalog">
    <?php while ($row=$result->fetch_assoc()) {
        echo '<div class="catalog-element">'.$row['nom'].'<a href="enchere.php?id='.$row['id_produit'].'">Participer à l\'enchère</a></div>'; // faudra aussi echo l'image quand on aura gérer ça
    } ?>
</div>
</body>
</html>



</body>
</html>