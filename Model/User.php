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
    private $lang;
    private $chat;
    private $currentOperation;
    
    public function getUserData($_user) {
        if (isset($_user["message"])) {
            $this->idTelegram = $_user["message"]["from"]["id"];
            $this->name = $_user["message"]["from"]["first_name"];
            $this->username = $_user["message"]["from"]["username"];
            $this->message = $_user["message"]["text"];

            switch ($_user["message"]["chat"]["type"]) {
                case "private":
                    $this->chat = new ChatPrivate($_user["message"]["chat"]);
                    break;
                case "group":
                    $this->chat = new ChatGroup($_user["message"]["chat"]);
                    break;
                default:
                break;
            }
        } else if ($this->idTelegram == null || $this->name == null || $this->username == null || $this->message == null) {
            $this->idTelegram = $_user["callback_query"]["from"]["id"];
            $this->name = $_user["callback_query"]["from"]["first_name"];
            $this->username = $_user["callback_query"]["from"]["username"];
            $this->message = $_user["callback_query"]["data"];
            
            switch ($_user["callback_query"]["message"]["chat"]["type"]) {
                case "private":
                    $this->chat = new ChatPrivate($_user["callback_query"]["message"]["chat"]);
                    break;
                case "group":
                    $this->chat = new ChatGroup($_chat);
                    break;
                default:
                break;
            }
        }
        
        if ($this->idTelegram == null || $this->name == null || $this->username == null || $this->message == null)
            return false;
    }
    
    public function setUserDataFromDb($_user) {
        $this->currentOperation = $_user['name_operation'];
        $this->lang = $_user['name_lang'];
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
    
    public function getMessage() {
        return $this->message;
    }
    
    public function getMessageId($_array) {
        return $_array["callback_query"]["message"]["message_id"];
    }
    
    public function getCallbackQueryId($_array) {
        return $_array["callback_query"]["id"];
    }
    
    public function getLang() {
        return $this->lang;
    }
    
    public function getChat() {
        return $this->chat;
    }
    
    public function setMessage($_message) {
        $this->message = $_message;
    }
    
    public function setLang($_lang) {
        $this->lang = $_lang;
    }
    
    public function getCurrentOperation() {
        return $this->currentOperation;
    }
    
    public function setCurrentOperation($_operation) {
        $this->currentOperation = $_operation;
    }
}
