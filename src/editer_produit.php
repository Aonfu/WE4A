<?php

session_start();

$page_title = "Editer un produit";

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

$id=$_GET['id'];

//requete pour remplir le formulaire
$stmt = $conn->prepare("SELECT produit.id_produit, produit.nom AS nom, produit.description, produit.photo, produit.date_fin, produit.id_categorie ,GREATEST(COALESCE(MAX(enchere.montant),0), produit.prix_depart) AS montant
FROM produit
LEFT JOIN enchere ON produit.id_produit = enchere.id_produit
WHERE  produit.id_produit = ?
GROUP BY produit.id_produit, produit.nom, produit.description, produit.photo, produit.id_categorie;");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_produit = $stmt->get_result();
$row_produit = $result_produit->fetch_assoc();

// requete pour update le produit dans la bdd
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"];
    $categorie = $_POST["categorie"];
    $description = $_POST["description"];
    $db_photo = $row_produit["photo"];
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === UPLOAD_ERR_OK && $_FILES["photo"]["name"] != '') {
        $photo = $_FILES["photo"];
        $db_photo = "images/" . $photo["name"];
        if (!is_dir(__DIR__ . "/images/")) {
            mkdir(__DIR__ . "/images/", 0755, true);
        }
        move_uploaded_file($photo["tmp_name"], __DIR__ . "/" . $db_photo);
    }
    $stmt = $conn->prepare("UPDATE produit SET nom = ?,id_categorie = ?, description = ?, photo = ? WHERE id_produit = ?");
    $stmt->bind_param("sissi", $nom, $categorie, $description, $db_photo,$id);
    $stmt->execute();
    echo "<script>window.location.href='mon_espace.php';</script>"; // redirection en Javascript pour éviter un bug causé par le header
}

include "header.php";

?>
    <div class="form-grid">
        <div class="card">
            <div class="card-body">
                <form class="form" action="editer_produit.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
                    <h1 class="pawnstar-font">Édition ou Suppression</h1>
                    <div class="form-row">
                        <div class="form-field">
                            <label class="card-text" for="nom">Nom :</label>
                            <input class="form-control form-input" type="text" id="nom" name="nom" placeholder="Nom" value="<?php echo $row_produit['nom']; ?>" required>
                        </div>
                        <div class="form-field">
                            <label class="card-text" for="categorie">Catégorie :</label>
                            <select class="form-select form-input" name="categorie" id="categorie">
                                <?php while ($row=$result->fetch_assoc()) {
                                    $selected = ($row['id_categorie'] == $row_produit['id_categorie'] ) ? 'selected' : '';
                                    echo '<option value="'.$row['id_categorie'].'" '.$selected.'>'.$row['nom'].'</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-field">
                        <label class="card-text" for="description">Description :</label>
                        <textarea class="form-control form-input" id="description" name="description" placeholder="Description" required><?php echo $row_produit['description']; ?></textarea>
                    </div>
                    <div class="form-field">
                        <label class="card-text" for="photo">Photo :</label>
                        <input class="form-control form-input" type="file" id="photo" name="photo" accept="image/*">
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <button class="button" type="submit">Modifier</button>
                        </div>
                        <div class="form-field">
                            <button class="button" type="submit" formaction="supprimer_produit.php?id=<?php echo $id; ?>" onclick="return confirm('Voulez vous vraiment supprimer ce produit ?')">Supprimer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>