<?php
require_once './Config/Config.php';
require_once './Model/Database.php';
require_once './Model/Lang.php';
require_once './Model/User.php';
require_once './Controller/UserController.php';
require_once './Controller/MenuController.php';
require_once './Controller/GroupController.php';
require_once './Controller/MessageManager.php';
require_once './Controller/Emoticon.php';
clearstatcache();

ini_set('error_reporting', E_ALL);
set_time_limit(0);
$userController = new UserController();
$menuController = new MenuController();
$groupController = new GroupController();
$messageManager = new MessageManager();
$user = new User();
$lang = Lang::getLang();
$newUser = false;
$me = $messageManager->getMe();

$getUnreadMessage = file_get_contents("php://input");
$unreadMessage = json_decode($getUnreadMessage, TRUE);

$whitelist = array('127.0.0.1', "::1");
// PRIVATE
if(in_array(filter_input(INPUT_SERVER,'REMOTE_ADDR'), $whitelist)){
//    $privateMessage = array(
//        "update_id" => 624792369,
//        "message" => array(
//            "message_id" => 1270,
//            "from" => array(
//                "id" => 19179842,
//                "first_name" => "fagottino",
//                "username" => "fagottino"
//            ),
//             "chat" => array(
//                "id" => 19179842,
//                 "first_name" => "fagottino",
//                 "username" => "fagottino",
//                 "type" => "private"
//            ),
//            "date" => 1482502325,
//            "text" => Emoticon::settings().$lang->menu->settings,
//            "entities" => array(
//                "type" => "bot_command",
//                "offset" => 0,
//                "length" => 9
//            )
//        )
//    );
    
// GROUP
//    $privateMessage = array(
//        "update_id" => 627311072,
//        "message" => array(
//            "message_id" => 8932,
//            "from" => array(
//                "id" => 19179842,
//                "first_name" => "fagottino",
//                "username" => "fagottino"
//            ),
//             "chat" => array(
//                "id" => -114342037,
//                "title" => "TestGroup",
//                "type" => "group",
//                "all_members_are_administrators" => true
//            ),
//            "date" => 1483134810,
//            "text" => "\/start@IlBenefattoreDelCaffe_Bot",
//            "entities" => array(
//                "type" => "bot_command",
//                "offset" => 0,
//                "length" => 14
//            )
//        )
//    );
    
// ADD/REMOVE BOT FROM GROUP
//    $privateMessage = array(
//        "update_id" => 624793628,
//        "message" => array(
//            "message_id" => 3407,
//            "from" => array(
//                "id" => 19179842,
//                "first_name" => "fagottino",
//                "username" => "fagottino"
//            ),
//             "chat" => array(
//                "id" => -114342037,
//                "title" => "TestGroup",
//                "type" => "group",
//                "all_members_are_administrators" => true
//            ),
//            "date" => 1483614336,
//            "new_chat_member" => array(
//                "id" => 186132931,
//                "first_name" => "Il benefattore del caff\u00e8",
//                "username" => "IlBenefattoreDelCaffe_Bot"
//            )
//        )
//    );
    
// MESSAGE WITH ANSWER
    $privateMessage = array(
        "update_id" => 624793660,
        "message" => array(
            "message_id" => 3722,
            "from" => array(
                "id" => 19179842,
                "first_name" => "fagottino",
                "username" => "fagottino"
            ),
             "chat" => array(
                "id" => -114342037,
                "title" => "TestGroup",
                "type" => "group",
                "all_members_are_administrators" => true
            ),
            "date" => 1483905746,
            "reply_to_message" => array(
                "message_id" => 3471,
                "from" => array(
                    "id" => 186132931,
                    "first_name" => "Il benefattore del caff\u00e8",
                    "username" => "IlBenefattoreDelCaffe_Bot"
                ),
                 "chat" => array(
                    "id" => -114342037,
                    "title" => "TestGroup",
                    "type" => "group",
                    "all_members_are_administrators" => true
                ),
                "date" => 1483905728,
                "text" => "Ciao a tutti! @fagottino ha il potere.\nPer iniziare chiedete a lui.",
                "entities" => array(
                    "type" => "mention",
                    "offset" => 14,
                    "length" => 10
                )
                ),
            "text" => Emoticon::plus().$lang->menu->chooseBenefactor
                )
            );
    
// CALLACK QUERY
//    $privateMessage = array(
//        "update_id" => 624792859,
//        "callback_query" => array(
//            "id" => "82376795026451405",
//            "from" => array(
//                "id" => 19179842,
//                "first_name" => "fagottino",
//                "username" => "fagottino"
//                ),
//            "message" => array(
//                "message_id" => 2777,
//                "from" => array(
//                    "id" => 186132931,
//                    "first_name" => "Il benefattore del caff\u00e8",
//                    "username" => "IlBenefattoreDelCaffe_Bot"
//                    ),
//                "chat" => array(
//                    "id" => 19179842,
//                    "first_name" => "fagottino",
//                    "username" => "fagottino",
//                    "type" => "private"
//                    ),
//                "date" => 1483050172,
//                "text" => "Setta la linuga:"
//                ),
//            "chat_instance" => "-4206174352189128888",
//            "data" => "it"
//            )
//        );
    
    $unreadMessage = $privateMessage;
}

