<?php
/*
 * Ce fichier sert à lire la requête envoyée par un client (l'URL donc), et instancier les classes Contrôleur correspondantes
 * De ce fait, il faut vérifier les valeurs passées dans l'URL (variable $_GET en PHP) et s'assurer qu'elles sont bien écrites avant de réaliser les instanciations.
 * Plusieurs valeurs successives peuvent être utilisées, mais il faut bien faire attention à gérer tout les cas, notamment les cas par défaut lorsqu'on utilise l'instruction switch de PHP. 
 */

//error_reporting(0);  //gestion des erreurs et des warnings


// GESTION UTILISATEUR (COOKIE et/ou SESSION) ici :
session_start();




// Constantes 
require_once 'mode.php';
if(!PROD){
	define("PROD_ROOT", "http://localhost/fil-rouge/trunk/MVC/"); 
}else{
	define("PROD_ROOT", "http://192.168.172.145/fil-rouge/trunk/MVC/"); 
}


define("ROOT", PROD_ROOT);  // cette variable globale designe http://adresse_du_site.fr/
define("ROOT_URL", ROOT.'index.php'); // cette variable globale designe http://adresse_du_site.fr/index.php/
define("ROOT_LONG", ""); 
set_include_path(get_include_path() . PATH_SEPARATOR . ROOT_LONG);

//fonctions 
require 'functions.php';

define("_S","cd8b398f0d31e84507325e8339545b6e3a979d42789ff357dca18b032c0550a6"); //important à ne pas effacer


spl_autoload_register('generic_autoload'); //fonction permettant d'utiliser les objets en PHP


// Instanciation de la connexion à la BDD
//mysql_query("SET NAMES UTF8");

Controller_Template::$db = new MyPDO('mysql:host=localhost;dbname=fil_rouge', 'root', 'root', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')); // changer le nom de la base de donnée + rajouter mdp
//Controller_Template::$db = new MyPDO('mysql:host=localhost;dbname=prod_face2book', 'prod_face2book', ''); // changer le nom de la base de donnée + rajouter mdp


// Analyse de la requête HTTP (route)
if(empty($_GET) OR empty($_GET['module'])){ // on regarde si il y a un module de spécifié dans la requete
	$controller = Controller_Index::getInstance('Index');
	$controller->index();
}else{
	switch($_GET['module']){
		case 'index':
				$controller = Controller_Utilisateurs::getInstance('Index');
				$controller->index();
			break;
		case 'user' : 
			if (!empty($_GET['action'])) {
				switch ($_GET['action']) {
					case 'register':
						$controller = Controller_Utilisateurs::getInstance('Utilisateurs');
						$controller->register_form();
						break;
					case 'display':
						if(!empty($_GET['id']) && ctype_digit($_GET['id'])) {
							$controller = Controller_Utilisateurs::getInstance('Utilisateurs');
							$controller->display($_GET['id']);
						}else{
							$controller = Controller_Error::getInstance('Error');
							$controller->documentNotFound("Problème d'écriture de la requête (ID non valide)");
						}
						break;
				}
			}
			else {
				$controller = Controller_Error::getInstance('Error');
				$controller->documentNotFound("Problème d'écriture de la requête (ACTION non spécifiée)");
			}
			break;
		default:
			$controller = Controller_Index::getInstance('Index');
			$controller->index();
			break;
	}
}

?>

