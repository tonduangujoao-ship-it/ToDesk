<?php
include "db.php";

$mois = $_GET['mois'] ?? date('m');
$annee = $_GET['annee'] ?? date('Y');

$sql = "
SELECT 
    e.nom,
    SUM(g.absent) AS absences,
    SUM(g.pas_cahier) AS cahier,
    SUM(g.pas_bible) AS bible
FROM gestion_classe g
JOIN eleves e ON g.eleve_id = e.id
JOIN seances s ON g.seance_id = s.id
WHERE MONTH(s.date_seance) = $mois
AND YEAR(s.date_seance) = $annee
GROUP BY e.id
ORDER BY e.nom
";

$resultat = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Compte rendu</title>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #f5f7fa;
        margin: 0;
        padding: 20px;
    }

    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 25px;
    }

    form {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        max-width: 700px;
        margin: 20px auto;
    }

    label {
        font-weight: bold;
        color: #444;
        margin-right: 10px;
    }

    input[type="number"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 6px;
        width: 80px;
    }

    button {
        padding: 10px 18px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 15px;
        background: #007bff;
        color: white;
    }

    button:hover {
        background: #0069d9;
    }

    table {
        width: 90%;
        margin: 20px auto;
        border-collapse: collapse;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    th {
        background: #007bff;
        color: white;
        padding: 12px;
        text-align: left;
    }

    td {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    tr:hover {
        background: #f1f5ff;
    }

    a button {
        background: #555;
    }

    a button:hover {
        background: #333;
    }

    /* --- SWITCH MODERNES POUR LES CHECKBOXES --- */
    .switch {
        position: relative;
        display: inline-block;
        width: 45px;
        height: 22px;
        margin-right: 8px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .3s;
        border-radius: 22px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #28a745;
    }

    input:checked + .slider:before {
        transform: translateX(23px);
    }
</style>

</head>
<body>

<h2>📊 Compte rendu mensuel</h2>

<form method="get">
    <label>Mois :</label>
    <input type="number" name="mois" min="1" max="12" value="<?= $mois ?>">

    <label>Année :</label>
    <input type="number" name="annee" value="<?= $annee ?>">

    <button type="submit">Afficher</button>
</form>

<table>
    <tr>
        <th>Élève</th>
        <th>Absences</th>
        <th>Sans cahier</th>
        <th>Sans bible</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($resultat)) { ?>
        <tr>
            <td><?= htmlspecialchars($row['nom']) ?></td>
            <td><?= $row['absences'] ?? 0 ?></td>
            <td><?= $row['cahier'] ?? 0 ?></td>
            <td><?= $row['bible'] ?? 0 ?></td>
        </tr>
    <?php } ?>
</table>

<br>

<a href="javascript:history.back()">
    <button>⬅ Retour</button>
</a>

</body>
</html>