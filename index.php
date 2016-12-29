<?php
require_once './Config/Config.php';
require_once './Model/Database.php';
require_once './Model/Lang.php';
require_once './Model/User.php';
require_once './Controller/UserController.php';
require_once './Controller/MenuController.php';
require_once './Controller/MessageManager.php';
clearstatcache();

ini_set('error_reporting', E_ALL);
set_time_limit(0);
$userController = new UserController();
$menuController = new MenuController();
$messageManager = new MessageManager();
$user = new User();
$lang = Lang::getLang();
$newUser = false;

$getUnreadMessage = file_get_contents("php://input");
$unreadMessage = json_decode($getUnreadMessage, TRUE);

$whitelist = array('127.0.0.1', "::1");
if(in_array(filter_input(INPUT_SERVER,'REMOTE_ADDR'), $whitelist)){
    $privateMessage = array(
        "update_id" => 624792369,
        "message" => array(
            "message_id" => 1270,
            "from" => array(
                "id" => 19179842,
                "first_name" => "fagottino",
                "username" => "fagottino"
            ),
             "chat" => array(
                "id" => 19179842,
                 "first_name" => "fagottino",
                 "username" => "fagottino",
                 "type" => "private"
            ),
            "date" => 1482502325,
            "text" => Emoticon::quit().$lang->menu->quit,
            "entities" => array(
                "type" => "bot_command",
                "offset" => 0,
                "length" => 9
            )
        )
    );
    $unreadMessage = $privateMessage;
}

// PROBLEMA SULLE QUERY

$user->getUserData($unreadMessage);
if ($user->getIdTelegram() != null) {
    try {
        $userProfile = $userController->getInfo($user->getIdTelegram());
        $user->setUserDataFromDb($userProfile);
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
                        $text = '
                            Ciao <strong>'.$user->getName().'</strong>, benvenuto!'.chr(10)."".chr(10)
                            .'Ogni giorno, insieme, possiamo decidere chi è il benefattore che si offre di pagare i caffè!'.chr(10)
                            .'Qui in basso trovi dei pulsanti con cui puoi darmi gli ordini.'.chr(10)."".chr(10)
                            .'Sei pronto per iniziare? Ok, cominciamo! '.Emoticon::smile().chr(10)
                            .'Buon divertimento!'.chr(10)."".chr(10)
                            .'PS: tutte le informazioni utili sul bot e sullo sviluppatore le trovi <a href="http://www.orlandoantonio.it/">cliccando qui</a>'
                        ;
                    } else {
                        $text = '
                            <strong>'.$user->getName().'</strong>, hai già avviato il bot'.Emoticon::smile().chr(10)."".chr(10)
                            .'Ti invio alcuni pulsanti se vuoi che ti aiuti su qualcosa di specifico.'.chr(10).chr(10)
                            .'I suggerimenti sono benvenuti, qualora ne avessi inviameli pure qui <a href="http://www.orlandoantonio.it/">cliccando qui</a>.'.Emoticon::smile()
                        ;
                    }
                    $menu = array(array("action" => Emoticon::help().$lang->menu->help), array("action" => Emoticon::settings().$lang->menu->settings), array("action" => Emoticon::quit().$lang->menu->quit));
                    $customMenu = $menuController->createCustomReplyMarkupMenu($menu);
                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);                
                    break;
                //case Emoticon::help().$lang->menu->help:
                case Emoticon::help().HELP:
                    try {
                        $user->setCurrentOperation("help");
                        $userController->updateCurrentOperation($user);
                        $text = 'Usa i pulsanti in basso per scoprire le loro funzioni';
                        $menu = array(array("action" => "Pulsante aiuto 1"), array("action" => "Pulsante aiuto 2"), array("action" => "Pulsante aiuto 3"), array("action" => "Pulsante aiuto 4"));
                        $customMenu = $menuController->createCustomReplyMarkupMenu($menu, true);
                        $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                    } catch (DatabaseException $dbEx) {
                        $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantUnderstandTypeOfChat);
                    }
                    break;
                case Emoticon::settings().$lang->menu->settings:
                    $user->setCurrentOperation("settings");
                    $userController->updateCurrentOperation($user);
                    //$menu = array(array("nome" => "Settings 1"), array("nome" => "Settings 2"), array("nome" => "Settings 3"), array("nome" => "Settings 4"));
                    $menu = array(array("action" => "Settings 1"), array("action" => "Settings 2"), array("action" => "Settings 3"), array("action" => "Settings 4"));
//                    $customMenu = $menuController->createCustomInlineMenu($menu);
                    $text = "Impostazioni del bot:";