$user->getUserData($unreadMessage);
if ($user->getIdTelegram() != null) {
    try {
        $userProfile = $userController->getInfo($user->getIdTelegram());
        $user->setUserDataFromDb($userProfile);
        $lang = Lang::getLang($user->getLang());
    } catch (DatabaseException $ex) {
        // se non lo trovo lo registro
        try {
            $userController->register($user);
            $newUser = true;
        } catch (UserControllerException $e) {
            $messageManager->sendSimpleMessage($user->getChat()->getId(), $e->getMessage());
        }
    }

    if($user->getChat()->getType() != "") {
        switch ($user->getChat()->getType()) {
            case "private":
    // PRIVATE CHAT START
                switch ($user->getMessage()) {
                case START:
                    if ($newUser) {
                        $text = 
                            $lang->ui->hi.' <strong>'.$user->getName().'</strong>, '.$lang->ui->welcome.'!'.chr(10)."".chr(10)
                            .$lang->ui->initialText.chr(10)
                            .$lang->ui->initialText1.chr(10).chr(10)
                            .$lang->ui->initialText2.' '.Emoticon::smile().chr(10)
                            .$lang->ui->enjoy.chr(10).chr(10)
                            .$lang->ui->infoBot." <a href='http://www.orlandoantonio.it'>".$lang->ui->clickingHere."</a>."
                        ;
                    } else {
                        $text = '
                            <strong>'.$user->getName().'</strong>, '.$lang->ui->botAlreadyStarted." ".Emoticon::smile().chr(10)."".chr(10)
                            .$lang->ui->sendSomeButtons.chr(10).chr(10)
                            .$lang->ui->giveMeTips." <a href='http://www.orlandoantonio.it'>".$lang->ui->clickingHere."</a> ".Emoticon::smile()
                        ;
                    }
                    //$menu = array(array("action" => Emoticon::plus().$lang->menu->addBenefactor, "alone" => true), array("action" => Emoticon::help().$lang->menu->help, "alone" => false), array("action" => Emoticon::settings().$lang->menu->settings, "alone" => false), array("action" => Emoticon::quit().$lang->menu->quit, "alone" => true));
                    $menu = array(array("action" => Emoticon::group().$lang->menu->yourGroups, "alone" => true), array("action" => Emoticon::help().$lang->menu->help, "alone" => false), array("action" => Emoticon::settings().$lang->menu->settings, "alone" => false), array("action" => Emoticon::quit().$lang->menu->quit, "alone" => true));
                    $customMenu = $menuController->createCustomReplyMarkupMenu($menu);
                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);                
                    break;
                    
                //case Emoticon::help().HELP:
                case Emoticon::help().$lang->menu->help:
                    try {
                        $user->setCurrentOperation("help");
                        $userController->updateCurrentOperation($user);
                        $text = (string)$lang->general->moreInfo;
                        $menu = array(array("action" => $lang->menu->help."1"), array("action" => $lang->menu->help."2"), array("action" => $lang->menu->help."3"));
                        $customMenu = $menuController->createCustomReplyMarkupMenu($menu, true);
                        $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                    } catch (DatabaseException $dbEx) {
                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantUnderstandTypeOfChat);
                    }
                    break;
                
                case Emoticon::settings().$lang->menu->settings:
                    $user->setCurrentOperation("settings");
                    $userController->updateCurrentOperation($user);
//                    $menu = array(array("action" => "Settings 1"), array("action" => "Settings 2"), array("action" => "Settings 3"), array("action" => "Settings 4"), array("action" => "Settings 5"));
//                    $customMenu = $menuController->createCustomReplyMarkupMenu($menu, true);
                    $text = (string)$lang->menu->setLanguage;
                    $menu = array(array("text" => Emoticon::it()."Italiano".($user->getLang() == IT ? Emoticon::check() : ""), "callback_data" => "it"), array("text" => Emoticon::en()."English".($user->getLang() == EN ? Emoticon::check() : ""), "callback_data" => "en"));
                    $customMenu = $menuController->createCustomInlineMenu($menu);
                    try {
                    $messageManager->sendInline($user->getChat()->getId(), $text, $customMenu);
//                    $text = "Setta un'altra cosa:";
//                    $menu = array(array("text" => "Setting 1", "callback_data" => "s1"), array("text" => "Setting 2", "callback_data" => "s2"), array("text" => "Setting 3", "callback_data" => "s3"), array("text" => "Google", "url" => "http://www.google.it"));
//                    $customMenu = $menuController->createCustomInlineMenu($menu);
//                    $messageManager->sendInline($user->getChat()->getId(), $text, $customMenu);
//                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                        
//                        $text = Emoticon::money().Emoticon::money().Emoticon::money().(string)$lang->menu->makeDonation.Emoticon::money().Emoticon::money().Emoticon::money();
//                        $customMenu = $menuController->createCustomInlineMenu();
//                        $messageManager->sendInline($user->getChat()->getId(), $text, $customMenu);
//                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $text);
                    } catch (MessageException $ex) {
                        $messageManager->sendSimpleMessage($user->getIdTelegram(), "UN ERRORE".$ex);
                    }
                    break;
                
                case "it":
                case "en":
                    try {
                        if ($user->getMessage() != $user->getLang()) {
                            $user->setLang($user->getMessage());
                            $userController->updateLang($user);
                            $lang = Lang::getLang($user->getLang());
                            $menu = array(array("action" => Emoticon::group().$lang->menu->yourGroups, "alone" => true), array("action" => Emoticon::help().$lang->menu->help, "alone" => false), array("action" => Emoticon::settings().$lang->menu->settings, "alone" => false), array("action" => Emoticon::quit().$lang->menu->quit, "alone" => true));
                            $customMenu = $menuController->createCustomReplyMarkupMenu($menu);
                            $text = (string)$lang->general->languageSet;
                            $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                            $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $text, true);

                            $menu = array(array(array("text" => Emoticon::it().$lang->general->it.($user->getMessage() == IT ? Emoticon::check() : ""), "callback_data" => "it"), array("text" => Emoticon::en().$lang->general->en.($user->getMessage() == EN ? Emoticon::check() : ""), "callback_data" => "en")));
                            $customMenu = $menuController->createCustomInlineMenu($menu);
                            $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageId($unreadMessage), $text, $menu);
                        } else {
                            $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                        }
                    } catch (DatabaseException $ex) {
                        $messageManager->sendSimpleMessage($ex->getMessage());
                    }
                    break;
                
                case "yes":
                case "no":
                    try {
                        $text = (string)$lang->general->notImplementedYet;
                        $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $text, true);
                        
                        $menu = array(array(array("text" => Emoticon::it().$lang->general->it.($user->getMessage() == IT ? Emoticon::check() : ""), "callback_data" => "it"), array("text" => Emoticon::en().$lang->general->en.($user->getMessage() == EN ? Emoticon::check() : ""), "callback_data" => "en")));
                        $customMenu = $menuController->createCustomInlineMenu($menu);
