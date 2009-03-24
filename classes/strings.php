<?
class Strings
{
	private $db;

	function __construct()
	{
		global $db_tenerian;
		$this->db = $db_tenerian;
	}

	function filterInputs($checkGets = false)
	{
		if($_POST)
		{
			foreach($_POST as $post => $value)
			{
				if(($post != "password" and $post != "account_password") and !$this->SQLInjection($value))
					return false;
			}
		}
	
		if($checkGets)
		{
			foreach($_GET as $post => $value)
			{
				if($post != "ref")
				{
					if(!$this->SQLInjection($value))
						return false;
				}	
			}		
		}

		return true;	
	}
	
	function SQLInjection($string)
	{
		if(preg_match("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/i", $string))
			return false;
		else
			return true;		
	}
	
	function randKey($tamanho, $separadores, $randTypeElement = "default") 
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
	
	function isFromBlackList($string)
	{
		$query = $this->db->query("SELECT string FROM ".DB_WEBSITE_PREFIX."blacklistStrings");
		
		while($fetch = $query->fetch())
		{
			if(preg_match("/(".$fetch->string.")/i", $string))
				return false;
		}
		
		return true;
	}
	
	function encrypt($string)
	{
		switch(ENCRYPTION_TYPE)
		{
			case "md5";
				$enc = md5($string);
			break;	
		}
		
		return $enc;
	}	
	
	function canUseName($nameString, $checkBlackList = true)
	{
		if(trim($nameString) != $nameString)
			return false;	
		
		$palavras = explode(" ", $nameString);
		
		if($checkBlackList and !$this->isFromBlackList($nameString))
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
			foreach(count_chars($palavras[$a], 1) as $letra => $quantidade)
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