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

//si l'user n'est pas connecté il est immédiatement redirigé à la page de connexion.
if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'non_connecte']);
    exit();
}

// Récupération des informations du produit
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

// Calcul du prix actuel et du minimum pour la prochaine enchère (+5%)
$row_produit = $result_produit->fetch_assoc();
$row_enchere = $result_enchere->fetch_assoc();
$enchere_max = max($row_produit['prix_depart'], $row_enchere['MAX(montant)']);
$enchere_min = ceil($enchere_max * 1.05); //ici on arrondit pour eviter un problème dans le form

// Récupération du meilleur enchérisseur actuel
$stmt = $conn->prepare("SELECT utilisateur.nom, enchere.montant FROM utilisateur JOIN enchere ON utilisateur.utilisateur_id = enchere.id_utilisateur WHERE id_produit = ? ORDER BY montant DESC LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_gagnant = $stmt->get_result();
$row_gagnant = $result_gagnant->fetch_assoc();

// Calcule la difference de temps entre le présent et la date de fin de l'enchère et indique si l'enchère a été terminée
$now = new DateTime();
$date_fin = new DateTime($row_produit['date_fin']);
$diff = $now->diff($date_fin);

// Récupération de tout l'historique des enchères
$stmt = $conn->prepare("SELECT *, utilisateur.nom FROM enchere JOIN utilisateur ON enchere.id_utilisateur = utilisateur.utilisateur_id WHERE id_produit = ? ORDER BY montant DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_historique = $stmt->get_result();
$historique = $result_historique->fetch_all(MYSQLI_ASSOC);

// Traitement d'une nouvelle enchère
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents('php://input'), true);
    $montant = $data['montant'];
    $id_user = $_SESSION["id"];
    $date = date('Y-m-d H:i:s');
    // Si moins d'1h restante, prolonge l'enchère d'1h
    if($diff->days == 0 and $diff->h == 0){
        $date_fin->modify('+1 hour');
        $date_fin->setTime($date_fin->format('H'), 0);
        $new_date_fin = $date_fin->format('Y-m-d H:i:s');
        $stmt = $conn->prepare("UPDATE produit SET date_fin = ? WHERE id_produit = ?");
        $stmt->bind_param("si", $new_date_fin, $id);
        $stmt->execute();
    }
    $stmt = $conn->prepare("INSERT INTO enchere(id_utilisateur,id_produit,montant,date) VALUES(?,?,?,?)");
    $stmt->bind_param("iiis", $id_user, $id, $montant, $date);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

echo json_encode([
    'produit' => $row_produit,
    'enchere_max' => $enchere_max,
    'enchere_min' => $enchere_min,
    'gagnant' => $row_gagnant,
    'historique' => $historique,
    'termine' => $date_fin < $now,
    'id_utilisateur_session' => $_SESSION['id'],
    'role' => $_SESSION['role'] ?? null
]);

