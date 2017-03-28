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
    
    public function newPaidCoffee($_idChat, $_idUser) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "INSERT INTO ".DB_PREFIX."paid_coffee (id_group, set_by, date, time) VALUES('".$_idChat."', '".$_idUser."', '".date("Y-m-d")."', '".date("H:i:s")."')";
            $result = $db->query($sql);
            //$sql = "INSERT INTO ".DB_PREFIX."paid_coffee (id_group, set_by, date, time) VALUES('".$_idUser."', '".date("Y-m-d")."', '".date("H:i:s")."'";
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
            $sql = "SELECT id_paid_coffee FROM ".DB_PREFIX."paid_coffee WHERE set_by = '".$_user->getIdTelegram()."' AND id_group =  '".$_user->getChat()->getId()."' AND powered_by IS NULL";
            $result = $db->query($sql);
            if (!$result || mysqli_num_rows($result) == 0) {
                throw new CoffeeControllerException($lang->error->errorWhileSelectionPaidCoffee);
            }
            $idToDelete = $result->fetch_assoc();
            $delete = "DELETE ".DB_PREFIX."paid_coffee, ".DB_PREFIX."paid_coffee_people FROM ".DB_PREFIX."paid_coffee INNER JOIN ".DB_PREFIX."paid_coffee_people WHERE ".DB_PREFIX."paid_coffee.id_paid_coffee = ".DB_PREFIX."paid_coffee_people.id_paid_coffee AND ".DB_PREFIX."paid_coffee.id_paid_coffee = '".$idToDelete["id_paid_coffee"]."'";
            $result = $db->query($delete);
            if (!$result) {
                throw new CoffeeControllerException($lang->error->errorWhileDestroyCoffee);
            }
            $delete = "DELETE FROM ".DB_PREFIX."paid_coffee WHERE ".DB_PREFIX."paid_coffee.id_paid_coffee = '".$idToDelete["id_paid_coffee"]."'";
            $result = $db->query($delete);
            if (!$result) {
                throw new CoffeeControllerException($lang->error->errorWhileDestroyCoffee);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function countOfferCoffee(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            //$allCompetitors = "SELECT DISTINCT ".DB_PREFIX."paid_coffee.powered_by FROM ".DB_PREFIX."paid_coffee WHERE ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."' AND ".DB_PREFIX."paid_coffee.powered_by IS NOT NULL";
            $allCompetitors = "SELECT DISTINCT ".DB_PREFIX."paid_coffee.powered_by, ".DB_PREFIX."user.name FROM ".DB_PREFIX."paid_coffee
                    JOIN ".DB_PREFIX."user ON ".DB_PREFIX."paid_coffee.powered_by = ".DB_PREFIX."user.id_telegram
                    WHERE ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."'
                    AND ".DB_PREFIX."paid_coffee.powered_by IS NOT NULL";
            $result = $db->query($allCompetitors);
            if (mysqli_num_rows($result) == 0) {
                throw new CoffeeControllerException($lang->error->errorWhileGettingName);
            }
            $i = 0;
            $query = "SELECT ";
            while ($value = $result->fetch_assoc()) {
                if (mysqli_num_rows($result) > ($i + 1)) {
                //$countCoffee = "SELECT SUM(powered_by = 1) AS user1, SUM(powered_by = 2) AS user2, SUM(powered_by = 3) AS user3 FROM coffee_paid_coffee WHERE id_group = -1 AND powered_by IS NOT NULL";
                    $query .= "SUM(".DB_PREFIX."paid_coffee.powered_by = ".$value["powered_by"].") AS '".$value["name"]."', ";
                } else {
                    $query .= "SUM(".DB_PREFIX."paid_coffee.powered_by = ".$value["powered_by"].") AS '".$value["name"]."' ";
                }
                $i++;
            }
            $query .= "FROM ".DB_PREFIX."paid_coffee WHERE ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."' AND powered_by IS NOT NULL";
            $result = $db->query($query);
            if (!$result) {
                throw new CoffeeControllerException($lang->error->errorWhileCountingCoffee);
            }
            $id = $result->fetch_assoc();
            return $id;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function countPaidCoffee(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            $allCompetitors = "SELECT DISTINCT ".DB_PREFIX."paid_coffee.powered_by, ".DB_PREFIX."user.name FROM ".DB_PREFIX."paid_coffee
                    JOIN ".DB_PREFIX."user ON ".DB_PREFIX."paid_coffee.powered_by = ".DB_PREFIX."user.id_telegram
                    WHERE ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."'
                    AND ".DB_PREFIX."paid_coffee.powered_by IS NOT NULL";
            $result = $db->query($allCompetitors);
            if (mysqli_num_rows($result) == 0) {
                throw new CoffeeControllerException($lang->error->errorWhileSelectionPaidCoffee);
            }
            $i = 0;
            $query = "SELECT ";
            while ($value = $result->fetch_assoc()) {
                if (mysqli_num_rows($result) > ($i + 1)) {
                //$countCoffee = "SELECT SUM(powered_by = 1) AS user1, SUM(powered_by = 2) AS user2, SUM(powered_by = 3) AS user3 FROM coffee_paid_coffee WHERE id_group = -1 AND powered_by IS NOT NULL";
                    $query .= "SUM(".DB_PREFIX."paid_coffee.powered_by = ".$value["powered_by"].") AS ".$value["name"].", ";
                } else {
                    $query .= "SUM(".DB_PREFIX."paid_coffee.powered_by = ".$value["powered_by"].") AS ".$value["name"]." ";
                }
                $i++;
            }
            $query .= "FROM ".DB_PREFIX."paid_coffee WHERE ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."' AND powered_by IS NOT NULL";
            $result = $db->query($query);
            if (!$result) {
                throw new CoffeeControllerException($lang->error->errorWhileSelectionPaidCoffee);
            }
            $id = $result->fetch_assoc();
            return $id;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function setPaid(User $_user, $_idUser) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "UPDATE ".DB_PREFIX."paid_coffee SET ".DB_PREFIX."paid_coffee.powered_by = '".$_idUser."' WHERE ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."' AND ".DB_PREFIX."paid_coffee.set_by = '".$_user->getIdTelegram()."' AND ".DB_PREFIX."paid_coffee.powered_by IS NULL";
            $result = $db->query($sql);
            if (!$result) {
                throw new CoffeeControllerException($lang->error->errorWhileCoffeeRegistration);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function checkCoffee(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "SELECT id_paid_coffee FROM ".DB_PREFIX."paid_coffee WHERE set_by = '".$_user->getIdTelegram()."' AND id_group =  '".$_user->getChat()->getId()."' AND powered_by IS NULL";
            $result = $db->query($sql);
            if (!$result || mysqli_num_rows($result) == 0) {
                throw new CoffeeControllerException($lang->error->cantAddBenefactor);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function checkCoffeeInSpecificGroup(Chat $_chat) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "SELECT COUNT(id_paid_coffee) FROM ".DB_PREFIX."paid_coffee WHERE id_group =  '".$_chat->getId()."' AND powered_by IS NULL";
            $result = $db->query($sql);
            if (!$result || mysqli_num_rows($result) == 0) {
                throw new CoffeeControllerException($lang->error->cantAddBenefactor);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
}

class CoffeeControllerException extends Exception { }
