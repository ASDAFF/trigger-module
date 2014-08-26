<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/classes/FoodclubEventActionClass.php");

class CFoodclubIblockElementEventHandler
{
    function OnBeforeIBlockElementAddHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields);
    }

    function OnStartIBlockElementAddHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields);
    }

    function OnAfterIBlockElementAddHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields);
    }

    function OnBeforeIBlockElementUpdateHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields);
    }
    
    function OnStartIBlockElementUpdateHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields);
    }
    
    function OnAfterIBlockElementUpdateHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields);
    }

    function OnBeforeIBlockElementDeleteHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields);
    }
    
    function OnAfterIBlockElementDeleteHandler(&$arFields) {
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
}