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
define("START", '/start');
define('INFO', '/info');
define("KEYBOARD", "/keyboard");
define('EXIT', '/exit');
define('HOME', 'home');
define('HELP', 'help');
define('SETTINGS', 'settings');
define('CANCEL_COFFEE', 'cancel');
define('SELECT_ALL', 'selectAll');
define('CHANGE_LANGUAGE', 'changeLanguage');
define('STATS', 'stats');
define('RESET_GROUP', 'resetGroup');
define('NULL_VALUE', 'nullValue');
define('CHOOSE_BENEFACTOR', 'chooseBenefactor');
define('CHOOSE_BENEFACTOR2', 'chooseBenefactor2');
define('BENEFACTOR_LIST', 'benefactorList');
define('SETTING_OPERATION_GROUP', 'setOperationGroup');
define('CHANGE_LANGUAGE_GROUP', 'changeLanguageGroup');

//class Config {
//    
//    private $language = DEFAULT_LANGUAGE;
//    
//    public function getLanguage() {
//        return $this->language;
//    }
//}