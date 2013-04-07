<?
namespace Core;
use Framework\Pages;

class Main
{
	static public $DB, $FoundController = false, $isAjax = false;
	
	static private $m_XMLRoot;
	
	static function Initialize($isCLI = false, $cliArgs = array())
	{
		//set_error_handler("Core\\Main::errorHandler", E_ALL^E_NOTICE);
		spl_autoload_register("Core\\Main::autoLoad");
		
		//multiclass files
		include_once "classes/Core/Enums.php";
		
		//configs handler
		Configs::Init();
		
		//external libs
		include_once "libs/phpmailer/class.phpmailer.php";		
		
		if(!Configs::Get(Configs::eConf()->ENABLE_MANUTENTION) || $isCLI)
		{
			try
			{
				self::$DB = new MySQL();
				self::$DB->connect(Configs::Get(Configs::eConf()->SQL_HOST), Configs::Get(Configs::eConf()->SQL_USER), Configs::Get(Configs::eConf()->SQL_PASSWORD), Configs::Get(Configs::eConf()->SQL_DATABASE));	
			}
			catch (\Exception $e)
			{
				echo "Impossivel se conectar ao banco de dados.";
			}
				
			self::InitPOT();	
				
			if(!$isCLI)
			{
				self::InitLanguage();
				Emails::init();		
				
				if(!$_SESSION["login_redirect"] && $_SESSION["login_post"])
				{
					$_POST = $_SESSION["login_post"];
					unset($_SESSION["login_post"]);
				}
					
				self::loadTemplate();
				self::routeToController();				
			}
			else
				self::runCLI($cliArgs);
		}
	}
	
	static function runCLI($cliArgs = array())
	{		
		if(count($cliArgs) == 1)
		{
			echo "Atenção, você deve digitar este comando com algum argumento, use o -h ou --help para ler a lista de argumentos\n";
			return;
		}
		
		$loadModule = false;
		$loadedModule = NULL;
		
		foreach($cliArgs as $key => $arg)
		{
			if($key == 0)
				continue;
			
			switch($arg)
			{
				case "-h":
				case "--help":
echo "Uso: {$cliArgs[0]} [args...]\n
	-h | --help	... Exibe esta mensagem\n
	-m [modulename] | --module [modulename] ... Carrega um modulo
";
					return true;
					break;
					
				case "-m":
				case "--module":
					$loadModule = true;
					break;
					
				default:
					if($loadModule)
					{
						$loadedModule = $arg;
						$loadModule = false;
					}
					else
					{
						echo "Argumento {$arg} desconhecido. Use o {$cliArgs[0]} --help para maiores informações.\n";
						return true;
					}
					break;
			}
		}
		
		if(!$loadedModule)
		{
			echo "Este comando requer que você carregue um modulo. Use o {$cliArgs[0]} --help para maiores informações.\n";
			return true;
		}
		
		$classStr = "CLIModules\\{$loadedModule}";
		
		if(self::autoLoad($classStr))
		{
			$obj = new $classStr();
			$obj->Run();
			
			return true;
		}
		else
		{
			echo "Modulo {$loadedModule} inexistente. Use o {$cliArgs[0]} --help para maiores informações.\n";
			return true;
		}
	}
	
	static function routeToController()
	{
		$data = explode(".", $_GET["ref"]);
		
		$class = array();
		
		
		foreach($data as $k => $v)
		{
			$data[$k] = strtolower($v);
			$data[$k] = ucfirst($v);
		}
		
		array_unshift($data, "Controllers");
		
		array_push($class, $data[0]);
		array_push($class, $data[1]);
		
		$class_str = implode("\\", $class);
		$method = $data[2];
		
		if(self::autoLoad($class_str))
		{
			$obj = new $class_str();
			if(method_exists($obj, $method))
			{
				$ret = $obj->$method();
								
				if(self::$isAjax)
					if(is_array($ret))
						echo json_encode($ret);
					else
						echo $ret;
				else
				{
					if($ret)
						self::$FoundController = true;
				}		
			}
		}
	}
	
