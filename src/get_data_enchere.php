<?php
header('Content-Type: application/json');
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

// Récupération des infos du produit
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM produit WHERE id_produit = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_produit = $stmt->get_result();

// Récupération du montant le plus élevé
$stmt = $conn->prepare("SELECT MAX(montant) FROM enchere WHERE id_produit = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_enchere = $stmt->get_result();

// Récupération du meilleur enchérisseur
$stmt = $conn->prepare("SELECT utilisateur.nom, enchere.montant FROM utilisateur JOIN enchere ON utilisateur.utilisateur_id = enchere.id_utilisateur WHERE enchere.id_produit = ? ORDER BY enchere.montant DESC LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_gagnant = $stmt->get_result();
$row_gagnant = $result_gagnant->fetch_assoc();

// Calcul du montant minimum pour la prochaine enchère (+5%)
$row_produit = $result_produit->fetch_assoc();
$row_enchere = $result_enchere->fetch_assoc();
$enchere_min = max($row_produit['prix_depart'], $row_enchere['MAX(montant)'] ?? 0);
$enchere_min = ceil($enchere_min * 1.05);

// Calcule la différence de temps entre le présent et la date de fin de l'enchère
$now = new DateTime();
$date_fin = new DateTime($row_produit['date_fin']);

// Récupération de tout l'historique des enchères
$stmt = $conn->prepare("SELECT enchere.montant, utilisateur.nom FROM enchere JOIN utilisateur ON enchere.id_utilisateur = utilisateur.utilisateur_id WHERE enchere.id_produit = ? ORDER BY enchere.montant DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_historique = $stmt->get_result();

// Envoi des données au format JSON pour l'AJAX
$historique = [];
while($row_historique = $result_historique->fetch_assoc()){
    $historique[] = $row_historique;
}
echo json_encode([
    'enchere_min' => $enchere_min,
    'date_fin' => $row_produit['date_fin'],
    'gagnant' => $row_gagnant,
    'historique' => $historique,
    'termine' => $date_fin < $now,
    'prix_actuel' => max($row_produit['prix_depart'], $row_enchere['MAX(montant)'] ?? 0)
]);
?>