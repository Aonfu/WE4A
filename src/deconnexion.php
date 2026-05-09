<?php
//Permet de se déconnecter
session_start();
session_destroy();
header("Location: index.php");
exit();