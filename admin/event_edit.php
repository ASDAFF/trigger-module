<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/include.php");

$CURRENCY_RIGHT = $APPLICATION->GetGroupRight("fevent");
if ($CURRENCY_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

__IncludeLang(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/admin/", "/trigger_events.php"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/prolog.php");

ClearVars("f_");

$message = null;
$bVarsFromForm = false;

$ID = htmlspecialcharsEx(trim($ID));
$ID = (strlen($ID) <= 0 ? false : $ID);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("action"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("action_settings")),
	array("DIV" => "edit2", "TAB" => GetMessage("conditions"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("action_settings")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if ($_SERVER["REQUEST_METHOD"] == "POST" && $CURRENCY_RIGHT=="W" && strlen($Update)>0 && check_bitrix_sessid())
{

	$obCond2 = new CCatalogCondTree();

	$boolCond = $obCond2->Init(BT_COND_MODE_PARSE, BT_COND_BUILD_CATALOG, array());
	if (!$boolCond)
	{
		if ($ex = $APPLICATION->GetException())
			$errorMessage .= $ex->GetString()."<br>";
		else
			$errorMessage .= (0 < $ID ? str_replace('#ID#', $ID, GetMessage('BT_CAT_DISCOUNT_EDIT_ERR_UPDATE')) : GetMessage('BT_CAT_DISCOUNT_EDIT_ERR_ADD'))."<br>";
		$bVarsFromForm = true;
		//echo "@";
	}
	else
	{
		$boolCond = false;
		if (isset($_POST['CONDITIONS']) && isset($_POST['CONDITIONS_CHECK']))
		{
			if (is_string($_POST['CONDITIONS']) && is_string($_POST['CONDITIONS_CHECK']) && md5($_POST['CONDITIONS']) == $_POST['CONDITIONS_CHECK'])
			{
				$CONDITIONS = base64_decode($_POST['CONDITIONS']);
				if (CheckSerializedData($CONDITIONS))
				{
					$CONDITIONS = unserialize($CONDITIONS);
					$boolCond = true;
				}
				else
				{
					$boolCondParseError = true;
				}
			}
		}

		if (!$boolCond){
			$CONDITIONS = $obCond2->Parse();
		}
		if (empty($CONDITIONS))
		{
			//echo "$";
			if ($ex = $APPLICATION->GetException())
				$errorMessage .= $ex->GetString()."<br>";
			else
				$errorMessage .= (0 < $ID ? str_replace('#ID#', $ID, GetMessage('BT_CAT_DISCOUNT_EDIT_ERR_UPDATE')) : GetMessage('BT_CAT_DISCOUNT_EDIT_ERR_ADD'))."<br>";
			$bVarsFromForm = true;
			$boolCondParseError = true;
		}
	}
	//echo "<pre>";print_r($CONDITIONS);echo "</pre>";
	//die;

	$arFields = array(		
		"NAME" => $_POST['NAME'],
		"SORT" => $_POST['SORT'],
		"EVENT_TYPE" => $_POST["EVENT_TYPE"],
		"ADDITIONAL_PROPS" => serialize($_POST["ADDITIONAL_PROPS"]),
		"CONDITIONS" => serialize($CONDITIONS),
	);

	//echo "<pre>";print_r($arFields);echo "</pre>";
	//die;

	$strAction = ($ID ? 'UPDATE' : 'ADD');
	$bVarsFromForm = !CFTriggerEvents::CheckFields($strAction, $arFields, $ID);

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
			if (strlen($ID) > 0){				
				CFTriggerEvents::Update($ID, $arFields);				
			}else
				$ID = CFTriggerEvents::Add($arFields);

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
				LocalRedirect("/bitrix/admin/trigger_events.php?lang=". LANG);

			LocalRedirect("/bitrix/admin/fevent_event_edit.php?ID=".$ID."&lang=".LANG);
		}
	}
	else
	{
		if ($e = $APPLICATION->GetException())
			$message = new CAdminMessage(GetMessage("action_error"), $e);
	}

}

$rsEventTypes = CFTriggerEventTypes::GetList($by, $order);
while($arEventType = $rsEventTypes->GetNext()){
	$arEventTypes[ $arEventType["ID"] ] = $arEventType["NAME"];
}

if (strlen($ID) > 0)
	$APPLICATION->SetTitle(GetMessage("EVENT_EDIT_TITLE"));
else
	$APPLICATION->SetTitle(GetMessage("EVENT_NEW_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aContext = array(
	array(
		"ICON" => "btn_list",
		"TEXT"=>GetMessage("MAIN_ADMIN_MENU_LIST"),
		"LINK"=>"trigger_events.php?lang=".LANG,
		"TITLE"=>GetMessage("MAIN_ADMIN_MENU_LIST")
	),
);

if (strlen($ID) > 0)
{
	$aContext[] = 	array(
		"ICON" => "btn_new",
		"TEXT"=>GetMessage("MAIN_ADMIN_MENU_CREATE"),
		"LINK"=>"fevent_event_edit.php?lang=".LANG,
		"TITLE"=>GetMessage("MAIN_ADMIN_MENU_CREATE")
	);

	if ($CURRENCY_RIGHT=="W")
	{
		$aContext[] = 	array(
			"ICON" => "btn_delete",
			"TEXT"=>GetMessage("MAIN_ADMIN_MENU_DELETE"),
			"ONCLICK"	=> "javascript:if(confirm('".GetMessage("CONFIRM_DEL_MESSAGE")."'))window.location='trigger_events.php?action=delete&ID[]=".CUtil::JSEscape($ID)."&lang=".LANG."&".bitrix_sessid_get()."';",
		);
	}
}
$context = new CAdminContextMenu($aContext);
$context->Show();

//Defaults
$f_SORT = "10";

if (strlen($ID) > 0 && !$bVarsFromForm)
{
	$result = CFTriggerEvents::GetByID($ID);
	//echo "<Pre>";print_R($result);echo "</pre>";die;
	$f_NAME = $result["NAME"];
	$f_SORT = $result["SORT"] ;
	$f_EVENT_TYPE = $result["EVENT_TYPE"] ;
	$f_ADDITIONAL_PROPS = unserialize($result["ADDITIONAL_PROPS"]);
	$f_CONDITIONS = unserialize($result["CONDITIONS"]);
	$arDiscount['CONDITIONS'] = $result["CONDITIONS"];
	//echo "<pre>";print_r($f_CONDITIONS);echo "</pre>";
}

if(intval($_REQUEST["EVENT_TYPE"])){
	$EVENT_TYPE = $_REQUEST["EVENT_TYPE"];	
}elseif(intval($f_EVENT_TYPE)){
	$EVENT_TYPE = $f_EVENT_TYPE;
}elseif(!empty($arEventTypes) && intval(key($arEventTypes))){
	$EVENT_TYPE = key($arEventTypes);	
}
if($EVENT_TYPE){
	$TYPE = CFTriggerEventTypes::GetByID($EVENT_TYPE);
	if(intval($TYPE["ID"]) > 0)
		$arEventTypeFields = CFTriggerEventTypeFields::GetList($by, $order,array("TYPE"=>$TYPE["ID"]));
}

if($bVarsFromForm)
{
	$DB->InitTableVarsForEdit("f_events", "", "f_");
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
		<input type="text" value="<?echo htmlspecialcharsbx($f_NAME)?>" size="100" name="NAME" maxlength="100">
		</td>
	</tr>

	<tr class="adm-detail-required-field">
		<td width="40%"><?echo GetMessage("event_type")?>:</td>
		<td width="60%">
		<?if($EVENT_TYPE > 0):?>
		<select name="EVENT_TYPE_ID" onChange="window.location='fevent_event_edit.php?lang=<?=LANGUAGE_ID?><?=($ID > 0 ? "&ID=".$ID : "")?>&EVENT_TYPE='+this[this.selectedIndex].value">
			<?foreach($arEventTypes as $key => $type):?>
			<option value="<?=$key?>"<?=($key == $EVENT_TYPE ? " selected" : "")?>><?=$type?></option>
			<?endforeach;?>
		</select>
		<input type="hidden" name="EVENT_TYPE" value="<? echo $EVENT_TYPE?>">
		<?elseif($ID > 0):?>		
		<select name="EVENT_TYPE_ID" onChange="window.location='fevent_event_edit.php?lang=<?=LANGUAGE_ID?><?=($ID > 0 ? "&ID=".$ID : "")?>&EVENT_TYPE='+this[this.selectedIndex].value">
			<?foreach($arEventTypes as $key => $type):?>
			<option value="<?=$key?>"<?=($f_EVENT_TYPE > 0 && $key == $f_EVENT_TYPE ? " selected" : "")?>><?=$type?></option>
			<?endforeach;?>
		</select>
		<input type="hidden" name="EVENT_TYPE" value="<? echo $f_EVENT_TYPE?>">
		<?else:?>
		<select name="EVENT_TYPE_ID" onChange="window.location='fevent_event_edit.php?lang=<?=LANGUAGE_ID?><?=($ID > 0 ? "&ID=".$ID : "")?>&EVENT_TYPE='+this[this.selectedIndex].value">
			<?foreach($arEventTypes as $key => $type):?>
			<option value="<?=$key?>"<?=($f_EVENT_TYPE > 0 && $key == $f_EVENT_TYPE ? " selected" : "")?>><?=$type?></option>
			<?endforeach;?>
		</select>
		<input type="hidden" name="EVENT_TYPE" value="">
		<?endif;?>
		</td>
	</tr>

	<?/*foreach($arEventTypeFields as $field):?>
	<tr>
	<td width="40%"><?=$field["NAME"]?>:</td>
	<td width="60%">
 	<?
	switch($field["ADDITIONAL_PROPS"]["TYPE"]){
		case "SELECT":
		?><select name="ADDITIONAL_PROPS[<?=$field["ADDITIONAL_PROPS"]["NAME"]?>]<?=($field["ADDITIONAL_PROPS"]["CTYPE"] == "MULTIPLE" ? "[]" : "")?>"<?=($field["ADDITIONAL_PROPS"]["CTYPE"] == "MULTIPLE" ? ' SIZE="'.$field["ADDITIONAL_PROPS"]["SIZE"].'" multiple="multiple"' : "")?>>
			<?foreach($field["ADDITIONAL_PROPS"]["VALUES"] as $key => $value):?>
			<option value="<?=$key?>"<?=(in_array($key,$f_ADDITIONAL_PROPS[ $field["ADDITIONAL_PROPS"]["NAME"] ]) ? " selected" : "")?>><?=$value?></option>
			<?endforeach;?>
		</select><?
		break;
	}
	?>
	</td>	
	</tr>
	<?endforeach;*/?>

	<tr>
		<td><?echo GetMessage("action_sort_ex")?>:</td>
		<td>
			<input type="text" class="typeinput" size="10" name="SORT" value="<?echo intval($f_SORT)?>" maxlength="10">
		</td>
	</tr>
<?$tabControl->EndTab();?>
<?$tabControl->BeginNextTab();?>
	<tr id="tr_CONDITIONS">
		<td valign="top" colspan="2"><div id="tree" style="position: relative; z-index: 1;"></div><?			
			if (!is_array($arDiscount['CONDITIONS']))
			{
				if (CheckSerializedData($arDiscount['CONDITIONS']))
				{
					$arDiscount['CONDITIONS'] = unserialize($arDiscount['CONDITIONS']);
				}
				else
				{
					$arDiscount['CONDITIONS'] = '';
				}
			}
			$obCond = new CCatalogCondTree();
			$boolCond = $obCond->Init(BT_COND_MODE_DEFAULT, BT_COND_BUILD_CATALOG, array('FORM_NAME' => 'form1', 'CONT_ID' => 'tree', 'JS_NAME' => 'JSCatCond'));
			if (!$boolCond)
			{
				if ($ex = $APPLICATION->GetException())
				echo $ex->GetString()."<br>";
			}
			else
			{
				$obCond->Show($arDiscount['CONDITIONS']);
			}
		?></td>
	</tr>
<?$tabControl->EndTab();?>
<?$tabControl->Buttons(Array("disabled" => $CURRENCY_RIGHT<"W", "back_url" =>"/bitrix/admin/trigger_events.php?lang=".LANGUAGE_ID));?>
<?$tabControl->End();?>
</form>
<?$tabControl->ShowWarnings("form1", $message);
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>