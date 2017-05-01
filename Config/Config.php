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
define('I_LIKE_THE_BOT', '/ilikethebot');
define('HOME', 'home');
define('HELP', 'help');
define('SETTINGS', 'settings');
define('SETTINGS_INLINE', 'settingsInline');
define('CANCEL_COFFEE', 'cancel');
define('SELECT_ALL', 'selectAll');
define('CHANGE_LANGUAGE', 'changeLanguage');
define('STATS', 'stats');
define('STATS_GROUP', 'statsGroup');
define('RESET_GROUP', 'groupReset');
define('RESET_HOLD_HOLDINGS', 'resetHoldHoldings');
define('RESET_HOLD_HOLDINGS_YES', 'YesResetGroupHoldHolings');
define('RESET_HOLD_HOLDINGS_NO', 'NoResetGroupHoldHolings');
define('RESET_OLD_COFFEE', 'resetOldCoffee');
define('RESET_OLD_COFFEE_YES', 'YesResetGroupOldCoffee');
define('RESET_OLD_COFFEE_NO', 'NoResetGroupOldCoffee');
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