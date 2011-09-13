<?
class Core
{
	static public $DB;
	
	static function FCKEditor($instance)
	{
		include "libs/fckeditor/fckeditor.php";
		return new FCKeditor($instance);
	}
	
	static function CKEditor($element, $value)
	{
		include_once "libs/ckeditor/ckeditor.php";
		$class = new CKEditor();
		$class->returnOutput = true;
		$class->basePath = "libs/ckeditor/";
		return $class->editor($element, $value);
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
	
	static function addChangeLog($type, $key, $value)
	{
		self::$DB->query("
			INSERT INTO 
				".Tools::getSiteTable("changelog")." 
				(`type`,`key`,`value`,`time`) 
			VALUES 
			(
				'{$type}',
				'{$key}',
				'{$value}',
				'".time()."'
			)");
	}	
	
	static function autoLoad($classname)
	{
		if(class_exists($classname))
			return;
		
		if(file_exists("classes/{$classname}.php"))		
		{
			require_once("classes/{$classname}.php");
			return;
		}
		elseif(file_exists("classes/". strtolower($classname).".php"))
		{
			require_once("classes/". strtolower($classname).".php");		
			return;
		}	
			
		$array = explode("_", $classname);
		if(count($array) > 1)
		{
			$sepCount = count($array);
			
			$patch = "";
			
			$first = true;
			
			foreach($array as $value)
			{
				$patch .= (!$first) ? "/" : null;
				$patch .= "{$value}";
				$first = false;
			}
			
			$patch .= ".php";
			
			if(file_exists($patch))
			{
				require_once($patch);
				return;
			}
		}
		
		trigger_error("Não foi possivel carregar classe {$classname} automaticamente.");
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
		//if($local)
			//$url = CONFIG_SITEEMAIL."/".$url;
	
		//header("Location: ".$url." ");	
		$html =  "<script type='text/javascript'>window.location = \"http://{$_SERVER["HTTP_HOST"]}/{$url}\"</script>";
		echo $html;
	}
	
	static function requireLogin()
	{
		$_SESSION["login_redirect"] = $_SERVER["REQUEST_URI"];
		Core::redirect("?ref=account.login");
	}
	
	static function getIpTries()
	{
		$query = self::$DB->query("SELECT COUNT(*) as `rows` FROM `".Tools::getSiteTable("iptries")."` WHERE `ip_addr` = '".$_SERVER['REMOTE_ADDR']."' AND `date` >= '".(time() - (60 * 60 * 24))."'");		
		
		if($query->numRows() != 0)
		{
			return $query->fetch()->rows;
		}
		else
			return false;
	}
	
	static function increaseIpTries()
	{
		self::$DB->query("INSERT INTO `".Tools::getSiteTable("iptries")."` (`ip_addr`, `date`) VALUES ('".$_SERVER['REMOTE_ADDR']."', '".time()."')");
	}		
	
	static function getGlobalValue($field)
	{
		$query = self::$DB->query("SELECT value FROM ".DB_WEBSITE_PREFIX."global WHERE field = '{$field}'");
		
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
		$query = self::$DB->query("SELECT value FROM ".DB_WEBSITE_PREFIX."global WHERE field = '{$field}'");
		
		if($query->numRows() != 0)
			self::$DB->query("UPDATE ".DB_WEBSITE_PREFIX."global SET value = '{$value}' WHERE field = '{$field}'");
		else
			self::$DB->query("INSERT INTO ".DB_WEBSITE_PREFIX."global (`field`, `value`) values('{$field}', '{$value}')");
	}	
	
	static function getLastAdClick()
	{
		$query = self::$DB->query("SELECT date FROM ".DB_WEBSITE_PREFIX."adpage ORDER BY date DESC LIMIT 1");
		
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
	
	static function includeJavaScriptSource($file)
	{
		global $module;
		
		$module .= '
		<script type="text/javascript" src="javascript/'.$file.'"></script>
		';		
	}	
}		
?>