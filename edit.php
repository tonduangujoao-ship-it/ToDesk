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

/* ----------------------------------------------------
   Suppression du fichier
---------------------------------------------------- */
if(isset($_POST['delete_fichier'])){
    if($devoir['fichier'] && file_exists("uploads/" . $devoir['fichier'])){
        unlink("uploads/" . $devoir['fichier']);
    }
    mysqli_query($conn, "UPDATE devoirs SET fichier='' WHERE id=$id");
    header("Location: edit.php?id=$id");
    exit();
}

/* ----------------------------------------------------
   Suppression de l'exemple
---------------------------------------------------- */
if(isset($_POST['delete_exemple'])){
    if($devoir['exemple'] && file_exists("uploads/" . $devoir['exemple'])){
        unlink("uploads/" . $devoir['exemple']);
    }
    mysqli_query($conn, "UPDATE devoirs SET exemple='' WHERE id=$id");
    header("Location: edit.php?id=$id");
    exit();
}

/* ----------------------------------------------------
   Mise à jour du devoir
---------------------------------------------------- */
if(isset($_POST['update'])){

    $titre = trim($_POST['titre']);
    $date_rendu = $_POST['date_rendu'];
    $description = trim($_POST['description']);

    // Valeurs actuelles
    $fichier = $devoir['fichier'];
    $exemple = $devoir['exemple'];

    // Nouveau fichier ?
    if(isset($_FILES['fichier']) && $_FILES['fichier']['error'] == 0){
        $fichier = time() . "_" . basename($_FILES["fichier"]["name"]);
        move_uploaded_file($_FILES["fichier"]["tmp_name"], "uploads/" . $fichier);
    }

    // Nouvel exemple ?
    if(isset($_FILES['exemple']) && $_FILES['exemple']['error'] == 0){
        $exemple = time() . "_" . basename($_FILES["exemple"]["name"]);
        move_uploaded_file($_FILES["exemple"]["tmp_name"], "uploads/" . $exemple);
    }

    // Requête SQL
    $sql = "UPDATE devoirs SET 
            titre='$titre',
            date_rendu='$date_rendu',
            description='$description',
            fichier='$fichier',
            exemple='$exemple'
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
   <!-- JS pour changer de theme-->
        <script>
window.addEventListener('DOMContentLoaded', function() {
  const theme = localStorage.getItem("theme");
  
  if (theme === "dark") {
    document.body.style.backgroundColor = "black";
    document.getElementById("themebut").src = "img/moon.svg";
    applyThemeToClass("dark");
  } else {
    document.body.style.backgroundColor = "white";
    document.getElementById("themebut").src = "img/sun.svg";
    applyThemeToClass("light");
  }
});

function toggleTheme() {
  const currentTheme = localStorage.getItem("theme");
  
  if (currentTheme === "dark") {
    localStorage.setItem("theme", "light");
    document.body.style.backgroundColor = "#ffffff";
    document.getElementById("themebut").src = "img/sun.svg";
    applyThemeToClass("light");
  } else {
    localStorage.setItem("theme", "dark");
    document.body.style.backgroundColor = "#222233";
    document.getElementById("themebut").src = "img/moon.svg";
    applyThemeToClass("dark");
  }
}

// applique le theme aux éléments qui ont une certaine class
function applyThemeToClass(theme) {
  const elements = document.querySelectorAll(".texttheme");
  
  elements.forEach(element => {
    if (theme === "dark") {
      element.style.color = "white"; 
      element.style.backgroundColor = "#222233"; 
    } else {
      element.style.color = "black"; 
      element.style.backgroundColor = "#ffffff"; 
    }
  });
}
</script>
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

    <!-- FICHIER -->
    <p class="mt-3">
        Fichier actuel :
        <?php if($devoir['fichier']){ ?>
            <a href="uploads/<?= $devoir['fichier'] ?>" target="_blank"><?= $devoir['fichier'] ?></a>

            <button type="submit" name="delete_fichier" class="btn btn-danger btn-sm ms-2"
                onclick="return confirm('Supprimer le fichier actuel ?')">
                Supprimer
            </button>
        <?php } else { echo "Aucun fichier"; } ?>
    </p>

    <label class="form-label">Nouveau fichier (optionnel)</label>
    <input type="file" name="fichier" class="form-control">

    <!-- EXEMPLE -->
    <p class="mt-3">
        Exemple actuel :
        <?php if($devoir['exemple']){ ?>
            <a href="uploads/<?= $devoir['exemple'] ?>" target="_blank"><?= $devoir['exemple'] ?></a>

            <button type="submit" name="delete_exemple" class="btn btn-danger btn-sm ms-2"
                onclick="return confirm('Supprimer l\'exemple actuel ?')">
                Supprimer
            </button>
        <?php } else { echo "Aucun fichier"; } ?>
    </p>

    <label class="form-label">Nouvel Exemple (optionnel)</label>
    <input type="file" name="exemple" class="form-control">

    <button type="submit" name="update" class="btn btn-warning mt-4">
        ✔ Enregistrer les modifications
    </button>

</form>

</div>
<div style="position:absolute; height:auto; width:10%; aspect-ratio:1/1; bottom:0; left:0">
    <img src="img/sun.svg" onclick="toggleTheme()" draggable="false" id="themebut" style="height:auto; width:auto;" alt="music">
</div>
</body>
</html>