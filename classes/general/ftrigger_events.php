<?
IncludeModuleLangFile(__FILE__);

class CAllFTriggerEvents{
	//TODO: 
	function GetTrigger($trigger)
	{
		$arRes = CFTriggerEvents::GetByID($trigger);
		return $arRes;
	}

	function CheckFields($ACTION, &$arFields, $strCurrencyID = false)
	{
		global $APPLICATION;
		global $DB;

		$arMsg = array();

		if ('UPDATE' != $ACTION && 'ADD' != $ACTION)
			return false;

		/*if ('UPDATE' == $ACTION)
		{
			if (strlen($strCurrencyID) <= 0)
			{
				$arMsg[] = array('id' => 'CURRENCY','text' => GetMessage('BT_MOD_CURR_ERR_CURR_CUR_ID_BAD'));
			}
			else
			{
				$strCurrencyID = substr($strCurrencyID, 0, 3);
			}
		}

		if (is_set($arFields, "CURRENCY") || 'ADD' == $ACTION)
		{
			if (!is_set($arFields, "CURRENCY"))
			{
				$arMsg[] = array('id' => 'CURRENCY','text' => GetMessage('BT_MOD_CURR_ERR_CURR_CUR_ID_ABSENT'));
			}
			else
			{
				$arFields["CURRENCY"] = substr($arFields["CURRENCY"], 0, 3);
			}
		}

		if ('ADD' == $ACTION)
		{
			if (!preg_match("~^[a-z]{3}$~i", $arFields["CURRENCY"]))
			{
				$arMsg[] = array('id' => 'CURRENCY','text' => GetMessage('BT_MOD_CURR_ERR_CURR_CUR_ID_LAT'));
			}
			else
			{
				$db_result = $DB->Query("SELECT 'x' FROM f_events WHERE UPPER(CURRENCY) = UPPER('".$DB->ForSql($arFields["CURRENCY"])."')");
				if ($db_result->Fetch())
				{
					$arMsg[] = array('id' => 'CURRENCY','text' => GetMessage('BT_MOD_CURR_ERR_CURR_CUR_ID_EXISTS'));
				}
				else
				{
					$arFields["CURRENCY"] = strtoupper($arFields["CURRENCY"]);
				}
			}
		}

		if (is_set($arFields, 'AMOUNT_CNT') || 'ADD' == $ACTION)
		{
			if (!isset($arFields['AMOUNT_CNT']))
			{
				$arMsg[] = array('id' => 'AMOUNT_CNT','text' => GetMessage('BT_MOD_CURR_ERR_CURR_AMOUNT_CNT_ABSENT'));
			}
			elseif (0 >= intval($arFields['AMOUNT_CNT']))
			{
				$arMsg[] = array('id' => 'AMOUNT_CNT','text' => GetMessage('BT_MOD_CURR_ERR_CURR_AMOUNT_CNT_BAD'));
			}
			else
			{
				$arFields['AMOUNT_CNT'] = intval($arFields['AMOUNT_CNT']);
			}
		}

		if (is_set($arFields, 'AMOUNT') || 'ADD' == $ACTION)
		{
			if (!isset($arFields['AMOUNT']))
			{
				$arMsg[] = array('id' => 'AMOUNT','text' => GetMessage('BT_MOD_CURR_ERR_CURR_AMOUNT_ABSENT'));
			}
			else
			{
				$arFields['AMOUNT'] = doubleval($arFields['AMOUNT']);
				if (!(0 < $arFields['AMOUNT']))
				{
					$arMsg[] = array('id' => 'AMOUNT','text' => GetMessage('BT_MOD_CURR_ERR_CURR_AMOUNT_BAD'));
				}
			}
		}*/

		if (is_set($arFields,'SORT') || 'ADD' == $ACTION)
		{
			$arFields['SORT'] = intval($arFields['SORT']);
			if (0 >= $arFields['SORT'])
				$arFields['SORT'] = 100;
		}

		/*if (isset($arFields['DATE_UPDATE']))
			unset($arFields['DATE_UPDATE']);*/

		if (!empty($arMsg))
		{			
			$obError = new CAdminException($arMsg);
			$APPLICATION->ResetException();
			$APPLICATION->ThrowException($obError);
			return false;
		}
		return true;
	}

