-<?php
require_once './Config/Config.php';
require_once './Model/Database.php';
require_once './Model/Lang.php';
require_once './Model/User.php';
require_once './Controller/UserController.php';
require_once './Controller/MenuController.php';
require_once './Controller/GroupController.php';
require_once './Controller/MessageManager.php';
require_once './Controller/CoffeeController.php';
require_once './Controller/Emoticon.php';
clearstatcache();

ini_set('error_reporting', E_ALL);
set_time_limit(0);
$userController = new UserController();
$menuController = new MenuController();
$groupController = new GroupController();
$messageManager = new MessageManager();
$coffeeController = new CoffeeController();
$user = new User();
$lang = Lang::getLang();
$newUser = false;
$me = $messageManager->getMe();

$getUnreadMessage = file_get_contents("php://input");
$unreadMessage = json_decode($getUnreadMessage, TRUE);

// Logging request
$requestFile = "./file/request.txt";
$requestCurrent = file_get_contents($requestFile);
$requestCurrent .= date("d/m/Y H:i:s / ");
$requestCurrent .= json_encode($unreadMessage, JSON_PRETTY_PRINT);
$requestCurrent .= "\n";
file_put_contents($requestFile, $requestCurrent);

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
//            "text" => Emoticon::group().$lang->menu->yourGroups,
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
//            "text" => "\/keyboard@IlBenefattoreDelCaffe_Bot",
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
//    $privateMessage = array(
//        "update_id" => 624793660,
//        "message" => array(
//            "message_id" => 4854,
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
//            "date" => 1483905746,
//            "reply_to_message" => array(
//                "message_id" => 4853,
//                "from" => array(
//                    "id" => 186132931,
//                    "first_name" => "Il benefattore del caff\u00e8",
//                    "username" => "IlBenefattoreDelCaffe_Bot"
//                ),
//                 "chat" => array(
//                    "id" => -114342037,
//                    "title" => "TestGroup",
//                    "type" => "group",
//                    "all_members_are_administrators" => true
//                ),
//                "date" => 1483905728,
//                "text" => "Ciao a tutti! @fagottino ha il potere.\nPer iniziare chiedete a lui.",
//                "entities" => array(
//                    "type" => "mention",
//                    "offset" => 14,
//                    "length" => 10
//                )
//                ),
//            "text" => $lang->menu->start.Emoticon::rocket()
//                )
//            );

// MESSAGE WITH ANSWER RICCISHOP
//    $privateMessage = array(
//        "update_id" => 624794186,
//        "message" => array(
//            "message_id" => 4443,
//            "from" => array(
//                "id" => 49402640,
//                "first_name" => "Riccishop.it",
//                "username" => "riccishop"
//            ),
//             "chat" => array(
//                "id" => -114342037,
//                "title" => "TestGroup",
//                "type" => "group",
//                "all_members_are_administrators" => true
//            ),
//            "date" => 1483905746,
//            "reply_to_message" => array(
//                "message_id" => 4438,
//                "from" => array(
//                    "id" => 186132931,
//                    "first_name" => "Il benefattore del caff\u00e8",
//                    "username" => "IlBenefattoreDelCaffe_Bot"
//                ),
//                 "chat" => array(
//                    "id" => -114342037,
//                    "title" => "TestGroup",
//                    "type" => "group",
//                    "all_members_are_administrators" => true
//                ),
//                "date" => 1483905728,
//                "text" => "Ok, iniziamo!\nChi vuole partecipare deve salutatarmi con il tasto in basso.",
//                ),
//            "text" => $lang->ui->hiFriend.Emoticon::giveMeFive()
//                )
//            );
    
//// CALLACK PRIVATE QUERY
//    $privateMessage = array(
//        "update_id" => 624792859,
//        "callback_query" => array(
//            "id" => "82376795988365271",
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
//            "data" => "myGroups~-114342037~TestGroup"
//            )
//        );
        
// CALLACK GROUP QUERY
    $privateMessage = array(
        "update_id" => 404132637,
        "callback_query" => array(
            "id" => "82376795781791020",
            "from" => array(
                "id" => 19179842,
                "first_name" => "fagottino",
                "username" => "fagottino"
            ),
            "message" => array (
                "message_id" => 5938,
                "from" => array (
                    "id" => 186132931,
                    "first_name" => "Il benefattore del caff\u00e8",
                    "username" => "IlBenefattoreDelCaffe_Bot"
                ),
                "chat" => array (
                    "id" => -114342037,
                    "title" => "TestGroup",
                    "type" => "group",
                    "all_members_are_administrators" => true
                ),
                "date" => 1484227281,
                "text" => "Ok @fagottino!\nSeleziona i nomi dei partecipanti.",
                "entities" => array(
                        "type" => "mention",
                        "offset" => 3,
                        "length" => 10
                        )
                ),
            "chat_instance" => "-3971973381224532564",
            "data" => CHOOSE_BENEFACTOR2
        )
    );
    
