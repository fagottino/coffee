<?php
require_once './Model/ChatPrivate.php';
require_once './Model/ChatGroup.php';
require_once './Model/ChatMember.php';
/**
 * Description of User
 *
 * @author Admin
 */
class User {
    private $idTelegram;
    private $name;
    private $username;
    private $idMessage;
    private $message;
    private $lang;
    private $chat;
    private $chatMember;
    private $currentOperation;
    private $groupOperation;
    
    public function getUserData($_user) {
        if (isset($_user["message"])) {
            $this->idTelegram = $_user["message"]["from"]["id"];
            $this->name = $_user["message"]["from"]["first_name"];
            $this->username = $_user["message"]["from"]["username"];
            if (isset($_user["message"]["text"])) {
                $this->idMessage = $_user["message"]["message_id"];
                $this->message = $_user["message"]["text"];
            }

            switch ($_user["message"]["chat"]["type"]) {
                case "private":
                    $this->chat = new ChatPrivate($_user["message"]["chat"]);
                    break;
                case "group":
                case "supergroup":
                    $this->chat = new ChatGroup($_user["message"]["chat"]);
                    
                    if (isset($_user["message"]["new_chat_member"])) {
                        $this->chatMember = new ChatMember("new_chat_member", $_user["message"]["new_chat_member"]);
                    } else if (isset($_user["message"]["left_chat_member"])) {
                        $this->chatMember = new ChatMember("left_chat_member", $_user["message"]["left_chat_member"]);
                    }
                    break;
                default:
                break;
            }
        } else if (isset($_user["callback_query"])) {
            $this->idTelegram = $_user["callback_query"]["from"]["id"];
            $this->name = $_user["callback_query"]["from"]["first_name"];
            $this->username = $_user["callback_query"]["from"]["username"];
            $this->message = $_user["callback_query"]["data"];
            
            switch ($_user["callback_query"]["message"]["chat"]["type"]) {
                case "private":
                    $this->chat = new ChatPrivate($_user["callback_query"]["message"]["chat"]);
                    break;
                case "group":
                case "supergroup":
                    $this->chat = new ChatGroup($_user["callback_query"]["message"]["chat"]);
                    break;
                default:
                break;
            }
        }
    }
    
    public function setUserDataFromDb($_user) {
        $this->currentOperation = $_user['operation'];
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
    
    public function getIdMessage() {
        return $this->idMessage;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
    public function setMessage($_message) {
        $this->message = $_message;
    }
    
    public function getMessageIdCallBack($_array) {
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
    
    public function getChatMember() {
        return $this->chatMember;
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
    
    public function getGroupOperation() {
        return $this->groupOperation;
    }
    
    public function setGroupOperation($_groupOperation) {
        $this->groupOperation = $_groupOperation;
    }
}
