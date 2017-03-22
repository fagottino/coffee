<?php
require './webservice/class-http-request.php';
$lang = Lang::getLang();
/**
 * Description of MessageManager
 *
 * @author fagottino
 */
class MessageManager {
//    public function __construct() {
//        
//    }
        
    //primo parametro: $chatID
    //secondo: testo messaggio
    //terzo: array di array della tastiera da mostrare all'utente
    //quarto: true->disabilita notifica per questo messaggio
    public function sendReplyMarkup($_idChat, $_text, $_replyMarkup, $_selectiveKeyboard = false, $_replyToMessage = false, $_disableNotificationMessage = false)
    {
        $args = array(
            'chat_id' => $_idChat,
            'text' => $_text,
            'parse_mode' => "HTML",
            'disable_notification' => $_disableNotificationMessage,
            'reply_to_message_id' => $_replyToMessage,
        );
        
        if (is_array($_replyMarkup)) {
            $keyboard = array('keyboard' => $_replyMarkup,
                'resize_keyboard' => true
            );
        } else if ($_replyMarkup == false) {
            $keyboard = array('remove_keyboard' => true);
        } else {
            
        }

        if (!is_array($keyboard) && $_selectiveKeyboard)
            $keyboard = array('selective' => true);
        else if ($_selectiveKeyboard)
            $keyboard['selective'] = true;
        
        $_replyMarkup = json_encode($keyboard);
        $args['reply_markup'] = $_replyMarkup;
        
//        if (!$_replyMarkup) {
//            $rmGen = array('hide_keyboard' => true);
//            $_replyMarkup = json_encode($rmGen);
//        } else {
//            $rmGen = array('keyboard' => $_replyMarkup,
//            'resize_keyboard' => true
//            );
//        
//            if ($_selectiveKeyboard) {
//                $rmGen['selective'] = true;
//            }
//            $_replyMarkup = json_encode($rmGen);
//            $args['reply_markup'] = $_replyMarkup;
//        }
                
        $this->sendMessage("sendMessage", $args);
    }
    
    public function sendInline($_idChat, $text, $_keyboard, $_replyTo)
    {
        global $lang;
        if ($text) {
            if ($_keyboard) {
                $_keyboardGen = array('inline_keyboard' => $_keyboard);
                $_keyboard = json_encode($_keyboardGen);

            $args = array(
                'chat_id' => $_idChat,
                'text' => $text,
                'reply_markup' => $_keyboard,
                "parse_mode" => "HTML",
                "reply_to_message_id" => $_replyTo
            );

            $this->sendMessage("sendMessage", $args);
            } else {
                throw new MessageException($lang->error->inlineKeyboardIsRequired);
            }
        } else {
            throw new MessageException($lang->error->textIsRequired);
        }
    }
    
//    public function editInlineMessage($_idChat, $_messageId, $_text, $_keyboard) {
//        global $lang;
//        if ($_text) {
//            if ($_keyboard) {
//                $_keyboardGen = array('inline_keyboard' => $_keyboard);
//                $_keyboard = json_encode($_keyboardGen);
//
//                $args = array(
//                    'chat_id' => $_idChat,
//                    'message_id' => $_messageId,
//                    'text' => $_text,
//                    'reply_markup' => $_keyboard
//                );
// 
//                $this->sendMessage("editMessageReplyMarkup", $args);
//            } else {
//                throw new MessageException($lang->error->inlineKeyboardIsRequired);
//            }
//        } else {
//            throw new MessageException($lang->error->textIsRequired);
//        }
//    }
    
    public function editInlineMessage($_idChat, $_messageId, $_keyboard) {
        global $lang;
        $_keyboardGen = array('inline_keyboard' => $_keyboard);
        $_keyboards = json_encode($_keyboardGen);

        $args = array(
            'chat_id' => $_idChat,
            'message_id' => $_messageId,
            'reply_markup' => $_keyboards
        );

        $this->sendMessage("editMessageReplyMarkup", $args);
    }
    
    public function editMessageText($_idChat, $_messageId, $_text) {
        global $lang;
        
        $args = array(
            'chat_id' => $_idChat,
            'message_id' => $_messageId,
            'text' => $_text
        );

        $this->sendMessage("editMessageText", $args);
    }
    
    public function replyMessage($_idChat, $_idMessage) {
        
    }
    
    public function getNumberMemberGroup($_idChat, $_idUser)
    {
        $args = array(
        'chat_id' => $_idChat,
        'text' => $text,
        'reply_markup' => $rm,
        'disable_notification' => $dis
        );
        
        $this->sendMessage("getChatMembersCount", $args);
    }

    private function sendMessage($_action, $_args) {
        $request = new HttpRequest("get", API_URL."/".$_action, $_args);

        $response = $request->getResponse();
        $data = json_decode($response, true);
        $ok = $data["ok"]; //false
        if ($ok == 0) {
            $error = $data["error_code"];
            switch ($error) {
                case 403:
                //imposta che tale utente ha disattivato il bot.                        
                break;
                case 400:
                default:
                    $errorFile = "./file/errors.txt";
                    if (!file_exists($errorFile)) {
                        $eF = fopen($errorFile, "wr");
                        fclose($eF);
                    }
                    $errorCurrent = file_get_contents($errorFile);
                    $errorCurrent .= date("d/m/Y H:i:s / ");
                    $errorCurrent .= $data["description"];
                    $errorCurrent .= "\n";
                    file_put_contents($errorFile, $errorCurrent);
                break;
            }
        }
    }
        
    function sendSimpleMessage($id, $message, $disableNotification = false, $_replyToMsg = 0) {
       file_get_contents(API_URL."/sendmessage?chat_id=".$id."&text=".urlencode($message).($disableNotification ? "&disable_notification=true" : "").($_replyToMsg != 0 ? "&reply_to_message_id=".$_replyToMsg : ""));
       //file_get_contents(API_URL."/sendmessage?chat_id=".$id."&text=".urlencode($message)."&disable_notification=true");
    }
        
    function answerCallbackQuery($_id, $_message = "", $_showAlert = false) {
       file_get_contents(API_URL."/answerCallbackQuery?callback_query_id=".$_id."&text=".urlencode($_message)."&show_alert=".$_showAlert);
    }
        
    function getMe() {
       $me = json_decode(file_get_contents(API_URL."/getme"));
       return $me;
    }
}

class MessageException extends Exception { }
