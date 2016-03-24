<?php

/**
* Liste des requêtes pour accéder aux données de la BDD
* Rend le code plus fluide par la suite
* 
* Toutes les variables utilisées viennent de $_POST
*/

// Vérification de l'authentification d'un utilisateur
$check_authentification = "
SELECT pseudo 
FROM user 
WHERE pseudo='".$_POST['login']."' 
AND password='".$_POST['password']."'
";

// Récupération des infos de l'utilisateur
$infos_user ="
SELECT mail, genre, profession, adresse, fr_natif
FROM user
WHERE pseudo='".$_SESSION['pseudo']."'
";

?>
