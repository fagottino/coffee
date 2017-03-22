<?php
require_once './Controller/Emoticon.php';
$lang = Lang::getLang();
/**
 * Description of MenuController
 *
 * @author fagottino
 */
class MenuController {
    
    /*public function createInlineMenu($_itemArray, $_backButton = false) {
        global $lang;

        $menu[] = array();
        $menu[0] = array();
        $i = 0;
        $j = -1;
        while($row = $_itemArray->fetch_assoc()) {
            if ($i % 3 == 0) {
                $k = 0;
                $menu[++$j][$k++] = array("text" => $row["name"], "callback_data" => "http://www.google.it");
            } else {
                array_push($menu[$j], array("text" => $row["name"], "callback_data" => "http://www.google.it"));
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
    }*/
    
    function createReplyMarkupMenu($_itemArray, $_backButton = false) {
        global $lang;

        $menu[] = array();
        $i = 0;
        $j = -1;
        while($row = $_itemArray->fetch_assoc()) {
            if ($i % 3 == 0) {
                $menu[++$j] = array("" . $row["name"]);
            } else {
                array_push($menu[$j], "" . $row["name"]);
            }
            $i++;
        }
        
        if (sizeOf($menu) === 0) {
            array_push($menu, array(Emoticon::cancel().$lang->error->errorWithCreationOfCustomMenu), array(Emoticon::retry().$lang->general->retry));
        }
        
        if ($_backButton) {
            array_push($menu, array(Emoticon::back().$lang->menu->back, Emoticon::home().$lang->menu->home));
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
            if (isset($value["alone"]) && $value["alone"]) {
                $menu[++$j] = array($value["action"]);
            } else {
                if ($i % 3 == 0) {
                    $menu[++$j] = array($value["action"]);
                } else {
                    array_push($menu[$j], $value["action"]);
                }
                $i++;
            }
        }
        
        if (sizeOf($menu) === 0)
            array_push($menu, array(Emoticon::cancel().$lang->error->errorWithCreationOfCustomMenu), array(Emoticon::retry().$lang->general->retry));
        
        if ($_backButton) {
            array_push($menu, array(Emoticon::back().$lang->menu->back, Emoticon::home().$lang->menu->home));
        }
                
        return $menu;
    }
    
    public function createCustomInlineMenu($_itemArray, $_backButton = false, $_itemToRow = 3, $_offer = false) {
        global $lang;
        
        $menu[] = array();
        $menu[0] = array();
        $i = 0;
        $j = -1;
        foreach ($_itemArray as $key => $value) {
            if ($i % $_itemToRow == 0) {
                $menu[++$j][0] = $value;
            } else {
                array_push($menu[$j], $value);
            }
            $i++;
        }
        
        if (sizeOf($menu) === 0 && !$_backButton && !$_offer) {
            array_push($menu[$j], array(Emoticon::cancel().$lang->error->errorWithCreationOfCustomMenu, Emoticon::home().$lang->general->retry));
        }
        
        if ($_backButton) {
            array_push($menu, array(Emoticon::back().$lang->back, Emoticon::home().$lang->menu->home));
        }
        
        if ($_offer)
            $menu[++$j][0] = array("text" => $lang->menu->offer." ".Emoticon::coffee(), "url" => "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=anto%2eorla%40gmail%2ecom&lc=IT&item_name=Il%20Benefattore%20del%20Caff%c3%a8&item_number=bdc&amount=0%2e80&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted");
        
        return $menu;
    }
    
//    public function createCustomInlineMenu($_itemArray, $_backButton = false, $_itemToRow = 3, $_offer = false) {
//        global $lang;
//        
//        $menu[] = array();
//        $menu[0] = array();
//        $i = 0;
//        $j = -1;
//        foreach ($_itemArray as $key => $value) {
//            if ($i % $_itemToRow == 0) {
//                $menu[++$j][$key] = $value;
//            } else {
//                //array_push($menu[$j][$key], $value);
//                $menu[$j][$key] = $value;
//            }
//            $i++;
//        }
//        
//        if (sizeOf($menu) === 0 && !$_backButton && !$_offer) {
//            array_push($menu[$j], array(Emoticon::cancel().$lang->error->errorWithCreationOfCustomMenu, Emoticon::home().$lang->general->retry));
//        }
//        
//        if ($_backButton) {
//            array_push($menu, array(Emoticon::back().$lang->back, Emoticon::home().$lang->menu->home));
//        }
//        
//        if ($_offer) {
//            $menu[++$j][0] = array("text" => $lang->menu->offer . " " . Emoticon::coffee(), "url" => "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=anto%2eorla%40gmail%2ecom&lc=IT&item_name=Il%20Benefattore%20del%20Caff%c3%a8&item_number=bdc&amount=0%2e80&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted");
//        }
//
//        return $menu;
//    }

    public function addBackButton($_menu) {
        
    }
}