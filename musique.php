!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>SpotHifi: Musique</title>
        <link rel="stylesheet" href="styles.css?v=1.0">
    </head>
    <body>
        <header>
            <br><br><br>
            <h1>Informations sur la musique</h1>
            <hr>
            <br>
        </header>
        
    </body>
</html>

<?php
session_start();
include('fonctions.php');
include('connex.inc.php');
$pdo = connex("spothifi");

creation_nav(isset($_SESSION['pseudo']));

if (isset($_GET["id_musique"]))
{
    $id_musique = htmlspecialchars($_GET["id_musique"]);
    $lien = recup_donnee_table_id($pdo, "lien", "musique", $id_musique);
    $nom = recup_donnee_table_id($pdo, "nom", "musique", $id_musique);
    $date_creation = recup_donnee_table_id($pdo, "date_creation", "musique", $id_musique);
    $nb_like = recup_donnee_table_id($pdo, "nb_like", "musique", $id_musique);
    $description = recup_donnee_table_id($pdo, "description", "musique", $id_musique);
    $paroles = recup_donnee_table_id($pdo, "paroles", "musique", $id_musique);
    
    echo("<div id='page_musique'>");
    echo("<div id='video'><iframe src='$lien' frameborder='0' allow='picture-in-picture' allowfullscreen></iframe><br>");
    echo("<a href='$lien' target='_blank'>Cliquez ici, si la vidéo ne s'affiche pas</a>");
    echo("<h1>$nom</h1>");
    echo("<p>Crée le $date_creation<br>Nombre de like : $nb_like<br></p>");
    echo("<h1>Description</h1><hr><br>");
    echo("<p id='description'>$description</p>");
    echo("<h1>Paroles</h1><hr><br>");
    echo("<p id='paroles'>$paroles</p>");
}
else
{
    echo("<h1>ERREUR : 418 I'm a teapot</h1>");
}


echo("</div>");


creation_footer();
?>