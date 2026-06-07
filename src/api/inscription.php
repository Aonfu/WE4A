<?php
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

session_start();

$data = json_decode(file_get_contents('php://input'), true);
$nom = $data['nom'];
$prenom = $data['prenom'];
$email = $data['email'];
$mdp = $data['mdp'];

// Hashage du mot de passe
$mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);

$conn = new mysqli(
    $_ENV['MYSQL_HOST'],
    $_ENV['MYSQL_USER'],
    $_ENV['MYSQL_PASSWORD'],
    $_ENV['MYSQL_DATABASE']
);

// Vérifie si l'email est déjà utilisé par un autre compte
$stmt = $conn->prepare("SELECT * FROM utilisateur WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Cet email est déjà utilisé !']);
} else {
    $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, email, mdp) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nom, $prenom, $email, $mdp_hash);
    $stmt->execute();
    echo json_encode(['success' => true]);
}