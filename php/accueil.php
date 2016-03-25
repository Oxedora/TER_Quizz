<?php

session_start();
include_once("connexion.php");
include_once("fonctions.php");

/* Affiche un message d'erreur si connexion échouée */
$erreurCo = "";
$erreurCrea = "";
$erreurModif = "";

if(isset($_POST['login']) && isset($_POST['password']) && !($_SESSION['connecte'])){
	$erreurCo = authentification($dbh);
}

if(isset($_POST['nlogin']) && isset($_POST['npassword']) && isset($_POST['npasswordV'])){
	$erreurCrea = inscription($dbh);
}

if(isset($_POST['infoPassword'])){
	$erreurModif = modifInfoUser($dbh);
}

$infos_user = getInfosUser($dbh);

/* Stockage de la vue à charger dans un buffer */
$html = recupererHTML("../main.html");

/* Initialisation du tableau pour le remplacement */
$remplacement = array(
	'%navbar%'		=> recupererHTML("../html/navbar.html"),
	'%contenu%' 		=> ($_SESSION['connecte'] ? recupererHTML("../html/compte.html") : recupererHTML("../html/accueil.html")),
	'%scripts%'		=> "",
	'%accueilActif%'	=> 'class="active"',
	'%questActif%'		=> "",
	'%statActif%'		=> "",
	'%deconnexion%' 	=> ($_SESSION['connecte'] ? '<ul class="nav navbar-nav navbar-right"><li><a href="../php/deconnexion.php">Deconnexion</a></li></ul>' : ''),
	'%erreurCo%'		=> $erreurCo,
	'%erreurCrea%'		=> $erreurCrea,
	'%erreurModif%'		=> $erreurModif,
	'%selGNull%'		=> (!isset($infos_user->genre) ? "selected" : ""),
	'%selGHom%'		=> ($infos_user->genre == "homme" ? "selected" : ""),
	'%selGFem%'		=> ($infos_user->genre == "femme" ? "selected" : ""),
	'%infoProf%'		=> ( isset($infos_user->profession) ? "value='".$infos_user->profession."'" : ""),
	'%infoFrT%'		=> ( $infos_user->fr_natif ? 'checked' : ''),
	'%infoFrF%'		=> ( !$infos_user->fr_natif ? 'checked' : '')
	);

/* Remplacement des variables de la vue par les données de la page */ 
$html = remplacerContenu($html, $remplacement);

echo $html;

?>
