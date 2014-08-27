<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/classes/general/utils.php");

class CFoodclubEventCompare
{
	function compare($user_id = 0, $logic, $value){
		switch ($logic) {
			case 'Equal':
				return ($value == $user_id ? true : false);
				break;

			case 'Not':
				return ($value != $user_id ? true : false);
				break;

			case 'Contain':
				return (strpos($user_id,$value) !== false ? true : false);
				break;

			case 'NotCont':
				return (strpos($user_id,$value) === false ? true : false);
				break;

			case 'Great':
				return ($value < $user_id ? true : false);
				break;

			case 'Less':
				return ($value > $user_id ? true : false);
				break;

			case 'EqGr':
				return ($value <= $user_id ? true : false);
				break;

			case 'EqLs':
				return ($value >= $user_id ? true : false);
				break;
		}
	}

	function compareString($user_id = "", $logic, $value){
		switch ($logic) {
			case 'Equal':
				return ($value == $user_id ? true : false);
				break;

			case 'Not':
				return ($value != $user_id ? true : false);
				break;

			case 'Contain':
				return (strpos($user_id,$value) !== false ? true : false);
				break;

			case 'NotCont':
				return (strpos($user_id,$value) === false ? true : false);
				break;

			case 'Great':
				return ($value < strlen($user_id) ? true : false);
				break;

			case 'Less':
				return ($value > strlen($user_id) ? true : false);
				break;

			case 'EqGr':
				return ($value <= strlen($user_id) ? true : false);
				break;

			case 'EqLs':
				return ($value >= strlen($user_id) ? true : false);
				break;
		}
	}

	function compareArrayKey($user_group_array = array(), $logic, $value){
		switch ($logic) {
			case 'Equal':
				return (array_key_exists($value,$user_group_array) ? true : false);
				break;
			
			default:
				return (!array_key_exists($value,$user_group_array) ? true : false);
				break;
		}
	}	

	function compareArrayValue($user_group_array = array(), $logic, $value){
		switch ($logic) {
			case 'Equal':
				return (in_array($value,$user_group_array) ? true : false);
				break;
			
			default:
				return (!in_array($value,$user_group_array) ? true : false);
				break;
		}
	}

	function compareMultiArrayValue($user_group_array = array(), $logic, $value){
		switch ($logic) {
			case 'Equal':
				return (CFoodclubEventUtils::in_multiarray($value,$user_group_array) ? true : false);
				break;
			
			default:
				return (!CFoodclubEventUtils::in_multiarray($value,$user_group_array) ? true : false);
				break;
		}	
	}

	function compareConditions($conditions_array = array(),$arData) {
		$trueCount = 0;$condCount = count($conditions_array);
		foreach ($conditions_array as $condition => $values) {
			switch ($condition) {
				case 'compare':
					if(self::compare($values[0],$values[1],$values[2]))
						$trueCount++;
					break;

				case 'compareString':
					if(self::compareString($values[0],$values[1],$values[2]))
						$trueCount++;
					break;

				case 'compareArrayKey':
					if(self::compareArrayKey($values[0],$values[1],$values[2]))
						$trueCount++;
					break;
				
				case 'compareArrayValue':
					if(self::compareArrayValue($values[0],$values[1],$values[2]))
						$trueCount++;
					break;

				case 'compareMultiArrayValue':
					if(self::compareMultiArrayValue($values[0],$values[1],$values[2]))
						$trueCount++;
					break;

				default:
					# code...
					break;
			}
		}
		
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

	function compareDates($date, $logic, $value) {
		if($date == "now()"){
			$date = strtotime("now");
		}else{
			$date = strtotime($date);
		}
		switch ($logic) {
			case 'Equal':
				return (strtotime($value) == $date ? true : false);
				break;

			case 'Not':
				return (strtotime($value) != $date ? true : false);
				break;

			case 'Great':
				return ($date > strtotime($value) ? true : false);
				break;

			case 'Less':
				return ($date < strtotime($value)   ? true : false);
				break;

			case 'EqGr':
				return ($date >= strtotime($value) ? true : false);
				break;

			case 'EqLs':
				return ($date <= strtotime($value) ? true : false);
				break;
		}
	}
}
?>