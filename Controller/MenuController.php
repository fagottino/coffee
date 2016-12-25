<?php
require_once './Controller/Emoticon.php';
$lang = Lang::getLang();
/**
 * Description of MenuController
 *
 * @author fagottino
 */
class MenuController {
    
    function createCustomMenu($_itemArray, $_backButton = false) {
        global $lang;

        $menu[] = array();
        $i = 0;
        $j = -1;
        while($row = $_itemArray->fetch_assoc()) {
            if ($i % 3 == 0) {
                $menu[++$j] = array("" . $row["nome"]);
            } else {
                array_push($menu[$j], "" . $row["nome"]);
            }
            $i++;
        }
        
        if (sizeof($menu) == 0)
            array_push($menu, array(Emoticon::error().$lang->error->errorWithCreationOfCustomMenu), array(Emoticon::retry().$lang->general->retry));
        
        if ($_backButton) {
            array_push($menu, array(Emoticon::back().$lang->menu->back, Emoticon::home().$lang->menu->home));
        }
                
        return $menu;
    }
    
    public function createInlineMenu($_itemArray, $_backButton = false) {
        global $lang;

        $menu[] = array();
        $menu[0] = array();
        $i = 0;
        $j = -1;
        while($row = $_itemArray->fetch_assoc()) {
            if ($i % 3 == 0) {
                $k = 0;
//                $menu[++$j][$k++] = array("text" => $row["nome"], "url" => "http://www.google.com");
                $menu[++$j][$k++] = array("text" => $row["nome"], "callback_data" => "http://www.google.it");
            } else {
//                array_push($menu[$j], array("text" => $row["nome"], "url" => "http://www.google.com"));
                array_push($menu[$j], array("text" => $row["nome"], "callback_data" => "http://www.google.it"));
            }
            $i++;
            if ($_backButton) {
                //array_push($menu, array(Emoticon::back()."Indietro", Emoticon::home()."Menù principale"));
                array_push($menu, array(Emoticon::back().$lang->back, Emoticon::home().$lang->menu->home));
            }
        }
        
        if (sizeof($menu) == 0)
            array_push($menu[$j], array(Emoticon::back().$lang->error->errorWithCreationOfCustomMenu, Emoticon::home().$lang->general->retry));
        
        return $menu;
    }
}