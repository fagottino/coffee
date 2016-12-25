<?php

/**
 * Description of Chat
 *
 * @author fagottino
 */
class Chat {
    private $idChat;
    private $name;
    private $username;
    private $title;
    private $type;
    
    public function __construct($_chat) {
        $this->idChat = $_chat["id"];
        $this->type = $_chat["type"];
        switch ($this->type) {
            case "private":
                $this->name = $_chat["first_name"];
                $this->username = $_chat["username"];
            break;
            case "group":
                $this->title = $_chat["title"];
            break;
            case "":
                
            break;
        }
    }
    
    public function getId() {
        return $this->idChat;
    }
    
    public function setId($_idChat) {
        $this->idChat = $_idChat;
    }
    
    public function getType() {
        return $this->type;
    }
}