	function Add($arFields)
	{
		global $DB;
		global $CACHE_MANAGER;

		if (!CFTriggerEvents::CheckFields('ADD', $arFields))
			return false;

		$arInsert = $DB->PrepareInsert("f_events", $arFields);

		$strSql =
			"INSERT INTO f_events(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$CACHE_MANAGER->Clean("currency_currency_list");
		$rsLangs = CLanguage::GetList(($by="lid"), ($order="asc"));
		while ($arLang = $rsLangs->Fetch())
		{
			$CACHE_MANAGER->Clean("currency_currency_list_".$arLang['LID']);
		}
		$CACHE_MANAGER->Clean("currency_base_currency");

		return $arFields["CURRENCY"];
	}

	function Update($currency, $arFields)
	{
		global $DB;
		global $CACHE_MANAGER;

		if (!CFTriggerEvents::CheckFields('UPDATE', $arFields, $currency))
			return false;

		$strCurrencyID = substr($currency, 0, 3);

		$strUpdate = $DB->PrepareUpdate("f_events", $arFields);

		if (!empty($strUpdate))
		{			
			$strSql = "UPDATE f_events SET ".$strUpdate." WHERE ID = '".$DB->ForSql($strCurrencyID)."' ";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			$CACHE_MANAGER->Clean("currency_base_currency");
			$CACHE_MANAGER->Clean("currency_currency_list");
			$rsLangs = CLanguage::GetList(($by="lid"), ($order="asc"));
			while ($arLang = $rsLangs->Fetch())
			{
				$CACHE_MANAGER->Clean("currency_currency_list_".$arLang['LID']);
			}

			if (defined("BX_COMP_MANAGED_CACHE"))
				$CACHE_MANAGER->ClearByTag("currency_id_".$strCurrencyID);
		}

		return $strCurrencyID;
	}

	function Delete($action)
	{
		global $DB;
		global $stackCacheManager;
		global $CACHE_MANAGER;

		$action = substr($action, 0, 3);

		$bCanDelete = true;
		
		$stackCacheManager->Clear("currency_currency_lang");
		$stackCacheManager->Clear("currency_rate");
		$CACHE_MANAGER->Clean("currency_currency_list");
		$rsLangs = CLanguage::GetList(($by="lid"), ($order="asc"));
		while ($arLang = $rsLangs->Fetch())
		{
			$CACHE_MANAGER->Clean("currency_currency_list_".$arLang['LID']);
		}
		$CACHE_MANAGER->Clean("currency_base_currency");

		if (defined("BX_COMP_MANAGED_CACHE"))
			$CACHE_MANAGER->ClearByTag("currency_id_".$currency);

		return $DB->Query("DELETE FROM f_events WHERE ID = '".$DB->ForSQL($action)."'", true);
	}

	function GetByID($action)
	{
		global $DB;

		$strSql =
			"SELECT * ".
			"FROM f_events ".
			"WHERE ID = '".$DB->ForSQL($action, 3)."'";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
			return $res;

		return false;
	}

