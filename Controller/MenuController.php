<?php
require_once './Controller/Emoticon.php';
$lang = Lang::getLang();
/**
 * Description of MenuController
 *
 * @author fagottino
 */
class MenuController {
    
    function createReplyMarkupMenu($_itemArray, $_backButton = false) {
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
        
        array_push($menu, array(Emoticon::coffee().$lang->menu->offer));
                
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
                $menu[++$j][$k++] = array("text" => $row["nome"], "callback_data" => "http://www.google.it");
            } else {
                array_push($menu[$j], array("text" => $row["nome"], "callback_data" => "http://www.google.it"));
            }
            $i++;
        }
        
        if (sizeof($menu) == 0)
            array_push($menu[$j], array(Emoticon::back().$lang->error->errorWithCreationOfCustomMenu, Emoticon::home().$lang->general->retry));
        
        if ($_backButton) {
            array_push($menu, array(Emoticon::back().$lang->back, Emoticon::home().$lang->menu->home));
        }
        
        array_push($menu, array(Emoticon::coffee().$lang->menu->offer));
        
        
        return $menu;
    }
    
    public function createCustomReplyMarkupMenu($_itemArray, $_backButton = false) {
        global $lang;

        $menu[] = array();
        $i = 0;
        $j = -1;
        foreach ($_itemArray as $key => $value) {
            if ($i % 3 == 0) {
                $menu[++$j] = array("" . $value["action"]);
            } else {
                array_push($menu[$j], "" . $value["action"]);
            }
            $i++;
        }
        
        if (sizeof($menu) == 0)
            array_push($menu, array(Emoticon::error().$lang->error->errorWithCreationOfCustomMenu), array(Emoticon::retry().$lang->general->retry));
        
        if ($_backButton) {
            array_push($menu, array(Emoticon::back().$lang->menu->back, Emoticon::home().$lang->menu->home));
        }
        
        //array_push($menu, array(Emoticon::coffee().$lang->menu->offer.Emoticon::smile()));
                
        return $menu;
    }
    
    public function createCustomInlineMenu($_itemArray, $_backButton = false, $_itemToRow = 3) {
        global $lang;
        

        $menu[] = array();
        $menu[0] = array();
        $i = 0;
        $j = -1;
        foreach ($_itemArray as $key => $value) {
            if ($i % $_itemToRow == 0) {
                $k = 0;
                $menu[++$j][$k++] = array("text" => $value["nome"], "callback_data" => "http://www.google.it");
            } else {
                array_push($menu[$j], array("text" => $value["nome"], "callback_data" => "http://www.google.it"));
            }
            $i++;
        }
        
        if (sizeof($menu) == 0)
            array_push($menu[$j], array(Emoticon::back().$lang->error->errorWithCreationOfCustomMenu, Emoticon::home().$lang->general->retry));
        
        if ($_backButton) {
            array_push($menu, array(Emoticon::back().$lang->back, Emoticon::home().$lang->menu->home));
        }
        
        //array_push($menu[$j], array("text" => Emoticon::coffee().$lang->menu->offer, "url" => "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=anto%2eorla%40gmail%2ecom&lc=IT&item_name=Il%20Benefattore%20del%20Caff%c3%a8&item_number=bdc&amount=0%2e80&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted"));
        
        $menu[++$j][0] = array("text" => $lang->menu->offer." ".Emoticon::coffee(), "url" => "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=anto%2eorla%40gmail%2ecom&lc=IT&item_name=Il%20Benefattore%20del%20Caff%c3%a8&item_number=bdc&amount=0%2e80&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted");
        
        return $menu;
    }

        public function addBackButton($_menu) {
        
    }
}