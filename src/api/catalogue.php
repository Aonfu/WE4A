<?php
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Content-Type: application/json');

session_start();
date_default_timezone_set('Europe/Paris');

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
$result_categorie = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$now = new DateTime();
$now_str = $now->format('Y-m-d H:i:s');

if( isset($_GET['tri']) && $_GET['tri'] == 'prix_desc') {
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    WHERE produit.date_fin > ?
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo ORDER BY montant DESC;");
    $stmt->bind_param("s", $now_str);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

elseif ( isset($_GET['tri']) && $_GET['tri'] == 'prix_asc') {
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    WHERE produit.date_fin > ?
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo ORDER BY montant ASC;");
    $stmt->bind_param("s", $now_str);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

elseif ( isset($_GET['categorie'])) {
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    WHERE produit.id_categorie = ? AND produit.date_fin > ?
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo;");
    $stmt->bind_param("is",$_GET['categorie'], $now_str);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

elseif ( isset($_GET['search'])) {
    $search = '%'.$_GET['search'].'%';
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    WHERE produit.date_fin > ? AND produit.nom LIKE ?
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo;");
    $stmt->bind_param("ss", $now_str, $search);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

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
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

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
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

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
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

else {
    $stmt = $conn->prepare("SELECT produit.id_produit, produit.nom, produit.description, produit.photo, GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
    FROM produit
    LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
    WHERE produit.date_fin > ?
    GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo;");
    $stmt->bind_param("s", $now_str);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

echo json_encode([
    'categories' => $result_categorie,
    'produits' => $data
]);