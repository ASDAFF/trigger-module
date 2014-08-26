<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/classes/general/ftrigger_events.php");

class CFTriggerEvents extends CAllFTriggerEvents{
	//TODO: 
	function __GetList(&$by, &$order, $lang = LANGUAGE_ID)
	{
		global $DB;

		$strSql =
			"SELECT * ".
			"FROM f_events ";

		if (strtolower($by) == "name") $strSqlOrder = " ORDER BY NAME ";
		else
		{
			$strSqlOrder = " ORDER BY SORT ";
			$by = "sort";
		}

		if ($order=="desc")
			$strSqlOrder .= " desc ";
		else
			$order = "asc";

		$strSql .= $strSqlOrder;
		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return $res;
	}
}

class CFTriggerEventTypes extends CAllFTriggerEventTypes{
	//TODO: 
	function __GetList(&$by, &$order, $lang = LANGUAGE_ID)
	{
		global $DB;

		$strSql =
			"SELECT * ".
			"FROM f_event_types ";

		if (strtolower($by) == "name") $strSqlOrder = " ORDER BY NAME ";
		else
		{
			$strSqlOrder = " ORDER BY SORT ";
			$by = "sort";
		}

		if ($order=="desc")
			$strSqlOrder .= " desc ";
		else
			$order = "asc";

		$strSql .= $strSqlOrder;
		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return $res;
	}
}

class CFTriggerEventTypeFields extends CAllFTriggerEventTypeFields{
	//TODO: 
	function __GetList(&$by, &$order, $filter = array(), $lang = LANGUAGE_ID)
	{
		global $DB;

		$strSql =
			"SELECT * ".
			"FROM f_event_type_fields ";

		if(!empty($filter)){
			$strWhere = "WHERE ";
		}else{
			$strWhere = "";
		}

		foreach($filter as $key => $f){
			$strWhere .= $key . " = " .$f ." ";
		}		

		$strSql .= $strWhere;

		if (strtolower($by) == "name") $strSqlOrder = " ORDER BY NAME ";
		else
		{
			$strSqlOrder = " ORDER BY SORT ";
			$by = "sort";
		}

		if ($order=="desc")
			$strSqlOrder .= " desc ";
		else
			$order = "asc";

		$strSql .= $strSqlOrder;
		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return $res;
	}
}
?>