//    $json = '{
//    "update_id": 404132244,
//    "message": {
//        "message_id": 5349,
//        "from": {
//            "id": 19179842,
//            "first_name": "fagottino",
//            "username": "fagottino"
//        },
//        "chat": {
//            "id": -114342037,
//            "title": "TestGroup",
//            "type": "group",
//            "all_members_are_administrators": true
//        },
//        "date": 1489659593,
//        "new_chat_participant": {
//            "id": 186132931,
//            "first_name": "Il benefattore del caff\u00e8",
//            "username": "IlBenefattoreDelCaffe_Bot"
//        },
//        "new_chat_member": {
//            "id": 186132931,
//            "first_name": "Il benefattore del caff\u00e8",
//            "username": "IlBenefattoreDelCaffe_Bot"
//        },
//        "new_chat_members": [
//            {
//                "id": 186132931,
//                "first_name": "Il benefattore del caff\u00e8",
//                "username": "IlBenefattoreDelCaffe_Bot"
//            }
//        ]
//    }
//}';
//    
//    $privateMessage = json_decode($json, true);
    
    $unreadMessage = $privateMessage;
}

$user->getUserData($unreadMessage);
try {
    $userProfile = $userController->getInfo($user->getIdTelegram());
    $user->setUserDataFromDb($userProfile);
    $lang = Lang::getLang($user->getLang());
}
catch (DatabaseException $ex) {
        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
}
catch (UserControllerException $ex) {
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
                $menu = array(
                    array(
                        "action" => Emoticon::group().$lang->menu->yourGroups,
                        "alone" => true
                        ),
                    array(
                        "action" => Emoticon::help().$lang->menu->help,
                        "alone" => false
                        ),
                    array(
                        "action" => Emoticon::settings().$lang->menu->settings,
                        "alone" => false
                        ),
                    array(
                        "action" => Emoticon::quit().$lang->menu->quit,
                        "alone" => true
                        )
                    );
                $customMenu = $menuController->createCustomReplyMarkupMenu($menu);
                $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);                
                break;

            case Emoticon::help().$lang->menu->help:
                try {
                    $user->setCurrentOperation(HELP);
                    $userController->updateCurrentOperation($user);
                    $text = (string)$lang->general->moreInfo;
                    $menu = array(
                        array(
                            "action" => $lang->menu->help."1"
                            ),
                        array(
                            "action" => $lang->menu->help."2"
                            ),
                        array(
                            "action" => $lang->menu->help."3"
                            )
                        );
                    $customMenu = $menuController->createCustomReplyMarkupMenu($menu, true);
                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                } catch (DatabaseException $dbEx) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantUnderstandTypeOfChat);
                }
                break;

            case Emoticon::settings().$lang->menu->settings:
                $user->setCurrentOperation(SETTINGS);
                $userController->updateCurrentOperation($user);
                $text = "".$lang->menu->setLanguage;
                $menu = array(
                    array(
                        "text" => Emoticon::it()."Italiano".($user->getLang() == IT ? Emoticon::check() : ""),
                        "callback_data" => "it"
                        ),
                    array(
                        "text" => Emoticon::en()."English".($user->getLang() == EN ? Emoticon::check() : ""),
                        "callback_data" => "en"
                        )
                    );
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
                    $messageManager->sendSimpleMessage($user->getIdTelegram(), $ex);
                }
                break;

            case "it":
            case "en":
                try {
                    if ($user->getMessage() != $user->getLang()) {
                        $user->setLang($user->getMessage());
                        $userController->updateLang($user);
                        $lang = Lang::getLang($user->getLang());
                        $menu = array(
                            array(
                                "action" => Emoticon::group().$lang->menu->yourGroups, 
                                "alone" => true
                                ),
                            array(
                                "action" => Emoticon::help().$lang->menu->help, 
                                "alone" => false
                                ), 
                            array(
                                "action" => Emoticon::settings().$lang->menu->settings, 
                                "alone" => false
                                ), 
                            array(
                                "action" => Emoticon::quit().$lang->menu->quit, 
                                "alone" => true
                                )
                            );
                        $customMenu = $menuController->createCustomReplyMarkupMenu($menu);
                        $text = (string)$lang->general->languageSet;
                        $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                        $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $text, true);

                        $menu = array(
                            array(
                                array(
                                    "text" => Emoticon::it().$lang->general->it.($user->getMessage() == IT ? Emoticon::check() : ""),
                                    "callback_data" => "it"
                                    ),
                                array(
                                    "text" => Emoticon::en().$lang->general->en.($user->getMessage() == EN ? Emoticon::check() : ""),
                                    "callback_data" => "en"
                                    )
                                )
                            );
                        $customMenu = $menuController->createCustomInlineMenu($menu);
                        $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $menu);
                    } else {
                        $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                    }
                } catch (DatabaseException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                }
                break;

            case Emoticon::quit().$lang->menu->quit:
                $user->setCurrentOperation(QUIT);
                $userController->updateCurrentOperation($user);
                $item = array(
                    array(
                        "text" => (string)$lang->menu->quitNow, 
                        "callback_data" => "yes"
                        ), 
                    array(
                        "text" => (string)$lang->menu->justJoking,
                        "callback_data" => "no"
                        )
                    );
                $customMenu = $menuController->createCustomInlineMenu($item, false, 2, true);
                $text = (string)$lang->general->disableBot;
                $messageManager->sendInline($user->getChat()->getId(), $text, $customMenu);
                break;

            case "yes":
            case "no":
                try {
                    $text = (string)$lang->error->notImplementedYet;
                    $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $text, true);

                    $menu = array(
                        array(
                            array(
                                "text" => Emoticon::it().$lang->general->it.($user->getMessage() == IT ? Emoticon::check() : ""),
                                "callback_data" => "it"
                                ),
                            array("text" => Emoticon::en().$lang->general->en.($user->getMessage() == EN ? Emoticon::check() : ""),
                                "callback_data" => "en"
                                )
                            )
                        );
                    $customMenu = $menuController->createCustomInlineMenu($menu);