//                    $messageManager->sendInline($user->getChat()->getId(), $text, $customMenu);
                    $customMenu = $menuController->createCustomReplyMarkupMenu($menu, true);
                    $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                    break;
                case Emoticon::quit().$lang->menu->quit:
                    $user->setCurrentOperation("quit");
                    $userController->updateCurrentOperation($user);
                    $item = array(array("nome" => "Si, esci subito."), array("nome" => "No, ho sbagliato."));
                    $customMenu = $menuController->createCustomInlineMenu($item, false, 2);
                    $text = "Sei sicuro di voler disabilitare il bot?";
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
                                $menu = array(array("action" => Emoticon::help().$lang->menu->help), array("action" => Emoticon::settings().$lang->menu->settings), array("action" => Emoticon::quit().$lang->menu->quit));
                                $customMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                $text = "Sei al ".$lang->menu->home;
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
                                $menu = array(array("action" => Emoticon::help().$lang->menu->help), array("action" => Emoticon::settings().$lang->menu->settings), array("action" => Emoticon::quit().$lang->menu->quit));
                                $customMenu = $menuController->createCustomReplyMarkupMenu($menu);
                                $text = "Sei al ".$lang->menu->home;
                                $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                            } catch (DataBaseException $dbEx) {
                                $text = "QUA1 ".$dbEx->getMessage();
                                $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
                            }
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
                $text = "anche qui!";
                $messageManager->sendSimpleMessage($user->getChat()->getId(), $text);
                break;
    // GROUP CHAT END
            default:
                break;
        }
    } else {
        $messageManager->sendSimpleMessage($user->getChat()->getId(), $lang->error->cantUnderstandTypeOfChat);
    }







    //switch ($user->getMessage()) {
    //    case "/ilbenefattore":
    //    case "/ilbenefattore@IlBenefattoreDelCaffe_Bot":
    //        $benefattore = chiPaga();
    //        $text = $benefattore;
    //        $messageManager->send($user->getChatId(), $text, false, true);
    //    break;
    //    case "/hapagato":
    //        if ($user->getOperation() == "setNumeroCaffe") {
    //            $text = "Ho capito. Dammi il nome di chi sta pagando!";
    //            $messageManager->sendSimpleMessage($user->getChatId(), $text);
    //        } else {
    //            $user->setOperation("setNumeroCaffe");
    //            $userController->updateCurrentOperation($user);
    //            $allName = $userController->getAllUserName();
    ////            try {
    ////                $customMenu = $menuController->createInlineMenu($allName);
    ////                $text = "Chi ha pagato i caffè?";
    ////                $messageManager->sendInline($user->getChatId(), $text, $customMenu);
    ////            } catch (MessageException $ex) {
    //                $customMenu = $menuController->createCustomMenu($allName, true);
    //                $text = "Chi ha pagato i caffè?";
    //                $messageManager->send($user->getChatId(), $text, $customMenu);
    ////            }
    //        }
    //    break;
    //    case "listaUtenti":
    //        $messageManager->send("-114342037", "19179842d");
    //    break;
    //
    //    default:
    //        $currentOperation = $user->getOperation();
    //
    //        switch ($currentOperation) {
    //            case "setNumeroCaffe":
    //                $user->setNomeBenefattore($user->getMessage());
    //                $user->setOperation("setNumeroCaffe_step1");
    //                try {
    //                    $userController->updateCurrentOperation($user);
    //                } catch (DatabaseException $ex) {
    //                    // errore
    //                }
    //                $text = "Quanti caffè ha pagato ".$user->getMessage()."?";
    //                $messageManager->send($user->getChatId(), $text, false, true);
    //            break;
    //            case "setNumeroCaffe_step1":
    //                $user->setOperation("");
    //                try {
    //                    $userController->updateCurrentOperation($user);
    //                } catch (DatabaseException $ex) {
    //                    // errore
    //                }
    //                setPayment($user, $user->getMessage());
    //                $text = "Ho regisrato il pagamento di ".$user->getMessage() ." caffè.";
    //                $messageManager->send($user->getChatId(), $text);
    //            break;
    //            default:
    //                $text = "Cheddici? Usa i comandi di defaultAVvzv.";
    //                $messageManager->send($user->getChatId(), $text);
    //                    if (!file_exists($errorFile)) {
    //                        $eF = fopen($errorFile, "wr");
    //                        fclose($eF);
    //                    }
    //            break;
    //    }
    //}

    $requestFile = "./file/request.txt";
    $requestCurrent = file_get_contents($requestFile);
    $requestCurrent .= date("d/m/Y H:i:s / ");
    $requestCurrent .= json_encode($unreadMessage);
    $requestCurrent .= "\n";
    file_put_contents($requestFile, $requestCurrent);
}

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

//function registerOperation(User $_user) {
//    $connection = new Database();
//    $conn = $connection->getConnection();
//    //$sql = "UPDATE utente SET operation = ".$_user->getCurrentOperation()." WHERE id_utente = ".$_user->getId();
//    $sql = "UPDATE utente SET operation = (SELECT id_operation FROM operation WHERE name_operation = ".$_user->getCurrentOperation().") WHERE id_telegram = ".$_user->getId();
//    $query = $conn->prepare($sql);
//        if ($query) {
//            $query->execute();
//        } else 
//            trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
//
//        $query->close();
//        return true;
//}

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
