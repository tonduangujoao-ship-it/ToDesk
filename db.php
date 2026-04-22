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


//Accès à la base de données via PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie à la base de données.";
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>