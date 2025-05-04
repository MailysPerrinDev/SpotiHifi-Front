<?php                                                
function connex($base){                                   
    include_once("param.inc.php");                     
    try {
        $pdo = new PDO('mysql:host='.HOTE.';dbname='.$base, UTILISATEUR, PASSE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo $e->getMessage();
        echo '<br>Problème à la connexion';
            die();
        }
            
            return $pdo;
        }
?>
