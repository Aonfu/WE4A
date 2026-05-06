<?php

session_start();

$page_title = "Mon Espace";

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

// Calcul du nombre d'enchères gagné et du montant dépensé
$now = new DateTime();
$now_str = $now->format('Y-m-d H:i:s');
$stmt = $conn->prepare("SELECT MAX(enchere.montant) as montant FROM enchere JOIN produit ON enchere.id_produit = produit.id_produit WHERE enchere.montant = (SELECT MAX(montant) FROM enchere WHERE id_produit = produit.id_produit) AND produit.date_fin < ? AND enchere.id_utilisateur = ? GROUP BY produit.id_produit");
$stmt->bind_param("si", $now_str, $_SESSION['id']);
$stmt->execute();
$result_gangnant = $stmt->get_result();
$enchere_gagne = $result_gangnant->num_rows;
$montant_depense=0;
while ($row_gagnant = $result_gangnant->fetch_assoc() ) {
    $montant_depense += $row_gagnant['montant'];
}

// Calcul du nombre d'enchères où l'user a participé et calcul du winrate
$stmt = $conn->prepare("SELECT enchere.id_produit FROM enchere JOIN produit ON enchere.id_produit = produit.id_produit WHERE produit.date_fin < ? AND enchere.id_utilisateur = ? GROUP BY produit.id_produit");
$stmt->bind_param("si", $now_str, $_SESSION['id']);
$stmt->execute();
$result_participe = $stmt->get_result();
$enchere_participe = $result_participe->num_rows;
if ($enchere_participe > 0) {
    $winrate = ($enchere_gagne/$enchere_participe) * 100;
}
else {
    $winrate = 0;
}

//Calcul du nombre d'enchères en cours
$stmt = $conn->prepare("SELECT enchere.id_produit FROM enchere JOIN produit ON enchere.id_produit = produit.id_produit WHERE produit.date_fin > ? AND enchere.id_utilisateur = ? GROUP BY produit.id_produit");
$stmt->bind_param("si", $now_str, $_SESSION['id']);
$stmt->execute();
$result_en_cours = $stmt->get_result();
$enchere_en_cours = $result_en_cours->num_rows;

//calcul de la categorie favorite
$stmt = $conn->prepare("SELECT categorie.nom, COUNT(*) as nb FROM enchere JOIN produit ON enchere.id_produit = produit.id_produit JOIN categorie ON produit.id_categorie = categorie.id_categorie WHERE enchere.id_utilisateur = ? GROUP BY categorie.id_categorie ORDER BY nb DESC LIMIT 1");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result_categorie = $stmt->get_result();
$row_categorie = $result_categorie->fetch_assoc();

// Calcul du nombre de produits vendu et du revenu total
$stmt = $conn->prepare("SELECT MAX(enchere.montant) as montant FROM enchere JOIN produit ON enchere.id_produit = produit.id_produit WHERE produit.date_fin < ? AND produit.id_utilisateur = ? GROUP BY produit.id_produit");
$stmt->bind_param("si", $now_str, $_SESSION['id']);
$stmt->execute();
$result_vente = $stmt->get_result();
$nb_vente = $result_vente->num_rows;
$revenu_total=0;
while ($row_vente = $result_vente->fetch_assoc() ) {
    $revenu_total += $row_vente['montant'];
}

// Calcul de la plus grosse vente
$stmt = $conn->prepare("SELECT MAX(enchere.montant) as montant FROM enchere LEFT JOIN produit ON enchere.id_produit = produit.id_produit WHERE produit.date_fin < ? AND produit.id_utilisateur = ? GROUP BY produit.id_produit ORDER BY montant DESC LIMIT 1");
$stmt->bind_param("si", $now_str, $_SESSION['id']);
$stmt->execute();
$result_plus_grosse_vente = $stmt->get_result();
$row_plus_grosse_vente = $result_plus_grosse_vente->fetch_assoc();

// Selectionne les produits en vente de l'utilisateur
$stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
FROM produit
LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
WHERE produit.date_fin > ? AND produit.id_utilisateur = ?
GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo;");
$stmt->bind_param("si", $now_str,$_SESSION['id']);
$stmt->execute();
$data = $stmt->get_result();

include "header.php";

?>
    <div class="stat-grid">
        <div class="card">
            <h1 class="pawnstar-font text-center">Statistiques</h1>
            <div class="card-body">
                <div class="stats">
                    <div class="stat">
                        <p class="card-text">Enchères Gagnées</p>
                        <?php echo'<h1 class="pawnstar-font">'. $enchere_gagne .'</h1>'; ?>
                    </div>
                    <div class="stat">
                        <p class="card-text">Montant Dépensé</p>
                        <?php echo'<h1 class="pawnstar-font">$'. $montant_depense .'</h1>'; ?>
                    </div>
                    <div class="stat">
                        <p class="card-text">Winrate</p>
                        <?php echo'<h1 class="pawnstar-font">'. $winrate .' %</h1>'; ?>
                    </div>
                    <div class="stat">
                        <p class="card-text">Enchères en Cours</p>
                        <?php echo'<h1 class="pawnstar-font">'. $enchere_en_cours .'</h1>'; ?>
                    </div>
                    <div class="stat">
                        <p class="card-text">Catégorie Favorite</p>
                        <?php echo'<h1 class="pawnstar-font">'. ($row_categorie['nom'] ?? 'aucune') .'</h1>'; ?>
                    </div>
                    <div class="stat">
                        <p class="card-text">Nombre de Ventes Effectuées</p>
                        <?php echo'<h1 class="pawnstar-font">'. $nb_vente .'</h1>'; ?>
                    </div>
                    <div class="stat">
                        <p class="card-text">Revenu Total</p>
                        <?php echo'<h1 class="pawnstar-font">'. $revenu_total .'$</h1>'; ?>
                    </div>
                    <div class="stat">
                        <p class="card-text">Plus Grosse Vente</p>
                        <?php echo'<h1 class="pawnstar-font">$'. ($row_plus_grosse_vente['montant'] ?? 0) .'</h1>'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="centrer">
        <div class="catalogue-grid">
            <?php while ($row = $data->fetch_assoc()) { ?>
                <div class="card">
                    <div class="card-images">
                        <img src="ressources/img/<?php echo $row["photo"]; ?>" class="card-img-top" alt="...">
                        <img src="ressources/img/scotch.png" class="scotch-1" alt="...">
                        <img src="ressources/img/scotch.png" class="scotch-2" alt="...">
                    </div>
                    <div class="card-body">
                        <h3 class="card-text card-title"><?php echo $row["nom"]; ?></h3>
                        <p class="card-text"><?php echo $row["description"]; ?></p>
                        <h1 class="card-text pawnstar-font">$<?php echo $row["montant"]; ?></h1>
                    </div>
                    <a href="enchere.php?id=<?php echo $row["id_produit"]; ?>" class="stretched-link text-decoration-none"></a>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
