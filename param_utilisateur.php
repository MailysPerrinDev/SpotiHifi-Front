<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>SpotHifi: Gestion compte</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
	
</body>
</html>

<?php
session_start();
include('fonctions.php');

function modifier($param)
{
	echo("<form class='modif'>");
	if($param == 'mot de passe')
	{
		echo("<label>Ancien $param<br><input type='text' name='a_$param' required='required'></label><br>");
	}
	echo("<label>Nouveau $param<br><input type='text' name='n_$param' required='required'></label><br>");
	echo("<button type='submit'>Confirmer</button>");
	echo("<button type='button'>Annuler</button>");
	echo("</form>");
}

function afficher_param_normaux()
{
	/*photo de profil*/
	echo("<div>");
	afficher_img_profil($_SESSION['img_profil'], null, "140", "100", null);
	echo("<button type='button' class='modif' onclick='modifier_photo()'><img href='' alt='modifier'></button>");
	echo('</div><br>');

	/*pseudo*/
	echo('<div>');
	echo($_SESSION['pseudo']);
	echo("<button type='button' class='modif' onclick='modifier('pseudo')'><img href='' alt='modifier'></button>");
	echo('</div><br>');

	/*adresse mail*/
	echo('<div>');
	echo("<label>Adresse mail");
	echo("<button type='button' class='modif' onclick='modifier('adresse mail')'><img href='' alt='modifier'></button></label>");
	
	//recup du mail
	include('connex.inc.php');
	$pdo = connex("spothifi");
	try
    {
        $stmt = $pdo->prepare("SELECT adresse_mail FROM utilisateur WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['id']);
        $stmt->execute();

        echo ("<br>".$stmt->fetch()[0]);

        $stmt->closeCursor();
        $pdo=null;
    }
    catch(PDOException $e)
    {
        echo '<p>Problème PDO</p>';
        echo $e->getMessage();
    } 
    echo('</div><br>');

	/*mot de passe*/
	echo('<div>');
	echo("<label>Mot de passe");
	echo("<button type='button' class='modif' onclick='modifier('mot de passe')'><img href='' alt='modifier'></button></label>");
	echo('</div><br>');
}

function afficher_param_artiste()
{
	/*à venir*/
}

function afficher_param_admin()
{
	/*à venir*/
}


if(!isset($_SESSION['pseudo']))
{
	echo("<p>");
	echo("<span>Vous vous êtes perdu :O<br>Veuillez vous connecter pour accéder aux options de cette page. Le bouton suivant vous mettra directement sur la page d'accès :</span><br>");
	echo("<button type='button'><a href='connexion.php'>Connexion</a></button>");
	echo("</p>");
	
	echo("<p>");
	echo("<span>Sinon veuillez retourner à la page d'accueil :</span><br>");
	echo("<button type='button'><a href='index.html'>Home</a></button>");
	echo("</p>");
}
else
{
	afficher_param_normaux();
	switch($_SESSION['statut'])
	{
		case 1:
			afficher_param_artiste();  
			break;
		case 2:
			afficher_param_admin();
			break;
	}
}
?>