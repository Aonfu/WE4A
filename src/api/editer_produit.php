<?php
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');
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

$id = $_GET['id'];

// Récupération de toutes les catégories pour le select
$stmt = $conn->prepare("SELECT * FROM categorie");
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$min_date = date('Y-m-d\TH:i');

// Récupération des informations du produit à éditer
$stmt = $conn->prepare("SELECT produit.id_produit, produit.nom AS nom, produit.description, produit.photo, produit.date_fin, produit.id_categorie ,GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
FROM produit
LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
WHERE  produit.id_produit = ?
GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo, produit.id_categorie;");
$stmt->bind_param("i", $id);
$stmt->execute();
$row_produit = $stmt->get_result()->fetch_assoc();

// Traitement du formulaire de modification
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"];
    $categorie = $_POST["categorie"];
    $description = $_POST["description"];
    // Garde l'ancienne photo par défaut
    $db_photo = $row_produit["photo"];
    // Si la photo a été modifiée on prend la nouvelle
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === UPLOAD_ERR_OK && $_FILES["photo"]["name"] != '') {
        $photo = $_FILES["photo"];
        $db_photo = "images/" . $photo["name"];
        if (!is_dir(__DIR__ . "/../images/")) {
            mkdir(__DIR__ . "/../images/", 0755, true);
        }
        move_uploaded_file($photo["tmp_name"], __DIR__ . "/../" . $db_photo);
    }
    // Mise à jour du produit dans la BDD
    $stmt = $conn->prepare("UPDATE produit SET nom = ?,id_categorie = ?, description = ?, photo = ? WHERE id_produit = ?");
    $stmt->bind_param("sissi", $nom, $categorie, $description, $db_photo, $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

echo json_encode([
    'produit' => $row_produit,
    'categories' => $categories,
    'min_date' => $min_date
]);