//                        $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageId($unreadMessage), $text, $menu);
                } catch (DatabaseException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                }
                break;

            case Emoticon::back().$lang->menu->back:
                $operation = $user->getCurrentOperation();
                switch ($operation) {
                    case "help":
                    case "settings":
                        try {
                            $user->setCurrentOperation(HOME);
                            $userController->updateCurrentOperation($user);
                            $menu = array(
                                array(
                                    "action" => Emoticon::group().$lang->menu->yourGroups,
                                    "alone" => true
                                    ),
                                array(
                                    "action" => Emoticon::help().$lang->menu->help, 
                                    "alone" => false
                                    ), 
                                array(
                                    "action" => Emoticon::settings().$lang->menu->settings,
                                    "alone" => false
                                    ), 
                                array(
                                    "action" => Emoticon::quit().$lang->menu->quit,
                                    "alone" => true
                                    )
                                );
                            $customMenu = $menuController->createCustomReplyMarkupMenu($menu);
                            $text = $lang->general->youAreHere." ".$lang->menu->home;
                            $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                        } catch (DataBaseException $dbEx) {
                            $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                        }
                    break;

                default:
                    break;
                }
                break;

            case Emoticon::home().$lang->menu->home:
                try {
                    $user->setCurrentOperation(HOME);
                    $userController->updateCurrentOperation($user);
                    $menu = array(
                        array(
                            "action" => Emoticon::group().$lang->menu->yourGroups, 
                            "alone" => true
                            ), 
                        array(
                            "action" => Emoticon::help().$lang->menu->help,
                            "alone" => false
                            ), 
                        array(
                            "action" => Emoticon::settings().$lang->menu->settings, 
                            "alone" => false
                            ),
                        array(
                            "action" => Emoticon::quit().$lang->menu->quit,
                            "alone" => true
                            )
                        );
                    $customMenu = $menuController->createCustomReplyMarkupMenu($menu);
                    $text = "Sei al ".$lang->menu->home;
                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                } catch (DataBaseException $dbEx) {
                    $text = "QUA1 ".$dbEx->getMessage();
                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                }
                break;

            case Emoticon::group().$lang->menu->yourGroups:
                try {
                    $myGroup = $groupController->getMyGroup($user);
                    $menu = array();
                    $i = 0;
                    foreach ($myGroup as $key) {
                        if ($key["partecipate"] == 0) {
                                //$menu[$i] = array("text" => $key["title"].Emoticon::checkNegative(), "callback_data" => $key["id_group"]);
                                $menu[$i]["text"] = $key["title"].Emoticon::cancel();
//                                    array_push($menu, array("text" => $key["title"] . Emoticon::cancel(), "callback_data" => $key["id_group"]));
                            } else {
                            //$menu[$i] = array("text" => $key["title"].Emoticon::checkPositive(), "callback_data" => $key["id_group"]);
                            $menu[$i]["text"] = $key["title"].Emoticon::checkPositive();
//                                    array_push($menu, array("text" => $key["title"] . Emoticon::checkPositive(), "callback_data" => "myGroups~".$key["id_group"]));
                            }

                            $menu[$i]["callback_data"] = "myGroups~".$key["id_group"]."~".$key["title"];
                        $i++;
                    }
                    $text = $lang->general->listsOfYourGroup.chr(10).chr(10)
                            .Emoticon::lists()." ".$lang->general->legend.chr(10).chr(10)
                            .Emoticon::cancel()." ".$lang->general->notParticipatingToGame.chr(10)
                            .Emoticon::checkPositive()." ".$lang->general->participatingToGame.chr(10).chr(10)
                            .$lang->general->changeGroupState." ".Emoticon::smile();
                $customMenu = $menuController->createCustomInlineMenu($menu, false, 2);
                $messageManager->sendInline($user->getChat()->getId(), $text, $customMenu, $user->getIdMessage());
                //$messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu, false, $user->getIdMessage());
                }
                catch (DatabaseException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                }
                catch (GroupControllerException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                }
                $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $text, true);
                break;

            case (strpos($user->getMessage(), 'myGroups') !== false):
                $groupInfo = explode("~", $user->getMessage());
                $groupId = $groupInfo[1];
                $groupTitle = $groupInfo[2];

                try {
                    $groupController->joinTheGame($user, $groupId);
                    $myGroup = $groupController->getMyGroup($user);
                    $menu = array();
                    $i = 0;
                    foreach ($myGroup as $key) {
                        if ($key["partecipate"] == 0) {
                                $menu[$i]["text"] = $key["title"].Emoticon::cancel();
                            } else {
                                $menu[$i]["text"] = $key["title"].Emoticon::checkPositive();
                            }
                            $menu[$i]["callback_data"] = "myGroups~".$key["id_group"]."~".$key["title"];
                            if ($key["id_group"] == $groupId) {
                                $onOff = $key["partecipate"];
                            }
                        $i++;
                    }

                    $customMenu = $menuController->createCustomInlineMenu($menu, false, 2);
                    $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $customMenu);

