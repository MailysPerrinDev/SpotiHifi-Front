<?php

function creation_nav($est_connecter)
{
    echo("
        <nav>
            <a id='aide' href='aide.php'>Aide</a>
            <a id='titre' href='index.php'>SpotHifi</a>
        ");
    if ($est_connecter == 1)
    {
        echo("<a id='connexion' href='deconnexion.php'>Déconnexion</a>");
    }
    else
    {
         echo("<a id='connexion' href='connexion.php'>Connexion</a>");
    }     
    echo("</nav>");
}

function creation_recherche($pdo)
{
    echo("
        <div id='barre_recherche'>
            <div id='btn_recherche'>
                <img src='img/logoRecherche.svg' alt='valider'>
            </div>
            <input id='recherche' type='text' placeholder='Recherche...''>
        </div>"
        );
    echo("<div id='menu_filtre'>");
    
    try
    {
        $stmt = $pdo->prepare("SELECT DISTINCT(tags) FROM musique");
        $stmt->execute();
        $tags = $stmt->fetchALL(PDO::FETCH_COLUMN);
        
        if ($stmt->rowcount() != 0)
        {

            if (count($tags)>=6) /*Si on en a plus de 6 alors on en selectionne au hazard*/
            {
                $tags_aleat = array_rand($tags, 6);

                foreach ($tags_aleat as $i)
                {
                    echo("<button class='tag'><span>".$tags_aleat[$i]."</span></button>");
                }
            }
            else
            {
                foreach($tags as $tag)
                {
                    echo("<button class='tag'><span>".$tag."</span></button>");
                }
            }
        }
        else
        {
            echo("marche pô! t nul...");
        }
    }
    catch(PDOException $e)
    {
        echo '<p>Problème PDO</p>';
        echo $e->getMessage();
    }
    
    echo("</div>");
}

function creation_footer()
{
    echo("
        <footer>
            <div>PERRIN Maïlys <br /> BRUNET-DEFRENNE Claudia</div>
        </footer>
        ");
}

function creation_fichier_image($pdo, $id_photo, $nom_img)
{
    try
    {
        $stmt = $pdo->prepare("SELECT photo FROM photos WHERE id = :id");
        $stmt->bindParam(':id', $id_photo);
        $stmt->execute();

        
        if ($stmt->rowCount() != 0) //Si la photo existe
        {
            file_put_contents($nom_img, $stmt->fetch()[0]);
        }
        else
        {
            echo("ERREUR creation_fichier_image(): la photo d'id $id_photo n'existe pas");
        }

        $stmt->closeCursor();
    }
    catch(PDOException $e)
    {
        echo '<p>Problème PDO</p>';
        echo $e->getMessage();
    }
}

function afficher_img_profil($img_profil, $id_img, $l, $h, $message)
{
    /*gestion des options*/
    if(!is_null($id_img))
    {
        $id_img = "id=".$id_img;
    }
    if(!is_null($l))
    {
        $l = "width=".$l;
    }
    if(!is_null($h))
    {
        $h = "height=".$h;
    }
    if(is_null($img_profil))
    {
        $img_profil = "img/photoParDefaut.png";
    }

    echo("<figure>");
    echo("<img id='pdp' src=$img_profil alt='Photo de profil' $id_img $l $h>");
    if (!is_null($message))
    {
        echo("<br><legend $id_img>$message</legend>");
    }
    echo("</figure>");  
}
?>
