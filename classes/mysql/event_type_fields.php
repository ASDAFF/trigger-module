<?
require_once(str_replace("/mysql/", "/general/", __FILE__));

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