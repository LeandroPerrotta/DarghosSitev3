<?
ini_set("display_errors", "0");
error_reporting(E_ALL^E_NOTICE);
$date = date("d-m-y");

$patch = "/var/log/php/darghos_{$date}.log";

if(!file_exists($patch))
{
	$handle = fopen($patch, "x+");
	fwrite($handle, "Error file log generated.\n");
}

ini_set ("error_log", $patch);

setlocale(LC_ALL, "pt_BR");
date_default_timezone_set("America/Sao_Paulo");

session_start();

$module = "";

$menu = array();
$buttons = array();
$pages = array();

include "classes/Core/Main.php";
spl_autoload_register("Core\\Main::autoLoad");

Core\Main::Initialize();

use \Core\Configs as g_Configs;

include "modules.php";
Core\Main::drawTemplate();

if(!g_Configs::Get(g_Configs::eConf()->ENABLE_MANUTENTION))	
	Core\Main::$DB->close();
?>
