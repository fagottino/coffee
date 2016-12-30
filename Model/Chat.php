<?php
/**
 * Description of Chat
 *
 * @author fagottino
 */
class Chat {

    protected $type;

    public function __construct() {
        
    }

//    public static function getChat($_chat) {
//        $type = $_chat["type"];
//        switch ($type) {
//            case "private":
//                $chat = new ChatPrivate($_chat);
//                break;
//            case "group":
//                $chat = new ChatGroup($_chat);
//                break;
//            case "":
//
//                break;
//        }
//        return $chat;
//    }

    public function getName() {
        return $this->name;
    }

    public function getUsername() {
        return $this->username;
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
}

class ChatException extends Exception { }
