<?php
require_once './Model/Chat.php';
/**
 * Description of ChatPublic
 *
 * @author fagottino
 */
class ChatGroup extends Chat {
    protected $title;
    // amad = ALL MEMEMBERS OF GROUP ARE ADMINISTRATOR
    protected $amad;
    
    public function __construct($_chat) {
        $this->title = $_chat["title"];
        $this->amad = $_chat["all_members_are_administrators"];
        $this->idChat = $_chat["id"];
        $this->type = $_chat["type"];
    }
}
