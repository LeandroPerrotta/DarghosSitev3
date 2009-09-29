<?
ini_set("display_errors", 0 );
error_reporting(0); 

list($t, $date) = explode(" ", microtime());

setlocale(LC_ALL, "pt_BR");
date_default_timezone_set("America/Sao_Paulo");

session_start();

include "configs/index.php";

if(GLOBAL_LANGUAGE == "pt")
{
	include("language/pt/menu.php");
	include("language/pt/pages.php");
	include("language/pt/buttons.php");
	include("language/pt/messageBox.php");
}

$layoutDir = "newlay/";

if(MANUTENTION == 1)
{
	$module .= "
	<p>
	<h3>Estamos desligados para manutenção critica. Voltaremos em breve.</h3>
	</p>
	";
	
	include "{$layoutDir}indexsimple.php";
}
else
{		
	include "classes/mysql.php";
	include "classes/core.php";
	
	try
	{
		$db = new MySQL();
		$db->connect(DB_HOST, DB_USER, DB_PASS, DB_SCHEMA);	
	
		$core = new Core();
		$core->InitPOT();
	}
	catch (Exception $e)
	{
		echo "Impossivel se conectar ao banco de dados.";
	}
	
	if(defined('SITE_ROOT_DIR'))
	{	
		if("http://".$_SERVER["HTTP_HOST"].SITE_ROOT_DIR != CONFIG_SITEEMAIL)
		{
			$core->redirect(CONFIG_SITEEMAIL, false); 
		}	
	}
	else
	{		
		if("http://".$_SERVER["HTTP_HOST"] != CONFIG_SITEEMAIL)
		{
			$core->redirect(CONFIG_SITEEMAIL, false); 
		}		
	}	
	
	$strings = $core->loadClass("strings");
	$tools = $core->loadClass("tools");
	
	include "modules.php";
	include "{$layoutDir}index.php";
	
	$db->close();
}
?>
