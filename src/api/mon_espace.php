<?php
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

session_start();
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

if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'non_connecte']);
    exit();
}

// Récupération des enchères remportées par l'utilisateur et calcul du montant total dépensé
$now = new DateTime();
$now_str = $now->format('Y-m-d H:i:s');
$stmt = $conn->prepare("SELECT MAX(enchere.montant) as montant, enchere.id_produit FROM enchere JOIN produit ON enchere.id_produit = produit.id_produit WHERE enchere.montant = (SELECT MAX(montant) FROM enchere WHERE id_produit = produit.id_produit) AND produit.date_fin < ? AND enchere.id_utilisateur = ? GROUP BY produit.id_produit");
$stmt->bind_param("si", $now_str, $_SESSION['id']);
$stmt->execute();
$result_gagnant = $stmt->get_result();
$enchere_gagne = $result_gagnant->num_rows;
$montant_depense = 0;
while ($row_gagnant = $result_gagnant->fetch_assoc()) {
    $montant_depense += $row_gagnant['montant'];
}

// Récupération des produits correspondant aux enchères gagnées
$stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, MAX(enchere.montant) as montant
FROM enchere 
JOIN produit ON enchere.id_produit = produit.id_produit 
WHERE enchere.montant = (SELECT MAX(montant) FROM enchere WHERE id_produit = produit.id_produit) 
AND produit.date_fin < ? 
AND enchere.id_utilisateur = ? 
GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo
ORDER BY produit.date_fin DESC");
$stmt->bind_param("si", $now_str, $_SESSION['id']);
$stmt->execute();
$encheres_gagnees = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

//Récupération des enchères en cours avec les détails des produits
$stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, enchere.montant
FROM enchere 
JOIN produit ON enchere.id_produit = produit.id_produit 
WHERE produit.date_fin > ? 
AND enchere.id_utilisateur = ? 
GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo, enchere.montant
ORDER BY produit.date_fin ASC");
$stmt->bind_param("si", $now_str, $_SESSION['id']);
$stmt->execute();
$encheres_en_cours = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calcul du nombre d'enchères auxquelles l'utilisateur a participé et du taux de réussite (winrate)
$stmt = $conn->prepare("SELECT enchere.id_produit FROM enchere JOIN produit ON enchere.id_produit = produit.id_produit WHERE produit.date_fin < ? AND enchere.id_utilisateur = ? GROUP BY produit.id_produit");
$stmt->bind_param("si", $now_str, $_SESSION['id']);
$stmt->execute();
$result_participe = $stmt->get_result();
$enchere_participe = $result_participe->num_rows;
if ($enchere_participe > 0) {
    $winrate = ($enchere_gagne / $enchere_participe) * 100;
} else {
    $winrate = 0;
}

// Calcul du nombre d'enchères actives auxquelles l'utilisateur participe
$stmt = $conn->prepare("SELECT enchere.id_produit FROM enchere JOIN produit ON enchere.id_produit = produit.id_produit WHERE produit.date_fin > ? AND enchere.id_utilisateur = ? GROUP BY produit.id_produit");
$stmt->bind_param("si", $now_str, $_SESSION['id']);
$stmt->execute();
$enchere_en_cours = $stmt->get_result()->num_rows;

// Détermination de la catégorie de produits préférée de l'utilisateur
$stmt = $conn->prepare("SELECT categorie.nom, COUNT(*) as nb FROM enchere JOIN produit ON enchere.id_produit = produit.id_produit JOIN categorie ON produit.id_categorie = categorie.id_categorie WHERE enchere.id_utilisateur = ? GROUP BY categorie.id_categorie ORDER BY nb DESC LIMIT 1");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$row_categorie = $stmt->get_result()->fetch_assoc();

// Calcul des produits vendus par l'utilisateur et du revenu total généré
$stmt = $conn->prepare("SELECT MAX(enchere.montant) as montant FROM enchere JOIN produit ON enchere.id_produit = produit.id_produit WHERE produit.date_fin < ? AND produit.id_utilisateur = ? GROUP BY produit.id_produit");
$stmt->bind_param("si", $now_str, $_SESSION['id']);
$stmt->execute();
$result_vente = $stmt->get_result();
$nb_vente = $result_vente->num_rows;
$revenu_total = 0;
while ($row_vente = $result_vente->fetch_assoc()) {
    $revenu_total += $row_vente['montant'];
}

// Récupération du montant de la plus grosse vente réalisée par l'utilisateur
$stmt = $conn->prepare("SELECT MAX(enchere.montant) as montant FROM enchere LEFT JOIN produit ON enchere.id_produit = produit.id_produit WHERE produit.date_fin < ? AND produit.id_utilisateur = ? GROUP BY produit.id_produit ORDER BY montant DESC LIMIT 1");
$stmt->bind_param("si", $now_str, $_SESSION['id']);
$stmt->execute();
$row_plus_grosse_vente = $stmt->get_result()->fetch_assoc();

// Récupération des produits actuellement mis en vente par l'utilisateur (enchères non terminées)
$stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
FROM produit
LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
WHERE produit.date_fin > ? AND produit.id_utilisateur = ?
GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo
ORDER BY produit.date_fin ASC");
$stmt->bind_param("si", $now_str, $_SESSION['id']);
$stmt->execute();
$produits_en_vente = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'enchere_gagne' => $enchere_gagne,
    'montant_depense' => $montant_depense,
    'winrate' => round($winrate, 2),
    'enchere_en_cours' => $enchere_en_cours,
    'categorie_favorite' => $row_categorie['nom'] ?? 'Aucune',
    'nb_vente' => $nb_vente,
    'revenu_total' => $revenu_total,
    'plus_grosse_vente' => $row_plus_grosse_vente['montant'] ?? 0,
    'encheres_gagnees' => $encheres_gagnees,
    'encheres_en_cours' => $encheres_en_cours,
    'produits_en_vente' => $produits_en_vente
]);