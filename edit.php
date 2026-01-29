<?php
session_start();
include "db.php";

// Vérifier droit délégué
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'delegue'){
    die(" Accès refusé");
}

if(!isset($_GET['id'])){
    die("ID manquant");
}

$id = intval($_GET['id']);

// Charger devoir
$sql = "SELECT * FROM devoirs WHERE id=$id LIMIT 1";
$result = mysqli_query($conn, $sql);
$devoir = mysqli_fetch_assoc($result);

if(!$devoir){
    die("Devoir introuvable");
}

// Si formulaire envoyé
if(isset($_POST['update'])){

    $titre = trim($_POST['titre']);
    $date_rendu = $_POST['date_rendu'];
    $description = trim($_POST['description']);
    $fichier = $devoir['fichier'];

    // Nouveau fichier ?
    if(isset($_FILES['fichier']) && $_FILES['fichier']['error'] == 0){

        $fichier = time()."_".basename($_FILES["fichier"]["name"]);
        move_uploaded_file($_FILES["fichier"]["tmp_name"], "uploads/".$fichier);
    }

    $sql = "UPDATE devoirs SET 
            titre='$titre',
            date_rendu='$date_rendu',
            description='$description',
            fichier='$fichier'
            WHERE id=$id";

    mysqli_query($conn, $sql);

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Modifier un devoir</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container mt-4">

<h2 class="text-warning text-center mb-4">✏ Modifier le devoir</h2>

<a href="index.php" class="btn btn-secondary mb-3">⬅ Retour</a>

<form method="post" enctype="multipart/form-data" class="card p-4 shadow">

    <label class="form-label">Titre</label>
    <input type="text" name="titre" class="form-control" value="<?= $devoir['titre'] ?>" required>

    <label class="form-label mt-3">Date de rendu</label>
    <input type="date" name="date_rendu" class="form-control" value="<?= $devoir['date_rendu'] ?>" required>

    <label class="form-label mt-3">Description</label>
    <textarea name="description" class="form-control" rows="4"><?= $devoir['description'] ?></textarea>

    <p class="mt-3">
        Fichier actuel :
        <?php
        if($devoir['fichier']){
            echo "<a href='uploads/".$devoir['fichier']."' target='_blank'>".$devoir['fichier']."</a>";
        } else {
            echo "Aucun fichier";
        }
        ?>
    </p>

    <label class="form-label">Nouveau fichier (optionnel)</label>
    <input type="file" name="fichier" class="form-control">

    <button type="submit" name="update" class="btn btn-warning mt-4">
        ✔ Enregistrer les modifications
    </button>

</form>

</div>
</body>
</html>

