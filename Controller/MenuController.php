<?php
require_once './Controller/Emoticon.php';
$lang = Lang::getLang();
/**
 * Description of MenuController
 *
 * @author fagottino
 */
class MenuController {
    
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
        
        if ($_backButton) {
            array_push($menu, array(Emoticon::back().$lang->menu->back, Emoticon::home().$lang->menu->home));
        }
                
        return $menu;
    }
    
    public function createCustomInlineMenu($_itemArray, $_itemToRow = 3, $_backButton = false, $_donation = false) {
        global $lang;
        
        $menu[] = array();
        $menu[0] = array();
        $i = 0;
        $j = -1;
        foreach ($_itemArray as $key => $value) {
            if (isset($value["alone"]) && $value["alone"]) {
                unset($value["alone"]);
                $menu[++$j][0] = $value;
            } else {
                if ($i % $_itemToRow == 0) {
                    $menu[++$j][0] = $value;
                } else {
                    array_push($menu[$j], $value);
                }
                $i++;
            }
        }
//        foreach ($_itemArray as $key => $value) {
//            if (isset($value["alone"]) && $value["alone"]) {
//                $menu[++$j] = array($value["action"]);
//            } else {
//                if ($i % 3 == 0) {
//                    $menu[++$j] = array($value["action"]);
//                } else {
//                    array_push($menu[$j], $value["action"]);
//                }
//                $i++;
//            }
//        }
        
        if ($_backButton) {
            $menu[++$j] = array(array("text" => Emoticon::back().$lang->menu->back, "callback_data" => Emoticon::back().$lang->menu->back), array("text" => Emoticon::home().$lang->menu->home, "callback_data" => Emoticon::home().$lang->menu->home));
        }
        
        if ($_donation) {
            $menu[++$j] = array("text" => $lang->menu->offer." ".Emoticon::coffee(), "url" => "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=anto%2eorla%40gmail%2ecom&lc=IT&item_name=Il%20Benefattore%20del%20Caff%c3%a8&item_number=bdc&amount=0%2e80&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted");
        }
        
        return $menu;
    }
    
    public function defaultPrivate() {
        global $lang;
        $item = array(
                    array(
                        "action" => Emoticon::group().$lang->menu->yourGroups,
                        "alone" => true
                        ),
                    array(
                        "action" => Emoticon::help().$lang->menu->help,
                        "alone" => false
                        ),
                    array(
                        "action" => Emoticon::settings().$lang->menu->settings,
                        "alone" => false
                        ),
                    array(
                        "action" => Emoticon::quit().$lang->menu->quit,
                        "alone" => true
                        )
                    );
        $menu = $this->createCustomReplyMarkupMenu($item);
        return $menu;
    }
    
    public function lang($_actuallyLanguage) {
        global $lang;
        $item = array(
                    array(
                        "text" => Emoticon::it().$lang->general->it.($_actuallyLanguage == IT ? Emoticon::check() : ""),
                        "callback_data" => "it"
                        ),
                    array(
                        "text" => Emoticon::en().$lang->general->en.($_actuallyLanguage == EN ? Emoticon::check() : ""),
                        "callback_data" => "en"
                        )
                );
        $menu = $this->createCustomInlineMenu($item, 3, true);
        return $menu;
    }
    
    public function quit() {
        global $lang;
        $item = array(
                    array(
                        "text" => Emoticon::bye().$lang->menu->quitNow, 
                        "callback_data" => "yes"
                        ), 
                    array(
                        "text" => Emoticon::justJocking().$lang->menu->justJoking,
                        "callback_data" => "no"
                        )
                    );
        $menu = $this->createCustomInlineMenu($item, 2, false, true);
        return $menu;
    }
    
    public function myGroups($_groups) {
        global $lang;
        $item = array();
        $i = 0;
        foreach ($_groups as $key) {
                $item[$i]["text"] = $key["title"];
                $item[$i]["callback_data"] = "myGroups~".$key["id_group"]."~".$key["title"];
            $i++;
        }
        $menu = $this->createCustomInlineMenu($item, 2);
        return $menu;
    }
    
    public function defaultGroup() {
        global $lang;
        $item = array(
                    array(
                        "action" => Emoticon::plus().$lang->menu->chooseBenefactor, 
                        "alone" => true
                    ),
                    array(
                        "action" => Emoticon::lists().$lang->menu->listCompetitors
                    ),
                    array(
                        "action" => Emoticon::stats().$lang->menu->stats
                    ),
                    array(
                        "action" => Emoticon::off()." ".$lang->menu->exitToTheGame,
                        "alone" => true
                    )
            );
        $menu = $this->createCustomReplyMarkupMenu($item);
        return $menu;
    }
    
    public function starterGroup() {
        global $lang;
        $item = array(
            array(
                "action" => $lang->ui->hiFriend.Emoticon::giveMeFive(),
                "alone" => true
            ),
            array(
                "action" => Emoticon::cancel().$lang->menu->willNotParticipate
            )
        );
        $menu = $this->createCustomReplyMarkupMenu($item);
        return $menu;
    }
    
    public function helpGroup() {
        global $lang;
        $item = array(
            array(
                "action" => $lang->menu->help."1"
                ),
            array(
                "action" => $lang->menu->help."2"
                ),
            array(
                "action" => $lang->menu->help."3"
                )
            );
        $menu = $this->createCustomReplyMarkupMenu($item, true);
        return $menu;
    }
    
    public function keepOrResetConfigurationGroup() {
        global $lang;
        $item = array(
            array(
                "action" => Emoticon::older().$lang->menu->keepOlderConfiguration,
                "alone" => true
            ),
            array(
                "action" => Emoticon::neww().$lang->menu->resetOlderConfiguration
            )
        );
        $menu = $this->createCustomReplyMarkupMenu($item);
        return $menu;
    }
    
    public function settingsGroup($_group) {
        global $lang;
        
        if ($_group[0]["partecipate"] == 0) {
            $state = array(
                "text" => Emoticon::cancel().$lang->general->notParticipatingToGame1,
                "callback_data" => "partecipateGroup~".$_group[0]["id_group"]."~".$_group[0]["title"],
                "alone" => true
            );
        } else {
            $state = array(
                "text" => Emoticon::checkPositive().$lang->general->participatingToGame1,
                "callback_data" => "partecipateGroup~".$_group[0]["id_group"].$_group[0]["title"],
                "alone" => true
            );
        }
        
        if ($_group[0]["bot_owner"] == 1) {
            $item = array(
                        $state,
                        array(
                            "text" => Emoticon::globe().$lang->menu->changeLanguage,
                            "callback_data" => "changeLanguageGroup~".$_group[0]["id_group"]."~".$_group[0]["title"]."~".(string)$lang->menu->changeLanguage
                            ),
                        array(
                            "text" => Emoticon::stats().$lang->menu->stats,
                            "callback_data" => "statsGroup~".$_group[0]["id_group"]."~".$_group[0]["title"]."~".(string)$lang->menu->stats
                            ),
                        array(
                            "text" => Emoticon::neww().$lang->menu->resetOlderConfiguration,
                            "callback_data" => "resetGroup~".$_group[0]["id_group"]."~".$_group[0]["title"]."~".(string)$lang->menu->resetOlderConfiguration
                            )
                    );
        } else {
            $item = array(
                        $state,
                        array(
                            "text" => Emoticon::plus().$lang->menu->changeLanguage,
                            "callback_data" => "changeLanguageGroup~".$_group[0]["id_group"]."~".$_group[0]["title"]."~".(string)$lang->menu->changeLanguage
                            ),
                        array(
                            "text" => Emoticon::stats().$lang->menu->stats,
                            "callback_data" => "statsGroup~".$_group[0]["id_group"]."~".$_group[0]["title"]."~".(string)$lang->menu->stats
                            )
                    );
        }

        $menu = $this->createCustomInlineMenu($item, 2, true);
        return $menu;
    }
}