<?php  
session_start();
include "db.php";

// --------------------
//  LOG DES VISITES : Pour voi qui se connectée 
// --------------------
//le site verifie si on se connectée , si oui il prend notre nom notre role
if(isset($_SESSION['user'])){
    $user = mysqli_real_escape_string($conn, $_SESSION['user']); 
    $role = mysqli_real_escape_string($conn, $_SESSION['role']);
//sinon il affiche juste invité et aucun dans la page de log
} else {
    $user = 'invité';
    $role = 'aucun';
}

$ip = $_SERVER['REMOTE_ADDR'];
$page = 'index.php';

// Enregistrement dans la table logs
mysqli_query($conn, "INSERT INTO logs(user, role, ip, page) VALUES ('$user', '$role', '$ip', '$page')");

// --------------------
// Récupération des devoirs : permet de trier les devoirs pars date de randu.
// --------------------
$sql = "SELECT * FROM devoirs ORDER BY date_rendu ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Classeur de Devoirs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .logo{ display:block; margin:auto; width:180px; border-radius:10px; }
        .logo2{ display:block; margin-left:5px; width:180px; border-radius:10px; position:absolute; top:-15%; }
    </style>
</head>
<body>

<!-- Logo -->
<header>
    <img src="sorbonne.png" alt="Logo Sorbonne" class="logo">
    <img src="logo.svg" alt="logo" class="logo2">
</header>

<div class="container my-4">

    <!-- Bouton de connexion : il varie selon notre stauts , connecté ou non connexté -->
    <div class="d-flex justify-content-end mb-3">
        <?php
        if(isset($_SESSION['user'])){
            echo "<span class='me-3'>Connecté en tant que <strong>"
                . htmlspecialchars($_SESSION['user'])
                . "</strong> (" . htmlspecialchars($_SESSION['role']) . ")</span>";
            echo "<a href='logout.php' class='btn btn-outline-danger btn-sm'>Déconnexion</a>";
        } else {
            echo "<a href='login.php' class='btn btn-outline-primary btn-sm'>Connexion</a>";
        }
        ?>
    </div>

    <h1 class="text-center mb-2 text-primary">Classeur MMI</h1>
    <h4 class="text-center mb-4">Made By Joao Tonduangu</h4>
    <h5 class="text-center mb-4">En poursuivant votre navigation sur ce site, vous acceptez l’utilisation de traceurs pour vous permettre l'utilisation de ToDesk<span style="color:red;"><br>Connectez-vous pour plus d'options</span></h5>

    <!-- BOUTON CORRIGER AU-DESSUS DU TABLEAU , Listes de Correction , et Ajouter devoirs -->
    <?php 
    if(isset($role) && ($role == 'eleve')){
        echo "<div class='d-flex justify-content-between mn-3'>
                <a href='corriger.php' class='btn btn-secondary'>Corriger un texte</a>
                <a href='historique_connexions.php' class='btn btn-info'>Historique des connexions</a>
              </div>";
    }
    if(isset($role) && ($role == 'delegue')){
        echo "<div class='d-flex justify-content-between mn-3'>
                <a href='corriger.php' class='btn btn-secondary'>Corriger un texte</a>
                <a href='add_devoir.php' class='btn btn-success'>Ajouter un devoir</a>
                <a href='historique_connexions.php' class='btn btn-info'>Historique des connexions</a>
              </div>";
    }
    ?>
    <!--Tableau d'affichade de devoie -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light text-center">
                <tr>
                    <th>Titre</th>
                    <th>Date de rendu</th>
                    <th>Description</th>
                    <th>Fichier</th>
                    <?php if(isset($role) && $role == 'delegue') echo "<th>Actions</th>"; ?>
                </tr>
            </thead>
            <tbody>
            <?php
            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){
                    echo "<tr>";
                    echo "<td>".htmlspecialchars($row['titre'])."</td>";
                    echo "<td class='text-center'>".htmlspecialchars($row['date_rendu'])."</td>";
                    echo "<td>".htmlspecialchars($row['description'])."</td>";

                    echo "<td class='text-center'>";
                    if(!empty($row['fichier'])){
                        echo "<a href='uploads/".htmlspecialchars($row['fichier'])."' 
                                target='_blank' 
                                class='btn btn-outline-secondary btn-sm'>Voir</a>";
                    } else {
                        echo "Aucun fichier";
                    }
                    echo "</td>";

                    // Actions pour le délégué
                    if(isset($role) && $role == 'delegue'){
                        echo "<td class='text-center'>
                                <a href='edit.php?id=".$row['id']."' class='btn btn-warning btn-sm me-1'>Modifier</a>
                                <a href='delete.php?id=".$row['id']."' class='btn btn-danger btn-sm' 
                                   onclick=\"return confirm('Supprimer ce devoir ?');\">Supprimer</a>
                              </td>";
                    }

                    echo "</tr>";
                }
            } else {
                $colspan = (isset($role) && $role == 'delegue') ? 5 : 4;
                echo "<tr><td colspan='$colspan' class='text-center'>Aucun devoir disponible</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

