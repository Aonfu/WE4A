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

// Calcul du nombre d'enchères gagné et du montant dépensé
$now = new DateTime();
$now_str = $now->format('Y-m-d H:i:s');
$stmt = $conn->prepare("SELECT MAX(enchere.montant) as montant FROM enchere JOIN produit ON enchere.id_produit = produit.id_produit WHERE enchere.montant = (SELECT MAX(montant) FROM enchere WHERE id_produit = produit.id_produit) AND produit.date_fin < ? AND enchere.id_user = ? GROUP BY produit.id_produit");
$stmt->bind_param("si", $now_str, $_SESSION['id']);
$stmt->execute();
$result_gangnant = $stmt->get_result();
$enchere_gagne = $result_gangnant->num_rows;
$montant_depense=0;
while ($row_gagnant = $result_gangnant->fetch_assoc() ) {
    $montant_depense += $row_gagnant['montant'];
}

// Calcul du nombre d'enchères où l'user a participé et calcul du winrate
$stmt = $conn->prepare("SELECT enchere.id_produit FROM enchere JOIN produit ON enchere.id_produit = produit.id_produit WHERE produit.date_fin < ? AND enchere.id_user = ? GROUP BY produit.id_produit");
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
$stmt = $conn->prepare("SELECT enchere.id_produit FROM enchere JOIN produit ON enchere.id_produit = produit.id_produit WHERE produit.date_fin > ? AND enchere.id_user = ? GROUP BY produit.id_produit");
$stmt->bind_param("si", $now_str, $_SESSION['id']);
$stmt->execute();
$result_en_cours = $stmt->get_result();
$enchere_en_cours = $result_en_cours->num_rows;

//calcul de la categorie favorite
$stmt = $conn->prepare("SELECT categorie.nom, COUNT(*) as nb FROM enchere JOIN produit ON enchere.id_produit = produit.id_produit JOIN categorie ON produit.id_categorie = categorie.id_categorie WHERE enchere.id_user = ? GROUP BY categorie.id_categorie ORDER BY nb DESC LIMIT 1");
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Mon Espace</title>
</head>
<body>
<h1>Mon Espace</h1>
<h2>Statistiques:</h2>
<div class="stats">
    <!-- Stats pour les users normaux !-->
    <?php echo'<p>Enchères gagnées : '. $enchere_gagne .'</p>'; ?>
    <?php echo'<p>Montant Dépensé : '. $montant_depense .'</p>'; ?>
    <?php echo'<p>Enchères participées : '. $enchere_participe .'</p>'; ?>
    <?php echo'<p>Winrate : '. $winrate .' % </p>'; ?>
    <?php echo'<p>Enchere en cours : '. $enchere_en_cours .' </p>'; ?>
    <?php echo'<p>Catégorie Favorite : '. ($row_categorie['nom'] ?? 'aucune') .' avec '. ($row_categorie['nb'] ?? 0). ' enchères placées</p>'; ?>
    <br>
    <!-- Stats pour les users vendeur !-->
    <?php echo'<p>Nombre de ventes effectuées : '. $nb_vente .'</p>'; ?>
    <?php echo'<p>Revenu total : '. $revenu_total .'</p>'; ?>
    <?php echo'<p>Plus grosse vente : '. ($row_plus_grosse_vente['montant'] ?? 0) .'</p>'; ?>

</div>

<br>

<hr>  <!-- j'utilise juste ca pour séparer la partie stat de la redirection, à suprimer si besoin -->

<div class="redirection-vente">
    <a href="vente.php">Mettre aux enchères un nouveau produit</a>
</div>
</body>
</html>
