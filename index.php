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
        ?>
    </head>
    <body>
        <header>
            <?php
                creation_nav();
                if (isset($_SESSION['pseudo'])){
                    echo("<div id='profile'><div id='pdp'>");
                    afficher_img_profil($_SESSION['photo'], NULL, NULL, NULL, NULL);
                    echo("<h2>Bon retour parmi nous ".$_SESSION['pseudo']."!</h2></div>");
                }
                creation_recherche();
            ?>
        </header>
        <h1>Nos Selections</h1>
        <hr>
        <div id="recommandations">
            <div id="musiques">
                <div class="carte_musique">
                    <div class="couverture">
                        <img src="img/Tests/Video.png">
                    </div>
                    <div class="description">
                        <h2>Imagine Doggoes</h2>
                        <p>Best mix de l'été franchement je recommande grandement mais attention aux mosh parce que prout. mais Solence c'est large mieux hein faites pas gaffe. Mais nan mais nan toutoutouloulou... Mais nan mais nan toutoutouloulou MAIS nan mais nan toutoutouloulou</p>
                        <div class="liste_tags">
                            <button class="tag"><span>mignon</span></button>
                            <button class="tag"><span>rock</span></button>
                            <button class="tag"><span>...</span></button>
                        </div>
                    </div>
                </div>
                <div class="carte_musique">
                    <div class="couverture">
                        <img src="img/Tests/WhereWereYou.png">
                    </div>
                    <div class="description">
                        <h2>WHERE WERE YOU..?</h2>
                        <p>Best musique de l'année</p>
                        <br />
                        <div class="liste_tags">
                            <button class="tag"><span>electronique</span></button>
                            <button class="tag"><span>metal</span></button>
                        </div>
                    </div>
                </div>
                <div class="carte_musique">
                    <div class="couverture">
                        <img src="img/Tests/oiseau.jpg">
                    </div>
                    <div class="description">
                        <h2>Imagine Zoizo</h2>
                        <p>Best mix de l'hiver</p>
                        <br />
                        <div class="liste_tags">
                            <button class="tag"><span>zoizo</span></button>
                            <button class="tag"><span>jazz</span></button>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <h1>Quelques playlist/albums</h1>
            <hr>
            <div id="playlists">
                <div class="carte_playlist">
                    <div class="couverture"><img src="img/Tests/album.png" alt="couvertureGoodKid"></div>
                    <h3>Meilleur Playlist Ever</h3>
                    <h4>Anarisil</h4>
                </div>
                <div class="carte_playlist">
                    <div class="couverture"><img src="img/Tests/AngelsCalling.png" alt="couvertureAngelsCalling"></div>
                    <h3>Angels Calling</h3>
                    <h4>Solence</h4>
                </div>
                 <div class="carte_playlist">
                     <div class="couverture"><img src="img/Tests/album.png" alt="couvertureGoodKid"></div>
                    <h3>Meilleur Playlist Ever</h3>
                    <h4>Anarisil</h4>
                </div>
                <div class="carte_playlist">
                    <div class="couverture"><img src="img/photoParDefaut.png" alt=""></div>
                    <h3>Meilleur Playlist Ever</h3>
                    <h4>Anarisil</h4>
                </div>
                <div class="carte_playlist">
                    <div class="couverture"><img src="img/photoParDefaut.png" alt="couvertureGoodKid"></div>
                    <h3>Meilleur Playlist Ever</h3>
                    <h4>Anarisil</h4>
                </div>
                 <div class="carte_playlist">
                    <div class="couverture"><img src="img/Tests/album.png" alt="couvertureGoodKid"></div>
                    <h3>Meilleur Playlist Ever</h3>
                    <h4>Anarisil</h4>
                </div>
                <div class="carte_playlist">
                    <div class="couverture"><img src="img/Tests/album.png" alt="couvertureGoodKid"></div>
                    <h3>Meilleur Playlist Ever</h3>
                    <h4>Anarisil</h4>
                </div>
                <div class="carte_playlist">
                    <div class="couverture"><img src="img/Tests/papillon.jpg" alt="couvertureGoodKid"></div>
                    <h3>Meilleur Playlist Ever</h3>
                    <h4>Anarisil</h4>
                </div>
                 <div class="carte_playlist">
                    <div class="couverture"><img src="img/Tests/album.png" alt="couvertureGoodKid"></div>
                    <h3>Meilleur Playlist Ever</h3>
                    <h4>Anarisil</h4>
                </div>
            </div>
        </div>
        <?php
            creation_footer();
        ?>
    </body>
</html>


