<?
class Strings
{
	private static $db;
	
	static function Init()
	{
		global $db;
		self::$db = $db;
	}
	
	static function filterInputs($checkGets = false)
	{
		if($_POST)
		{
			foreach($_POST as $post => $value)
			{
				$_POST[$post] = self::SQLInjection($value);
			}
		}
	
		if($checkGets)
		{
			foreach($_GET as $get => $value)
			{
				$_GET[$get] = self::SQLInjection($value);	
			}		
		}

		return true;	
	}
	
	static function SQLInjection($string)
	{
		//$string = nl2br($string);
	    //$string = get_magic_quotes_gpc() ? stripslashes($string) : $string;
	    
		$string = !get_magic_quotes_gpc() ? addslashes($string) : $string;
	 
	    //$string = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($string) : mysql_escape_string($string);
	 
	    return $string;		
	}
	
	static function randKey($tamanho, $separadores, $randTypeElement = "default") 
	{ 
		$options['upper'] = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$options['lower'] = "abcdefghijklmnopqrstuvwxyz";
		$options['number'] = "01234567890123456789";
			
		if($randTypeElement != "default")
		{
			$randTypeElement = explode("+", $randTypeElement);
			
			foreach($randTypeElement as $value)
			{
				$fullRand .= $options[$value];
			}
		}
		else
			$fullRand = $options['upper'].$options['lower'].$options['number'];
			
		$countChars = strlen($fullRand);
	
		$string = "";
		$part = array();
	
		for($i = 0; $i < $separadores; $i++)
		{
			for($n = 0; $n < $tamanho; $n++)
			{
				$rand = mt_rand(1, $countChars);
				$part[$i] .= $fullRand[$rand];	
			}
			
			if($i == 0)
				$string .= $part[$i];
			else
				$string .= "-".$part[$i];
		}
		
		return $string;
	}		
	
	static function validEmail($email) 
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
	    
    	return false;
	}
	
	static function isFromBlackList($string)
	{
		$query = self::$db->query("SELECT string FROM ".DB_WEBSITE_PREFIX."blackliststrings");
		
		while($fetch = $query->fetch())
		{
			if(preg_match("/(".$fetch->string.")/i", $string))
				return false;
		}
		
		return true;
	}
	
	static function encrypt($string)
	{
		switch(ENCRYPTION_TYPE)
		{
			case "md5";
				$enc = md5($string);
			break;	
		}
		
		return $enc;
	}	
	
	static function canUseName($nameString, $checkBlackList = true)
	{
		if(trim($nameString) != $nameString)
			return false;	
		
		$palavras = explode(" ", $nameString);
		
		if($checkBlackList and !self::isFromBlackList($nameString))
			return false;
		
		if(count($palavras) > 3)
			return false;
		
		if(ucfirst($palavras[0]) != $palavras[0])
			return false;
			
		if(ucfirst($palavras[2]) != $palavras[2])
			return false;			
			
		if(count($palavras) == 3)
		{
			if(strlen($palavras[0]) < 3)
				return false;	
				
			if(strlen($palavras[2]) < 3)
				return false;	
		}
		elseif(count($palavras) == 2)	
		{
			if(strlen($palavras[0]) < 3)
				return false;	
				
			if(strlen($palavras[1]) < 3)
				return false;			
		}
		elseif(count($palavras) == 1)	
		{
			if(strlen($palavras[0]) < 3)
				return false;			
		}		
	
		for($a = 0; $a != count($palavras); $a++)
		{	
			foreach(count_chars($palavras[$a], 1) as $quantidade)
			{
				if($quantidade > 4)
					return false;				
			}
		}
			
		if(strlen($nameString) > 30)	
			return false;
			
		$letras = str_split($nameString);	
		$space = array();
		
		for($a = 0; $a != count($letras); $a++)
		{
			if($letras[$a] == " ")
			{
				if(count($space) != 0 and ($space[0] + 1) == $a)
					return false;

				$space[] = $a;
			}				
		}
		
		$temp = strspn("$nameString", "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM '-");
		
		if($temp != strlen($nameString))
			return false;
		
		return true;
	}		
}
?>