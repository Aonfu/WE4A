<?php
date_default_timezone_set('Europe/Paris'); //ligne assez importante pour régler un bug
session_start();
$conn = new mysqli(
        $_ENV['MYSQL_HOST'],
        $_ENV['MYSQL_USER'],
        $_ENV['MYSQL_PASSWORD'],
        $_ENV['MYSQL_DATABASE']
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//recupére tout les élements sur le produits
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

// partie qui calcule le prix minimum de l'enchère qui doit etre 5% plus que le prix actuel (la valeur peut etre modifiée)
$row_produit = $result_produit->fetch_assoc();
$row_enchere = $result_enchere->fetch_assoc();
$enchere_min = max($row_produit['prix_depart'], $row_enchere['MAX(montant)']);
$enchere_min = $enchere_min * 1.05;

// Recupère le nom du gagnant de l'enchère et le montant de son enchère
$stmt = $conn->prepare("SELECT utilisateur.nom, enchere.montant FROM utilisateur JOIN enchere ON utilisateur.utilisateur_id = enchere.id_user WHERE id_produit = ? ORDER BY montant DESC LIMIT 1 ");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_gagnant = $stmt->get_result();
$row_gagnant = $result_gagnant->fetch_assoc();

// Calcule la difference de temps entre le présent et la date de fin de l'enchère et indique si l'enchère a été terminée
$now = new DateTime();
$date_fin = new DateTime($row_produit['date_fin']);
$diff = $now->diff($date_fin);
if ($date_fin < $now) {
    echo '<div class="enchere_over">L\'enchère a été gagnée par ' . $row_gagnant['nom'] . ' pour un montant de ' . $row_gagnant['montant'] . '€</div>';
}

//recupère l'historique des enchères pour l'afficher
$stmt = $conn->prepare("SELECT *, utilisateur.nom FROM enchere JOIN utilisateur ON enchere.id_user = utilisateur.utilisateur_id  WHERE id_produit = ? ORDER BY montant DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_historique = $stmt->get_result();

// Gère le formulaire, s'il reste moins de 1h à l'enchère, la date de fin est modifié et est arrondie à l'heure suppérieure
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $montant=$_POST["montant"];
    $id_user=$_SESSION["id"];
    $date = date('Y-m-d H:i:s');
    if($diff->days == 0 and $diff->h == 0){
        $date_fin->modify('+1 hour');
        $date_fin->setTime($date_fin->format('H'), 0);
        $new_date_fin = $date_fin->format('Y-m-d H:i:s');
        $stmt = $conn->prepare("UPDATE produit SET date_fin = ? WHERE id_produit = ?");
        $stmt->bind_param("si", $new_date_fin, $id);
        $stmt->execute();
    }
    $stmt = $conn->prepare("INSERT INTO enchere(id_user,id_produit,montant,date) VALUES(?,?,?,?)");
    $stmt->bind_param("iiis", $id_user,$id,$montant,$date);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enchère</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <link rel="stylesheet" type="text/css" href="ressources/style/style.css">
</head>
<body>
<nav class="navbar navbar-expand-md navbar-light bg-warning-subtle flex-nowrap">
    <div class="container-fluid d-flex align-items-center">
        <a class="navbar-brand me-3 pawnstar-font" href="index.php">PawnStar?</a>
        <div class="collapse navbar-collapse order-2 order-md-1" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-0 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active pawnstar-font" aria-current="page" href="index.php">Hub</a>
                </li>
                <?php
                if (isset($_SESSION["id"])) {
                    echo
                        '<li class="nav-item">
                            <a class="nav-link pawnstar-font" href="mon_espace.php">Mon Espace</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pawnstar-font" href="vente.php">Vente</a>
                        </li>';
                } else {
                    echo
                        '<li class="nav-item">
                            <a class="nav-link pawnstar-font" href="connexion.php">Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pawnstar-font" href="inscription.php">Inscription</a>
                        </li>';
                }?>
            </ul>
        </div>
        <form class="d-flex order-1 order-md-2" role="search">
            <input class="form-control me-2 search-bar" type="search" placeholder="Search" aria-label="Search"/>
            <button class="btn search-button" type="submit"></button>
        </form>
        <button class="navbar-toggler order-3 ms-auto navbar-burger" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>
<h1>Enchère</h1>

<div class="product">
    <?php echo '<div class="product-element">'.$row_produit['nom'].$row_produit['description'].$row_produit['date_fin'].'</div>'; // faudra aussi echo l'image quand on aura gérer ça?>
</div>

<div class="historique">
    <?php while ($row_historique = $result_historique->fetch_assoc()) {
        $datetime = explode(' ', $row_historique['date']); //sépare la date en deux a l'espace
        echo '<p>' . $row_historique['nom'] . ' a placé une enchère de ' . $row_historique['montant'] . '€ le ' . $datetime[0] . ' à ' . $datetime[1] . '</p>';
    } ?>
</div>

<?php if ($date_fin > $now && $_SESSION["id"] != $row_produit["id_utilisateur"]) { ?>
<div class="enchere-form">
    <form action="enchere.php?id=<?php echo $id; ?>" method="post" >

        <label for="montant">Montant de l'enchère:</label>
        <input type="number" id="montant" name="montant" required min="<?php echo $enchere_min; ?>" step="1">

        <br>

        <button type="submit">Placer l'enchère</button>

    </form>
</div>
<?php } ?>

</body>
</html>