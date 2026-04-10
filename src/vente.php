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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"];
    $categorie = $_POST["categorie"];
    $description = $_POST["description"];
    $photo = $_FILES["photo"];
    $db_photo = __DIR__."/temporary/path/" . $photo["name"];
    $prix = $_POST["prix"];
    $stmt = $conn->prepare("INSERT INTO produit (id_utilisateur, nom, id_categorie, description, photo, prix_depart) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isissi", $_SESSION["id"],$nom, $categorie, $description, $db_photo, $prix);
    $stmt->execute();
    header("Location: mon_espace.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Vente</title>
<body>
<h1>Mise aux enchères</h1>
<form action="vente.php" method="post" enctype="multipart/form-data">

    <label for="nom">Nom:</label>
    <input type="text" id="nom" name="nom" placeholder="Nom" required>

    <br>

    <label for="categorie">Catégorie:</label>
    <select name="categorie" id="categorie">
        <?php while ($row=$result->fetch_assoc()) {
            echo('<option value="'.$row['id_categorie'].'">'.$row['nom'].'</option>');
        } ?>
    </select>

    <br>

    <label for="description">Description:</label>
    <textarea id="description" name="description" placeholder="description"></textarea>

    <br>

    <label for="photo">Photo:</label>
    <input type="file" id="photo" name="photo" accept="image/*" required>

    <br>

    <label for="prix">Prix de départ:</label>
    <input type="number" id="prix" name="prix" required min="1" step="1">

    <br>

    <button type="submit">Mettre aux enchères</button>

</form>
</body>
</html>