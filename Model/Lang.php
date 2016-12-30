<?php
/**
 * Description of Lang
 *
 * @author fagottino
 */
class Lang {
    
    public function __construct() {
        
    }
    
    public static function getLang($_lang = "it") {
        switch ($_lang) {
            case "it":
            default:
                $_lang = simplexml_load_file("./Lang/it.xml") or die("Error: Cannot create object");
            break;
            case "en":
                $_lang = simplexml_load_file("./Lang/en.xml") or die("Error: Cannot create object");
            break;
        }
        return $_lang;
    }
}
