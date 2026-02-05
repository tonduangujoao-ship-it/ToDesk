<?php
session_start();
include "db.php";

// Vérifier droit délégué
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'delegue'){
    die(" Accès refusé");
}

if(isset($_POST['submit'])){

    $titre = trim($_POST['titre']);
    $date_rendu = $_POST['date_rendu'];
    $description = trim($_POST['description']);

    // Gestion fichier
    $fichier = "";

    if(isset($_FILES['fichier']) && $_FILES['fichier']['error'] == 0){

        $fichier = time() . "_" . basename($_FILES["fichier"]["name"]);
        move_uploaded_file($_FILES["fichier"]["tmp_name"], "uploads/" . $fichier);
    }
    $exemple= "";

    if(isset($_FILES['fichier']) && $_FILES['exemple']['error'] == 0){

        $exemple = time() . "_" . basename($_FILES["exemple"]["name"]);
        move_uploaded_file($_FILES["exemple"]["tmp_name"], "uploads/" . $exemple);
    }

    $sql = "INSERT INTO devoirs (titre, date_rendu, description, fichier, exemple)
            VALUES ('$titre', '$date_rendu', '$description', '$fichier', '$exemple')";

    mysqli_query($conn, $sql);

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Ajouter un devoir</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container mt-4">

<h2 class="text-primary text-center mb-4">➕ Ajouter un devoir</h2>

<a href="index.php" class="btn btn-secondary mb-3">⬅ Retour</a>

<form method="post" enctype="multipart/form-data" class="card p-4 shadow">

    <label class="form-label">Titre</label>
    <input type="text" name="titre" class="form-control" required>

    <label class="form-label mt-3">Date de rendu</label>
    <input type="date" name="date_rendu" class="form-control" required>

    <label class="form-label mt-3">Description</label>
    <textarea name="description" class="form-control" rows="4"></textarea>

    <label class="form-label mt-3">Joindre un fichier</label>
    <input type="file" name="fichier" class="form-control">

     <label class="form-label mt-3">Joindre un exemple</label>
    <input type="file" name="exemple" class="form-control">

    <button type="submit" name="submit" class="btn btn-success mt-4">
        ✔ Enregistrer le devoir
    </button>

</form>

</div>
</body>
</html>
