<?php

include "header.php";
include "footer.php";

date_default_timezone_set('Europe/Paris'); //ligne assez importante pour régler un bug
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

$min_date = date('Y-m-d\TH:i');

$id=$_GET['id'];

//requete pour remplir le formulaire
$stmt = $conn->prepare("SELECT produit.id_produit, produit.nom AS nom, produit.description, produit.photo, produit.date_fin, produit.id_categorie ,GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
FROM produit
LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
WHERE  produit.id_produit = ?
GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo, produit.id_categorie;");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_produit = $stmt->get_result();
$row_produit = $result_produit->fetch_assoc();

// requete pour update le produit dans la bdd
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"];
    $categorie = $_POST["categorie"];
    $description = $_POST["description"];
    $photo = $_FILES["photo"];
    $db_photo = __DIR__."/temporary/path/" . $photo["name"];
    $prix = $_POST["prix"];
    $date_fin= $_POST["date_fin"];
    $stmt = $conn->prepare("UPDATE produit SET nom = ?,id_categorie = ?, description = ?, photo = ?, date_fin = ? WHERE id_produit = ?");
    $stmt->bind_param("sisssi", $nom, $categorie, $description, $db_photo, $date_fin,$id);
    $stmt->execute();
    echo "<script>window.location.href='mon_espace.php';</script>"; // redirection en Javascript pour éviter un bug causé par le header
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Editer un produit</title>
</head>
<body>
<h1>Édition et Suppression</h1>

<br>

<h2>Éditer</h2>

<form action="editer_produit.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">

    <label for="nom">Nom:</label>
    <input type="text" id="nom" name="nom" placeholder="Nom" value="<?php echo $row_produit['nom']; ?>" required>

    <br>

    <label for="categorie">Catégorie:</label>
    <select name="categorie" id="categorie">
        <?php while ($row=$result->fetch_assoc()) {
            $selected = ($row['id_categorie'] == $row_produit['id_categorie'] ) ? 'selected' : '';
            echo '<option value="'.$row['id_categorie'].'" '.$selected.'>'.$row['nom'].'</option>';
        } ?>
    </select>

    <br>

    <label for="description">Description:</label>
    <textarea id="description" name="description" placeholder="description"> <?php echo $row_produit['description']; ?></textarea>

    <br>

    <label for="photo">Photo:</label>
    <input type="file" id="photo" name="photo" accept="image/*" >
    <img src="<?php echo $row_produit['photo']; ?>" alt="<?php echo $row_produit['description']; ?>">

    <br>

    <label for="prix">Prix de départ:</label>
    <input type="number" id="prix" name="prix" required min="1" step="1" value="<?php echo $row_produit['montant']; ?>" readonly>

    <br>

    <label for="date_fin">Date de fin:</label>
    <input type="datetime-local" id="date_fin" name="date_fin" min = "<?php echo $min_date; ?>" value="<?php echo date('Y-m-d\TH:i', strtotime($row_produit['date_fin'])); ?>" required >

    <button type="submit">Mettre aux enchères</button>

</form>
</body>
</html>