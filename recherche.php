<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>SpotHifi: Recherche</title>
        <link rel="stylesheet" href="styles.css?v=1.0">
    </head>
    <body>
        
    </body>
</html>

<?php
session_start();
include('fonctions.php');
include('connex.inc.php');

$pdo = connex("spothifi");
$recherche = $_POST['recherche'];

creation_nav(isset($_SESSION['pseudo']));
creation_recherche($pdo, $recherche);
echo("<h1>Résultat");
if (isset($recherche) && !($resultat == ''))
{
    echo(" pour : ".$recherche);
}
echo("...</h1><hr>");

if ($recherche == '' || !isset($recherche))
{
    echo("<p>Aucun résultat.</p>");
}

creation_footer();
?>