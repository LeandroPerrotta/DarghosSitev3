<?
class Core
{
	private $db;
	private $alreadyShowBanner = false;

	function __construct()
	{
		global $db;
		$this->db = $db;
	}	
	
	function FCKEditor($instance)
	{
		include "libs/fckeditor/fckeditor.php";
		return new FCKeditor($instance);
	}
	
	function InitPOT()
	{
		// includes POT main file
		include_once('classes/pot/OTS.php');
		
		list($ip, $port) = explode(":", DB_HOST);
		
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
		include_once "classes/".strtolower($class).".php";
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
	
	function getHour()
	{
		return date("H", time());
	}
	
	function redirect($url, $local = true/*, $delay = false*/) 
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
			return false;
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
	
	function getLastAdClick()
	{
		$query = $this->db->query("SELECT date FROM ".DB_WEBSITE_PREFIX."adpage ORDER BY date DESC LIMIT 1");
		
		$fetch = $query->fetch();
		
		return $fetch->date;
	}
	
	function sendMessageBox($title, $msg, $adbanner = false, $showbannerChanceFactor = 50000)
	{
		global $module;
		
		$module .= '
			<table cellspacing="0" cellpadding="0">
				<tr>
					<th>'.$title.'</th>
				</tr>';		
		
		if(!$adbanner)
		{
			$module .= '
				<tr>
					<td>'.$msg.'</td>
				</tr>';
		}
		elseif($adbanner)
		{
			$showBannerChance = rand(0, 100000);
		
			if(!$this->alreadyShowBanner and $showBannerChance < $showbannerChanceFactor)
			{
				$this->alreadyShowBanner = true;
			
				$module .= '
					<tr>
						<td>									
							<div style="float: right; width: 300px; top: 10px;">
								<script type="text/javascript"><!--
								google_ad_client = "pub-1678394806564868";
								/* 300x250, criado 09/10/09 */
								google_ad_slot = "7656234698";
								google_ad_width = 300;
								google_ad_height = 250;
								//-->
								</script>
								<script type="text/javascript"
								src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
								</script>						
							</div>
							
							'.$msg.'							
						</td>
					</tr>';	
			}	
			else
			{
				$module .= '
					<tr>
						<td>'.$msg.'</td>
					</tr>';			
			}			
		}
		


		$module .= '
			</table>		
		';
	}
}		
?>