<?php
/**
 * Description of User
 *
 * @author Admin
 */
class User {
    private $idTelegram;
    private $name;
    private $username;
    private $chatId;
    private $message;
    private $nomeBenefattore;
    private $currentOperation;
    
    public function getUserData($_user) {
        $this->idTelegram = $_user["message"]["from"]["id"];
        $this->name = $_user["message"]["from"]["first_name"];
        $this->username = $_user["message"]["from"]["username"];
        $this->chatId = $_user["message"]["chat"]["id"];
        $this->message = $_user["message"]["text"];
    }
    
    public function setUserDataFromDb($_user) {
        $this->currentOperation = $_user['operation'];
    }
    
    public function getIdTelegram() {
        return $this->idTelegram;
    }
    
    public function setIdTelegram($_idTelegram) {
        $this->idTelegram = $_idTelegram;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    public function getChatId() {
        return $this->chatId;
    }
    
    public function setChatId($_chatId) {
        $this->chatId = $_chatId;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
    public function setMessage($_message) {
        $this->message = $_message;
    }
    
    public function getNomeBenefattore() {
        return $this->nomeBenefattore;
    }
    
    public function setNomeBenefattore($_name) {
        $this->nomeBenefattore = $_name;
    }
    
    public function getOperation() {
        return $this->currentOperation;
    }
    
    public function setOperation($_operation) {
        $this->currentOperation = $_operation;
    }
}
