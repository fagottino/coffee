<?php
require './webservice/class-http-request.php';
$lang = Lang::getLang();
/**
 * Description of MessageManager
 *
 * @author fagottino
 */
class MessageManager {
    
    public $answerFromHttpRequest;
    
//    public function __construct() {
//        
//    }
        
    //primo parametro: $chatID
    //secondo: testo messaggio
    //terzo: array di array della tastiera da mostrare all'utente
    //quarto: true->disabilita notifica per questo messaggio
    public function sendReplyMarkup($_idChat, $_text, $_replyMarkup = null, $_replyToMessage = false, $_selectiveKeyboard = false, $_disableNotificationMessage = false, $_oneTimeKeyboard = false) {
        $args = array(
            'chat_id' => $_idChat,
            'text' => $_text,
            'parse_mode' => "HTML"
        );
        
        if (is_array($_replyMarkup)) {
            $keyboard = array(
                'keyboard' => $_replyMarkup
            );
            
            $keyboard['resize_keyboard'] = true;
            
            if ($_oneTimeKeyboard) {
                $keyboard['one_time_keyboard'] = true;
            }
            
            if ($_selectiveKeyboard) {
                $keyboard['selective'] = true;
            }
        
            $_replyMarkup = json_encode($keyboard);
            $args['reply_markup'] = $_replyMarkup;
        } else if ($_replyMarkup == "remove") {
            $keyboard = array('remove_keyboard' => true);
            
            if ($_selectiveKeyboard) {
                $keyboard['selective'] = true;
            }
        
            $_replyMarkup = json_encode($keyboard);
            $args['reply_markup'] = $_replyMarkup;
        }
        
        if ($_replyToMessage) {
            $args['reply_to_message_id'] = $_replyToMessage;
        }

        if ($_disableNotificationMessage) {
            $args['disable_notification'] = $_disableNotificationMessage;
        }
        
        try {
            $this->sendMessage("sendMessage", $args);
        } catch (MessageException $ex) {
            throw new MessageException($ex->getMessage());
        }
    }
    
    public function sendChatAction($_idChat, $_action)
    {
        $args = array(
            'chat_id' => $_idChat,
            'action' => $_action
        );
                        
        $this->sendMessage("sendChatAction", $args);
    }
    
    public function sendInline($_idChat, $text, $_keyboard, $_replyTo, $_resize = false)
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
                    'resize_keyboard' => $_resize,
                    "parse_mode" => "HTML",
                    "reply_to_message_id" => $_replyTo
                );

                try {
                    $this->sendMessage("sendMessage", $args);
                }
                catch (MessageException $ex) {
                    throw new MessageException($ex->getMessage());
                }
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
            'text' => $_text,
            "parse_mode" => "HTML"
        );

        try {
            $this->sendMessage("editMessageText", $args);
        }
        catch (MessageException $ex) {
            throw new MessageException($ex->getMessage());
        }
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
            if ($error == 403 ) {
                //l' utente ha disattivato il bot
                throw new MessageException($data["error_code"]);
            } else if ($error == 400) {
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
                throw new MessageException((string)$lang->error->sendingMessage);
            } else {
                
            }
        } else {
            $this->answerFromHttpRequest = $data["result"];
        }
    }
        
    function sendSimpleMessage($id, $message, $_replyToMsg = 0, $disableNotification = false, $_selective = false) {
        $url = "/sendmessage?chat_id=".$id."&text=".urlencode($message).($disableNotification ? "&disable_notification=true" : "").($_replyToMsg != 0 ? "&reply_to_message_id=".$_replyToMsg : "").($_selective != 0 ? "&selective=true&parse_mode=HTML" : "");
       file_get_contents(API_URL.$url);
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
