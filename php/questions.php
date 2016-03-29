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
				$pseudo = "patate";
				include "Question.php";
				if (isset($_SESSION["question"])){
					$q = unserialize($_SESSION["question"]);
					$q->enregistrerReponses();
				}
				elseif (isset($_SESSION["nbQuestionRestantes"])){
					if($_SESSION["nbQuestionRestantes"] == 0){
						//questionnaire fini. rediriger vers stats ?
					}
					else{
						echo "autruche";
						$_SESSION["nbQuestionRestantes"]--;
						$q = new Question($pseudo);//pseudo utilisateur
                		$q->afficheQ();
					}
				}
				else{
					$_SESSION["nbQuestionRestantes"] = 1; //nombre de question par série (initialisation)
					
					$q = new Question($pseudo);//pseudo utilisateur
					$_SESSION["nbQuestionRestantes"]--;
                	$q->afficheQ();
				}
            ?>
        </section>
    </center>
</body>
</html>