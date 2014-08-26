<?class CAllFTrigger{
	//TODO:
	function GetTrigger($trigger)
	{
		$arRes = CFTrigger::GetByID($trigger);
		return $arRes;
	}

	function CheckFields($ACTION, &$arFields, $strCurrencyID = false)
	{
		global $APPLICATION;
		global $DB;

		$arMsg = array();

		if ('UPDATE' != $ACTION && 'ADD' != $ACTION)
			return false;

		if ('UPDATE' == $ACTION)
		{
			if (strlen($strCurrencyID) <= 0)
			{
				$arMsg[] = array('id' => 'CURRENCY','text' => GetMessage('BT_MOD_CURR_ERR_CURR_CUR_ID_BAD'));
			}
			else
			{
				$strCurrencyID = substr($strCurrencyID, 0, 3);
			}

			if (!isset($arFields["NAME"]))
			{
				$arMsg[] = array('id' => 'NAME','text' => GetMessage('BT_MOD_CURR_ERR_CURR_CUR_ID_LAT'));
			}
		}

		if ('ADD' == $ACTION)
		{
			if (!isset($arFields["NAME"]))
			{
				$arMsg[] = array('id' => 'NAME','text' => GetMessage('BT_MOD_CURR_ERR_CURR_CUR_ID_LAT'));
			}
		}

		/*if (is_set($arFields, "CURRENCY") || 'ADD' == $ACTION)
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
				$db_result = $DB->Query("SELECT 'x' FROM b_catalog_currency WHERE UPPER(CURRENCY) = UPPER('".$DB->ForSql($arFields["CURRENCY"])."')");
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

		if (!CFTrigger::CheckFields('ADD', $arFields))
			return false;

		$arInsert = $DB->PrepareInsert("f_triggers", $arFields);

		$strSql =
			"INSERT INTO f_triggers(".$arInsert[0].") ".
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

	function Update($id, $arFields)
	{
		global $DB;
		global $CACHE_MANAGER;

		if (!CFTrigger::CheckFields('UPDATE', $arFields, $id))
			return false;

		$TriggerID = substr($id, 0, 3);

		//echo "<pre>";print_r($arFields);echo "</pre>";die;

		$strUpdate = $DB->PrepareUpdate("f_triggers", $arFields);

		//echo "<pre>";print_r($strUpdate);echo "</pre>";die;

		if (strlen($strUpdate) > 0)
		{
			$strSql = "UPDATE f_triggers SET ".$strUpdate." WHERE ID = '".$DB->ForSql($TriggerID)."' ";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			//echo $strSql;die;
		}

		return $TriggerID;
	}

	function Delete($currency)
	{
		global $DB;
		global $stackCacheManager;
		global $CACHE_MANAGER;

		$currency = substr($currency, 0, 3);

		$bCanDelete = true;
		$db_events = GetModuleEvents("fevent", "OnBeforeCurrencyDelete");
		while($arEvent = $db_events->Fetch())
			if(ExecuteModuleEventEx($arEvent, array($currency))===false)
				return false;

		$events = GetModuleEvents("fevent", "OnCurrencyDelete");
		while($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($currency));

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

		return $DB->Query("DELETE FROM f_triggers WHERE ID = '".$DB->ForSQL($currency)."'", true);
	}

	function GetByID($currency)
	{
		global $DB;

		$strSql =
			"SELECT CUR.* ".
			"FROM f_triggers CUR ".
			"WHERE CUR.ID = '".$DB->ForSQL($currency, 3)."'";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
			return $res;

		return false;
	}

	function GetList(&$by, &$order, $filter, $lang = LANGUAGE_ID)
	{
		global $DB;
		global $CACHE_MANAGER;

		if (defined("CURRENCY_SKIP_CACHE") && CURRENCY_SKIP_CACHE
			|| StrToLower($by) == "name"
			|| StrToLower($by) == "currency"
			|| StrToLower($order) == "desc")
		{
			$dbCurrencyList = CFTrigger::__GetList($by, $order, $filter, $lang);
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
				$dbCurrencyList = CFTrigger::__GetList($by, $order, $filter, $lang);
				while ($arCurrency = $dbCurrencyList->Fetch())
					$arCurrencyList[] = $arCurrency;

				$CACHE_MANAGER->Set("currency_currency_list_".$lang, $arCurrencyList);

				$dbCurrencyList = new CDBResult();
				$dbCurrencyList->InitFromArray($arCurrencyList);
			}
		}

		return $dbCurrencyList;
	}
}?>