<?php
$whitelist = array('127.0.0.1', "::1");
if(in_array(filter_input(INPUT_SERVER,'REMOTE_ADDR'), $whitelist)){
// PRIVATE
//    $privateMessage = array(
//        "update_id" => 624792369,
//        "message" => array(
//            "message_id" => 1270,
//            "from" => array(
//                "id" => 19179842,
//                "first_name" => "fagottino",
//                "username" => "fagottino"
//            ),
//             "chat" => array(
//                "id" => 19179842,
//                 "first_name" => "fagottino",
//                 "username" => "fagottino",
//                 "type" => "private"
//            ),
//            "date" => 1482502325,
//            "text" => Emoticon::back().$lang->menu->back,
//            "entities" => array(
//                "type" => "bot_command",
//                "offset" => 0,
//                "length" => 9
//            )
//        )
//    );
    
// GROUP
//    $privateMessage = array(
//        "update_id" => 627311072,
//        "message" => array(
//            "message_id" => 8720,
//            "from" => array(
//                "id" => 19179842,
//                "first_name" => "fagottino",
//                "username" => "fagottino"
//            ),
//             "chat" => array(
//                "id" => -114342037,
//                "title" => "TestGroup",
//                "type" => "group",
//                "all_members_are_administrators" => true
//            ),
//            "date" => 1483134810,
//            "text" => Emoticon::lists().$lang->menu->listCompetitors,
//            "entities" => array(
//                "type" => "bot_command",
//                "offset" => 0,
//                "length" => 14
//            )
//        )
//    );
    
// ADD/REMOVE BOT FROM GROUP
//    $privateMessage = array(
//        "update_id" => 624793628,
//        "message" => array(
//            "message_id" => 3407,
//            "from" => array(
//                "id" => 19179842,
//                "first_name" => "fagottino",
//                "username" => "fagottino"
//            ),
//             "chat" => array(
//                "id" => -114342037,
//                "title" => "TestGroup",
//                "type" => "group",
//                "all_members_are_administrators" => true
//            ),
//            "date" => 1483614336,
//            "new_chat_member" => array(
//                "id" => 186132931,
//                "first_name" => "Il benefattore del caff\u00e8",
//                "username" => "IlBenefattoreDelCaffe_Bot"
//            )
//        )
//    );
    
// MESSAGE WITH ANSWER
//    $privateMessage = array(
//        "update_id" => 624793660,
//        "message" => array(
//            "message_id" => 4854,
//            "from" => array(
//                "id" => 19179842,
//                "first_name" => "fagottino",
//                "username" => "fagottino"
//            ),
//             "chat" => array(
//                "id" => -200342587,
//                "title" => "TestGroup",
//                "type" => "group",
//                "all_members_are_administrators" => true
//            ),
//            "date" => 1483905746,
//            "reply_to_message" => array(
//                "message_id" => 4853,
//                "from" => array(
//                    "id" => 186132931,
//                    "first_name" => "Il benefattore del caff\u00e8",
//                    "username" => "IlBenefattoreDelCaffe_Bot"
//                ),
//                 "chat" => array(
//                    "id" => -200342587,
//                    "title" => "TestGroup",
//                    "type" => "group",
//                    "all_members_are_administrators" => true
//                ),
//                "date" => 1483905728,
//                "text" => "Ciao a tutti! @fagottino ha il potere.\nPer iniziare chiedete a lui.",
//                "entities" => array(
//                    "type" => "mention",
//                    "offset" => 14,
//                    "length" => 10
//                )
//                ),
//            "text" => Emoticon::group().$lang->menu->yourGroups
//                )
//            );

// MESSAGE WITH ANSWER RICCISHOP
//    $privateMessage = array(
//        "update_id" => 624794186,
//        "message" => array(
//            "message_id" => 4443,
//            "from" => array(
//                "id" => 49402640,
//                "first_name" => "Riccishop.it",
//                "username" => "riccishop"
//            ),
//             "chat" => array(
//                "id" => -114342037,
//                "title" => "TestGroup",
//                "type" => "group",
//                "all_members_are_administrators" => true
//            ),
//            "date" => 1483905746,
//            "reply_to_message" => array(
//                "message_id" => 4438,
//                "from" => array(
//                    "id" => 186132931,
//                    "first_name" => "Il benefattore del caff\u00e8",
//                    "username" => "IlBenefattoreDelCaffe_Bot"
//                ),
//                 "chat" => array(
//                    "id" => -114342037,
//                    "title" => "TestGroup",
//                    "type" => "group",
//                    "all_members_are_administrators" => true
//                ),
//                "date" => 1483905728,
//                "text" => "Ok, iniziamo!\nChi vuole partecipare deve salutatarmi con il tasto in basso.",
//                ),
//            "text" => $lang->ui->hiFriend.Emoticon::giveMeFive()
//                )
//            );
    
//// CALLACK PRIVATE QUERY
//    $privateMessage = array(
//        "update_id" => 624792859,
//        "callback_query" => array(
//            "id" => "82376794464694035",
//            "from" => array(
//                "id" => 19179842,
//                "first_name" => "fagottino",
//                "username" => "fagottino"
//                ),
//            "message" => array(
//                "message_id" => 8288,
//                "from" => array(
//                    "id" => 186132931,
//                    "first_name" => "Il benefattore del caff\u00e8",
//                    "username" => "IlBenefattoreDelCaffe_Bot"
//                    ),
//                "chat" => array(
//                    "id" => 19179842,
//                    "first_name" => "fagottino",
//                    "username" => "fagottino",
//                    "type" => "private"
//                    ),
//                "date" => 1483050172,
//                "text" => "Setta la linuga:"
//                ),
//            "chat_instance" => "-4206174352189128888",
//            "data" => "yes"
//            )
//        );
        
// CALLACK GROUP QUERY
//    $privateMessage = array(
//        "update_id" => 404132637,
//        "callback_query" => array(
//            "id" => "82376795781791020",
//            "from" => array(
//                "id" => 19179842,
//                "first_name" => "fagottino",
//                "username" => "fagottino"
//            ),
//            "message" => array (
//                "message_id" => 5938,
//                "from" => array (
//                    "id" => 186132931,
//                    "first_name" => "Il benefattore del caff\u00e8",
//                    "username" => "IlBenefattoreDelCaffe_Bot"
//                ),
//                "chat" => array (
//                    "id" => -114342037,
//                    "title" => "TestGroup",
//                    "type" => "group",
//                    "all_members_are_administrators" => true
//                ),
//                "date" => 1484227281,
//                "text" => "Ok @fagottino!\nSeleziona i nomi dei partecipanti.",
//                "entities" => array(
//                        "type" => "mention",
//                        "offset" => 3,
//                        "length" => 10
//                        )
//                ),
//            "chat_instance" => "-3971973381224532564",
//            "data" => "competitor~37889904"
//        )
//    );
    
//    $json = '{
//    "update_id": 404135507,
//    "message": {
//        "message_id": 9117,
//        "from": {
//            "id": 49402640,
//            "first_name": "Riccishop.it",
//            "username": "riccishop"
//        },
//        "chat": {
//            "id": -114342037,
//            "title": "TestGroup",
//            "type": "group",
//            "all_members_are_administrators": true
//        },
//        "date": 1492897863,
//        "reply_to_message": {
//            "message_id": 9111,
//            "from": {
//                "id": 186132931,
//                "first_name": "Il benefattore del caff\u00e8",
//                "username": "IlBenefattoreDelCaffe_Bot"
//            },
//            "chat": {
//                "id": -114342037,
//                "title": "TestGroup",
//                "type": "group",
//                "all_members_are_administrators": true
//            },
//            "date": 1492897839,
//            "text": "Reset avvenuto con successo. Ok, iniziamo!\n \ud83d\ude0aChi vuole partecipare deve salutatarmi con il tasto in basso."
//        },
//        "text": "Ciao amico\u270b"
//    }
//}';
    
//    $json = '{
//    "update_id": 404135565,
//    "message": {
//        "message_id": 9199,
//        "from": {
//            "id": 19179842,
//            "first_name": "fagottino",
//            "username": "fagottino"
//        },
//        "chat": {
//            "id": -114342037,
//            "title": "TestGroup",
//            "type": "group",
//            "all_members_are_administrators": true
//        },
//        "date": 1492962856,
//        "new_chat_participant": {
//            "id": 88998907,
//            "first_name": "Alessio"
//        },
//        "new_chat_member": {
//            "id": 88998907,
//            "first_name": "Alessio"
//        },
//        "new_chat_members": [
//            {
//                "id": 88998907,
//                "first_name": "Alessio"
//            }
//        ]
//    }
//    }';
    
    $json = '{
    "update_id": 404135787,
    "callback_query": {
        "id": "82376794293774174",
        "from": {
            "id": 19179842,
            "first_name": "fagottino",
            "username": "fagottino"
        },
        "message": {
            "message_id": 9436,
            "from": {
                "id": 186132931,
                "first_name": "Il benefattore del caff\u00e8",
                "username": "IlBenefattoreDelCaffe_Bot"
            },
            "chat": {
                "id": 19179842,
                "first_name": "fagottino",
                "username": "fagottino",
                "type": "private"
            },
            "date": 1493056756,
            "edit_date": 1493056762,
            "reply_to_message": {
                "message_id": 9435,
                "from": {
                    "id": 19179842,
                    "first_name": "fagottino",
                    "username": "fagottino"
                },
                "chat": {
                    "id": 19179842,
                    "first_name": "fagottino",
                    "username": "fagottino",
                    "type": "private"
                },
                "date": 1493056755,
                "text": "\ud83d\udc65I miei gruppi"
            },
            "text": "Yess, gestisci qui le impostazioni del gruppo."
        },
        "chat_instance": "-4206174352189128888",
        "data": "en"
    }
}';
    
    $privateMessage = json_decode($json, true);
    
    $unreadMessage = $privateMessage;
}
