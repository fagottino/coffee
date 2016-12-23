<?php
require './webservice/class-http-request.php';
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
    public function send($chatID, $text, $rm = false, $dis = false)
    {
        if (!$rm) {
            $rm = array('hide_keyboard' => true);
            $rm = json_encode($rm);
        } else {
            $rm = array('keyboard' => $rm,
            'resize_keyboard' => true
            );
            $rm = json_encode($rm);
        }

        $args = array(
        'chat_id' => $chatID,
        'text' => $text,
        'reply_markup' => $rm,
        'disable_notification' => $dis
        );

        if($text)
        {
            $r = new HttpRequest("get", API_URL."/sendmessage", $args);
            
            $rr = $r->getResponse();
            $ar = json_decode($rr, true);
            $ok = $ar["ok"]; //false
            if ($ok == 0) {
                $error = $ar["error_code"];
                if($error == 403)
                {
                    //imposta che tale utente ha disattivato il bot.
                } else if ($error == 400) {
                    $errorFile = "./file/errors.txt";
                    if (!file_exists($errorFile)) {
                        $eF = fopen($errorFile, "wr");
                        fclose($eF);
                    }
                    $errorCurrent = file_get_contents($errorFile);
                    $errorCurrent .= date("d/m/Y H:i:s / ");
                    $errorCurrent .= $ar["description"];
                    $errorCurrent .= $rm;
                    $errorCurrent .= "\n";
                    file_put_contents($errorFile, $errorCurrent);
                }
            }
        }
    }
    
    public function sendInline($chatID, $text, $rm = false, $dis = false)
    {
        if (!$rm) {
            $rm = array('hide_keyboard' => true);
            $rm = json_encode($rm);
        } else {
            $rm = array('inline_keyboard' => $rm,
            );
            $rm = json_encode($rm);
        }

        $args = array(
        'chat_id' => $chatID,
        'text' => $text,
        'reply_markup' => $rm,
        'disable_notification' => $dis
        );

        if($text)
        {
            $r = new HttpRequest("get", API_URL."/sendmessage", $args);
            
            $rr = $r->getResponse();
            $ar = json_decode($rr, true);
            $ok = $ar["ok"]; //false
            if ($ok == 0) {
                $error = $ar["error_code"];
                if($error == 403)
                {
                    //imposta che tale utente ha disattivato il bot.
                } else if ($error == 400) {
                    $errorFile = "./file/errors.txt";
                    if (!file_exists($errorFile)) {
                        $eF = fopen($errorFile, "wr");
                        fclose($eF);
                    }
                    $errorCurrent = file_get_contents($errorFile);
                    $errorCurrent .= date("d/m/Y H:i:s / ");
                    $errorCurrent .= $ar["description"];
                    $errorCurrent .= $rm;
                    $errorCurrent .= "\n";
                    file_put_contents($errorFile, $errorCurrent);
                }
            }
        }
    }
        
    function sendSimpleMessage($id, $message) {
       file_get_contents(API_URL."/sendmessage?chat_id=".$id."&text=".urlencode($message));
    }

}
