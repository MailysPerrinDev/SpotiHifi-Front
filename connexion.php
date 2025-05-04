<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>SpotHifi: Connexion</title>
        <link rel="stylesheet" href="connexion.css">
    </head>
    <body>
        
    </body>
</html>

<?php
session_start();

function afficheFormulaire($p, $e_connexion){
    echo ("<form class='connexion' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
    echo ("<label><span class='titre'>Nom utilisateur</span><br><input type='text' name='pseudo' value='".$p."' required='required'></label><br>");
    echo ("<label><span class='titre'>Mot de passe</span><br><input type='password' name='mdp' required='required'></label><br>");
    echo ("<span class='erreur'>$e_connexion</span><br>"); // si problème
    echo ("<button type='submit' class='connexion' name='b_connexion'>Connexion</button>");
    echo("</form>");
}


echo ("<div class='bloc_connexion'>");
echo ("<h1>Connexion</h1>");

if (isset($_SESSION['pseudo'])){
    echo("<p class='titre'>Vous ne pouvez pas vous connecter, vu que vous êtes connecté ;w;<br></p>");
}
else
{
    if (!isset($_POST['pseudo']))
    {
        afficheFormulaire(NULL, NULL);
    }
    else
    {
        $pseudo = $_POST['pseudo'];
        $mdp = md5($_POST['mdp']);
        
        include('connex.inc.php');
        $pdo = connex("spothifi");
        try
        {
            $stmt = $pdo->prepare("SELECT mdp FROM utilisateur WHERE pseudo = :pseudo");
            $stmt->bindParam(':pseudo', $pseudo);
            $stmt->execute();

            $hachage = $stmt->fetch();
            if ($stmt->rowCount() == 0 || !(password_verify($mdp, $hachage[0])))
            {
                afficheFormulaire($pseudo, "Votre pseudo ou votre mot de passe n'est pas bon");
            }
            else
            {
                try
                {
                    $stmt2 = $pdo->prepare("SELECT id, statut FROM utilisateur WHERE pseudo = :pseudo");
                    $stmt2->bindParam(':pseudo', $pseudo);
                    $stmt2->execute();
                    $_SESSION['pseudo'] = $pseudo;
                    $_SESSION['id'] = $stmt2->fetch()[0];
                    $_SESSION['statut'] = $stmt2->fetch()[1];

                    $stmt2->closeCursor();
                }
                catch(PDOException $e)
                {
                    echo '<p>Problème PDO</p>';
                    echo $e->getMessage();
                }
                
            }

            $stmt->closeCursor();
            $pdo = NULL;
        }
        catch(PDOException $e)
        {
            echo '<p>Problème PDO</p>';
            echo $e->getMessage();
        }  


        if (isset($_POST['pseudo'])) //si tout c'est bien passé
        {
            header('index.html');
        }     
    }

    echo("</div>");
}

?>
