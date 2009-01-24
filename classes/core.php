<?
class Core
{
	function mail($message, $subject, $to, $from = CONFIG_SITEEMAIL) 
	{
		$mail = new PHPMailer();
		
		$mail->Host = SMTP_HOST;
		$mail->IsSMTP();
		$mail->Password = SMTP_PASS;
		$mail->SMTPAuth = true;
		$mail->Username = SMTP_USER;
		$mail->Port = SMTP_PORT; 
		
		$mail->AddAddress($to);
		$mail->AddReplyTo($from, CONFIG_SITENAME);
		$mail->From = $from;
		$mail->FromName = CONFIG_SITENAME;
		
		$mail->Body = $message;
		$mail->Subject = $subject;
		
		if ($mail->Send()) 
		{
			return true;
		}
		
		return false;
	}
	
	function loadClass($class)
	{
		include_once "classes/".$class.".php";
		return new $class();
	}
}		
?>