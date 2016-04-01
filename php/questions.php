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
				$pseudo = "testChrono3";
				include "Question.php";
                while ($_SESSION["nbQuestionRestantes"] > 0) {// tant qu'il reste des questions
                    if (isset($_SESSION["question"]) & isset($_POST)){//si l'utilisateur viens de répondre
					   $q = unserialize($_SESSION["question"]);//on récupère la question
					   $q->enregistrerReponses();//et on met à jour la bdd
				    }
				    elseif (isset($_SESSION["nbQuestionRestantes"])){
					   if($_SESSION["nbQuestionRestantes"] == 0){
				    		//questionnaire fini. rediriger vers stats ?
					   }
					   else{//on pose une question
						  $_SESSION["nbQuestionRestantes"]--;
						  $q = new Question($pseudo);//pseudo utilisateur
                		  $q->afficheQ();
					   }
				    }
				    else{//si le nombre de question restantes n'exite pas c'est qu'on viens ariver sur la page
					   $_SESSION["nbQuestionRestantes"] = 1; //nombre de question par série (initialisation)
					   $q = new Question($pseudo);//pseudo utilisateur
					   $_SESSION["nbQuestionRestantes"]--;
                	   $q->afficheQ();
				    }
                }
            ?>
        </section>
    </center>
</body>
</html>