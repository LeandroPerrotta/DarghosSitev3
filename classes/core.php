<?
class Core
{
	private $db;

	function __construct()
	{
		global $db_tenerian;
		$this->db = $db_tenerian;
	}	
	
	function FCKEditor($instance)
	{
		include "libs/fckeditor/fckeditor.php";
		return new FCKeditor($instance);
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
		
		$mail->IsHTML(true);
		$mail->IsSMTP();
		//$mail->SMTPDebug = true;

		$mail->SMTPAuth   = true;
		$mail->Host       = SMTP_HOST;
		$mail->Port       = SMTP_PORT;

		$mail->Username   = SMTP_USER;
		$mail->Password   = SMTP_PASS;
			
		$mail->FromName = CONFIG_SITENAME;
		$mail->From = SMTP_USER;
			
		$mail->AddAddress($to);

		//$mail->AddBcc("jotape.ms@hotmail.com");

		$mail->Subject = $emailsubject[$emailid];
		$mail->Body = $emailmodel[$emailid];
		
		if ($mail->Send()) 
		{
			return true;
		}
		
		return false;
	}
	
	function loadUploadClass() 
	{
		include "libs/upload/upload_class.php";	
	}	
	
	function loadClass($class)
	{
		include_once "classes/".$class.".php";
		return new $class();
	}
	
	function extractPost()
	{
		$strings = $this->loadClass("strings");
		
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
	
	function getGlobalValue($field)
	{
		$query = $this->db->query("SELECT value FROM ".DB_WEBSITE_PREFIX."global WHERE field = '{$field}'");
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			
			return $fetch->value;
		}
		else
			return false;
	}
	
	function setGlobalValue($field, $value)
	{
		$query = $this->db->query("SELECT value FROM ".DB_WEBSITE_PREFIX."global WHERE field = '{$field}'");
		
		if($query->numRows() != 0)
			$this->db->query("UPDATE ".DB_WEBSITE_PREFIX."global SET value = '{$value}' WHERE field = '{$field}'");
		else
			$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."global (`field`, `value`) values('{$field}', '{$value}')");
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
	
	function sendMessageBox($title, $msg)
	{
		global $module;
		
		$module .= '
			<table cellspacing="0" cellpadding="0">
				<tr>
					<th>'.$title.'</th>
				</tr>

				<tr>
					<td>'.$msg.'</td>
				</tr>	
			</table>		
		';
	}
}		
?>