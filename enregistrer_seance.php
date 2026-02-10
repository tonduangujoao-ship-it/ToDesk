<?php
session_start();
include "db.php";

if (!isset($_SESSION['gestion_autorisee'])) {
    die("Accès interdit");
}

if (!isset($_POST['date_seance'])) {
    die("Données manquantes");
}

$date = $_POST['date_seance'];

//  Créer la séance
mysqli_query($conn, "INSERT INTO seances (date_seance) VALUES ('$date')");
$seance_id = mysqli_insert_id($conn);

//  Récupérer les tableaux (ou tableaux vides)
$absents = $_POST['absent'] ?? [];
$cahiers = $_POST['cahier'] ?? [];
$bibles  = $_POST['bible'] ?? [];

// Pour chaque élève
$eleves = mysqli_query($conn, "SELECT id FROM eleves");
while ($e = mysqli_fetch_assoc($eleves)) {

    $id = $e['id'];

    $absent = isset($absents[$id]) ? 1 : 0;
    $pas_cahier = isset($cahiers[$id]) ? 1 : 0;
    $pas_bible  = isset($bibles[$id]) ? 1 : 0;

    mysqli_query($conn, "INSERT INTO gestion_classe 
        (eleve_id, seance_id, absent, pas_cahier, pas_bible)
        VALUES ($id, $seance_id, $absent, $pas_cahier, $pas_bible)");
}

//  REDIRECTION OBLIGATOIRE
header("Location: gestion_classe.php?ok=1");
exit();
