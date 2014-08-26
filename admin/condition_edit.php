<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/include.php");

$CURRENCY_RIGHT = $APPLICATION->GetGroupRight("fevent");
if ($CURRENCY_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

__IncludeLang(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/admin/", "/trigger_conditions.php"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/prolog.php");

ClearVars("f_");

$message = null;
$bVarsFromForm = false;

$ID = htmlspecialcharsEx(trim($ID));
$ID = (strlen($ID) <= 0 ? false : $ID);


/*$db_result_lang = CLangAdmin::GetList($by = "sort", $order = "asc");

$iCount = 0;
while ($db_result_lang_array = $db_result_lang->Fetch())
{
	$arLangsLID[$iCount] = $db_result_lang_array["LID"];
	$arLangNamesLID[$iCount] = htmlspecialcharsbx($db_result_lang_array["NAME"]);
	$iCount++;
}*/

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("action"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("action_settings")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if ($_SERVER["REQUEST_METHOD"] == "POST" && $CURRENCY_RIGHT=="W" && strlen($Update)>0 && check_bitrix_sessid())
{

	$arFields = array(
		"NAME" => $_POST['NAME'],
		"SORT" => $_POST['SORT']
	);

	$strAction = ($ID ? 'UPDATE' : 'ADD');
	$bVarsFromForm = !CFTriggerConditions::CheckFields($strAction, $arFields, $ID);

	if (!$bVarsFromForm)
	{
		$arMsg = array();
		/*for ($i=0; $i<$iCount; $i++)
		{
			if (!isset(${"FORMAT_STRING_".$arLangsLID[$i]}) || strlen(${"FORMAT_STRING_".$arLangsLID[$i]})<=0)
			{
				$arMsg[] = array("id"=>"FORMAT_STRING_".$arLangsLID[$i], "text"=> GetMessage("action_format_string", Array("#LANG#" => $arLangNamesLID[$i])));
				continue;
			}
		}*/

		if(!empty($arMsg))
		{
			$bVarsFromForm = true;
			$e = new CAdminException($arMsg);
			$APPLICATION->ThrowException($e);
			$e = $APPLICATION->GetException();
			$message = new CAdminMessage(GetMessage("action_error"), $e);
		}
		else
		{
			if (strlen($ID) > 0)
				CFTriggerConditions::Update($ID, $arFields);
			else
				$ID = CFTriggerConditions::Add($arFields);

			for ($i=0; $i<$iCount; $i++)
			{
				unset($arFields);
				$arFields["FORMAT_STRING"] = Trim(${"FORMAT_STRING_".$arLangsLID[$i]});
				$arFields["FULL_NAME"] = Trim(${"FULL_NAME_".$arLangsLID[$i]});
				$arFields["DEC_POINT"] = ${"DEC_POINT_".$arLangsLID[$i]};
				$arFields["THOUSANDS_SEP"] = ${"THOUSANDS_SEP_".$arLangsLID[$i]};
				$arFields["THOUSANDS_VARIANT"] = ${"THOUSANDS_VARIANT_".$arLangsLID[$i]};
				$arFields["DECIMALS"] = IntVal(${"DECIMALS_".$arLangsLID[$i]});
				$arFields["CURRENCY"] = $ID /*$arFields["CURRENCY"]*/;
				$arFields["LID"] = $arLangsLID[$i];
				if(strlen($arFields["THOUSANDS_VARIANT"]) > 0)
					$arFields["THOUSANDS_SEP"] = false;
				else
					$arFields["THOUSANDS_VARIANT"] = false;

				if (strlen($ID) > 0)
				{
					$db_result_lang = CCurrencyLang::GetByID($ID, $arLangsLID[$i]);
					if ($db_result_lang)
						CCurrencyLang::Update($ID, $arLangsLID[$i], $arFields);
					else
						CCurrencyLang::Add($arFields);
				}
				else
				{
					CCurrencyLang::Add($arFields);
				}
			}

			if(strlen($apply)<=0)
				LocalRedirect("/bitrix/admin/trigger_conditions.php?lang=". LANG);

			LocalRedirect("/bitrix/admin/condition_edit.php?ID=".$ID."&lang=".LANG);
		}
	}
	else
	{
		if ($e = $APPLICATION->GetException())
			$message = new CAdminMessage(GetMessage("action_error"), $e);
	}

}

if (strlen($ID) > 0)
	$APPLICATION->SetTitle(GetMessage("ACTION_EDIT_TITLE"));
else
	$APPLICATION->SetTitle(GetMessage("ACTION_NEW_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aContext = array(
	array(
		"ICON" => "btn_list",
		"TEXT"=>GetMessage("MAIN_ADMIN_MENU_LIST"),
		"LINK"=>"trigger_actions.php?lang=".LANG,
		"TITLE"=>GetMessage("MAIN_ADMIN_MENU_LIST")
	),
);

if (strlen($ID) > 0)
{
	$aContext[] = 	array(
		"ICON" => "btn_new",
		"TEXT"=>GetMessage("MAIN_ADMIN_MENU_CREATE"),
		"LINK"=>"condition_edit.php?lang=".LANG,
		"TITLE"=>GetMessage("MAIN_ADMIN_MENU_CREATE")
	);

	if ($CURRENCY_RIGHT=="W")
	{
		$aContext[] = 	array(
			"ICON" => "btn_delete",
			"TEXT"=>GetMessage("MAIN_ADMIN_MENU_DELETE"),
			"ONCLICK"	=> "javascript:if(confirm('".GetMessage("CONFIRM_DEL_MESSAGE")."'))window.location='trigger_condition.php?action=delete&ID[]=".CUtil::JSEscape($ID)."&lang=".LANG."&".bitrix_sessid_get()."';",
		);
	}
}
$context = new CAdminContextMenu($aContext);
$context->Show();

//Defaults
$f_SORT = "10";

if (strlen($ID) > 0 && !$bVarsFromForm)
{
	$result = CFTriggerConditions::GetByID($ID);
	$f_NAME = $result["NAME"];
	$f_SORT = $result["SORT"] ;

/*	$res = CCurrencyLang::GetList($by, $order, $ID);
	while ($ar = $res->Fetch())
	{
		${"l_FULL_NAME_".$ar["LID"]} = $ar["FULL_NAME"];
		${"l_FORMAT_STRING_".$ar["LID"]} = $ar["FORMAT_STRING"];
		${"l_DEC_POINT_".$ar["LID"]} = $ar["DEC_POINT"];
		${"l_THOUSANDS_SEP_".$ar["LID"]} = $ar["THOUSANDS_SEP"];
		${"l_THOUSANDS_VARIANT_".$ar["LID"]} = $ar["THOUSANDS_VARIANT"];
		${"l_DECIMALS_".$ar["LID"]} = $ar["DECIMALS"];
	}*/

}

if($bVarsFromForm)
{
	$DB->InitTableVarsForEdit("f_conditions", "", "f_");

	/*for ($i=0; $i<$iCount; $i++)
	{
		${"l_FULL_NAME_".$arLangsLID[$i]} = ${"FULL_NAME_".$arLangsLID[$i]};
		${"l_FORMAT_STRING_".$arLangsLID[$i]} = ${"FORMAT_STRING_".$arLangsLID[$i]};
		${"l_DEC_POINT_".$arLangsLID[$i]} = ${"DEC_POINT_".$arLangsLID[$i]};
		${"l_THOUSANDS_SEP_".$arLangsLID[$i]} = ${"THOUSANDS_SEP_".$arLangsLID[$i]};
		${"l_THOUSANDS_VARIANT_".$arLangsLID[$i]} = ${"THOUSANDS_VARIANT_".$arLangsLID[$i]};
		${"l_DECIMALS_".$arLangsLID[$i]} = ${"DECIMALS_".$arLangsLID[$i]};
	}*/
}

if($message)
	echo $message->Show();
?>
<form method="post" action="<?$APPLICATION->GetCurPage()?>" name="form1">
<? echo bitrix_sessid_post(); ?>
<?echo GetFilterHiddens("filter_");?>
<input type="hidden" name="ID" value="<?echo $ID?>">
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="from" value="<?echo htmlspecialcharsbx($from)?>">
<?if(strlen($return_url)>0):?><input type="hidden" name="return_url" value="<?=htmlspecialcharsbx($return_url)?>"><?endif?>

<?$tabControl->Begin();?>
<?$tabControl->BeginNextTab();?>

	<tr class="adm-detail-required-field">
		<td width="40%"><?echo GetMessage("action")?>:</td>
		<td width="60%">
		<input type="text" value="<?echo htmlspecialcharsbx($f_NAME)?>" size="20" name="NAME" maxlength="20">
		</td>
	</tr>

	<tr>
		<td><?echo GetMessage("action_sort_ex")?>:</td>
		<td>
			<input type="text" class="typeinput" size="10" name="SORT" value="<?echo intval($f_SORT)?>" maxlength="10">
		</td>
	</tr>	

<?$tabControl->EndTab();?>
<?$tabControl->Buttons(Array("disabled" => $CURRENCY_RIGHT<"W", "back_url" =>"/bitrix/admin/trigger_conditions.php?lang=".LANGUAGE_ID));?>
<?$tabControl->End();?>
</form>
<?$tabControl->ShowWarnings("form1", $message);
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>