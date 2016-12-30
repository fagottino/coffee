<?php
require_once './Model/Lang.php';
$lang = Lang::getLang();
/**
 * Description of Config
 *
 * @author fagottino
 */
define ('IT', 'it');
define ('EN', 'en');
define ('FR', 'fr');
define ('ES', 'es');

// change this
define('DEFAULT_LANGUAGE', IT);

define('DB_PREFIX', 'coffee_');


// Menu list
define ("START", '/start');
define ('HELP', $lang->menu->help);
define ('BACK', $lang->menu->back);

//class Config {
//    
//    private $language = DEFAULT_LANGUAGE;
//    
//    public function getLanguage() {
//        return $this->language;
//    }
//}