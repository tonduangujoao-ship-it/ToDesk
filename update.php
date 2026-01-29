<?php
include "db.php";

$id = intval($_POST['id']);
$titre = $_POST['titre'];
$date_rendu = $_POST['date_rendu'];
$description = $_POST['description'];

$sql = "UPDATE devoirs 
        SET titre='$titre', date_rendu='$date_rendu', description='$description' 
        WHERE id=$id LIMIT 1";
mysqli_query($conn, $sql);

header("Location: index.php");
exit();
?>
