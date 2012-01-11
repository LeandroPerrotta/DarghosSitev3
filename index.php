<?
//ini_set("display_errors", 1 );
//ini_set("memory_limit", "64M");
//error_reporting(E_ERROR | E_WARNING); 
error_reporting(E_ERROR | E_WARNING); 

/*function __autoload($class_name) {
    include_once "classes/{$class_name}.php";
}*/

list($t, $date) = explode(" ", microtime());

setlocale(LC_ALL, "pt_BR");
date_default_timezone_set("America/Sao_Paulo");

session_start();

include "configs/index.php";

$layoutDir = "newlay/";

include "classes/core.php";
include "classes/Enums.php";
spl_autoload_register("Core::autoLoad");

if(!MANUTENTION)
{		
	include "classes/mysql.php";
	
	include "classes/account.php";
	include "classes/character.php";
	include "classes/Guilds.php";
	include "classes/houses.php";
	include "classes/deaths.php";
	include "classes/contribute.php";
	include "classes/bans.php";
	include "classes/pagesdb.php";
	include "classes/tools.php";
	include "classes/strings.php";
	include "classes/tickets.php";
	include "classes/monsters.php";
	include "classes/Forum.php";
	
	include "classes/HTML_Table.php";
	include "classes/HTML_SelectBox.php";
	include "classes/HTML_Input.php";
	
	include "libs/phpmailer/class.phpmailer.php";
	
	try
	{
		$db = new MySQL();
		$db->connect(DB_HOST, DB_USER, DB_PASS, DB_SCHEMA);	
		
		Core::$DB = $db;
	}
	catch (Exception $e)
	{
		echo "Impossivel se conectar ao banco de dados.";
	}
	
	$tools = new Tools();

	$menu = array();
	$buttons = array();
	$pages = array();
	
	Core::InitLanguage();
	Emails::init();
	
	if(!$_SESSION["login_redirect"] && $_SESSION["login_post"])
	{
		$_POST = $_SESSION["login_post"];
		unset($_SESSION["login_post"]);
	}
}	

Core::InitPOT();

include "modules.php";
include "{$layoutDir}index.php";
	
if(!MANUTENTION)	
	$db->close();

?>
