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
            $insertMe = $db->query("INSERT INTO ".DB_PREFIX."user_group (id_user, id_group) VALUES('".$_user->getChatMember()->getId()."', '".$_user->getChat()->getId()."')");
            $insertUser = $db->query("INSERT INTO ".DB_PREFIX."user_group (id_user, id_group, bot_owner) VALUES('".$_user->getIdTelegram()."', '".$_user->getChat()->getId()."', '1')");
            if (!$insertMe || !$insertUser) {
                throw new GroupControllerException($lang->error->errorWhileUserRegistration);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function checkUser(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $result = $db->query("SELECT * FROM ".DB_PREFIX."user_group WHERE id_user = '".$_user->getIdTelegram()."' AND id_group = '".$_user->getChat()->getId()."' AND active = 1");
            if ($result->num_rows == 0) {
                
                if (!$db->query("INSERT INTO ".DB_PREFIX."user_group (id_user, id_group, partecipate) VALUES('".$_user->getIdTelegram()."', '".$_user->getChat()->getId()."', '1')"))
                    throw new GroupControllerException($lang->error->errorWhileUserRegistration);
                
                return true;
            } else {
                if (!$db->query("UPDATE ".DB_PREFIX."user_group SET partecipate = 1 WHERE id_user = '".$_user->getIdTelegram()."' AND id_group = '".$_user->getChat()->getId()."' AND active = 1"))
                    throw new GroupControllerException($lang->error->errorWhileUserRegistration);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function getCompetitors(Chat $_chat, $_me) {
        global $lang;
        try {
            $db = Database::getConnection();
            //$sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name FROM ".DB_PREFIX."user JOIN ".DB_PREFIX."user_group ON ".DB_PREFIX."user.id_telegram = ".DB_PREFIX."user_group.id_user JOIN ".DB_PREFIX."group ON ".DB_PREFIX."user_group.id_group = ".DB_PREFIX."group.id_group WHERE ".DB_PREFIX."user_group.id_group = '".$_chat->getId()."' AND ".DB_PREFIX."user_group.active = '1' AND ".DB_PREFIX."user_group.id_user != '".$_me->result->id."'";
            $sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name"
                    . "FROM ".DB_PREFIX."user "
                    . "JOIN ".DB_PREFIX."user_group ON ".DB_PREFIX."user.id_telegram = ".DB_PREFIX."user_group.id_user "
                    . "WHERE ".DB_PREFIX."user_group.id_group = '".$_chat->getId()."'"
                    . "AND ".DB_PREFIX."user_group.active = '1'"
                    . "AND ".DB_PREFIX."user_group.id_user != '".$_me->result->id."'"
                    . "AND ".DB_PREFIX."user_group.active = '1'";
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
            //$sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name FROM ".DB_PREFIX."user JOIN ".DB_PREFIX."user_group ON ".DB_PREFIX."user.id_telegram = ".DB_PREFIX."user_group.id_user JOIN ".DB_PREFIX."paid_coffee ON ".DB_PREFIX."user_group.id_group = ".DB_PREFIX."paid_coffee.id_group WHERE ".DB_PREFIX."user_group.id_group = '".$_user->getChat()->getId()."' AND ".DB_PREFIX."user_group.id_user != '".$_me->result->id."' AND ".DB_PREFIX."user_group.active = '1' AND ".DB_PREFIX."user.id_telegram NOT IN (SELECT ".DB_PREFIX."paid_coffee_people.id_user FROM ".DB_PREFIX."paid_coffee JOIN ".DB_PREFIX."paid_coffee_people ON ".DB_PREFIX."paid_coffee.id_paid_coffee = ".DB_PREFIX."paid_coffee_people.id_paid_coffee WHERE ".DB_PREFIX."paid_coffee.id_group != '".$_user->getChat()->getId()."' AND ".DB_PREFIX."paid_coffee.set_by = '".$_user->getIdTelegram()."' AND ".DB_PREFIX."paid_coffee.powered_by IS NULL)";
            
            $sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name FROM ".DB_PREFIX."user JOIN ".DB_PREFIX."user_group ON ".DB_PREFIX."user.id_telegram = ".DB_PREFIX."user_group.id_user JOIN ".DB_PREFIX."paid_coffee ON ".DB_PREFIX."user_group.id_group = ".DB_PREFIX."paid_coffee.id_group WHERE ".DB_PREFIX."user_group.id_group = '".$_user->getChat()->getId()."' AND ".DB_PREFIX."user_group.active = '1' AND ".DB_PREFIX."user_group.id_user != '".$_me->result->id."' AND ".DB_PREFIX."user.id_telegram NOT IN (SELECT ".DB_PREFIX."paid_coffee_people.id_user FROM ".DB_PREFIX."paid_coffee JOIN ".DB_PREFIX."paid_coffee_people ON ".DB_PREFIX."paid_coffee.id_paid_coffee = ".DB_PREFIX."paid_coffee_people.id_paid_coffee WHERE ".DB_PREFIX."paid_coffee.id_group != '".$_user->getChat()->getId()."' AND ".DB_PREFIX."paid_coffee.set_by = '".$_user->getIdTelegram()."' AND ".DB_PREFIX."paid_coffee.powered_by IS NULL)";
            
            // // funziona
            $sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name FROM ".DB_PREFIX."user "
                    . "JOIN ".DB_PREFIX."user_group ON ".DB_PREFIX."user.id_telegram = ".DB_PREFIX."user_group.id_user "
                    . "JOIN ".DB_PREFIX."paid_coffee ON ".DB_PREFIX."user_group.id_group = ".DB_PREFIX."paid_coffee.id_group "
                    . "WHERE ".DB_PREFIX."user_group.id_group = '".$_user->getChat()->getId()."' "
                    . "AND ".DB_PREFIX."user_group.id_user != '".$_me->result->id."' "
                    . "AND ".DB_PREFIX."user_group.active = '1' "
                    . "AND ".DB_PREFIX."user.id_telegram NOT IN "
                    . "(SELECT ".DB_PREFIX."paid_coffee_people.id_user FROM ".DB_PREFIX."paid_coffee "
                    . "JOIN ".DB_PREFIX."paid_coffee_people ON ".DB_PREFIX."paid_coffee.id_paid_coffee = ".DB_PREFIX."paid_coffee_people.id_paid_coffee "
                    . "WHERE ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."' "
                    . "AND ".DB_PREFIX."paid_coffee.set_by = '".$_user->getIdTelegram()."' "
                    . "AND ".DB_PREFIX."paid_coffee.powered_by IS NULL)";

            $query = $db->query($sql);
            
            if ($query->num_rows > 0) {
                while($tmp = $query->fetch_assoc())
                    $res[] = $tmp;

                $db->close();
                return $res;
            } else {
                throw new GroupControllerException($lang->error->noResultsFound."AFASVAFF");
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function leaveGroup(Chat $_chat) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $db->query("UPDATE ".DB_PREFIX."user_group SET active = 0 WHERE id_group = ".$_chat->getId());
            
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