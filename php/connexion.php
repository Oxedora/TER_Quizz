<?php

	/**
	* Connexion à la BDD du site via PDO
	*/
	
	$host = "localhost";
	$dbname = "Questionnaire_theo";
	$user = "root";
	$pass = "Tchernobyl32";
	try {$dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);}
	catch(PDOException $e){die('Erreur : ' . $e->getMessage());}
?>
