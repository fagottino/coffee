<?php
require_once './Controller/Emoticon.php';
/**
 * Description of MenuController
 *
 * @author fagottino
 */
class MenuController {
    
    function createCustomMenu($_itemArray, $_backButton = false) {

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
            array_push($menu, array(Emoticon::back()."Errore nella creazione del menù custom.", Emoticon::home()."Riprova"));
        
        if ($_backButton) {
            array_push($menu, array(Emoticon::back()."Indietro", Emoticon::home()."Menù principale"));
        }
                
        return $menu;
    }
    
    public function createInlineMenu($_itemArray, $_backButton = false) {

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
                array_push($menu, array(Emoticon::back()."Indietro", Emoticon::home()."Menù principale"));
            }
        }
        
        if (sizeof($menu) == 0)
            array_push($menu, array(Emoticon::back()."Errore nella creazione del menù custom.", Emoticon::home()."Riprova"));
        
//        $menu = [
//                    [
//                        [
//                            'text' =>  "prova",
//                            'url' => "http://www.google.com"
//                        ],
//                        [
//                            'text' =>  "prova2",
//                            'url' => "http://www.tgcom.it"
//                        ]
//                    ],
//                    [
//                        [
//                            'text' =>  "prova3",
//                            'url' => "http://www.google.com"
//                        ]
//                    ]
//                ];
        return $menu;
    }
}