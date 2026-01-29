<?php
session_start();
include "db.php";

// protection : seulement admin/délégué
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'delegue'){
    die("Accès refusé");
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users(username, password, role) VALUES('$username', '$password', '$role')";
    mysqli_query($conn, $sql);
    echo "Utilisateur créé avec succès !";
}
?>
<form method="POST">
    Identifiant : <input type="text" name="username" required><br>
    Mot de passe : <input type="password" name="password" required><br>
    Rôle :
    <select name="role">
        <option value="eleve">Élève</option>
        <option value="delegue">Délégué</option>
        <option value="admin">Admin</option>
    </select>
    <button type="submit">Créer</button>
</form>
