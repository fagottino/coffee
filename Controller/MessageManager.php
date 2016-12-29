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
    public function sendReplyMarkup($chatID, $text, $rm = false, $dis = false)
    {
        if (!$rm) {
            $rmGen = array('hide_keyboard' => true);
            $rm = json_encode($rmGen);
        } else {
            $rmGen = array('keyboard' => $rm,
            'resize_keyboard' => true
            );
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

                $this->sendMessage("editMessageText", $args);
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
                    $errorCurrent .= $_args["reply_markup"];
                    $errorCurrent .= "\n";
                    file_put_contents($errorFile, $errorCurrent);
                break;
            }
        }
    }
        
    function sendSimpleMessage($id, $message) {
       file_get_contents(API_URL."/sendmessage?chat_id=".$id."&text=".urlencode($message));
    }
}

class MessageException extends Exception { }
