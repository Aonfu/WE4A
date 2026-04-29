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

//recupére tout les élements sur le produits
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM produit WHERE id_produit = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_produit = $stmt->get_result();

// Selection l'enchère acutelle (l'enchère avec le plus haut montant)
$stmt = $conn->prepare("SELECT MAX(montant) FROM enchere WHERE id_produit = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_enchere = $stmt->get_result();

// Recupère le nom du gagnant de l'enchère et le montant de son enchère
$stmt = $conn->prepare("SELECT utilisateur.nom, enchere.montant FROM utilisateur JOIN enchere ON utilisateur.utilisateur_id = enchere.id_user WHERE id_produit = ? ORDER BY montant DESC LIMIT 1 ");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_gagnant = $stmt->get_result();
$row_gagnant = $result_gagnant->fetch_assoc();



// partie qui calcule le prix minimum de l'enchère qui doit etre 5% plus que le prix actuel (la valeur peut etre modifiée)
$row_produit = $result_produit->fetch_assoc();
$row_enchere = $result_enchere->fetch_assoc();
$enchere_min = max($row_produit['prix_depart'], $row_enchere['MAX(montant)']);
$enchere_min = $enchere_min * 1.05;

// Calcule la difference de temps entre le présent et la date de fin de l'enchère et indique si l'enchère a été terminée
$now = new DateTime();
$date_fin = new DateTime($row_produit['date_fin']);
$diff = $now->diff($date_fin);

//recupère l'historique des enchères pour l'afficher
$stmt = $conn->prepare("SELECT enchere.montant, utilisateur.nom FROM enchere JOIN utilisateur ON enchere.id_user = utilisateur.utilisateur_id  WHERE id_produit = ? ORDER BY montant DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_historique = $stmt->get_result();

//Préparation d'un tableau PHP historique a retourner en JSON
$historique=[];
while($row_historique = $result_historique->fetch_assoc()){
    $historique[] = $row_historique;
}

//on envoi le JSON
echo json_encode([
    'enchere_min' => $enchere_min,
    'date_fin' => $row_produit['date_fin'],
    'gagnant' => $row_gagnant,
    'historique' => $historique,
    'termine'=> $date_fin<$now,
    'prix_actuel' => max($row_produit['prix_depart'], $row_enchere['MAX(montant)'])]);
?>
