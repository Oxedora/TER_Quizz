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
        $question = $this->bdd->query('SELECT * FROM question WHERE id IN (
            SELECT distinct id FROM reponses WHERE pseudo_user!="'.$pseudo.'") LIMIT 1;');
        
        $data = $question->fetch(PDO::FETCH_ASSOC);
        $question->closeCursor();
        
        //si la requête ne donne pas de résultat c'est qu'il n'y a plus de nouvelles question disponibles pour $pseudo
        if($data === false){
            //rediriger vers stats ?
            echo "vous avez répondu à toutes nos questions";
			$_SESSION["nbQuestionRestantes"] = 0;
			header($_SERVER['PHP_SELF']);
        }
        
        //on récuper l'id de la question et le paramètre ordonne
        $this->id = $data['id'];
        $this->type_question = $data['type_question'];
		
        //on récupère les énoncés de la question trouvée plus haut
        $enonce = $this->bdd->query('SELECT * FROM enonce_question WHERE id_question="'.$this->id.'"');
               
        while ($data = $enonce->fetch(PDO::FETCH_ASSOC)){
            array_push($this->enonce_question,array("id" => $data['id'],"type_contenu" => $data['type_contenu'],"contenu" => $data['contenu']));
        }
        $enonce->closeCursor();
        
        //on récupère les énoncés de réponse de la question trouvée plus haut
        $enonceRep = $this->bdd->query('SELECT * FROM enonce_reponse WHERE id_question="'.$this->id.'"');
               
        while ($data = $enonceRep->fetch(PDO::FETCH_ASSOC)){
            array_push($this->enonce_reponse,array("id" => $data['id'],"type_contenu" => $data['type_contenu'],"contenu" => $data['contenu']));
        }
        $enonce->closeCursor();        
    }
    
    public function getId(){
        return $this->id;
    }
    
    public function getOrdonnne(){
        return $this->ordonne;
    }
    
    public function getEnonceQ(){
        return $this->enonce_question;
    }
    
    public function getEnonceR(){
        return $this->enonce_reponse;
    }
    
    public function setTempsReponse($temps){
        $this->temps_reponse = $temps;
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
		echo "<script>tempsMax = 1000;
		window.onload = DemarrerChrono();
		</script>";
		
        echo "<form method='post' action='' onsubmit='ArreterChrono(); ChronoInInput()'>";
		echo "<input type='hidden' name='temps' value='0'>";
        foreach ($this->enonce_reponse as $value){
            if($value["type_contenu"]==="texte"){
                echo "<input type='radio' name='question' value=".$value["contenu"]." checked>".$value["contenu"]."</br>";
            }
            else{
                //gérer les images
            }
        }
        echo "<input name='subbut' type='submit'>";
        echo "</form>";
		
		$_SESSION["question"] = serialize($this);
    }
	
	public function enregistrerReponses(){
		$rep;
		//on récupère l'id de la réponse
		foreach($this->enonce_reponse as $value){
			if($_POST['question'] == $value['contenu']){		
				$rep = $value['id'];
			}
		}
		
		//insertion dans reponse
		$insertrep = $this->bdd->exec('INSERT INTO reponses (pseudo_user, temps_reponse) VALUES ("'.$this->pseudo.'", "'.$_POST["temps"].'");');
		
		//on doit récupérer l'id de l'entrée précédente dans la table réponse
		$requeteID = $this->bdd->query('SELECT id FROM reponses WHERE pseudo_user="'.$this->pseudo.'" ORDER BY id DESC LIMIT 1;');
		$data = $requeteID->fetch(PDO::FETCH_ASSOC);
		$requeteID->closeCursor();
		
		//insertion dans reponse_donnee
		$insertreps = $this->bdd->exec('INSERT INTO reponse_donnee (id_reponses, id_enonce_reponse, ordre) VALUES ("'.$data['id'].'", "'.$rep.'", "'.NULL.'");');
	}
}

?>