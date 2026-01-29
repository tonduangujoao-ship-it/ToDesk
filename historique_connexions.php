<?php
session_start();
include "db.php";

// Accès uniquement délégué
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'delegue'){
    die("Accès refusé");
}

// Récupération logs
$sql = "SELECT * FROM logs ORDER BY date_connexion DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des connexions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
    <h1 class="text-center mb-4 text-primary">Historique des connexions</h1>
    <div class="text-center mb-3">
        <a href="index.php" class="btn btn-secondary">Retour</a>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light text-center">
                <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Rôle</th>
                    <th>IP</th>
                    <th>Page visitée</th>
                    <th>Date / Heure</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)){
                        echo "<tr class='text-center'>";
                        echo "<td>".$row['id']."</td>";
                        echo "<td>".htmlspecialchars($row['user'])."</td>";
                        echo "<td>".htmlspecialchars($row['role'])."</td>";
                        echo "<td>".htmlspecialchars($row['ip'])."</td>";
                        echo "<td>".htmlspecialchars($row['page'])."</td>";
                        echo "<td>".htmlspecialchars($row['date_connexion'])."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Aucun log disponible</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
