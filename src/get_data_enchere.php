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

// Récupère tous les éléments sur le produit
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM produit WHERE id_produit = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_produit = $stmt->get_result();

// Selection l'enchère actuelle (l'enchère avec le plus haut montant)
$stmt = $conn->prepare("SELECT MAX(montant) FROM enchere WHERE id_produit = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_enchere = $stmt->get_result();

// Récupère le nom du gagnant de l'enchère et le montant de son enchère
$stmt = $conn->prepare("SELECT utilisateur.nom, enchere.montant FROM utilisateur JOIN enchere ON utilisateur.utilisateur_id = enchere.id_utilisateur WHERE enchere.id_produit = ? ORDER BY enchere.montant DESC LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_gagnant = $stmt->get_result();
$row_gagnant = $result_gagnant->fetch_assoc();

// Partie qui calcule le prix minimum de l'enchère qui doit être 5% plus que le prix actuel
$row_produit = $result_produit->fetch_assoc();
$row_enchere = $result_enchere->fetch_assoc();
$enchere_min = max($row_produit['prix_depart'], $row_enchere['MAX(montant)'] ?? 0);
$enchere_min = ceil($enchere_min * 1.05);

// Calcule la différence de temps entre le présent et la date de fin de l'enchère
$now = new DateTime();
$date_fin = new DateTime($row_produit['date_fin']);

// Récupère l'historique des enchères pour l'afficher
$stmt = $conn->prepare("SELECT enchere.montant, utilisateur.nom FROM enchere JOIN utilisateur ON enchere.id_utilisateur = utilisateur.utilisateur_id WHERE enchere.id_produit = ? ORDER BY enchere.montant DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_historique = $stmt->get_result();

// Préparation d'un tableau PHP historique à retourner en JSON
$historique = [];
while($row_historique = $result_historique->fetch_assoc()){
    $historique[] = $row_historique;
}

// On envoie le JSON
echo json_encode([
    'enchere_min' => $enchere_min,
    'date_fin' => $row_produit['date_fin'],
    'gagnant' => $row_gagnant,
    'historique' => $historique,
    'termine' => $date_fin < $now,
    'prix_actuel' => max($row_produit['prix_depart'], $row_enchere['MAX(montant)'] ?? 0)
]);
?>