//                        $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageId($unreadMessage), $text, $menu);
                    } catch (DatabaseException $ex) {
                        $messageManager->sendSimpleMessage($ex->getMessage());
                    }
                    break;
                
                case Emoticon::quit().$lang->menu->quit:
                    $user->setCurrentOperation("quit");
                    $userController->updateCurrentOperation($user);
                    //$item = array(array("text" => $lang->menu->quitNow, "callback_data" => "yes"), array("text" => $lang->menu->justJoking, "callback_data" => "no"));
                    $item = array(array("text" => (string)$lang->menu->quitNow, "callback_data" => "yes"), array("text" => (string)$lang->menu->justJoking, "callback_data" => "no"));
                    $customMenu = $menuController->createCustomInlineMenu($item, false, 2, true);
                    $text = (string)$lang->general->disableBot;
                    $messageManager->sendInline($user->getChat()->getId(), $text, $customMenu);
                    break;
                
                case Emoticon::back().$lang->menu->back:
                    $operation = $user->getCurrentOperation();
                    switch ($operation) {
                        case "help":
                        case "settings":
                            try {
                                $user->setCurrentOperation("home");
                                $userController->updateCurrentOperation($user);
                                $menu = array(array("action" => Emoticon::group().$lang->menu->yourGroups, "alone" => true), array("action" => Emoticon::help().$lang->menu->help, "alone" => false), array("action" => Emoticon::settings().$lang->menu->settings, "alone" => false), array("action" => Emoticon::quit().$lang->menu->quit, "alone" => true));
                                $customMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                $text = $lang->general->youAreHere." ".$lang->menu->home;
                                $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                            } catch (DataBaseException $dbEx) {
                                $text = "QUA1 ".$dbEx->getMessage();
                                $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                            }
                        break;
                    
                    default:
                        break;
                    }
                    break;
                
                    case Emoticon::home().$lang->menu->home:
                        try {
                            $user->setCurrentOperation("home");
                            $userController->updateCurrentOperation($user);
                            //$menu = array(array("action" => Emoticon::help().$lang->menu->help), array("action" => Emoticon::settings().$lang->menu->settings), array("action" => Emoticon::quit().$lang->menu->quit));
                            $menu = array(array("action" => Emoticon::group().$lang->menu->yourGroups, "alone" => true), array("action" => Emoticon::help().$lang->menu->help, "alone" => false), array("action" => Emoticon::settings().$lang->menu->settings, "alone" => false), array("action" => Emoticon::quit().$lang->menu->quit, "alone" => true));
                            $customMenu = $menuController->createCustomReplyMarkupMenu($menu);
                            $text = "Sei al ".$lang->menu->home;
                            $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                        } catch (DataBaseException $dbEx) {
                            $text = "QUA1 ".$dbEx->getMessage();
                            $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                        }
                        break;
                    
                    case Emoticon::plus().$lang->menu->addBenefactor:
                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->notImplementedYet);
                        break;
                    
                    case Emoticon::group().$lang->menu->yourGroups:
                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->notImplementedYet);
                        break;
                    
                default:
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantUnderstandMessage);
        //        $customMenus = $menuController->createInlineMenu($allName);
        //        $textq = "Chi ha pagato i caffè?";
        //        $messageManager->sendInline($user->getChat()->getId(), $textq, $customMenus);
                    break;
                }
                break;
    // PRIVATE CHAT END
    // 
    // GROUP CHAT START
            case "group":
                if ($user->getChatMember() != NULL) {
                    if ($user->getChatMember()->getType() == "new_chat_member") {
                        if ($user->getChatMember()->getId() == $me->result->id) {
                            try {
                                $groupController->checkGroup($user->getChat()->getId());
                            }
                            catch (DatabaseException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                            }
                            catch (GroupControllerException $ex) {
                                try {
                                    $groupController->insert($user->getChat());
                                }
                                catch (DatabaseException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                }
                                catch (GroupControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                }
                            }
                                $groupController->associateUser($user);
                                $text = "Ciao a tutti! @".($user->getUsername() != "" ? $user->getUsername() : $user->getName()). " ha il potere.".chr(10)
                                        ."Per iniziare chiedete a lui.";
                                $menu = array(array("action" => $lang->menu->start.Emoticon::rocket()));
                                $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu, true);
                        } else {
                            // E' stato aggiunto un utente al gruppo
                            // $messageManager->sendSimpleMessage($user->getChat()->getId(), "è quell'altro");
                        }
                    } else { // left_chat_member
                        $groupController->leaveGroup($user->getChat());
                    }
                    $i = 9;
                } else {
                    switch ($user->getMessage()) {
                        case $lang->menu->start.Emoticon::rocket():
                            // Se è admin
                                $text = $lang->ui->letsGo.chr(10)
                                .$lang->ui->sayHi
                                ;
                                $menu = array(array("action" => "Ciao amico".Emoticon::giveMeFive().""));
                                $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu);
                            break;
                        
                        case "Ciao amico".Emoticon::giveMeFive()."":
                            try {
                                $groupController->checkUser($user);
                                $text = "Ciao @".($user->getUsername() != "" ? $user->getUsername() : $user->getName()).Emoticon::victory();
                                $menu = array(array("action" => Emoticon::plus().$lang->menu->chooseBenefactor, "alone" => true), array("action" => Emoticon::lists().$lang->menu->listCompetitors), array("action" => Emoticon::settings().$lang->menu->settings), array("action" => Emoticon::off().$lang->menu->off, "alone" => true));
                                $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu, true);
                            }
                            catch (DatabaseException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                            }
                            break;
                            
                        case Emoticon::plus().$lang->menu->chooseBenefactor:
                            $text = "Ok @".($user->getUsername() != "" ? $user->getUsername() : $user->getName())."!".chr(10);
                            $text .= "Seleziona i nomi dei partecipanti.";
                            try {
                                $competitors = $groupController->getCompetitors($user->getChat(), $me);
                                $i = 0;
                                foreach ($competitors as $key => $value) {
                                    $competitors[$key]['text'] = $value['nome'];
                                    unset($competitors[$key]['nome']);
                                    $competitors[$key]['callback_data'] = "benefactor".$i++;
                                }
                                
                                $menu = $menuController->createCustomInlineMenu($competitors);
                                $messageManager->sendInline($user->getChat()->getId(), $text, $menu);
                            }
                            catch (DatabaseException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                            }
                            catch (GroupControllerException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                            }
                            break;
                        
                        case Emoticon::lists().$lang->menu->listCompetitors:
                            try {
                                $competitors = $groupController->getCompetitors($user->getChat(), $me);
                                $text = $groupController->createText($competitors);
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $text, true);
                            }
                            catch (DatabaseException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                            }
                            catch (GroupControllerException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                            }
                            break;
                        
                        case Emoticon::off().$lang->menu->off:
                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->notImplementedYet);
                            break;
                        
                        default:
                            $text = "Nonono";
                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $text);
                            break;
                    }
