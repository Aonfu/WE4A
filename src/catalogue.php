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

include "header.php";
include "footer.php";

$stmt = $conn->prepare("SELECT * FROM categorie");
$stmt->execute();
$result_categorie = $stmt->get_result();

if( isset($_GET['tri']) && $_GET['tri'] == 'prix_desc') {
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo ORDER BY montant DESC;");
    $stmt->execute();
    $data = $stmt->get_result();
}

elseif ( isset($_GET['tri']) && $_GET['tri'] == 'prix_asc') {
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo ORDER BY montant ASC;");
    $stmt->execute();
    $data = $stmt->get_result();
}

elseif ( isset($_GET['categorie'])) {
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    WHERE produit.id_categorie = ?
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo;");
    $stmt->bind_param("i",$_GET['categorie']);
    $stmt->execute();
    $data = $stmt->get_result();
}

else {
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo;");
    $stmt->execute();
    $data = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Catalogue</title>
</head>
<body class="background">
<div class="tri">
    <!-- Si le details c de la merde pour le css hésite pas a changer Loic -->
    <details>
        <summary>Trier par :</summary>
        <a href="catalogue.php?tri=prix_desc">Prix décroissant</a>
        <a href="catalogue.php?tri=prix_asc">Prix croissant</a>
        <details>
            <summary>Catégories :</summary>
            <?php while ($row_categorie=$result_categorie->fetch_assoc()) {
                echo('<a href="catalogue.php?categorie='.$row_categorie['id_categorie'].'">'.$row_categorie['nom'].'</a>');
            } ?>
        </details>
    </details>
</div>
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
            <a href="enchere.php?id='.$row['id_produit'].'" class="stretched-link text-decoration-none"></a>
        </div>';
    }
    ?>
</div>
</div>
</body>
</html>



