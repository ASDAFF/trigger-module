<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/classes/FoodclubEventActionClass.php");

class CFoodclubEventRegisterHandler
{
    public static function OnBeforeUserRegisterHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields);
    }

    //TODO похоже что этот обработчик не нужен
    public static function OnAfterUserRegisterHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields);
    }
}