	static function onEnd()
	{						
		//após tudo, se nao conseguimos achar nada para carregar a pagina, iremos tentar carregar uma pagina simples, ou então criar uma...
		$data = explode(".", $_GET["ref"]);		
		
		foreach($data as $k => $v)
		{
			$data[$k] = strtolower($v);
			$data[$k] = ucfirst($v);
		}		
		
		array_unshift($data, "Pages");
		
		$logged = \Framework\Account::loadLogged();
		
		$patch = implode("/", $data);
		
		$exists = file_exists($patch . ".xml");
		
		if($exists || ($logged && $logged->getAccess() >= \t_Access::CommunityManager))
		{
			$page = new \Core\Pages($patch . ".xml");
				
			global $module;
				
			if($logged && $logged->getAccess() >= \t_Access::CommunityManager)
			{
				$module .= "<p style='text-align: right;'>";
				if(!(bool)$_GET["edit"])
				{
					$module .= "<a href='{$_SERVER["REQUEST_URI"]}&edit=true'>Editar</a>";
					if($exists)
						$module .= $page->GetContent();
				}
				else
				{
					if($_POST)
					{
						$page->SetContent(trim($_POST["page_content"]));
						$b = $page->save();
						$exists = true;
						
						self::sendMessageBox("Sucesso!", "Pagina editada com sucesso! {$b} bytes.");
					}
						
					unset($_GET["edit"]);
						
					$str = "?";
					$first = true;
					foreach($_GET as $k => $v)
					{
						if($first)
							$first = false;
						else
							$str .= "&";
							
						$str .= "{$k}={$v}";
					}
					$module .= "<a href='{$str}'>Voltar</a>";
						
					$content = $exists ? $page->GetContent() : "";
					
					$module .= "<form action='{$_SERVER["REQUEST_URI"]}' method='POST'>
					<p>".\Core\Main::CKEditor("page_content", $content)."</p>
					</form>
					";
				}
		
					$module .= "</p>";
			}
			else
			{
				$module .= $page->GetContent();
			}
		}
		else
			return false;

		return true;
	}
	
	static function errorHandler($errno, $errstr, $errfile, $errline)
	{
		if(!self::$DB)
			return false;
		
		self::$DB->ExecQuery("INSERT INTO `".Tools::getSiteTable("errors")."` VALUES ('{$errno}', '".self::$DB->escapeString($errstr)."', '".self::$DB->escapeString($errfile)."', '{$errline}', UNIX_TIMESTAMP())");
	
		die("Um erro foi encontrado e reportado ao Administrador. Por favor, tente novamente mais tarde.");
	}
	
	static function autoLoad($classname)
	{
		if(class_exists($classname))
			return true;
		
		$rep = str_replace("\\", "/", $classname);
			
		if(is_dir("classes/{$rep}"))
		{			
			$explode = explode("/", $rep);
			$last = count($explode) - 1;
			
			if(is_file("classes/{$rep}/".$explode[$last].".php"))
			{
				require_once("classes/{$rep}/".$explode[$last].".php");
				return true;				
			}
		}	
		elseif(is_file("classes/{$rep}.php"))		
		{			
			require_once("classes/{$rep}.php");
			return true;
		}
		
		return false;
	}	
	
	static function loadTemplate()
	{
		$layoutDir = "newlay/";
		$patch = "{$layoutDir}index.html";
		self::$m_XMLRoot = self::ParseXML($patch);
		
		self::$m_XMLRoot->head[0]->title[0] = Configs::Get(Configs::eConf()->WEBSITE_NAME);
		
		$focus = self::$m_XMLRoot->head[0];
		
		$child = $focus->addChild("link");
		$child->addAttribute("rel", "shotcurt icon");
		$child->addAttribute("href", "favicon.ico");
		$child->addAttribute("type", "image/x-icon");
		
		$child = $focus->addChild("link");
		$child->addAttribute("href", "{$layoutDir}style.css");
		$child->addAttribute("media", "screen");
		$child->addAttribute("rel", "stylesheet");
		$child->addAttribute("type", "text/css");
		
		$child = $focus->addChild("link");
		$child->addAttribute("href", "default.css");
		$child->addAttribute("media", "screen");
		$child->addAttribute("rel", "stylesheet");
		$child->addAttribute("type", "text/css");
		
		/* JQuery UI theme */
		$child = $focus->addChild("link");
		$child->addAttribute("href", "javascript/libs/jquery-ui.css");
		$child->addAttribute("media", "screen");
		$child->addAttribute("rel", "stylesheet");
		$child->addAttribute("type", "text/css");		
		
		self::includeJavaScriptSource("libs/jquery.js");
		self::includeJavaScriptSource("libs/jquery-ui.js");
		self::includeJavaScriptSource("libs/ext.js");
		self::includeJavaScriptSource("functions.js");
		self::includeJavaScriptSource("lists.js");
		
		if(Configs::Get(Configs::eConf()->STATUS_SHOW_PING))
		{
			self::includeJavaScriptSource("ping.js");
		}		
	}
	
