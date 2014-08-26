<?
/*
##############################################
# Bitrix: SiteManager                        #
# Copyright (c) 2002-2009 Bitrix             #
# http://www.bitrix.ru                       #
# mailto:admin@bitrix.ru                     #
##############################################
*/
define("STOP_STATISTICS", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/prolog.php");

if(!($USER->CanDoOperation('view_subordinate_users') || $USER->CanDoOperation('view_all_users')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$arEvent = CFTriggerActions::GetByID($ID);
if($arEvent)
	$res = '[<a title="'.GetMessage("MAIN_EDIT_USER_PROFILE").'" class="tablebodylink" href="/bitrix/admin/action_edit.php?ID='.$arEvent["ID"].'&lang='.LANG.'">'.$arEvent["ID"].'</a>] ('.htmlspecialcharsbx($arEvent["NAME"]).')';
else
	$res = "&nbsp;".GetMessage("MAIN_NOT_FOUND");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");

$strName = preg_replace("/[^a-z0-9_\\[\\]:]/i", "", $_REQUEST["strName"]);
?>
<script type="text/javascript">
if(window.parent.document.getElementById("div_<?=$strName?>"))
	window.parent.document.getElementById("div_<?=$strName?>").innerHTML = '<?=CUtil::JSEscape($res)?>';
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>