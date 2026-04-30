<?php

include "header.php";
include "footer.php";

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

$stmt = $conn->prepare("SELECT * FROM categorie");
$stmt->execute();
$result = $stmt->get_result();

$min_date = date('Y-m-d\TH:i');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"];
    $categorie = $_POST["categorie"];
    $description = $_POST["description"];
    $photo = $_FILES["photo"];
    $db_photo = __DIR__."/temporary/path/" . $photo["name"];
    $prix = $_POST["prix"];
    $date_fin= $_POST["date_fin"];
    $stmt = $conn->prepare("INSERT INTO produit (id_utilisateur, nom, id_categorie, description, photo, prix_depart, date_fin) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isissis", $_SESSION["id"],$nom, $categorie, $description, $db_photo, $prix, $date_fin);
    $stmt->execute();
    echo "<script>window.location.href='mon_espace.php';</script>"; // redirection en Javascript pour éviter un bug causé par le header
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Vente</title>
<body class="background ">
    <div class="form-grid">
        <div class="card">
            <div class="card-body">
                <form class="form" action="vente.php" method="post" enctype="multipart/form-data">
                    <h1 class="pawnstar-font">Mise aux enchères</h1>
                    <div class="form-row">
                        <div class="form-field">
                            <label class="card-text" for="nom">Nom :</label>
                            <input class="form-control form-input" type="text" id="nom" name="nom" placeholder="Nom" required>
                        </div>
                        <div class="form-field">
                            <label class="card-text" for="prix">Prix de départ :</label>
                            <input class="form-control form-input" type="number" id="prix" name="prix" placeholder="Prix" required min="1" step="1">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <label class="card-text" for="categorie">Catégorie :</label>
                            <select class="form-select form-input" name="categorie" id="categorie">
                                <?php while ($row=$result->fetch_assoc()) {
                                echo('<option value="'.$row['id_categorie'].'">'.$row['nom'].'</option>');
                            } ?>
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="card-text" for="date_fin">Date de fin :</label>
                            <input class="form-control form-input" type="datetime-local" id="date_fin" name="date_fin" min="<?php echo $min_date; ?>" required>
                        </div>
                    </div>
                    <div class="form-field">
                        <label class="card-text" for="description">Description :</label>
                        <textarea class="form-control form-input" id="description" name="description" placeholder="Description" required></textarea>
                    </div>
                    <div class="form-field">
                        <label class="card-text" for="photo">Photo :</label>
                        <input class="form-control form-input" type="file" id="photo" name="photo" accept="image/*" required>
                    </div>
                    <button class="button" type="submit">Mettre aux enchères</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
