<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/include.php");

$CURRENCY_RIGHT = $APPLICATION->GetGroupRight("fevent");
if ($CURRENCY_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));


__IncludeLang(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/admin/", "/trigger_list.php"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/prolog.php");

$sTableID = "f_triggers";
$oSort = new CAdminSorting($sTableID, "sort", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

if ($lAdmin->EditAction() && $CURRENCY_RIGHT=="W")
{
	foreach($FIELDS as $ID => $arFields)
	{
		$ID = substr($ID,0,3);

		if(!$lAdmin->IsUpdated($ID))
			continue;

		if (!CFTrigger::Update($ID, $arFields))
		{
			if ($ex = $APPLICATION->GetException())
			{
				$lAdmin->AddUpdateError(GetMessage("ACTION_SAVE_ERR", array("#ID#" => $ID, "#ERROR_TEXT#" => $ex->GetString())), $ID);
			}
			else
			{
				$lAdmin->AddUpdateError(GetMessage("ACTION_SAVE_ERR2", array("#ID#"=>$ID)), $ID);
			}
		}
	}
}

if($CURRENCY_RIGHT=="W" && $arID = $lAdmin->GroupAction())
{
	if($_REQUEST['action_target']=='selected')
	{
		$rsData = CFTrigger::GetList($by, $order);
		while($arRes = $rsData->Fetch())
			$arID[] = $arRes['ID'];
	}

	foreach($arID as $ID)
	{
		if(strlen($ID)<=0)
			continue;

		switch($_REQUEST['action'])
		{
		case "delete":
			if ($CURRENCY_RIGHT=="W")
				if (!CFTrigger::Delete($ID))
				{
					if ($ex = $APPLICATION->GetException())
						$lAdmin->AddGroupError($ex->GetString(), $ID);
					else
						$lAdmin->AddGroupError(GetMessage("action_err1"), $ID);
				}
		break;

		}
	}
}

$rsData = CFTrigger::GetList($by, $order);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("ACTION_TITLE")));

$arHeaders = array();
$arHeaders[] = array("id"=>"ID", "content"=>GetMessage('action'), "sort"=>"ID", "default"=>true);
$arHeaders[] = array("id"=>"NAME", "content"=>GetMessage('ACTION_NAME'), "sort"=>"name", "default"=>true);
$arHeaders[] = array("id"=>"SORT", "content"=>GetMessage('action_sort'), "sort" => "sort", "default"=>true);

$lAdmin->AddHeaders($arHeaders);

while($arRes = $rsData->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arRes, "/bitrix/admin/trigger_edit.php?ID=".$f_ID."&lang=".LANGUAGE_ID, GetMessage('ACTION_A_EDIT'));

	//$row->AddViewField("CURRENCY", '<a href="/bitrix/admin/action_edit.php?ID='.$f_CURRENCY.'&lang='.LANGUAGE_ID.'" title="'.GetMessage('ACTION_A_EDIT_TITLE').'">'.$f_CURRENCY.'</a>');
	$row->AddInputField("SORT", array("size"=>"3"));
	$row->AddInputField("NAME", $f_NAME);

	$arActions = Array();

	$arActions[] = array(
		"ICON"=>"edit",
		"DEFAULT" => "Y",
		"TEXT"=>GetMessage("MAIN_ADMIN_MENU_EDIT"),
		"ACTION"=>$lAdmin->ActionRedirect("/bitrix/admin/trigger_edit.php?ID=".$f_ID."&lang=".LANGUAGE_ID)
	);

	if ($CURRENCY_RIGHT=="W")
	{
		$arActions[] = array("SEPARATOR"=>true);

		$arActions[] = array(
			"ICON"=>"delete",
			"TEXT"=>GetMessage("MAIN_ADMIN_MENU_DELETE"),
			"ACTION"=>"if(confirm('".GetMessage('CONFIRM_DEL_MESSAGE')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));
	}

	$row->AddActions($arActions);
}


$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);


if ($CURRENCY_RIGHT=="W")
{
	$lAdmin->AddGroupActionTable(Array(
		"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
		)
	);
}

$aContext = array(
	array(
		"ICON" => "btn_new",
		"TEXT"=>GetMessage("action_add"),
		"LINK"=>"/bitrix/admin/trigger_edit.php?lang=".LANGUAGE_ID,
		"TITLE"=>GetMessage("action_add")
	),
	/*array(
		"ICON" => "",
		"TEXT"=>GetMessage("action_list"),
		"LINK"=>"/bitrix/admin/trigger_actions.php?lang=".LANGUAGE_ID,
		"TITLE"=>GetMessage("action_list")
	),*/
);

$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("ACTION_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$lAdmin->DisplayList();
//echo BeginNote();
//echo GetMessage("CURRENCY_BASE_CURRENCY");
//echo EndNote();

?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>