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
            $result = $db->query("INSERT INTO ".DB_PREFIX."user_group (id_user, id_group) VALUES('".$_user->getChatMember()->getId()."', '".$_user->getChat()->getId()."')");
            $result = $db->query("INSERT INTO ".DB_PREFIX."user_group (id_user, id_group, bot_owner) VALUES('".$_user->getIdTelegram()."', '".$_user->getChat()->getId()."', '1')");
            if (!$result) {
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
            //$query = "SELECT ".DB_PREFIX."utente.nome, ".DB_PREFIX."group.title FROM ".DB_PREFIX."utente JOIN ".DB_PREFIX."user_group ON ".DB_PREFIX."utente.id_telegram = ".DB_PREFIX."user_group.id_user JOIN ".DB_PREFIX."group ON ".DB_PREFIX."user_group.id_group = ".DB_PREFIX."group.id_group WHERE ".DB_PREFIX."user_group.id_group = '".$_chat->getId()."' AND ".DB_PREFIX."user_group.active = '1' AND ".DB_PREFIX."user_group.id_user != '".$_me->result->id."'";
            $sql = "SELECT ".DB_PREFIX."utente.nome, ".DB_PREFIX."group.title FROM ".DB_PREFIX."utente JOIN ".DB_PREFIX."user_group ON ".DB_PREFIX."utente.id_telegram = ".DB_PREFIX."user_group.id_user JOIN ".DB_PREFIX."group ON ".DB_PREFIX."user_group.id_group = ".DB_PREFIX."group.id_group WHERE ".DB_PREFIX."user_group.id_group = '".$_chat->getId()."' AND ".DB_PREFIX."user_group.active = '1' AND ".DB_PREFIX."user_group.id_user != '".$_me->result->id."'";
            $query = $db->query($sql);
            //$result = mysqli_fetch_all($db->query($query), MYSQLI_ASSOC);
            
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
            $text .= ++$i.") ".$value["nome"].chr(10);
        }
        return $text;
    }
}

class GroupControllerException extends Exception { }