<?php
session_start();
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
    <meta charset="UTF-8">
    <title>Vente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <link rel="stylesheet" type="text/css" href="ressources/style/style.css">
<body>
<nav class="navbar navbar-expand-md navbar-light bg-warning-subtle flex-nowrap">
    <div class="container-fluid d-flex align-items-center">
        <a class="navbar-brand me-3 pawnstar-font" href="index.php">PawnStar?</a>
        <div class="collapse navbar-collapse order-2 order-md-1" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-0 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active pawnstar-font" aria-current="page" href="index.php">Hub</a>
                </li>
                <?php
                if (isset($_SESSION["id"])) {
                    echo
                    '<li class="nav-item">
                            <a class="nav-link pawnstar-font" href="mon_espace.php">Mon Espace</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pawnstar-font" href="vente.php">Vente</a>
                        </li>';
                } else {
                    echo
                    '<li class="nav-item">
                            <a class="nav-link pawnstar-font" href="connexion.php">Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pawnstar-font" href="inscription.php">Inscription</a>
                        </li>';
                }?>
            </ul>
        </div>
        <form class="d-flex order-1 order-md-2" role="search">
            <input class="form-control me-2 search-bar" type="search" placeholder="Search" aria-label="Search"/>
            <button class="btn search-button" type="submit"></button>
        </form>
        <button class="navbar-toggler order-3 ms-auto navbar-burger" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>
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