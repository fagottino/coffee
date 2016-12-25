<?php
require_once './Config/Config.php';

/**
 * Description of Lang
 *
 * @author fagottino
 */
class Lang {
    
    public function __construct() {
        
    }
    
    public static function getLang() {
        switch (DEFAULT_LANGUAGE) {
            case "it":
            default:
                $_lang = simplexml_load_file("./Lang/it.xml") or die("Error: Cannot create object");
            break;
            case "en":
                
            break;
        }
        return $_lang;
    }
}