//                    if ($onOff == 0) {
//                        $message = $lang->general->serviceCommunication.chr(10).$user->getName()." ".$lang->general->hasJustLeftTheGame.chr(10)
//                                .$lang->general->excludeFromChooseBenefactor;
//                        $text = $lang->general->youChoseToLeaveGame.$groupTitle;
//                    } else {
//                        $message = "Comunicazione di servizio:".chr(10)."@".$user->getName()." si è appena unito ai giochi!".chr(10).chr(10)
//                                ."Usa il comando /keyboard per ottenere di nuovo la tastiera personalizzata ".Emoticon::smile();
//                        $text = "Hai scelto di partecipare ai giochi nel gruppo ".$groupTitle;
//                    }
                    
                    if ($onOff == 0) {
                        $message = $lang->general->serviceCommunication.chr(10)."@".($user->getChat()->getUsername() != NULL ? $user->getChat()->getUsername() : $user->getName())
                                ." ".$lang->general->hasJustLeftTheGame.chr(10)
                                .$lang->general->excludeFromChooseBenefactor;
                        $messageManager->sendSimpleMessage($groupId, $message, true, 0, $user->getIdTelegram());
                        $text = $lang->general->youChoseToLeaveGame." ".$groupTitle;
                    } else if ($onOff == 1) {
                        $message = $lang->general->serviceCommunication.chr(10)."@".($user->getChat()->getUsername() != NULL ? $user->getChat()->getUsername() : $user->getName())
                                ." ".$lang->general->hasJustJoinTheGame.chr(10)
                                .$lang->general->includedInChooseBenefactor;
                        $text = $lang->general->youChoseToPlayIn." ".$groupTitle;
                        $menu = array(
                                    array(
                                        "action" => Emoticon::plus().$lang->menu->chooseBenefactor, 
                                        "alone" => true
                                    ),
                                    array(
                                        "action" => Emoticon::lists().$lang->menu->listCompetitors
                                    ),
                                    array(
                                        "action" => Emoticon::stats().$lang->menu->stats
                                    ),
                                    array(
                                        "action" => Emoticon::off()." ".$lang->menu->exitToTheGame,
                                        "alone" => true
                                    )
                            );
                        $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
                        $messageManager->sendReplyMarkup($groupId, $message, $createMenu, true, false, true, false);
                    }
                    
                    $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $text, true);
                }
                catch (DatabaseException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                }
                break;

            default:
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantUnderstandMessage);
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
                            $group = $groupController->checkGroup($user->getChat()->getId());
                            $groupController->activateGroup($user->getChat());
                        }
                        catch (GroupControllerException $ex) {
                            try {
                                $groupController->insert($user->getChat());
                                $groupController->associateUser($user);
                            }
//                            catch (DatabaseException $ex) {
//                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
//                            }
                            catch (GroupControllerException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                            }
                        }
                        catch (DatabaseException $ex) {
                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                        }
                        /*try {
                            $groupController->associateUser($user);
                        }
                        catch (DatabaseException $ex) {
                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                        }
                        catch (GroupControllerException $ex) {
                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                        }*/
                        $text = $lang->ui->hiEverybody." @".($user->getUsername() != "" ? $user->getUsername() : $user->getName()). " ".$lang->ui->hasPower.chr(10)
                                .$lang->ui->askHimToStart;
                        $menu = array(
                            array(
                                "action" => $lang->menu->start.Emoticon::rocket()
                                )
                            );
                        $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
                        $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu, true, $user->getIdMessage());
                    } else  {
                        // E' stato aggiunto un utente al gruppo
                    }
                } else { // left_chat_member
                    if ($user->getChatMember()->getId() == $me->result->id) { // il bot è uscito dal gruppo
                        $groupController->leaveGroup($user->getChat());
                    } else {
                        // qualcuno è uscito dal gruppo
                    }
                }
            } else {
                try {
                    $user->setGroupOperation($userController->getGroupOperation($user));
                    switch ($user->getMessage()) {
                        case $lang->menu->start.Emoticon::rocket():
                            try {
                                // Se sono già stato in questo gruppo e ci sono vecchi settaggi
                                // Propongo quelli
                                $coffeeController->checkCoffeeInSpecificGroup($user->getChat());
                                $olderMember = $groupController->getOlderMember($user);
                                if ($olderMember > 0) {
                                    
                                } else {
                                    
                                }
                                $text = $lang->general->thereAreActiveUsers.chr(10)
                                        .$lang->general->whatDoYouWantToDoWithOlderConfiguration.chr(10).chr(10)
                                        .Emoticon::older()." ".$lang->menu->keepOlderConfiguration.$lang->general->keepOlderConfiguration.chr(10).chr(10)
                                        .Emoticon::neww()." ".$lang->menu->resetOlderConfiguration.$lang->general->resetOlderConfiguration;
                                
                                $menu = array(
                                    array(
                                        "action" => Emoticon::older().$lang->menu->keepOlderConfiguration,
                                        "alone" => true
                                    ),
                                    array(
                                        "action" => Emoticon::neww().$lang->menu->resetOlderConfiguration
                                    )
                                );
                                $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu, true, $user->getIdMessage());
                            }
                            catch (CoffeeControllerException $ex) {
                                // Altrimenti chiedo ai partecipanti del gruppo di avviare una nuova sessione
                                try {
                                    $groupController->setParticipate($user, 1);
                                    $text = $lang->ui->letsGo.chr(10)
                                        .$lang->ui->sayHi
                                    ;
                                    $menu = array(
                                        array(
                                            "action" => $lang->ui->hiFriend.Emoticon::giveMeFive(),
                                            "alone" => true
                                        ),
                                        array(
                                            "action" => Emoticon::cancel().$lang->menu->willNotParticipate
                                        )
                                    );
                                    $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu, false, $user->getIdMessage());
                                }
                                catch (DatabaseException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                }
                                catch (GroupControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                }
                            }
                            catch (GroupControllerException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                            }
                            break;

                        case $lang->ui->hiFriend.Emoticon::giveMeFive():
                            try {
//                                $groupController->setActive($user, 1);
                                $groupController->setParticipate($user, 1);
                                $text = $lang->ui->hi." @".($user->getUsername() != "" ? $user->getUsername() : $user->getName()).Emoticon::victory();
                                $menu = array(
                                    array(
                                        "action" => Emoticon::plus().$lang->menu->chooseBenefactor, 
                                        "alone" => true
                                    ),
                                    array(
                                        "action" => Emoticon::lists().$lang->menu->listCompetitors
                                    ),
                                    array(
                                        "action" => Emoticon::stats().$lang->menu->stats
                                    ),
                                    array(
                                        "action" => Emoticon::off()." ".$lang->menu->exitToTheGame,
                                        "alone" => true
                                    )
                                );
                                $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu, true, $user->getIdMessage());
                            }
                            catch (DatabaseException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                            }
                            break;

                        case Emoticon::plus().$lang->menu->chooseBenefactor:
                                if ($user->getGroupOperation() != CHOOSE_BENEFACTOR) {
                                    try {
                                        $competitors = $groupController->getCompetitors($user->getChat(), $me);
                                        if (sizeof($competitors) == 1 && $competitors[0]['id_telegram'] == $user->getIdTelegram()) {
                                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantAddCoffeeToYourself);
                                        } else {
                                            $coffeeController->newPaidCoffee($user->getChat()->getId(), $user->getIdTelegram());
                                            $text = $lang->ui->ok." @".($user->getUsername() != "" ? $user->getUsername() : $user->getName())."!".chr(10);
                                            $menu = array(
                                                array(
                                                    "action" => Emoticon::lists().$lang->menu->listCompetitors
                                                ),
                                                array(
                                                    "action" => Emoticon::off()." ".$lang->menu->exitToTheGame,
                                                    "alone" => true
                                                )
                                            );
                                            $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
//                                            $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu, false, $user->getIdMessage());

                                            $text = "".$lang->menu->selectParticipants;
                                            foreach ($competitors as $key => $value) {
                                                $competitors[$key]['text'] = $value['name'];
                                                $competitors[$key]['callback_data'] = $value['id_telegram'];
                                                unset($competitors[$key]['name']);
                                                unset($competitors[$key]['id_telegram']);
                                            }
                                            $menu = $menuController->createCustomInlineMenu($competitors);

                                            array_push($menu, array(
                                                        array(
                                                            "text" => Emoticon::cancel().$lang->menu->cancel, 
                                                            "callback_data" => CANCEL_COFFEE
                                                            )
                                                        )
                                                    );
                                            $user->setGroupOperation(CHOOSE_BENEFACTOR);
                                            $userController->updateGroupOperation($user);
                                            $messageManager->sendInline($user->getChat()->getId(), $text, $menu, $user->getIdMessage());
                                        }
                                    }
                                    catch (DatabaseException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                    }
                                    catch (GroupControllerException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                    }
                                    catch (CoffeeControllerException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                    }
                                catch (DatabaseException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                }
                            }
                            break;

                        case Emoticon::lists().$lang->menu->listCompetitors:
                            if ($user->getGroupOperation() != CHOOSE_BENEFACTOR) {
                                try {
                                    $user->setGroupOperation(BENEFACTOR_LIST);
                                    $userController->updateGroupOperation($user);
                                    $competitors = $groupController->getCompetitors($user->getChat(), $me);
                                    $text = $groupController->createText($competitors);
                                    $menu = array(
                                        array(
                                            "action" => Emoticon::plus().$lang->menu->chooseBenefactor, 
                                            "alone" => true
                                        ), 
                                        array(
                                            "action" => Emoticon::lists().$lang->menu->listCompetitors
                                        ),
                                        array(
                                            "action" => Emoticon::stats().$lang->menu->stats
                                        ),
                                        array(
                                            "action" => Emoticon::off()." ".$lang->menu->exitToTheGame,
                                            "alone" => true
                                        )
                                    );
                                    $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu, false, $user->getIdMessage(), false);
                                }
                                catch (DatabaseException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                }
                                catch (GroupControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                }
                            } else {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), "Hai un'operazione di aggiunta caffè in sospeso. Annullala per continuare a navigare con il bot.");
                            }
                            break;
                        
                        case Emoticon::stats().$lang->menu->stats:
                            $text = $lang->error->notImplementedYet;
                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $text, false, $user->getIdMessage());
                            break;

                        case CHOOSE_BENEFACTOR2:
                            try {
                                $countOfferCoffee = $coffeeController->countOfferCoffee($user);
                                $countReceivedCoffee = $coffeeController->countReceivedCoffee($user);
                                
                                
//                                SELECT coffee_user.name, coffee_paid_coffee.id_paid_coffee, COUNT(coffee_paid_coffee_people.id_paid_coffee) AS caffe_ricevuti, SUM(coffee_user.name) FROM coffee_paid_coffee
//                                JOIN coffee_paid_coffee_people ON coffee_paid_coffee.id_paid_coffee = coffee_paid_coffee_people.id_paid_coffee
//                                JOIN coffee_user ON coffee_paid_coffee_people.id_user = coffee_user.id_telegram
//                                WHERE coffee_paid_coffee.id_group = '-114342037'
//                                AND coffee_paid_coffee.powered_by IS NOT NULL
//                                GROUP BY coffee_user.name, coffee_paid_coffee.id_paid_coffee
//                                ORDER BY caffe_ricevuti DESC
//                                
//                                $benefactor = $coffeeController->setPaid($user, 49402640);

                                $user->setGroupOperation(HOME);
                                $userController->updateGroupOperation($user);
                                $text = "It's ok! Il benefattore del momento è ----";
                                $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                            }
                            catch (CoffeeControllerException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                            }
                            catch (DatabaseException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                            }
                            $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                            break;

                        case CANCEL_COFFEE:
                            try {
                                $coffeeController->destroyCoffee($user);
                                $user->setGroupOperation(HOME);
                                $userController->updateGroupOperation($user);
                                $text = (string)$lang->general->operationCanceled;
                                $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                            }
                            catch (CoffeeControllerException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                            }
                            catch (DatabaseException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                            }
                            $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                            break;

                        case Emoticon::off()." ".$lang->menu->exitToTheGame:
                        case Emoticon::cancel().$lang->menu->willNotParticipate:
                            if ($user->getGroupOperation() != CHOOSE_BENEFACTOR) {
                                try {
                                    $groupController->setParticipate($user, 0);
                                    $text = $user->getName()." ricorda che puoi sempre scegliere di partecipare al gioco abilitando il gruppo tramite il tasto in basso ".Emoticon::group()."<b>I miei gruppi</b>";
                                    $menu = array(array("action" => Emoticon::group().$lang->menu->yourGroups, "alone" => true), array("action" => Emoticon::help().$lang->menu->help, "alone" => false), array("action" => Emoticon::settings().$lang->menu->settings, "alone" => false), array("action" => Emoticon::quit().$lang->menu->quit, "alone" => true));
                                    $customMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                    $messageManager->sendReplyMarkup($user->getIdTelegram(), $text, $customMenu);

                                    $text = $lang->ui->ok." ".$user->getName().", ti ho inviato un messaggio privato con un piccolo promemoria qualora cambiassi idea.".chr(10)."A presto! ".Emoticon::smile();
                                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, false, true, $user->getIdMessage());
                                }
                                catch (DatabaseException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                }
                            } else {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), "Hai un'operazione di aggiunta caffè in sospeso. Annullala per continuare a navigare con il bot.");
                            }
                            break;
                        
                        case KEYBOARD:
                        case KEYBOARD."@".$me->result->username:
                                $text = $lang->ui->hereComesAgain." @".($user->getUsername() != "" ? $user->getUsername() : $user->getName()).", ".$lang->ui->haveYouBackWithUs." ".Emoticon::victory();
                                $menu = array(
                                    array(
                                        "action" => Emoticon::plus().$lang->menu->chooseBenefactor, 
                                        "alone" => true
                                    ),
                                    array(
                                        "action" => Emoticon::lists().$lang->menu->listCompetitors
                                    ),
                                    array(
                                        "action" => Emoticon::stats().$lang->menu->stats
                                    ),
                                    array(
                                        "action" => Emoticon::off()." ".$lang->menu->exitToTheGame,
                                        "alone" => true
                                    )
                                );
                                $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu, true, $user->getIdMessage());
                            break;
                        
                        case Emoticon::older().$lang->menu->keepOlderConfiguration:
                            try {
                                $userList = $groupController->getOlderMember($user);
                                $text = "Ok, prima di procedere ho bisogno della conferma dei singoli giocatori.".chr(10).chr(10);
                                //if (!in_array($user->getIdTelegram(), $userList)) {
                                
                                $sendIt = true;
                                foreach ($userList as $key => $value) {
                                    if ($user->getIdTelegram() == $value["id_telegram"]) {
                                        $sendIt = false;
                                    }
                                }
                                
                                if ($sendIt) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $text, false, $user->getIdMessage());
                                    $text = "";
                                }
                                
                                foreach ($userList as $key => $value) {
                                    $text .= "@".$value["name"].", ";
                                }
                                $text .= "per favore rispondetemi con le vostre intenzioni";
                                
                                $menu = array(
                                    array(
                                        "action" => $lang->menu->continueParticipating.Emoticon::victory(),
                                        "alone" => true
                                    ),
                                    array(
                                        "action" => Emoticon::cancel().$lang->menu->willNotParticipate
                                    )
                                );
                                $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu, true, $user->getIdMessage());
                            }
                            catch (CoffeeControllerException $ex) {
                                // Altrimenti chiedo ai partecipanti del gruppo di avviare una nuova sessione
                                try {
                                    $groupController->setParticipate($user, 1);
                                    $text = $lang->ui->letsGo.chr(10)
                                        .$lang->ui->sayHi
                                    ;
                                    $menu = array(
                                        array(
                                            "action" => $lang->ui->hiFriend.Emoticon::giveMeFive(),
                                            "alone" => true
                                        ),
                                        array(
                                            "action" => Emoticon::cancel().$lang->menu->willNotParticipate
                                        )
                                    );
                                    $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu, false, $user->getIdMessage());
                                }
                                catch (DatabaseException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                }
                                catch (GroupControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                }
                            }
                            catch (GroupControllerException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                            }
                            break;
                        
                        case Emoticon::neww().$lang->menu->resetOlderConfiguration:
                            try {
                                $groupController->resetParticipate($user->getChat());
                                
                                $text = $lang->ui->resetGroupIsOk." "
                                    .$lang->ui->letsGo.chr(10). " ".Emoticon::smile()
                                    .$lang->ui->sayHi
                                ;
                                $menu = array(
                                    array(
                                        "action" => $lang->ui->hiFriend.Emoticon::giveMeFive(),
                                        "alone" => true
                                    ),
                                    array(
                                        "action" => Emoticon::cancel().$lang->menu->willNotParticipate
                                    )
                                );
                                $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu, false, $user->getIdMessage());
                            } catch (DatabaseExceptionException $ex) {
                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                            }
                            break;
                        
                        case $lang->menu->continueParticipating.Emoticon::victory():
                            $text = $lang->ui->welcomeBack." @".($user->getUsername() != "" ? $user->getUsername() : $user->getName()).", ".$lang->ui->haveYouBackWithUs." ".Emoticon::victory();
                            $menu = array(
                                array(
                                    "action" => Emoticon::plus().$lang->menu->chooseBenefactor, 
                                    "alone" => true
                                ),
                                array(
                                    "action" => Emoticon::lists().$lang->menu->listCompetitors
                                ),
                                array(
                                    "action" => Emoticon::stats().$lang->menu->stats
                                ),
                                array(
                                    "action" => Emoticon::off()." ".$lang->menu->exitToTheGame,
                                    "alone" => true
                                )
                            );
                            $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
                            $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu, true, $user->getIdMessage());
                            break;

                        case NULL_VALUE:
                                $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                            break;

                        default:
                            switch ($user->getGroupOperation()) {
                                case CHOOSE_BENEFACTOR:
                                        try {
                                            $coffeeController->checkCoffee($user);
                                            $coffeeController->addPeopleToCoffee($user);
                                            $item = $groupController->getOtherCompetitors($user);
                                            foreach ($item as $key => $value) {
                                                $item[$key]['text'] = $value['name'];
                                                $item[$key]['callback_data'] = $value['id_telegram'];
                                                unset($item[$key]['name']);
                                                unset($item[$key]['id_telegram']);
                                            }
                                        }
                                        catch (DatabaseException $ex) {
                                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                        }
                                        catch (GroupControllerException $ex) {
                                            $text = $lang->ui->aspiringBenefactorsAreTerminated." ".Emoticon::smile().chr(10).chr(10)
                                                    .$lang->ui->tap." <b>".$lang->menu->nextStep.Emoticon::right()."</b> ".$lang->ui->toDiscoverTheBenefactor;
                                            $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                                        }
                                        catch (CoffeeControllerException $ex) {
                                            $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $ex->getMessage(), true);
                                            //$messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                        }
                                        finally {
                                            $menu = $menuController->createCustomInlineMenu($item);

                                            array_push($menu, array(
                                                            array(
                                                                "text" => Emoticon::cancel().$lang->menu->cancel, 
                                                                "callback_data" => CANCEL_COFFEE
                                                                )
                                                            )
                                                        );
                                            $allCompetitors = $groupController->getAllCompetitors($user);
                                            if ($allCompetitors[0] >= 2) {
                                                array_push($menu[sizeof($menu) - 1], array (
                                                                    "text" => $lang->menu->nextStep." ".Emoticon::right(), 
                                                                    "callback_data" => CHOOSE_BENEFACTOR2
                                                                    )
                                                            );
                                            }

                                            $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $menu);
                                        }
                                        $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                                    break;

                                case NULL_VALUE:
                                        $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                                    break;
                                
                            default:
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantGetGroupOperation, false, $user->getIdMessage());
                                break;
                            }
                            break;
                        }
                    }
                    catch (UserControllerException $ex) {
                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                    }
            }
            break;
// GROUP CHAT END
        default:
            $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantUnderstandTypeOfChat);
            break;
    }
} else {
    $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantUnderstandTypeOfChat);
}
