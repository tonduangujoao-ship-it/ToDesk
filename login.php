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
<div style="position:absolute; height:auto; width:10%; aspect-ratio:1/1; bottom:0; left:0">
    <img src="img/sun.svg" onclick="toggleTheme()" draggable="false" id="themebut" style="height:auto; width:auto;" alt="music">
</div>
</body>
</






