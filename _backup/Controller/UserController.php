<?php
require_once './Model/Database.php';
require_once './Model/User.php';

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
        try {
            $db = Database::getConnection();
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage()." Line -> ".$ex->getLine()." Code -> ".$ex->getCode());
        }
        
        $result = $db->query("SELECT * FROM utente AS u WHERE u.id_telegram = '".$_idTelegram."'");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $result->free();
            return $row;
        } else {
            throw new DatabaseException("Nessun risultato trovato");
        }
    }
    
    public function getAllUserName() {
        try {
            $db = Database::getConnection();
            
            $result = $db->query ("SELECT nome FROM utente");
            if ($result) {
                if ($result->num_rows > 0) {
                    return $result;
                } else {
                    throw new DatabaseException("Errore durante la lettura dei nomi degli utenti.");
                }
            }
            
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage()." Line -> ".$ex->getLine()." Code -> ".$ex->getCode());
        }
    }
    
    public function register(User $_user) {
        try {
            $db = Database::getConnection();
            
            $result = $db->query("INSERT INTO utente (nome, id_telegram) VALUES('".$_user->getName()."', '".$_user->getIdTelegram()."')");
            if ($result) {
//                $lastId = $db->insert_id;
//                // Valore opzionale, non presente a tutti i messaggi
//                if ($_user->getUsername())
//                    $addUsername = $db->query ("UPDATE `user` SET username = '".$_user->getUsername()."' WHERE id_user = ".$lastId." AND id_telegram = '".$_user->getIdTelegram()."'");
//                // Valore opzionale, non presente a tutti i messaggi
//                if ($_user->getIdChat())
//                    $addChatId = $db->query ("UPDATE `user` SET chat_id = '".$_user->getIdChat()."' WHERE id_user = ".$lastId." AND id_telegram = '".$_user->getIdTelegram()."'");
            } else {
                throw new UserControllerException("Errore nella registrazione dell'utente.");
            }
            
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage()." Line -> ".$ex->getLine()." Code -> ".$ex->getCode());
        }
    }
    
    public function updateCurrentOperation($_user) {
        try {
            $db = Database::getConnection();
            
            $result = $db->query ("UPDATE utente SET operation = '".$_user->getOperation()."' WHERE id_telegram = ".$_user->getIdTelegram()."");
            
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage()." Line -> ".$ex->getLine()." Code -> ".$ex->getCode());
        }
    }
    
    public function getCurrentOperation(User $_user) {
        try {
            $db = Database::getConnection();
            
            $result = $db->query ("SELECT operation FROM utente WHERE id_telegram = ".$_user->getIdTelegram()."");
            if ($result) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $result->free();
                    return $row["operation"];
                } else {
                    throw new DatabaseException("Errore durante la lettura dell'operazione corrente.");
                }
            }
            
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage()." Line -> ".$ex->getLine()." Code -> ".$ex->getCode());
        }
    }
}

class UserControllerException extends Exception { }