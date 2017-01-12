<?php
require_once './Model/Database.php';
require_once './Model/User.php';
$lang = Lang::getLang();

/**
 * Description of UserManager
 *
 * @author fagottino
 */
class UserController {
    
    public function __construct() {
        
    }
    
//    public function getMessageData($_data) {
//        $idTelegram = $_data["message"]["from"]["id"];
//        $name = $_data["message"]["from"]["first_name"];
//        $username = $_data["message"]["from"]["username"];
//        $idChat = $_data["message"]["chat"]["id"];
//        $message = $_data["message"]["text"];
//
//        $user = new User();
//        $user->createUser($idTelegram, $name, $message, $idChat, START_OPERATION, (isset($username) ? $username : NULL));
//        return $user;
//    }
    
    public function getInfo($_idTelegram) {
        global $lang;
        try {
            $db = Database::getConnection();
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
        //$result = $db->query("SELECT ".DB_PREFIX."user.name, ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."operation.name_operation, ".DB_PREFIX."lang.name_lang FROM ".DB_PREFIX."user JOIN ".DB_PREFIX."operation ON ".DB_PREFIX."user.operation = ".DB_PREFIX."operation.id_operation JOIN ".DB_PREFIX."lang ON ".DB_PREFIX."user.lang = ".DB_PREFIX."lang.id_lang WHERE ".DB_PREFIX."user.id_telegram = '".$_idTelegram."'");
        $result = $db->query("SELECT ".DB_PREFIX."user.name, ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.operation, ".DB_PREFIX."lang.name_lang FROM ".DB_PREFIX."user JOIN ".DB_PREFIX."lang ON ".DB_PREFIX."user.lang = ".DB_PREFIX."lang.id_lang WHERE ".DB_PREFIX."user.id_telegram = '".$_idTelegram."'");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $result->free();
            return $row;
        } else {
            throw new UserControllerException($lang->error->noResultsFound);
        }
    }
    
    public function getAllUserName() {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $result = $db->query ("SELECT name FROM ".DB_PREFIX."user");
            if ($result) {
                if ($result->num_rows > 0) {
                    return $result;
                } else {
                    throw new UserControllerException($lang->error->cantGetNameOfUser);
                }
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function register(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $result = $db->query("INSERT INTO ".DB_PREFIX."user (name, id_telegram, operation) VALUES('".$_user->getName()."', '".$_user->getIdTelegram()."', '6')");
            if ($result) {
//                $lastId = $db->insert_id;
//                // Valore opzionale, non presente a tutti i messaggi
//                if ($_user->getUsername())
//                    $addUsername = $db->query ("UPDATE `user` SET username = '".$_user->getUsername()."' WHERE id_user = ".$lastId." AND id_telegram = '".$_user->getIdTelegram()."'");
//                // Valore opzionale, non presente a tutti i messaggi
//                if ($_user->getIdChat())
//                    $addChatId = $db->query ("UPDATE `user` SET chat_id = '".$_user->getIdChat()."' WHERE id_user = ".$lastId." AND id_telegram = '".$_user->getIdTelegram()."'");
            } else {
                throw new UserControllerException($lang->error->errorWhileUserRegistration);
            }
            
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function updateCurrentOperation(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            //$sql = "UPDATE `".DB_PREFIX."user` SET `operation` = (SELECT id_operation FROM `".DB_PREFIX."operation` WHERE name_operation = '".$_user->getCurrentOperation()."') WHERE id_telegram = '".$_user->getIdTelegram()."'";
            $sql = "UPDATE `".DB_PREFIX."user` SET `operation` = '".$_user->getCurrentOperation()."' WHERE id_telegram = '".$_user->getIdTelegram()."'";
            $db->query($sql);
            $db->close();
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }        
    }
    
    public function getCurrentOperation(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $result = $db->query ("SELECT operation FROM ".DB_PREFIX."user WHERE id_telegram = ".$_user->getIdTelegram()."");
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $result->free();
                return $row["operation"];
            } else {
                throw new UserControllerException($lang->error->cantGetLastOperation);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function updateLang($_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "UPDATE `".DB_PREFIX."user` SET `lang` = (SELECT id_lang FROM `".DB_PREFIX."lang` WHERE name_lang = '".$_user->getLang()."') WHERE id_telegram = '".$_user->getIdTelegram()."'";
            $db->query($sql);
            $db->close();
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }        
    }
}

class UserControllerException extends Exception { }