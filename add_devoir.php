<?php
session_start();
include "db.php";

// Vérifier droit délégué
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'delegue'){
    die(" Accès refusé");
}

if(isset($_POST['submit'])){

    $titre = htmlspecialchars_decode(trim($_POST['titre']), ENT_QUOTES);
    $date_rendu = $_POST['date_rendu'];
    $description = htmlspecialchars_decode(trim($_POST['description']), ENT_QUOTES);

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
<div style="position:absolute; height:auto; width:10%; aspect-ratio:1/1; bottom:0; left:0">
    <img src="img/sun.svg" onclick="toggleTheme()" draggable="false" id="themebut" style="height:auto; width:auto;" alt="music">
</div>
</body>
</html>
