<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>SpotHifi: Inscription</title>
        <link rel="stylesheet" href="styles.css?v=1.0">
    </head>
    <body>
        
    </body>
</html>

<?php
session_start();
include('fonctions.php');

creation_nav();
echo ("<div class='bloc_inscription'>");
echo ("<h1>Inscription</h1><br><hr><br>");

function afficheFormulaire($p, $a, $m, $j, $mail, $e_pseudo, $e_date, $e_mail)
{
    echo("<form action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
    echo("<label>Pseudo <input type='text' name='pseudo' value='$p' required='required'></label><br>");
    echo ("<span class='erreur'>$e_pseudo</span><br>"); // si problème de pseudo
    echo("<label>Date de naissance 
        <input type='text' class='date' name='annee' value='$a' maxlength='4' pattern='\d\d\d\d'>/
        <input type='text' class='date' name='mois' value='$m' maxlength='2' pattern='\d\d'>/
        <input type='text' class='date' name='jour' value='$j' maxlength='2' pattern='\d\d'></label><br>");
    echo ("<span class='erreur'>$e_date</span><br>"); // si problème de date
    echo("<label>Adresse mail<input type='text' name='mail' value='$mail' required='required' pattern='[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$'></label><br>");
    echo("<span class='erreur'>$e_mail</span><br>"); // si problème de mail
    echo("<label>Mot de passe <input type='password' name='mdp' required='required'></label><br>");
    echo("<button type='submit'>Inscription</button>");
    echo("</form>");
}


if (isset($_SESSION['pseudo']))
{
    echo("<p>Vous pouvez pas vous inscrire, vu que vous êtes connecté ;w;<br></p>");
}
else
{
    /*initialisation des variables d'erreurs*/
    $e_pseudo = NULL;
    $e_mail = NULL;
    $e_date = NULL;

    if (!isset($_POST['pseudo']))//si c'est la première fois qu'on le rempli
    {
        afficheFormulaire(NULL, 'AAAA', 'MM', 'JJ', NULL, $e_pseudo, $e_date, $e_mail);
    }
    else
    {

        /*Recup de variable de post*/
        $pseudo = $_POST['pseudo'];
        $mdp = $_POST['mdp'];
        $mail = $_POST['mail'];
        $a = $_POST['annee'];
        $m = $_POST['mois'];
        $j = $_POST['jour'];

        $valide = 1;

        /*Vérifications*/
        if(!checkdate($m, $j, $a)) //si date de naissance valide
        {
            $e_date = "Date invalide";
            $m = 'MM';
            $j = 'JJ';
            $a = 'AAAA';
            $valide = 0;
        }

        include('connex.inc.php');
        $pdo = connex("spothifi");
        try
        {
            $stmt = $pdo->prepare("SELECT id FROM utilisateur WHERE pseudo LIKE :pseudo");
            $stmt->bindParam(':pseudo', $pseudo);
            $stmt->execute();
            if ($stmt->rowCount() != 0)
            {
                $e_pseudo = "Ce pseudo est déjà utilisé";
                $pseudo = NULL;
                $valide = 0;
            }
                
            $stmt = $pdo->prepare("SELECT id FROM utilisateur WHERE adresse_mail LIKE :mail");
            $stmt->bindParam(':mail', $mail);
            $stmt->execute();
            if ($stmt->rowCount() != 0)
            {
                $e_mail = "Ce mail est déjà utilisé";
                $mail = NULL;
                $valide = 0;
            }
            $stmt->closeCursor();
        }
        catch(PDOException $e)
        {
            echo '<p>Problème PDO</p>';
            echo $e->getMessage();
        }
        
        if($valide)
        { 
            $mdp = password_hash(md5($mdp), PASSWORD_BCRYPT);

            try
            {
                $stmt = $pdo->prepare("INSERT INTO utilisateur (pseudo, date_naissance, adresse_mail, mdp, statut) VALUES(:p, '$a-$m-$j', :m, :mdp, 0)");
                $stmt->bindParam(':p', $pseudo);
                $stmt->bindParam(':m', $mail);
                $stmt->bindParam(':mdp', $mdp);
                $stmt->execute();
                $stmt->closeCursor();
            }
            catch(PDOException $e)
            {
                echo '<p>Problème PDO</p>';
                echo $e->getMessage();
            }
            echo ("<p>Ca a marché :3<br> Félicitations vous êtes maintenant inscrit sur SpotHifi ! \(^w^)/</p><br><button type='button'><a href='connexion.php'>Connexion</a></button>");
        }
        else
        {
            afficheFormulaire($pseudo, $a, $m, $j, $mail, $e_pseudo, $e_date, $e_mail);
        }

        $pdo= NULL;
    }
    echo("</div>");
}
creation_footer();
?>