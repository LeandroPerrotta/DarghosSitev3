<?
class Core
{
	private $db;

	function __construct()
	{
		global $db_tenerian;
		$this->db = $db_tenerian;
	}	
	
	function mail($emailid, $to, $arg = null, $from = CONFIG_SITEEMAIL) 
	{
		include "libs/phpmailer/class.phpmailer.php";

		if($arg)
		{
			$emailvalue = array();
			foreach($arg as $value)
			{
				$emailvalue[] = $value;
			}
			
			include "configs/emails.php";		
		}
		
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
	
	function formatDate($date)
	{
		return date("d/m/y - H:i", $date);
	}
	
	function redirect($url, $local = true, $delay = false) 
	{		
		if($local)
			$url = CONFIG_SITEEMAIL."/".$url;
	
		header("Location: ".$url." ");
	}	
	
	function getIpTries()
	{
		$query = $this->db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."iptries WHERE ip_addr = '".$_SERVER['REMOTE_ADDR']."'");		
		
		if($query->numRows() != 0)
		{
			return $query->fetch()->tries;
		}
		else
			false;
	}
	
	function increaseIpTries()
	{
		$query = $this->db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."iptries WHERE ip_addr = '".$_SERVER['REMOTE_ADDR']."'");		
		
		if($query->numRows() != 0)
		{
			$this->db->query("UPDATE ".DB_WEBSITE_PREFIX."iptries SET tries = tries + 1, last_trie = '".time()."' WHERE ip_addr = '".$_SERVER['REMOTE_ADDR']."'");	
		}
		else
			$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."iptries (ip_addr, tries, last_trie) values('".$_SERVER['REMOTE_ADDR']."', '1', '".time()."')");
	}	
}		
?>