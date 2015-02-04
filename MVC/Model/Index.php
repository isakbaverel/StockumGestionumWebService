<?php

/*
  La classe modèle pour les catégories.
  Le constructeur permet de préparer trois requêtes de sélection de livres. Deux nouvelles propriétés sont définies par rapport à la classe Model/Template ; "selectByAuthor" et "selectByCategory". De la même manière, on retrouve plus loin les méthodes pour exécuter et récupérer les résultats de ces requêtes.
 */

class Model_Index extends Model_Template {
	
	protected $selectType;
    protected $selectGenres;
    protected $insertPanier;
    protected $getPanier;
    protected $getTot;
    protected $envoyerMessage;
    protected $getLignePanier;
    protected $countUsers;
    protected $countEx;
    protected $verfiMail;
    protected $countMessage;
    protected $getExStockLow;
    protected $getAllMessage;
    protected $getMessageById;
    protected $countReponse;
    protected $getReponses;
    protected $setReponse;
    protected $getQuantitePage;
    protected $setQuantitePage;
    protected $getTopLivre;

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

        $sql='SELECT * FROM setting_page LIMIT 0,1';
        $this->getQuantitePage = Controller_Template::$db->prepare($sql);

        $sql='UPDATE setting_page SET quantite = ?';
        $this->setQuantitePage = Controller_Template::$db->prepare($sql);

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

        $sql='SELECT COUNT(*) AS nb FROM utilisateur';
        $this->countUsers = Controller_Template::$db->prepare($sql);
        
        $sql = 'SELECT * FROM Utilisateur WHERE mail = ?';
        $this->verifMail = Controller_Template::$db->prepare($sql);
        
        $sql='INSERT INTO contact (Nom,Email,Sujet,Message,ID_Utilisateur) VALUES (:nom,:email,:sujet,:message,:iduser)';
        $this->envoyerMessage=Controller_Template::$db->prepare($sql);
        
        $sql='SELECT *
	        	FROM panier AS pan, ligne_panier AS lp, exemplaire AS ex, tva AS taux, livre AS book, image_exemplaire AS imgex, code_promo AS cp
	        	WHERE pan.ID_Panier = lp.ID_Panier AND pan.ID_Panier = ? AND lp.ID_Exemplaire = ex.ID_Exemplaire AND ex.ID_Taux = taux.ID_Taux AND ex.ID_Livre = book.ID_Livre AND imgex.ID_Exemplaire = ex.ID_Exemplaire AND pan.ID_CodePromo = cp.ID_CodePromo';
		$this->getLignePanier = Controller_Template::$db->prepare($sql);

        $sql='SELECT COUNT(*) AS nb FROM exemplaire WHERE publie=1';
        $this->countEx = Controller_Template::$db->prepare($sql);

        $sql= 'SELECT SUM(vente) as ventes FROM exemplaire';
        $this->countVentes = Controller_Template::$db->prepare($sql);
		
		$sql = 'SELECT COUNT(*) AS nb FROM contact WHERE Reponse IS NULL';
		$this->countMessage = Controller_Template::$db->prepare($sql);
		
		$sql = 'SELECT count(*) AS nb FROM exemplaire WHERE stock < seuil_reaprovisonnement OR stock <= 0 OR ID_etat = 4';
		$this->getExStockLow = Controller_Template::$db->prepare($sql);
		
		$sql = 'SELECT * from contact ORDER BY date DESC';
		$this->getAllMessage = Controller_Template::$db->prepare($sql);
		
		$sql = 'SELECT * FROM contact WHERE ID_Contact = ?';
		$this->getMessageById = Controller_Template::$db->prepare($sql);
		
		$sql = 'SELECT COUNT(*) AS nb FROM contact WHERE Reponse IS NOT NULL';
		$this->countReponse = Controller_Template::$db->prepare($sql);
		
		$sql = 'SELECT * FROM contact WHERE Reponse IS NOT NULL ORDER BY date DESC';
		$this->getReponses = Controller_Template::$db->prepare($sql);
		
		$sql = 'UPDATE contact SET Reponse = ? WHERE ID_Contact = ?';
		$this->setReponse = Controller_Template::$db->prepare($sql);
		
		$sql = 'SELECT *
				FROM exemplaire as ex, livre as li, image_exemplaire as img, ecrire as ec, auteur as au, etat as et
				WHERE ex.ID_Livre = li.ID_Livre AND ex.ID_Exemplaire = img.ID_Exemplaire AND li.ID_livre = ec.ID_Livre AND ec.ID_Auteur = au.ID_Auteur AND ex.ID_etat = et.ID_etat
				ORDER BY ex.vente DESC
				LIMIT 0,3';
		$this->getTopLivre = Controller_Template::$db->prepare($sql);
    }
    
    public function getTopLivre() {
	    $this->getTopLivre->execute();
	    return $this->getTopLivre->fetchAll();
    }
    
    public function setReponse($id,$reponse) {
	    $this->setReponse->execute(array($reponse,$id));
	    return $this->setReponse->rowCount();
    }
    
    public function getReponses() {
	    $this->getReponses->execute();
	    return $this->getReponses->fetchAll();
    }
    
    public function countReponse() {
	    $this->countReponse->execute();
	    return $this->countReponse->fetch();
    }
    
    public function getMessageById($id) {
	    $this->getMessageById->execute(array($id));
	    return $this->getMessageById->fetch();
    }
    
    public function getAllMessage() {
    	$this->getAllMessage->execute();
    	return $this->getAllMessage->fetchAll();
    }
    
    public function getExStockLow() {
		$this->getExStockLow->execute();
		return $this->getExStockLow->fetch();
	}
    
    public function countMessage() {
	    $this->countMessage->execute();
        return $this->countMessage->fetch();
    }
    
    public function verifMail($mail) {
	    $this->verifMail->execute(array($mail));
	    return $this->verifMail->fetch();
    }

    public function countVentes(){
        $this->countVentes->execute();
        return $this->countVentes->fetch();
    }

    public function countEx(){
        $this->countEx->execute();
        return $this->countEx->fetch();
    }

    public function countUsers(){
        $this->countUsers->execute();
        return $this->countUsers->fetch();
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
    
    public function envoyerMessage($nom,$mail,$sujet,$message,$iduser) {
	    $this->envoyerMessage->execute(array('nom'=>$nom,'email'=>$mail,'sujet'=>$sujet,'message'=>$message,'iduser'=>$iduser));
    }
    
    public function getLignePanier($id) {
	    $this->getLignePanier->execute(array($id));
	    return $this->getLignePanier->fetchAll();
    }

    public function settingPage(){
        $this->getQuantitePage->execute();
        return $this->getQuantitePage->fetch();
    }

    public function setSettingPage($nb){
        $this->setQuantitePage->execute(array($nb));
        return $this->setQuantitePage->rowCount();
    }
}