<?php
session_start();
include "db.php";

if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    //  Requête préparée pour sécurité minimale
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if(!$result){
        die("Erreur SQL : " . $conn->error);
    }

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();

        //  Vérification STRICTE du mot de passe
        if($password === $user['password']){
            // Enregistre la session
            //$_SESSION['user_id']=$user['user_id'];
            $_SESSION['user'] = $user['username'];
            $_SESSION['role'] = trim(strtolower($user['role']));

            // Redirection vers index
            header("Location: index.php");
            exit();
        } else {
            $msg = "Utilisateur ou mot de passe incorrect";
        }

    } else {
        $msg = "Utilisateur ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Connexion</title>
</head>
<body>

<h2>Connexion</h2>

<form method="post">

Nom d'utilisateur :
<input type="text" name="username" required><br><br>

Mot de passe :
<input type="password" name="password" required><br><br>

<button type="submit" name="login">Se connecter</button>

</form>

<?php
if(isset($msg)) echo "<p style='color:red;'>$msg</p>";
?>

</body>
</






