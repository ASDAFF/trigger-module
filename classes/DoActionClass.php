<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/classes/CompareClass.php");

class CFoodclubEventDoAction
{
	private $trueEventCounter = 0;

	function doAction($action){
		switch($action["ACTION_CODE"]){
			case "BAGE":
			self::addBage($action);
			break;
			case "ADD_POINTS":
			self::addPoints($action);
			break;
			case "SEND_MAIL":
			self::sendMail($action);
			break;
		}
	}

	function checkAction($event){
		$arActionID = array();
		$rsTriggers = CFTrigger::GetList(($by = "s_today_hits"), ($order = "desc"), array("EVENT_ID" => $event["ID"]));
        while($arTrigger = $rsTriggers->GetNext()){                        
            $TriggerList[] = $arTrigger;
            $returnValue = unserialize(base64_decode($arTrigger["ACTION_ID"]));            
            if(is_array($returnValue)){
            	$arActionID = array_merge($returnValue,$arActionID);
            }else{
            	$arActionID[] = $arTrigger["ACTION_ID"];
            }
            $TriggerActionList[ $arTrigger["ID"] ][] = $returnValue;
        }
        
        if(!empty($arActionID)){
            $ActionTypesID = array();
            $rsActions = CFTriggerActions::GetList($order, $by, array("ID" => $arActionID));
            while($arAction = $rsActions->GetNext()){
                $ActionList[ $arAction["ID"] ] = $arAction;                            
            }
            $rsActionTypes = CFTriggerActionTypes::GetList();
            while($arActionType = $rsActionTypes->GetNext()){
                $arActionTypeList[ $arActionType["ID"] ] = $arActionType["CODE"];
            }
            if(!empty($ActionList)){            	
                foreach($TriggerActionList as $key => $triggerAction){                	
                    foreach($triggerAction as $actionID){
                    	if(is_array($actionID)){
                    		foreach ($actionID as $k => $value) {
                    			if(intval($ActionList[ $value ]["ACTION_TYPE"]) > 0 && isset($arActionTypeList[ $ActionList[ $value ]["ACTION_TYPE"] ])){
		                            //Проверяем и выполняем действие                                        
		                            CFoodclubEventDoAction::doAction(
		                                array(
		                                    "ID" => $ActionList[ $value ]["ID"],
		                                    "NAME" => $ActionList[ $value ]["NAME"],
		                                    "ACTION_TYPE" => $ActionList[ $value ]["ACTION_TYPE"],
		                                    "ACTION_CODE" => $arActionTypeList[ $ActionList[ $value ]["ACTION_TYPE"] ],
		                                    "ADDITIONAL_PROPS" => $ActionList[ $value ]["ADDITIONAL_PROPS"],
		                                    "BODY_PARAMS" => $ActionList[ $value ]["BODY_PARAMS"]
		                                )
		                            );
		                            //Заносим в журнал
		                            CFTriggerLog::Add(
		                                array(
		                                    "NAME" => $ActionList[ $value ]["NAME"],
		                                    "TRIGGER_ID" => $key,
		                                    "DATE_CREATE" => ConvertTimeStamp(time() + CTimeZone::GetOffset(), "FULL"),
		                                    "CREATED_BY" => CUser::GetID()
		                                )    
		                            );
		                        }
	                    	}
                    	}else{
                    		if(intval($ActionList[ $actionID ]["ACTION_TYPE"]) > 0 && isset($arActionTypeList[ $ActionList[ $actionID ]["ACTION_TYPE"] ])){
	                            //Проверяем и выполняем действие                                        
	                            CFoodclubEventDoAction::doAction(
	                                array(
	                                    "ID" => $ActionList[ $actionID ]["ID"],
	                                    "NAME" => $ActionList[ $actionID ]["NAME"],
	                                    "ACTION_TYPE" => $ActionList[ $actionID ]["ACTION_TYPE"],
	                                    "ACTION_CODE" => $arActionTypeList[ $ActionList[ $actionID ]["ACTION_TYPE"] ],
	                                    "ADDITIONAL_PROPS" => $ActionList[ $actionID ]["ADDITIONAL_PROPS"],
	                                    "BODY_PARAMS" => $ActionList[ $actionID ]["BODY_PARAMS"]
	                                )
	                            );
	                            //Заносим в журнал
	                            CFTriggerLog::Add(
	                                array(
	                                    "NAME" => $ActionList[ $actionID ]["NAME"],
	                                    "TRIGGER_ID" => $key,
	                                    "DATE_CREATE" => ConvertTimeStamp(time() + CTimeZone::GetOffset(), "FULL"),
	                                    "CREATED_BY" => CUser::GetID()
	                                )    
	                            );
	                        }
                    	}
                    }
                }
            }
        }
	}

