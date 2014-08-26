<?class CAllFTriggerActionTypeFields{

	function GetByID($action){
		$arFieldList = array();
		switch($action){
			case "BAGE":
				$arFieldList = array(
					"ID" => "BAGE",
					"SIZE" => 5,
					"TYPE" => "SELECT",
					"CTYPE" => "SINGLE",
					"VALUES" => array()
				);
				$rsBages = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"badges","ACTIVE"=>"Y"),false,false,array("ID","NAME"));
				while($arBage = $rsBages->GetNext()){					
					$arFieldList["VALUES"][] = array(
						"ID" => $arBage["ID"],
						"NAME" => $arBage["NAME"]
					);
					//$arFieldList["VALUES"][] = $arBage;
				}
				break;
			case "ADD_POINTS":
				$arFieldList = array(
					"ID" => "ADD_POINTS",
					"TYPE" => "INPUT"
				);
				break;
			case "SEND_MAIL":
				$arFieldList = array(
					"ID" => "SEND_MAIL",
					"SIZE" => 5,
					"TYPE" => "SELECT",
					"CTYPE" => "SINGLE",
					"VALUES" => array()					
				);
				$rsEmailTemplates = CEventMessage::GetList($by, $order, array("ACTIVE"=>"Y"));
				while($arEmailTemplate = $rsEmailTemplates->GetNext()){
					$arFieldList["VALUES"][] = array(
						"ID" => $arEmailTemplate["ID"],
						"NAME" => $arEmailTemplate["EVENT_TYPE"]
					);
					//$arFieldList["VALUES"][] = $arEmailTemplate;
				}
				break;				
		}
		return $arFieldList;
	}

	function GetAdditionalFields($id = 0){
		$arFieldList = array(
			"ID" => "SEND_MAIL",
			"SIZE" => 5,
			"TYPE" => "SELECT",
			"CTYPE" => "SINGLE",
			"VALUES" => array()					
		);
		$curEventMessage = array();
		$arFilter = array(
			"ACTIVE" => "Y"	
		);
		if($id > 0){
			$arFilter["ID"] = $id;
		}
		//echo "<pre>";print_r($arFilter);echo "</pre>";
		$rsEmailTemplates = CEventMessage::GetList($by, $order, $arFilter);
		if($arEmailTemplate = $rsEmailTemplates->GetNext()){
			//echo "<pre>";print_r($arEmailTemplate);echo "</pre>";
			$arFieldList["VALUES"][] = array(
				"ID" => $arEmailTemplate["ID"],
				"NAME" => $arEmailTemplate["EVENT_TYPE"]
			);
			$curEventMessage = array(
				"ID" => $arEmailTemplate["ID"],
				"TYPE_ID" => $arEmailTemplate["EVENT_NAME"]
			);			
		}
		if($id > 0 && !empty($curEventMessage)){
			$rsEmailEventType = CEventType::GetList(
				array(
					"TYPE_ID" => $curEventMessage["TYPE_ID"],
			    	"LID"     => "ru"
				)
			);
			if($arEmailEventType = $rsEmailEventType->Fetch()){
				//echo "<pre>";print_r($arEmailEventType);echo "</pre>";
				preg_match_all('/#([A-Za-z_]+)#/iu', $arEmailEventType["DESCRIPTION"], $out);
				//echo "<pre>";print_r($out);echo "</pre>";die;
				if(!empty($out[0])){
					return implode(",",$out[0]);
				}				
			}else{
				return "";
			}
			//return ($arEmailEventType = $rsEmailEventType->Fetch() ? $arEmailEventType["DESCRIPTION"] : "");			
		}else{
			return "";
		}
	}
}?>