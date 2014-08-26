<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/classes/FoodclubEventActionClass.php");

class CFoodclubEventUserHandler{

	public static function OnBeforeUserLoginHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields);
    }

    public static function OnAfterUserLoginHandler(&$arFields) {
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

    public static function OnBeforeUserRegisterHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields);
    }

    //TODO похоже что этот обработчик не нужен
    public static function OnAfterUserRegisterHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields);
    }

	function OnBeforeUserUpdateHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields);
    }

    function OnAfterUserUpdateHandler(&$arFields) {
        $action = new CFoodclubEventAction;
        $action->build($arFields);
    }
}
?>