	function GetList(&$by, &$order, $lang = LANGUAGE_ID)
	{
		global $DB;
		global $CACHE_MANAGER;

		if (defined("CURRENCY_SKIP_CACHE") && CURRENCY_SKIP_CACHE
			|| StrToLower($by) == "name"
			|| StrToLower($by) == "currency"
			|| StrToLower($order) == "desc")
		{
			$dbCurrencyList = CFTriggerEvents::__GetList($by, $order, $lang);
		}
		else
		{
			$by = "sort";
			$order = "asc";

			$lang = substr($lang, 0, 2);

			$cacheTime = CURRENCY_CACHE_DEFAULT_TIME;
			if (defined("CURRENCY_CACHE_TIME"))
				$cacheTime = intval(CURRENCY_CACHE_TIME);

			if ($CACHE_MANAGER->Read($cacheTime, "currency_currency_list_".$lang))
			{
				$arCurrencyList = $CACHE_MANAGER->Get("currency_currency_list_".$lang);
				$dbCurrencyList = new CDBResult();
				$dbCurrencyList->InitFromArray($arCurrencyList);
			}
			else
			{
				$arCurrencyList = array();
				$dbCurrencyList = CFTriggerEvents::__GetList($by, $order, $lang);
				while ($arCurrency = $dbCurrencyList->Fetch())
					$arCurrencyList[] = $arCurrency;

				$CACHE_MANAGER->Set("currency_currency_list_".$lang, $arCurrencyList);

				$dbCurrencyList = new CDBResult();
				$dbCurrencyList->InitFromArray($arCurrencyList);
			}
		}

		return $dbCurrencyList;
	}
}

class CAllFTriggerEventTypes{

	function GetByID($action)
	{
		global $DB;

		$strSql =
			"SELECT * ".
			"FROM f_event_types ".
			"WHERE ID = '".$DB->ForSQL($action, 3)."'";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
			return $res;

		return false;
	}

	function GetList(&$by, &$order, $lang = LANGUAGE_ID)
	{
		global $DB;
		global $CACHE_MANAGER;
		$dbCurrencyList = CFTriggerEventTypes::__GetList($by, $order, $lang);

		return $dbCurrencyList;
	}
}

class CAllFTriggerEventTypeFields{

	function GetByID($action)
	{
		global $DB;

		$strSql =
			"SELECT * ".
			"FROM f_event_type_fields ".
			"WHERE ID = '".$DB->ForSQL($action, 3)."'";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
			return $res;

		return false;
	}

	function GetList(&$by, &$order, $filter = array(), $lang = LANGUAGE_ID)
	{
		global $DB;
		global $CACHE_MANAGER;

		$arFieldListsList = array();

		$dbFieldsList = CFTriggerEventTypeFields::__GetList($by, $order, $filter, $lang);
		while($arFieldList = $dbFieldsList->GetNext()){
			switch($arFieldList["FIELD_TYPE"]){
				case "GROUP_ID":
					$arFieldList["FieldStr"] = "<select name='GROUP_ID'>";
					$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), array("ACTIVE"=>"Y"));
					while($arGroup = $rsGroups->GetNext()){
						$arFieldList["FieldStr"] .= "<option value='".$arGroup["ID"]."'>".$arGroup["NAME"]."</option>";
					}
					$arFieldList["FieldStr"] .= "</select>";
				break;
				case "IBLOCK_ID":
					CModule::IncludeModule("iblock");
					$arFieldList["FieldStr"] = "<select name='IBLOCK_ID'>";
					$rsIblocks = CIBlock::GetList(Array("name"=>"asc"), Array('ACTIVE'=>'Y'), false);
					while($arIblock = $rsIblocks->Fetch()){						
						$arFieldList["FieldStr"] .= "<option value='".$arIblock["ID"]."'>".$arIblock["NAME"]." (".$arIblock["LID"].")</option>";
					}
					$arFieldList["FieldStr"] .= "</select>";
				break;
				case "FORM_FIELD":
					$arFieldList["FieldStr"] = "<select size='5' name='IBLOCK_ID' multiple='multiple'>";
					$rsUserEntities = CUserTypeEntity::GetList(array($by=>$order), array("ENTITY_ID" => "USER"));
					while($arUserEntity = $rsUserEntities->Fetch()){						
						$arFieldList["FieldStr"] .= "<option value='".$arUserEntity["ID"]."'>".$arUserEntity["FIELD_NAME"]."</option>";
					}
					$arFieldList["FieldStr"] .= "</select>";
			}
			$arFieldListsList[] = $arFieldList;
		}

		return $arFieldListsList;
		//return $dbCurrencyList;
	}
}
?>
