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
            if (!$result) {
                throw new CoffeeControllerException($lang->error->newPaidCoffee);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function addPeopleToCoffee(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "INSERT INTO ".DB_PREFIX."paid_coffee_people (id_paid_coffee, id_user) VALUES ((SELECT id_paid_coffee FROM ".DB_PREFIX."paid_coffee WHERE set_by = '".$_user->getIdTelegram()."' AND powered_by IS NULL AND ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."'), '".$_user->getMessage()."')";
            $result = $db->query($sql);
            if (!$result) {
                throw new CoffeeControllerException($lang->error->addPeopleToCoffee);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function destroyCoffee(User $_user, $_all = false) {
        global $lang;
        try {
            $db = Database::getConnection();
            if ($_all) {
                $sql = "SELECT id_paid_coffee FROM ".DB_PREFIX."paid_coffee WHERE set_by = '".$_user->getIdTelegram()."' AND id_group =  '".$_user->getChat()->getId()."' AND powered_by IS NULL";
            } else {
                $sql = "SELECT id_paid_coffee FROM ".DB_PREFIX."paid_coffee WHERE id_group =  '".$_user->getChat()->getId()."' AND powered_by IS NULL";
            }
            $result = $db->query($sql);
            if (!$result) {
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
    
    public function destroyAllCoffee(Chat $_chat = null, $_idGroup = null) {
        global $lang;
        try {
            $db = Database::getConnection();
            if ($_chat != null) {
                $sql = "SELECT id_paid_coffee FROM ".DB_PREFIX."paid_coffee WHERE id_group =  '".$_chat->getId()."'";
            } else {
                $sql = "SELECT id_paid_coffee FROM ".DB_PREFIX."paid_coffee WHERE id_group =  '".$_idGroup."'";
            }
            $result = $db->query($sql);
            if (!$result) {
                throw new CoffeeControllerException($lang->error->errorWhileSelectionPaidCoffee);
            }
            while ($idToDelete = $result->fetch_assoc()) {
                $delete = "DELETE ".DB_PREFIX."paid_coffee, ".DB_PREFIX."paid_coffee_people FROM ".DB_PREFIX."paid_coffee
                        INNER JOIN ".DB_PREFIX."paid_coffee_people
                        WHERE ".DB_PREFIX."paid_coffee.id_paid_coffee = ".DB_PREFIX."paid_coffee_people.id_paid_coffee
                        AND ".DB_PREFIX."paid_coffee.id_paid_coffee = '".$idToDelete["id_paid_coffee"]."'";
                $results = $db->query($delete);
                if (!$results) {
                    throw new CoffeeControllerException($lang->error->errorWhileDestroyAllCoffee);
                }
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function checkAllCoffee(Chat $_chat) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "SELECT id_paid_coffee FROM ".DB_PREFIX."paid_coffee WHERE id_group =  '".$_chat->getId()."'";
            $result = $db->query($sql);
            if (!$result || mysqli_num_rows($result) == 0) {
                throw new CoffeeControllerException($lang->error->errorWhileSelectionPaidCoffee);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function countOfferCoffee(User $_user, $_data) {
        global $lang;
        try {
            $db = Database::getConnection();
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
            } else {
                $singleUser = $result->fetch_assoc();
                $singleUser["caffe_offerti"] = (int)$singleUser["caffe_offerti"];
                $usersWithPaidCoffee = $singleUser;
            }
            return $usersWithPaidCoffee;
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
                throw new CoffeeControllerException($lang->error->setPaid);
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
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
            } else if (mysqli_num_rows($result) == 0) {
                $row = array();
            } else {
                throw new CoffeeControllerException($lang->error->checkCoffee);
            }
            return $row;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function checkCoffeeInSpecificGroup(Chat $_chat) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "SELECT * FROM ".DB_PREFIX."paid_coffee WHERE id_group =  '".$_chat->getId()."' AND powered_by IS NULL";
            $query = $db->query($sql);
            if (mysqli_num_rows($query) > 0) {
                while($row = $query->fetch_assoc()) {
                    $result[] = $row;
                }
            } else if (mysqli_num_rows($result) == 0) {
                $result = array();
            } else {
                throw new CoffeeControllerException($lang->error->checkCoffeeSpecificGroup);
            }
            return $result;
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
    
    public function checkParticipate(User $_user, $_idUser = null) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            if ($_idUser != null) {
                $sql = "SELECT ".DB_PREFIX."user_group.partecipate FROM ".DB_PREFIX."user_group
                    WHERE ".DB_PREFIX."user_group.id_group = '".$_user->getChat()->getId()."'
                    AND ".DB_PREFIX."user_group.id_user = '".$_idUser."'
                    AND ".DB_PREFIX."user_group.configuration = '0'";
            } else {
                $sql = "SELECT ".DB_PREFIX."user_group.partecipate FROM ".DB_PREFIX."user_group
                    WHERE ".DB_PREFIX."user_group.id_group = '".$_user->getChat()->getId()."'
                    AND ".DB_PREFIX."user_group.id_user = '".$_user->getIdTelegram()."'
                    AND ".DB_PREFIX."user_group.configuration = '0'";
            }
            $result = $db->query($sql);
            if (mysqli_num_rows($result) == 0) {
                $partecipate =  -1;
            } else {
                $partecipate = $result->fetch_assoc();
            }
            return $partecipate["partecipate"];
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function checkAllParticipate(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT COUNT(".DB_PREFIX."user_group.partecipate) FROM ".DB_PREFIX."user_group
                    WHERE ".DB_PREFIX."user_group.id_group = '".$_user->getChat()->getId()."'
                    AND ".DB_PREFIX."user_group.participate = '1'
                    AND ".DB_PREFIX."user_group.leaves = '0'";
            $result = $db->query($sql);
            if (mysqli_num_rows($result) == 0) {
                $partecipate =  0;
            } else {
                $partecipate = $result->fetch_assoc();
            }
            return $partecipate["partecipate"];
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
}

class CoffeeControllerException extends Exception { }
