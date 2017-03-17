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
                throw new CoffeeControllerException($lang->error->errorWhileCoffeeRegistration);
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
                throw new CoffeeControllerException($lang->error->errorWhileCoffeeRegistration);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function destroyCoffee(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            $idToDelete = "SELECT id_paid_coffee FROM ".DB_PREFIX."paid_coffee WHERE set_by = '".$_user->getIdTelegram()."' AND powered_by IS NULL";
            $result = $db->query($idToDelete);
            if (!$result) {
                throw new CoffeeControllerException($lang->error->errorWhileCoffeeRegistration);
            }
            $id = $result->fetch_assoc();
            //$delete = "DELETE FROM ".DB_PREFIX."paid_coffee WHERE ".DB_PREFIX."paid_coffee.id_paid_coffee = '".$result[0]."'";
            $delete = "DELETE ".DB_PREFIX."paid_coffee, ".DB_PREFIX."paid_coffee_people FROM ".DB_PREFIX."paid_coffee INNER JOIN ".DB_PREFIX."paid_coffee_people WHERE ".DB_PREFIX."paid_coffee.id_paid_coffee = ".DB_PREFIX."paid_coffee_people.id_paid_coffee AND ".DB_PREFIX."paid_coffee.id_paid_coffee = '".$id["id_paid_coffee"]."'";
            $result = $db->query($delete);
            if (!$result) {
                throw new CoffeeControllerException($lang->error->errorWhileCoffeeRegistration);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
}

class CoffeeControllerException extends Exception { }