	static function drawTemplate()
	{		
		if(self::$isAjax)
			return;
		
		$xml = self::$m_XMLRoot->asXML();
		
		$xml = str_replace("%TOP_BAR%", Menus::drawTopBar(), $xml);
		$xml = str_replace("%TOP_MENU%", Menus::drawTopMenu(), $xml);
		$xml = str_replace("%LEFT_MENU%", Menus::drawLeftMenu(), $xml);
		$xml = str_replace("%RIGHT_MENU%", Menus::drawRightMenu(), $xml);
		
		global $module, $patch;
		
		$xml = str_replace("%MODULE%", $module, $xml);
		
		$url = ($patch['urlnavigation']) ? $patch['urlnavigation'] : "/";
		$xml = str_replace("%URL_NAVIGATOR%", "<div id='nav-bar' style='padding: 0px'><span>{$url}</span></div>", $xml);		
		
		echo $xml;
	}
	
	static function ParseXML($patch)
	{
		if(is_readable($patch))
		{
			libxml_use_internal_errors(true);
			$load = simplexml_load_file($patch);
			
			if($load === false)
			{
				$error = "Falha ao carregar o XML ({$patch}):";		
			    foreach(libxml_get_errors() as $error) {
			    	echo "\t" . $error->message;
    			}
    			
    			trigger_error($error, E_USER_ERROR);
    			return false;
			}
			else
				return $load;		
		}
		else
		{
			trigger_error("XML file {$patch} not found.", E_USER_ERROR);
		}
			
		return false;
	}
	
	static function CKEditor($element, $value)
	{
		include_once "libs/ckeditor/ckeditor.php";
		$class = new \CKEditor();
		$class->returnOutput = true;
		$class->basePath = "libs/ckeditor/";
		return $class->editor($element, $value);
	}
	
