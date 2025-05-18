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
	echo("<a id='profile'>");
	afficher_img_profil($_SESSION['photo'], null, "140", "100", null);
	echo("<img class='logo' src='img/pen-solid.svg'>");
	echo("<label onclick='modifier_photo()'> Modifier votre photo de profil<img class='modif' src=$img_modif alt='modifier'></label>");
	echo('</a><br>');

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
	echo("<h1>Statut</h1><hr><br>");
	echo("<form class='modif' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
	echo("<label>Passer en compte artiste : <button type='submit'>Oui</button></form><input type='text' class='fonction' name='veux_artiste' value='oui'></label>");
	echo("</div>"); /*div modif_info (fond noir)*/
}

function afficher_param_artiste($e_musique)
{
	echo("<form class='modif' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
	echo("<h1>Statut</h1><hr><br>");
	echo("<label>Passer en compte lambda : <button type='submit'>Oui</button></form><input type='text' class='fonction' name='veux_lambda' value='oui'></label>");
	echo("</form>");

	/*Gestion Musique*/
	$pdo = connex("spothifi");

	if(isset($_POST['0id']))
	{
		for($i=0; $i<$_POST['nb_musique']; $i++)
		{
				modifier_musique($pdo,"nom", $_POST[$i."id"], $_POST[$i."nom"]);
				modifier_musique($pdo, 'description', $_POST[$i."id"], $_POST[$i."description"]);
				modifier_musique($pdo, 'lien', $_POST[$i."id"], $_POST[$i."lien"]);
		}
	}

	echo("<form class='modif' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
	echo("<h1>Vos musiques</h1><hr><br>");
	$nb_musique= afficher_musiques_artiste($pdo, $_SESSION['id']);
	echo("<button type='submit'>Confirmer</button>");
	echo("</form>");

	/*Insérer Musique*/
	echo("<form class='modif' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
	echo("<h2>Ajout musique</h2><hr><br>");
	echo("<label>Nom<input type='text' name = 'nom_m' required='required'></label><br>");
	echo("<label>Lien vidéo<input type='text' name = 'lien_m' required='required'></label><br>");
	echo("<label>duree (hh:mm:ss)<input type='text' name = 'duree_m' required='required' pattern='\d\d:\d\d:\d\d'></label><br>");
	echo("<label>Description<textarea name='description_m' placeholder='Rythmé et dansante un plaisir'></textarea></label><br>");
	echo("<label>Paroles<textarea name='paroles_m' placeholder='Never gonna give you up, never gonna let you down [...]'></textarea></label><br>");
	echo $e_musique;
	echo("<button type='submit'>Confirmer</button>");
	echo("</form>");

	/*--Suppression--*/
	echo("<form class='modif' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
	echo("<h2>Suppression musique</h2>");
	echo("<label>Nom<input type='text' name='s_m_id' required='required'></label>");
	echo("<button type='submit'>Confirmer</button>");
	echo("</form>");
	echo("</div>");

	$pdo = null;
}

function afficher_param_admin()
{
	$pdo = connex("spothifi");

	/*--Affichage formulaire--*/ 
	echo("<h1>Gestion des membres</h1><hr><br>");
	echo("<form class='modif' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
	echo("<h2>Recherche</h2>");
	echo("<label>Pseudo<input type='text' name='val'></label>");
	echo("<label>Colonne filtre<select name='colonne'>
		<option value='id'>id</option>
		<option value='pseudo'>pseudo</option>
		<option value='date_naissance'>date de naissance</option>
		<option value='adresse_mail'>mail</option>
		<option value='statut'>statut</option></select></label>");
	echo("<label>Voulez-vous que ce soit par ordre décroissant ?<br>(si oui écrivez oui)<input type='text' name='croissant' pattern='oui'></label>");
	echo("<label>Nombre de résultats<input type='text' name='nb_res' pattern='[1-9][0-9]*'></label>");
	echo("<button type='submit';'>Confirmer</button>");
	echo("</form>");

	if(isset($_POST['val']))
	{
		afficher_membres($pdo, $_POST['val'], $_POST['colonne'], $_POST['nb_res'], $_POST['croissant']);
	}
	/*--modification--*/
	echo("<form class='modif' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
	echo("<h2>Modification</h2>");
	echo("<label>id<input type='text' name='id' required='required' pattern='[1-9][0-9]*'></label>");
	echo("<label>Valeur à modifier<select name='colonne'>
		<option value='pseudo'>pseudo</option>
		<option value='statut'>statut</option></select></label>");
	echo("<label>Valeur<input type='text' name='n_val' required='required'></label>");
	echo("<button type='submit'>Confirmer</button>");
	echo("</form>");

	if(isset($_POST['id']) && isset($_POST['n_val']))		
		modifier_utilisateur($pdo, $_POST['colonne'], $_POST['id'], $_POST['n_val'], 1);

	/*--Suppression--*/
	echo("<form class='modif' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
	echo("<h2>Suppression</h2>");
	echo("<label>id<input type='text' name='s_id' required='required' pattern='[1-9][0-9]*'></label>");
	echo("<button type='submit'>Confirmer</button>");
	echo("</form>");

	if(isset($_POST['s_id']))
		supprimer_donnee($pdo, 'utilisateur', $_POST['s_id']);

	$pdo = null;
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
		$e_pseudo = modifier_utilisateur($pdo,"pseudo", $_SESSION['id'], $_POST['n_pseudo'], null);
	
	//changement mail
	else if(isset($_POST['n_mail']))
		$e_mail = modifier_utilisateur($pdo, "adresse_mail", $_SESSION['id'], $_POST['n_mail'], null);
	
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
			$e_mdp = modifier_utilisateur($pdo, "mdp", $_SESSION['id'], $n_mdp, null);
	}
	
	//changement statut
	else if(isset($_POST['veux_artiste']))
	{		
		modifier_utilisateur($pdo, "statut", $_SESSION['id'], 1, null);
		$pdo=null;
	}
	else if (isset($_POST['veux_lambda'])) 
	{
		modifier_utilisateur($pdo, "statut", $_SESSION['id'], 0, null);
	}
	//artiste : insertion musique
	else if(isset($_POST['nom_m']))
	{
		$e_musique = inserer_musique($pdo, $_SESSION['id'], $_POST['nom_m'], $_POST['duree_m'],date("Y-m-d"), 'rock', $_POST['paroles_m'], $_POST['description_m'], $_POST['lien_m']);
		$_POST = array();
	}
	//artiste : suppression musique
	else if(isset($_POST['s_m_id']))
		supprimer_donnee_musique($pdo, $_SESSION['id'], $_POST['s_m_id']);
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
			afficher_param_admin();
			break;
	}
}
creation_footer();
?>
