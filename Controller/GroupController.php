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
                throw new GroupControllerException($lang->error->noResultsFound);
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
            
            $associateUser = $db->query("INSERT INTO ".DB_PREFIX."user_group (id_user, id_group, bot_owner) VALUES('".$_user->getIdTelegram()."', '".$_user->getChat()->getId()."', '1')");
            if (!$associateUser) {
                throw new GroupControllerException($lang->error->errorWhileUserAssociate);
            }
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
    
//    public function setActive(User $_user, $_bool) {
//        global $lang;
//        try {
//            $db = Database::getConnection();
//            
//            $sql = "UPDATE ".DB_PREFIX."user_group SET active = '".$_bool."' WHERE id_user = '".$_user->getIdTelegram()."' AND id_group = '".$_user->getChat()->getId()."' AND leaves = '0'";
//            $updateUser = $db->query($sql);
//            if (!$updateUser) {
//                throw new GroupControllerException($lang->error->errorWhileUserUpdate);
//            }
//        } catch (DatabaseException $ex) {
//            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
//        }
//    }
    
    public function getCompetitors(Chat $_chat) {
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT ".DB_PREFIX."user.id_telegram AS id_user, ".DB_PREFIX."user.name FROM ".DB_PREFIX."user JOIN ".DB_PREFIX."user_group ON ".DB_PREFIX."user.id_telegram = ".DB_PREFIX."user_group.id_user WHERE ".DB_PREFIX."user_group.id_group = '".$_chat->getId()."' AND ".DB_PREFIX."user_group.partecipate = '1' AND ".DB_PREFIX."user_group.leaves = '0'";
            $query = $db->query($sql);
            
            if (mysqli_num_rows($query) > 0) {
                while($tmp = $query->fetch_assoc())
                    $res[] = $tmp;

                $db->close();
            } else if (mysqli_num_rows($query) == 0) {
                $res = array();
            } else {
                throw new GroupControllerException($lang->error->noResultsFound);
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
                    AND coffee_user_group.partecipate = '1'
                    AND ".DB_PREFIX."user.id_telegram NOT IN
                    (SELECT ".DB_PREFIX."paid_coffee_people.id_user FROM ".DB_PREFIX."paid_coffee
                    JOIN ".DB_PREFIX."paid_coffee_people ON ".DB_PREFIX."paid_coffee.id_paid_coffee = ".DB_PREFIX."paid_coffee_people.id_paid_coffee
                    WHERE ".DB_PREFIX."paid_coffee.set_by = '".$_user->getIdTelegram()."'
                    AND ".DB_PREFIX."paid_coffee.id_group = '".$_user->getChat()->getId()."'
                    AND ".DB_PREFIX."paid_coffee.powered_by IS NULL)
                    ";

            $query = $db->query($sql);
            
            if (!$query) {
                throw new GroupControllerException($lang->error->noResultsFound);
            } else if (mysqli_num_rows($query) == 0) {
                $res = array();
            } else {
                while($tmp = $query->fetch_assoc()) {
                    $res[] = $tmp;
                }

                $db->close();
            }
            return $res;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function getAllCompetitors(User $_user) {
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
                throw new GroupControllerException("Errore mentre aggiornavo l'entrata nel gruppo.");
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
                throw new GroupControllerException("Errore mentre aggiornavo l'uscita dal gruppo.");
            }
            
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function createText($_data) {
        $text = "I concorrenti sono:".chr(10);
        $i = 0;
        foreach ($_data  as $key => $value) {
            $text .= ++$i.") ".$value["name"].chr(10);
        }
        return $text;
    }
    
    public function checkAllUserOperation() {
        // QUERY FUNZIONANTE. CONTROLLARE SE CI SONO ALTRI UTENTI NELLO STESSO GRUPPO CHE STANNO GIÀ AGGIUNGENDO UN PAGAMENTO DI CAFFÈ
        // 
        //SELECT operation FROM coffee_user WHERE id_telegram IN (SELECT id_user FROM coffee_user_group WHERE id_group = '-114342037' AND active = '1')
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
            
            //if ($query->num_rows > 0) {
            if (mysqli_num_rows($query) > 0) {
                while($tmp = $query->fetch_assoc()) {
                    $res[] = $tmp;
                }
                $db->close();
            } else {
                $res = array();
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
            } else {
                $res = array();
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
            $group = $result->fetch_assoc();
            if ($group["partecipate"] == 1) {
                $value = 0;
            } else {
                $value = 1;
            }
            
            $sql = "UPDATE ".DB_PREFIX."user_group SET partecipate = ".$value." WHERE id_group = ".$_idGroup." AND id_user = ".$_user->getIdTelegram();
            $db->query($sql);
            return $value;
            
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function getOlderMember(User $_user) {
//        global $lang;
//        try {
//            $db = Database::getConnection();
//            
//            $sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name FROM ".DB_PREFIX."user_group JOIN ".DB_PREFIX."user ON ".DB_PREFIX."user_group.id_user = ".DB_PREFIX."user.id_telegram WHERE ".DB_PREFIX."user_group.id_group = '".$_user->getChat()->getId()."' AND ".DB_PREFIX."user_group.active = '1'  AND ".DB_PREFIX."user_group.partecipate = '1' AND ".DB_PREFIX."user_group.leaves = '0'";
//            $result = $db->query($sql);
//            while ($singleUser = $result->fetch_assoc()) {
//                $user[] = $singleUser; 
//            }
//            
//            if (mysqli_num_rows($result) > 0) {
//                return $user;
//            } else {
//                //throw new GroupControllerException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
//                return 0;
//            }
//        } catch (DatabaseException $ex) {
//            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
//        }
        global $lang;
        try {
            $db = Database::getConnection();
            
            $sql = "SELECT ".DB_PREFIX."user.id_telegram, ".DB_PREFIX."user.name FROM ".DB_PREFIX."user_group JOIN ".DB_PREFIX."user ON ".DB_PREFIX."user_group.id_user = ".DB_PREFIX."user.id_telegram WHERE ".DB_PREFIX."user_group.id_group = '".$_user->getChat()->getId()."' AND ".DB_PREFIX."user_group.partecipate = '1' AND ".DB_PREFIX."user_group.leaves = '0'";
            $result = $db->query($sql);
            
            if (mysqli_num_rows($result) > 0) {
                while ($singleUser = $result->fetch_assoc()) {
                    $user[] = $singleUser; 
                }
            } else if(mysqli_num_rows($result) == 0) {
                $user = array();
            } else {
                throw new GroupControllerException("Errore nella query per recuperare i vecchi partecipanti al gioco.");
            }
            return $user;
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }
    }
    
    public function resetParticipate(Chat $_chat) {
        global $lang;
        try {
            $db = Database::getConnection();
            $sql = "UPDATE ".DB_PREFIX."user_group SET partecipate = '0' WHERE id_group = ".$_chat->getId();
            $result = $db->query($sql);
            
            if (!$result) {
                throw new GroupControllerException("Errore nella query per aggiornare la partecipazione al gioco.");
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
                throw new GroupControllerException("Errore nella query per recuperare la lingua del gruppo.");
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
                throw new UserControllerException("Errore durante l'aggiornamento della lingua del gruppo");
            }
        } catch (DatabaseException $ex) {
            throw new DatabaseException($ex->getMessage().$lang->general->line.$ex->getLine().$lang->general->code.$ex->getCode());
        }        
    }
}

class GroupControllerException extends Exception { }