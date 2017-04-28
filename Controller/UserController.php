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
    
    public function getInfo($_idTelegram) {
        global $lang;
        try {
            $db = Database::getConnection();
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
        
        $sql = "SELECT ".DB_PREFIX."user.name, ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.operation, ".DB_PREFIX."lang.name_lang FROM ".DB_PREFIX."user JOIN ".DB_PREFIX."lang ON ".DB_PREFIX."user.lang = ".DB_PREFIX."lang.id_lang WHERE ".DB_PREFIX."user.id_telegram = '".$_idTelegram."'";
        $result = $db->query($sql);
        
        if (!$result) {
            throw new DatabaseException("Non sono riuscito a rintracciarti nel database.");
        } else if ($result->num_rows == 0) {
            $row = array();
        } else {
            $row = $result->fetch_assoc();
            $result->free();
        }
        return $row;
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
            if ($_user->getName() != null && $_user->getIdTelegram() != null) {
                $db = Database::getConnection();
                $sql = "INSERT INTO ".DB_PREFIX."user (name, id_telegram) VALUES('".$_user->getName()."', '".$_user->getIdTelegram()."')";
                $result = $db->query($sql);
                if (!$result) {
                    throw new UserControllerException($lang->error->errorWhileUserRegistration);
    //                $lastId = $db->insert_id;
    //                // Valore opzionale, non presente a tutti i messaggi
    //                if ($_user->getUsername())
    //                    $addUsername = $db->query ("UPDATE `user` SET username = '".$_user->getUsername()."' WHERE id_user = ".$lastId." AND id_telegram = '".$_user->getIdTelegram()."'");
    //                // Valore opzionale, non presente a tutti i messaggi
    //                if ($_user->getIdChat())
    //                    $addChatId = $db->query ("UPDATE `user` SET chat_id = '".$_user->getIdChat()."' WHERE id_user = ".$lastId." AND id_telegram = '".$_user->getIdTelegram()."'");
                } else {
                    return true;
                }
            } else {
                    throw new UserControllerException($lang->error->registerEmptyField);
            }
            
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function updateCurrentOperation(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "UPDATE `".DB_PREFIX."user` SET `operation` = '".$_user->getCurrentOperation()."' WHERE id_telegram = '".$_user->getIdTelegram()."'";
            $result = $db->query($sql);
            $db->close();
            if (!$result) {
                throw new UserControllerException("Errore durante l'aggiornamento dell'operazione corrente");
            }
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
    
    public function getGroupOperation(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT operation FROM ".DB_PREFIX."user_group WHERE id_user = ".$_user->getIdTelegram()." AND id_group =  ".$_user->getChat()->getId()."";
            $result = $db->query ($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $result->free();
                $res = $row["operation"];
            } else if ($result->num_rows == 0) {
                $res = array();
            } else {
                throw new UserControllerException($lang->error->cantGetGroupOperation);
            }
            return $res;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function updateGroupOperation(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            //$sql = "UPDATE `".DB_PREFIX."user` SET `operation` = (SELECT id_operation FROM `".DB_PREFIX."operation` WHERE name_operation = '".$_user->getCurrentOperation()."') WHERE id_telegram = '".$_user->getIdTelegram()."'";
            $sql = "UPDATE `".DB_PREFIX."user_group` SET `operation` = '".$_user->getGroupOperation()."' WHERE id_user = '".$_user->getIdTelegram()."' AND id_group =  ".$_user->getChat()->getId()."";
            $result = $db->query($sql);
            $db->close();
            if (!$result) {
            throw new UserControllerException("Errore durante l'aggiornamento dell'operazione nel gruppo dell'utente.");
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
            $result = $db->query($sql);
            $db->close();
            if (!$result) {
                throw new UserControllerException("Errore durante l'aggiornamento della lingua");
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }        
    }
    
    public function getPath() {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT * FROM ".DB_PREFIX."storage";
            $result = $db->query ($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $result->free();
                $res = $row["path"];
            } else if ($result->num_rows == 0) {
                $res = array();
            } else {
                throw new UserControllerException($lang->error->cantGetPath);
            }
            return $res;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function setConfiguration(Chat $_chat, $_idTelegram, $_configuration) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "UPDATE `".DB_PREFIX."user_group` SET `configuration` = '".$_configuration."'
                    WHERE id_user = '".$_idTelegram."'
                    AND id_group = '".$_chat->getId()."'";
            $result = $db->query($sql);
            $db->close();
            if (!$result) {
                throw new UserControllerException("Errore durante l'aggiornamento della configurazione dell'utente.");
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }        
    }
}

class UserControllerException extends Exception { }