//                    $item = array(array("text" => (string)$lang->menu->quitNow, "callback_data" => "yes"), array("text" => (string)$lang->menu->justJoking, "callback_data" => "no"));
//                    $text = Emoticon::money().Emoticon::money().Emoticon::money().(string)$lang->menu->makeDonation.Emoticon::money().Emoticon::money().Emoticon::money();
//                    $customMenu = $menuController->createCustomInlineMenu($item);
//                    $messageManager->sendInline($user->getChat()->getId(), $text, $customMenu);
//                    $menu = array(array("action" => Emoticon::plus().$lang->menu->addBenefactor, "alone" => true), array("action" => Emoticon::help().$lang->menu->help, "alone" => false), array("action" => Emoticon::settings().$lang->menu->settings, "alone" => false), array("action" => Emoticon::quit().$lang->menu->quit, "alone" => true));
//                    $customMenu = $menuController->createCustomReplyMarkupMenu($menu);
//                    $text = "Sei al ".$lang->menu->home;
//                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                }
                break;
    // GROUP CHAT END
            default:
                break;
        }
    } else {
        $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantUnderstandTypeOfChat);
    }
}

$requestFile = "./file/request.txt";
$requestCurrent = file_get_contents($requestFile);
$requestCurrent .= date("d/m/Y H:i:s / ");
$requestCurrent .= json_encode($unreadMessage);
$requestCurrent .= "\n";
file_put_contents($requestFile, $requestCurrent);

