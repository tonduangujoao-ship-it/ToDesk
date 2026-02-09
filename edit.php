<?php
session_start();
include "db.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'delegue'){
    die("Accès refusé");
}

if(!isset($_GET['id'])){
    die("ID manquant");
}

$id = intval($_GET['id']);

// Charger devoir
$stmt = $conn->prepare("SELECT * FROM devoirs WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$devoir = $stmt->get_result()->fetch_assoc();

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

    $stmt = $conn->prepare("UPDATE devoirs SET fichier='' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

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

    $stmt = $conn->prepare("UPDATE devoirs SET exemple='' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

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

    // Requête préparée
    $stmt = $conn->prepare("
        UPDATE devoirs
        SET titre=?, date_rendu=?, description=?, fichier=?, exemple=?
        WHERE id=?
    ");

    $stmt->bind_param("sssssi", $titre, $date_rendu, $description, $fichier, $exemple, $id);
    $stmt->execute();

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
</head>
<script>
window.addEventListener('DOMContentLoaded', function() {
  const theme = localStorage.getItem("theme") || "light";

  applyTheme(theme);
});

function toggleTheme() {
  const currentTheme = localStorage.getItem("theme") || "light";
  const newTheme = (currentTheme === "dark") ? "light" : "dark";

  localStorage.setItem("theme", newTheme);
  applyTheme(newTheme);
}

// applique le theme global
function applyTheme(theme) {

  const themeBtn = document.getElementById("themebut");

  // Sécurité : taille fixe du bouton (évite l’icône géante)
  themeBtn.style.width = "60px";
  themeBtn.style.height = "60px";
  themeBtn.style.cursor = "pointer";

  if (theme === "dark") {
    document.body.style.backgroundColor = "#1e1e2f";
    document.body.style.color = "#ffffff";
    themeBtn.src = "img/moon.svg";
    applyThemeToClass("dark");
  } else {
    document.body.style.backgroundColor = "#ffffff";
    document.body.style.color = "#000000";
    themeBtn.src = "img/sun.svg";
    applyThemeToClass("light");
  }
}

// applique le theme aux éléments qui ont une certaine class
function applyThemeToClass(theme) {
  const elements = document.querySelectorAll(".texttheme");

  elements.forEach(element => {
    if (theme === "dark") {
      element.style.color = "#ffffff";
      element.style.backgroundColor = "transparent";
    } else {
      element.style.color = "#000000";
      element.style.backgroundColor = "transparent";
    }
  });
}
</script>


<body>
<div class="container mt-4 texttheme">

<h2 class="text-warning text-center mb-4 texttheme">✏ Modifier le devoir</h2>

<a href="index.php" class="btn btn-secondary mb-3">⬅ Retour</a>

<form method="post" enctype="multipart/form-data" class="card p-4 shadow texttheme">

    <label class="form-label texttheme">Titre</label>
    <input type="text" name="titre" class="form-control texttheme" value="<?= $devoir['titre'] ?>" required>

    <label class="form-label mt-3 texttheme">Date de rendu</label>
    <input type="date" name="date_rendu" class="form-control texttheme" value="<?= $devoir['date_rendu'] ?>" required>

    <label class="form-label mt-3 texttheme">Description</label>
    <textarea name="description" class="form-control texttheme" rows="4"><?= $devoir['description'] ?></textarea>

    <!-- FICHIER -->
    <p class="mt-3 texttheme">
        Fichier actuel :
        <?php if($devoir['fichier']){ ?>
            <a href="uploads/<?= $devoir['fichier'] ?>" target="_blank"><?= $devoir['fichier'] ?></a>

            <button type="submit" name="delete_fichier" class="btn btn-danger btn-sm ms-2"
                onclick="return confirm('Supprimer le fichier actuel ?')">
                Supprimer
            </button>
        <?php } else { echo "Aucun fichier"; } ?>
    </p>

    <label class="form-label texttheme">Nouveau fichier (optionnel)</label>
    <input type="file" name="fichier" class="form-control texttheme">

    <!-- EXEMPLE -->
    <p class="mt-3 texttheme">
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
    <input type="file" name="exemple" class="form-control texttheme">

    <button type="submit" name="update" class="btn btn-warning mt-4">
        ✔ Enregistrer les modifications
    </button>

</form>

</div>

<div style="position:absolute; top:3%; right:5%;">
    <img src="img/sun.svg" onclick="toggleTheme()" draggable="false" id="themebut" style="height:auto; width:auto;" alt="music">
</div>
</body>
</html>