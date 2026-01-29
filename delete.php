<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'delegue'){
    header("Location: index.php");
    exit;
}
?>

<?php
include "db.php";

// Vérifie qu'un id est envoyé
if(isset($_GET['id']) && !empty($_GET['id'])){
    $id = intval($_GET['id']); // sécurité : entier seulement

    // Supprime le devoir correspondant
    $sql = "DELETE FROM devoirs WHERE id=$id LIMIT 1";
    mysqli_query($conn, $sql);
}

// Retour à la page index
header("Location: index.php");
exit();
?>
