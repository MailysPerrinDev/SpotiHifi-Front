<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>SpotHifi: Aide</title>
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
            ?>
            <br><br>
            <h1 id="titre_page_aide">Page d'aide</h1>
            <br>
        </header>
        <div id="page_aide">
            <h1>Inscription</h1>
            <hr>
            <br>
            <p>Pour s'inscrire il suffit d'appuyer sur "Connexion" en haut à gauche de l'écran puis "pas encore inscrit?" en dessous du bouton "Connexion" de la page.</p>
            <br>
            <h1>Gestion du compte</h1>
            <hr>
            <br>
            <p>Pour ouvrir la page de gestion de votre compte, connectez-vous. Puis, sur la page d'accueil, vous verrez apparaître votre photo de profil et pseudo. il vous suffit de cliquer dessus et vous serez redirigés vers la page de gestion de compte.</p>
            <br>
            <h1>Nous contacter</h1>
            <hr>
            <br>
            <p>PERRRIN Maïlys : mailys.perrin@etu.univ-st-etienne.fr<br>BRUNET-DEFRENNE Claudia : claudia.brunet.defrenne@etu.univ-st-etienne.fr</p>
            <br>
        </div>
        <footer>
            <?php
            creation_footer();
            ?>
        </footer>
    </body>
</html>