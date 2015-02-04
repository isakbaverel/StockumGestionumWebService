<?php
/*
 * Fonction de formatage à utiliser dans le code PHP  lorsqu'on écrit des données dans la BDD
 */

/*function html($string) {
    return trim(htmlentities(htmlspecialchars(strip_tags($string), ENT_QUOTES)));
}*/

function html($string) {
    return trim((htmlspecialchars(strip_tags($string), ENT_QUOTES)));
}

/***
    Fonction de formatage à utiliser dans le code PHP lorsqu'on génère des fichiers HTML
*/
function mys($string) {
    return html_entity_decode(htmlentities(utf8_decode(strip_tags($string))));
}

function url_format($string) {
    $string = str_replace(' ', '-', strtolower($string));
    return html(preg_replace('#[^a-z0-9-]#', '', $string));
}

function redirect($url){
    header('Location: '.$url);
    //die();
}

function redirect_timer($url, $timer){
    sleep($timer);
    redirect($url);
}



/*
 * Permet de gérer l'inclusion automatique de fichiers en fonction du nom de la classe appelée dans le code PHP
 * EX: $this->selfModel = new Model_Book();
 * Dans cet exemple l'appel à la classe Model_Book permet de charger le fichier 'Book.php' du dossier 'Model'
 */

function generic_autoload($class) {
    require_once str_replace('_', '/', $class) . '.php';
}

// vérfie si un mail est valide
function validEmail($email) {
    $isValid = true;
    $atIndex = strrpos($email, "@");
    if (is_bool($atIndex) && !$atIndex) {
        $isValid = false;
    } else {
        $domain = substr($email, $atIndex + 1);
        $local = substr($email, 0, $atIndex);
        $localLen = strlen($local);
        $domainLen = strlen($domain);
        if ($localLen < 1 || $localLen > 64) {
            // local part length exceeded
            $isValid = false;
        } else if ($domainLen < 1 || $domainLen > 255) {
            // domain part length exceeded
            $isValid = false;
        } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
            // local part starts or ends with '.'
            $isValid = false;
        } else if (preg_match('/\\.\\./', $local)) {
            // local part has two consecutive dots
            $isValid = false;
        } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
            // character not valid in domain part
            $isValid = false;
        } else if (preg_match('/\\.\\./', $domain)) {
            // domain part has two consecutive dots
            $isValid = false;
        } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
            // character not valid in local part unless 
            // local part is quoted
            if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
                $isValid = false;
            }
        }
        if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
            // domain not found in DNS
            $isValid = false;
        }
    }
    return $isValid;
}


function is_date($value, $format = 'd/m/Y'){
     # Par Frédéric FAYS, www.blue-invoice.com
     $format=strtolower($format);
     if(strlen($value)>7 && strlen($format)==5){
         # Trouver le séparateur
         $sep = str_replace(array('m','d','y'),'', $format);
         if(strlen($sep)==2 && $sep[0]==$sep[1]){
             # création du regexp
             $regexp = str_replace('m','[0-1]?[0-9]', $format);
             $regexp = str_replace('d','[0-3]?[0-9]', $regexp);
             $regexp = str_replace('y','[0-9]{4}', $regexp);
             $regexp = str_replace(']'.$sep[0].'[', ']\\' . $sep[0].'[', $regexp);
             if(preg_match('#'.$regexp.'#', $value)){
                 # Trouver les éléments de la date
                 $fmd=str_replace($sep[0],'',$format);
                 $DtExplode=explode($sep[0],$value);
                 # Tester la date
                $d = $DtExplode[strpos($fmd,'d')];
                $m = $DtExplode[strpos($fmd,'m')];
                $y = $DtExplode[strpos($fmd,'y')];
                if(@checkdate($m, $d, $y)) return true;
             }
         }
     }
     return false;
} 


?>
