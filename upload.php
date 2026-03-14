<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'delegue'){
    header("Location: index.php");
    exit;
}
?>

<?php
// Fichier upload.php
// Permet de téléverser un fichier DOCX dans le dossier uploads/

if(isset($_POST['submit'])){
    if(isset($_FILES['docx_file']) && $_FILES['docx_file']['error'] == 0){
        $fichier = basename($_FILES['docx_file']['name']); // Nom du fichier
        $target_dir = "uploads/";                          // Dossier de destination
        $target_file = $target_dir . $fichier;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Vérifie que le fichier est un DOCX
        if($fileType == "docx"){
            if(move_uploaded_file($_FILES['docx_file']['tmp_name'], $target_file)){
                echo "<p style='color:green;'>Fichier uploadé avec succès : <a href='$target_file'>$fichier</a></p>";
            } else {
                echo "<p style='color:red;'>Erreur lors de l'upload du fichier.</p>";
            }
        } else {
            echo "<p style='color:red;'>Erreur : seuls les fichiers .docx sont autorisés.</p>";
        }
    } else {
        echo "<p style='color:red;'>Aucun fichier sélectionné.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Upload de DOCX</title>
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
    <h1 style="text-align:center;">Upload de fichier DOCX</h1>
    <form method="POST" enctype="multipart/form-data" style="width:400px; margin:auto;">
        <label>Choisir un fichier DOCX :</label><br>
        <input type="file" name="docx_file" accept=".docx"><br><br>
        <input type="submit" name="submit" value="Uploader">
    </form>
    <p style="text-align:center;"><a href="index.php">Retour à la liste des devoirs</a></p>
<!--<div style="position:absolute; top:3%; right:5%;">
    <img src="img/sun.svg" onclick="toggleTheme()" draggable="false" id="themebut" style="height:auto; width:auto;" alt="music">
</div>-->
</body>
</html>
