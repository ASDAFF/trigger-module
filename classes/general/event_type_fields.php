<?class CAllFTriggerEventTypeFields{

	function GetByID($action)
	{
		global $DB;

		$strSql =
			"SELECT * ".
			"FROM f_event_type_fields ".
			"WHERE ID = '".$DB->ForSQL($action, 3)."'";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
			return $res;

		return false;
	}

	function GetList(&$by, &$order, $filter = array(), $lang = LANGUAGE_ID)
	{
		global $DB;
		global $CACHE_MANAGER;

		$arFieldListsList = array();

		$dbFieldsList = CFTriggerEventTypeFields::__GetList($by, $order, $filter, $lang);
		while($arFieldList = $dbFieldsList->GetNext()){
			switch($arFieldList["FIELD_TYPE"]){
				case "GROUP_ID":
					$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), array("ACTIVE"=>"Y"));
					while($arGroup = $rsGroups->GetNext()){
						//$arFieldList["FieldStr"] .= "<option value='".$arGroup["ID"]."'>".$arGroup["NAME"]."</option>";
						$arGroups[ $arGroup["ID"] ] = $arGroup["NAME"];
					}
					$arFieldList["ADDITIONAL_PROPS"] = array(
						"NAME" => "GROUP_ID",
						"SIZE" => 5,
						"TYPE" => "SELECT",
						"CTYPE" => "MULTIPLE",
						"VALUES" => $arGroups
					);
					/*$arFieldList["FieldStr"] = "<select size='5' name='ADDITIONAL_PROPS[GROUP_ID][]' multiple='multiple'>";					
					$arFieldList["FieldStr"] .= "</select>";*/
				break;
				case "IBLOCK_ID":
					CModule::IncludeModule("iblock");
					//$arFieldList["FieldStr"] = "<select name='ADDITIONAL_PROPS[IBLOCK_ID]'>";
					$rsIblocks = CIBlock::GetList(Array("name"=>"asc"), Array('ACTIVE'=>'Y'), false);
					while($arIblock = $rsIblocks->Fetch()){						
						//$arFieldList["FieldStr"] .= "<option value='".$arIblock["ID"]."'>".$arIblock["NAME"]." (".$arIblock["LID"].")</option>";
						$arIblocks[ $arIblock["ID"] ] = $arIblock["NAME"];
					}
					//$arFieldList["FieldStr"] .= "</select>";
					$arFieldList["ADDITIONAL_PROPS"] = array(
						"NAME" => "IBLOCK_ID",						
						"TYPE" => "SELECT",
						"CTYPE" => "SINGLE",
						"VALUES" => $arIblocks
					);
				break;
				case "FORM_FIELD":
					//$arFieldList["FieldStr"] = "<select size='5' name='ADDITIONAL_PROPS[FORM_FIELD][]' multiple='multiple'>";
					$rsUserEntities = CUserTypeEntity::GetList(array($by=>$order), array("ENTITY_ID" => "USER"));
					while($arUserEntity = $rsUserEntities->Fetch()){						
						//$arFieldList["FieldStr"] .= "<option value='".$arUserEntity["ID"]."'>".$arUserEntity["FIELD_NAME"]."</option>";
						$arUserEntities[ $arUserEntity["ID"] ] = $arUserEntity["FIELD_NAME"];
					}
					//$arFieldList["FieldStr"] .= "</select>";
					$arFieldList["ADDITIONAL_PROPS"] = array(
						"NAME" => "FORM_FIELD",	
						"SIZE" => 5,					
						"TYPE" => "SELECT",
						"CTYPE" => "MULTIPLE",
						"VALUES" => $arUserEntities
					);
				break;
			}
			$arFieldListsList[] = $arFieldList;
		}

		return $arFieldListsList;		
	}
}?>