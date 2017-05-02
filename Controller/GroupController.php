<?php
require_once './Model/Database.php';
require_once './Model/User.php';
$lang = Lang::getLang();

/**
 * Description of GroupController
 *
 * @author fagottino
 */
class GroupController {
    
    public function __construct() {
        
    }
    
    public function checkGroup($_idGroup) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "SELECT * FROM ".DB_PREFIX."group WHERE ".DB_PREFIX."group.id_group = '".$_idGroup."'";
            $result = $db->query($sql);
            if (mysqli_num_rows($result) > 0) {
                $row = $result->fetch_assoc();
                $result->free();
            } else if (mysqli_num_rows($result) == 0) {
                $row = array();
            } else {
                throw new GroupControllerException($lang->error->checkGroup);
            }
            return $row;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function insert(Chat $_chat) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "INSERT INTO ".DB_PREFIX."group (id_group, title".($_chat->getAmaa() != null ? ", amaa" : "").") VALUES('".$_chat->getId()."', '".$_chat->getTitle().($_chat->getAmaa() != null ? "', '".$_chat->getAmaa() : "")."')";
            $result = $db->query($sql);
            
            if (!$result) {
                throw new GroupControllerException($lang->error->errorWhileUserRegistrationInGroup);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function associateUser(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT * FROM ".DB_PREFIX."user_group WHERE id_user = '".$_user->getIdTelegram()."' AND id_group = '".$_user->getChat()->getId()."' AND leaves = 0";
            $result = $db->query($sql);
            
            if (mysqli_num_rows($result) == 0) {
                $sql = "INSERT INTO ".DB_PREFIX."user_group (id_user, id_group, bot_owner) VALUES('".$_user->getIdTelegram()."', '".$_user->getChat()->getId()."', '1')";
                $result = $db->query($sql);
                if (!$result) {
                    throw new GroupControllerException($lang->error->errorWhileRegisterParticipate);
                }
            } else {
                $sql = "UPDATE ".DB_PREFIX."user_group SET bot_owner = '1' WHERE id_user = '".$_user->getIdTelegram()."' AND id_group = '".$_user->getChat()->getId()."'";
                $result = $db->query($sql);
                if (!$result) {
                    throw new GroupControllerException($lang->error->errorWhileUserAssociate);
                }
            }
            return true;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function setParticipate(User $_user, $_partecipate) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT * FROM ".DB_PREFIX."user_group WHERE id_user = '".$_user->getIdTelegram()."' AND id_group = '".$_user->getChat()->getId()."' AND leaves = 0";
            $result = $db->query($sql);
            
            if (mysqli_num_rows($result) == 0) {
                $sql = "INSERT INTO ".DB_PREFIX."user_group (id_user, id_group, partecipate) VALUES('".$_user->getIdTelegram()."', '".$_user->getChat()->getId()."', '".$_partecipate."')";
                $result = $db->query($sql);
                if (!$result) {
                    throw new GroupControllerException($lang->error->errorWhileRegisterParticipate);
                }
            } else {
                $sql = "UPDATE ".DB_PREFIX."user_group SET partecipate = ".$_partecipate." WHERE id_user = '".$_user->getIdTelegram()."' AND id_group = '".$_user->getChat()->getId()."'";
                $result = $db->query($sql);
                if (!$result) {
                    throw new GroupControllerException($lang->error->errorWhileUptadingParticipated);
                }
            }
            return true;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
        return true;
    }
    
    public function setLeave(User $_user, $_idUser = null, $_leave) {
        global $lang;
        try {
            $db = Database::getConnection();
            if ($_idUser != null) {
                $sql = "UPDATE ".DB_PREFIX."user_group SET leaves = '".$_leave."' WHERE id_user = '".$_idUser."' AND id_group = '".$_user->getChat()->getId()."'";
            } else {
                $sql = "UPDATE ".DB_PREFIX."user_group SET leaves = '".$_leave."' WHERE id_user = '".$_user->getIdTelegram()."' AND id_group = '".$_user->getChat()->getId()."'";
            }
            $result = $db->query($sql);
            if (!$result) {
                throw new GroupControllerException($lang->error->errorWhileUptadingParticipated);
            }
            return true;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
        return true;
    }
    
    public function getCompetitors(Chat $_chat) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT ".DB_PREFIX."user.id_telegram AS id_user, ".DB_PREFIX."user.name, ".DB_PREFIX."user.username, ".DB_PREFIX."user_group.configuration FROM ".DB_PREFIX."user
                    JOIN ".DB_PREFIX."user_group ON ".DB_PREFIX."user.id_telegram = ".DB_PREFIX."user_group.id_user
                    WHERE ".DB_PREFIX."user_group.id_group = '".$_chat->getId()."'
                    AND ".DB_PREFIX."user_group.partecipate = '1'
                    AND ".DB_PREFIX."user_group.configuration = '0'
                    AND ".DB_PREFIX."user_group.leaves = '0'";
            $query = $db->query($sql);
            
            if (mysqli_num_rows($query) > 0) {
                while($tmp = $query->fetch_assoc())
                    $res[] = $tmp;

                $db->close();
            } else if (mysqli_num_rows($query) == 0) {
                $res = array();
            } else {
                throw new GroupControllerException($lang->error->getCompetitors);
            }
            return $res;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function checkConfiguration(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT ".DB_PREFIX."usergroup.configuration FROM ".DB_PREFIX."user_group
                    WHERE ".DB_PREFIX."user_group.id_group = '".$_user->getChat()->getId()."'
                    AND ".DB_PREFIX."user_group.id_user = '".$_user->getIdTelegram()."'
                    AND ".DB_PREFIX."user_group.leaves = '0'";
            $query = $db->query($sql);
            
            if (mysqli_num_rows($query) > 0) {
                $res = $query->fetch_assoc();

                $db->close();
            } else if (mysqli_num_rows($query) == 0) {
                $res = array();
            } else {
                throw new GroupControllerException($lang->error->getCompetitors);
            }
            return $res;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function getAllCompetitors(Chat $_chat) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT ".DB_PREFIX."user.id_telegram AS id_user, ".DB_PREFIX."user.name, ".DB_PREFIX."user.username, ".DB_PREFIX."user_group.configuration FROM ".DB_PREFIX."user
                    JOIN ".DB_PREFIX."user_group ON ".DB_PREFIX."user.id_telegram = ".DB_PREFIX."user_group.id_user
                    WHERE ".DB_PREFIX."user_group.id_group = '".$_chat->getId()."'
                    AND ".DB_PREFIX."user_group.partecipate = '1'
                    AND ".DB_PREFIX."user_group.leaves = '0'";
            $query = $db->query($sql);
            
            if (mysqli_num_rows($query) > 0) {
                while($tmp = $query->fetch_assoc())
                    $res[] = $tmp;

                $db->close();
            } else if (mysqli_num_rows($query) == 0) {
                $res = array();
            } else {
                throw new GroupControllerException($lang->error->getCompetitors);
            }
            return $res;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function getOtherCompetitors(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name FROM ".DB_PREFIX."user
                    JOIN ".DB_PREFIX."user_group ON ".DB_PREFIX."user.id_telegram = ".DB_PREFIX."user_group.id_user
                    WHERE ".DB_PREFIX."user_group.id_group = '".$_user->getChat()->getId()."'
                    AND coffee_user_group.leaves = '0'
                    AND coffee_user_group.configuration = '0'
                    AND coffee_user_group.partecipate = '1'
                    AND ".DB_PREFIX."user.id_telegram NOT IN
                    (SELECT ".DB_PREFIX."paid_coffee_people.id_user FROM ".DB_PREFIX."paid_coffee
                    JOIN ".DB_PREFIX."paid_coffee_people ON ".DB_PREFIX."paid_coffee.id_paid_coffee = ".DB_PREFIX."paid_coffee_people.id_paid_coffee
                    WHERE ".DB_PREFIX."paid_coffee.set_by = '".$_user->getIdTelegram()."'
                    AND ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."'
                    AND ".DB_PREFIX."paid_coffee.powered_by IS NULL)
                    ";

            $query = $db->query($sql);
            
            if (mysqli_num_rows($query) > 0) {
                while($tmp = $query->fetch_assoc())
                    $res[] = $tmp;

                $db->close();
            } else if (mysqli_num_rows($query) == 0) {
                $res = array();
            } else {
                throw new GroupControllerException($lang->error->getOtherCompetitors);
            }
            return $res;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function countCompetitors(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT COUNT(".DB_PREFIX."paid_coffee_people.id_paid_coffee_people) FROM ".DB_PREFIX."paid_coffee_people
                    JOIN ".DB_PREFIX."paid_coffee ON ".DB_PREFIX."paid_coffee_people.id_paid_coffee = ".DB_PREFIX."paid_coffee.id_paid_coffee
                    WHERE ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."'
                    AND ".DB_PREFIX."paid_coffee.set_by = '".$_user->getIdTelegram()."'
                    AND ".DB_PREFIX."paid_coffee.powered_by IS NULL";
            $result = $db->query($sql);
            
            return $result->fetch_array();
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function activateGroup(Chat $_chat) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "UPDATE ".DB_PREFIX."group SET leaves = '0' WHERE id_group = ".$_chat->getId();
            $result = $db->query($sql);
            
            if (!$result) {
                throw new GroupControllerException($lang->error->activateGroup);
            }
            
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function leaveGroup(Chat $_chat) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "UPDATE ".DB_PREFIX."group SET leaves = '1' WHERE id_group = ".$_chat->getId();
            $result = $db->query($sql);
            
            if (!$result) {
                throw new GroupControllerException($lang->error->leaveGroup);
            }
            
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function createText($_data) {
        global $lang;
        $text = $lang->ui->theCompetitorsAre.chr(10).chr(10);
        $i = 0;
        foreach ($_data  as $key => $value) {
//                          $competitors[$key]['text'] = "a"."&#822;"."n"."&#822;"."t"."&#822;"."o"."&#822;"."n"."&#822;"."i"."&#822;"."o"."&#822;";
            if ($value["configuration"] == 1) {
                $text .= ++$i."<b>)</b> ";
                $text .= $this->strikethrougText($value["name"]);
                $text .= " (deve riconfermare la volontà a partecipare)".chr(10);
            } else {
                $text .= ++$i.") ".$value["name"].chr(10);
            }
        }
        return $text;
    }
    
    public function getMyGroup(User $_user) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT ".DB_PREFIX."group.id_group, ".DB_PREFIX."group.title, ".DB_PREFIX."user_group.partecipate FROM ".DB_PREFIX."user_group
                    JOIN ".DB_PREFIX."group ON ".DB_PREFIX."user_group.id_group = ".DB_PREFIX."group.id_group
                    WHERE ".DB_PREFIX."user_group.id_user = '".$_user->getIdTelegram()."'
                    AND ".DB_PREFIX."user_group.leaves = '0'
                    ";

            $query = $db->query($sql);
            
            if (mysqli_num_rows($query) > 0) {
                while($tmp = $query->fetch_assoc()) {
                    $res[] = $tmp;
                }
                $db->close();
            } else if (mysqli_num_rows($query) == 0) {
                $res = array();
            } else {
                throw new GroupControllerException($lang->error->getMyGroup);
            }
            return $res;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function getGroupInfo(User $_user, $_idGroup) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT * FROM ".DB_PREFIX."user_group WHERE ".DB_PREFIX."user_group.id_user = '".$_user->getIdTelegram()."' AND ".DB_PREFIX."user_group.id_group = '".$_idGroup."'";

            $query = $db->query($sql);
            
            if (mysqli_num_rows($query) > 0) {
                    $res[] = $query->fetch_assoc();
                $db->close();
            } else if (mysqli_num_rows($query) == 0) {
                $res = array();
            } else {
                throw new GroupControllerException($lang->error->getGroupInfo);
            }
            return $res;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function joinTheGame(User $_user, $_idGroup) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT partecipate FROM ".DB_PREFIX."user_group WHERE id_group = ".$_idGroup." AND id_user = ".$_user->getIdTelegram();
            $result = $db->query($sql);
            if (!$result) {
                throw new GroupControllerException($lang->error->joinTheGameSelect);
            }
            $group[] = $result->fetch_assoc();
            if ($group[0]["partecipate"] == 1) {
                $value = 0;
            } else if ($group[0]["partecipate"] == 0) {
                $value = 1;
            }
            
            $sql = "UPDATE ".DB_PREFIX."user_group SET partecipate = ".$value." WHERE id_group = ".$_idGroup." AND id_user = ".$_user->getIdTelegram();
            $db->query($sql);
            if (!$result) {
                throw new GroupControllerException($lang->error->joinTheGameUpdate);
            }
            return $value;
            
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function getOlderMember(User $_user, $_all = false) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name, ".DB_PREFIX."user.username FROM ".DB_PREFIX."user_group
                JOIN ".DB_PREFIX."user ON ".DB_PREFIX."user_group.id_user = ".DB_PREFIX."user.id_telegram
                WHERE ".DB_PREFIX."user_group.id_group = '".$_user->getChat()->getId()."'
                AND ".DB_PREFIX."user_group.leaves = '0'";
            $result = $db->query($sql);
            
            if (mysqli_num_rows($result) > 1) {
                while ($singleUser = $result->fetch_assoc()) {
                    $user[] = $singleUser; 
                }
            } else if(mysqli_num_rows($result) == 0 || mysqli_num_rows($result) == 1) {
                $user = array();
            } else {
                throw new GroupControllerException($lang->error->getOlderMember);
            }
            return $user;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function resetParticipate($_idChat) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "UPDATE ".DB_PREFIX."user_group SET partecipate = '0', configuration = '0' WHERE id_group = ".$_idChat;
            $result = $db->query($sql);
            
            if (!$result) {
                throw new GroupControllerException($lang->error->resetParticipate);
            }
            
            return true;            
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function getLang($_idGroup) {
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT ".DB_PREFIX."lang.name_lang FROM ".DB_PREFIX."group JOIN ".DB_PREFIX."lang ON ".DB_PREFIX."group.id_lang = ".DB_PREFIX."lang.id_lang WHERE ".DB_PREFIX."group.id_group = '".$_idGroup."' AND ".DB_PREFIX."group.leaves = '0'";
            $result = $db->query($sql);
            
            if (mysqli_num_rows($result) > 0) {
                while ($row = $result->fetch_assoc()) {
                    $lang = $row["name_lang"]; 
                }
            } else if(mysqli_num_rows($result) == 0) {
                $lang = "";
            } else {
                throw new GroupControllerException($lang->error->getLang);
            }
            return $lang;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function updateLang($_user, $_idGroup) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "UPDATE `".DB_PREFIX."group` SET `id_lang` = (SELECT id_lang FROM `".DB_PREFIX."lang` WHERE name_lang = '".$_user->getChat()->getLang()."') WHERE id_group = '".$_idGroup."'";
            $result = $db->query($sql);
            $db->close();
            if (!$result) {
                throw new UserControllerException($lang->error->updateLang);
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }        
    }
    
    public function checkUserInGroup($_user, $_idUser) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            if ($_idUser != null) {
                $sql = "SELECT COUNT(".DB_PREFIX."user_group.id_user) FROM ".DB_PREFIX."user_group
                    WHERE ".DB_PREFIX."user_group.id_group = '".$_user->getChat()->getId()."'
                    AND ".DB_PREFIX."user_group.id_user = '".$_idUser."'";
            } else {
                $sql = "SELECT COUNT(".DB_PREFIX."user_group.id_user) FROM ".DB_PREFIX."user_group
                    WHERE ".DB_PREFIX."user_group.id_group = '".$_user->getChat()->getId()."'
                    AND ".DB_PREFIX."user_group.id_user = '".$_user->getIdTelegram()."'";
            }
            $result = $db->query($sql);
            if (mysqli_num_rows($result) == 0) {
                $return =  -1;
            } else {
                $return = $result->fetch_assoc();
            }
            return $return;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function strikethrougText($_text) {
        $chars = str_split($_text);
            $text = "";
            foreach($chars as $char){
                $text .= $char."&#822;";
            }
        return $text;
    }
}

class GroupControllerException extends Exception { }