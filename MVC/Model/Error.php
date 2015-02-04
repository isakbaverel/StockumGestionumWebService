<?php

/*
  La classe modèle pour les catégories.
  Le constructeur permet de préparer trois requêtes de sélection de livres. Deux nouvelles propriétés sont définies par rapport à la classe Model/Template ; "selectByAuthor" et "selectByCategory". De la même manière, on retrouve plus loin les méthodes pour exécuter et récupérer les résultats de ces requêtes.
 */

class Model_Error extends Model_Template {
	
	protected $selectType;
    protected $selectGenres;
    protected $insertPanier;
    protected $getPanier;
    protected $getTot;
    protected $envoyerMessage;
    protected $getLignePanier;

	public function __construct() {
        parent::__construct();

        $sql='SELECT ID_Famille, libelle FROM famille';
        $this->selectType = Controller_Template::$db->prepare($sql);

        $sql='SELECT DISTINCT m.texte AS GENRE
                FROM famille f, mot_cle m, exemplaire e, definir d
                WHERE f.ID_Famille = m.ID_Famille
                AND d.ID_livre = e.ID_Livre
                AND m.ID_Mot = d.ID_Mot
                AND LOWER( f.libelle ) LIKE "genre"
                LIMIT 0 , 30';
        $this->selectGenres = Controller_Template::$db->prepare($sql);

        $sql='SELECT *
                FROM panier AS pan, ligne_panier AS lp, exemplaire AS ex, tva AS taux, livre AS book, image_exemplaire AS imgex, code_promo AS cp
                WHERE pan.ID_Panier = lp.ID_Panier AND pan.ID_Panier = ? AND lp.ID_Exemplaire = ex.ID_Exemplaire AND ex.ID_Taux = taux.ID_Taux AND ex.ID_Livre = book.ID_Livre AND imgex.ID_Exemplaire = ex.ID_Exemplaire AND pan.ID_CodePromo = cp.ID_CodePromo';
        $this->getLignePanier = Controller_Template::$db->prepare($sql);

        $sql='SELECT lp.quantite AS quantite, ex.prix AS prix, taux.tauxTVA AS TVA
				FROM panier AS p, ligne_panier AS lp, exemplaire as ex, tva AS taux
				WHERE p.ID_Panier = lp.ID_Panier AND p.ID_Panier = ? AND lp.ID_Exemplaire = ex.ID_Exemplaire AND ex.ID_Taux = taux.ID_Taux';
        $this->getTot=Controller_Template::$db->prepare($sql);
        
        $sql= 'INSERT IGNORE INTO panier(ID_Utilisateur) VALUES(NULL)';
        $this->insertPanier = Controller_Template::$db->prepare($sql);

        $sql='SELECT SUM(quantite) AS nb_articles
                FROM ligne_panier
                WHERE ID_panier = ?';
        $this->getPanier=Controller_Template::$db->prepare($sql);
        
        $sql='INSERT INTO contact (Nom,Email,Sujet,Message) VALUES (:nom,:email,:sujet,:message)';
        $this->envoyerMessage=Controller_Template::$db->prepare($sql);
    }

    public function getType() {
    	$this->selectType->execute();
    	return $this->selectType->fetchAll();
    }

    public function getGenres(){
        $this->selectGenres->execute();
        return $this->selectGenres->fetchAll();
    }

    public function createPanier(){
        $this->insertPanier->execute();
        return Controller_Template::$db->lastInsertId();
    }

    public function getPanier($panier){
        $this->getPanier->execute(array($panier));
        return  $this->getPanier->fetch();
    }

    public function getTotal($panier){
        $this->getTot->execute(array($panier));
        return  $this->getTot->fetchAll();
    }
    
    public function envoyerMessage($nom,$mail,$sujet,$message) {
	    $this->envoyerMessage->execute(array('nom'=>$nom,'email'=>$mail,'sujet'=>$sujet,'message'=>$message));
    }

    public function getLignePanier($id) {
        $this->getLignePanier->execute(array($id));
        return $this->getLignePanier->fetchAll();
    }
}