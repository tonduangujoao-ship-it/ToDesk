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
    <title>ToDesk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="CSS/core.css" rel="stylesheet">
    <style>
        .bt{ position:relative; left: 4%;}
        .logo { display:block; margin:auto; width:180px; border-radius:10px; }
        .logo2 { display:block; margin-left:5px; width:180px; border-radius:10px; position:absolute; top:-15%; }
        .Cp{text-align:center; position:relative; bottom: 0; color: red; }


        .urgent-row td{
            background-color: #ff4b4b; /* jaune clair */
            font-weight: bold;
            
            overflow: hidden; 
            animation: softShake 0.6s infinite ease-in-out;
            transform: scale(1);
            transition: 0.2s ease;
            cursor: pointer;

        }
      
        
        .table-rounded {
            border: 4px solid #000000;
            display: inline-block;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 20px rgba(0,0,0,0.25);

        }


        @keyframes softShake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-0.7px); }
            50% { transform: translateX(0.7px); }
            75% { transform: translateX(-0.7px); }
            100% { transform: translateX(0); }
    
        }

        .tr {
            background-color: #0a1a33;
            color: white;             
        }
       
    </style>
    
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

    <h1 class="text-center mb-2 text-primary texttheme">ToDesk</h1>
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
        <table class="table table-bordered table-hover table-rounded">
            <thead class="text-center tr">
                <tr class="tr">
                    <th class="tr">Titre</th>
                    <th class="tr">Date de rendu</th>
                    <th class="tr">Description</th>
                    <th class="tr">Fichier</th>
                    <th class="tr">Exemple</th>
                    <?php if ($role == 'delegue') echo "<th class='tr'>Actions</th>"; ?>
                </tr>
            </thead>

            <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                $index = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    // Ajouter une classe spéciale aux 3 premiers devoirs
                   $highlight = ($index < 3) ? "urgent-row" : "";
                   echo "<tr class='$highlight'>";
                   $index++;

                   echo "<td class='tr'>" . htmlspecialchars($row['titre']) . "</td>";
                   echo "<td class='tr'>" . htmlspecialchars($row['date_rendu']) . "</td>";
                   echo "<td class='tr'>" . htmlspecialchars($row['description']) . "</td>";


                    // Colonne Fichier
                    echo "<td class='text-center tr'>";
                    if (!empty($row['fichier'])) {
                        echo "<a href='uploads/" . htmlspecialchars($row['fichier']) . "' 
                                target='_blank' 
                                class='btn btn-outline-secondary btn-sm'>Voir</a>";
                    } else {
                        echo "Aucun fichier";
                    }
                    echo "</td>";

                    // Colonne Exemple
                    echo "<td class='text-center tr'>";
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
                        echo "<td class='text-center tr'>
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

<!--<div style="position:absolute; top:3%; right:5%;">
    <img src="img/sun.svg" onclick="toggleTheme()" draggable="false" id="themebut" style="height:auto; width:auto;" alt="music">
</div>-->

<?php if ($role === 'delegue'): ?>
    <a href="verif_gestion.php" class="btn btn-warning mt-3 bt">
        Gestion de classe
    </a>
<?php endif; ?>

<h6 class="Cp">© Copyright Joao Tonduangu 2025</h6>


</body>
</html>

