<?php
namespace Core;
class Lang extends \Lang_Messages{
	
	public static $e_Msgs;
	
	static function GetMsgs()
	{
		return self::$e_Msgs;
	}
	
	static function Init()
	{
		self::$e_Msgs = new \e_LangMsg();
	}
	
	static function Message()
	{
		$args_num = func_num_args(); //numero de argumentos recebidos
		$args = func_get_args(); //array contendo os argumentos recebidos
		
		//o primeiro argumento (zero na array) sempre é a mensagem
		$messageid = $args[0];
		
		$message = self::$messages[$messageid];
		
		// os argumentos seguintes serão substituidos na mensagem pelo 
		// seu valor correspondente, argumento 1 na array em diante por
		// @v1@ em diante... se não ouver mais de 1 argumento, nada é feito
		if($args_num > 1)
		{
			for($i = 1; $i < $args_num; $i++)
			{
				$message = str_replace("@v{$i}@", $args[$i], $message);
			}
		}	
		
		return $message;
	}
}

?>