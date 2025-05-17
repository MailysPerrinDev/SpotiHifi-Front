<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>SpotHifi</title>
        <link rel="stylesheet" href="styles.css?v=1.0" />
        <script src="script.js"></script>
        <?php
            session_start();
            include('fonctions.php');
            include('connex.inc.php');
            $pdo = connex("spothifi");
        ?>
    </head>
    <body>
        <header>
            <?php
                creation_nav(isset($_SESSION['pseudo']));
                if (isset($_SESSION['pseudo']))
                {
                    echo("<a id='profile' href='param_utilisateur.php'>");
                    afficher_img_profil($_SESSION['photo'], NULL, NULL, NULL, NULL);
                    echo("<img class='logo' src='img/pen-solid.svg'>");
                    echo("<h2>Bienvenu ".$_SESSION['pseudo']."!</h2></a>");
                }
                creation_recherche($pdo, NULL);
            ?>
        </header>
        <h1>Nos Selections</h1>
        <hr>
        <div id="recommandations">
            <div id="musiques">
                <?php
                   musique_aléatoire($pdo);
                ?>
            </div>
        </div>
<!--
            <br>
            <h1>Quelques playlist/albums</h1>
            <hr>
            <div id="playlists">

                 <div class="carte_playlist">
                    <div class="couverture"><img src="img/Tests/album.png" alt="couvertureGoodKid"></div>
                    <h3>Meilleur Playlist Ever</h3>
                    <h4>Anarisil</h4>
                </div>
                
            </div>
-->
        <?php
            creation_footer();
        ?>
    </body>
</html>


