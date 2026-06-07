<?php
// Page de suppression : supprime d'abord les enchères liées au produit
// puis le produit lui-même, puis redirige vers mon_espace.php
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

session_start();
date_default_timezone_set('Europe/Paris'); //ligne importante pour régler un bug

$conn = new mysqli(
    $_ENV['MYSQL_HOST'],
    $_ENV['MYSQL_USER'],
    $_ENV['MYSQL_PASSWORD'],
    $_ENV['MYSQL_DATABASE']
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

$stmt = $conn->prepare("DELETE FROM enchere WHERE id_produit = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$stmt = $conn->prepare("DELETE FROM produit WHERE id_produit = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode(['success' => true]);