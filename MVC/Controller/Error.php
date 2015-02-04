<?php
 class Controller_Error extends Controller_Template{

	protected $categoriesModel;

    protected function __construct() {
        parent::__construct();
        $this->selfModel = new Model_Error();
        $this->categoriesModel = NULL;
    }

	public  function documentNotFound($title){
		header('HTTP/1.1 404 Not Found');
		header('Content-Type: text/html; charset=utf-8');
		$idp = parent::cookie();
		if(!empty($_COOKIE['panier'])){
    	   $id = $_COOKIE['panier'];
    	}else if(isset($_SESSION['idPanier'])){
    	   $id = $_SESSION['idPanier'];
    	}else{
    		redirect(ROOT_URL);
    	}
		$nb_articles = $this->selfModel->getPanier($idp);
		if($nb_articles['nb_articles'] == null)
		{
			$nb_articles['nb_articles'] = 0;
		}
		$total = $this->selfModel->getTotal($idp);

		$home = array("active" => false , "lien" => ROOT_URL, "nom" =>"Accueil");
        $reg = array("active" => true, "lien" => ROOT_URL.'?module=user&action=register' , "nom" => "Erreur");
        $menu = array("home" => $home,"reg" => $reg);
		require './View/header.php';
        require './View/sidebar.php';
        require './View/navbar.php';
        require './View/error/index.tpl'; 
        require './View/footer.php';
	}
}

