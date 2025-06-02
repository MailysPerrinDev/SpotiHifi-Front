<script src="script.js"></script>
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
            <input id='recherche' name='recherche' type='text' placeholder='Recherche...' value='$valeur'>
        </form>
        ");
    echo("<div id='menu_filtre'>");
    
    $t_tags = recup_donnee_table($pdo, "tags", "musique");
    $tags = array();
    
    foreach ($t_tags as $t) /*On filtre pour qu'il n'y ait pas de fusion de plusieurs tags*/
    {
        $t = explode(',', $t);
        foreach ($t as $tag)
        {
            $existe = 0;
            foreach ($tags as $existant)
            {
                if($tag === $existant)
                {
                    $existe = 1;
                }
            }
            if (!$existe)
            {
                array_push($tags, $tag);
            }
        }
    }
    
    if (count($tags) > 7) /*Si on en a plus de 6 alors on en selectionne au hazard*/
    {
        $tags_aleat = array_rand($tags, 7);
        
        foreach ($tags_aleat as $i)
        {
            if ($tags[$i] != NULL)
            {
                echo("<button class='tag' name='$tags[$i]' onclick='recherche_tag()'><span>".$tags[$i]."</span></button>");
            }
        }
    }
    else
    {
        foreach($tags as $tag)
        {
            if ($tag != NULL)
            {
                echo("<button class='tag' name='$tag' onclick='recherche_tag()'><span>".$tag."</span></button>");
            }
        }
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
    echo("<img class='image' src=$img_profil alt='Photo de profil' $id_img $l $h>");
    if (!is_null($message))
    {
        echo("<br><legend $id_img>$message</legend>");
    }
    echo("</figure>");  
}

/*---UTILISATEUR---*/
function afficher_membres($pdo, $val, $colonne, $nb_res, $ordre)
{ 
    /*--traitement des variables--*/
    $tri = "ORDER BY";
    if($colonne != null)
        $tri .= " $colonne";
    else
        $tri .= " pseudo";

    if( $ordre != null)
        $tri .= " DESC";

    if($nb_res != null)
        $nb_res = "LIMIT $nb_res";
    
    if($val != null)
        $val = "WHERE pseudo = '$val'";

    /*--recherche--*/
    echo("<table>");
    echo("<caption>Utilisateur</caption>");
    echo("<tr><th>id</th><th>pseudo</th><th>date de naissance</th><th>mail</th><th>statut</th></tr>");
    
    try
    {
        $stmt = $pdo->prepare("SELECT id, pseudo, date_naissance, adresse_mail, statut FROM utilisateur $val $tri $nb_res");
        $stmt->execute();
        if ($stmt->rowCount() == 0)
            echo("<tr><td class='erreur' colspan=4>Aucun résultat</td></tr>");
        else
        { 
            $i = 0;
            $donnees = $stmt->fetchALL();
            foreach($donnees as $ligne)
            {
                echo("<tr>");
                foreach($ligne as $colonne)
                {
                    if($i%2 == 0)
                        echo("<td>$colonne</td>");
                    $i++;
                }
                echo("</tr><br>");
            }
        }
        $stmt->closeCursor();
    }
    catch(PDOException $e)
    {
        echo '<p>Problème PDO</p>';
        echo $e->getMessage();
    }
    echo("</table>");
    return $donnees;
}

function modifier_utilisateur($pdo, $colonne, $id, $n_val, $s)
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
                if($s==null && ($colonne == "pseudo" || $colonne == "statut"))
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

/*---MUSIQUE---*/

function afficher_musiques_artiste($pdo, $id_artiste)
{
    try
    {
        $stmt= $pdo->prepare("SELECT id, nom, duree, date_creation, nb_like, description, lien FROM musique WHERE id_artiste = :id ORDER BY date_creation DESC");
        $stmt->bindParam(":id", $id_artiste);
        $stmt->execute();

        $nb_musique = $stmt->rowCount();
        echo("<input type='text' name='nb_musique' value='$nb_musique' readonly='readonly'><span> musique(s)</span>");
        $donnees = $stmt->fetchALL();
    }
    catch(PDOException $e)
    {
        echo '<p>Problème PDO</p>';
        echo $e->getMessage();
    }

    $field= ['nom', 'duree', 'date_creation', 'nb_like', 'description', 'lien'];
    

    echo("<table>");
    echo("<thead><tr><th>Id</th><th>Nom</th><th>Duree</th><th>Date de creation</th><th>Nombre de likes</th><th>Description</th><th>Lien</th></tr></thead>");
    if(isset($donnees))
    {
        for($j=0; $j<$nb_musique; $j+=1)
        {
            echo("<tr>");
            echo("<td><input type='text' name='".$j."id' value='".$donnees[$j][0]."' readonly='readonly'></td>");
            for($i=1; $i<7; $i+=1)
            {
                echo("<td>");
                if($field[$i-1] == 'description')
                    echo('<textarea cols="20" rows="1" name="'.$j.$field[$i-1].'" value = "'.$donnees[$j][$i].'"></textarea>');
                else
                {
                    echo("<input type='text' name='".$j.$field[$i-1]."'");
                    if($field[$i-1] == 'name')
                    {
                        echo("required='required'");
                    }
                    else if($i != 1 && $i<=4)
                        echo(" readonly= 'readonly' ");
                    echo(" value='".$donnees[$j][$i]."'>");
                }
                echo("</td>");
            }
            echo("</tr><br>");
        }           
    }
    echo("</table>");
    return $nb_musique;
}

