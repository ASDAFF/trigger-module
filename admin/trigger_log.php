<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/include.php");

$CURRENCY_RIGHT = $APPLICATION->GetGroupRight("fevent");
if ($CURRENCY_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));


__IncludeLang(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/admin/", "/trigger_actions.php"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/prolog.php");

$sTableID = "f_triggers_log";
$oSort = new CAdminSorting($sTableID, "sort", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$rsData = CFTriggerLog::GetList($by, $order);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

function GetUserNameByID($USER_ID){
	if(intval($USER_ID)){
		$rsUser = CUser::GetByID($USER_ID);
		if($arUser = $rsUser->Fetch()){
			return (strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0 ? $arUser["NAME"]." ".$arUser["LAST_NAME"] : $arUser["LOGIN"]);
		}else{
			return "";
		}
	}else{
		return "";
	}
}

function GetTriggerNameByID($TRIGGER_ID){
	if(intval($TRIGGER_ID)){
		$arTrigger = CFTrigger::GetByID($TRIGGER_ID);
		return $arTrigger["NAME"];
	}else{
		return "";
	}
}

//CUserOptions::SetOptionsFromArray($arOptions);

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("ACTION_TITLE")));

$arHeaders = array();
$arHeaders[] = array("id"=>"ID", "content"=>GetMessage('action'), "sort"=>"ID", "default"=>true);
$arHeaders[] = array("id"=>"NAME", "content"=>GetMessage('ACTION_NAME'), "sort"=>"name", "default"=>true);
$arHeaders[] = array(
	"id" => "DATE_CREATE",
	"content" => GetMessage("DATE_CREATE"),	
	"sort" => "created",
	"default" => false,
);
$arHeaders[] = array(
	"id" => "CREATED_BY",
	"content" => GetMessage("CREATED_USER_NAME"),	
	"sort" => "created_by",
	"default" => false,
);
$arHeaders[] = array(
	"id" => "TRIGGER",
	"content" => GetMessage("TRIGGER"),	
	"sort" => "created_by",
	"default" => false,
);

$lAdmin->AddHeaders($arHeaders);

$arSelectedFields = $lAdmin->GetVisibleHeaderColumns();

while($arRes = $rsData->NavNext(true, "f_"))
{
	//echo "<pre>";print_r($arRes);echo "</pre>";
	//$row =& $lAdmin->AddRow($f_ID, $arRes, "/bitrix/admin/_edit.php?ID=".$f_ID."&lang=".LANGUAGE_ID, GetMessage('ACTION_A_EDIT'));
	$row =& $lAdmin->AddRow($f_ID, $arRes);

	//$row->AddViewField("CURRENCY", '<a href="/bitrix/admin/action_edit.php?ID='.$f_CURRENCY.'&lang='.LANGUAGE_ID.'" title="'.GetMessage('ACTION_A_EDIT_TITLE').'">'.$f_CURRENCY.'</a>');
	//$row->AddInputField("SORT", array("size"=>"3"));
	$row->AddInputField("NAME", $f_NAME);
	$row->AddViewField("CREATED_BY", '<a href="user_edit.php?lang='.LANGUAGE_ID.'&ID='.$f_CREATED_BY.'">'.GetUserNameByID($f_CREATED_BY).'</a>');
	$row->AddViewField("TRIGGER", '<a href="trigger_edit.php?lang='.LANGUAGE_ID.'&ID='.$f_TRIGGER_ID.'">'.GetTriggerNameByID($f_TRIGGER_ID).'</a>');
	//$row->AddInputField("SORT", array("size"=>"3"));
	//$row->AddViewField("DATE_CREATE", $f_DATE_CREATE);
	$row->AddCalendarField("DATE_CREATE");

	/*$arActions = Array();

	$arActions[] = array(
		"ICON"=>"edit",
		"DEFAULT" => "Y",
		"TEXT"=>GetMessage("MAIN_ADMIN_MENU_EDIT"),
		"ACTION"=>$lAdmin->ActionRedirect("/bitrix/admin/action_edit.php?ID=".$f_ID."&lang=".LANGUAGE_ID)
	);

	if ($CURRENCY_RIGHT=="W")
	{
		$arActions[] = array("SEPARATOR"=>true);

		$arActions[] = array(
			"ICON"=>"delete",
			"TEXT"=>GetMessage("MAIN_ADMIN_MENU_DELETE"),
			//"ACTION"=>"if(confirm('".GetMessage('CONFIRM_DEL_MESSAGE')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
		);
	}*/

	//$row->AddActions($arActions);
}
//die;


$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);


/*if ($CURRENCY_RIGHT=="W")
{
	$lAdmin->AddGroupActionTable(Array(
		"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
		)
	);
}*/

$aContext = array(
	/*array(
		"ICON" => "btn_new",
		"TEXT"=>GetMessage("action_add"),
		"LINK"=>"/bitrix/admin/action_edit.php?lang=".LANGUAGE_ID,
		"TITLE"=>GetMessage("action_add")
	),*/
	/*array(
		"ICON" => "",
		"TEXT"=>GetMessage("action_list"),
		"LINK"=>"/bitrix/admin/trigger_actions.php?lang=".LANGUAGE_ID,
		"TITLE"=>GetMessage("action_list")
	)*/
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