function chiPaga() {
    $connection = new Database();
    $conn = $connection->getConnection();
	$stringa = "";
    
    if ($conn->connect_error) {
        trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
    }
    
    $query = "SELECT u.nome, SUM(cp.n_caffe) AS Somma_Caffe FROM caffe_pagati AS cp JOIN utente AS u ON cp.id_utente = u.id_utente GROUP BY cp.id_utente ORDER BY Somma_Caffe ASC";
    $result = $conn->query($query);
    
    if($result === false) {
        trigger_error('Wrong SQL: ' . $query . ' Error: ' . $conn->error, E_USER_ERROR);
    } else {
        $result->data_seek(0);
        //$row = $result->fetch_assoc();
        
        while ($row = $result->fetch_assoc()) {
            $stringa .= $row["nome"]." ha pagato ". $row["Somma_Caffe"] ." caffe".chr(10);
        }
        return $stringa;
            //return $row['nome'];
    }
}

function setPayment(User $_user, $n_caffe) {
    $connection = new Database();
    $conn = $connection->getConnection();
    
    if ($conn->connect_error) {
        trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
    }
    
    $query = "SELECT id_utente FROM utente WHERE nome = '".$_user->getNomeBenefattore()."'";
    $result = $conn->query($query);
    
    if($result === false) {
        trigger_error('Wrong SQL: ' . $query . ' Error: ' . $conn->error, E_USER_ERROR);
    } else {
        $result->data_seek(0);
        $row = $result->fetch_assoc();
        $sql = "INSERT INTO caffe_pagati(id_utente,n_caffe,settato_da,data) VALUES ('".$row['id_utente']."', '".$n_caffe."', '".$_user->getChatId()."', '".date('Y/m/d h:i:s')."')";
        $query = $conn->prepare($sql);
            if ($query) {
                $query->execute();
            } else 
                trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
            
            $query->close();
            return true;
    }
    return false;
}

function situazioneAttuale() {
    $conn = new Database();
    
    if ($conn->connect_error) {
        trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
    }
    
    $query = "SELECT u.nome, SUM(cp.n_caffe) AS Somma_Caffe FROM caffe_pagati AS cp JOIN utente AS u ON cp.id_utente = u.id_utente GROUP BY cp.id_utente ORDER BY Somma_Caffe DESC";
    $result = $conn->query($query);

$result->free();
$conn->close();
}
?>
