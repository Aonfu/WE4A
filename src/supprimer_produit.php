<?php

// Page de suppression : supprime d'abord les enchères liées au produit
// puis le produit lui-même, puis redirige vers mon_espace.php

session_start();

$page_title = "Supprimer un produit";

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

$id=$_GET['id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("DELETE FROM enchere WHERE id_produit = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt = $conn->prepare("DELETE FROM produit WHERE id_produit = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>window.location.href='mon_espace.php';</script>";
}

include "header.php";
include "footer.php";
?>
