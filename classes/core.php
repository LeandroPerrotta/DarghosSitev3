<?
class Core
{
	static public $DB;
	
	static function FCKEditor($instance)
	{
		include "libs/fckeditor/fckeditor.php";
		return new FCKeditor($instance);
	}
	
	static function InitPOT()
	{
		// includes POT main file
		include_once('classes/pot/OTS.php');
		
		$array = explode(":", DB_HOST);
		
		if(count($array) == 2)
		{
			$ip = $array[0];
			$port = $array[1];
		}
		else
		{
			$ip = DB_HOST;
			$port = 3306;
		}
		
		// database configuration - can be simply moved to external file, eg. config.php
		$config = array(
		    'driver' => POT::DB_MYSQL,
		    'host' => $ip,
		    'port' => $port,
		    'user' => DB_USER,
		    'password' => DB_PASS,
		    'database' => DB_SCHEMA
		);
		
		// creates POT instance (or get existing one)
		// dont use POT::getInstance() anymore
		POT::connect(null, $config);
		// could be: POT::connect(POT::DB_MYSQL, $config);		
	}
	
	static function mail($emailid, $to, $arg = null, $from = CONFIG_SITEEMAIL) 
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
	
	/* DEPRECATED FUNCTION */
	static function loadClass($class)
	{
		include_once "classes/".strtolower($class).".php";
		return new $class();
	}
	
	static function InitLanguage()
	{
		if(GLOBAL_LANGUAGE == "pt")
		{	
			include "language/pt/menu.php";
			include "language/pt/pages.php";
			include "language/pt/buttons.php";
			include "language/pt/Messages.php";
			
			include "classes/Lang.php";
			
			Lang_Messages::Load();
		}		
	}
	
	/* DEPRECATED FUNCTION */
	static function extractPost()
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
	
	static function formatDate($date)
	{
		return date("d/m/y - H:i", $date);
	}
	
	static function getHour()
	{
		return date("H", time());
	}
	
	static function redirect($url, $local = true/*, $delay = false*/) 
	{		
		if($local)
			$url = CONFIG_SITEEMAIL."/".$url;
	
		header("Location: ".$url." ");
	}	
	
	static function getIpTries()
	{
		$query = $this->db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."iptries WHERE ip_addr = '".$_SERVER['REMOTE_ADDR']."'");		
		
		if($query->numRows() != 0)
		{
			return $query->fetch()->tries;
		}
		else
			return false;
	}
	
	static function getGlobalValue($field)
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
	
	static function setGlobalValue($field, $value)
	{
		$query = $this->db->query("SELECT value FROM ".DB_WEBSITE_PREFIX."global WHERE field = '{$field}'");
		
		if($query->numRows() != 0)
			$this->db->query("UPDATE ".DB_WEBSITE_PREFIX."global SET value = '{$value}' WHERE field = '{$field}'");
		else
			$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."global (`field`, `value`) values('{$field}', '{$value}')");
	}	
	
	static function increaseIpTries()
	{
		$query = $this->db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."iptries WHERE ip_addr = '".$_SERVER['REMOTE_ADDR']."'");		
		
		if($query->numRows() != 0)
		{
			$this->db->query("UPDATE ".DB_WEBSITE_PREFIX."iptries SET tries = tries + 1, last_trie = '".time()."' WHERE ip_addr = '".$_SERVER['REMOTE_ADDR']."'");	
		}
		else
			$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."iptries (ip_addr, tries, last_trie) values('".$_SERVER['REMOTE_ADDR']."', '1', '".time()."')");
	}	
	
	static function getLastAdClick()
	{
		$query = $this->db->query("SELECT date FROM ".DB_WEBSITE_PREFIX."adpage ORDER BY date DESC LIMIT 1");
		
		$fetch = $query->fetch();
		
		return $fetch->date;
	}
	
	static function sendMessageBox($title, $msg)
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