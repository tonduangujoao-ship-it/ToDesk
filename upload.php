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
</head>
<body>
    <h1 style="text-align:center;">Upload de fichier DOCX</h1>
    <form method="POST" enctype="multipart/form-data" style="width:400px; margin:auto;">
        <label>Choisir un fichier DOCX :</label><br>
        <input type="file" name="docx_file" accept=".docx"><br><br>
        <input type="submit" name="submit" value="Uploader">
    </form>
    <p style="text-align:center;"><a href="index.php">Retour à la liste des devoirs</a></p>
</body>
</html>
