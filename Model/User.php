<?php
require_once './Model/ChatPrivate.php';
require_once './Model/ChatGroup.php';
/**
 * Description of User
 *
 * @author Admin
 */
class User {
    private $idTelegram;
    private $name;
    private $username;
    private $message;
    private $chat;
    private $currentOperation;
    
    public function getUserData($_user) {
        $this->idTelegram = $_user["message"]["from"]["id"];
        $this->name = $_user["message"]["from"]["first_name"];
        $this->username = $_user["message"]["from"]["username"];
        $this->message = $_user["message"]["text"];
//        $this->chat = Chat::getChat($_user["message"]["chat"]);
        switch ($_user["message"]["chat"]["type"]) {
            case "private":
                $this->chat = new ChatPrivate($_user["message"]["chat"]);
                break;
            case "group":
                $this->chat = new ChatGroup($_chat);
                break;
            default:
            break;
        }
        
        if ($this->idTelegram == null || $this->name == null || $this->username == null || $this->message == null)
            return false;
    }
    
    public function setUserDataFromDb($_user) {
        $this->currentOperation = $_user['name_operation'];
    }
    
    public function getIdTelegram() {
        return $this->idTelegram;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    public function getChat() {
        return $this->chat;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
    public function setMessage($_message) {
        $this->message = $_message;
    }
    
    public function getCurrentOperation() {
        return $this->currentOperation;
    }
    
    public function setCurrentOperation($_operation) {
        $this->currentOperation = $_operation;
    }
}
