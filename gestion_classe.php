<?php
session_start();
include "db.php";

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'delegue' ||
    !isset($_SESSION['gestion_autorisee'])
) {
    die("Accès interdit.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion de la séance</title>

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
    }

    input[type="date"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 6px;
        margin-top: 5px;
    }

    .eleve {
        padding: 10px 0;
    }

    .eleve strong {
        font-size: 1.1em;
        color: #222;
    }

    .eleve label {
        margin-right: 15px;
        cursor: pointer;
        font-weight: normal;
    }

    hr {
        border: none;
        border-top: 1px solid #eee;
        margin: 15px 0;
    }

    button {
        padding: 10px 18px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 15px;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background: #0069d9;
    }

    p {
        text-align: center;
        font-size: 16px;
    }
</style>

</head>
<body>

<h2>Gestion de la séance</h2>

<?php
if (isset($_GET['ok'])) {
    echo "<p style='color:green;'>✅ Séance enregistrée, vous pouvez saisir une nouvelle annotation.</p>";
}
?>

<form method="post" action="enregistrer_seance.php" id="formSeance">

    <label for="date_seance"><strong>Date de la séance :</strong></label>
    <input type="date" name="date_seance" id="date_seance" required>

    <hr>

    <?php
    $eleves = mysqli_query($conn, "SELECT * FROM eleves ORDER BY nom");

    while ($e = mysqli_fetch_assoc($eleves)) {
        $id = $e['id'];
        $nom = htmlspecialchars($e['nom']);
        ?>

        <div class="eleve">
            <strong><?= $nom ?></strong><br>

            <label>
                <input type="checkbox" name="absent[<?= $id ?>]">
                Absent
            </label>

            <label>
                <input type="checkbox" name="cahier[<?= $id ?>]">
                Pas de cahier
            </label>

            <label>
                <input type="checkbox" name="bible[<?= $id ?>]">
                Pas de bible
            </label>
        </div>

        <hr>

    <?php } ?>

    <button type="submit" class="btn btn-success">Enregistrer</button>
</form>

<form action="compte_rendu.php" method="get">
    <button type="submit" class="btn btn-primary">
         Générer le compte rendu
    </button>
</form>

</body>
</html>