<?php
include "header.php";
$conn = new mysqli(
    $_ENV['MYSQL_HOST'],
    $_ENV['MYSQL_USER'],
    $_ENV['MYSQL_PASSWORD'],
    $_ENV['MYSQL_DATABASE']
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT * FROM categorie");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Vente</title>
<body>
<h1>Mise aux enchères</h1>
<form action="inscription.php" method="post">

    <label for="nom">Nom:</label>
    <input type="text" id="nom" name="nom" placeholder="Nom" required>

    <br>

    <label for="categorie">Catégorie:</label>
    <select name="categorie" id="categorie">
        <?php while ($row=$result->fetch_assoc()) {
            echo('<option value="'.$row['nom'].'">'.$row['nom'].'</option>');
        } ?>
    </select>

    <br>

    <label for="decription">Description:</label>
    <textarea id="decription" placeholder="description"></textarea>

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