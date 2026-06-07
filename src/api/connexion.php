<?php
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

session_start();

$conn = new mysqli(
    $_ENV['MYSQL_HOST'],
    $_ENV['MYSQL_USER'],
    $_ENV['MYSQL_PASSWORD'],
    $_ENV['MYSQL_DATABASE']
);

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'];
$mdp = $data['mdp'];

// Recherche de l'utilisateur par son email
$stmt = $conn->prepare("SELECT * FROM utilisateur WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

// Vérification du mot de passe avec password_verify()
if ($result and password_verify($mdp, $result['mdp'])) {
    $_SESSION["id"] = $result["utilisateur_id"];
    $_SESSION["role"] = $result["role"];
    echo json_encode(['success' => true, 'role' => $result['role']]);
} else {
    // Message d'erreur
    echo json_encode(['success' => false, 'error' => 'Mot de passe ou Email invalide']);
}