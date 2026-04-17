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
$stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, MAX(enchere.montant) AS montant
FROM produit
JOIN enchere ON produit.id_produit = enchere.id_produit
GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo;");
$stmt->execute();
$data = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="..." crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="ressources/style/style.css">
</head>
<body class="background">
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
<div class="centrer">
<div class="card-grid">
    <?php
    while ($row = $data->fetch_assoc()) {
        echo
                '<div class="card">
            <div class="card-images">
                <img src="ressources/img/' . $row["photo"] . '" class="card-img-top" alt="...">
                <img src="ressources/img/scotch.png" class="scotch-1" alt="...">
                <img src="ressources/img/scotch.png" class="scotch-2" alt="...">
            </div>
            <div class="card-body">
                <h3 class="card-text card-title">' . $row["nom"] . '</h3>
                <p class="card-text">' . $row["description"] . '</p>
                <h1 class="card-text pawnstar-font">' . $row["montant"] . '$</h1>
            </div>
            <a href="enchere.php" class="stretched-link text-decoration-none" id="' . $row["id_produit"] . '"></a>
        </div>';
    }
    ?>
</div>
</div>
</body>
</html>



