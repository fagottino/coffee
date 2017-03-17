<?php
require_once './Model/Database.php';
require_once './Model/User.php';
$lang = Lang::getLang();

/**
 * Description of GroupController
 *
 * @author fagottino
 */
class GroupController {
    
    public function __construct() {
        
    }
    
    public function checkGroup($_idGroup) {
        global $lang;
        try {
            $db = Database::getConnection();
            $result = $db->query("SELECT * FROM ".DB_PREFIX."group WHERE ".DB_PREFIX."group.id_group = '".$_idGroup."'");
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $result->free();
                return $row;
            } else {
                throw new GroupControllerException($lang->error->noResultsFound);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function insert(Chat $_chat) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $result = $db->query("INSERT INTO ".DB_PREFIX."group (id_group, title, amaa) VALUES('".$_chat->getId()."', '".$_chat->getTitle()."', '".$_chat->getAmaa()."')");
            
            if (!$result) {
                throw new GroupControllerException($lang->error->errorWhileUserRegistration);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function associateUser(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            //$result = $db->query("INSERT INTO ".DB_PREFIX."user_group (id_user, id_group) VALUES('".$_user->getChatMember()->getId()."', '".$_user->getChat()->getId()."')");
            //$insertMe = $db->query("INSERT INTO ".DB_PREFIX."user_group (id_user, id_group) VALUES('".$_user->getChatMember()->getId()."', '".$_user->getChat()->getId()."')");
            $insertUser = $db->query("INSERT INTO ".DB_PREFIX."user_group (id_user, id_group, bot_owner) VALUES('".$_user->getIdTelegram()."', '".$_user->getChat()->getId()."', '1')");
            //if (!$insertMe || !$insertUser) {
            if (!$insertUser) {
                throw new GroupControllerException($lang->error->errorWhileUserRegistration);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function addUser(User $_user, $_partecipate) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT * FROM ".DB_PREFIX."user_group WHERE id_user = '".$_user->getIdTelegram()."' AND id_group = '".$_user->getChat()->getId()."' AND leaves = 0";
            $result = $db->query("SELECT * FROM ".DB_PREFIX."user_group WHERE id_user = '".$_user->getIdTelegram()."' AND id_group = '".$_user->getChat()->getId()."' AND leaves = 0");
            //if ($result->num_rows == 0) {
            if (mysqli_num_rows($result) == 0) {
                
                if (!$db->query("INSERT INTO ".DB_PREFIX."user_group (id_user, id_group, partecipate) VALUES('".$_user->getIdTelegram()."', '".$_user->getChat()->getId()."', '".$_partecipate."')"))
                    throw new GroupControllerException($lang->error->errorWhileUserRegistration);
            } else {
                $sql = "UPDATE ".DB_PREFIX."user_group SET partecipate = ".$_partecipate." WHERE id_user = '".$_user->getIdTelegram()."' AND id_group = '".$_user->getChat()->getId()."' AND active = 1";
                if (!$db->query($sql))
                    throw new GroupControllerException($lang->error->errorWhileUserRegistration);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
        return true;
    }
    
    public function setActive(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "UPDATE ".DB_PREFIX."user_group SET active = '1' WHERE id_user = '".$_user->getIdTelegram()."' AND id_group = '".$_user->getChat()->getId()."' AND leaves = '0'";
            $updateUser = $db->query($sql);
            if (!$updateUser) {
                throw new GroupControllerException($lang->error->errorWhileUserUpdate);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function getCompetitors(Chat $_chat, $_me) {
        global $lang;
        try {
            $db = Database::getConnection();
            //$sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name FROM ".DB_PREFIX."user JOIN ".DB_PREFIX."user_group ON ".DB_PREFIX."user.id_telegram = ".DB_PREFIX."user_group.id_user WHERE ".DB_PREFIX."user_group.id_group = '".$_chat->getId()."' AND ".DB_PREFIX."user_group.id_user != '".$_me->result->id."' AND ".DB_PREFIX."user_group.active = '1'";
            
            $sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name FROM ".DB_PREFIX."user JOIN ".DB_PREFIX."user_group ON ".DB_PREFIX."user.id_telegram = ".DB_PREFIX."user_group.id_user WHERE ".DB_PREFIX."user_group.id_group = '".$_chat->getId()."' AND ".DB_PREFIX."user_group.active = '1' AND ".DB_PREFIX."user_group.partecipate = '1' AND ".DB_PREFIX."user_group.leaves = '0'";
            $query = $db->query($sql);
            
            if ($query->num_rows > 0) {
                while($tmp = $query->fetch_assoc())
                    $res[] = $tmp;

                $db->close();
                return $res;
            } else {
                throw new GroupControllerException($lang->error->noResultsFound);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function getOtherCompetitors(User $_user, $_me) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            /*$sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name FROM ".DB_PREFIX."user
                    JOIN ".DB_PREFIX."user_group ON ".DB_PREFIX."user.id_telegram = ".DB_PREFIX."user_group.id_user
                    JOIN ".DB_PREFIX."paid_coffee ON ".DB_PREFIX."user_group.id_group = ".DB_PREFIX."paid_coffee.id_group
                    WHERE ".DB_PREFIX."user_group.id_group = '".$_user->getChat()->getId()."'
                    AND ".DB_PREFIX."user_group.active = '1'
                    AND ".DB_PREFIX."user_group.id_user != '".$_me->result->id."'
                    AND ".DB_PREFIX."user.id_telegram NOT IN
                    (SELECT ".DB_PREFIX."paid_coffee_people.id_user FROM ".DB_PREFIX."paid_coffee
                    JOIN ".DB_PREFIX."paid_coffee_people ON ".DB_PREFIX."paid_coffee.id_paid_coffee = ".DB_PREFIX."paid_coffee_people.id_paid_coffee
                    WHERE ".DB_PREFIX."user_group.id_user != '".$_me->result->id."'
                    AND ".DB_PREFIX."paid_coffee.set_by = '".$_user->getIdTelegram()."'
                    AND ".DB_PREFIX."paid_coffee.powered_by IS NULL
                    )";
             */
            $sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name FROM ".DB_PREFIX."user
                    JOIN ".DB_PREFIX."user_group ON ".DB_PREFIX."user.id_telegram = ".DB_PREFIX."user_group.id_user
                    JOIN ".DB_PREFIX."paid_coffee ON ".DB_PREFIX."user_group.id_group = ".DB_PREFIX."paid_coffee.id_group
                    WHERE ".DB_PREFIX."user_group.id_group = '".$_user->getChat()->getId()."'
                    AND ".DB_PREFIX."user_group.leaves = '0'
                    AND ".DB_PREFIX."user_group.partecipate = '1'
                    AND ".DB_PREFIX."user.id_telegram NOT IN
                    (SELECT ".DB_PREFIX."paid_coffee_people.id_user FROM ".DB_PREFIX."paid_coffee
                    JOIN ".DB_PREFIX."paid_coffee_people ON ".DB_PREFIX."paid_coffee.id_paid_coffee = ".DB_PREFIX."paid_coffee_people.id_paid_coffee
                    WHERE ".DB_PREFIX."paid_coffee.set_by = '".$_user->getIdTelegram()."'
                    AND ".DB_PREFIX."user_group.leaves = '0'
                    AND ".DB_PREFIX."user_group.partecipate = '1'
                    AND ".DB_PREFIX."paid_coffee.powered_by IS NULL
                    )";
            
            /*$sql = "SELECT coffee_user.id_telegram, coffee_user.name FROM coffee_user
                    JOIN coffee_user_group ON coffee_user.id_telegram = coffee_user_group.id_user
                    JOIN coffee_paid_coffee ON coffee_user_group.id_group = coffee_paid_coffee.id_group
                    WHERE coffee_user_group.id_group = '-114342037'
                    AND coffee_user_group.leaves = '0'
                    AND coffee_user.id_telegram NOT IN
                    (SELECT coffee_paid_coffee_people.id_user FROM coffee_paid_coffee
                    JOIN coffee_paid_coffee_people ON coffee_paid_coffee.id_paid_coffee = coffee_paid_coffee_people.id_paid_coffee
                    WHERE coffee_paid_coffee.set_by = '19179842'
                    AND coffee_paid_coffee.powered_by IS NULL
                    )";*/

            $query = $db->query($sql);
            
            //if ($query->num_rows > 0) {
            if (mysqli_num_rows($query) > 0) {
                while($tmp = $query->fetch_assoc()) {
                    $res[] = $tmp;
                }

                $db->close();
                return $res;
            } else {
                throw new GroupControllerException($lang->error->noResultsFound);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function activateGroup(Chat $_chat) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $db->query("UPDATE ".DB_PREFIX."user_group SET active = 1 WHERE id_group = ".$_chat->getId());
            
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function leaveGroup(Chat $_chat) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $db->query("UPDATE ".DB_PREFIX."user_group SET leaves = 1 WHERE id_group = ".$_chat->getId());
            
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function createText($_data) {
        $text = "I concorrenti sono:".chr(10);
        $i = 0;
        foreach ($_data  as $key => $value) {
            $text .= ++$i.") ".$value["name"].chr(10);
        }
        return $text;
    }
    
    public function checkAllUserOperation() {
        // QUERY FUNZIONANTE. CONTROLLARE SE CI SONO ALTRI UTENTI NELLO STESSO GRUPPO CHE STANNO GIÀ AGGIUNGENDO UN PAGAMENTO DI CAFFÈ
        // 
        //SELECT operation FROM coffee_user WHERE id_telegram IN (SELECT id_user FROM coffee_user_group WHERE id_group = '-114342037' AND active = '1')
    }
}

class GroupControllerException extends Exception { }