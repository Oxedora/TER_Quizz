<?php

/**
* Renvoie le contenu de la $page html
*
* $page : vue html
*/
function recupererHTML($page){
	ob_start();
	include($page);
	$html = ob_get_contents();

	ob_end_clean();
	return $html;
}
/**
* Remplace le contenu de la $vue par le $nouveauContenu et retourne le résultat
*
* $vue : vue html
	* $nouveauContenu : contenu à insérer dans la vue
*/
	function remplacerContenu($vue, $nouveauContenu){
		return str_replace(array_keys($nouveauContenu), array_values($nouveauContenu), $vue);
	}

/**
* Met $_SESSION['connecte'] vrai si les identifiants sont corrects, faux sinon 
* et renvoie le message d'erreur.
*/
function authentification($dbh){
	$connexionAccepte = $dbh->query("
		SELECT pseudo 	
		FROM user 
		WHERE pseudo='".$_POST['login']."' 
		AND password='".$_POST['password']."'
"); // $_POST contient les variables
	$connexionAccepte = $connexionAccepte->fetch(PDO::FETCH_OBJ);
	$_SESSION['connecte'] = $connexionAccepte != false;
	$erreur = ($_SESSION['connecte'] ? "" : '<p class="text-danger"><b>Erreur: identifiant ou mot de passe incorrect.</b></p>');	
	$_SESSION['pseudo'] = ($_SESSION['connecte'] ? $_POST['login'] : null);
	return $erreur;
}

/**
* Inscrit une personne dans la base de donnée si le pseudo est disponible, affiche un message d'erreur sinon.
**/

function inscription($dbh){
	if($_POST['npassword'] == $_POST['npasswordV']){
		$pseudoPris = $dbh->query("
			SELECT pseudo
			FROM user
			WHERE pseudo='".$_POST['nlogin']."'
			");

		$pseudoPris = $pseudoPris->fetch(PDO::FETCH_OBJ);
		$fr_natif = (isset($_POST['fr_check']) ? '1' : '0');

		if($pseudoPris === false){
			if($dbh->query("INSERT INTO user (pseudo, password, fr_natif) VALUES ('".$_POST['nlogin']."','".$_POST['npassword']."', $fr_natif)") === FALSE){
				return '<p class="text-danger"><b>Erreur: impossible de créer le compte.</b></p>';
			}
		}
		else{
			return '<p class="text-danger"><b>Erreur: identifiant indisponible.</b></p>';
		}
	}			
	else{
		return '<p class="text-danger"><b>Erreur: Les mots de passes sont différents.</b></p>';
	}
}

/**
* Récupère les informations de l'utilisateur
*/

function getInfosUser($dbh){
	$infos_user = $dbh->query("
		SELECT mail, genre, profession, fr_natif
		FROM user
		WHERE pseudo='".$_SESSION['pseudo']."'
		");

	return $infos_user->fetch(PDO::FETCH_OBJ);
}

/**
* Modifie les champs renseignés de l'utilisateur
*/
function modifInfoUser($dbh){
	$verifCompte = $dbh->query("
		SELECT pseudo
		FROM user
		WHERE pseudo='".$_SESSION['pseudo']."'
		AND password='".$_POST['infoPassword']."'
		");

	$verifCompte = $verifCompte->fetch(PDO::FETCH_OBJ);

	if($verifCompte != false){
		var_dump($fr_natif = ($_POST['infoFr'] == "true" ? 1 : 0));
		var_dump($_POST);
		var_dump($dbh->query("UPDATE user SET genre='".$_POST['infoGenre']."',
			profession='".$_POST['infoProf']."',
			fr_natif=".$fr_natif."
			WHERE pseudo='".$_SESSION['pseudo']."'"));
			
	}
	else{
		return '<p class="text-danger"><b>Erreur: Mot de passe incorrect.</b></p>';
	}
}

?>