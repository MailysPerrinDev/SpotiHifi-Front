<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>SpotHifi: Gestion compte</title>
	<link rel="stylesheet" href="styles.css">
	<script langage="javascript">
	  function testJavascript() {
	    // test pour valider que Javascript fonctionne
	    if (! document.getElementById)
	    {
	       <?php $js= 1; ?>
	    }
	  }
	  testJavascript();
	</script>
	<script src="script.js"></script>
</head>
<body>
	
</body>
</html>

<?php
session_start();
include('fonctions.php');
include('connex.inc.php');

creation_nav(isset($_SESSION['pseudo']));
if(!isset($js))
	$js = 0;

function modifier($param, $js, e)
{
	if($js)//si javscript ou pas
		$class = 'fonction';
	else
		$class = 'modif';
	$contrainte = null;
	$type = "text";
	
	echo("<form class='$class' id=$param action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
	if($param == 'mdp')
	{
		$type = "password
		echo("<label>Ancien $param<br><input type=$type name='a_$param' required='required'></input></label><br>");
	}
	if($param == 'mail')
		$contrainte = "pattern='[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$'";
	echo("<label>Nouveau $param<br><input type='$type' name='n_$param' required='required' $contrainte></input></label><br>");
	echo("<button type='submit'>Confirmer</button>");
	echo("<button type='button'>Annuler</button>");
	echo("</form>");
}

function afficher_param_base($js, $e_pseudo, $e_mail, $e_mdp)
{
	$img_modif = 'kuhik';
	/*photo de profil*/
	echo("<div id='fond_profile'></div>");
	echo("<div id='profile'>");
	afficher_img_profil($_SESSION['img_profil'], null, "140", "100", null);
	echo("<button type='button' class='modif' onclick='modifier_photo()'>");
	echo("<img href=$img_modif alt='modifier'></button>");
	echo('</div><br>');

	/*Modification informations*/
	echo("<div id='modif_infos'>");
	
	/*pseudo*/
	echo($_SESSION['pseudo']);
	echo("<button type='button' class='modif' onclick='afficher_form(0)'>");
	echo("<img href=$img_modif alt='modifier'></button><br>");
	modifier('pseudo', $js, $e_pseudo);
	echo('<br>');

	/*adresse mail*/
	echo("<label>Adresse mail");
	echo("<button type='button' class='modif' onclick='afficher_form(1)'>");
	echo("<img href=$img_modif alt='modifier'></button></label>");
	
	//recup du mail
	$pdo = connex("spothifi");
	try
    {
        $stmt = $pdo->prepare("SELECT adresse_mail FROM utilisateur WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['id']);
        $stmt->execute();

        echo ("<br>".$stmt->fetch()[0]."<br>");

        $stmt->closeCursor();
        $pdo=null;
    }
    catch(PDOException $e)
    {
        echo '<p>Problème PDO</p>';
        echo $e->getMessage();
    } 
    modifier('mail', $js, $e_mail);
    echo('<br>');

	/*mot de passe*/
	echo("<label>Mot de passe");
	echo("<button type='button' class='modif' onclick='afficher_form(2)'>");
	echo("<img href=$img_modif alt='modifier'></button></label><br>");
	modifier('mdp', $js, e_mdp);
	echo('<br>');
}

function afficher_param_normal()
{
	echo("<form action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
	echo("<label>Passer en compte artiste : <button type='submit'>Oui</button></form><input type='text' class='fonction' name='veux_artiste' value='oui'></label>");
	echo("</div>"); /*div modif_info (fond noir)*/
}

function afficher_param_artiste($js)
{
	echo("<form action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
	echo("<label>Passer en compte lambda : <button type='submit'>Oui</button></form><input type='text' class='fonction' name='veux_lambda' value='oui'></label>");
	echo("</div>");
}

function afficher_param_admin($js)
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
	$e_pseudo = null;
	$e_mail = null;
	$e_mdp = null;

	/*--Changement paramètres--*/

	/*modification des données et récupération des erreurs*/

	$pdo = connex("spothifi");

	//changement pseudo
	if(isset($_POST['n_pseudo']))
		$e_pseudo = modifier_utilisateur($pdo,"pseudo", $_SESSION['id'], $_POST['n_pseudo']);

	//changement mail
	else if(isset($_POST['n_mail']))
		$e_mail = modifier_utilisateur($pdo, "adresse_mail", $_SESSION['id'], $_POST['n_mail']);

	//changement mot de passe
	else if(isset($_POST['n_mdp']))
	{
		$a_mdp = password_hash(md5($_POST['a_mdp']), PASSWORD_BCRYPT);
		$n_mdp = password_hash(md5($_POST['n_mdp']), PASSWORD_BCRYPT);

		//faut vérifier si bon mot de passe
		$stmt = $pdo->prepare("SELECT mdp FROM utilisateur WHERE pseudo = :pseudo");
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->execute();

        $hachage = $stmt->fetch()[0];
        if ($stmt->rowCount() == 0 || !(password_verify($a_mdp, $hachage)))
            $e_mdp = "Votre mot de passe n'est pas bon";
		else
			$e_mdp = modifier_utilisateur($pdo, "mdp", $_SESSION['id'], $n_mdp);
	}
	//changement statut
	else if(isset($_POST['veux_artiste']))
	{		
		modifier_utilisateur($pdo, "statut", $_SESSION['id'], 1);
		$pdo=null;
	}
	else if (isset($_POST['veux_lambda'])) 
	{
		modifier_utilisateur($pdo, "statut", $_SESSION['id'], 0);
	}
	$pdo=null;

	/*--Affichage de la page--*/

	afficher_param_base($js, $e_pseudo, $e_mail, $e_mdp);
	switch($_SESSION['statut'])
	{
		case 0:
			afficher_param_normal();
			break;
		case 1:
			afficher_param_artiste($js);  
			break;
		case 2:
			afficher_param_admin($js);
			break;
	}
}
creation_footer();
?>
