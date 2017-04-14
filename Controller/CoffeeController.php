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
    
//    public function countOfferCoffee(User $_user, $_data) {
//        global $lang;
//        try {
//            $db = Database::getConnection();
////            $sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name, COUNT(".DB_PREFIX."paid_coffee.powered_by) AS caffe_offerti FROM ".DB_PREFIX."paid_coffee
////                    JOIN ".DB_PREFIX."user ON ".DB_PREFIX."paid_coffee.powered_by = ".DB_PREFIX."user.id_telegram
////                    WHERE ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."'
////                    AND ".DB_PREFIX."paid_coffee.powered_by IS NOT NULL
////                    GROUP BY ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name
////                    ORDER BY caffe_offerti DESC";
//            $sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name, COUNT(".DB_PREFIX."paid_coffee.powered_by) AS caffe_offerti FROM ".DB_PREFIX."paid_coffee
//                    JOIN ".DB_PREFIX."user ON ".DB_PREFIX."paid_coffee.powered_by = ".DB_PREFIX."user.id_telegram
//                    WHERE ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."'
//                    AND ".DB_PREFIX."paid_coffee.powered_by != '".$_data["id_user"]."'
//                    GROUP BY ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name
//                    ORDER BY caffe_offerti DESC";
//            $result = $db->query($sql);
//            if (mysqli_num_rows($result) == 0) {
//                return 0;
//            } else {
//                while($singleUser = $result->fetch_assoc()) {
//                    $singleUser["caffe_offerti"] = (int)$singleUser["caffe_offerti"];
//                    $usersWithPaidCoffee[] = $singleUser;
//                }
//                
//                return $usersWithPaidCoffee;
//            }
//        } catch (DatabaseException $ex) {
//            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
//        }
//    }
    
    public function countOfferCoffee(User $_user, $_data) {
        global $lang;
        try {
            $db = Database::getConnection();
//            $sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name, COUNT(".DB_PREFIX."paid_coffee.powered_by) AS caffe_offerti FROM ".DB_PREFIX."paid_coffee
//                    JOIN ".DB_PREFIX."user ON ".DB_PREFIX."paid_coffee.powered_by = ".DB_PREFIX."user.id_telegram
//                    WHERE ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."'
//                    AND ".DB_PREFIX."paid_coffee.powered_by IS NOT NULL
//                    GROUP BY ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name
//                    ORDER BY caffe_offerti DESC";
            $sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name, COUNT(".DB_PREFIX."paid_coffee.powered_by) AS caffe_offerti FROM ".DB_PREFIX."paid_coffee
                    JOIN ".DB_PREFIX."user ON ".DB_PREFIX."paid_coffee.powered_by = ".DB_PREFIX."user.id_telegram
                    WHERE ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."'
                    AND ".DB_PREFIX."paid_coffee.powered_by = '".$_data["id_user"]."'
                    GROUP BY ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name
                    ORDER BY caffe_offerti DESC";
            $result = $db->query($sql);
            if (mysqli_num_rows($result) == 0) {
                $usersWithPaidCoffee["id_telegram"] = $_data["id_user"];
                $usersWithPaidCoffee["name"] = $_data["name"];
                $usersWithPaidCoffee["caffe_offerti"] = 0;
                return $usersWithPaidCoffee;
                //return 0;
            } else {
                $singleUser = $result->fetch_assoc();
                    $singleUser["caffe_offerti"] = (int)$singleUser["caffe_offerti"];
                    $usersWithPaidCoffee = $singleUser;
                
                return $usersWithPaidCoffee;
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function countReceivedCoffee(User $_user, $_data) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name, COUNT(".DB_PREFIX."paid_coffee_people.id_paid_coffee) AS caffe_ricevuti FROM ".DB_PREFIX."paid_coffee
                    JOIN ".DB_PREFIX."paid_coffee_people ON ".DB_PREFIX."paid_coffee.id_paid_coffee = ".DB_PREFIX."paid_coffee_people.id_paid_coffee
                        JOIN ".DB_PREFIX."user ON ".DB_PREFIX."paid_coffee_people.id_user = ".DB_PREFIX."user.id_telegram
                        WHERE ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."'
                        AND ".DB_PREFIX."paid_coffee.powered_by != '".$_data["id_user"]."'
                        AND ".DB_PREFIX."paid_coffee.powered_by IS NOT NULL
                        AND ".DB_PREFIX."paid_coffee_people.id_user = '".$_data["id_user"]."'
                        GROUP BY ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name
                        ORDER BY caffe_ricevuti DESC";
            $result = $db->query($sql);
            if (mysqli_num_rows($result) == 0) {
                $usersWithReceivedCoffee["id_telegram"] = $_data["id_user"];
                $usersWithReceivedCoffee["name"] = $_data["name"];
                $usersWithReceivedCoffee["caffe_ricevuti"] = 0;
                return $usersWithReceivedCoffee;
            } else {
                $singleUser = $result->fetch_assoc();
                $singleUser["caffe_ricevuti"] = (int)$singleUser["caffe_ricevuti"];
                $usersWithReceivedCoffee = $singleUser;
                
                return $usersWithReceivedCoffee;
            }
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
    
    public function getCandidate(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT ".DB_PREFIX."paid_coffee_people.id_user, ".DB_PREFIX."user.name FROM ".DB_PREFIX."paid_coffee
					JOIN ".DB_PREFIX."paid_coffee_people ON ".DB_PREFIX."paid_coffee.id_paid_coffee = ".DB_PREFIX."paid_coffee_people.id_paid_coffee
                    JOIN ".DB_PREFIX."user ON ".DB_PREFIX."paid_coffee_people.id_user = ".DB_PREFIX."user.id_telegram
                    WHERE ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."'
                    AND ".DB_PREFIX."paid_coffee.powered_by IS NULL";
            
//            $sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name, COUNT(".DB_PREFIX."paid_coffee.powered_by) AS caffe_offerti FROM ".DB_PREFIX."paid_coffee
//                    JOIN ".DB_PREFIX."user ON ".DB_PREFIX."paid_coffee.powered_by = ".DB_PREFIX."user.id_telegram
//                    WHERE ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."'
//                    AND ".DB_PREFIX."paid_coffee.powered_by IS NOT NULL
//                    GROUP BY ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name
//                    ORDER BY caffe_offerti DESC";
            $result = $db->query($sql);
            if (mysqli_num_rows($result) == 0) {
                return 0;
            } else {
                while($singleUser = $result->fetch_assoc()) {
                    $users[] = $singleUser;
                }
                
                return $users;
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
}

class CoffeeControllerException extends Exception { }
