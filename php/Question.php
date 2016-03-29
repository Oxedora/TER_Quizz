<?php

class Question{
    protected $id;
    protected $ordonne;
    protected $enonce_question;
    protected $enonce_reponse;
    protected $reponse_donnee;
    protected $temp_reponse;
    protected $bdd;
	protected $pseudo;
    
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
            exit();
        }
        
        //on récuper l'id de la question et le paramètre ordonne
        $this->id = $data['id'];
        $this->ordonne = $data['ordonne'];
        
        //on récupère les énoncés de la question trouvée plus haut
        $enonce = $this->bdd->query('SELECT * FROM enonce_question WHERE id_question="'.$this->id.'"');
               
        while ($data = $enonce->fetch(PDO::FETCH_ASSOC)){
            array_push($this->enonce_question,array($data['id'],$data['type_contenu'],$data['contenu']));
        }
        $enonce->closeCursor();
        
        //on récupère les énoncés de réponse de la question trouvée plus haut
        $enonceRep = $this->bdd->query('SELECT * FROM enonce_reponse WHERE id_question="'.$this->id.'"');
               
        while ($data = $enonceRep->fetch(PDO::FETCH_ASSOC))
        {
            array_push($this->enonce_reponse,array($data['id'],$data['type_contenu'],$data['contenu']));
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
    
    public function setReponse($reponses){
        $this->reponse_donnee = $reponses;
    }
    
    public function setTempsReponse($temps){
        $this->temps_reponse = $temps;
    }
    
    public function afficheQ(){
        foreach($this->enonce_question as $value){
            if($value[1]==="texte"){
                echo "<p class='enonce'>".$value[2]."</p></br>";
            }
            else if($value[1]==="image"){
                echo "<img class='enonce' src=".$value[2]."></br>";
            }
        }
        echo "<form method='post' action=''>";
        foreach ($this->enonce_reponse as $value){
            if($value[1]==="texte"){
                echo "<input type='radio' name='question' value=".$value[2].">".$value[2]."</br>";
            }
            else{
                //gérer les images
            }
        }
        echo "<input type='submit'>";
        echo "</form>";
		
		//gestion du chrono
		echo "tempsMax = 10;" //voir comment gérer le temps max par question selon le type de question;
		echo "<script>window.onload = DemarrerChrono();</script>"
    }
	
	public function enregistrerReponses(){
		
		//on récupère l'id de la réponse
		if($POST['question'] == 'Vrai'){
			foreach($this->enonce_reponse as $value){
				if ($value['contenu'] == 'Vrai'){
					$rep = $value['id'];
				}
			}
		}
		else{
			foreach($this->enonce_reponse as $value){
				if ($value['contenu'] == 'Faux'){
					$rep = $value['id'];
				}
			}
		}
		
		//insertion dans reponse
		$insertrep = $this->bdd->exec('INSERT INTO reponses (pseudo_user, temps_reponse) VALUES ("'.$this->pseudo.'", "'$this->temp_reponse'");');
		
		//on doit récupérer l'id de l'entrée précédente dans la table réponse
		$requeteID = $this->bdd->query('SELECT id FROM reponses WHERE pseudo_user="'.$this->pseudo.'" ORDER BY id DESC LIMIT 1;');
		$data = $requeteID->fetch(PDO::FETCH_ASSOC);
		$requeteID->closeCursor();
		
		//insertion dans reponse_donnee
		$insertreps = $this->bdd->exec('INSERT INTO reponse_donnee (id_reponses, id_enonce_reponse, ordre) VALUES ("'$data['id']'", "'$rep'", "'NULL'");');
	}
}

?>