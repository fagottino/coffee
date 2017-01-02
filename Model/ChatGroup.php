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
    protected $amaa;
    
    public function __construct($_chat) {
        $this->title = $_chat["title"];
        $this->amaa = $_chat["all_members_are_administrators"];
        $this->idChat = $_chat["id"];
        $this->type = $_chat["type"];
    }
}
