<?
ini_set("display_errors", 0 );
error_reporting(0); 

list($t, $date) = explode(" ", microtime());

setlocale(LC_ALL, "pt_BR");
date_default_timezone_set("America/Sao_Paulo");

session_start();

include "configs/index.php";

$layoutDir = "newlay/";

if(MANUTENTION == 1)
{
	$module .= "
	<p>
	<h3>Voltaremos em breve.</h3>
	</p>
	";
	
	include "{$layoutDir}indexsimple.php";
}
else
{			
	include "classes/mysql.php";
	include "classes/core.php";
	
	include "classes/account.php";
	include "classes/character.php";
	include "classes/guilds.php";
	include "classes/houses.php";
	include "classes/deaths.php";
	include "classes/contribute.php";
	include "classes/bans.php";
	include "classes/itemshop.php";
	include "classes/itemshop_list.php";
	include "classes/pagesdb.php";
	include "classes/tools.php";
	include "classes/strings.php";
	include "classes/tickets.php";
	include "classes/monsters.php";
	
	try
	{
		$db = new MySQL();
		$db->connect(DB_HOST, DB_USER, DB_PASS, DB_SCHEMA);	
	
		Core::InitPOT();
	}
	catch (Exception $e)
	{
		echo "Impossivel se conectar ao banco de dados.";
	}
	
	if(defined('SITE_ROOT_DIR'))
	{	
		if("http://".$_SERVER["HTTP_HOST"].SITE_ROOT_DIR != CONFIG_SITEEMAIL.SITE_ROOT_DIR)
		{
			Core::redirect(CONFIG_SITEEMAIL.SITE_ROOT_DIR, false); 
		}	
	}
	else
	{		
		if("http://".$_SERVER["HTTP_HOST"] != CONFIG_SITEEMAIL)
		{
			Core::redirect(CONFIG_SITEEMAIL, false); 
		}		
	}
	
	$tools = new Tools();
	Strings::Init();

	$menu = array();
	$buttons = array();
	$pages = array();
	
	Core::InitLanguage();
	
	include "modules.php";
	include "{$layoutDir}index.php";
	
	$db->close();
}
?>
