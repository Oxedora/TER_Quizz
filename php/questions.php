<?php session_start(); ?>
<html>
<head>
    <title>Question</title>
    <script src="./script/script.js"></script>
</head>
<body>
    <center>
        <section>
            <?php
				if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] == false){// si non connecté
					// rediriger vers la page de connection
					echo "Veuillez aller sur la page d'accueil pour vous connecter ou vous inscrire.";
					exit;
				}
				$pseudo = $_SESSION['pseudo'];
				if (!isset($_SESSION["nbQuestionRestantes"])){// on initialise le nombre de question pour la série
					$_SESSION["nbQuestionRestantes"] = 10;
				}
				include "Question.php";
                while ($_SESSION["nbQuestionRestantes"] > 0) {// tant qu'il reste des questions
                    if (isset($_SESSION["question"]) & isset($_POST)){//si l'utilisateur viens de répondre
					   $q = unserialize($_SESSION["question"]);//on récupère la question
					   $q->enregistrerReponses();//et on met à jour la bdd
				    }
				    elseif (isset($_SESSION["nbQuestionRestantes"])){
					   if($_SESSION["nbQuestionRestantes"] == 0){
				    		//questionnaire fini. rediriger vers stats ?
						   	header("refresh: 5; Location: statistiques.php");
							echo "Vous avez fini le questionnaire vous allez être redirigé vers les statistiques.";
					   }
					   else{//on pose une question
						  $_SESSION["nbQuestionRestantes"]--;
						  $q = new Question($pseudo);//pseudo utilisateur
						  if (!$q->existe()){
							  $_SESSION["nbQuestionRestantes"] = 0;
							  header("refresh: 5; Location: statistiques.php");
							  echo "Vous avez répondu à toutes les questions disponibles. Vous allez être redirigé vers les statistiques.";
						  }
						  else{
                		  	$q->afficheQ();
						  }
					   }
				    }
                }
            ?>
        </section>
    </center>
</body>
</html>