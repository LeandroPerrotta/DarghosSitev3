<?
class Strings
{
	function validEmail($email) 
	{ 
	    $e = explode("@",$email); 
	    if(count($e) <= 1) 
		{ 
	        return false; 
	    } 
		elseif(count($e) == 2) 
		{ 
	        $ip = gethostbyname($e[1]); 
	        if($ip == $e[1]) 
			{ 
	            return false; 
	        } 
			elseif($ip != $e[1]) 
			{ 
	            return true; 
	        } 
	    } 
	}
}
?>