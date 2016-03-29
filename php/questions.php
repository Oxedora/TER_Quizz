<?php session_start(); ?>
<html>
<head>
    <title>Question</title>
    
</head>
<body>
    <center>
        <section>
            <?php
				include "question.php";
				if (isset($_SESSION["question"])){
					$q = unserialize($_SESSION["question"]);
					$q->enregistrerReponses();
				}
				else if (isset($_SESSION["nbQuestionRestantes"])){
					if($_SESSION["nbQuestionRestantes"] == 0){
						//questionnaire fini. rediriger vers stats ?
					}
					else{
						$_SESSION["nbQuestionRestantes"]--;
						$q = new Question("gérard");//pseudo utilisateur
                		$q->afficheQ();
					}
				}
				else{
					$_SESSION["nbQuestionRestantes"] = 1; //nombre de question par série (initialisation)
				}
            ?>
        </section>
    </center>
</body>
</html>