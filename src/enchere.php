<?php


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

include "header.php";
include "footer.php";

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
$enchere_max = max($row_produit['prix_depart'], $row_enchere['MAX(montant)']);
$enchere_min = ceil($enchere_max * 1.05); //ici on arrondit pour eviter un problème dans le form

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
    <title>Enchère</title>
</head>
<body>
<div class="centrer">
    <div class="enchere-grid">
        <div class="main-column">
            <div class="card">
                <div class="card-images">
                    <img src="ressources/img/adiren.jpg" class="card-img-top" alt="...">
                    <img src="ressources/img/scotch.png" class="scotch-1" alt="...">
                    <img src="ressources/img/scotch.png" class="scotch-2" alt="...">
                </div>
                <div class="card-body">
                    <h1 class="card-title"><?php echo $row_produit['nom']; ?></h1>
                    <p><?php echo $row_produit['description']; ?></p>
                    <h1 class="card-title pawnstar-font" id="prix">$<?php echo $enchere_max; ?></h1>
                </div>
            </div>
        </div>
        <div class="side-column">
            <?php if ($date_fin > $now && $_SESSION["id"] != $row_produit["id_utilisateur"]) { ?>
                <div class="card">
                    <div class="card-body">
                        <form class="form" action="enchere.php?id=<?php echo $id; ?>" method="post" id="form">
                            <h1 class="pawnstar-font">Placer Enchère</h1>
                            <?php echo '<p class="card-text">L\'Enchère finit le :</p>
                                        <h1 id="date_fin">' . $row_produit['date_fin'] . '</h1>'; ?>
                            <label class="card-text" for="montant">Montant de l'enchère :</label>
                            <input class="form-input" type="number" id="montant" name="montant" required min="<?php echo $enchere_min; ?>" placeholder="Min : $<?php echo $enchere_min; ?>">
                            <button class="button" type="submit">Placer l'enchère</button>
                        </form>
                    </div>
                </div>
            <?php } ?>
            <div class="card">
                <h1 class="pawnstar-font text-center">Historique</h1>
                <?php if ($result_historique->num_rows > 0) { ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Nom</th>
                                <th scope="col">Montant</th>
                            </tr>
                        </thead>
                        <tbody id="historique">
                            <?php while ($row_historique = $result_historique->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $row_historique['nom']; ?></td>
                                    <td><?php echo $row_historique['montant']; ?></td>
                                </tr>
                            <?php }?>
                        </tbody>
                    </table>
                <?php } else {
                    echo '<p id="historique" class="card-text text-center">Aucune enchère n\'a encore été placée</p>';
                } ?>
            </div>
        </div>
    </div>
</div>


<?php /*
<h1>Enchère</h1>
<div class="product-element">
    <h2><?php echo $row_produit['nom']; ?></h2>
    <p><?php echo $row_produit['description']; ?></p>
    <p id="date_fin"><?php echo 'L\'Enchère finit le: '.$row_produit['date_fin']; ?></p>
</div>

<div id="historique" class="historique">
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
<?php } */ ?>

<script>
    // Requette AJAX pour garder les information a jours
    setInterval(function () {
        fetch('get_data_enchere.php?id=<?php echo $id; ?>')
            .then(response => response.json())
            .then(data => {
                document.getElementById('date_fin').innerHTML = data.date_fin;
                document.getElementById('montant').min = data.enchere_min;
                document.getElementById('montant').placeholder = data.enchere_min;
                document.getElementById('prix').innerHTML = '$'+data.prix_actuel;
                let html = '';
                data.historique.forEach(function(row){
                    html += '<tr> <td>'  + row.nom + ' </td> <td>' + row.montant + ' </td> </tr>'
                })
                document.getElementById('historique').innerHTML = html
                if (data.termine) {
                    document.getElementById('form').style.display = 'none';
                }
            });
    },5000);
</script>
</body>
</html>