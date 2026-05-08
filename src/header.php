<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="..." crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="ressources/style/style.css">
    <?php if (isset($page_title)) {
        echo '<title>' . $page_title . '</title>';
    } ?>
</head>
<body class="background">
    <nav class="navbar navbar-expand-md navbar-light bg-warning-subtle flex-nowrap">
        <div class="container-fluid d-flex align-items-center">
            <a class="navbar-brand me-3 pawnstar-font" href="catalogue.php">
                <img class="logo" src="ressources/img/logo.png" alt="logo">
            </a>
            <div class="collapse navbar-collapse order-2 order-md-1" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-0 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active pawnstar-font" aria-current="page" href="catalogue.php">Catalogue</a>
                    </li>
                    <?php
                    if (isset($_SESSION["id"])) {
                        echo
                        '<li class="nav-item">
                                <a class="nav-link pawnstar-font" href="mon_espace.php">Mon Espace</a>
                            </li>
                        <li class="nav-item"> 
                            <a class="nav-link pawnstar-font" href="vente.php">Vente</a>
                        </li>
                        <li class="nav-item"> 
                            <a class="nav-link pawnstar-font" href="deconnexion.php">Déconnexion</a>
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
            <form  action = "catalogue.php?search=" class="d-flex order-1 order-md-2" role="search" method="get">
                <input class="form-control me-2 search-bar" type="search" placeholder="Search" aria-label="Search" name="search"/>
                <button class="btn search-button" type="submit"></button>
            </form>
            <button class="navbar-toggler order-3 ms-auto navbar-burger" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>