function generation_carte_musique($pdo, $nom_artiste, $nom_musique)
{
    $photo = "img/photoParDefaut.png";
    
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
        
        if ($photo == NULL)
        {
            $photo = "img/photoParDefaut.png";
        }

        $stmt->closeCursor();
    }
    catch(PDOException $e)
    {
        echo '<p>Problème PDO</p>';
        echo $e->getMessage();
    }
    echo("<a class='carte_musique' href='musique.php?id_musique=$id_musique'>");
    echo("<div class='couverture'>");
    echo("<img src=".$photo.">");
    echo("</div>");
    
    /*Informations*/
    $nom = recup_donnee_table_id($pdo, "nom", "musique", $id_musique); 
    $tags = recup_donnee_table_id($pdo, "tags", "musique", $id_musique);
    $description = recup_donnee_table_id($pdo, "description", "musique", $id_musique);
    
    if ($description == NULL)
    {
        $description = "Aucune description";
    }
    
    echo("<div class='description'>");
    echo("<h2>".htmlspecialchars($nom)."</h2>");
    echo("<p>".htmlspecialchars($description)."</p>");
    echo("<div class='liste_tags'>");
    
    $tags = explode(',', $tags);
    
    if (count($tags) < 3)
    {
        foreach ($tags as $tag)
        {
            if ($tag != NULL)
            {
                echo("<button class='tag' name='$tag' onclick='recherche_tag()'><span>".htmlspecialchars($tag)."</span></button>");
            }
        }
    }
    else
    {
        $tags_aleat = array_rand($tags, 2);
        foreach ($tags_aleat as $i)
        {
            if ($tags[$i]!= NULL)
            {
                echo("<button class='tag' name='$tags[$i]' onclick='recherche_tag()'><span>".htmlspecialchars($tags[$i])."</span></button>");
            }
        }
        echo("<button class='tag'><span>...</span></button>");
    }
    echo("</div></div>");
    echo("</a>");
}

