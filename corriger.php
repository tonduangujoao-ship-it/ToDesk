<?php
session_start();
include "db.php"; // tu peux garder ça si tu veux logger l'utilisateur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Correction automatique</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <h2 class="text-center mb-4">Correction Sans ChatGPT</h2>

    <div class="mb-3">
        <label class="form-label">Colle ton texte ici :</label>
        <textarea id="texte" class="form-control" rows="8"></textarea>
    </div>

    <button id="corrigerBtn" class="btn btn-primary mb-3">Corriger</button>
    <button id="copierBtn" class="btn btn-success mb-3">Copier le texte corrigé</button>

    <div id="resultat" class="mt-4"></div>
</div>

<script>
document.getElementById('corrigerBtn').addEventListener('click', async () => {
    const texte = document.getElementById('texte').value.trim();
    if(!texte) return alert('Merci de coller un texte à corriger !');

    // Préparer les données pour LanguageTool
    const data = new URLSearchParams();
    data.append('text', texte);
    data.append('language', 'fr');

    try {
        const response = await fetch('https://api.languagetool.org/v2/check', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: data
        });
        const result = await response.json();

        let corrected = texte;
        if(result.matches && result.matches.length > 0){
            // on applique les corrections de la fin vers le début pour garder les offsets corrects
            result.matches.reverse().forEach(match => {
                if(match.replacements && match.replacements.length > 0){
                    const replacement = match.replacements[0].value;
                    const offset = match.offset;
                    const length = match.length;
                    corrected = corrected.slice(0, offset) + replacement + corrected.slice(offset + length);
                }
            });
        }

        // afficher le texte corrigé
        document.getElementById('resultat').innerHTML = `
            <h4>Texte corrigé :</h4>
            <div class="border p-3 bg-white">${corrected.replace(/\n/g,'<br>')}</div>
        `;

        // mettre à jour le textarea pour pouvoir copier directement
        document.getElementById('texte').value = corrected;

    } catch (err) {
        alert('Erreur lors de la correction : ' + err.message);
    }
});

// Copier le texte corrigé dans le presse-papier
document.getElementById('copierBtn').addEventListener('click', () => {
    const texte = document.getElementById('texte').value;
    navigator.clipboard.writeText(texte)
        .then(() => alert('Texte copié !'))
        .catch(() => alert('Impossible de copier le texte.'));
});
</script>

</body>
</html>
