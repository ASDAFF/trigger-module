<?
IncludeModuleLangFile(__FILE__);

$ADV_RIGHT = $APPLICATION->GetGroupRight("fevent");
if($ADV_RIGHT!="D")
{
	$aMenu = array(
		"parent_menu" => "global_menu_triggers",
		"section" => "fevent",
		"sort" => 200,
		"text" => GetMessage("AD_MENU_MAIN"),
		"title" => GetMessage("AD_MENU_MAIN_TITLE"),
		"icon" => "advertising_menu_icon",
		"page_icon" => "advertising_page_icon",
		"items_id" => "menu_triggers",
		"items" => array(
			array(
				"text" => GetMessage("AD_MENU_EVENTS_LIST"),
				"url" => "trigger_events.php?lang=".LANGUAGE_ID,
				"more_url" => array(
					"fevent_event_edit.php"
				),
				"title" => GetMessage("AD_MENU_BANNER_LIST_ALT")
			),
			array(
				"text" => GetMessage("AD_MENU_ACTIONS_LIST"),
				"url" => "trigger_actions.php?lang=".LANGUAGE_ID,
				"more_url" => array(
					"action_edit.php"
				),
				"title" => GetMessage("AD_MENU_ACTIONS_LIST_ALT")
			),
			array(
				"text" => GetMessage("AD_MENU_TRIGGER_LIST"),
				"url" => "trigger_list.php?lang=".LANGUAGE_ID,
				"more_url" => array(
					"trigger_edit.php"
				),
				"title" => GetMessage("AD_MENU_TRIGGER_LIST_ALT")
			),
			array(
				"text" => GetMessage("AD_MENU_TRIGGER_LOG_LIST"),
				"url" => "trigger_log_list.php?lang=".LANGUAGE_ID,
				"more_url" => array(),
				"title" => GetMessage("AD_MENU_TRIGGER_LOG_LIST_ALT")
			),
		)
	);

	return $aMenu;
}
return false;
?>
