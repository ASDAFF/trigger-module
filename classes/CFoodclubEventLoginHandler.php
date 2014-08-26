<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/classes/FoodclubEventActionClass.php");

class CFoodclubEventLoginHandler
{
    public static function OnBeforeUserLoginHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields);
    }

    /*
    [CLASS_ID] => CondGroup
    [DATA] => Array
        (
            [All] => AND
            [True] => False
        )

    [CHILDREN] => Array
        (
            [0] => Array
                (
                    [CLASS_ID] => CondIBElement
                    [DATA] => Array
                        (
                            [logic] => Equal
                            [value] => 4
                        )

                )

            [1] => Array
                (
                    [CLASS_ID] => CondIBSection
                    [DATA] => Array
                        (
                            [logic] => Equal
                            [value] => 29
                        )

                )

            [2] => Array
                (
                    [CLASS_ID] => CondIBIBlock
                    [DATA] => Array
                        (
                            [logic] => Equal
                            [value] => 6
                        )

                )

            [3] => Array
                (
                    [CLASS_ID] => CondUser
                    [DATA] => Array
                        (
                            [logic] => Equal
                            [value] => 20
                        )

                )

            [4] => Array
                (
                    [CLASS_ID] => CondUserGroup
                    [DATA] => Array
                        (
                            [logic] => Equal
                            [value] => 2
                        )

                )

        )
        */

    public static function OnAfterUserLoginHandler(&$arFields) {
        /*if(!empty($arFields["EVENTS"])){
            
            foreach($arFields["EVENTS"] as $event){
                $bOk = true;
                
                if(strlen($event["CONDITIONS"]) > 0 ){
                    $TriggerList = array();
                    $arActionID = array();
                    $TriggerActionList = array();
                    $TrueEventCounter = 0;$NeedTrueEvent = 0;
                    $bOk = false;

                    $arConditions = unserialize($event["CONDITIONS"]);                    
                    if(!empty($arConditions["CHILDREN"])){
                        //echo "<pre>";print_R($arConditions);echo "</pre>";die;
                        $NeedTrueEvent = count($arConditions["CHILDREN"]);                        
                        
                        foreach($arConditions["CHILDREN"] as $child){
                            if(strlen($child["CLASS_ID"]) > 0 && !empty($child["DATA"])){
                                switch($child["CLASS_ID"]){
                                    case "CondUserGroup":
                                    //Проверяем группу пользователя                                        
                                    if(CFoodclubEventDoAction::checkUserGroup(
                                        CUser::GetUserGroupArray(),
                                        $child["DATA"]["logic"],
                                        $child["DATA"]["value"]
                                    ))
                                    {
                                        $TrueEventCounter++;
                                    }
                                    break;
                                    case "CondIBElement":
                                    break;
                                    case "CondIBSection":
                                    break;
                                    case "CondIBIBlock":
                                    break;
                                    case "CondUser":
                                    //Проверяем пользователя
                                    if(CFoodclubEventDoAction::checkUser(
                                        CUser::GetID(),
                                        $child["DATA"]["logic"],
                                        $child["DATA"]["value"]
                                    ))
                                    {
                                        $TrueEventCounter++;
                                    }
                                    break;
                                }
                            }
                        }
                        $bOk = CFoodclubEventDoAction::checkLogic($arConditions["DATA"],$TrueEventCounter,$NeedTrueEvent);
                        //die;
                    }

                    if($bOk){
                        //Получаем триггеры
                        CFoodclubEventDoAction::checkAction($event);
                    }
                }                    
            }            
        }*/
        $action = new CFoodclubEventAction;
        $action->build($arFields); 
    }

    //эти обработчики кажется не нужны
    public static function OnAfterUserAuthorizeHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields); 
    }

    public static function OnUserLoginExternalHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields); 
    }
}