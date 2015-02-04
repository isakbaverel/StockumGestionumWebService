<?php
class Controller_Index extends Controller_Template {

    protected $categoriesModel;

    protected function __construct() {
        parent::__construct();
        $this->selfModel = new Model_Index();
        $this->categoriesModel = NULL;
        $this->BookModel = new Model_Livre();
    }

    public function cookie_deleted(){
    	$idp = parent::cookie();
    	redirect(ROOT_URL);
    }


	public function index(){
		
		$idp = parent::cookie();
		$nb_articles = $this->selfModel->getPanier($idp);
		if($nb_articles['nb_articles'] == null)
		{
			$nb_articles['nb_articles'] = 0;
		}
		$total = $this->selfModel->getTotal($idp);
		$colonne ="e.vente";
		$sens = "DESC";
        $this->BookModel->setGetExGen($colonne,$sens);
        $livres = $this->BookModel->getAllExGen(0,12,$colonne,$sens);
        $ids = array();
        $i=0;
        foreach ($livres as $livre) {
            $ids[$i]=$livre['ID_Exemplaire'];
            $i++;
        }
        $ids_dedou = array_unique($ids);

        $colonne ="e.ID_Exemplaire";
		$sens = "DESC";
        $this->BookModel->setGetExGen($colonne,$sens);
        $livres_new = $this->BookModel->getAllExGen(0,6,$colonne,$sens);

        $ids_new = array();
        $i=0;
        foreach ($livres_new as $livre) {
            $ids_new[$i]=$livre['ID_Exemplaire'];
            $i++;
        }
        $ids_dedou_new = array_unique($ids_new);
        
    	if(!empty($_COOKIE['panier'])){
    	   $id = $_COOKIE['panier'];
    	}else if(isset($_SESSION['idPanier'])){
    	   $id = $_SESSION['idPanier'];
    	}else{
    		redirect(ROOT_URL);
    	}
		require './View/header.php';
		
		require './View/Index/carrousel.php';
		require './View/sidebar.php';
		require './View/Index/index.php';
		require './View/footer.php';
		require './Scripts/Panier/addPanier.js';
	}

	public function index_admin(){
		if(isset($_SESSION) && $_SESSION['droits']>=LIB_RIGHT){
			$nb=$this->BookModel->getExStockLow();
			$nbu = $this->selfModel->countUsers();
			$nbe = $this->selfModel->countEx();
			$nbv = $this->selfModel->countVentes();
			$nbMsg = $this->selfModel->countMessage();
			$livreLow = $this->selfModel->getExStockLow();
			$topLivres = $this->selfModel->getTopLivre();
			require './View/Back/header.php';
			require './View/Back/sidebar.php';
			require './View/Back/head.php';
			require './View/Back/Index/index.php';
			require './View/Back/footer.php';
		}else{
			redirect(ROOT_URL);
		}
	}

	public function setSettings(){
		if(isset($_SESSION) && $_SESSION['droits']>=LIB_RIGHT){
			$livreLow = $this->selfModel->getExStockLow();
			$nbMsg = $this->selfModel->countMessage();

			$settting =  $this->selfModel->settingPage();

			//var_dump($settting);

			require './View/Back/header.php';
			require './View/Back/sidebar.php';
			require './View/Back/head.php';
			require './View/Back/Index/settings.php';
			require './View/Back/footer.php';
		}else{
			redirect(ROOT_URL);
		}
	}

	public function updateSettings(){
		if(isset($_SESSION) && $_SESSION['droits']>=LIB_RIGHT){
			$livreLow = $this->selfModel->getExStockLow();
			$nbMsg = $this->selfModel->countMessage();

			

			$modif = $this->selfModel->setSettingPage($_POST['quantite']);

			$settting =  $this->selfModel->settingPage();

			require './View/Back/header.php';
			require './View/Back/sidebar.php';
			require './View/Back/head.php';

			if($modif ==1){
				require './View/Back/Book/add_done.php';
			}else{
				require './View/Back/Book/add_fail.php';
			}

			require './View/Back/Index/settings.php';
			require './View/Back/footer.php';
		}else{
			redirect(ROOT_URL);
		}
	}
	
	public function	contact() {
		$idp=parent::cookie();
		$nb_articles = $this->selfModel->getPanier($idp);
		if($nb_articles['nb_articles'] == null)
		{
			$nb_articles['nb_articles'] = 0;
		}
		$type = $this->selfModel->getType();
		$genres = $this ->selfModel->getGenres();
		
		require './View/header.php';
		require './View/Index/contact.php';
		require './View/footer.php';	
	}
	
