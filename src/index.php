<?php
include "header.php";
$conn = new mysqli(
    $_ENV['MYSQL_HOST'],
    $_ENV['MYSQL_USER'],
    $_ENV['MYSQL_PASSWORD'],
    $_ENV['MYSQL_DATABASE']
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Hub</title>
</head>
<body>

</body>
</html>