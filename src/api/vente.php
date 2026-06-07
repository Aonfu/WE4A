<?php
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Methods: POST');
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

// Récupération de toutes les catégories pour le menu déroulant
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $stmt = $conn->prepare("SELECT * FROM categorie");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['categories' => $result]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"];
    $categorie = $_POST["categorie"];
    $description = $_POST["description"];
    $photo = $_FILES["photo"];
    // Upload de la photo : création du dossier si inexistant et déplacement du fichier
    $db_photo = "images/" . $photo["name"];  // chemin relatif pour la BDD
    if (!is_dir(__DIR__ . "/../images/")) {
        mkdir(__DIR__ . "/../images/", 0755, true);
    }
    move_uploaded_file($photo["tmp_name"], __DIR__ . "/../" . $db_photo);
    $prix = $_POST["prix"];
    $date_fin = $_POST["date_fin"];
    $stmt = $conn->prepare("INSERT INTO produit (id_utilisateur, nom, id_categorie, description, photo, prix_depart, date_fin) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isissis", $_SESSION["id"], $nom, $categorie, $description, $db_photo, $prix, $date_fin);
    $stmt->execute();
    // Redirection vers l'espace personnel après création
    echo json_encode(['success' => true]);
    exit();
}