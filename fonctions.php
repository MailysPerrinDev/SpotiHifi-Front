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

function creation_recherche($pdo, $valeur)
{
    echo("
        <form method='POST' action='recherche.php' id='barre_recherche'>
            <div id='btn_recherche'>
                <img src='img/logoRecherche.svg' alt='valider'>
            </div>
            <input id='recherche' name='recherche' type='text' placeholder='Recherche...' value=$valeur>
        </form>
        ");
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

    echo("<figure id='pdp'>");
    echo("<img src=$img_profil alt='Photo de profil' $id_img $l $h>");
    if (!is_null($message))
    {
        echo("<br><legend $id_img>$message</legend>");
    }
    echo("</figure>");  
}

function modifier_utilisateur($pdo, $colonne, $id, $n_val)
{
    $e = null;

    try
    {
        if($colonne == "statut" && $n_val == 0) //si veux devenir utilisateur lambda on supprime les musiques associées
        {
            $stmt = $pdo->prepare("DELETE FROM ajouter WHERE id_musique IN (
                SELECT id FROM musique WHERE id_artiste = :id);
                DELETE FROM musique WHERE id_artiste = :id;"
                );
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }

        if($colonne == "pseudo" || $colonne == "adresse_mail") 
        // on vérifie que ça n'existe pas déjà
        {
            $stmt = $pdo->prepare("SELECT id FROM utilisateur WHERE $colonne=:val");
            $stmt->bindParam(':val', $n_val);
            $stmt->execute();
            
            if($stmt->rowCount() != 0)
            {
                $e= $colonne." est déjà utilisé";
            } 
        }

        if($e == null)
        {
            $stmt = $pdo->prepare("UPDATE utilisateur SET $colonne = :val WHERE id = :id ;");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':val', $n_val);
            $stmt->execute();

            if($stmt->rowCount() != 0)
            {
                if($colonne == "pseudo" || $colonne == "statut")
                    $_SESSION[$colonne] = $n_val;
                $modif = 1;
            }
        }

        $stmt->closeCursor();
    }
    catch(PDOException $e)
    {
        echo '<p>Problème PDO</p>';
        echo $e->getMessage();
    }

    return $e;
}

function recup_donnee_musique($pdo, $donnee, $id_musique)
{
     try
    {
        $stmt = $pdo->prepare("SELECT $donnee FROM musique WHERE id = :id_musique;");
        $stmt->bindParam(':id_musique', $id_musique);
        $stmt->execute();
        $resultat = $stmt->fetchColumn();

        $stmt->closeCursor();
        return $resultat;
    }
    catch(PDOException $e)
    {
        echo '<p>Problème PDO</p>';
        echo $e->getMessage();
    }
}

function generation_carte_musique($pdo, $nom_artiste, $nom_musique)
{
    $photo = "img/photoParDefaut.png";
        
    echo("<div class='carte_musique'>");
    
    /*couverture de la musique*/
    
    /*On recupere la photo*/
    try
    {
        $stmt = $pdo->prepare("SELECT id FROM musique WHERE nom = :nom_musique 
                                AND id_artiste = (SELECT id FROM utilisateur WHERE pseudo = :nom_artiste);");
        $stmt->bindParam(':nom_musique', $nom_musique);
        $stmt->bindParam(':nom_artiste', $nom_artiste);
        $stmt->execute();
        $id_musique = $stmt->fetchColumn();
        
        if ($stmt->rowCount() != 0) //Si la photo existe
        {
            $stmt2 = $pdo->prepare("SELECT photo FROM photo_musique WHERE id_musique = :id_musique;");
            $stmt2->bindParam(':id_musique', $id_musique);
            $stmt2->execute();
            $photo = $stmt2->fetchColumn();
            
            $stmt2->closeCursor();
        }

        $stmt->closeCursor();
    }
    catch(PDOException $e)
    {
        echo '<p>Problème PDO</p>';
        echo $e->getMessage();
    }
    
    echo("<div class='couverture'>");
    echo("<img src=".$photo.">");
    echo("</div>");
    
    /*Informations*/
    $nom = recup_donnee_musique($pdo, "nom", $id_musique); 
    $tags = recup_donnee_musique($pdo, "tags", $id_musique);
    $description = recup_donnee_musique($pdo, "description", $id_musique);
    
    if ($description == NULL)
        $description = "Aucune description";
    
    echo("<div class='description'>");
    echo("<h2>".htmlspecialchars($nom)."</h2>");
    echo("<p>".htmlspecialchars($description)."</p>");
    echo("<div class='liste_tags'>");
    
    $tags = explode(',', $tags);
    if (count($tags) <= 3)
    {
        foreach ($tags as $tag)
        {
            echo("<button class='tag'><span>".htmlspecialchars($tag)."</span></button>");
        }
    }
    else
    {
        for ($i=0; $i<count($tags); $i++)
        {
             echo("<button class='tag'><span>".htmlspecialchars($tags[$i])."</span></button>");
        }
        echo("<button class='tag'><span>...</span></button>");
    }
    echo("</div></div>");
    echo("</div>");
}

?>

