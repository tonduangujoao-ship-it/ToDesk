<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "sql208.infinityfree.com"; 
$user = "if0_40811600";        // attention aux zéros et aux lettres
$pass = "uxDukTQIGJ";     // mot de passe exact
$db   = "if0_40811600_classeur";    

$conn = mysqli_connect($host, $user, $pass, $db, 3306);

if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}
?>