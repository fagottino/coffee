<?php
require_once './Model/Chat.php';
/**
 * Description of ChatPrivate
 *
 * @author fagottino
 */
class ChatPrivate extends Chat {
    protected $name;
    protected $username;
    
    public function __construct($_chat) {
        parent::__construct();
        $this->name = $_chat["first_name"];
        $this->username = $_chat["username"];
        $this->idChat = $_chat["id"];
        $this->type = $_chat["type"];
    }
}
