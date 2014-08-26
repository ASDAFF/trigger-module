<?
global $DB, $MESS, $DBType;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/filter_tools.php");
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/errors.php");

$arTreeDescr = array(
	'js' => '/bitrix/js/fevent/core_tree.js',
	'css' => '/bitrix/panel/fevent/catalog_cond.css',
	'lang' => '/bitrix/modules/fevent/lang/'.LANGUAGE_ID.'/js_core_tree.php',
);
CJSCore::RegisterExt('core_condtree', $arTreeDescr);

/*$GLOBALS["CACHE_ADVERTISING"] = Array(
	"BANNERS_ALL" => Array(),
	"BANNERS_CNT" => Array(),
	"CONTRACTS_ALL" => Array(),
	"CONTRACTS_CNT" => Array(),
);*/

CModule::AddAutoloadClasses(
	"fevent",
	array(
		"CFTriggerActions" => "classes/".$DBType."/actions.php",
		"CFTriggerActionTypes" => "classes/".$DBType."/action_types.php",
		"CFTriggerConditions" => "classes/".$DBType."/conditions.php",
		"CFTriggerEvents" => "classes/".$DBType."/events.php",
		"CFTriggerEventTypes" => "classes/".$DBType."/event_types.php",
		"CFTriggerEventTypeFields" => "classes/".$DBType."/event_type_fields.php",
		"CFTrigger" => "classes/".$DBType."/triggers.php",
		"CCatalogCondTree" => "classes/".$DBType."/conditions.php",
        "CFoodclubEventUserHandler" => "classes/CFoodclubEventUserHandler.php",
        "CFoodclubEventRegisterHandler" => "classes/CFoodclubEventRegisterHandler.php",
        "CFTriggerActionTypeFields" => "classes/".$DBType."/action_type_fields.php",
        "CFTriggerLog" => "classes/".$DBType."/log.php",
        "CFoodclubIblockElementEventHandler" => "classes/CFoodclubIblockElementEventHandler.php",
	)
);

/*CPanasonicMDBLog::Init(); // инициализация класса, занесение в свойства значения уровня логирования
CPanasonicMDBBitrixUserCreator::Init();
InitProxy();


function InitProxy() {
    $options = array(
        "panasonic_mbd_host",
        "panasonic_mbd_client_id",
        "panasonic_mbd_secret_key",
    );

    $connectionParams = array();

    foreach ($options as $option) {
        $connectionParams[$option] = COption::GetOptionString("panasonicmdbconnect", $option);
    }

    $host = $connectionParams["panasonic_mbd_host"];
    $key = $connectionParams["panasonic_mbd_client_id"];
    $secret = $connectionParams["panasonic_mbd_secret_key"];

    CPanasonicMDBProxy::init($host, $key, $secret);
    //эта строка нужна на тестовом. на продакшене по умолчанию https
    //CPanasonicMDBProxy::setProtocol('http'); //TODO потом это убрать
    CPanasonicMDBProxy::setRequestOption("timeout", 5);
}*/


class CFoodclubEvent //CPanasonicMDBConnect
{
	private function getEventTypeIDList($type, $code = "", $handler = "") {
		global $DB;

		$strSql =
			"SELECT ID ".
			"FROM f_event_types WHERE `TYPE` = '".$type."'";

        if(strlen($code)){
            $strSql .= " AND `CODE` = '".$code."'";
        }

        if(strlen($handler)){
            $strSql .= " AND `HANDLER` = '".$handler."'";
        }        

		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$arEventTypeIDList = array();

		while ($arEventType = $res->Fetch())
			$arEventTypeIDList[] = $arEventType["ID"];
		
		return $arEventTypeIDList;
	}

	private function getEventList($IdList) {
		global $DB;

		if(empty($IdList)){
			return array();
		}

		$strSql =
			"SELECT * ".
			"FROM f_events WHERE `EVENT_TYPE` IN (".implode(",", $IdList).")";
		
		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$arEventList = array();

		while ($arEvent = $res->Fetch())
			$arEventList[] = $arEvent;
		
		return $arEventList;
	}


