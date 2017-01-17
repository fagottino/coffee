<?php
require_once './Model/Database.php';
$lang = Lang::getLang();

/**
 * Description of CoffeeController
 *
 * @author fagottino
 */
class CoffeeController {
    
    public function __construct() {
        
    }
    
    public function setPaidCoffee($_idChat, $_idUser) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $result = $db->query("INSERT INTO ".DB_PREFIX."paid_coffee (id_group, set_by, date, time) VALUES('".$_idChat."', '".$_idUser."', '".date("Y-m-d")."', '".date("H:i:s")."')");
            $sql = "INSERT INTO ".DB_PREFIX."paid_coffee (id_group, set_by, date, time) VALUES('".$_idUser."', '".date("Y-m-d")."', '".date("H:i:s")."'";
            if (!$result) {
                throw new UserControllerException($lang->error->errorWhileCoffeeRegistration);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function addPeopleToCoffee(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "INSERT INTO ".DB_PREFIX."paid_coffee_people (id_paid_coffee, id_user) VALUES ((SELECT id_paid_coffee FROM coffee_paid_coffee WHERE set_by = '".$_user->getIdTelegram()."' AND powered_by IS NULL), '".$_user->getMessage()."')";
            $result = $db->query($sql);
            if (!$result) {
                throw new UserControllerException($lang->error->errorWhileCoffeeRegistration);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
}
