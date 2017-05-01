<?php
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

include './file/messages.php';

$user->getUserData($unreadMessage);
try {
    $userProfile = $userController->getInfo($user->getIdTelegram());
    if (sizeof($userProfile) > 0) {
        $user->setUserDataFromDb($userProfile);
        $lang = Lang::getLang($user->getLang());
    } else {
        $userController->register($user);
        $newUser = true;
    }
    $path = $userController->getPath();
    if ($path != "") {
        $requestFile = $path.$user->getIdTelegram().".txt";
        $currentRequest = file_get_contents($requestFile);
        $currentRequest .= json_encode($unreadMessage, JSON_PRETTY_PRINT);
        $currentRequest .= "\n";
        file_put_contents($requestFile, $currentRequest);
    }
}
catch (DatabaseException $ex) {
    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
}
catch (UserControllerException $ex) {
    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
}

if($user->getChat()->getType() != "") {
    switch ($user->getChat()->getType()) {
// PRIVATE CHAT START
        case "private":
            switch ($user->getMessage()) {
            case START:
                $messageManager->sendChatAction($user->getChat()->getId(), "typing");
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
                        .$lang->ui->giveMeTips." <a href='http://www.orlandoantonio.it'>".$lang->ui->clickingHere."</a> ".Emoticon::smile();
                }
                $menu = $menuController->defaultPrivate();
                try {
                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu);
                }
                catch (MessageException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                break;

            case Emoticon::help().$lang->menu->help:
                $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                try {
                    $user->setCurrentOperation(HELP);
                    $userController->updateCurrentOperation($user);
                    $text = (string)$lang->general->moreInfo;
                    $menu = $menuController->helpGroup();
                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu, $user->getIdMessage());
                }
                catch (DatabaseException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (UserControllerException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (MessageException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                break;

            case Emoticon::settings().$lang->menu->settings:
                $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                try {
                    $user->setCurrentOperation(SETTINGS_INLINE);
                    $userController->updateCurrentOperation($user);
                    $text = (string)$lang->menu->setLanguage;
                    $menu = $menuController->lang($user->getLang(), false);
                    $messageManager->sendInline($user->getChat()->getId(), $text, $menu);
                }
                catch (DatabaseException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (UserControllerException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (MessageException $ex) {
                    $messageManager->sendSimpleMessage($user->getIdTelegram(), $ex->getMessage(), $user->getIdMessage());
                }
                break;

            case "it":
            case "en":
                try {
                    $operation = $user->getCurrentOperation();
                    if (strpos($operation, CHANGE_LANGUAGE_GROUP) !== false) {
                        $operation = $user->getCurrentOperation();
                        $groupInfo = explode("~", $operation);
                        $groupId = $groupInfo[1];
                        $groupTitle = $groupInfo[2];
                        $groupLang = $groupController->getLang($groupId);
                        if ($user->getMessage() != $groupLang) {
                            $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                            $user->getChat()->setLang($user->getMessage());
                            $groupController->updateLang($user, $groupId);
                            $lang = Lang::getLang($user->getChat()->getLang());
                            $menu = $menuController->defaultGroup();
                            $text = (string)$lang->general->languageSet;
                            $messageManager->sendReplyMarkup($groupId, $text, $menu, 0, false, true);
                            $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $text);
                            $lang = Lang::getLang($user->getLang());
                            $inlineMenu = $menuController->lang($user->getChat()->getLang());
                            $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $inlineMenu);
                        }
                    } else if ($operation == SETTINGS_INLINE) {
                        if ($user->getMessage() != $user->getLang()) {
                            $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                            $user->setLang($user->getMessage());
                            $userController->updateLang($user);
                            $lang = Lang::getLang($user->getLang());
                            $menu = $menuController->defaultPrivate();
                            $text = (string)$lang->general->changedKeyboard." ".Emoticon::smile();
                            $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu);
                            $text = (string)$lang->general->languageSet;
                            $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                            $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $text);
                            $inlineMenu = $menuController->lang($user->getMessage());
                            //$messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $inlineMenu);
                        }
                    }
                }
                catch (DatabaseException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (UserControllerExceptions $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (MessageException $ex) {
                    //$messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                    $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                } finally {
                    $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                }
                break;

            case I_LIKE_THE_BOT:
            case Emoticon::inLove().$lang->menu->doYouLikeTheBot:
                $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                try {
                    $menu = $menuController->doYouLikeTheBot();
                    $text = (string)$lang->ui->iLikeYouToo.Emoticon::inLove().Emoticon::inLove().chr(10);
                    $text .= (string)$lang->ui->iLikeYouToo1.Emoticon::inLove().Emoticon::inLove().Emoticon::heart();
                    $messageManager->sendInline($user->getChat()->getId(), $text, $menu);
                }
                catch (DatabaseException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (UserControllerException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (MessageException $ex) {
                    $messageManager->sendSimpleMessage($user->getIdTelegram(), $ex->getMessage(), $user->getIdMessage());
                }
                break;

            case Emoticon::back().$lang->menu->back:
                $operation = $user->getCurrentOperation();
                switch ($operation) {
                    case "setOperationGroup":
                    case SETTINGS_INLINE:
                            try {
                                $myGroups = $groupController->getMyGroup($user);
                                if (sizeof($myGroups) > 0) {
                                $text = Emoticon::lists()." ".$lang->general->listsOfYourGroup.chr(10).chr(10)
                                        .$lang->general->changeGroupState." ".Emoticon::smile();
                                    $menu = $menuController->myGroups($myGroups);
                                    $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                                    $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $menu);
                                } else {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->noResultsFound, $user->getIdMessage());
                                }
                            }
                            catch (DatabaseException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                            }
                            catch (GroupControllerException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                            }
                            catch (MessageException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                            }
                            $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                        break;
                        
                    case (strpos($operation, CHANGE_LANGUAGE) !== false):
                    case (strpos($operation, CHANGE_LANGUAGE_GROUP) !== false):
                    case (strpos($operation, RESET_HOLD_HOLDINGS) !== false):
                    case (strpos($operation, RESET_OLD_COFFEE) !== false):
                        $groupInfo = explode("~", $operation);
                        $groupId = $groupInfo[1];
                        $groupTitle = $groupInfo[2];
                        try {
                            $user->setCurrentOperation(SETTING_OPERATION_GROUP);
                            $userController->updateCurrentOperation($user);
                            $text = $lang->ui->manageGroupSettings." <b>".$groupTitle."</b>";
                            $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                            $getGroupInfo = $groupController->getGroupInfo($user, $groupId);
                            $getGroupInfo[0]["title"] = $groupTitle;
                            $menu = $menuController->settingsGroup($getGroupInfo);
                            $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $menu);

                            $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                        }
                        catch (DatabaseException $ex) {
                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                        }
                        catch (MessageException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                            }
                        break;

                    case "help":
//                    case "settings":
                        try {
                            $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                            $user->setCurrentOperation(HOME);
                            $userController->updateCurrentOperation($user);
                            $menu = $menuController->defaultPrivate();
                            $text = $lang->general->youAreHere." ".$lang->menu->home;
                            $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu, $user->getIdMessage());
                        }
                        catch (DataBaseException $ex) {
                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                        }
                        catch (MessageException $ex) {
                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                        }
                    break;
                
                    default:
                        
                        break;
                }
                break;

            case Emoticon::home().$lang->menu->home:
                $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                $operation = $user->getCurrentOperation();
                switch ($operation) {
//                    case "setOperationGroup":
//                    case SETTINGS_INLINE:
//                    case (strpos($operation, CHANGE_LANGUAGE) !== false):
//                    case (strpos($operation, CHANGE_LANGUAGE_GROUP) !== false):
//                    case (strpos($operation, RESET_GROUP) !== false):
//                        break;

                    case "help":
//                    case "settings":
                        try {
                            $user->setCurrentOperation(HOME);
                            $userController->updateCurrentOperation($user);
                            $menu = $menuController->defaultPrivate();
                            $text = $lang->general->youAreHere." ".$lang->menu->home;
                            $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu);
                        }
                        catch (DataBaseException $ex) {
                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                        }
                        catch (MessageException $ex) {
                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                        }
                    break;
                
                    default:
                        try {
                            $myGroups = $groupController->getMyGroup($user);
                            if (sizeof($myGroups) > 0) {
                            $text = Emoticon::lists()." ".$lang->general->listsOfYourGroup.chr(10).chr(10)
                                    .$lang->general->changeGroupState." ".Emoticon::smile();
                                $menu = $menuController->myGroups($myGroups);
                                $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                                $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $menu);
                            } else {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->noResultsFound, $user->getIdMessage());
                            }
                        }
                        catch (DatabaseException $ex) {
                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                        }
                        catch (GroupControllerException $ex) {
                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                        }
                        catch (MessageException $ex) {
                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                        }
                        $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                        break;
                }
                break;

            case Emoticon::group().$lang->menu->yourGroups:
                $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                try {
                    $myGroups = $groupController->getMyGroup($user);
                    if (sizeof($myGroups) > 0) {
                        $text = Emoticon::lists()." ".$lang->general->listsOfYourGroup.chr(10).chr(10)
                                .$lang->general->changeGroupState." ".Emoticon::smile();
                        $menu = $menuController->myGroups($myGroups);
                        $messageManager->sendInline($user->getChat()->getId(), $text, $menu, $user->getIdMessage());
                    } else {
                        $text = (string)$lang->ui->ok." "
                                .$user->getName().", "
                                .$lang->ui->triedToSendYouAMessageIfYouChangeIdea.chr(10)
                                .$lang->ui->seeYouSoon." "
                                .Emoticon::smile();
                        $menu = $menuController->writeMe("addToGroup");
                        $messageManager->sendInline($user->getChat()->getId(), $text, $menu, $user->getIdMessage(), true);
//                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->noResultsFound, $user->getIdMessage());
                    }
                }
                catch (DatabaseException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (GroupControllerException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (MessageException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $text, true);
                break;

            case (strpos($user->getMessage(), 'myGroups') !== false):
                $groupInfo = explode("~", $user->getMessage());
                $groupId = $groupInfo[1];
                $groupTitle = $groupInfo[2];
                
                try {
                    $user->setCurrentOperation(SETTING_OPERATION_GROUP);
                    $userController->updateCurrentOperation($user);
                    $text = $lang->ui->manageGroupSettings." <b>".$groupTitle."</b>".chr(10).chr(10)
                            .Emoticon::lists()." ".$lang->general->legend.chr(10).chr(10)
                            .Emoticon::cancel()." ".$lang->general->notParticipatingToGame.chr(10)
                            .Emoticon::checkPositive()." ".$lang->general->participatingToGame.chr(10).chr(10);
                    $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                    $getGroupInfo = $groupController->getGroupInfo($user, $groupId);
                    $getGroupInfo[0]["title"] = $groupTitle;
                    $menu = $menuController->settingsGroup($getGroupInfo);
                    $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $menu);
                    
                    $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                }
                catch (DatabaseException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (MessageException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                break;

            case (strpos($user->getMessage(), 'partecipateGroup') !== false):
                $groupInfo = explode("~", $user->getMessage());
                $groupId = $groupInfo[1];
                $groupTitle = $groupInfo[2];

                try {
                    $groupController->joinTheGame($user, $groupId);
                    $getGroupInfo = $groupController->getGroupInfo($user, $groupId);
                    $menu = $menuController->settingsGroup($getGroupInfo);
                    $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $menu);
                    
                    if ($getGroupInfo[0]["partecipate"] == 0) {
                        $message = $lang->general->serviceCommunication.chr(10).($user->getUsername() != NULL ? "@".$user->getUsername() : $user->getName())
                                ." ".$lang->general->hasJustLeftTheGame.chr(10)
                                .$lang->general->excludeFromChooseBenefactor;
                        $messageManager->sendReplyMarkup($groupId, $message, "remove", 0, true, true);
                        $text = $lang->general->youChoseToLeaveGame." ".$groupTitle;
                    } else if ($getGroupInfo[0]["partecipate"] == 1) {
                        $message = $lang->general->serviceCommunication.chr(10).($user->getUsername() != NULL ? "@".$user->getUsername() : $user->getName())
                                ." ".$lang->general->hasJustJoinTheGame.chr(10)
                                .$lang->general->includedInChooseBenefactor;
                        $menu = $menuController->defaultGroup();
                        $messageManager->sendReplyMarkup($groupId, $message, $menu, 0, true, true);
                        $text = $lang->general->youChoseToPlayIn." ".$groupTitle;
                    }
                    
                    $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $text);
                }
                catch (DatabaseException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (MessageException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                break;

            case (strpos($user->getMessage(), CHANGE_LANGUAGE_GROUP) !== false):
                try {
                    $groupInfo = explode("~", $user->getMessage());
                    $groupId = $groupInfo[1];
                    $groupTitle = $groupInfo[2];
                    $user->setCurrentOperation(CHANGE_LANGUAGE_GROUP."~".$groupId."~".$groupTitle);
                    $userController->updateCurrentOperation($user);
                    $text = $lang->menu->setLanguageGroup." <b>".$groupTitle."</b>";
                    $menu = $menuController->lang($user->getLang());
                    $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                    $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $menu);
                    $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $text);
                }
                catch (DatabaseException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (UserControllerException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (MessageException $ex) {
                    $messageManager->sendSimpleMessage($user->getIdTelegram(), $ex->getMessage(), $user->getIdMessage());
                }
                break;

            case (strpos($user->getMessage(), STATS_GROUP) !== false):
                $text = $lang->error->notImplementedYet;
                $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $text, true);
                break;

            case (strpos($user->getMessage(), RESET_HOLD_HOLDINGS) !== false):
                $groupInfo = explode("~", $user->getMessage());
                $groupId = $groupInfo[1];
                $groupTitle = $groupInfo[2];
                try {
                    $user->setCurrentOperation(RESET_HOLD_HOLDINGS."~".$groupId."~".$groupTitle);
                    $userController->updateCurrentOperation($user);
                    $text = $lang->ui->resetAllParticipation." <b>".$groupTitle."</b>".chr(10);
                    $text .= $lang->ui->continue;
                    $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                    
                    $menu = $menuController->yesOrNo($groupInfo, RESET_HOLD_HOLDINGS);
                    $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $menu);
                }
                catch (DatabaseExceptionException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (UserControllerException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (MessageException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                break;

            case (strpos($user->getMessage(), RESET_OLD_COFFEE) !== false):
                $groupInfo = explode("~", $user->getMessage());
                $groupId = $groupInfo[1];
                $groupTitle = $groupInfo[2];
                try {
                    $user->setCurrentOperation(RESET_OLD_COFFEE."~".$groupId."~".$groupTitle);
                    $userController->updateCurrentOperation($user);
                    $text = $lang->ui->resetAllCoffee." <b>".$groupTitle."</b>".chr(10);
                    $text .= $lang->ui->continue;
                    $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                    
                    $menu = $menuController->yesOrNo($groupInfo, RESET_OLD_COFFEE);
                    $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $menu);
                }
                catch (DatabaseExceptionException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (UserControllerException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (MessageException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                break;
            
            case (strpos($user->getMessage(), RESET_HOLD_HOLDINGS_YES) !== false):
                $groupInfo = explode("~", $user->getMessage());
                $groupId = $groupInfo[1];
                $groupTitle = $groupInfo[2];
                try {
                    $groupController->resetParticipate($groupId);

                    $text = $lang->ui->resetParticipiantDone." <b>".$groupTitle."</b>";
                    $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                    
                    $menu = $menuController->createCustomInlineMenu(null, 3, true);
                    $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $menu);
                    
                    $text = $lang->ui->settingResetWhoWantToParticipate.Emoticon::smile();
                    $menu = $menuController->starterGroup();
                    $messageManager->sendReplyMarkup($groupId, $text, $menu, $user->getIdMessage());
                }
                catch (DatabaseExceptionException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (MessageException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                break;
            
            case (strpos($user->getMessage(), RESET_OLD_COFFEE_NO) !== false):
            case (strpos($user->getMessage(), RESET_HOLD_HOLDINGS_NO) !== false):
                $groupInfo = explode("~", $user->getMessage());
                $groupId = $groupInfo[1];
                $groupTitle = $groupInfo[2];
                try {
                    $text = $lang->ui->manageGroupSettings." <b>".$groupTitle."</b>";
                    $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                    
                    $getGroupInfo = $groupController->getGroupInfo($user, $groupId);
                    $getGroupInfo[0]["title"] = $groupTitle;
                    $menu = $menuController->settingsGroup($getGroupInfo);
                    $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $menu);
                    
                }
                catch (DatabaseExceptionException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (MessageException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                break;
            
            case (strpos($user->getMessage(), RESET_OLD_COFFEE_YES) !== false):
                $groupInfo = explode("~", $user->getMessage());
                $groupId = $groupInfo[1];
                $groupTitle = $groupInfo[2];
                try {
                    $coffeeController->destroyAllCoffee(null, $groupId);
                    $text = $lang->ui->resetCoffeeDone." <b>".$groupTitle."</b>";
                    $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                    
                    $menu = $menuController->createCustomInlineMenu(null, 3, true);
                    $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $menu);
                }
                catch (DatabaseExceptionException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (CoffeeControllerException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                catch (MessageException $ex) {
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                }
                break;

            default:
                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantUnderstandMessage, $user->getIdMessage());
                break;
            }
            break;
// PRIVATE CHAT END
// 
// GROUP CHAT START
        case "group":
        case "supergroup":
            $getLang = $groupController->getLang($user->getChat()->getId());
            if ($getLang != "") {
                $user->getChat()->setLang($getLang);
            } else {
                $user->getChat()->setLang(DEFAULT_LANGUAGE);
            }
                $lang = Lang::getLang($user->getChat()->getLang());
                if ($user->getChatMember() != NULL) {
                    if ($user->getChatMember()->getType() == "new_chat_member") {
                        if ($user->getChatMember()->getId() == $me->result->id) {
                            try {
                                $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                $group = $groupController->checkGroup($user->getChat()->getId());
                                if (sizeof($group) > 0) {
                                    $groupController->activateGroup($user->getChat());
                                } else {
                                    $groupController->insert($user->getChat());
                                }
                                $groupController->associateUser($user);
                                $text = $lang->ui->hiEverybody." @".($user->getUsername() != "" ? $user->getUsername() : $user->getName()). " ".$lang->ui->hasPower.chr(10)
                                        .$lang->ui->askHimToStart;
                                $menu = array(
                                    array(
                                        "action" => $lang->menu->start.Emoticon::rocket()
                                        )
                                    );
                                $createMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu, $user->getIdMessage(), true);
                            }
                            catch (DatabaseException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                            }
                            catch (GroupControllerException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                            }
                            catch (MessageException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                            }
                        } else {
                            try {
                                $participation = $groupController->checkUserInGroup($user, $user->getChatMember()->getId());
                                if ($participation != -1) {
                                    $groupController->setLeave($user, $user->getChatMember()->getId(), 0);
                                }
                            }
                            catch (DatabaseException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                            }
                            catch (GroupControllerException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                            }
                        }
                    } else { // left_chat_member
                        if ($user->getChatMember()->getId() == $me->result->id) {
                        // il bot è uscito dal gruppo
                                try {
                                    $userWhoIsSettingCoffee = $coffeeController->checkCoffeeInSpecificGroup($user->getChat());
                                    if (sizeof($userWhoIsSettingCoffee) > 0) {
                                        $coffeeController->destroyCoffee($user, true);
                                    }
                                    $groupController->leaveGroup($user->getChat());
                                }
                                catch (DatabaseException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (CoffeeControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (GroupControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                        } else {
                            // qualcuno è uscito dal gruppo
                            try {
                                $participation = $groupController->checkUserInGroup($user, $user->getChatMember()->getId());
                                if ($participation != -1) {
                                    $groupController->setLeave($user, $user->getChatMember()->getId(), 1);
                                }
                            }
                            catch (DatabaseException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                            }
                            catch (GroupControllerException $ex) {
                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                            }
                        }
                    }
                } else {
                        $user->setGroupOperation($userController->getGroupOperation($user));
                        switch ($user->getMessage()) {
                            case $lang->menu->start.Emoticon::rocket():
                                try {
                                    $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                    $olderMember = $groupController->getOlderMember($user);
                                    if (sizeof($olderMember) > 0) {
                                        $text = $lang->general->thereAreActiveUsers.chr(10)
                                                .$lang->general->whatDoYouWantToDoWithOlderConfiguration.chr(10).chr(10)
                                                .Emoticon::older()." ".$lang->menu->keepOlderConfiguration.$lang->general->keepOlderConfiguration.chr(10).chr(10)
                                                .Emoticon::neww()." ".$lang->menu->resetOldHoldings.$lang->general->resetOldHoldings;

                                        $menu = $menuController->keepOrResetConfigurationGroup();
                                        $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu, $user->getIdMessage(), true, true);
                                    } else {
                                        // Altrimenti chiedo ai partecipanti del gruppo di avviare una nuova sessione
                                        $groupController->setParticipate($user, 1);
                                        $text = $lang->ui->letsGo.chr(10)
                                            .$lang->ui->sayHi;
                                        $menu = $menuController->starterGroup();
                                        $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu, $user->getIdMessage(), false, true);
                                    }
                                }
                                catch (DatabaseException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (CoffeeControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (GroupControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (MessageException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                break;

                            case Emoticon::older().$lang->menu->keepOlderConfiguration:
                                try {
                                    $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                    $userList = $groupController->getOlderMember($user);

//                                    $sendIt = true;
//                                    foreach ($userList as $key => $value) {
//                                        if ($user->getIdTelegram() == $value["id_telegram"]) {
//                                            $sendIt = false;
//                                        }
//                                    }
//
//                                    if ($sendIt) {
//                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $text, false, $user->getIdMessage());
//                                        $text = "";
//                                    }
                                    
                                    if (sizeof($userList) > 0) {
                                        $text = $lang->ui->beforeStartConfirmConfiguration.chr(10).chr(10);
                                        foreach ($userList as $key => $value) {
                                            $text .= "@".$value["name"].", ";
                                            $userController->setConfiguration($user->getChat(), $value["id_telegram"], 1);
                                        }
                                        $text .= $lang->ui->pleaseAnswerMeYourIntentions;
                                    } else {
                                        $text = "Nella vecchia configurazione non risultano esserci utenti attivi.";
                                        $text .= chr(10)."Potete iniziare a partecipare ai giochi tramite i pulsanti in basso";
                                    }

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
                                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $createMenu, $user->getIdMessage(), true);
                                }
                                catch (DatabaseExceptionException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (GroupControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (UserControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (MessageException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                break;

                            case Emoticon::neww().$lang->menu->resetOlderConfiguration:
                                try {
                                    $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                    $text = (string)$lang->ui->whatDoYouWantToReset;
                                    $menu = $menuController->whatDoYouWantToReset();
                                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu, $user->getIdMessage(), true);
                                }
                                catch (DatabaseExceptionException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (CoffeeControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (MessageException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                break;

                            case Emoticon::older().$lang->menu->resetOldHoldings:
                                try {
                                    $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                    $groupController->resetParticipate($user->getChat()->getId());
                                    $text = $lang->ui->resetGroupIsOk.chr(10);
                                    $text .= $lang->ui->doYouWantResetOther.chr(10);
                                    $text .= $lang->ui->pressNextToContinue." ".Emoticon::smile();
                                    $menu = $menuController->whatDoYouWantToReset();
                                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu, $user->getIdMessage(), true);
                                }
                                catch (DatabaseExceptionException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (CoffeeControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (MessageException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                break;

                            case Emoticon::neww().$lang->menu->resetOldCoffee:
                                try {
                                    $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                    $coffeeController->destroyAllCoffee($user->getChat());
                                    $text = $lang->ui->resetGroupIsOk.chr(10);
                                    $text .= $lang->ui->doYouWantResetOther.chr(10);
                                    $text .= $lang->ui->pressNextToContinue." ".Emoticon::smile();
                                    $menu = $menuController->whatDoYouWantToReset();
                                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu, $user->getIdMessage(), true);
                                }
                                catch (DatabaseExceptionException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (CoffeeControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (MessageException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                break;

                            case Emoticon::right().$lang->menu->nextStep:
                                try {
                                    $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                    $text = $lang->ui->letsGo." ".Emoticon::smile().chr(10).chr(10)
                                        .$lang->ui->sayHi;
                                    $menu = $menuController->starterGroup();
                                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu, $user->getIdMessage());
                                }
                                catch (DatabaseExceptionException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (CoffeeControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (MessageException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                break;

                            case $lang->ui->hiFriend.Emoticon::giveMeFive():
                                if ($user->getGroupOperation() != CHOOSE_BENEFACTOR) {
                                    try {
                                        $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                        $groupController->setParticipate($user, 1);
                                        $userController->setConfiguration($user->getChat(), $user->getIdTelegram(), 0);
                                        //$text = $lang->ui->hi." ".($user->getUsername() != "" ? "@".$user->getUsername() : $user->getName());
                                        $text = (string)$lang->ui->hi;
                                        if ($user->getUsername() != "") {
                                             $text .= " @".$user->getUsername().Emoticon::victory();
                                        } else {
                                            $text .= Emoticon::victory().chr(10).chr(10).$lang->ui->pleaseAddUsername.Emoticon::smileOpenMouth().Emoticon::smileOpenMouth().Emoticon::smileTongue();
                                        }
                                        $menu = $menuController->defaultGroup();
                                        $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu, $user->getIdMessage(), true, true);
                                    }
                                    catch (DatabaseException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                    }
                                    catch (GroupControllerException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                    }
                                    catch (UserControllerException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                    }
                                    catch (MessageException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                    }
                                } else {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->pendingChooseBenefactor, $user->getIdMessage());
                                }
                                break;

                            case Emoticon::plus().$lang->menu->chooseBenefactor:
                                try {
                                    $checkPartecipate = $coffeeController->checkParticipate($user);
                                    if ($checkPartecipate > 0) {
                                        $competitors = $groupController->getCompetitors($user->getChat());
                                        if (sizeof($competitors) > 0) {
                                            $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                            $areThereOtherCoffee = $coffeeController->checkCoffeeInSpecificGroup($user->getChat());
                                            if (sizeof($areThereOtherCoffee) == 0) {
//                                                $competitors = $groupController->getCompetitors($user->getChat());
                                                if (sizeof($competitors) == 1 && $competitors[0]['id_telegram'] == $user->getIdTelegram()) {
                                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantAddCoffeeToYourself, $user->getIdMessage());
                                                } else if (sizeof($competitors) == 1) {
                                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantAddCoffeeThereIsOneUser, $user->getIdMessage());
                                                } else {
                                                    $coffeeController->newPaidCoffee($user->getChat()->getId(), $user->getIdTelegram());
                                                    $text = $lang->ui->ok." @".($user->getUsername() != "" ? $user->getUsername() : $user->getName())."!".chr(10);

                                                    $text = (string)$lang->menu->selectParticipants;
                                                    foreach ($competitors as $key => $value) {
                                                        $competitors[$key]['text'] = $value['name'];
                                                        $competitors[$key]['callback_data'] = "competitor~".$value['id_user'];
                                                        unset($competitors[$key]['name']);
                                                        unset($competitors[$key]['id_user']);
                                                    }
                                                    $menu = $menuController->createCustomInlineMenu($competitors);

                                                    array_push($menu, array(
                                                                array(
                                                                    "text" => Emoticon::sparkles().$lang->menu->selectAll, 
                                                                    "callback_data" => SELECT_ALL
                                                                    )
                                                                ),
                                                            array(
                                                                array(
                                                                    "text" => Emoticon::cancel().$lang->menu->cancel, 
                                                                    "callback_data" => CANCEL_COFFEE
                                                                    )
                                                                )
                                                            );
                                                    $user->setGroupOperation(CHOOSE_BENEFACTOR);
                                                    $userController->updateGroupOperation($user);
                                                    $messageManager->sendInline($user->getChat()->getId(), $text, $menu, $user->getIdMessage(), true);
                                                }
                                            } else {
                                                $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantAddCoffeeNow.chr(10).$lang->error->mustWaitToAddCoffee, $user->getIdMessage());
                                            }
                                        } else {
                                            $text = (string)$lang->error->notEnoughUser." ".Emoticon::smile();
                                            $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, null, $user->getIdMessage(), false, true);
                                        }
//                                        $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu, $user->getIdMessage(), true, false);
                                    } else {
                                        $text = $lang->error->cantHaveKeyboard. " ".Emoticon::smile().chr(10);
                                        $text .= $lang->ui->restartTogether." ".Emoticon::rocket().Emoticon::rocket();
                                        $menu = $menuController->starterGroup();
                                    }
                                }
                                catch (DatabaseException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (GroupControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (CoffeeControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (MessageException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                break;

                            case CANCEL_COFFEE:
                                try {
                                    $userWhoIsSettingCoffee = $coffeeController->checkCoffee($user);
                                    if (sizeof($userWhoIsSettingCoffee) > 0) {
                                        $coffeeController->destroyCoffee($user);
                                        $user->setGroupOperation(HOME);
                                        $userController->updateGroupOperation($user);
                                        $text = (string)$lang->general->operationCanceled;
                                        $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                                    } else {
                                        $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $lang->error->cantDeleteOtherCoffee, true);
                                    }
                                }
                                catch (DatabaseException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (CoffeeControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (UserControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (MessageException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage());
                                }
                                $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                                break;

                            case SELECT_ALL:
                                try {
                                    $userWhoIsSettingCoffee = $coffeeController->checkCoffee($user);
                                    if (sizeof($userWhoIsSettingCoffee) > 0) {
                                    $competitors = $groupController->getCompetitors($user->getChat());
                                    if (sizeof($competitors) > 0) {
                                        foreach ($competitors as $data) {
                                            $user->setMessage($data["id_user"]);
                                            $coffeeController->addPeopleToCoffee($user);
                                        }
                                            $text = $lang->ui->aspiringBenefactorsAreTerminated." ".Emoticon::smile().chr(10).chr(10)
                                                    .$lang->ui->tap." <b>".$lang->menu->nextStep.Emoticon::right()."</b> ".$lang->ui->toDiscoverTheBenefactor;
                                            $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);

                                            $menu = array();
                                            array_push($menu, array(
                                                            array(
                                                                "text" => Emoticon::cancel().$lang->menu->cancel, 
                                                                "callback_data" => CANCEL_COFFEE
                                                                )
                                                            )
                                                        );
                                                array_push($menu[sizeof($menu) - 1], array (
                                                                    "text" => $lang->menu->nextStep." ".Emoticon::right(), 
                                                                    "callback_data" => CHOOSE_BENEFACTOR2
                                                                    )
                                                            );

                                        $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $menu);
                                    } else {
                                        $messageManager->sendSimpleMessage($lang->error->dontFindAspiringBenefactor);
                                    }
                                    $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                                    } else {
                                        $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $lang->error->cantSelectAllOnOtherCoffee, true);
                                    }
                                }
                                catch (DatabaseExceptionException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (MessageException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                break;

                            case CHOOSE_BENEFACTOR2:
                                try {
                                    $userWhoIsSettingCoffee = $coffeeController->checkCoffee($user);
                                    if (sizeof($userWhoIsSettingCoffee) > 0) {
                                        $candidate = $coffeeController->getCandidate($user);
                                        if (sizeof($candidate) > 1) {
                                            foreach ($candidate as $data) {
                                            $countOfferCoffee[] = $coffeeController->countOfferCoffee($user, $data);
                                            $countReceivedCoffee[] = $coffeeController->countReceivedCoffee($user, $data);
                                            }

                                            $allBenefactor = array();
                                            $allId = array_column($countReceivedCoffee, 'id_telegram');

                                            foreach ($countOfferCoffee as $offerCoffee) {
                                                foreach ($countReceivedCoffee as $receivedCoffee) {
                                                    if ($offerCoffee["id_telegram"] == $receivedCoffee["id_telegram"]) {
                                                        $difference = $offerCoffee["caffe_offerti"] - $receivedCoffee["caffe_ricevuti"];
                                                        $allBenefactor[] = array("id_telegram" => $offerCoffee["id_telegram"], "name" => $offerCoffee["name"], "caffe_ricevuti" => (int)$difference);
                                                    }
                                                }
                                            }

                                            $difference = array();
                                            foreach ($allBenefactor as $key)
                                            {
                                                $difference[] = $key['caffe_ricevuti'];
                                            }
                                            array_multisort($difference, SORT_ASC, $allBenefactor);
                                            usort($allBenefactor, "caffe_ricevuti");

                                            $max = $allBenefactor[0]["caffe_ricevuti"];
                                            foreach($allBenefactor as $k => $v)
                                            {
                                                if($v['caffe_ricevuti'] <= $max) {
                                                   $found_item[] = $v;
                                                }
                                            }

                                            if (sizeof($found_item) > 1) {
                                                $end = (sizeof($found_item) - 1);
                                                $choose = $found_item[rand(0, $end)];

                                                $text = $lang->ui->amongBenefactorThereAre." ".sizeof($found_item)." ".$lang->ui->contenders;
                                                $i = 0;
                                                while(isset($found_item[$i])) {
                                                    $text .= " ".$found_item[$i]["name"];
                                                    if (isset($found_item[$i + 1])) {
                                                        if (!isset($found_item[$i + 2])) {
                                                            $text .= " ".$lang->ui->and." ";
                                                        } else {
                                                            $text .= ", ";
                                                        }
                                                    }
                                                    $i++;
                                                }
                                                $text .= chr(10).$lang->ui->betweenUsIchoose." ".chr(10)
                                                        .Emoticon::party1().Emoticon::party2().Emoticon::party1()."<b>".$choose["name"]."</b> ".Emoticon::party1().Emoticon::party1().Emoticon::party2();
                                                $benefactor = $coffeeController->setPaid($user, $choose["id_telegram"]);
                                            } else if (sizeof($found_item) == 1) {
                                                $text = $lang->ui->benefactorIs.chr(10);

                                                $text .= Emoticon::party1().Emoticon::party2().Emoticon::party1()." <b>".$found_item[0]["name"]."</b> ".Emoticon::party1().Emoticon::party1().Emoticon::party2();
                                                $benefactor = $coffeeController->setPaid($user, $found_item[0]["id_telegram"]);
                                            }
                                        } else {
                                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->errorWhileCountingParticipants, $user->getIdMessage());
                                        }

                                        $user->setGroupOperation(HOME);
                                        $userController->updateGroupOperation($user);
                                        $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                                    } else {
                                        $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $lang->error->cantDoOperationOnOtherCoffee, true);
                                    }
                                }
                                catch (CoffeeControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (DatabaseException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                                break;

                            case Emoticon::settings().$lang->menu->settings:
                                try {
                                    $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                    $user->setCurrentOperation(SETTINGS);
                                    $userController->updateCurrentOperation($user);
                                    $text = (string)$lang->menu->setLanguage;
                                    $menu = $menuController->lang($user->getLang());
                                    $messageManager->sendInline($user->getChat()->getId(), $text, $menu);
                                }
                                catch (DatabaseException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (UserControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (MessageException $ex) {
                                    $messageManager->sendSimpleMessage($user->getIdTelegram(), $ex->getMessage(), $user->getIdMessage());
                                }
                                break;

                            case Emoticon::lists().$lang->menu->listCompetitors:
                                if ($user->getGroupOperation() != CHOOSE_BENEFACTOR) {
                                    try {
                                        $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                        $user->setGroupOperation(BENEFACTOR_LIST);
                                        $userController->updateGroupOperation($user);
                                        $competitors = $groupController->getAllCompetitors($user->getChat());
                                        if (sizeof($competitors) > 0) {
                                            $text = $groupController->createText($competitors);
                                            $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, null, $user->getIdMessage(), false, true);
                                        } else {
                                            $text = (string)$lang->error->notEnoughUser." ".Emoticon::smile();
                                            $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, null, $user->getIdMessage(), false, true);
                                        }
                                    }
                                    catch (DatabaseException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                    }
                                    catch (GroupControllerException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                    }
                                    catch (MessageException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                    }
                                } else {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->pendingChooseBenefactor, $user->getIdMessage());
                                }
                                break;

                            case Emoticon::stats().$lang->menu->stats:
                                if ($user->getGroupOperation() != CHOOSE_BENEFACTOR) {
                                    try {
                                        $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                        $candidate = $groupController->getCompetitors($user->getChat());
                                        if (sizeof($candidate) > 0) {
                                            foreach ($candidate as $data) {
                                                $countOfferCoffee[] = $coffeeController->countOfferCoffee($user, $data);
                                                $countReceivedCoffee[] = $coffeeController->countReceivedCoffee($user, $data);
                                            }
                                            $text = $lang->ui->currentSituationIs.chr(10);

                                            $allIdOfferCoffee = array_column($countOfferCoffee, 'id_telegram');
                                            $allIdReceivedCoffee = array_column($countReceivedCoffee, 'id_telegram');
                                            $i = 0;
                                            foreach ($candidate as $data) {
                                                $idOffer = array_search($data["id_user"], $allIdOfferCoffee);
                                                $idReceived = array_search($data["id_user"], $allIdReceivedCoffee);
                                                $text .= $countOfferCoffee[$idOffer]["name"]." ".$lang->ui->offered." <b>".$countOfferCoffee[$idOffer]["caffe_offerti"]."</b> ".$lang->ui->andReceivedIt." <b>".$countReceivedCoffee[$idReceived]["caffe_ricevuti"]."</b>\n";
                                                $i++;
                                            }
                                            $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, null, $user->getIdMessage(), false, true);
                                        } else {
                                            $text = (string)$lang->error->notEnoughUser." ".Emoticon::smile();
                                            $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, null, $user->getIdMessage(), false, true);
                                        }
                                    }
                                    catch (DatabaseException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                    }
                                    catch (GroupControllerException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                    }
                                    catch (MessageException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                    }

                                } else {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->pendingChooseBenefactor, $user->getIdMessage());
                                }
    //                            $text = $lang->error->notImplementedYet;
    //                            $messageManager->sendSimpleMessage($user->getChat()->getId(), $text, false, $user->getIdMessage());
                                break;

                            case $lang->menu->continueParticipating.Emoticon::victory():
                                $text = $lang->ui->welcomeBack." @".($user->getUsername() != "" ? $user->getUsername() : $user->getName()).", ".$lang->ui->haveYouBackWithUs." ".Emoticon::victory();
                                $menu = $menuController->defaultGroup();
                                try {
                                    $userController->setConfiguration($user->getChat(), $user->getIdTelegram(), 0);
                                    $groupController->setParticipate($user, 1);
                                    $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu, $user->getIdMessage(), true);
                                }
                                catch (UserControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (MessageException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                break;

                            case Emoticon::off()." ".$lang->menu->exitToTheGame:
                            case Emoticon::cancel().$lang->menu->willNotParticipate:
                                try {
                                    if ($user->getGroupOperation() == CHOOSE_BENEFACTOR) {
                                            $userWhoIsSettingCoffee = $coffeeController->checkCoffee($user);
                                            if (sizeof($userWhoIsSettingCoffee) > 0) {
                                                $coffeeController->destroyCoffee($user);
                                                $user->setGroupOperation(HOME);
                                                $userController->updateGroupOperation($user);
                                            }
                                        }
                                        $userController->setConfiguration($user->getChat(), $user->getIdTelegram(), 0);
                                        $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                        $lang = Lang::getLang($user->getLang());
                                        $groupController->setParticipate($user, 0);
                                        $text = $user->getName()." ".$lang->ui->rememberYouCanRejoinGames." ".Emoticon::group()." ".$lang->ui->myGroup;
                                        $menu = $menuController->defaultPrivate();
                                        $messageManager->sendReplyMarkup($user->getIdTelegram(), $text, $menu);
                                        $lang = Lang::getLang($user->getChat()->getLang());
                                        $text = (string)$lang->ui->ok." "
                                                .$user->getName().", "
                                                .$lang->ui->sendYouAMessageIfYouChangeIdea.chr(10)
                                                .$lang->ui->seeYouSoon." "
                                                .Emoticon::smile();   
                                        $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, "remove", $user->getIdMessage(), true, false);
                                    }
                                    catch (DatabaseException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                    }
                                    catch (CoffeeControllerException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                    }
                                    catch (UserControllerException $ex) {
                                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                    }
                                    catch (MessageException $ex) {
                                        if ($ex->getMessage() == 403) {
                                            $lang = Lang::getLang($user->getChat()->getLang());
                                            $text = (string)$lang->ui->ok." "
                                                    .$user->getName().", "
                                                    .$lang->ui->triedToSendYouAMessageIfYouChangeIdea.chr(10)
                                                    .$lang->ui->seeYouSoon." "
                                                    .Emoticon::smile();
                                            $menu = $menuController->writeMe("privateChat");
                                        $messageManager->sendInline($user->getChat()->getId(), $text, $menu, $user->getIdMessage(), true);
                                        }
                                    }
                                break;

//                            case KEYBOARD:
//                                $text = $lang->ui->hereComesAgain." ".($user->getUsername() != "" ? "@".$user->getUsername() : $user->getName()).", ".$lang->ui->youAskIobey." ".Emoticon::smile().Emoticon::victory();
//                                $menu = $menuController->defaultGroup();
//                                try {
//                                    $messageManager->sendChatAction($user->getChat()->getId(), "typing");
//                                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu, $user->getIdMessage(), true, false);
//                                }
//                                catch (MessageException $ex) {
//                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
//                                }
//                                break;

                            case START."@".$me->result->username:
                            case KEYBOARD."@".$me->result->username:
                                try {
                                    $checkPartecipate = $coffeeController->checkParticipate($user);
                                    $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                    if ($checkPartecipate > 0) {
                                        $text = $lang->ui->hereComesAgain." ".($user->getUsername() != "" ? "@".$user->getUsername() : $user->getName()).", ".$lang->ui->youAskIobey." ".Emoticon::smile().Emoticon::victory();
                                        $menu = $menuController->defaultGroup();
                                    } else {
                                        $text = $lang->error->cantHaveKeyboard. " ".Emoticon::smile().chr(10);
                                        $text .= $lang->ui->restartTogether." ".Emoticon::rocket().Emoticon::rocket();
                                        $menu = $menuController->starterGroup();
                                    }
                                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $menu, $user->getIdMessage(), true, false);
                                }
                                catch (DatabaseExceptionException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (MessageException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                break;

                            case I_LIKE_THE_BOT."@".$me->result->username:
                                $messageManager->sendChatAction($user->getChat()->getId(), "typing");
                                try {
                                    $menu = $menuController->doYouLikeTheBot();
                                    $text = (string)$lang->ui->iLikeYouToo.Emoticon::inLove().Emoticon::inLove().chr(10);
                                    $text .= (string)$lang->ui->iLikeYouToo1.Emoticon::inLove().Emoticon::inLove().Emoticon::heart();
                                    $messageManager->sendInline($user->getChat()->getId(), $text, $menu);
                                }
                                catch (DatabaseException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (UserControllerException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (MessageException $ex) {
                                    $messageManager->sendSimpleMessage($user->getIdTelegram(), $ex->getMessage(), $user->getIdMessage());
                                }
                                break;

                            case (strpos($user->getMessage(), 'competitor') !== false):
                                $competitorInfo = explode("~", $user->getMessage());
                                $competitorText = $competitorInfo[0];
                                $competitorId = $competitorInfo[1];
                                $user->setMessage($competitorId);
                                try {
                                    $checkCoffee = $coffeeController->checkCoffee($user);
                                    if (sizeof($checkCoffee) > 0) {
                                        $coffeeController->addPeopleToCoffee($user);
                                        $item = $groupController->getOtherCompetitors($user);
                                        if (sizeof($item) > 0) {
                                            foreach ($item as $key => $value) {
                                                $item[$key]['text'] = $value['name'];
                                                $item[$key]['callback_data'] = "competitor~".$value['id_telegram'];
                                                unset($item[$key]['name']);
                                                unset($item[$key]['id_telegram']);
                                            }
                                            $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                                        } else {
                                            $text = $lang->ui->aspiringBenefactorsAreTerminated." ".Emoticon::smile().chr(10).chr(10)
                                                    .$lang->ui->tap." <b>".$lang->menu->nextStep.Emoticon::right()."</b> ".$lang->ui->toDiscoverTheBenefactor;

                                            $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $text);
                                        }
                                            $menu = $menuController->createCustomInlineMenu($item);

                                            array_push($menu, array(
                                                            array(
                                                                "text" => Emoticon::cancel().$lang->menu->cancel, 
                                                                "callback_data" => CANCEL_COFFEE
                                                                )
                                                            )
                                                        );
                                            $allCompetitors = $groupController->countCompetitors($user);
                                            if ($allCompetitors[0] >= 2) {
                                                array_push($menu[sizeof($menu) - 1], array (
                                                                    "text" => $lang->menu->nextStep." ".Emoticon::right(), 
                                                                    "callback_data" => CHOOSE_BENEFACTOR2
                                                                    )
                                                            );
                                            }

                                            $messageManager->editInlineMessage($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $menu);
                                    } else {
//                                        $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $ex->getMessage(), true);
                                        $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $lang->error->cantDoOperationOnOtherCoffee, true);
                                    }
                                }
                                catch (DatabaseException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                catch (GroupControllerException $ex) {
                                    $messageManager->editMessageText($user->getChat()->getId(), $user->getMessageIdCallBack($unreadMessage), $ex->getMessage());
                                }
                                catch (CoffeeControllerException $ex) {
                                    $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage), $ex->getMessage(), true);
                                }
                                catch (MessageException $ex) {
                                    $messageManager->sendSimpleMessage($user->getChat()->getId(), $ex->getMessage(), $user->getIdMessage());
                                }
                                break;

                            case NULL_VALUE:
                                    $messageManager->answerCallbackQuery($user->getCallbackQueryId($unreadMessage));
                                break;

                            default:
                                break;
                            }
                    }
            break;
// GROUP CHAT END
        default:
            $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantUnderstandTypeOfChat, $user->getIdMessage());
            break;
    }
} else {
    $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantUnderstandTypeOfChat, $user->getIdMessage());
}
