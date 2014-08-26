<?php

class CFoodclubEventUtils
{
	function recursive_array_search($needle,$haystack) {
        foreach($haystack as $key=>$value) {
            $current_key=$key;
            if($needle===$value OR (is_array($value) && self::recursive_array_search($needle,$value) !== false)) {
                return $current_key;
            }
        }
        return false;
    }

    function in_multiarray($elem, $array) {
        $top = sizeof($array) - 1;
        $bottom = 0;
        while($bottom <= $top)
        {
            if($array[$bottom] == $elem)
                return true;
            else 
                if(is_array($array[$bottom]))
                    if(self::in_multiarray($elem, ($array[$bottom])))
                        return true;
                    
            $bottom++;
        }        
        return false;
    }
}
?>