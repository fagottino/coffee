<?php
require_once './Model/Chat.php';
/**
 * Description of ChatPublic
 *
 * @author fagottino
 */
class ChatMember extends Chat {
    protected $idChat;
    protected $firstName;
    protected $username;
    
    public function __construct($_requestName, $_request) {
        parent::__construct();
        $this->type = $_requestName;
        $this->idChat = $_request["id"];
        $this->firstName = $_request["first_name"];
        ($_request["username"] != "" ? $this->username = $_request["username"] : "");
    }
}
