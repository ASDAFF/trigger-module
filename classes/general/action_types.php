<?class CAllFTriggerActionTypes{
	function GetByID($action)
	{
		global $DB;

		$strSql =
			"SELECT * ".
			"FROM f_action_types ".
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

		$dbActionTypeList = CFTriggerActionTypes::__GetList($by, $order, $lang);

		return $dbActionTypeList;
	}
}?>