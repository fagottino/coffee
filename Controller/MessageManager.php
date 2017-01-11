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
    public function sendReplyMarkup($chatID, $text, $rm = false, $selective = false, $dis = false)
    {
        if (!$rm) {
            $rmGen = array('hide_keyboard' => true);
            $rm = json_encode($rmGen);
        } else {
            $rmGen = array('keyboard' => $rm,
            'resize_keyboard' => true
            );
        
            if ($selective) {
                $rmGen['selective'] = true;
            }
            $rm = json_encode($rmGen);
        }

        $args = array(
            'chat_id' => $chatID,
            'text' => $text,
            'reply_markup' => $rm,
            'parse_mode' => "HTML",
            'disable_notification' => $dis
        );
        
        $this->sendMessage("sendMessage", $args);
    }
    
    public function sendInline($chatID, $text, $_keyboard)
    {
        global $lang;
        if ($text) {
            if ($_keyboard) {
                $_keyboardGen = array('inline_keyboard' => $_keyboard);
                $_keyboard = json_encode($_keyboardGen);

            $args = array(
                'chat_id' => $chatID,
                'text' => $text,
                'reply_markup' => $_keyboard
            );

            $this->sendMessage("sendMessage", $args);
            } else {
                throw new MessageException($lang->error->inlineKeyboardIsRequired);
            }
        } else {
            throw new MessageException($lang->error->textIsRequired);
        }
    }
    
    public function editInlineMessage($_chatId, $_messageId, $_text, $_keyboard) {
        global $lang;
        if ($_text) {
            if ($_keyboard) {
                    $_keyboardGen = array('inline_keyboard' => $_keyboard);
                    $_keyboard = json_encode($_keyboardGen);

                $args = array(
                    'chat_id' => $_chatId,
                    'message_id' => $_messageId,
                    'text' => $_text,
                    'reply_markup' => $_keyboard
                );

                //throw new MessageException($lang->error->inlineKeyboardIsRequired);
                //https://api.telegram.org/bot186132931:AAHLkdfVXtdWX53wsySA219hABBeUUirgko/editMessageReplyMarkup?chat_id=19179842&message_id=2781&reply_markup={"inline_keyboard":[[{"text":"Italiano","callback_data":"it"},{"text":"English","callback_data":"en"}]]}
                //https://api.telegram.org/bot186132931:AAHLkdfVXtdWX53wsySA219hABBeUUirgko/editMessageReplyMarkup?chat_id=19179842&message_id=2781&reply_markup={"inline_keyboard":[{"text":"ItalianoAAAAAAAAA","callback_data":"it"},{"text":"English","callback_data":"en"}]}
                
            $this->sendMessage("editMessageReplyMarkup", $args);
            } else {
                throw new MessageException($lang->error->inlineKeyboardIsRequired);
            }
        } else {
            throw new MessageException($lang->error->textIsRequired);
        }
    }
    
    public function getNumberMemberGroup($_chatID, $_userId)
    {
        $args = array(
        'chat_id' => $chatID,
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
        
    function sendSimpleMessage($id, $message, $disableNotification = false) {
       file_get_contents(API_URL."/sendmessage?chat_id=".$id."&text=".urlencode($message).($disableNotification ? "&disable_notification=true" : ""));
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