    //USER HANDLERS
    function OnBeforeUserRegisterHandler(&$arFields) {
        //return CPanasonicMDBRegisterHandler::OnBeforeUserRegisterHandler($arFields);
        $idList = self::getEventTypeIDList("USER", "REGISTRATION", "OnBeforeUserRegisterHandler");
        $arFields["EVENTS"] = self::getEventList($idList);       
        return CFoodclubEventUserHandler::OnBeforeUserRegisterHandler($arFields);
    }

    function OnAfterUserRegisterHandler(&$arFields) {
        //return CPanasonicMDBRegisterHandler::OnAfterUserRegisterHandler($arFields);
        $idList = self::getEventTypeIDList("USER", "REGISTRATION", "OnAfterUserRegisterHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
        return CFoodclubEventUserHandler::OnAfterUserRegisterHandler($arFields);
    }

    function OnBeforeUserLoginHandler(&$arFields) {
        //return CPanasonicMDBLoginHandler::OnBeforeUserLoginHandler($arFields);
        $idList = self::getEventTypeIDList("USER", "AUTHORIZATION", "OnBeforeUserLoginHandler");        
        $arFields["EVENTS"] = self::getEventList($idList);
        return CFoodclubEventUserHandler::OnBeforeUserLoginHandler($arFields);
    }

    function OnAfterUserLoginHandler(&$arFields) {
        //return CPanasonicMDBLoginHandler::OnAfterUserLoginHandler($arFields);
        $idList = self::getEventTypeIDList("USER", "AUTHORIZATION", "OnAfterUserLoginHandler");
        //echo "<pre>";print_r($idList);echo "</pre>";
        $arFields["EVENTS"] = self::getEventList($idList);
        //echo "<pre>";print_r($arFields["EVENTS"]);echo "</pre>";die;
        return CFoodclubEventUserHandler::OnAfterUserLoginHandler($arFields);
    }

    function OnUserLoginExternalHandler(&$arFields) {
        //return CPanasonicMDBLoginHandler::OnUserLoginExternalHandler($arFields);
        $idList = self::getEventTypeIDList("USER", "AUTHORIZATION", "OnUserLoginExternalHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
        return CFoodclubEventUserHandler::OnUserLoginExternalHandler($arFields);
    }

    function OnAfterUserAuthorizeHandler(&$arFields) {
        //return CPanasonicMDBLoginHandler::OnAfterUserAuthorizeHandler($arFields);
        $idList = self::getEventTypeIDList("USER", "AUTHORIZATION", "OnAfterUserAuthorizeHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
        return CFoodclubEventUserHandler::OnAfterUserAuthorizeHandler($arFields);
    }

    function OnBeforeUserAddHandler(&$arFields) {
        //return CPanasonicMDBUserAddHandler::OnBeforeUserAddHandler($arFields);
        $arFields["EVENTS"] = self::getEvents("USER");
    }

    function OnAfterUserAddHandler(&$arFields) {
        //return CPanasonicMDBUserAddHandler::OnAfterUserAddHandler($arFields);
        $idList = getEventTypeIDList("USER", "AUTHORIZATION", "OnAfterUserAddHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
    }

    function OnBeforeUserUpdateHandler(&$arFields) {
        //return CPanasonicMDBUpdateHandler::OnBeforeUserUpdateHandler($arFields);
        $idList = getEventTypeIDList("USER", "AUTHORIZATION", "OnBeforeUserUpdateHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
    }

    function OnAfterUserUpdateHandler(&$arFields) {
        //return CPanasonicMDBUpdateHandler::OnAfterUserUpdateHandler($arFields);
        $idList = getEventTypeIDList("USER", "AUTHORIZATION", "OnAfterUserUpdateHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
    }

    function OnBeforeUserDeleteHandler(&$arFields) {
        //return CPanasonicMDBDeleteHandler::OnBeforeUserDeleteHandler($arFields);
        $idList = getEventTypeIDList("USER", "AUTHORIZATION", "OnBeforeUserDeleteHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
    }

    function OnAfterSocServUserAddHandler(&$arFields) {
        //return CPanasonicMDBSocServUserAddHandler::OnAfterSocServUserAddHandler($arFields);
        $idList = getEventTypeIDList("USER", "AUTHORIZATION", "OnAfterSocServUserAddHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
    }

    function OnBeforePrologHandler(&$arFields) {
        /*if (isset($_SESSION["redirect_page_url"])) {
            $url = $_SESSION["redirect_page_url"];
            unset($_SESSION["redirect_page_url"]);
            $GLOBALS["USER"]->Logout();
            LocalRedirect($url);
        }*/
    }

    //IBLOCK ELEMENT HANDLERS
    function OnBeforeIBlockElementAddHandler(&$arFields) {
        $idList = getEventTypeIDList("IBLOCK", "IBLOCK_ELEMENT_ADD", "OnBeforeIBlockElementAddHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
        return CFoodclubIblockElementEventHandler::OnBeforeIBlockElementAddHandler($arFields);
    }

    function OnStartIBlockElementAddHandler(&$arFields) {
        $idList = getEventTypeIDList("IBLOCK", "IBLOCK_ELEMENT_ADD", "OnStartIBlockElementAddHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
        return CFoodclubIblockElementEventHandler::OnStartIBlockElementAddHandler($arFields);
    }

    function OnAfterIBlockElementAddHandler(&$arFields) {
        $idList = self::getEventTypeIDList("IBLOCK", "IBLOCK_ELEMENT_ADD", "OnAfterIBlockElementAddHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
        //echo "<pre>";print_r($arFields);echo "</pre>";die;
        return CFoodclubIblockElementEventHandler::OnAfterIBlockElementAddHandler($arFields);
    }

    function OnBeforeIBlockElementUpdateHandler(&$arFields) {
        $idList = getEventTypeIDList("IBLOCK", "IBLOCK_ELEMENT_UPDATE", "OnBeforeIBlockElementUpdateHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
        return CFoodclubIblockElementEventHandler::OnBeforeIBlockElementUpdateHandler($arFields);
    }
    
    function OnStartIBlockElementUpdateHandler(&$arFields) {
        $idList = getEventTypeIDList("IBLOCK", "IBLOCK_ELEMENT_UPDATE", "OnStartIBlockElementUpdateHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
        return CFoodclubIblockElementEventHandler::OnStartIBlockElementUpdateHandler($arFields);
    }
    
    function OnAfterIBlockElementUpdateHandler(&$arFields) {
        $idList = getEventTypeIDList("IBLOCK", "IBLOCK_ELEMENT_UPDATE", "OnAfterIBlockElementUpdateHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
        return CFoodclubIblockElementEventHandler::OnAfterIBlockElementUpdateHandler($arFields);
    }

    function OnBeforeIBlockElementDeleteHandler(&$arFields) {
        $idList = getEventTypeIDList("IBLOCK", "IBLOCK_ELEMENT_DELETE", "OnBeforeIBlockElementDeleteHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
        return CFoodclubIblockElementEventHandler::OnBeforeIBlockElementDeleteHandler($arFields);
    }
    
    function OnAfterIBlockElementDeleteHandler(&$arFields) {
        $idList = getEventTypeIDList("IBLOCK", "IBLOCK_ELEMENT_DELETE", "OnAfterIBlockElementDeleteHandler");
        $arFields["EVENTS"] = self::getEventList($idList);
        return CFoodclubIblockElementEventHandler::OnAfterIBlockElementDeleteHandler($arFields);
    }

    /*function OnOrderUpdateHandler($ID, $arFields) {
        //если заказ открыт через админку обрабатывать не надо (https://dev.1c-bitrix.ru/community/forums/forum6/topic48782/)
        if (isset($arFields['LOCKED_BY']))
            return true;

        //если нет цены обрабатывать не надо (на еплазе заказ обновляется 3 раза при покупке, а отправить в МБД нужно только раз)
        if (!isset($arFields['PRICE']) || $arFields['PRICE'] == "")
            return true;


        // *
        // * при создании заказа обновляются только поля PRICE и UPDATED_1C. Заказ отправляется в МБД только в этот  момент
        // * при редактировании заказа через админку PRICE тоже есть но кроме этого есть много других полей (все который есть на странице редактирования)
        // * примерно так
        // * Array ( [LID] => s1 [PERSON_TYPE_ID] => 1 [PRICE] => 510 [CURRENCY] => RUB [USER_ID] => 91989 [PAY_SYSTEM_ID] => 1 [PRICE_DELIVERY] => 0 [DELIVERY_ID] => [DISCOUNT_VALUE] => 0 [TAX_VALUE] => 0 [USER_DESCRIPTION] => [ADDITIONAL_INFO] => [COMMENTS] => Оригинальный заказ: <br /> (848723) - KX-TS2352RU (3339) - 1.00шт. - 510.00руб/шт.<br />--------1/510 [STATUS_ID] => F [UPDATED_1C] => N )
        // * поэтому чтобы не посылать заказ в МБД при таком редактировании можно сделать проверку на наличие например LID
        // *
        if (isset($arFields['LID']))
            return true;


        CPanasonicMDBLog::logDebugMessage("OnOrderUpdateHandler: " . print_r($arFields, true));

        CModule::IncludeModule('sale');
        $arOrder = CSaleOrder::GetByID($ID);
        $user_id = $arOrder["USER_ID"];

        //получение global_id
        $filter = array("ID" => $user_id);
        $arParams["SELECT"][] = "UF_MDB_GLOBAL_ID";
        $rsUsers = CUser::GetList($by, $order, $filter, $arParams);
        if ($users = $rsUsers->Fetch()) {
            $global_id = $users["UF_MDB_GLOBAL_ID"];
        }


        //
        //без global_id нельзя отправить заказ в МБД
        //т.к может быть несколько профилей с таким логином (до слияния например)
        //if ($global_id == "")
        //    return true;
		//
        //$account = array(
        //    array(
        //        "login" => $global_id,
        //        "type" => "global_id"
        //    ),
        //);
        //



        $user_profile = CPanasonicMDBUserDataExtractorHelper::GetFullProfile(array("ID" => $user_id), true);
        $account = $user_profile["account"];

        if (empty($account)) {
            $user_profile = CPanasonicMDBUserDataExtractorHelper::GetFullProfile(array("ID" => $user_id));
            $account = $user_profile["account"];
            CPanasonicMDBLog::logErrorMessage("пользователь без global_id сделал заказ. account: " . print_r($account, true));
        }

        $orders_helper = new CPanasonicMDBOrdersHelper();
        $order_data = $orders_helper->get_order_data_by_id($ID);

        $updated = array(
            "orders" => array($order_data)
        );

        CPanasonicMDBLog::logDebugMessage("добавлен заказ: " . print_r($updated, true));

        try {
            $res = CPanasonicMDBProxy::apiUpdateProfile($account, $updated, array());
        } catch (Exception $ex) {
            CPanasonicMDBLog::logErrorMessage("Добавление заказа CPanasonicMDBProxy::apiUpdateProfile error: " . (string)$ex);
            CPanasonicMDBLog::logErrorMessage("account: " . print_r($account, true));
            CPanasonicMDBLog::logErrorMessage("updated: " . print_r($updated, true));
            return true;
        }

        CPanasonicMDBLog::logDebugMessage("order add res: " . print_r($res, true));

        return true;
    }*/
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/admin_tools.php");
?>
