<?php
session_start();

if (file_exists("img/".$_SESSION['pseudo'].".jpg")) //si l'utilisateur a une photo personnalisée
{
	unlink("img/".$_SESSION['pseudo'].".jpg");
}

session_unset();
session_destroy();

header('Location:index.php');
?>
