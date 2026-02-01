<?php
session_start();
require_once 'phpword/autoloader.php';

use PhpOffice\PhpWord\IOFactory;

if(!isset($_GET['file'])){
    die("Fichier manquant");
}

$filename = basename($_GET['file']);
$filepath = "uploads/$filename";

if(!file_exists($filepath)){
    die("Fichier introuvable");
}

$phpWord = IOFactory::load($filepath);
$texte = "";

foreach ($phpWord->getSections() as $section) {
    foreach ($section->getElements() as $element) {
        if (method_exists($element, 'getText')) {
            $texte .= $element->getText()."\n";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Conversion</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container my-4">

<h2>Texte extrait du document</h2>
<pre class="border p-3 bg-light"><?= htmlspecialchars($texte) ?></pre>

<a href="index.php" class="btn btn-secondary mt-3">Retour</a>

</body>
</html>
