<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Correction automatique</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
<body class="bg-light">

<div class="container my-5">
    <h2 class="text-center mb-4">Correction Sans ChatGPT </h2>
    <h4 class="text-center mb-4"><span style="color:red;">Comment utiliser : </span>Le correcteur ne fait pas le travail à ta place, 
    il t'affiche les erreurs et tu dois cliquer dessus pour les corriger selon ton besoin. 
    <span style="color:red;">De texte trop grande à éviter</span> et <span style="color:red;">Le Correcteur peut commettre des erreurs</span>.
    </h4>

    <div class="mb-3">
        <label class="form-label">Colle ton texte ici :</label>
        <textarea id="texte" class="form-control" rows="8"></textarea>
    </div>

    <button id="analyser" class="btn btn-primary">Analyser</button>
    <button id="copier" class="btn btn-success ms-2">Copier le texte corrigé</button>

    <hr>

    <h4>Erreurs détectées :</h4>
    <ul id="erreurs" class="list-group"></ul>
</div>

<script>
// Bouton analyser
document.getElementById("analyser").addEventListener("click", () => {

    const texteArea = document.getElementById("texte");
    const texte = texteArea.value;

    if (!texte.trim()) return;

    // Préparation de l'envoi vers LanguageTool
    const data = new URLSearchParams();
    data.append("text", texte);
    data.append("language", "fr");

    fetch("https://api.languagetool.org/v2/check", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: data
    })
    .then(res => res.json())
    .then(result => {
        afficherErreurs(result.matches);
    });
});

// Affichage des erreurs (comme LanguageTool)
function afficherErreurs(matches) {

    const liste = document.getElementById("erreurs");
    const texteArea = document.getElementById("texte");

    liste.innerHTML = "";

    if (matches.length === 0) {
        liste.innerHTML = "<li class='list-group-item text-success'>Aucune erreur trouvée 🎉</li>";
        return;
    }

    // On garde les matches pour recalculer après chaque correction
    matches.forEach(match => {

        const li = document.createElement("li");
        li.className = "list-group-item";

        li.innerHTML = `
            <strong>Erreur :</strong> ${match.message}<br>
            <em>${texteArea.value.substr(match.offset, match.length)}</em>
        `;

        // Propositions de correction
        match.replacements.forEach(rep => {

            const btn = document.createElement("button");
            btn.className = "btn btn-sm btn-outline-primary mt-2 me-2";
            btn.textContent = rep.value;

            // Correction AUTOMATIQUE au clic
            btn.onclick = () => {
                appliquerCorrection(match.offset, match.length, rep.value);
            };

            li.appendChild(btn);
        });

        liste.appendChild(li);
    });
}

// Applique la correction dans le texte
function appliquerCorrection(offset, length, replacement) {

    const texteArea = document.getElementById("texte");
    const texte = texteArea.value;

    texteArea.value =
        texte.substring(0, offset) +
        replacement +
        texte.substring(offset + length);

    // Relancer l'analyse après correction (comme le site officiel)
    document.getElementById("analyser").click();
}

// Copier le texte corrigé
document.getElementById("copier").addEventListener("click", () => {

    const texteArea = document.getElementById("texte");
    texteArea.select();
    document.execCommand("copy");

    alert("Texte corrigé copié ✅");
});
</script>
<div style="position:absolute; height:auto; width:10%; aspect-ratio:1/1; bottom:0; left:0">
    <img src="img/sun.svg" onclick="toggleTheme()" draggable="false" id="themebut" style="height:auto; width:auto;" alt="music">
</div>
</body>
</html>
