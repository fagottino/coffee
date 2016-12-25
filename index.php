<?php
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

$getUnreadMessage = file_get_contents("php://input");
$unreadMessage = json_decode($getUnreadMessage, TRUE);

$whitelist = array('127.0.0.1', "::1");

if(in_array(filter_input(INPUT_SERVER,'REMOTE_ADDR'), $whitelist)){
    $privateMessage = [
        "update_id" => 624792369,
        "message" => [
            "message_id" => 1270,
            "from" => [
                "id" => 19179842,
                "first_name" => "fagottino",
                "username" => "fagottino"
            ],
             "chat" => [
                "id" => 19179842,
                 "first_name" => "fagottino",
                 "username" => "fagottino",
                 "type" => "private"
            ],
            "date" => 1482502325,
            "text" => "/hapagato",
            "entities" => [
                "type" => "bot_command",
                "offset" => 0,
                "length" => 9
            ]
        ]
    ];
    $unreadMessage = $privateMessage;
}

$user->getUserData($unreadMessage);

try {
    $userProfile = $userController->getInfo($user->getIdTelegram());
    $user->setUserDataFromDb($userProfile);
} catch (DatabaseException $ex) {
    // se non lo trovo lo registro
    try {
        $userController->register($user);
    } catch (UserControllerException $e) {
        sendMessage($user->getChat()->getId(), $e->getMessage());
    }
}



switch ($user->getChat()->getType()) {

    case "private":
        $text = "beccato!";
        $allName = $userController->getAllUserName();
        $customMenu = $menuController->createCustomMenu($allName, true);
        $messageManager->sendReplyMarkup($user->getChat()->getId(), $text, $customMenu);
//        $customMenus = $menuController->createInlineMenu($allName);
//        $textq = "Chi ha pagato i caffè?";
//        $messageManager->sendInline($user->getChat()->getId(), $textq, $customMenus);
    break;
    case "group":
        $text = "anche qui!";
        $messageManager->sendSimpleMessage($user->getChat()->getId(), $text);
    break;
    default:
    break;
    
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
$requestCurrent .= $getUnreadMessage;
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

function registerOperation($_user) {
    $sql = "UPDATE utente SET operation = ".$_user->getCurrentOperation()." WHERE id_utente = ".$_user->getId();
    $query = $conn->prepare($sql);
        if ($query) {
            $query->execute();
        } else 
            trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);

        $query->close();
        return true;
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