	static function InitPOT()
	{
		// includes POT main file
		include_once('libs/pot/OTS.php');
		
		$array = explode(":", Configs::Get(Configs::eConf()->SQL_HOST));
		
		if(count($array) == 2)
		{
			$ip = $array[0];
			$port = $array[1];
		}
		else
		{
			$ip = Configs::Get(Configs::eConf()->SQL_HOST);
			$port = 3306;
		}
		
		// database configuration - can be simply moved to external file, eg. config.php
		$config = array(
		    'driver' => \POT::DB_MYSQL,
		    'host' => $ip,
		    'port' => $port,
		    'user' => Configs::Get(Configs::eConf()->SQL_USER),
		    'password' => Configs::Get(Configs::eConf()->SQL_PASSWORD),
		    'database' => Configs::Get(Configs::eConf()->SQL_DATABASE)
		);
		
		// creates POT instance (or get existing one)
		// dont use POT::getInstance() anymore
		\POT::connect(null, $config);
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
	
	static function InitLanguage()
	{		
		if(Configs::Get(Configs::eConf()->LANGUAGE) == Consts::LANGUAGE_PTBR)
		{	
			include_once "language/".Consts::LANGUAGE_PTBR."/menu.php";
			include_once "language/".Consts::LANGUAGE_PTBR."/pages.php";
			include_once "language/".Consts::LANGUAGE_PTBR."/buttons.php";
			include_once "language/".Consts::LANGUAGE_PTBR."/Messages.php";			
		}		
		
		Lang::Init();
		\Lang_Messages::Load(Lang::GetMsgs());
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
	
	static function redirect($url, $local = true, $delay = false) 
	{		
		//if($local)
			//$url = Configs::Get(Configs::eConf()->WEBSITE_URL)."/".$url;
	
		//header("Location: ".$url." ");	
		if($delay)
		{
			global $module;
			
			$module =  '
			<script type="text/javascript">
				setTimeout(\'window.location.href="' . Configs::Get(Configs::eConf()->WEBSITE_URL) . '/' . $url . '"\', 5000);
			</script>';
		}
		else
		{
			$html =  '<script type="text/javascript">window.location = "' . Configs::Get(Configs::eConf()->WEBSITE_URL) . '/' . $url . '"</script>';
			echo $html;
		}
	}
	
	static function requireLogin()
	{
		if($_POST)
		{
			$_SESSION["login_post"] = $_POST;
		}
		
		$_SESSION["login_redirect"] = $_SERVER["REQUEST_URI"];
		Main::redirect("?ref=account.login");
	}
	
	static function requireWorldSelection($asTable = false)
	{
		if(!Configs::Get(Configs::eConf()->ENABLE_MULTIWORLD))
		{
			$_GET["world"] = Configs::Get(Configs::eConf()->DEFAULT_WORLD);
			return;
		}
		
		global $module;
		
		if(!$asTable)
		{
			$select = new \Framework\HTML\SelectBox();
			$select->SetName("world");
			$select->onChangeSubmit();
			
			$select->AddOption("", null, null, true);
			
			while(\t_Worlds::ItValid())
			{
				$selected = false;
				if(isset($_GET["world"]) && $_GET["world"] == \t_Worlds::It())
					$selected = true;
				
				$select->AddOption(\t_Worlds::GetString(\t_Worlds::It()), \t_Worlds::It(), $selected);
				\t_Worlds::ItNext();
			}
			
			$hidden_inputs = "";
			
			foreach($_GET as $k => $v)
			{
				if($k == "world")
					continue;
			
				$input = new \Framework\HTML\Input();
				$input->IsHidden();
				$input->SetName($k);
				$input->SetValue($v);
				$hidden_inputs .= $input->Draw();
			}	
			
			$str = "
			<form action='{$_SERVER["REQUEST_URI"]}' method='GET'>
				<fieldset>
					{$hidden_inputs}
				
					<p>		
						<label for='world'>Selecione um mundo</label>
						{$select->Draw()}
					</p>		
					
					<p class='line'></p>
				</fieldset>
			</form>
			";
		}
		else
		{
			$table = new \Framework\HTML\Table();
			
			$table->AddField("Selecione um mundo", null, null, null, true);
			$table->AddRow();
			
			$table->AddField("<strong>Mundo</strong>", "75%");
			$table->AddField("<strong>Jogadores</strong>");
			$table->AddRow();
			
			$status = \Core\Main::$DB->query("SELECT `server_id`, `players`, `online` FROM `serverstatus` ORDER BY `date` DESC LIMIT 2");
			
			$total = 0;
			
			while($s = $status->fetch())
			{
				$table->AddField("<a href='{$_SERVER["REQUEST_URI"]}&world={$s->server_id}'>" . \t_Worlds::GetString($s->server_id) . "</a>");
				$table->AddField($s->online == 1 ? $s->players : "<span style='color: red;'>Fora do ar<span>");
				$table->AddRow();		
				
				$total += $s->players;
			}
			
			$table->AddField("<strong>Total</strong>" . \t_Worlds::GetString($s->server_id) . "</a>");
			$table->AddField($total);
			$table->AddRow();			
			
			$str = $table->Draw();
		}

		$module .= $str;
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
		$query = self::$DB->query("SELECT value FROM ".Tools::getSiteTable("global")." WHERE field = '{$field}'");
		
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
		$query = self::$DB->query("SELECT value FROM ".Tools::getSiteTable("global")." WHERE field = '{$field}'");
		
		if($query->numRows() != 0)
			self::$DB->query("UPDATE ".Tools::getSiteTable("global")." SET value = '{$value}' WHERE field = '{$field}'");
		else
			self::$DB->query("INSERT INTO ".Tools::getSiteTable("global")." (`field`, `value`) values('{$field}', '{$value}')");
	}	
	
	static function isLogged()
	{
		return $_SESSION["login"];
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
		if(!file_exists("javascript/{$file}"))
			return;
		
		$focus = self::$m_XMLRoot->head[0];
		
		$child = $focus->addChild("script");
		$child->addAttribute("src", "javascript/{$file}");
		$child->addAttribute("type", "text/javascript");			
	}	
	
	static function readTempFile($file)
	{
		$content = file_get_contents(Configs::Get(Configs::eConf()->WEBSITE_FOLDER_TEMP) . $file);
		return $content;
	}
	
	static function writeTempFile($file, $content)
	{
		file_put_contents(Configs::Get(Configs::eConf()->WEBSITE_FOLDER_TEMP) . $file, $content);
	}
}		
?>