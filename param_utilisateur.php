<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>SpotHifi: Gestion compte</title>
	<link rel="stylesheet" href="styles.css?v=1.0">
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

function modifier($param, $js, $e)
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
		$type = "password";
		echo("<label>Ancien $param<br><input type=$type name='a_$param' required='required'></input></label><br>");
	}
	if($param == 'mail')
		$contrainte = "pattern='[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$'";
	echo("<label>Nouveau $param<br><input type='text' name='n_$param' required='required' $contrainte></input></label><br>");
	echo("<div class='confirmation'><button type='submit'>Confirmer</button>");
	echo("<button type='button' onclick='afficher_form('$param')'>Annuler</button></div>");
	echo("</form>");
}

function afficher_param_base($js, $e_pseudo, $e_mail, $e_mdp)
{
	$img_modif = 'img/pen-solid.svg';
	/*photo de profil*/
	echo("<div id='fond_profile'></div>");
	echo("<div id='profile'>");
	afficher_img_profil($_SESSION['photo'], null, "140", "100", null);
	echo("<label onclick='modifier_photo()'> Modifier votre photo de profil<img class='modif' src=$img_modif alt='modifier'></label>");
	echo('</div><br>');

	/*Modification informations*/
	echo("<div id='modif_infos'>");
	echo("<h1>Informations du compte</h1><hr><br>");
	
	/*pseudo*/
	echo("<div class='modif'>");
	echo("<label onclick='afficher_form(0)'>Pseudo");
	echo("<img src=$img_modif alt='modifier'></label><br>");
	echo($_SESSION['pseudo']);
	modifier('pseudo', $js, $e_pseudo);
	echo('</div><br>');

	/*adresse mail*/
	echo("<div class='modif'>");
	echo("<label onclick='afficher_form(1)'>Adresse mail");
	echo("<img src=$img_modif alt='modifier'></label>");
	
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
    echo('</div><br>');

	/*mot de passe*/
	echo("<div class='modif'>");
	echo("<label onclick='afficher_form(2)'>Mot de passe");
	echo("<img src=$img_modif alt='modifier'></label><br>");
	modifier('mdp', $js, $e_mdp);
	echo('</div><br>');
}

function afficher_param_normal()
{
	echo("<form class='modif' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
	echo("<h1>Statut</h1><hr><br>");
	echo("<label>Passer en compte artiste : <button type='submit'>Oui</button></form><input type='text' class='fonction' name='veux_artiste' value='oui'></label>");
	echo("</div>"); /*div modif_info (fond noir)*/
}

function afficher_param_artiste($js)
{
	echo("<form class='modif' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
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

	/*affichage de la page*/
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