function musique_aléatoire($pdo)
{
    try
    {
        $stmt = $pdo->prepare("SELECT id FROM musique;");
        $stmt->execute();
        $id_musiques = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if ($stmt->rowcount() != 0)
        {
            if (count($id_musiques) >= 9) /*Si on en a plus de 6 alors on en selectionne au hazard*/
            {
                $id_musiques_aleat = array_rand($id_musiques, 9);
                foreach ($id_musiques_aleat as $i)
                {
                    $nom_musique = recup_donnee_table_id($pdo, "nom", "musique", $id_musiques_aleat[$i]);
                    $id_artiste = recup_donnee_table_id($pdo, "id_artiste", "musique", $id_musiques_aleat[$i]);
                    $nom_artiste = recup_donnee_table_id($pdo, "pseudo", "utilisateur", $id_artiste);
                    generation_carte_musique($pdo, $nom_artiste, $nom_musique);
                }
            }
            else
            {
                foreach($id_musiques as $id_musique)
                {
    
                    $nom_musique = recup_donnee_table_id($pdo, "nom", "musique", $id_musique);
                    $id_artiste = recup_donnee_table_id($pdo, "id_artiste", "musique", $id_musique);
                    $nom_artiste = recup_donnee_table_id($pdo, "pseudo", "utilisateur", $id_artiste);
                    generation_carte_musique($pdo, $nom_artiste, $nom_musique);
                }
            }
            $stmt->closeCursor();
        }
       
    }
    catch(PDOException $e)
    {
        echo '<p>Problème PDO</p>';
        echo $e->getMessage();
    }
}
/*gestion musique*/
function modifier_musique($pdo, $colonne, $id, $n_val)
{

    $e = null;

    try
    {
        if($colonne = "nb_like")
        {
            $stmt = $pdo->prepare("SELECT nb_like FROM musique WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $nb_val = $stmt->fetch()[0]+1;
        }
        if($colonne = "duree" && $n_val >= '10:00:00')
        {
            $e = "duree trop grande";
        }
        
        if($e == null && $n_val != null)
        {
            $stmt = $pdo->prepare("UPDATE musique SET $colonne = :val WHERE id = :id ;");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':val', $n_val);
            $stmt->execute();

            if($stmt->rowCount() == 0)
            {
                $e = "Problème mise à jour pas été faite";
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

function inserer_musique($pdo, $id_artiste, $nom, $duree, $date_creation, $tags, $paroles, $description, $lien_clip)
{

    $e = null;

    if($id_artiste==null || $duree==null || $nom==null || $date_creation==null || $tags==null || $lien_clip==null)
            $e = "il manque des attributs obligatoires<br>";
    else
    {
        try
        {
            $stmt = $pdo->prepare("SELECT id FROM musique WHERE id_artiste= :id AND nom= :n");
            $stmt->bindParam(':id', $id_artiste);
            $stmt->bindParam(':n', $nom);
            $stmt->execute();

            if($stmt->rowCount() == 0)
            {
                $stmt = $pdo->prepare("INSERT INTO musique(id_artiste, nom, duree, date_creation, paroles, tags, description, lien) VALUES (:id, :n, :d, :dc, :p, :t, :dec, :l)");
                $stmt->bindParam(':id', $id_artiste);
                $stmt->bindParam(':n', $nom);
                $stmt->bindParam(':d', $duree);
                $stmt->bindParam(':dc', $date_creation);
                $stmt->bindParam(':p', $paroles);
                $stmt->bindParam(':t', $tags);
                $stmt->bindParam(':dec', $description);
                $stmt->bindParam(':l', $lien_clip);
                $stmt->execute();

                if($stmt->rowCount() == 0)
                {
                    $e = "Problème insertion pas faite<br>";
                }
            }
            else
            {
                $e= "Nom de la musique déjà existant<br>";
            }

            $stmt->closeCursor();
        }
        catch(PDOException $e)
        {
            echo '<p>Problème PDO</p>';
            echo $e->getMessage();
        }
    }

    return $e;
}

function supprimer_donnee_musique($pdo, $id_artiste, $nom)
{
    try
    {
        $stmt= $pdo->prepare("DELETE FROM photo_musique WHERE id_musique IN (
            SELECT id FROM musique WHERE nom = :nom AND id_artiste = :id);");
        $stmt->bindParam(":nom", $nom);
        $stmt->bindParam(":id", $id_artiste);
        $stmt->execute();

        $stmt= $pdo->prepare("DELETE FROM musique WHERE nom = :nom AND id_artiste = :id");
        $stmt->bindParam(":nom", $nom);
        $stmt->bindParam(":id", $id_artiste);
        $stmt->execute();
    }
    catch(PDOException $e)
        {
            echo '<p>Problème PDO</p>';
            echo $e->getMessage();
        }
}
/*--TABLES GENERALES*/
function recup_donnee_table_id($pdo, $donnee, $table, $id)
{
    try
    {
        $stmt = $pdo->prepare("SELECT $donnee FROM $table WHERE id = :id;");
        $stmt->bindParam(':id', $id);
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

function recup_donnee_table($pdo, $donnee, $table)
{
    try
    {
        $stmt = $pdo->prepare("SELECT $donnee FROM $table");
        $stmt->execute();
        $resultat = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $stmt->closeCursor();
        return $resultat;
    }
    catch(PDOException $e)
    {
        echo '<p>Problème PDO</p>';
        echo $e->getMessage();
    } 
}

function supprimer_donnee($pdo, $table, $id)
{
    try
    {
        $stmt = $pdo->prepare("DELETE FROM $table WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        echo($stmt->rowCount()." ligne supprimée");
    }
    catch(PDOException $e)
    {
        echo '<p>Problème PDO</p>';
        echo $e->getMessage();
    }
}

/*--Recherche--*/
function recherche($pdo, $mot_recherche)
{
    $tab_id_musique = recup_donnee_table($pdo, "id", "musique");

    foreach ($tab_id_musique as $id_musique)
    {
        $nom_musique = recup_donnee_table_id($pdo, "nom", "musique", $id_musique);
        $distance = levenshtein(mb_strtolower($mot_recherche, 'UTF-8'), mb_strtolower($nom_musique, 'UTF-8'));
        
        if ($nom_musique === $mot_recherche || $distance <= 7)
        {
            $id_artiste = recup_donnee_table_id($pdo, "id_artiste", "musique", $id_musique);
            $nom_artiste = recup_donnee_table_id($pdo, "pseudo", "utilisateur", $id_artiste);
            generation_carte_musique($pdo, $nom_artiste, $nom_musique);
        }
    }    
}
?>

