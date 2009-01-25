<?
class Core
{
	function mail($emailid, $to, $arg, $from = CONFIG_SITEEMAIL) 
	{
		include "libs/phpmailer/class.phpmailer.php";

		$emailvalue = array();
		foreach($arg as $value)
		{
			$emailvalue[] = $value;
		}
		
		include "emails.php";		
		
		$mail = new PHPMailer();
		
		$mail->Host = SMTP_HOST;
		$mail->IsHTML(true);
		$mail->IsSMTP();
		$mail->Password = SMTP_PASS;
		$mail->SMTPAuth = true;
		$mail->Username = SMTP_USER;
		$mail->Port = SMTP_PORT; 
		
		$mail->AddAddress($to);
		$mail->AddReplyTo($from, CONFIG_SITENAME);
		$mail->From = $from;
		$mail->FromName = CONFIG_SITENAME;
		
		$mail->Body = $emailmodel[$emailid];
		$mail->Subject = $emailsubject[$emailid];
		
		if ($mail->Send()) 
		{
			return true;
		}
		
		return false;
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
	
	function loadClass($class)
	{
		include_once "classes/".$class.".php";
		return new $class();
	}
	
	function extractPost()
	{
		if($_POST)
		{
			$post = array();
		
			foreach($_POST as $field => $value)
			{
				$post[] = $value;
			}
			
			return $post;
		}		
		else
			return false;
	}
	
	function redirect($url, $delay = false) 
	{		
		echo '
			<script type="text/javascript">
				$(document).ready(function() {
					setTimeout(function() { window.location = "' . $url . '"; }, ' . $delay . ');
				});
			</script>
		';
	}	
	
	function filterInputs($checkGets = false)
	{
		if($_POST)
		{
			foreach($_POST as $post => $value)
			{
				if(!$this->SQLInjection($value))
					return false;
			}
		}
	
		if($checkGets)
		{
			foreach($_GET as $post => $value)
			{
				if(!$this->SQLInjection($value))
					return false;
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
}		
?>