<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/include.php");

$CURRENCY_RIGHT = $APPLICATION->GetGroupRight("fevent");
if ($CURRENCY_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

__IncludeLang(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/admin/", "/trigger_actions.php"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/prolog.php");

ClearVars("f_");

$message = null;
$bVarsFromForm = false;

$ID = htmlspecialcharsEx(trim($ID));
$ID = (strlen($ID) <= 0 ? false : $ID);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("action"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("action_settings")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if ($_SERVER["REQUEST_METHOD"] == "POST" && $CURRENCY_RIGHT=="W" && strlen($Update)>0 && check_bitrix_sessid())
{

	$arFields = array(
		"NAME" => $_POST['NAME'],
		"SORT" => $_POST['SORT'],
		"ACTION_TYPE" => $_POST["ACTION_TYPE"],
		"ADDITIONAL_PROPS" => serialize($_POST["ADDITIONAL_PROPS"]),
		"BODY_PARAMS" => serialize(explode("\n", $_POST["BODY_PARAMS"]))
	);
	
	//echo "<pre>";print_r($_POST);echo "</pre>";
	//echo "<pre>";print_r($arFields);echo "</pre>";die;
	
	$strAction = ($ID ? 'UPDATE' : 'ADD');
	$bVarsFromForm = !CFTriggerActions::CheckFields($strAction, $arFields, $ID);

	if (!$bVarsFromForm)
	{
		$arMsg = array();

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
				CFTriggerActions::Update($ID, $arFields);
			else
				$ID = CFTriggerActions::Add($arFields);

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
				LocalRedirect("/bitrix/admin/trigger_actions.php?lang=". LANG);

			LocalRedirect("/bitrix/admin/action_edit.php?ID=".$ID."&lang=".LANG);
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
		"LINK"=>"action_edit.php?lang=".LANG,
		"TITLE"=>GetMessage("MAIN_ADMIN_MENU_CREATE")
	);

	if ($CURRENCY_RIGHT=="W")
	{
		$aContext[] = 	array(
			"ICON" => "btn_delete",
			"TEXT"=>GetMessage("MAIN_ADMIN_MENU_DELETE"),
			"ONCLICK"	=> "javascript:if(confirm('".GetMessage("CONFIRM_DEL_MESSAGE")."'))window.location='trigger_actions.php?action=delete&ID[]=".CUtil::JSEscape($ID)."&lang=".LANG."&".bitrix_sessid_get()."';",
		);
	}
}
$context = new CAdminContextMenu($aContext);
$context->Show();

//Defaults
$f_SORT = "10";

$rsActionTypes = CFTriggerActionTypes::GetList();
while($arActionType = $rsActionTypes->Fetch()){
	$arActionTypes[] = $arActionType;
}

//echo "<pre>";print_r($arActionTypes);echo "</pre>";die;

if (strlen($ID) > 0 && !$bVarsFromForm)
{
	$result = CFTriggerActions::GetByID($ID);
	$f_NAME = $result["NAME"];
	$f_SORT = $result["SORT"] ;	
	$f_ACTION_TYPE = $result["ACTION_TYPE"];
	$f_ADDITIONAL_PROPS = unserialize($result["ADDITIONAL_PROPS"]);
	//$_BODY_PARAMS = unserialize(implode("\n", $result["BODY_PARAMS"]));
	$_BODY_PARAMS = unserialize($result["BODY_PARAMS"]);
}

//echo "@".$f_ACTION_TYPE."@";

if(intval($_REQUEST["ACTION_TYPE"])){
	$ACTION_TYPE = $_REQUEST["ACTION_TYPE"];
	$TYPE = CFTriggerActionTypes::GetByID($ACTION_TYPE);
	//$TYPE = GetDetail($ACTION_TYPE,intval($_REQUEST["ACTION_TYPE"]));
	if(strlen($TYPE["CODE"]) > 0){
		$arActionTypeFields = CFTriggerActionTypeFields::GetByID($TYPE["CODE"]);
		if(intval($_REQUEST["ADDITIONAL_PROPS"][0])){
			$strAdditionalActionTypeFields = CFTriggerActionTypeFields::GetAdditionalFields(intval($_REQUEST["ADDITIONAL_PROPS"][0]));
		}		
	}
}elseif(strlen($f_ACTION_TYPE)){
	$ACTION_TYPE = $f_ACTION_TYPE;
	$TYPE = CFTriggerActionTypes::GetByID($ACTION_TYPE);
	//echo "<pre>";print_r($TYPE);echo "</pre>";
	if(strlen($TYPE["CODE"]) > 0){
		$arActionTypeFields = CFTriggerActionTypeFields::GetByID($TYPE["CODE"]);
	}
}elseif(!empty($arActionTypes) && intval(key($arActionTypes))){
	$ACTION_TYPE = key($arActionTypes);
	$TYPE = CFTriggerActionTypes::GetByID($ACTION_TYPE);
	if(strlen($TYPE["CODE"]) > 0)
		$arActionTypeFields = CFTriggerActionTypeFields::GetByID($TYPE["CODE"]);
}

//echo "<pre>";print_r($arActionTypeFields);echo "</pre>";

if($bVarsFromForm)
{
	$DB->InitTableVarsForEdit("f_actions", "", "f_");
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
	<td width="40%">Тип действий:</td>
	<td width="60%">
	<?if($ACTION_TYPE > 0):?>
	<select name="ACTION_TYPE_ID" onChange="window.location='action_edit.php?lang=<?=LANGUAGE_ID?><?=($ID > 0 ? "&ID=".$ID : "")?>&ACTION_TYPE='+this[this.selectedIndex].value">
		<?foreach($arActionTypes as $field):?>
		<option value="<?=$field["ID"]?>"<?=($field["ID"] == $ACTION_TYPE ? " selected" : "")?>><?=$field["NAME"]?></option>
		<?endforeach;?>	
	</select>
	<input type="hidden" name="ACTION_TYPE" value="<? echo $ACTION_TYPE?>">
	<?elseif($ID > 0):?>		
	<select name="ACTION_TYPE_ID" onChange="window.location='action_edit.php?lang=<?=LANGUAGE_ID?><?=($ID > 0 ? "&ID=".$ID : "")?>&ACTION_TYPE='+this[this.selectedIndex].value">
		<?foreach($arActionTypes as $field):?>
		<option value="<?=$field["ID"]?>"<?=($f_ACTION_TYPE > 0 && $field["ID"] == $f_ACTION_TYPE ? " selected" : "")?>><?=$field["NAME"]?></option>
		<?endforeach;?>
	</select>
	<input type="hidden" name="ACTION_TYPE" value="<? echo $f_ACTION_TYPE?>">
	<?else:?>
	<select name="ACTION_TYPE_ID" onChange="window.location='action_edit.php?lang=<?=LANGUAGE_ID?><?=($ID > 0 ? "&ID=".$ID : "")?>&ACTION_TYPE='+this[this.selectedIndex].value">
		<?foreach($arActionTypes as $field):?>
		<option value="<?=$field["ID"]?>"<?=($f_ACTION_TYPE > 0 && $field["ID"] == $f_ACTION_TYPE ? " selected" : "")?>><?=$field["NAME"]?></option>
		<?endforeach;?>
	</select>
	<input type="hidden" name="ACTION_TYPE" value="">
	<?endif;?>
	</td>
	</tr>	
	<tr>
	<td width="40%"><?echo GetMessage($arActionTypeFields["ID"])?>:</td>
	<td width="60%">
 	<?
	switch($arActionTypeFields["TYPE"]){
		case "SELECT":		
		?>
		<select name="ADDITIONAL_PROPS[<?=$arActionTypeFields["NAME"]?>]<?=($arActionTypeFields["CTYPE"] == "MULTIPLE" ? "[]" : "")?>"<?=($arActionTypeFields["CTYPE"] == "MULTIPLE" ? ' SIZE="'.$arActionTypeFields["SIZE"].'" multiple="multiple"' : "")?>
		onChange="window.location='action_edit.php?lang=<?=LANGUAGE_ID?><?=($ID > 0 ? "&ID=".$ID : "")?>&ACTION_TYPE=<? echo $ACTION_TYPE?>&ADDITIONAL_PROPS[<?=$arActionTypeFields["NAME"]?>]<?=($arActionTypeFields["CTYPE"] == "MULTIPLE" ? "[]" : "")?>='+this[this.selectedIndex].value">
			<?foreach($arActionTypeFields["VALUES"] as $key => $value):?>
			<option value="<?=$value["ID"]?>"<?=(in_array($value["ID"],$f_ADDITIONAL_PROPS) ? " selected" : "")?>
			<?=($_REQUEST["ADDITIONAL_PROPS"][0] > 0 && $_REQUEST["ADDITIONAL_PROPS"][0] == $value["ID"] ? " selected" : "")?>><?=$value["NAME"]?></option>
			<?endforeach;?>
		</select><?
		break;
		case "INPUT":
		?><input name="ADDITIONAL_PROPS[<?=$arActionTypeFields["NAME"]?>]" value="<?=$f_ADDITIONAL_PROPS[0]?>" /><?
		break;
	}
	if(isset($_REQUEST["ADDITIONAL_PROPS"])){
		if(isset($strAdditionalActionTypeFields) && strlen($strAdditionalActionTypeFields)){
		?>
		<tr>
		<td width="40%"><?echo GetMessage("VARIABLES")?>:</td>
		<td width="60%">
			<textarea name="BODY_PARAMS" rows="10" cols="50"><?=str_replace(",", " \n",$strAdditionalActionTypeFields )." "?></textarea>
		</td>
		</tr>
		<?
		}
	}elseif($TYPE["CODE"] == "SEND_MAIL"){?>
		<tr>
		<td width="40%"><?echo GetMessage("VARIABLES")?>:</td>
		<td width="60%">
			<textarea name="BODY_PARAMS" rows="10" cols="50"><?=implode("\n",$_BODY_PARAMS)?></textarea>
		</td>
		</tr>
	<?
	}
	?>
	</td>	
	</tr>	

	<tr>
		<td><?echo GetMessage("action_sort_ex")?>:</td>
		<td>
			<input type="text" class="typeinput" size="10" name="SORT" value="<?echo intval($f_SORT)?>" maxlength="10">
		</td>
	</tr>

<?$tabControl->EndTab();?>
<?$tabControl->Buttons(Array("disabled" => $CURRENCY_RIGHT<"W", "back_url" =>"/bitrix/admin/trigger_actions.php?lang=".LANGUAGE_ID));?>
<?$tabControl->End();?>
</form>
<?$tabControl->ShowWarnings("form1", $message);
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>