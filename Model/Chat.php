<?php
/**
 * Description of Chat
 *
 * @author fagottino
 */
class Chat {

    protected $type;
    protected $lang;

    public function __construct() {
        
    }

    public function getName() {
        return $this->name;
    }

    public function getUsername() {
        if ($this->username != null)
            return $this->username;
        else
            throw new ChatException("");
    }

    public function getId() {
        return $this->idChat;
    }

    public function getType() {
        if ($this->type != null)
            return $this->type;
        else
            throw new ChatException("");
    }

    public function getRequestType() {
        if ($this->requestType != null)
            return $this->requestType;
        else
            throw new ChatException("");
    }

    public function getFirstname() {
        if ($this->firstname != null)
            return $this->firstname;
        else
            throw new ChatException("");
    }

    public function getTitle() {
        return $this->title;
    }
    
    public function getAmaa() {
        return $this->amaa;
    }
    
    public function getOperation() {
        return $this->operation;
    }
    
    public function getLang() {
        return $this->lang;
    }
    
    public function setLang($_lang) {
        $this->lang = $_lang;
    }
}

class ChatException extends Exception { }
