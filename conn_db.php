<?php
// config.php

$host = "localhost";
$user = "root";
$password = "";
$dbname = "facture_db";

// Connexion à la base de données
$conn = mysqli_connect($host, $user, $password, $dbname);

// Vérifier la connexion
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}
?>
