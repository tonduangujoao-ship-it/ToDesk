<?php
session_start();

// Vérification rôle
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'delegue') {
    die("Accès refusé.");
}

$error = "";

if (isset($_POST['verifier'])) {

    $mdp = $_POST['mdp'];

    // Mot de passe secondaire (temporaire)
    $mdp_secret = "delegueMMI2024";

    if ($mdp === $mdp_secret) {
        $_SESSION['gestion_autorisee'] = true;
        header("Location: gestion_classe.php");
        exit;
    } else {
        $error = "Mot de passe incorrect";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Vérification</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <h3 class="text-center">Accès à la gestion de classe</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" class="text-center">
        <input type="password" name="mdp" class="form-control mb-3"
               placeholder="Mot de passe de sécurité" required>
        <button type="submit" name="verifier" class="btn btn-primary">
            Vérifier
        </button>
    </form>
</div>

</body>
</html>
