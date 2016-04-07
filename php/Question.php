<?php

class Question implements Serializable{
    protected $id;
    protected $type_question;
    protected $enonce_question;
    protected $enonce_reponse;
    protected $reponse_donnee;
    protected $temp_reponse;
    protected $bdd;
	protected $pseudo;
    
	public function serialize() {
		return serialize(array("id"=>$this->id, "type_question"=>$this->type_question, "enonce_question"=>$this->enonce_question, "enonce_reponse"=>$this->enonce_reponse, "reponse_donnee"=>$this->reponse_donnee, "temp_reponse"=>$this->temp_reponse, "pseudo"=>$this->pseudo));
	}
	public function unserialize($data) {
		$temp = unserialize($data);
		$this->id = $temp["id"];
		$this->type_question = $temp["type_question"];
		$this->enonce_question = $temp["enonce_question"];
		$this->enonce_reponse = $temp["enonce_reponse"];
		$this->reponse_donne = $temp["reponse_donne"];
		$this->temp_reponse = $temp["temp_reponse"];
		$this->pseudo = $temp["pseudo"];
		
		try{
            $this->bdd = new PDO('mysql:host=localhost;dbname=slyntcom_projet', 'slyntcom_projet', 'projet-um',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        }
        catch( PDOException $Exception ) {
            //rediriger vers une page erreur
            echo "la connection à la bdd a échoué";
            exit();
        }
	}
	
    function __construct($pseudo){
		$this->pseudo = $pseudo;
        $this->enonce_question = array();
        $this->enonce_reponse = array();
        $this->reponse_donnee = array();
        
        //accès à la base de donnée
        try{
            $this->bdd = new PDO('mysql:host=localhost;dbname=slyntcom_projet', 'slyntcom_projet', 'projet-um',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        }
        catch( PDOException $Exception ) {
            //rediriger vers une page erreur
            echo "la connection à la bdd a échoué";
            exit();
        }
        
        //selection d'une seule question non répondue par $pseudo
        $requete = 'SELECT * FROM question WHERE id IN (SELECT distinct id FROM reponses WHERE pseudo_user!= :pseudo) LIMIT 1;';
        $question = $this->bdd->prepare($requete);
        $question->bindValue(':pseudo',$this->pseudo,PDO::PARAM_STR);
        $question->execute();
        
        $data = $question->fetch(PDO::FETCH_ASSOC);
        $question->closeCursor();
        
        //si la requête ne donne pas de résultat c'est qu'il n'y a plus de nouvelles question disponibles pour $pseudo
        if($data === false){
           	$this->id = -1;
        }
        
        //on récuper l'id de la question et le paramètre ordonne
        $this->id = $data['id'];
        $this->type_question = $data['type_question'];
		
        //on récupère les énoncés de la question trouvée plus haut
        $requete = 'SELECT * FROM enonce_question WHERE id_question= :q_id';
        $enonce = $this->bdd->prepare($requete);
        $enonce->bindValue(':q_id',$this->id,PDO::PARAM_INT);
        $enonce->execute();
               
        while ($data = $enonce->fetch(PDO::FETCH_ASSOC)){
            array_push($this->enonce_question,array("id" => $data['id'],"type_contenu" => $data['type_contenu'],"contenu" => $data['contenu']));
        }
        $enonce->closeCursor();
        
        //on récupère les énoncés de réponse de la question trouvée plus haut
        $requete = 'SELECT * FROM enonce_reponse WHERE id_question= :q_id';
        $enonceRep = $this->bdd->prepare($requete);
        $enonceRep->bindValue(':q_id',$this->id,PDO::PARAM_INT);
        $enonceRep->execute();
               
        while ($data = $enonceRep->fetch(PDO::FETCH_ASSOC)){
            array_push($this->enonce_reponse,array("id" => $data['id'],"type_contenu" => $data['type_contenu'],"contenu" => $data['contenu']));
        }
        $enonce->closeCursor();        
    }
    
    public function existe(){// renvoie true si il existe une question auquel l'utilisateur n'a pas répondu
        if ($this->id == -1){
			return false;
		}
		else{
			return true;
		}
    }
    
    public function afficheQ(){
        foreach($this->enonce_question as $value){
            if($value["type_contenu"]==="texte"){
                echo "<p class='enonce'>".$value["contenu"]."</p></br>";
            }
            else if($value["type_contenu"]==="image"){
                echo "<img class='enonce' src=".$value["contenu"]."></br>";
            }
        }
		
		//gestion du chrono
		//voir comment gérer le temps max par question selon le type de question;
		echo "<script>tempsMax = 4500;
		window.onload = DemarrerChrono();
		</script>";
		
        echo "<form method='post' action='".$_SERVER['PHP_SELF']."' onsubmit='ArreterChrono(); ChronoInInput()'>";
		echo "<input type='hidden' name='temps' value='0'>";
        foreach ($this->enonce_reponse as $value){
            if($value["type_contenu"]==="texte"){
                echo "<input type='radio' name='question' value=".$value["contenu"].">".$value["contenu"]."</br>";
            }
            else{
                //gérer les images
            }
        }
        echo "<input name='subbut' type='submit'>";
        echo "</form>";
		
		$_SESSION["question"] = serialize($this);
    }
	
	public function enregistrerReponses(){// fonctionne seulement pour le type_question simple (radio bouton, 1 seule réponse)
		$rep;
		//on récupère l'id de la réponse
		foreach($this->enonce_reponse as $value){
			if($_POST['question'] == $value['contenu']){		
				$rep = $value['id'];
			}
		}	
		
		try{
			$this->bdd->beginTransaction();

			//insertion dans reponse
			$requete = 'INSERT INTO reponses (pseudo_user, temps_reponse) VALUES (:pseudo, :temps);';
			$insertrep = $this->bdd->prepare($requete);
			$insertrep->bindValue(':pseudo',$this->pseudo,PDO::PARAM_STR);
			$insertrep->bindValue(':temps',$_POST['temps'],PDO::PARAM_INT);
			$insertrep->execute();

			//on doit récupérer l'id de l'entrée précédente dans la table réponse
			$requete = 'SELECT id FROM reponses WHERE pseudo_user= :pseudo ORDER BY id DESC LIMIT 1;';
			$requeteID = $this->bdd->prepare($requete);
			$requeteID->bindValue(':pseudo',$this->pseudo,PDO::PARAM_STR);
			$requeteID->execute(); 

			$data = $requeteID->fetch(PDO::FETCH_ASSOC);
			$requeteID->closeCursor();

			//insertion dans reponse_donnee
			$requete = 'INSERT INTO reponse_donnee (id_reponses, id_enonce_reponse, ordre) VALUES (:id_q, :id_rep, :ordonne);';
			$insertreps = $this->bdd->prepare($requete);
			$insertreps->bindValue(':id_q',$this->id,PDO::PARAM_INT);
			$insertreps->bindValue(':id_rep',$rep,PDO::PARAM_INT);
			$insertreps->bindValue(':ordonne',NULL,PDO::PARAM_INT);//NULL dans ce type de question
			$insertreps->execute();
			
			$this->bdd->commit();
		}
		catch(PDOException $Exception){
			$this->bdd->rollBack();
		}
	}
}

function mainQuestionnaire(){
	echo "<script src='../script/script.js'></script>";
	if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] == false){// si non connecté
		// rediriger vers la page de connection
		echo "Veuillez vous rendre sur la page d'accueil pour vous connecter ou vous inscrire.";
	}
	else{
		$pseudo = $_SESSION['pseudo'];

		if (!isset($_SESSION["nbQuestionRestantes"])){// on initialise le nombre de question pour la série
			$_SESSION["nbQuestionRestantes"] = 10;
		}

		while ($_SESSION["nbQuestionRestantes"] >= 0) {// tant qu'il reste des questions
			if (isset($_SESSION["question"]) && isset($_POST)){//si l'utilisateur viens de répondre
				$q = unserialize($_SESSION["question"]);//on récupère la question
				$q->enregistrerReponses();//et on met à jour la bdd
				unset($_SESSION["question"]);
			}

			elseif (isset($_SESSION["nbQuestionRestantes"])){

			   if($_SESSION["nbQuestionRestantes"] == 0){
					unset($_SESSION["nbQuestionRestantes"]);
					//questionnaire fini. rediriger vers stats ?
					header("refresh: 5; Location: statistiques.php");
					echo "Vous avez fini le questionnaire vous allez être redirigé vers les statistiques.";
			   }

			   else{//on pose une question
				  $_SESSION["nbQuestionRestantes"]--;
				  $q = new Question($pseudo);//pseudo utilisateur

				  if (!$q->existe()){
					  unset($_SESSION["nbQuestionRestantes"]);
					  header("refresh: 5; Location: statistiques.php");
					  echo "Vous avez répondu à toutes les questions disponibles. Vous allez être redirigé vers les statistiques.";
				  }

				  else{
					$q->afficheQ();
				  }
			   }
			}
		}
	}
	
}

?>