	function increas(){		
		$this->trueEventCounter += 1;
	}

	function getTrueEventCounter(){
		return $this->trueEventCounter;
	}

	function countTrueEvents($arConditions){		
		foreach($arConditions as $child){			
            if(strlen($child["CLASS_ID"]) > 0 && !empty($child["DATA"])){
                switch($child["CLASS_ID"]){
                	case 'CondUserName':
                	if(CFoodclubEventCompare::compareString(
                        $arFields["NAME"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        self::increas();
                    }
                	break;
					case 'CondUserLastName':
					if(CFoodclubEventCompare::compareString(
                        $arFields["LAST_NAME"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        self::increas();
                    }
					break;
					case 'CondUserEmail':
					if(CFoodclubEventCompare::compareString(
                        $arFields["EMAIL"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        self::increas();
                    }
					break;
					case 'CondPersonalPhoto':
					/*if(CFoodclubEventCompare::compare(
                        $arFields["EMAIL"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        self::increas();
                    }*/
					break;
					case 'CondUserWorkWWW':
					if(CFoodclubEventCompare::compareString(
                        $arFields["WORK_WWW"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        self::increas();
                    }
					break;
                    case "CondUserGroup":
                    //Проверяем группу пользователя                                        
                    if(CFoodclubEventCompare::compareArray(
                        CUser::GetUserGroupArray(),
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        self::increas();
                    }
                    break;
                    case "CondIBElement":
                    if(CFoodclubEventCompare::compare(
                        $arFields["ID"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        self::increas();
                    }
                    break;
                    case "CondIBSection":
                    //Проверяем группу пользователя                                        
                    if(CFoodclubEventCompare::compare(
                        $arFields["SECTION_ID"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        self::increas();
                    }
                    break;
                    case "CondIBIBlock":
                    //Проверяем группу пользователя                                        
                    if(CFoodclubEventCompare::compare(
                        $arFields["IBLOCK_ID"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        self::increas();
                    }
                    break;
                    case "CondUser":
                    //Проверяем пользователя
                    if(CFoodclubEventCompare::compare(
                        CUser::GetID(),
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        self::increas();
                    }
                    break;
                }
            }
        }
	}

	function checkUser($user_id = 0, $logic, $value){
		if($user_id <= 0) $user_id = CUser::GetID();
		switch ($logic) {
			case 'Equal':
				return ($value == $user_id ? true : false);
				break;
			
			default:
				return ($value != $user_id ? true : false);
				break;
		}
	}

	function checkUserGroup($user_group_array = array(), $logic, $value){
		if(empty($user_group_array)) $user_group_array = CUser::GetUserGroupArray();
		switch ($logic) {
			case 'Equal':
				return (in_array($value,$user_group_array) ? true : false);
				break;
			
			default:
				return (!in_array($value,$user_group_array) ? true : false);
				break;
		}
	}

	function checkElement($element, $logic, $value){
		switch ($logic) {
			case 'Equal':
				return ($value == $element ? true : false);
				break;
			
			default:
				return ($value != $element ? true : false);
				break;
		}
	}

	function checkIBlock($iblock, $logic, $value){
		switch ($logic) {
			case 'Equal':
				return ($value == $iblock ? true : false);
				break;
			
			default:
				return ($value != $iblock ? true : false);
				break;
		}
	}

	function checkSection($iblock, $logic, $value){
		switch ($logic) {
			case 'Equal':
				return ($value == $iblock ? true : false);
				break;
			
			default:
				return ($value != $iblock ? true : false);
				break;
		}
	}	

	function checkLogic($arData,$trueCount,$condCount){
		if($arData["All"] == "AND"){
			if($arData["True"] == "True"){				
				return ($trueCount == $condCount ? true : false);
			}else{
				return ($trueCount ==  0 ? true : false);
			}
		}elseif($arData["All"] == "OR"){
			if($arData["True"] == "True"){
				return ($trueCount > 0 ? true : false);
			}else{
				return ($trueCount != $condCount ? true : false);
			}
		}
	}

	private function addBage($action){		
		//current User
		$user = new CUser;
		//current user badges
		$arUserBadges = array();
		//get current user badges
		$rsUser = CUser::GetByID($user->GetID());
		if($arUser = $rsUser->Fetch()){
			$arUserBadges = $arUser["UF_BADGES"];
			$arUserEmail = $arUser["EMAIL"];
			$arUserBadges = array_merge($arUserBadges, unserialize(htmlspecialchars_decode($action["ADDITIONAL_PROPS"])));		
			$user->Update($user->GetID(), array("UF_BADGES"=> $arUserBadges));
					
			/*if( $firstRecipe && $arUserEmail ){
				$arEventFields = array(
					"EMAIL_TO" => $arUserEmail
				);
				CEvent::Send("FOODSHOT_ADDED", "s1", $arEventFields, "N", 44);
			}*/
		}		
	}

	private function addPoints($action){
		//echo "<pre>";print_r($action);echo "</pre>";die;
		//current User
		$user = new CUser;
		//current user points
		$intUserPoints = 0;
		//get current user badges
		//a:1:{i:0;s:3:&quot;100&quot;;
		$rsUser = CUser::GetByID($user->GetID());
		if($arUser = $rsUser->Fetch()){
			$intUserPoints = $arUser["UF_RAITING"];
			$arUserEmail = $arUser["EMAIL"];
		}		
		$arProp = unserialize(htmlspecialchars_decode($action["ADDITIONAL_PROPS"]));
		//var_dump(self::isSerialized($action["ADDITIONAL_PROPS"]));
		//var_dump(self::isSerialized(htmlspecialchars_decode($action["ADDITIONAL_PROPS"])));
		//echo $intUserPoints."@";
		//echo "<pre>";print_r($action);echo "</pre>";
		//echo "<pre>";print_r(unserialize($action["ADDITIONAL_PROPS"]));echo "</pre>";
		//echo $intUserPoints;echo "@";
		$intUserPoints += intval($arProp[0]);
		//echo $intUserPoints;die;
		$user->Update($user->GetID(), array("UF_RAITING"=> $intUserPoints));
	}

	private function sendMail($action){
		//echo "<pre>";print_r($action);echo "</pre>";
		$arProp = unserialize(htmlspecialchars_decode($action["ADDITIONAL_PROPS"]));
		$arBodyParams = unserialize(htmlspecialchars_decode($action["BODY_PARAMS"]));
		//echo "<pre>";print_r($arProp);echo "</pre>";
		//echo "<pre>";print_r($arBodyParams);echo "</pre>";die;
		if($arProp[0] > 0){
			$arFilter = array(
				"ACTIVE" => "Y",
				"ID" => $arProp[0]
			);
			$rsEmailTemplates = CEventMessage::GetList($by, $order, $arFilter);
			if($arEmailTemplate = $rsEmailTemplates->GetNext()){
				$EVENT_NAME = $arEmailTemplate["EVENT_NAME"];
				/*$arEventFields = array(
					"EMAIL_TO" => $arUserEmail,
				);*/
				foreach($arBodyParams as $param){
					$ar = explode(" - ",$param);
					$arEventFields[ trim(str_replace("#", "", $ar[0])) ] = trim($ar[1]);
				}
				//echo "<pre>";print_r($arEventFields);echo "</pre>";die;
				//array_combine();				
				CEvent::Send($EVENT_NAME, "s1", $arEventFields, "N", $arEmailTemplate["ID"]);
			}
		}		
	}

	private function isSerialized($str) {
	    return ($str == serialize(false) || @unserialize($str) !== false);
	}

}
?>