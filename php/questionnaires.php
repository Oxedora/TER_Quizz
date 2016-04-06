<?php
session_start();

include_once("fonctions.php"); // Fonctions auxiliaires
include_once("Question.php");

/* Stockage de la vue à charger dans un buffer */
$html = recupererHTML("../main.html");

/* Initialisation du tableau pour le remplacement */
$remplacement = array(
'%navbar%'		=> recupererHTML("../html/navbar.html"),
'%contenu%' 		=> mainQuestionnaire(),
'%scripts%'		=> "",
'%accueilActif%'	=> "",
'%questActif%'		=> 'class="active"',
'%statActif%'		=> "",
'%deconnexion%' 	=> ($_SESSION['connecte'] ? '<ul class="nav navbar-nav navbar-right"><li><a href="../php/deconnexion.php">Deconnexion</a></li></ul>' : '')
);

/* Remplacement des variables de la vue par les données de la page */ 
$html = remplacerContenu($html, $remplacement);

echo $html;

?>
