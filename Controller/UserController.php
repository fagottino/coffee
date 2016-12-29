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
    
    public function getMessageData($_data) {
        $idTelegram = $_data["message"]["from"]["id"];
        $name = $_data["message"]["from"]["first_name"];
        $username = $_data["message"]["from"]["username"];
        $idChat = $_data["message"]["chat"]["id"];
        $message = $_data["message"]["text"];

        $user = new User();
        $user->createUser($idTelegram, $name, $message, $idChat, START_OPERATION, (isset($username) ? $username : NULL));
        return $user;
    }
    
    public function getInfo($_idTelegram) {
        global $lang;
        try {
            $db = Database::getConnection();
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
        
        //$result = $db->query("SELECT * FROM utente AS u WHERE u.id_telegram = '".$_idTelegram."'");
        $result = $db->query("SELECT utente.nome, utente.id_telegram, operation.name_operation FROM utente JOIN operation ON utente.operation = operation.id_operation WHERE utente.id_telegram = '".$_idTelegram."'");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $result->free();
            return $row;
        } else {
            throw new DatabaseException($lang->error->noResultsFound);
        }
    }
    
    public function getAllUserName() {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $result = $db->query ("SELECT nome FROM utente");
            if ($result) {
                if ($result->num_rows > 0) {
                    return $result;
                } else {
                    throw new DatabaseException($lang->error->cantGetNameOfUser);
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
            
            $result = $db->query("INSERT INTO utente (nome, id_telegram, operation) VALUES('".$_user->getName()."', '".$_user->getIdTelegram()."', '6')");
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
    
    public function updateCurrentOperation($_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "UPDATE `utente` SET `operation` = (SELECT id_operation FROM `operation` WHERE name_operation = '".$_user->getCurrentOperation()."') WHERE id_telegram = '".$_user->getIdTelegram()."'";
            
            $result = $db->query ($sql);
            $db->close();
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }        
    }
    
    public function getCurrentOperation(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $result = $db->query ("SELECT operation FROM utente WHERE id_telegram = ".$_user->getIdTelegram()."");
            if ($result) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $result->free();
                    return $row["operation"];
                } else {
                    throw new DatabaseException($lang->error->cantGetLastOperation);
                }
            }
            
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
}

class UserControllerException extends Exception { }