	public function submit_contact()
	{
		if(isset($_POST['nom']) && isset($_POST['mail']) && isset($_POST['sujet']) && isset($_POST['message'])) {
			$error = array();
	        $valid = array();
			$type = $this->selfModel->getType();
			$genres = $this ->selfModel->getGenres(); 
			$idp = parent::cookie();
			$nb_articles = $this->selfModel->getPanier($idp);
			if($nb_articles['nb_articles'] == null)
			{
				$nb_articles['nb_articles'] = 0;
			}
			
			foreach ($_POST as $key => $value) {
	        	if (isset($value) && !empty($value) && trim(strip_tags($value))!="") {
	            	if ($key === "mail") {
	                	if (validEmail($value)) {
	                    	$valid[$key] = $value;                    	
	                    }
	                    else 
	                    {
	                    	$error[$key] = 'Mail non valide';
	                    }          
	                }
	                else
	                {
	                	$valid[$key]=$value;
	                }
	            }
	            else 
	            {
	            	$error[$key] = ' Vous devez remplir ce champs';
				}
			}
			
			if(!empty($error)) {
				require './View/header.php';
				require './View/Index/contact_failed.php';
	   			require './View/Index/contact.php';
				require './View/footer.php';
			}
			else
			{
				$nom = html($valid['nom']);
				$mail = $valid['mail'];
				$sujet = html($valid['sujet']);
				$message = html($valid['message']);
				$verifMail = $this->selfModel->verifMail($mail);
				$iduser = null;
				if($verifMail)
				{
					$iduser = $verifMail['ID_Utilisateur'];
				}
				$this->selfModel->envoyerMessage($nom,$mail,$sujet,$message,$iduser);
				$valid = array();
				require './View/header.php';
				require './View/Index/contact_success.php';
	   			require './View/Index/contact.php';
				require './View/footer.php';
			}
		}
		else
		{
			redirect(ROOT_URL);
		}	
	}
	
	public function all_message()
	{
		if(isset($_SESSION) && $_SESSION['droits']>=LIB_RIGHT){
			$nbMsg = $this->selfModel->countMessage();
			$livreLow = $this->selfModel->getExStockLow();
			$messages = $this->selfModel->getAllMessage();
			require './View/Back/header.php';
			require './View/Back/sidebar.php';
			require './View/Back/head.php';
			require './View/Back/Index/all_message.php';
			//require './View/Back/Scripts/datatable.php';
			require './View/Back/footer.php';
		}
		else
		{
			redirect(ROOT_URL);
		}
	}
	
	public function getMessageById($id,$reponse,$isUser)
	{
		if(isset($_SESSION) && $_SESSION['droits']>=LIB_RIGHT){
			$nbMsg = $this->selfModel->countMessage();
			$livreLow = $this->selfModel->getExStockLow();
			$messageById = $this->selfModel->getMessageById($id);
			$nbRep = $this->selfModel->countReponse();
			require './View/Back/header.php';
			require './View/Back/sidebar.php';
			require './View/Back/head.php';
			require './View/Back/Index/message_by_id.php';
			require './View/Back/footer.php';
		}
		else
		{
			redirect(ROOT_URL);
		}	
	}
	
	public function getAllReponse()
	{
		if(isset($_SESSION) && $_SESSION['droits']>=LIB_RIGHT){
			$nbMsg = $this->selfModel->countMessage();
			$livreLow = $this->selfModel->getExStockLow();
			$reponses = $this->selfModel->getReponses();
			require './View/Back/header.php';
			require './View/Back/sidebar.php';
			require './View/Back/head.php';
			require './View/Back/Index/message_reponse.php';
			//require './View/Back/Scripts/datatable.php';
			require './View/Back/footer.php';
		}
		else
		{
			redirect(ROOT_URL);
		}
	}
	
	public function setReponse($id,$isUser)
	{
		if(isset($_SESSION) && $_SESSION['droits']>=LIB_RIGHT){
			if(isset($_POST['reponse']) && !empty($_POST['reponse']))
			{
				$this->selfModel->setReponse($id,$_POST['reponse']);
				if($isUser == "non")
				{
					$messageById = $this->selfModel->getMessageById($id);
					$this->send_reponse($_POST['reponse'],$messageById['Email']);
				}
				$nbMsg = $this->selfModel->countMessage();
				$livreLow = $this->selfModel->getExStockLow();
				$reponses = $this->selfModel->getReponses();
				$messages = $this->selfModel->getAllMessage();
				
				require './View/Back/header.php';
				require './View/Back/sidebar.php';
				require './View/Back/head.php';
				require './View/Back/Index/message_success.php';
				require './View/Back/Index/all_message.php';
				//require './View/Back/Scripts/datatable.php';
				require './View/Back/footer.php';
			}
			else
			{
				$nbMsg = $this->selfModel->countMessage();
				$livreLow = $this->selfModel->getExStockLow();
				$messageById = $this->selfModel->getMessageById($id);
				$nbRep = $this->selfModel->countReponse();
				require './View/Back/header.php';
				require './View/Back/sidebar.php';
				require './View/Back/head.php';
				require './View/Back/Index/message_fail.php';
				require './View/Back/Index/message_by_id.php';
				//require './View/Back/Scripts/datatable.php';
				require './View/Back/footer.php';
			}
		}
		else
		{
			redirect(ROOT_URL);
		}
	}
	
	public function send_reponse($reponse,$mail){
		$formMail = $mail;
		if(empty($formMail)){
			return false;
		}
		$formMail =  strip_tags(html_entity_decode($formMail));

        require_once './View/Mail/contact.php';
        $headers = 'From: noreply@'.ROOT_URL;
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        return (mail($formMail, 'No reply : Réponse du support Face2Book1', $text, $headers));
	}
}

