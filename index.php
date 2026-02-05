<?php
session_start();
include "db.php";

// --------------------
//  LOG DES VISITES : Pour voir qui se connecte
// --------------------
if (isset($_SESSION['user'])) {
    $user = mysqli_real_escape_string($conn, $_SESSION['user']);
    $role = mysqli_real_escape_string($conn, $_SESSION['role']);
} else {
    $user = 'invité';
    $role = 'aucun';
}

$ip = $_SERVER['REMOTE_ADDR'];
$page = 'index.php';

// Enregistrement dans la table logs
mysqli_query($conn, "INSERT INTO logs(user, role, ip, page) VALUES ('$user', '$role', '$ip', '$page')");

// --------------------
// Récupération des devoirs
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
        .logo { display:block; margin:auto; width:180px; border-radius:10px; }
        .logo2 { display:block; margin-left:5px; width:180px; border-radius:10px; position:absolute; top:-15%; }
    </style>
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

<body style="background-color: white;">

<header>
    <img src="sorbonne.png" alt="Logo Sorbonne" class="logo">
    <img src="logo.svg" alt="logo" class="logo2">
</header>

<div class="container my-4">

    <!-- Bouton de connexion -->
    <div class="d-flex justify-content-end mb-3">
        <?php
        if (isset($_SESSION['user'])) {
            echo "<span class='me-3'>Connecté en tant que <strong>"
                . htmlspecialchars($_SESSION['user'])
                . "</strong> (" . htmlspecialchars($_SESSION['role']) . ")</span>";
            echo "<a href='logout.php' class='btn btn-outline-danger btn-sm'>Déconnexion</a>";
        } else {
            echo "<a href='login.php' class='btn btn-outline-primary btn-sm'>Connexion</a>";
        }
        ?>
    </div>

    <h1 class="text-center mb-2 text-primary texttheme">Classeur MMI</h1>
    <h4 class="text-center mb-4 texttheme">Made By Joao Tonduangu</h4>
    <h5 class="text-center mb-4 texttheme">
        En poursuivant votre navigation sur ce site, vous acceptez l’utilisation de traceurs pour vous permettre l'utilisation de ToDesk
        <span style="color:red;"><br>Connectez-vous pour plus d'options</span>
    </h5>

    <!-- Boutons selon rôle -->
    <?php
    if ($role == 'eleve') {
        echo "
        <div class='d-flex justify-content-between mn-3'>
            <a href='corriger.php' class='btn btn-secondary'>Corriger un texte</a>
            <a href='historique_connexions.php' class='btn btn-info'>Historique des connexions</a>
        </div>";
    }

    if ($role == 'delegue') {
        echo "
        <div class='d-flex justify-content-between mn-3'>
            <a href='corriger.php' class='btn btn-secondary'>Corriger un texte</a>
            <a href='add_devoir.php' class='btn btn-success'>Ajouter un devoir</a>
            <a href='historique_connexions.php' class='btn btn-info'>Historique des connexions</a>
        </div>";
    }
    ?>

    <!-- Tableau des devoirs -->
    <div class="table-responsive mt-4">
        <table class="table table-bordered table-hover">
            <thead class="table-light text-center">
                <tr>
                    <th class="texttheme">Titre</th>
                    <th class="texttheme">Date de rendu</th>
                    <th class="texttheme">Description</th>
                    <th class="texttheme">Fichier</th>
                    <th class="texttheme">Exemple</th>
                    <?php if ($role == 'delegue') echo "<th>Actions</th>"; ?>
                </tr>
            </thead>

            <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {

                    echo "<tr>";

                    echo "<td>" . htmlspecialchars($row['titre']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_rendu']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";

                    // Colonne Fichier
                    echo "<td class='text-center'>";
                    if (!empty($row['fichier'])) {
                        echo "<a href='uploads/" . htmlspecialchars($row['fichier']) . "' 
                                target='_blank' 
                                class='btn btn-outline-secondary btn-sm'>Voir</a>";
                    } else {
                        echo "Aucun fichier";
                    }
                    echo "</td>";

                    // Colonne Exemple
                    echo "<td class='text-center'>";
                    if (!empty($row['exemple'])) {
                        echo "<a href='uploads/" . htmlspecialchars($row['exemple']) . "' 
                                target='_blank' 
                                class='btn btn-outline-secondary btn-sm'>Voir</a>";
                    } else {
                        echo "Aucun exemple";
                    }
                    echo "</td>";

                    // Actions délégué
                    if ($role == 'delegue') {
                        echo "<td class='text-center'>
                                <a href='edit.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm me-1'>Modifier</a>
                                <a href='delete.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm'
                                   onclick=\"return confirm('Supprimer ce devoir ?');\">Supprimer</a>
                              </td>";
                    }

                    echo "</tr>";
                }
            } else {
                $colspan = ($role == 'delegue') ? 6 : 5;
                echo "<tr><td colspan='$colspan' class='text-center'>Aucun devoir disponible</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<div style="position:absolute; height:auto; width:10%; aspect-ratio:1/1; bottom:0; left:0">
    <img src="img/sun.svg" onclick="toggleTheme()" draggable="false" id="themebut" style="height:auto; width:auto;" alt="music">
</div> 

</body>
</html>

