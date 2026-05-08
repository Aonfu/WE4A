<?php

session_start();
date_default_timezone_set('Europe/Paris');
$page_title = "Catalogue";

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
$result_categorie = $stmt->get_result();

$now = new DateTime();
$now_str = $now->format('Y-m-d H:i:s');

//tri par prix décroissant
if( isset($_GET['tri']) && $_GET['tri'] == 'prix_desc') {
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    WHERE produit.date_fin > ?
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo ORDER BY montant DESC;");
    $stmt->bind_param("s", $now_str);
    $stmt->execute();
    $data = $stmt->get_result();
}

//tri par prix croissant
elseif ( isset($_GET['tri']) && $_GET['tri'] == 'prix_asc') {
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    WHERE produit.date_fin > ?
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo ORDER BY montant ASC;");
    $stmt->bind_param("s", $now_str);
    $stmt->execute();
    $data = $stmt->get_result();
}

//tri par catégorie
elseif ( isset($_GET['categorie'])) {
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    WHERE produit.id_categorie = ? AND produit.date_fin > ?
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo;");
    $stmt->bind_param("is",$_GET['categorie'], $now_str);
    $stmt->execute();
    $data = $stmt->get_result();
}

//recherche
elseif ( isset($_GET['search'])) {
    $search = '%'.$_GET['search'].'%';
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    WHERE produit.date_fin > ? AND produit.nom LIKE ?
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo;");
    $stmt->bind_param("ss", $now_str, $search);
    $stmt->execute();
    $data = $stmt->get_result();
}

//filtre si prix max et prix min sont tous les deux rentrés par l'user
elseif (isset($_GET['prix_min']) && isset($_GET['prix_max'])) {
    $prix_max = $_GET['prix_max'];
    $prix_min = $_GET['prix_min'];
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    WHERE produit.date_fin > ?
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo
    HAVING montant <= ? AND montant >= ? ;");
    $stmt->bind_param("sii", $now_str,$prix_max, $prix_min);
    $stmt->execute();
    $data = $stmt->get_result();
}

//filtre Plus de :
elseif ( isset($_GET['prix_min'])) {
    $prix_min = $_GET['prix_min'];
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    WHERE produit.date_fin > ?
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo
    HAVING montant >= ?;");
    $stmt->bind_param("si", $now_str,$prix_min);
    $stmt->execute();
    $data = $stmt->get_result();
}

//filtre Moins de :
elseif ( isset($_GET['prix_max'])) {
    $prix_max = $_GET['prix_max'];
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    WHERE produit.date_fin > ?
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo
    HAVING montant <= ?;");
    $stmt->bind_param("si", $now_str,$prix_max);
    $stmt->execute();
    $data = $stmt->get_result();
}

//tri par défaut
else {
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    WHERE produit.date_fin > ?
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo;");
    $stmt->bind_param("s", $now_str);
    $stmt->execute();
    $data = $stmt->get_result();
}

include "header.php";

?>
<div class="tri">
    <!-- Si le details c de la merde pour le css hésite pas a changer Loic -->
    <details>
        <summary>Trier :</summary>
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
<div class="filtre">
    <details>
        <summary>Filtrer par prix :</summary>
        <form action="catalogue.php" method="get">

            <label>Plus de :</label>
            <input type="number" name="prix_min">

            <label>Moins de :</label>
            <input type="number" name="prix_max">

            <button type="submit">Filtrer</button>
        </form>
    </details>
</div>
<div class="centrer">
<div class="catalogue-grid">
    <?php
    while ($row = $data->fetch_assoc()) {
        echo
                '<div class="card">
            <div class="card-images">
                <img src="' . $row["photo"] . '" class="card-img-top" alt="produit aux enchères">
                <img src="ressources/img/scotch.png" class="scotch-1" alt="...">
                <img src="ressources/img/scotch.png" class="scotch-2" alt="...">
            </div>
            <div class="card-body">
                <h3 class="card-text card-title">' . $row["nom"] . '</h3>
                <p class="card-text">' . $row["description"] . '</p>
                <h1 class="card-text pawnstar-font">$' . $row["montant"] . '</h1>
            </div>
            <a href="enchere.php?id='.$row['id_produit'].'" class="stretched-link text-decoration-none"></a>
        </div>';
    }
    ?>
</div>
</div>
<?php include "footer.php"; ?>
</body>
</html>



