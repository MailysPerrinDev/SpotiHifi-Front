<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>SpotHifi: Connexion</title>
        <link rel="stylesheet" href="styles.css?v=1.0">
    </head>
    <body>
        
    </body>
</html>

<?php
session_start();
include('fonctions.php');
include('connex.inc.php');

creation_nav(isset($_SESSION['pseudo']));

function afficheFormulaire($p, $e_connexion){
    echo ("<form class='connexion' action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method='post'>");
    echo ("<label><span class='titre'>Nom utilisateur</span><br><input type='text' name='pseudo' value='".$p."' required='required'></label><br>");
    echo ("<label><span class='titre'>Mot de passe</span><br><input type='password' name='mdp' required='required'></label><br>");
    echo ("<span class='erreur'>$e_connexion</span><br>"); // si 
    echo ("<button type='submit' class='connexion' name='b_connexion'>Connexion</button><br>");
    echo("<a href='inscription.php'>pas encore inscrit?</a><br>");
    echo("</form>");
}


echo ("<div class='bloc_connexion'>");
echo ("<h1>Connexion</h1><br><hr><br>");

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
                    $stmt2 = $pdo->prepare("SELECT id FROM utilisateur WHERE pseudo = :pseudo");
                    $stmt2->bindParam(':pseudo', $pseudo);
                    $stmt2->execute();
                    $_SESSION['pseudo'] = $pseudo;
                    $_SESSION['id'] = $stmt2->fetch()[0];
                    
                    $stmt2 = $pdo->prepare("SELECT statut FROM utilisateur WHERE pseudo = :pseudo");
                    $stmt2->bindParam(':pseudo', $pseudo);
                    $stmt2->execute();
                    $_SESSION['statut'] = $stmt2->fetch()[0];
                    
                    /*création fichier image de profil de l'utilisateur*/
                    $img_tmp = "img/utilisateur/$pseudo.jpg";
                    //on cherche si l'utilisateur a une photo de profil
                    $stmt2 = $pdo->prepare("SELECT id_photo FROM photo_utilisateur WHERE id_utilisateur = :id");
                    $stmt2->bindParam(':id', $_SESSION['id']);
                    $stmt2->execute();

                    if ($stmt2->rowCount() != 0) //Si l'utilisateur a une photo
                    {
                        $id_photo = $stmt2->fetch()[0];
                        creation_fichier_image($pdo, $id_photo, $img_tmp);
                        $_SESSION['photo'] = $img_tmp;                        
                    }
                    else
                    {
                        $_SESSION['photo'] = null;
                    }
                    
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

        if(isset($_SESSION['id'])) // si connecter
        {
            header("Location: index.php");
            exit();
        }
    }
}
echo("</div>");
creation_footer();
?>
